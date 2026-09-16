-- ============================================================
--  À table pour Viva for Life — schéma MySQL (installation neuve)
--  À importer une seule fois (phpMyAdmin > Importer, ou
--  mysql -u USER -p BASE < schema.sql)
--
--  Si tu as déjà l'ancienne version (formules Repas+Soirée /
--  Soirée seule / Emporter), utilise plutôt migrate_v2.sql.
-- ============================================================

SET NAMES utf8mb4;

-- Réglages de l'événement (clé/valeur, modifiables depuis l'admin) -------
CREATE TABLE IF NOT EXISTS settings (
  skey  VARCHAR(64)  NOT NULL PRIMARY KEY,
  sval  TEXT         NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Plats au menu (modifiables depuis l'admin) ------------------------------
CREATE TABLE IF NOT EXISTS dishes (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(120) NOT NULL,
  description  VARCHAR(255) NULL,
  emoji        VARCHAR(16)  NULL,
  price_adult  DECIMAL(8,2) NOT NULL DEFAULT 0,
  price_child  DECIMAL(8,2) NOT NULL DEFAULT 0,
  active       TINYINT(1)   NOT NULL DEFAULT 1,
  sort_order   INT          NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Réservations ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS bookings (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  ref               VARCHAR(32)  NOT NULL UNIQUE,           -- communication structurée
  name              VARCHAR(160) NOT NULL,
  email             VARCHAR(190) NULL,
  phone             VARCHAR(40)  NULL,
  notes             TEXT         NULL,
  mode              ENUM('dine','take') NOT NULL DEFAULT 'dine',  -- sur place / à emporter
  qty_dine          INT NOT NULL DEFAULT 0,                 -- nb de repas sur place
  qty_take          INT NOT NULL DEFAULT 0,                 -- nb de repas à emporter
  amount_cents      INT NOT NULL DEFAULT 0,
  status            ENUM('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
  method            ENUM('transfer','stripe') NOT NULL DEFAULT 'transfer',
  stripe_session_id VARCHAR(255) NULL,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  paid_at           DATETIME NULL,
  KEY idx_status (status),
  KEY idx_session (stripe_session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Détail des plats d'une réservation -------------------------------------
-- (le nom et le prix sont copiés au moment de la résa : changer le menu
--  ensuite ne modifie pas les réservations existantes)
CREATE TABLE IF NOT EXISTS booking_items (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  booking_id  BIGINT UNSIGNED NOT NULL,
  dish_id     INT UNSIGNED NULL,
  dish_name   VARCHAR(120) NOT NULL,
  variant     ENUM('adult','child') NOT NULL DEFAULT 'adult',
  qty         INT NOT NULL DEFAULT 0,
  unit_cents  INT NOT NULL DEFAULT 0,
  KEY idx_booking (booking_id),
  KEY idx_dish (dish_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Valeurs par défaut ----------------------------------------------------
INSERT INTO settings (skey, sval) VALUES
  ('eventName',    'À table pour Viva for Life'),
  ('cause',        'Viva for Life'),
  ('date',         'Sam. 14 mars 2026'),
  ('time',         'Dès 18h30'),
  ('place',        'Salle communale, Awans'),
  ('goal',         '2000'),
  ('capDine',      '80'),
  ('capTake',      '40'),
  ('includedText', 'Apéritif et dessert (panna cotta) offerts avec chaque repas'),
  ('takeText',     'Retrait des repas à emporter sur place, entre 17h30 et 19h00'),
  ('afterText',    'Après le repas, la soirée DJ continue jusqu''au bout de la nuit. Elle est en accès libre : pas besoin de réserver, on ne réserve que son repas !'),
  ('iban',         'BE00 0000 0000 0000'),
  ('accountName',  'ASBL À table pour Viva for Life')
ON DUPLICATE KEY UPDATE sval = sval;

INSERT INTO dishes (name, description, emoji, price_adult, price_child, active, sort_order)
SELECT d.* FROM (
            SELECT 'Pâtes bolognaise' AS name, 'La classique, sauce maison mijotée' AS description, '🍝' AS emoji, 14.00 AS price_adult, 8.00 AS price_child, 1 AS active, 1 AS sort_order
  UNION ALL SELECT 'Pâtes 4 fromages', 'Onctueuses et généreuses', '🧀', 14.00, 8.00, 1, 2
  UNION ALL SELECT 'Pâtes carbonara', 'Lardons, crème et parmesan', '🥓', 14.00, 8.00, 1, 3
) AS d
WHERE NOT EXISTS (SELECT 1 FROM dishes);
