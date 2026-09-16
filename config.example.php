<?php
/* ============================================================
   CONFIGURATION — copie ce fichier en "config.php" et remplis-le.
   NE JAMAIS committer / partager config.php : il contient tes secrets.
   Le .htaccess fourni empêche déjà son accès direct par le web.
   ============================================================ */

// --- Base de données MySQL ---------------------------------------------
define('DB_HOST', 'waystopkyberiprd.mysql.db');
define('DB_NAME', 'waystopkyberiprd');
define('DB_USER', '');
define('DB_PASS', '');

// --- URL publique du site (sans slash final) ---------------------------
// Sert à construire les liens de retour Stripe et ceux des e-mails.
define('BASE_URL', 'https://reservation.kyberis.be');

// --- Espace organisateur (admin) ---------------------------------------
// Choisis un mot de passe solide. Il reste côté serveur, jamais exposé.
define('ADMIN_PASSWORD', 'xxxxx');

// --- Brevo (e-mails transactionnels) -----------------------------------
// Clé : Brevo > SMTP & API > API Keys.  L'expéditeur doit être un
// expéditeur/domaine VÉRIFIÉ dans ton compte Brevo, sinon l'envoi échoue.
 define('SMTP_HOST',   'smtp-relay.brevo.com');
 define('SMTP_PORT',   587);
 define('SMTP_SECURE', 'tls');                 // 'ssl' | 'tls' | ''
 define('SMTP_USER',   'ae64dd001@smtp-brevo.com');
 define('SMTP_PASS',   'xxxxx');
define('SENDER_EMAIL',  'no-reply@kyberis.be');
define('SENDER_NAME',   'Reservation Viva for Life Awans');

// Notification à l'organisateur à chaque nouvelle réservation (optionnel)
define('ORGANIZER_EMAIL',  'luc.joachims@gmail.com');     // laisse vide pour désactiver
define('ORGANIZER_NOTIFY', true);

// --- Stripe (paiement par carte, Checkout — paiement unique) -----------
// Clés : Stripe Dashboard > Développeurs > Clés API.
// Laisse STRIPE_SECRET_KEY vide pour désactiver la carte (virement seul).
define('STRIPE_SECRET_KEY',  'sk_test_51SqX600VZ0IK0h7');
// Webhook : Stripe > Développeurs > Webhooks > "+ Ajouter un endpoint"
//   URL = BASE_URL . '/stripe_webhook.php'
//   Événements : checkout.session.completed  ET  checkout.session.expired
// Copie ensuite le "secret de signature" (whsec_...) ici :
define('STRIPE_WEBHOOK_SECRET', 'whsecMP64xr');

// --- Divers ------------------------------------------------------------
define('CURRENCY', 'eur');          // devise Stripe
define('TIMEZONE', 'Europe/Brussels');
