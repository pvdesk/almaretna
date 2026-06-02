# Almaretna — Installazione su WordPress Vergine

## Requisiti
- WordPress 6.0+
- PHP 8.1+
- MySQL/MariaDB 5.7+
- Estensione PHP: `zip`, `curl`, `json`

---

## Metodo 1 — Installer automatico (consigliato)

1. Installa WordPress normalmente
2. Attiva il plugin **Astra** dal repository WP (o installalo manualmente)
3. Copia nella cartella `wp-content/` questi file:
   - `almaretna-child.zip`
   - `almaretna-booking.zip`
   - `almaretna-installer.php`
4. Apri nel browser:
   ```
   https://tuosito.it/wp-content/almaretna-installer.php?token=almaretna-setup-2024
   ```
5. L'installer creerà tutto automaticamente
6. **Elimina** `almaretna-installer.php` dopo l'installazione

---

## Metodo 2 — Installazione manuale

### A. Installa il tema

1. Installa e attiva il tema genitore **Astra** da WP → Aspetto → Temi
2. Carica `almaretna-child.zip` in WP → Aspetto → Temi → Aggiungi → Carica
3. Attiva il tema **Almaretna Child**

### B. Installa il plugin

1. Carica `almaretna-booking.zip` in WP → Plugin → Aggiungi → Carica plugin
2. Attiva il plugin **Almaretna Booking**

### C. Crea le pagine

Crea queste pagine in WP → Pagine → Aggiungi:

| Titolo          | Slug       | Template                          |
|-----------------|------------|-----------------------------------|
| Home            | home       | (default)                         |
| Le Nostre Camere | camere    | Template Rooms                    |
| Prenota         | prenota    | Template Booking                  |
| Chi Siamo       | chi-siamo  | (default)                         |
| Contatti        | contatti   | (default)                         |

Imposta "Home" come pagina statica: WP → Impostazioni → Lettura

### D. Configura le chiavi API

In `wp-config.php` aggiungi:

```php
// Stripe
define('ALM_STRIPE_SECRET_KEY',      'sk_live_...');
define('ALM_STRIPE_PUBLISHABLE_KEY', 'pk_live_...');
define('ALM_STRIPE_WEBHOOK_SECRET',  'whsec_...');

// Beds24 (channel manager)
define('ALM_BEDS24_API_TOKEN',     'il_tuo_token');
define('ALM_BEDS24_PROP_KEY',      'il_tuo_prop_key');
define('ALM_BEDS24_WEBHOOK_TOKEN', 'segreto_webhook');
```

### E. Dove trovare le chiavi

**Stripe:**
- Vai su [dashboard.stripe.com](https://dashboard.stripe.com)
- Developers → API Keys → copia `Secret key` e `Publishable key`
- Developers → Webhooks → Add endpoint → inserisci: `https://tuosito.it/wp-json/scv/v1/stripe/webhook`
- Seleziona eventi: `payment_intent.succeeded`, `payment_intent.payment_failed`, `charge.refunded`

**Beds24:**
- Vai su [beds24.com](https://beds24.com) → Settings → Account → API
- Crea un token API (token personale)
- Prop Key: Settings → Property → Property Key
- Webhook URL da inserire in Beds24: `https://tuosito.it/wp-json/scv/v1/beds24/webhook`

---

## Struttura file generati

```
dist/
├── almaretna-child.zip       # Tema figlio Astra
├── almaretna-booking.zip     # Plugin prenotazione + Beds24 + Stripe
├── almaretna-installer.php   # Installer one-click (eliminare dopo uso)
└── INSTALLAZIONE.md          # Questo file
```

---

## Dopo l'installazione — Checklist

- [ ] Chiavi Stripe configurate in wp-config.php
- [ ] Chiavi Beds24 configurate in wp-config.php
- [ ] Email struttura impostata in Prenotazioni → Impostazioni
- [ ] Telefono struttura impostato
- [ ] Almeno una camera inserita (CPT → Le Mie Camere)
- [ ] Logo caricato in Aspetto → Personalizza → Logo
- [ ] Foto hero impostata
- [ ] Menu di navigazione assegnato
- [ ] Permalink impostato su /%postname%/
- [ ] SSL/HTTPS attivo
- [ ] almaretna-installer.php eliminato