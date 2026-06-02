# Almaretna — Tema e Plugin Custom

Repository delle personalizzazioni WordPress per **Almaretna Villa Vacanze**.

> ⚠️ Questo repo contiene **solo i file personalizzati**. Il core WordPress, i plugin di terze parti e i temi stock vanno installati separatamente.

---

## Contenuto del repository

```
wp-content/
├── themes/
│   └── almaretna-child/      ← Tema figlio (richiede Astra attivo)
└── plugins/
    └── almaretna-booking/    ← Plugin prenotazione + Beds24 + Stripe

build-packages.php            ← Script per generare i ZIP di distribuzione
dist/                         ← ZIP pronti all'installazione
```

---

## Installazione su WordPress vergine

### Metodo rapido (installer automatico)

1. Installa WordPress + tema **Astra** dal repository ufficiale
2. Copia nella cartella `wp-content/`:
   - `dist/almaretna-child.zip`
   - `dist/almaretna-booking.zip`
   - `dist/almaretna-installer.php`
3. Apri nel browser:
   ```
   https://tuosito.it/wp-content/almaretna-installer.php?token=almaretna-setup-2024
   ```
4. **Elimina** `almaretna-installer.php` dopo l'installazione

### Metodo manuale

1. Installa e attiva il tema **Astra**
2. Carica `dist/almaretna-child.zip` → Aspetto → Temi → Aggiungi
3. Attiva il tema **Almaretna Child**
4. Carica `dist/almaretna-booking.zip` → Plugin → Aggiungi
5. Attiva il plugin **Almaretna Booking**

---

## Rigenerare i pacchetti ZIP

```bash
php build-packages.php
```

Genera `dist/almaretna-child.zip` e `dist/almaretna-booking.zip`.

---

## Configurazione chiavi API (in wp-config.php)

```php
// Stripe
define('ALM_STRIPE_SECRET_KEY',      'sk_live_...');
define('ALM_STRIPE_PUBLISHABLE_KEY', 'pk_live_...');
define('ALM_STRIPE_WEBHOOK_SECRET',  'whsec_...');

// Beds24
define('ALM_BEDS24_API_TOKEN',       '...');
define('ALM_BEDS24_PROP_KEY',        '...');
define('ALM_BEDS24_WEBHOOK_TOKEN',   '...');
```

---

## Workflow di sviluppo

```bash
# Modifica i file in wp-content/themes/almaretna-child/ o wp-content/plugins/almaretna-booking/
# poi:
git add .
git commit -m "descrizione della modifica"
git push origin main
```

---

## Struttura tema child

```
almaretna-child/
├── assets/
│   ├── css/         ← layout.css, premium.css, responsive.css, rooms.css …
│   ├── js/          ← navigation.js, booking-form.js, calendar.js
│   └── img/         ← logo, icone PWA
├── inc/
│   ├── customizer.php    ← opzioni Aspetto → Personalizza
│   ├── shortcodes.php    ← [alm_rooms_grid], [alm_image], [alm_gallery]
│   └── …
├── templates/
│   ├── template-rooms.php
│   └── template-booking.php
├── front-page.php   ← Homepage premium
├── single-almaretna_room.php  ← Pagina singola camera
└── functions.php    ← Enqueue, palette override, scroll-to-top custom
```
