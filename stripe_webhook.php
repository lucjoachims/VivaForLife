<?php
/* ============================================================
   stripe_webhook.php
   Endpoint à déclarer dans Stripe (Développeurs > Webhooks).
   Événements : checkout.session.completed, checkout.session.expired
   ============================================================ */
require_once __DIR__ . '/lib.php';

$payload   = file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$secret    = defined('STRIPE_WEBHOOK_SECRET') ? STRIPE_WEBHOOK_SECRET : '';

/* --- Vérification de signature (sans SDK) --- */
function stripe_verify(string $payload, string $sigHeader, string $secret, int $tolerance = 300): bool {
  if (!$secret) return false;
  $t = null; $v1 = [];
  foreach (explode(',', $sigHeader) as $part) {
    $kv = explode('=', trim($part), 2);
    if (count($kv) !== 2) continue;
    if ($kv[0] === 't')  $t = $kv[1];
    if ($kv[0] === 'v1') $v1[] = $kv[1];
  }
  if (!$t || !$v1) return false;
  $expected = hash_hmac('sha256', $t . '.' . $payload, $secret);
  $ok = false;
  foreach ($v1 as $sig) if (hash_equals($expected, $sig)) $ok = true;
  if (!$ok) return false;
  if (abs(time() - (int)$t) > $tolerance) return false;   // anti-rejeu
  return true;
}

if (!stripe_verify($payload, $sigHeader, $secret)) {
  http_response_code(400);
  echo 'invalid signature';
  exit;
}

$event = json_decode($payload, true);
$type  = $event['type'] ?? '';
$obj   = $event['data']['object'] ?? [];
$ref   = $obj['client_reference_id'] ?? ($obj['metadata']['ref'] ?? '');

if ($ref) {
  $b = get_booking_by_ref($ref);
  if ($b) {
    if ($type === 'checkout.session.completed' && ($obj['payment_status'] ?? '') === 'paid') {
      mark_paid((int)$b['id']);          // marque payé + e-mail de confirmation (idempotent)
    } elseif ($type === 'checkout.session.expired') {
      if ($b['status'] === 'pending') {  // libère la place réservée
        db()->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND status='pending'")
            ->execute([(int)$b['id']]);
      }
    }
  }
}

http_response_code(200);
echo 'ok';
