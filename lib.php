<?php
/* ============================================================
   lib.php — cœur du backend (DB, capacités, e-mails, Stripe)
   ============================================================ */
require_once __DIR__ . '/config.php';
date_default_timezone_set(defined('TIMEZONE') ? TIMEZONE : 'Europe/Brussels');
mb_internal_encoding('UTF-8');

/* ---------- Connexion DB (PDO singleton) ---------- */
function db(): PDO {
  static $pdo = null;
  if ($pdo === null) {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
  }
  return $pdo;
}

/* ---------- Réglages ---------- */
function settings_all(): array {
  static $cache = null;
  if ($cache === null) {
    $cache = [];
    foreach (db()->query('SELECT skey, sval FROM settings') as $r) {
      $cache[$r['skey']] = $r['sval'];
    }
  }
  return $cache;
}
function setting(string $k, $default = null) {
  $all = settings_all();
  return array_key_exists($k, $all) ? $all[$k] : $default;
}
function settings_save(array $kv): void {
  $stmt = db()->prepare(
    'INSERT INTO settings (skey, sval) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE sval = VALUES(sval)'
  );
  foreach ($kv as $k => $v) $stmt->execute([$k, (string)$v]);
}


/* ---------- Helpers ---------- */
function caps(): array {
  return [
    'capDine' => (int) setting('capDine', 80),   // repas servis sur place
    'capTake' => (int) setting('capTake', 40),   // repas à emporter
  ];
}
function to_cents($v): int { return (int) round(((float) $v) * 100); }

/* ---------- Menu (plats configurables depuis l'admin) ---------- */
function dish_public(array $r): array {
  return [
    'id'          => (int) $r['id'],
    'name'        => $r['name'],
    'description' => $r['description'] ?? '',
    'emoji'       => $r['emoji'] ?? '',
    'priceAdult'  => (float) $r['price_adult'],
    'priceChild'  => (float) $r['price_child'],
    'active'      => (int) $r['active'] === 1,
  ];
}
function menu_all(bool $onlyActive = false): array {
  $sql = 'SELECT * FROM dishes' . ($onlyActive ? ' WHERE active = 1' : '') . ' ORDER BY sort_order, id';
  $out = [];
  foreach (db()->query($sql) as $r) $out[] = dish_public($r);
  return $out;
}
/* Remplace le menu complet : ids présents = mis à jour, sans id = créés,
   ids absents = supprimés. Les réservations gardent leur copie du nom
   et du prix, donc modifier le menu ne touche pas l'historique. */
function menu_save(array $dishes): void {
  $pdo = db();
  $pdo->beginTransaction();
  try {
    $keep = [];
    $upd = $pdo->prepare('UPDATE dishes SET name=?, description=?, emoji=?, price_adult=?, price_child=?, active=?, sort_order=? WHERE id=?');
    $ins = $pdo->prepare('INSERT INTO dishes (name, description, emoji, price_adult, price_child, active, sort_order) VALUES (?,?,?,?,?,?,?)');
    $i = 0;
    foreach ($dishes as $d) {
      if (!is_array($d)) continue;
      $name = mb_substr(trim((string)($d['name'] ?? '')), 0, 120);
      if ($name === '') continue;
      $row = [
        $name,
        mb_substr(trim((string)($d['description'] ?? '')), 0, 255),
        mb_substr(trim((string)($d['emoji'] ?? '')), 0, 16),
        max(0, round((float)($d['priceAdult'] ?? 0), 2)),
        max(0, round((float)($d['priceChild'] ?? 0), 2)),
        !empty($d['active']) ? 1 : 0,
        ++$i,
      ];
      $id = (int)($d['id'] ?? 0);
      if ($id > 0) { $upd->execute(array_merge($row, [$id])); $keep[] = $id; }
      else         { $ins->execute($row); $keep[] = (int)$pdo->lastInsertId(); }
    }
    if ($keep) {
      $ph = implode(',', array_fill(0, count($keep), '?'));
      $pdo->prepare("DELETE FROM dishes WHERE id NOT IN ($ph)")->execute($keep);
    } else {
      $pdo->exec('DELETE FROM dishes');
    }
    $pdo->commit();
  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    throw $e;
  }
}
function money_cents(int $c): string {
  return number_format($c / 100, 2, ',', ' ') . ' €';
}
function json_out($data, int $code = 200): void {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}
function input_json(): array {
  $raw = file_get_contents('php://input');
  $d = json_decode($raw, true);
  return is_array($d) ? $d : [];
}


/* ---------- Comptage / disponibilités ---------- */
function counts(PDO $pdo, bool $lock = false): array {
  $sql = "SELECT COALESCE(SUM(qty_dine),0) AS d, COALESCE(SUM(qty_take),0) AS t
          FROM bookings WHERE status <> 'cancelled'";
  if ($lock) $sql .= ' FOR UPDATE';
  $r = $pdo->query($sql)->fetch();
  return ['dine' => (int)$r['d'], 'take' => (int)$r['t']];
}
function remaining(?array $c = null): array {
  $c = $c ?? counts(db());
  $k = caps();
  return [
    'dine' => max(0, $k['capDine'] - $c['dine']),
    'take' => max(0, $k['capTake'] - $c['take']),
  ];
}
function raised(PDO $pdo): array {
  $r = $pdo->query("SELECT COALESCE(SUM(amount_cents),0) c, COUNT(*) n
                    FROM bookings WHERE status='paid'")->fetch();
  return ['cents' => (int)$r['c'], 'count' => (int)$r['n']];
}

/* ---------- Communication structurée belge (+++xxx/xxxx/xxxxx+++) ---------- */
function mod97_str(string $s): int {
  $r = 0;
  for ($i = 0, $n = strlen($s); $i < $n; $i++) $r = ($r * 10 + (int)$s[$i]) % 97;
  return $r;
}
function make_comm(string $base10): string {
  $chk = mod97_str($base10); if ($chk === 0) $chk = 97;
  $full = $base10 . str_pad((string)$chk, 2, '0', STR_PAD_LEFT);
  return '+++' . substr($full, 0, 3) . '/' . substr($full, 3, 4) . '/' . substr($full, 7, 5) . '+++';
}
function gen_ref(PDO $pdo): string {
  $stmt = $pdo->prepare('SELECT 1 FROM bookings WHERE ref = ? LIMIT 1');
  for ($i = 0; $i < 50; $i++) {
    $base = str_pad((string)random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
    $ref = make_comm($base);
    $stmt->execute([$ref]);
    if (!$stmt->fetch()) return $ref;
  }
  throw new RuntimeException('Impossible de générer une référence unique');
}

/* ============================================================
   CRÉATION D'UNE RÉSERVATION (transaction + verrou anti-survente)
   $data: name, email, phone, notes, method,
          mode  : 'dine' (sur place) | 'take' (à emporter)
          items : [ ['dish'=>id, 'variant'=>'adult'|'child', 'qty'=>n], … ]
   Retourne le booking, ['error'=>'sold','remaining'=>…] ou ['error'=>'empty']
   ============================================================ */
function normalize_items(array $items): array {
  // Regroupe par (plat, variante), ne garde que les plats actifs,
  // et fige le nom + le prix du moment (jamais ceux envoyés par le navigateur).
  $menu = [];
  foreach (menu_all(true) as $d) $menu[$d['id']] = $d;
  $agg = [];
  foreach ($items as $it) {
    if (!is_array($it)) continue;
    $id  = (int)($it['dish'] ?? 0);
    $var = ($it['variant'] ?? 'adult') === 'child' ? 'child' : 'adult';
    $qty = max(0, min(200, (int)($it['qty'] ?? 0)));
    if ($qty <= 0 || !isset($menu[$id])) continue;
    $key = $id . ':' . $var;
    if (!isset($agg[$key])) {
      $agg[$key] = [
        'dish_id'    => $id,
        'dish_name'  => $menu[$id]['name'],
        'variant'    => $var,
        'qty'        => 0,
        'unit_cents' => to_cents($var === 'child' ? $menu[$id]['priceChild'] : $menu[$id]['priceAdult']),
      ];
    }
    $agg[$key]['qty'] += $qty;
  }
  return array_values($agg);
}

function create_booking(array $data) {
  $pdo    = db();
  $mode   = ($data['mode'] ?? 'dine') === 'take' ? 'take' : 'dine';
  $method = ($data['method'] ?? 'transfer') === 'stripe' ? 'stripe' : 'transfer';
  $items  = normalize_items($data['items'] ?? []);
  $total  = array_sum(array_column($items, 'qty'));
  if ($total <= 0) return ['error' => 'empty'];

  $pdo->beginTransaction();
  try {
    $c = counts($pdo, true);                 // verrou
    $k = caps();
    $free = $mode === 'take' ? $k['capTake'] - $c['take'] : $k['capDine'] - $c['dine'];
    if ($total > $free) {
      $pdo->rollBack();
      return ['error' => 'sold', 'remaining' => remaining($c)];
    }

    $amount = 0;
    foreach ($items as $it) $amount += $it['qty'] * $it['unit_cents'];
    $ref = gen_ref($pdo);

    $stmt = $pdo->prepare(
      "INSERT INTO bookings
        (ref,name,email,phone,notes,mode,qty_dine,qty_take,amount_cents,status,method)
       VALUES (?,?,?,?,?,?,?,?,?,'pending',?)"
    );
    $stmt->execute([
      $ref,
      mb_substr(trim($data['name'] ?? ''), 0, 160),
      mb_substr(trim($data['email'] ?? ''), 0, 190) ?: null,
      mb_substr(trim($data['phone'] ?? ''), 0, 40) ?: null,
      mb_substr(trim($data['notes'] ?? ''), 0, 1000) ?: null,
      $mode,
      $mode === 'dine' ? $total : 0,
      $mode === 'take' ? $total : 0,
      $amount, $method,
    ]);
    $id = (int)$pdo->lastInsertId();
    $ins = $pdo->prepare('INSERT INTO booking_items (booking_id, dish_id, dish_name, variant, qty, unit_cents) VALUES (?,?,?,?,?,?)');
    foreach ($items as $it) {
      $ins->execute([$id, $it['dish_id'], $it['dish_name'], $it['variant'], $it['qty'], $it['unit_cents']]);
    }
    $pdo->commit();
  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    throw $e;
  }
  return get_booking($id);
}

/* Détail des plats de plusieurs réservations : [booking_id => items[]] */
function booking_items_map(array $ids): array {
  if (!$ids) return [];
  $ph = implode(',', array_fill(0, count($ids), '?'));
  $s = db()->prepare("SELECT * FROM booking_items WHERE booking_id IN ($ph) ORDER BY id");
  $s->execute(array_values($ids));
  $map = [];
  foreach ($s->fetchAll() as $r) {
    $map[(int)$r['booking_id']][] = [
      'dish'    => $r['dish_id'] !== null ? (int)$r['dish_id'] : null,
      'name'    => $r['dish_name'],
      'variant' => $r['variant'],
      'qty'     => (int)$r['qty'],
      'unit'    => (int)$r['unit_cents'],
    ];
  }
  return $map;
}
function get_booking(int $id): ?array {
  $s = db()->prepare('SELECT * FROM bookings WHERE id = ?');
  $s->execute([$id]);
  $b = $s->fetch();
  if (!$b) return null;
  $b['items'] = booking_items_map([(int)$b['id']])[(int)$b['id']] ?? [];
  return $b;
}
function get_booking_by_ref(string $ref): ?array {
  $s = db()->prepare('SELECT id FROM bookings WHERE ref = ?');
  $s->execute([$ref]);
  $r = $s->fetch();
  return $r ? get_booking((int)$r['id']) : null;
}


/* ============================================================
   E-MAIL — aiguilleur : SMTP si configuré, sinon API Brevo
   ============================================================ */
function brevo_send(string $toEmail, string $toName, string $subject, string $html): bool {
  if (!$toEmail) return false;
  if (defined('SMTP_HOST') && SMTP_HOST) {
    return smtp_send($toEmail, $toName, $subject, $html);
  }
  return brevo_api_send($toEmail, $toName, $subject, $html);
}

/* ---------- Envoi via API HTTP Brevo (repli) ---------- */
function brevo_api_send(string $toEmail, string $toName, string $subject, string $html): bool {
  if (!defined('BREVO_API_KEY') || !BREVO_API_KEY) return false;
  $payload = [
    'sender'      => ['name' => SENDER_NAME, 'email' => SENDER_EMAIL],
    'to'          => [['email' => $toEmail, 'name' => $toName ?: $toEmail]],
    'subject'     => $subject,
    'htmlContent' => $html,
  ];
  $ch = curl_init('https://api.brevo.com/v3/smtp/email');
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
      'accept: application/json',
      'content-type: application/json',
      'api-key: ' . BREVO_API_KEY,
    ],
    CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_TIMEOUT        => 15,
  ]);
  $res  = curl_exec($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  return $code >= 200 && $code < 300;
}

/* ---------- Envoi via SMTP complet (sans dépendance) ---------- */
function smtp_send(string $toEmail, string $toName, string $subject, string $html): bool {
  $host    = SMTP_HOST;
  $port    = (int) (defined('SMTP_PORT') ? SMTP_PORT : 587);
  $secure  = strtolower(defined('SMTP_SECURE') ? SMTP_SECURE : 'tls');  // 'ssl' | 'tls' | ''
  $timeout = 20;
  $ehloHost = parse_url(BASE_URL, PHP_URL_HOST) ?: 'localhost';

  $remote = ($secure === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
  $ctx = stream_context_create(['ssl' => ['SNI_enabled' => true]]);
  $fp = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $ctx);
  if (!$fp) { error_log("SMTP: connexion impossible ($errno) $errstr"); return false; }
  stream_set_timeout($fp, $timeout);

  $read = function () use ($fp) {
    $data = '';
    while (($line = fgets($fp, 515)) !== false) {
      $data .= $line;
      if (strlen($line) < 4 || $line[3] === ' ') break;  // dernière ligne de la réponse
    }
    return $data;
  };
  $code = fn($r) => (int) substr($r, 0, 3);
  $cmd  = function ($c) use ($fp, $read) { fwrite($fp, $c . "\r\n"); return $read(); };
  $fail = function () use ($fp) { @fwrite($fp, "QUIT\r\n"); @fclose($fp); return false; };

  if ($code($read()) !== 220) return $fail();                 // accueil serveur
  if ($code($cmd("EHLO $ehloHost")) !== 250) return $fail();

  if ($secure === 'tls') {
    if ($code($cmd('STARTTLS')) !== 220) return $fail();
    $crypto = STREAM_CRYPTO_METHOD_TLS_CLIENT;
    if (defined('STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT')) $crypto |= STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
    if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) $crypto |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
    if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) $crypto |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
    if (!stream_socket_enable_crypto($fp, true, $crypto)) return $fail();
    if ($code($cmd("EHLO $ehloHost")) !== 250) return $fail();  // re-EHLO après TLS
  }

  // Authentification AUTH LOGIN
  if ($code($cmd('AUTH LOGIN')) !== 334) return $fail();
  if ($code($cmd(base64_encode(SMTP_USER))) !== 334) return $fail();
  if ($code($cmd(base64_encode(SMTP_PASS))) !== 235) return $fail();

  // Enveloppe
  if ($code($cmd('MAIL FROM:<' . SENDER_EMAIL . '>')) !== 250) return $fail();
  $rcpt = $code($cmd('RCPT TO:<' . $toEmail . '>'));
  if ($rcpt !== 250 && $rcpt !== 251) return $fail();
  if ($code($cmd('DATA')) !== 354) return $fail();

  // En-têtes + corps (HTML encodé en base64 : pas de souci de longueur de ligne ni de "point")
  $enc = fn($s) => mb_encode_mimeheader($s, 'UTF-8', 'B', "\r\n");
  $headers = [
    'Date: ' . date('r'),
    'From: ' . $enc(SENDER_NAME) . ' <' . SENDER_EMAIL . '>',
    'To: ' . $enc($toName ?: $toEmail) . ' <' . $toEmail . '>',
    'Subject: ' . $enc($subject),
    'Message-ID: <' . bin2hex(random_bytes(8)) . '@' . $ehloHost . '>',
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'Content-Transfer-Encoding: base64',
  ];
  $bodyB64 = rtrim(chunk_split(base64_encode($html), 76, "\r\n"));
  $message = implode("\r\n", $headers) . "\r\n\r\n" . $bodyB64;

  fwrite($fp, $message . "\r\n.\r\n");
  $ok = $code($read()) === 250;
  $cmd('QUIT');
  fclose($fp);
  return $ok;
}


/* ---------- Contenu des e-mails ---------- */
function email_layout(string $title, string $body): string {
  $name = htmlspecialchars(setting('eventName', 'À table pour Viva for Life'));
  return '<div style="font-family:Arial,Helvetica,sans-serif;max-width:560px;margin:auto;color:#1E2A5A">
    <div style="background:#1E2A5A;color:#FFF7EC;padding:22px 24px;border-radius:14px 14px 0 0">
      <div style="font-size:13px;letter-spacing:.12em;text-transform:uppercase;color:#F7B733">' . $name . '</div>
      <h1 style="margin:6px 0 0;font-size:22px">' . $title . '</h1>
    </div>
    <div style="border:1px solid #F1E3CB;border-top:none;border-radius:0 0 14px 14px;padding:24px;background:#FFF7EC">'
    . $body .
    '<p style="margin-top:22px;font-size:13px;color:#7A6E55">Merci pour ton soutien 🧡</p></div></div>';
}

function mode_label(string $mode): string { return $mode === 'take' ? 'À emporter' : 'Sur place'; }
function variant_label(string $v): string { return $v === 'child' ? 'enfant' : 'adulte'; }

/* Résumé texte : "Sur place — 2 × Pâtes bolognaise (adulte) · 1 × Carbonara (enfant)" */
function lines_text(array $b): string {
  $L = [];
  foreach ($b['items'] ?? [] as $it) {
    $L[] = $it['qty'] . ' × ' . $it['name'] . ' (' . variant_label($it['variant']) . ')';
  }
  return mode_label($b['mode'] ?? 'dine') . ($L ? ' — ' . implode(' · ', $L) : '');
}
/* Résumé HTML pour les e-mails */
function lines_html(array $b): string {
  $rows = '';
  foreach ($b['items'] ?? [] as $it) {
    $rows .= '<tr><td style="padding:5px 0;border-bottom:1px solid #F1E3CB">' . htmlspecialchars($it['qty'] . ' × ' . $it['name'])
           . ' <span style="color:#7A6E55">(' . variant_label($it['variant']) . ')</span></td>'
           . '<td style="padding:5px 0;border-bottom:1px solid #F1E3CB;text-align:right">' . money_cents($it['qty'] * $it['unit']) . '</td></tr>';
  }
  $take = ($b['mode'] ?? 'dine') === 'take';
  $html = '<p style="margin:0 0 6px"><b>' . ($take ? '🥡 À emporter' : '🍽️ Sur place') . '</b>';
  if ($take && setting('takeText')) $html .= '<br><span style="font-size:13px;color:#7A6E55">' . htmlspecialchars(setting('takeText')) . '</span>';
  $html .= '</p><table style="width:100%;border-collapse:collapse">' . $rows . '</table>';
  if (setting('includedText')) $html .= '<p style="font-size:13px;color:#7A6E55;margin:6px 0 0">' . htmlspecialchars(setting('includedText')) . '</p>';
  return $html;
}

function email_payment_info(array $b): bool {
  if (empty($b['email'])) return false;
  $iban = htmlspecialchars(setting('iban'));
  $acc  = htmlspecialchars(setting('accountName'));
  $body = '<p>Bonjour ' . htmlspecialchars($b['name']) . ',</p>
    <p>Ta réservation est enregistrée 🎉 — il ne reste qu\'à régler par virement pour la confirmer.</p>
    ' . lines_html($b) . '
    <table style="width:100%;border-collapse:collapse;margin:18px 0;background:#1E2A5A;color:#FFF7EC;border-radius:10px">
      <tr><td style="padding:12px 14px 4px;font-size:12px;color:#F7B733">MONTANT</td></tr>
      <tr><td style="padding:0 14px 12px;font-size:22px;font-weight:bold">' . money_cents((int)$b['amount_cents']) . '</td></tr>
      <tr><td style="padding:0 14px 4px;font-size:12px;color:#F7B733">IBAN</td></tr>
      <tr><td style="padding:0 14px 12px;font-family:monospace;font-size:16px">' . $iban . '</td></tr>
      <tr><td style="padding:0 14px 4px;font-size:12px;color:#F7B733">COMMUNICATION STRUCTURÉE</td></tr>
      <tr><td style="padding:0 14px 12px;font-family:monospace;font-size:16px">' . htmlspecialchars($b['ref']) . '</td></tr>
      <tr><td style="padding:0 14px 14px;font-size:13px">Bénéficiaire : ' . $acc . '</td></tr>
    </table>
    <p style="font-size:14px">Indique bien <b>cette communication structurée</b> dans ton virement : ta réservation est confirmée dès réception.</p>';
  return brevo_send($b['email'], $b['name'], 'Tes infos de paiement — ' . setting('eventName'), email_layout('À régler par virement', $body));
}

function email_confirmation(array $b): bool {
  if (empty($b['email'])) return false;
  $after = setting('afterText') ? '<p style="font-size:14px">' . htmlspecialchars(setting('afterText')) . '</p>' : '';
  $body = '<p>Bonjour ' . htmlspecialchars($b['name']) . ',</p>
    <p>Paiement bien reçu — ta réservation est <b>confirmée</b> ✅. On a hâte de te voir !</p>
    ' . lines_html($b) . '
    <table style="width:100%;border-collapse:collapse;margin:16px 0">
      <tr><td style="padding:6px 0;border-bottom:1px solid #F1E3CB">Date</td><td style="padding:6px 0;border-bottom:1px solid #F1E3CB;text-align:right"><b>' . htmlspecialchars(setting('date')) . '</b></td></tr>
      <tr><td style="padding:6px 0;border-bottom:1px solid #F1E3CB">Heure</td><td style="padding:6px 0;border-bottom:1px solid #F1E3CB;text-align:right"><b>' . htmlspecialchars(setting('time')) . '</b></td></tr>
      <tr><td style="padding:6px 0">Lieu</td><td style="padding:6px 0;text-align:right"><b>' . htmlspecialchars(setting('place')) . '</b></td></tr>
    </table>
    ' . $after . '
    <p style="font-size:13px;color:#7A6E55">Référence : ' . htmlspecialchars($b['ref']) . '</p>';
  return brevo_send($b['email'], $b['name'], 'Réservation confirmée — ' . setting('eventName'), email_layout('C\'est confirmé !', $body));
}

function email_organizer(array $b): void {
  if (!defined('ORGANIZER_NOTIFY') || !ORGANIZER_NOTIFY) return;
  if (!defined('ORGANIZER_EMAIL') || !ORGANIZER_EMAIL) return;
  $body = '<p>Nouvelle réservation (' . ($b['method'] === 'stripe' ? 'carte' : 'virement') . ') :</p>
    <p><b>' . htmlspecialchars($b['name']) . '</b><br>' . htmlspecialchars(($b['email'] ?? '') . ' ' . ($b['phone'] ?? '')) . '</p>
    ' . lines_html($b) . '
    <p>Montant : <b>' . money_cents((int)$b['amount_cents']) . '</b><br>Réf : ' . htmlspecialchars($b['ref'])
    . ($b['notes'] ? '<br>Remarque : ' . htmlspecialchars($b['notes']) : '') . '</p>';
  brevo_send(ORGANIZER_EMAIL, 'Organisateur', 'Nouvelle réservation — ' . $b['name'], email_layout('Nouvelle réservation', $body));
}

/* ============================================================
   STRIPE — Checkout Session (paiement unique, via API REST)
   ============================================================ */
function stripe_enabled(): bool {
  return defined('STRIPE_SECRET_KEY') && STRIPE_SECRET_KEY && strpos(STRIPE_SECRET_KEY, 'sk_') === 0;
}
function stripe_request(string $path, array $params): array {
  $ch = curl_init('https://api.stripe.com/v1/' . $path);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_USERPWD        => STRIPE_SECRET_KEY . ':',
    CURLOPT_POSTFIELDS     => http_build_query($params),
    CURLOPT_TIMEOUT        => 20,
  ]);
  $res  = curl_exec($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  $data = json_decode($res, true) ?: [];
  return ['code' => $code, 'data' => $data];
}
function stripe_create_session(array $b): array {
  $items = [];
  $add = function (string $label, int $unit, int $qty) use (&$items) {
    if ($qty <= 0 || $unit <= 0) return;
    $i = count($items) / 4;
    $items["line_items[$i][price_data][currency]"]           = CURRENCY;
    $items["line_items[$i][price_data][product_data][name]"] = $label;
    $items["line_items[$i][price_data][unit_amount]"]        = $unit;
    $items["line_items[$i][quantity]"]                       = $qty;
  };
  $suffix = ' (' . mode_label($b['mode'] ?? 'dine') . ')';
  foreach ($b['items'] ?? [] as $it) {
    $add($it['name'] . ' — ' . variant_label($it['variant']) . $suffix, (int)$it['unit'], (int)$it['qty']);
  }

  $params = array_merge([
    'mode'                 => 'payment',
    'success_url'          => BASE_URL . '/index.php?status=success&ref=' . urlencode($b['ref']),
    'cancel_url'           => BASE_URL . '/index.php?status=cancel&ref=' . urlencode($b['ref']),
    'client_reference_id'  => $b['ref'],
    'metadata[ref]'        => $b['ref'],
    'expires_at'           => time() + 32 * 60,   // hold ~30 min puis libère la place
  ], $items);
  if (!empty($b['email'])) $params['customer_email'] = $b['email'];

  $r = stripe_request('checkout/sessions', $params);
  if ($r['code'] >= 200 && $r['code'] < 300 && !empty($r['data']['url'])) {
    $upd = db()->prepare('UPDATE bookings SET stripe_session_id = ? WHERE id = ?');
    $upd->execute([$r['data']['id'], (int)$b['id']]);
    return ['ok' => true, 'url' => $r['data']['url']];
  }
  return ['ok' => false, 'error' => $r['data']['error']['message'] ?? 'Erreur Stripe'];
}

/* Expire (annule) une session Checkout encore ouverte. Best-effort. */
function stripe_expire_session(string $sessionId): bool {
  if (!$sessionId || !stripe_enabled()) return false;
  $r = stripe_request('checkout/sessions/' . urlencode($sessionId) . '/expire', []);
  return $r['code'] >= 200 && $r['code'] < 300;
}

/* ---------- Mise à jour de statut ---------- */
function mark_paid(int $id): void {
  $b = get_booking($id);
  if (!$b || $b['status'] === 'paid') return;
  db()->prepare("UPDATE bookings SET status='paid', paid_at=NOW() WHERE id=?")->execute([$id]);
  $b['status'] = 'paid';
  email_confirmation($b);
}
function mark_status(int $id, string $status): void {
  if (!in_array($status, ['pending', 'paid', 'cancelled'], true)) return;
  if ($status === 'paid') { mark_paid($id); return; }
  $paidAt = $status === 'pending' ? 'NULL' : 'paid_at';
  db()->prepare("UPDATE bookings SET status=?, paid_at=$paidAt WHERE id=?")->execute([$status, $id]);
}
