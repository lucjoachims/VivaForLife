# À table pour Viva for Life — site de réservation (PHP / MySQL / Stripe / Brevo)

Site d'une page pour réserver et **payer à l'avance** son repas lors d'une soirée caritative.
On ne réserve **que le repas** : la soirée qui suit est en accès libre.

- **Menu configurable** depuis l'admin : plats (par défaut pâtes bolognaise, 4 fromages,
  carbonara), chacun en version **adulte** et **enfant** avec son propre prix.
- Apéritif et dessert inclus pour tous (texte configurable).
- Deux capacités indépendantes : **places sur place** et **repas à emporter**.
- Paiement par **carte (Stripe Checkout)** ou par **virement** (communication structurée
  belge générée automatiquement). E-mails de confirmation via **Brevo**.
- Espace organisateur intégré : réservations, paiements, **fiche cuisine par plat**
  (adulte/enfant × sur place/emporter), éditeur de menu, réglages, export CSV.

---

## Contenu

| Fichier | Rôle |
|---|---|
| `index.php` | La page publique + l'espace organisateur (front-end) |
| `api.php` | API JSON (réservations, état, admin) |
| `lib.php` | Cœur : base de données, capacités, e-mails Brevo, Stripe |
| `stripe_webhook.php` | Reçoit les paiements Stripe (confirme / libère les places) |
| `schema.sql` | Tables MySQL à créer une fois (installation neuve) |
| `migrate_v2.sql` | Migration si tu as déjà l'ancienne version « 3 formules » |
| `config.example.php` | Modèle de configuration → **à copier en `config.php`** |
| `.htaccess` | Bloque l'accès web direct aux fichiers sensibles |

---

## Installation (≈ 15 min)

### 1. Base de données
Crée une base MySQL (ou utilise une existante), puis importe `schema.sql`
(phpMyAdmin → *Importer*, ou en ligne de commande) :
```
mysql -u TON_USER -p TA_BASE < schema.sql
```
Ça crée les tables `settings`, `dishes`, `bookings` et `booking_items`, et insère les
réglages et les trois plats par défaut.

**Déjà l'ancienne version en ligne ?** N'importe pas `schema.sql` : lance **une seule fois**
`migrate_v2.sql` à la place. Les anciennes réservations sont conservées.

### 2. Configuration
Copie `config.example.php` en **`config.php`** et remplis :
- **MySQL** : `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
- **`BASE_URL`** : l'URL publique exacte du dossier (ex. `https://ton-domaine.be/repas`)
- **`ADMIN_PASSWORD`** : ton code d'accès organisateur
- **E-mails (Brevo)** — au choix :
  - **SMTP** (recommandé si tu préfères) : `SMTP_HOST`, `SMTP_PORT` (587=TLS / 465=SSL),
    `SMTP_SECURE`, `SMTP_USER`, `SMTP_PASS`. S'il est rempli, c'est lui qui est utilisé.
  - **API HTTP** : laisse `SMTP_HOST` vide et renseigne `BREVO_API_KEY`.
  - Dans les deux cas : `SENDER_EMAIL` doit être un expéditeur **vérifié** dans Brevo, + `SENDER_NAME`.
- **Stripe** : `STRIPE_SECRET_KEY` (laisse vide pour désactiver la carte), `STRIPE_WEBHOOK_SECRET`

### 3. Dépôt des fichiers
Téléverse tout le dossier sur ton serveur (FTP/SSH). Vérifie que `config.php`
n'est **pas** accessible depuis le web (le `.htaccess` s'en charge sur Apache ;
sur Nginx, ajoute une règle équivalente).

### 4. Webhook Stripe
Dans Stripe → *Développeurs → Webhooks → + Ajouter un endpoint* :
- **URL** : `BASE_URL/stripe_webhook.php`
- **Événements** : `checkout.session.completed` **et** `checkout.session.expired`
- Copie le **secret de signature** (`whsec_…`) dans `config.php` → `STRIPE_WEBHOOK_SECRET`

### 5. Test
- Ouvre l'URL : la page doit s'afficher et montrer les places restantes.
- Fais une résa **virement** → tu reçois l'e-mail d'infos de paiement.
- Fais une résa **carte** avec une carte de test Stripe (`4242 4242 4242 4242`,
  date future, CVC quelconque) → retour sur le site + e-mail de confirmation.
- Clique **⚙ Espace organisateur** (en bas à droite), entre `ADMIN_PASSWORD`.

---

## Le modèle : plats, variantes, capacités

- Une réservation est soit **sur place**, soit **à emporter** (une personne qui veut
  les deux fait deux réservations).
- Elle contient un ou plusieurs **plats**, chacun en version **adulte** ou **enfant**.
  Le nom et le prix sont copiés dans la réservation : modifier le menu ensuite ne
  change pas les réservations existantes.
- Deux plafonds réglables (onglet *Réglages*) : **places sur place** (`capDine`) et
  **repas à emporter** (`capTake`). Un plafond = un nombre de repas.

Les contraintes et les montants sont vérifiés **côté serveur**, dans une transaction
avec verrou, donc impossible de vendre plus de places que disponible même en cas de
clics simultanés.

---

## Statuts & paiements

- **Virement** : la réservation est créée en *À payer*, l'e-mail contient l'IBAN et la
  communication structurée. Tu marques *Payé* dans l'admin à réception du virement.
- **Carte** : la réservation est *À payer* le temps du paiement (la place est tenue
  ~30 min). Stripe confirme via le webhook → passage automatique en *Payé* +
  e-mail de confirmation. Paiement non terminé → la place est libérée automatiquement.

Une réservation *À payer* **occupe** sa place (pour éviter la survente). Pense à
annuler depuis l'admin les virements jamais reçus pour libérer les places.

---

## Sécurité — points importants

- Les secrets restent dans `config.php` (jamais envoyés au navigateur).
- Requêtes SQL préparées partout (pas d'injection).
- Montants recalculés côté serveur (jamais d'après le navigateur).
- Webhook Stripe : signature vérifiée + protection anti-rejeu.
- Recommandé : servir le site en **HTTPS** (obligatoire pour Stripe en production).

## Personnalisation rapide
Tout se modifie depuis l'espace organisateur, sans toucher au code :
- Onglet **Menu & prix** : ajouter / renommer / réordonner / désactiver des plats,
  prix adulte et enfant.
- Onglet **Réglages** : nom, cause, date, heure, lieu, capacités, texte « inclus pour
  tous », info de retrait à emporter, texte « et après le repas », IBAN, objectif.
