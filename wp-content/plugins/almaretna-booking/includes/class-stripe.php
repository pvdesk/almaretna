<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_Stripe
 *
 * Wrapper leggero per l'API Stripe v1 senza SDK ufficiale.
 * Usa wp_remote_post/get e verifica i webhook con HMAC-SHA256.
 *
 * Chiavi: priorità costanti wp-config.php → fallback DB (alm_stripe_keys).
 *   define('ALM_STRIPE_SECRET_KEY',      'sk_live_...' );
 *   define('ALM_STRIPE_PUBLISHABLE_KEY', 'pk_live_...' );
 *   define('ALM_STRIPE_WEBHOOK_SECRET',  'whsec_...'  );
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

class ALM_Stripe {

    private const API_BASE      = 'https://api.stripe.com/v1/';
    private const WEBHOOK_TOL   = 300; // secondi di tolleranza timestamp

    // ── Chiavi (costanti wp-config.php > DB) ───────────────────────────────

    public static function get_publishable_key(): string {
        if (defined('ALM_STRIPE_PUBLISHABLE_KEY') && ALM_STRIPE_PUBLISHABLE_KEY !== '') {
            return ALM_STRIPE_PUBLISHABLE_KEY;
        }
        $db = get_option('alm_stripe_keys', []);
        return (string) ($db['publishable_key'] ?? '');
    }

    private static function secret_key(): string {
        if (defined('ALM_STRIPE_SECRET_KEY') && ALM_STRIPE_SECRET_KEY !== '') {
            return ALM_STRIPE_SECRET_KEY;
        }
        $db = get_option('alm_stripe_keys', []);
        return (string) ($db['secret_key'] ?? '');
    }

    private static function webhook_secret(): string {
        if (defined('ALM_STRIPE_WEBHOOK_SECRET') && ALM_STRIPE_WEBHOOK_SECRET !== '') {
            return ALM_STRIPE_WEBHOOK_SECRET;
        }
        $db = get_option('alm_stripe_keys', []);
        return (string) ($db['webhook_secret'] ?? '');
    }

    // ── Fonte chiavi (per UI admin) ────────────────────────────────────────

    public static function keys_source(): string {
        return defined('ALM_STRIPE_SECRET_KEY') && ALM_STRIPE_SECRET_KEY !== '' ? 'config' : 'db';
    }

    public static function is_test_mode(): bool {
        $key = self::get_publishable_key();
        return str_starts_with($key, 'pk_test_');
    }

    public static function is_configured(): bool {
        return self::secret_key() !== '' && self::get_publishable_key() !== '';
    }

    // ── PaymentIntent ───────────────────────────────────────────────────────

    /**
     * Crea un PaymentIntent Stripe.
     *
     * @param int    $amount_cents Importo in centesimi (es. 10000 = €100,00).
     * @param string $currency     Codice valuta ISO 4217 (default 'eur').
     * @param array  $metadata     Coppie chiave/valore allegati al PaymentIntent.
     * @return array|WP_Error      Dati PaymentIntent o errore.
     */
    public static function create_payment_intent(
        int    $amount_cents,
        string $currency = 'eur',
        array  $metadata = []
    ): array|WP_Error {
        if (!self::is_configured()) {
            return new WP_Error('stripe_not_configured', __('Stripe non configurato.', 'almaretna-booking'));
        }

        $body = [
            'amount'               => $amount_cents,
            'currency'             => strtolower($currency),
            'automatic_payment_methods[enabled]' => 'true',
        ];

        foreach ($metadata as $k => $v) {
            $body['metadata[' . $k . ']'] = (string) $v;
        }

        return self::request('POST', 'payment_intents', $body);
    }

    /**
     * Recupera un PaymentIntent esistente.
     *
     * @param string $payment_intent_id
     * @return array|WP_Error
     */
    public static function get_payment_intent(string $payment_intent_id): array|WP_Error {
        if (!self::is_configured()) {
            return new WP_Error('stripe_not_configured', __('Stripe non configurato.', 'almaretna-booking'));
        }
        if (!$payment_intent_id) {
            return new WP_Error('stripe_invalid_id', __('ID PaymentIntent non valido.', 'almaretna-booking'));
        }
        return self::request('GET', 'payment_intents/' . rawurlencode($payment_intent_id));
    }

    /**
     * Annulla (rimborsa) un pagamento già completato.
     *
     * @param string   $payment_intent_id
     * @param int|null $amount_cents  Importo parziale; null = rimborso totale.
     * @return array|WP_Error
     */
    public static function refund(string $payment_intent_id, ?int $amount_cents = null): array|WP_Error {
        if (!self::is_configured()) {
            return new WP_Error('stripe_not_configured', __('Stripe non configurato.', 'almaretna-booking'));
        }
        if (!$payment_intent_id) {
            return new WP_Error('stripe_invalid_id', __('ID PaymentIntent non valido.', 'almaretna-booking'));
        }

        $body = ['payment_intent' => $payment_intent_id];
        if ($amount_cents !== null) {
            $body['amount'] = $amount_cents;
        }

        return self::request('POST', 'refunds', $body);
    }

    // ── Webhook ─────────────────────────────────────────────────────────────

    /**
     * Verifica la firma del webhook Stripe (HMAC-SHA256, nessun SDK).
     *
     * Formato header: Stripe-Signature: t=1234567890,v1=abc123...
     *
     * @param string $payload     Body grezzo della richiesta.
     * @param string $sig_header  Valore dell'header Stripe-Signature.
     * @return bool
     */
    public static function verify_webhook_signature(string $payload, string $sig_header): bool {
        $secret = self::webhook_secret();
        if (!$secret || !$sig_header) {
            return false;
        }

        // Parse t= e v1= dall'header
        $timestamp  = null;
        $signatures = [];

        foreach (explode(',', $sig_header) as $part) {
            $part = trim($part);
            if (str_starts_with($part, 't=')) {
                $timestamp = (int) substr($part, 2);
            } elseif (str_starts_with($part, 'v1=')) {
                $signatures[] = substr($part, 3);
            }
        }

        if ($timestamp === null || empty($signatures)) {
            return false;
        }

        // Rifiuta webhook troppo vecchi
        if (abs(time() - $timestamp) > self::WEBHOOK_TOL) {
            return false;
        }

        $signed_payload = $timestamp . '.' . $payload;
        $expected       = hash_hmac('sha256', $signed_payload, $secret);

        foreach ($signatures as $sig) {
            if (hash_equals($expected, $sig)) {
                return true;
            }
        }

        return false;
    }

    // ── HTTP helper ─────────────────────────────────────────────────────────

    /**
     * Esegue una chiamata all'API Stripe.
     *
     * @param string $method  'GET' | 'POST'
     * @param string $endpoint Percorso relativo (es. 'payment_intents').
     * @param array  $body    Parametri per POST.
     * @return array|WP_Error
     */
    private static function request(string $method, string $endpoint, array $body = []): array|WP_Error {
        $url  = self::API_BASE . $endpoint;
        $args = [
            'method'  => $method,
            'timeout' => 30,
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(self::secret_key() . ':'),
                'Stripe-Version'=> '2023-10-16',
            ],
        ];

        if ($method === 'POST' && !empty($body)) {
            $args['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
            $args['body']                    = http_build_query($body, '', '&');
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code($response);
        $raw  = wp_remote_retrieve_body($response);
        $data = json_decode($raw, true);

        if (!is_array($data)) {
            return new WP_Error(
                'stripe_invalid_response',
                __('Risposta Stripe non valida.', 'almaretna-booking')
            );
        }

        if ($code >= 400) {
            $err_obj = $data['error'] ?? [];
            return new WP_Error(
                'stripe_api_error',
                $err_obj['message'] ?? __('Errore API Stripe.', 'almaretna-booking'),
                ['code' => $err_obj['code'] ?? null, 'type' => $err_obj['type'] ?? null]
            );
        }

        return $data;
    }
}
