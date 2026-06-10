# Stripe — Promemoria installazione Almaretna

## 1. Chiavi in wp-config.php

Aggiungere **prima** della riga `/* That's all, stop editing! */`:

```php
define('ALM_STRIPE_SECRET_KEY',     'sk_live_...');
define('ALM_STRIPE_PUBLISHABLE_KEY','pk_live_...');
define('ALM_STRIPE_WEBHOOK_SECRET', 'whsec_...');
```

> Le chiavi si trovano su: https://dashboard.stripe.com/apikeys

---

## 2. Webhook su Stripe Dashboard

URL da registrare:
```
https://almaretna.it/wp-json/scv/v1/stripe/webhook
```

Percorso: **Stripe Dashboard → Sviluppatori → Webhook → Aggiungi endpoint**

Eventi da ascoltare:
- `payment_intent.succeeded`
- `payment_intent.payment_failed`

Dopo la creazione, copiare il **Signing secret** (`whsec_...`) e inserirlo in `ALM_STRIPE_WEBHOOK_SECRET`.

---

## 3. Immagini WebP mancanti

Caricare in: `wp-content/uploads/2026/06/`

File attesi (usati nel front-page.php come fallback):
- `piscina-cover-principale.webp`
- `piscina-panoramica.webp`
- `villa-esterna.webp`
- `camera-matrimoniale.webp`
- `camera-singola.webp`
- `salottino-comune.webp`

---

## 4. Verifica finale

- [ ] Pagina `/le-mie-prenotazioni/` visibile
- [ ] Booking strip homepage: calendario funziona (flatpickr locale)
- [ ] Flusso prenotazione → pagamento Stripe → email di conferma
- [ ] Webhook ricevuto correttamente (log in Stripe Dashboard)
