-- ============================================================
--  Migration v1 → v2 : formules → plats configurables
--  À lancer UNE fois sur une base qui a déjà l'ancien schéma.
--  (Sur une base neuve, importe simplement schema.sql.)
--
--  ⚠ Les anciennes réservations gardent leurs quantités mais
--    n'ont pas de détail par plat ; les « Soirée seule » sont
--    conservées à titre d'historique (qty_party) mais ne comptent
--    plus dans les capacités.
-- ============================================================

SET NAMES utf8mb4;

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

ALTER TABLE bookings
  ADD COLUMN mode ENUM('dine','take') NOT NULL DEFAULT 'dine' AFTER notes;

-- Une ancienne résa uniquement « à emporter » passe en mode take
UPDATE bookings SET mode = 'take' WHERE qty_dine = 0 AND qty_take > 0;

INSERT INTO settings (skey, sval) VALUES
  ('capTake',      '40'),
  ('includedText', 'Apéritif et dessert (panna cotta) offerts avec chaque repas'),
  ('takeText',     'Retrait des repas à emporter sur place, entre 17h30 et 19h00'),
  ('helpPhone',    '0470 00 00 00'),
  ('afterText',    'Après le repas, la soirée DJ continue jusqu''au bout de la nuit. Elle est en accès libre : pas besoin de réserver, on ne réserve que son repas !')
ON DUPLICATE KEY UPDATE sval = sval;

DELETE FROM settings WHERE skey IN ('priceDine','priceParty','priceTake','capKitchen','capParty');

INSERT INTO dishes (name, description, emoji, price_adult, price_child, active, sort_order)
SELECT d.* FROM (
            SELECT 'Pâtes bolognaise' AS name, 'La classique, sauce maison mijotée' AS description, '🍝' AS emoji, 14.00 AS price_adult, 8.00 AS price_child, 1 AS active, 1 AS sort_order
  UNION ALL SELECT 'Pâtes 4 fromages', 'Onctueuses et généreuses', '🧀', 14.00, 8.00, 1, 2
  UNION ALL SELECT 'Pâtes carbonara', 'Lardons, crème et parmesan', '🥓', 14.00, 8.00, 1, 3
) AS d
WHERE NOT EXISTS (SELECT 1 FROM dishes);
