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
    'capDine'    => (int) setting('capDine', 50),
    'capKitchen' => (int) setting('capKitchen', 60),
    'capParty'   => (int) setting('capParty', 90),
  ];
}
function prices_cents(): array {
  return [
    'dine'  => (int) round(((float) setting('priceDine', 25)) * 100),
    'party' => (int) round(((float) setting('priceParty', 10)) * 100),
    'take'  => (int) round(((float) setting('priceTake', 15)) * 100),
  ];
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
  $sql = "SELECT
            COALESCE(SUM(qty_dine),0)  AS d,
            COALESCE(SUM(qty_party),0) AS p,
            COALESCE(SUM(qty_take),0)  AS t
          FROM bookings WHERE status <> 'cancelled'";
  if ($lock) $sql .= ' FOR UPDATE';
  $r = $pdo->query($sql)->fetch();
  $d = (int)$r['d']; $p = (int)$r['p']; $t = (int)$r['t'];
  return ['d' => $d, 'p' => $p, 't' => $t, 'meals' => $d + $t];
}
function remaining(?array $c = null): array {
  $c = $c ?? counts(db());
  $k = caps();
  $remDine  = max(0, min($k['capDine'] - $c['d'],
                         $k['capKitchen'] - $c['meals'],
                         $k['capParty'] - ($c['d'] + $c['p'])));
  $remTake  = max(0, $k['capKitchen'] - $c['meals']);
  $remParty = max(0, $k['capParty'] - ($c['d'] + $c['p']));
  return ['dine' => $remDine, 'party' => $remParty, 'take' => $remTake];
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
   $data: name,email,phone,notes, qd,qp,qt, method
   Retourne le booking, ou ['error'=>'sold','remaining'=>...]
   ============================================================ */
function create_booking(array $data) {
  $pdo = db();
  $qd = max(0, (int)($data['qd'] ?? 0));
  $qp = max(0, (int)($data['qp'] ?? 0));
  $qt = max(0, (int)($data['qt'] ?? 0));
  $method = ($data['method'] ?? 'transfer') === 'stripe' ? 'stripe' : 'transfer';

  $pdo->beginTransaction();
  try {
    $c = counts($pdo, true);                 // verrou
    $k = caps();
    $okDine  = $qd <= ($k['capDine'] - $c['d']);
    $okMeals = ($qd + $qt) <= ($k['capKitchen'] - $c['meals']);
    $okParty = ($qd + $qp) <= ($k['capParty'] - ($c['d'] + $c['p']));
    if (!$okDine || !$okMeals || !$okParty) {
      $pdo->rollBack();
      return ['error' => 'sold', 'remaining' => remaining($c)];
    }

    $pc = prices_cents();
    $amount = $qd * $pc['dine'] + $qp * $pc['party'] + $qt * $pc['take'];
    $ref = gen_ref($pdo);

    $stmt = $pdo->prepare(
      'INSERT INTO bookings
        (ref,name,email,phone,notes,qty_dine,qty_party,qty_take,amount_cents,status,method)
       VALUES (?,?,?,?,?,?,?,?,?,\'pending\',?)'
    );
    $stmt->execute([
      $ref,
      mb_substr(trim($data['name'] ?? ''), 0, 160),
      mb_substr(trim($data['email'] ?? ''), 0, 190) ?: null,
      mb_substr(trim($data['phone'] ?? ''), 0, 40) ?: null,
      mb_substr(trim($data['notes'] ?? ''), 0, 1000) ?: null,
      $qd, $qp, $qt, $amount, $method,
    ]);
    $id = (int)$pdo->lastInsertId();
    $pdo->commit();
  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    throw $e;
  }
  return get_booking($id);
}

function get_booking(int $id): ?array {
  $s = db()->prepare('SELECT * FROM bookings WHERE id = ?');
  $s->execute([$id]);
  $b = $s->fetch();
  return $b ?: null;
}
function get_booking_by_ref(string $ref): ?array {
  $s = db()->prepare('SELECT * FROM bookings WHERE ref = ?');
  $s->execute([$ref]);
  $b = $s->fetch();
  return $b ?: null;
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

function email_layout(string $title, string $body): string {
  $name = htmlspecialchars(setting('eventName', 'Le Grand Repas'));
  return '<div style="font-family:Arial,Helvetica,sans-serif;max-width:560px;margin:auto;color:#1A1226">
    <div style="background:#2B1A3D;color:#FBF3E4;padding:22px 24px;border-radius:14px 14px 0 0">
      <div style="font-size:13px;letter-spacing:.12em;text-transform:uppercase;color:#8E7FA6">' . $name . '</div>
      <h1 style="margin:6px 0 0;font-size:22px">' . $title . '</h1>
    </div>
    <div style="border:1px solid #eadfcf;border-top:none;border-radius:0 0 14px 14px;padding:24px;background:#FBF3E4">'
    . $body .
    '<p style="margin-top:22px;font-size:13px;color:#8a7d63">Merci pour ton soutien 💛</p></div></div>';
}

function lines_text(array $b): string {
  $L = [];
  if ($b['qty_dine'] > 0)  $L[] = $b['qty_dine'] . ' × Repas + Soirée DJ';
  if ($b['qty_party'] > 0) $L[] = $b['qty_party'] . ' × Soirée DJ seule';
  if ($b['qty_take'] > 0)  $L[] = $b['qty_take'] . ' × Repas à emporter';
  return implode(' · ', $L);
}

function email_payment_info(array $b): bool {
  if (empty($b['email'])) return false;
  $iban = htmlspecialchars(setting('iban'));
  $acc  = htmlspecialchars(setting('accountName'));
  $body = '<p>Bonjour ' . htmlspecialchars($b['name']) . ',</p>
    <p>Ta réservation est enregistrée 🎉 — il ne reste qu\'à régler par virement pour la confirmer.</p>
    <p style="margin:4px 0"><b>' . htmlspecialchars(lines_text($b)) . '</b></p>
    <table style="width:100%;border-collapse:collapse;margin:18px 0;background:#2B1A3D;color:#FBF3E4;border-radius:10px">
      <tr><td style="padding:12px 14px 4px;font-size:12px;color:#8E7FA6">MONTANT</td></tr>
      <tr><td style="padding:0 14px 12px;font-size:22px;font-weight:bold">' . money_cents((int)$b['amount_cents']) . '</td></tr>
      <tr><td style="padding:0 14px 4px;font-size:12px;color:#8E7FA6">IBAN</td></tr>
      <tr><td style="padding:0 14px 12px;font-family:monospace;font-size:16px">' . $iban . '</td></tr>
      <tr><td style="padding:0 14px 4px;font-size:12px;color:#8E7FA6">COMMUNICATION STRUCTURÉE</td></tr>
      <tr><td style="padding:0 14px 12px;font-family:monospace;font-size:16px">' . htmlspecialchars($b['ref']) . '</td></tr>
      <tr><td style="padding:0 14px 14px;font-size:13px">Bénéficiaire : ' . $acc . '</td></tr>
    </table>
    <p style="font-size:14px">Indique bien <b>cette communication structurée</b> dans ton virement : ta place est confirmée dès réception.</p>';
  return brevo_send($b['email'], $b['name'], 'Tes infos de paiement — ' . setting('eventName'), email_layout('À régler par virement', $body));
}

function email_confirmation(array $b): bool {
  if (empty($b['email'])) return false;
  $body = '<p>Bonjour ' . htmlspecialchars($b['name']) . ',</p>
    <p>Paiement bien reçu — ta place est <b>confirmée</b> ✅. On a hâte de te voir !</p>
    <p style="margin:4px 0"><b>' . htmlspecialchars(lines_text($b)) . '</b></p>
    <table style="width:100%;border-collapse:collapse;margin:16px 0">
      <tr><td style="padding:6px 0;border-bottom:1px solid #e3d6bf">Date</td><td style="padding:6px 0;border-bottom:1px solid #e3d6bf;text-align:right"><b>' . htmlspecialchars(setting('date')) . '</b></td></tr>
      <tr><td style="padding:6px 0;border-bottom:1px solid #e3d6bf">Heure</td><td style="padding:6px 0;border-bottom:1px solid #e3d6bf;text-align:right"><b>' . htmlspecialchars(setting('time')) . '</b></td></tr>
      <tr><td style="padding:6px 0">Lieu</td><td style="padding:6px 0;text-align:right"><b>' . htmlspecialchars(setting('place')) . '</b></td></tr>
    </table>
    <p style="font-size:13px;color:#8a7d63">Référence : ' . htmlspecialchars($b['ref']) . '</p>';
  return brevo_send($b['email'], $b['name'], 'Place confirmée — ' . setting('eventName'), email_layout('C\'est confirmé !', $body));
}

function email_organizer(array $b): void {
  if (!defined('ORGANIZER_NOTIFY') || !ORGANIZER_NOTIFY) return;
  if (!defined('ORGANIZER_EMAIL') || !ORGANIZER_EMAIL) return;
  $body = '<p>Nouvelle réservation (' . ($b['method'] === 'stripe' ? 'carte' : 'virement') . ') :</p>
    <p><b>' . htmlspecialchars($b['name']) . '</b><br>' . htmlspecialchars(($b['email'] ?? '') . ' ' . ($b['phone'] ?? '')) . '</p>
    <p>' . htmlspecialchars(lines_text($b)) . '<br>Montant : ' . money_cents((int)$b['amount_cents']) . '<br>Réf : ' . htmlspecialchars($b['ref']) . '</p>';
  brevo_send(ORGANIZER_EMAIL, 'Organisateur', 'Nouvelle réservation — ' . htmlspecialchars($b['name']), email_layout('Nouvelle réservation', $body));
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
    if ($qty <= 0) return;
    $i = count($items);
    $items["line_items[$i][price_data][currency]"]              = CURRENCY;
    $items["line_items[$i][price_data][product_data][name]"]    = $label;
    $items["line_items[$i][price_data][unit_amount]"]           = $unit;
    $items["line_items[$i][quantity]"]                          = $qty;
  };
  $pc = prices_cents();
  $add('Repas + Soirée DJ',  $pc['dine'],  (int)$b['qty_dine']);
  $add('Soirée DJ seule',    $pc['party'], (int)$b['qty_party']);
  $add('Repas à emporter',   $pc['take'],  (int)$b['qty_take']);

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
