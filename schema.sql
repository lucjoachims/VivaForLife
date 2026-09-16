-- ============================================================
--  Le Grand Repas — schéma MySQL
--  À importer une seule fois (phpMyAdmin > Importer, ou
--  mysql -u USER -p BASE < schema.sql)
-- ============================================================

SET NAMES utf8mb4;

-- Réglages de l'événement (clé/valeur, modifiables depuis l'admin) -------
CREATE TABLE IF NOT EXISTS settings (
  skey  VARCHAR(64)  NOT NULL PRIMARY KEY,
  sval  TEXT         NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Réservations ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS bookings (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  ref               VARCHAR(32)  NOT NULL UNIQUE,           -- communication structurée
  name              VARCHAR(160) NOT NULL,
  email             VARCHAR(190) NULL,
  phone             VARCHAR(40)  NULL,
  notes             TEXT         NULL,
  qty_dine          INT NOT NULL DEFAULT 0,                 -- Repas + Soirée
  qty_party         INT NOT NULL DEFAULT 0,                 -- Soirée seule
  qty_take          INT NOT NULL DEFAULT 0,                 -- À emporter
  amount_cents      INT NOT NULL DEFAULT 0,
  status            ENUM('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
  method            ENUM('transfer','stripe') NOT NULL DEFAULT 'transfer',
  stripe_session_id VARCHAR(255) NULL,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  paid_at           DATETIME NULL,
  KEY idx_status (status),
  KEY idx_session (stripe_session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Valeurs par défaut ----------------------------------------------------
INSERT INTO settings (skey, sval) VALUES
  ('eventName',   'Le Grand Repas'),
  ('cause',       'La cause'),
  ('date',        'Sam. 14 mars 2026'),
  ('time',        'Dès 19h00'),
  ('place',       'Salle communale, Juprelle'),
  ('goal',        '2000'),
  ('priceDine',   '25'),
  ('priceParty',  '10'),
  ('priceTake',   '15'),
  ('capDine',     '50'),
  ('capKitchen',  '60'),
  ('capParty',    '90'),
  ('iban',        'BE00 0000 0000 0000'),
  ('accountName', 'ASBL Le Grand Repas')
ON DUPLICATE KEY UPDATE sval = sval;
