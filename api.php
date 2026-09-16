<?php
/* ============================================================
   api.php — point d'entrée JSON (front + admin)
   ============================================================ */
require_once __DIR__ . '/lib.php';
session_start();

$action = $_GET['action'] ?? '';

function require_admin(): void {
  if (empty($_SESSION['admin'])) json_out(['error' => 'auth'], 401);
}
function public_config(): array {
  return [
    'eventName'   => setting('eventName'),
    'cause'       => setting('cause'),
    'date'        => setting('date'),
    'time'        => setting('time'),
    'place'       => setting('place'),
    'goal'         => (float) setting('goal'),
    'capDine'      => (int) setting('capDine'),
    'capTake'      => (int) setting('capTake'),
    'includedText' => setting('includedText', ''),
    'takeText'     => setting('takeText', ''),
    'afterText'    => setting('afterText', ''),
    'helpPhone'    => setting('helpPhone', ''),
    'iban'         => setting('iban'),
    'accountName'  => setting('accountName'),
  ];
}

try {
  switch ($action) {

    /* ---------- ÉTAT PUBLIC ---------- */
    case 'state': {
      $c = counts(db());
      $r = raised(db());
      json_out([
        'config'        => public_config(),
        'menu'          => menu_all(true),
        'remaining'     => remaining($c),
        'raisedCents'   => $r['cents'],
        'raisedCount'   => $r['count'],
        'stripeEnabled' => stripe_enabled(),
      ]);
    }

    /* ---------- CONSULTER UNE RÉSA (retour Stripe) ---------- */
    case 'lookup': {
      $ref = $_GET['ref'] ?? '';
      $b = get_booking_by_ref($ref);
      if (!$b) json_out(['error' => 'notfound'], 404);
      json_out([
        'ref'    => $b['ref'],
        'name'   => $b['name'],
        'status' => $b['status'],
        'amount' => money_cents((int)$b['amount_cents']),
        'method' => $b['method'],
        'mode'   => $b['mode'],
        'items'  => $b['items'],
      ]);
    }

    /* ---------- LIBÉRER UNE RÉSA EN ATTENTE (annulation Stripe) ---------- */
    case 'cancel_pending': {
      $in = input_json();
      $ref = trim($in['ref'] ?? ($_GET['ref'] ?? ''));
      $b = get_booking_by_ref($ref);
      if (!$b) json_out(['ok' => false], 404);
      // on ne touche qu'aux réservations carte encore "à payer"
      if ($b['status'] === 'pending' && $b['method'] === 'stripe') {
        if (!empty($b['stripe_session_id'])) stripe_expire_session($b['stripe_session_id']);
        $fresh = get_booking((int)$b['id']);   // un webhook a-t-il payé entre-temps ?
        if ($fresh && $fresh['status'] === 'pending') mark_status((int)$b['id'], 'cancelled');
      }
      json_out(['ok' => true]);
    }

    /* ---------- CRÉER UNE RÉSERVATION ---------- */
    case 'book': {
      $in = input_json();
      $name = trim($in['name'] ?? '');
      $email = trim($in['email'] ?? '');
      $phone = trim($in['phone'] ?? '');
      $mode  = ($in['mode'] ?? 'dine') === 'take' ? 'take' : 'dine';
      $items = is_array($in['items'] ?? null) ? $in['items'] : [];
      $method = ($in['method'] ?? 'transfer') === 'stripe' ? 'stripe' : 'transfer';

      if ($name === '')                   json_out(['error' => 'name'], 422);
      if ($email === '' && $phone === '') json_out(['error' => 'contact'], 422);
      if (!$items)                        json_out(['error' => 'empty'], 422);
      if ($method === 'stripe' && !stripe_enabled()) $method = 'transfer';
      if ($method === 'stripe' && $email === '') json_out(['error' => 'email_required'], 422);

      $b = create_booking([
        'name' => $name, 'email' => $email, 'phone' => $phone,
        'notes' => $in['notes'] ?? '', 'mode' => $mode, 'items' => $items, 'method' => $method,
      ]);
      if (isset($b['error'])) json_out($b, $b['error'] === 'empty' ? 422 : 409);   // 'sold' + remaining

      email_organizer($b);

      if ($method === 'stripe') {
        $s = stripe_create_session($b);
        if (!$s['ok']) {
          // Stripe a échoué → on bascule en virement pour ne pas perdre la résa
          db()->prepare("UPDATE bookings SET method='transfer' WHERE id=?")->execute([(int)$b['id']]);
          email_payment_info($b);
          json_out(['ok' => true, 'mode' => 'transfer', 'booking' => booking_public($b),
                    'warning' => 'Paiement carte indisponible, infos de virement envoyées.']);
        }
        json_out(['ok' => true, 'mode' => 'stripe', 'url' => $s['url']]);
      }

      email_payment_info($b);
      json_out(['ok' => true, 'mode' => 'transfer', 'booking' => booking_public($b)]);
    }

    /* ---------- ADMIN : login / logout ---------- */
    case 'admin_login': {
      $in = input_json();
      if (($in['password'] ?? '') === ADMIN_PASSWORD) {
        $_SESSION['admin'] = true;
        json_out(['ok' => true]);
      }
      json_out(['error' => 'bad'], 401);
    }
    case 'admin_logout': {
      $_SESSION['admin'] = false;
      json_out(['ok' => true]);
    }

    /* ---------- ADMIN : tableau de bord ---------- */
    case 'admin_state': {
      require_admin();
      $rows = db()->query('SELECT * FROM bookings ORDER BY id DESC')->fetchAll();
      $items = booking_items_map(array_map(fn($b) => (int)$b['id'], $rows));
      foreach ($rows as &$row) $row['items'] = $items[(int)$row['id']] ?? [];
      unset($row);
      $c = counts(db()); $r = raised(db());
      json_out([
        'config'      => public_config(),
        'menu'        => menu_all(false),
        'caps'        => caps(),
        'remaining'   => remaining($c),
        'used'        => $c,
        'raisedCents' => $r['cents'],
        'bookings'    => array_map('booking_public', $rows),
      ]);
    }

    /* ---------- ADMIN : changer un statut ---------- */
    case 'admin_update': {
      require_admin();
      $in = input_json();
      mark_status((int)($in['id'] ?? 0), $in['status'] ?? '');
      json_out(['ok' => true]);
    }

    /* ---------- ADMIN : encoder une réservation prise par téléphone ---------- */
    case 'admin_book': {
      require_admin();
      $in = input_json();
      $name  = trim($in['name'] ?? '');
      $email = trim($in['email'] ?? '');
      $phone = trim($in['phone'] ?? '');
      $mode  = ($in['mode'] ?? 'dine') === 'take' ? 'take' : 'dine';
      $items = is_array($in['items'] ?? null) ? $in['items'] : [];
      $paid  = !empty($in['paid']);
      if ($name === '') json_out(['error' => 'name'], 422);
      if (!$items)      json_out(['error' => 'empty'], 422);

      $b = create_booking([
        'name' => $name, 'email' => $email, 'phone' => $phone,
        'notes' => $in['notes'] ?? '', 'mode' => $mode, 'items' => $items, 'method' => 'transfer',
      ]);
      if (isset($b['error'])) json_out($b, $b['error'] === 'empty' ? 422 : 409);

      if ($paid) { mark_paid((int)$b['id']); $b = get_booking((int)$b['id']); }   // + e-mail de confirmation si e-mail
      elseif ($email !== '') email_payment_info($b);                              // e-mail avec IBAN + communication
      json_out(['ok' => true, 'booking' => booking_public($b)]);
    }

    /* ---------- ADMIN : supprimer définitivement une résa annulée ---------- */
    case 'admin_delete': {
      require_admin();
      $in = input_json();
      $id = (int)($in['id'] ?? 0);
      $b = get_booking($id);
      if (!$b) json_out(['error' => 'notfound'], 404);
      if ($b['status'] !== 'cancelled') json_out(['error' => 'not_cancelled'], 409);
      db()->prepare('DELETE FROM booking_items WHERE booking_id = ?')->execute([$id]);
      db()->prepare('DELETE FROM bookings WHERE id = ?')->execute([$id]);
      json_out(['ok' => true]);
    }

    /* ---------- ADMIN : enregistrer les réglages ---------- */
    case 'admin_config': {
      require_admin();
      $in = input_json();
      $allowed = ['eventName','cause','date','time','place','goal',
                  'capDine','capTake','includedText','takeText','afterText','helpPhone',
                  'iban','accountName'];
      $kv = [];
      foreach ($allowed as $k) if (array_key_exists($k, $in)) $kv[$k] = $in[$k];
      if ($kv) settings_save($kv);
      json_out(['ok' => true]);
    }

    /* ---------- ADMIN : enregistrer le menu (plats + prix) ---------- */
    case 'admin_menu': {
      require_admin();
      $in = input_json();
      $dishes = is_array($in['dishes'] ?? null) ? $in['dishes'] : [];
      menu_save($dishes);
      json_out(['ok' => true, 'menu' => menu_all(false)]);
    }

    /* ---------- ADMIN : tout effacer ---------- */
    case 'admin_wipe': {
      require_admin();
      db()->exec('DELETE FROM booking_items');
      db()->exec('DELETE FROM bookings');
      json_out(['ok' => true]);
    }

    default:
      json_out(['error' => 'unknown_action'], 404);
  }
} catch (Throwable $e) {
  json_out(['error' => 'server', 'detail' => $e->getMessage()], 500);
}

/* projection sans données inutiles vers le client */
function booking_public(array $b): array {
  return [
    'id'      => (int)$b['id'],
    'ref'     => $b['ref'],
    'name'    => $b['name'],
    'email'   => $b['email'],
    'phone'   => $b['phone'],
    'notes'   => $b['notes'],
    'mode'    => $b['mode'],
    'meals'   => (int)$b['qty_dine'] + (int)$b['qty_take'],
    'items'   => $b['items'] ?? [],
    'amount'  => (int)$b['amount_cents'],
    'status'  => $b['status'],
    'method'  => $b['method'],
    'created' => $b['created_at'],
  ];
}
