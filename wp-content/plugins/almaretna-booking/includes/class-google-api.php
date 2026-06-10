<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_Google_API
 *
 * Wrapper per Google Analytics Data API v1beta e Google Search Console API.
 * Autenticazione via Service Account (JWT RS256) senza SDK ufficiale.
 *
 * Setup richiesto:
 *   1. Creare un Service Account in Google Cloud Console
 *   2. Scaricare il JSON e incollarlo in WP Admin → Impostazioni
 *   3. Aggiungere il Service Account come Viewer in GA4 Property
 *   4. Aggiungere il Service Account come Utente in GSC
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

class ALM_Google_API {

    // ── Configurazione ─────────────────────────────────────────────────────

    public static function service_account(): ?array {
        $json = get_option('alm_google_sa_json', '');
        if ($json === '') return null;
        $sa = json_decode($json, true);
        return (is_array($sa) && ($sa['type'] ?? '') === 'service_account'
                && !empty($sa['private_key']) && !empty($sa['client_email']))
            ? $sa : null;
    }

    public static function is_configured(): bool {
        return self::service_account() !== null;
    }

    public static function ga4_property_id(): string {
        return (string) get_option('alm_ga4_property_id', '');
    }

    public static function gsc_site_url(): string {
        $url = get_option('alm_gsc_site_url', '');
        return $url !== '' ? $url : home_url('/');
    }

    // ── JWT / Token ────────────────────────────────────────────────────────

    private static function b64url(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function access_token(array $sa, string $scope): string|WP_Error {
        $tk = 'alm_gapi_tok_' . substr(md5($sa['client_email'] . $scope), 0, 12);
        $cached = get_transient($tk);
        if ($cached !== false) return (string) $cached;

        if (!extension_loaded('openssl')) {
            return new WP_Error('no_openssl', 'Estensione PHP openssl non disponibile.');
        }

        $now = time();
        $hdr = self::b64url((string) json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $pay = self::b64url((string) json_encode([
            'iss'   => $sa['client_email'],
            'scope' => $scope,
            'aud'   => $sa['token_uri'] ?? 'https://oauth2.googleapis.com/token',
            'exp'   => $now + 3600,
            'iat'   => $now,
        ]));

        $key = openssl_pkey_get_private($sa['private_key']);
        if (!$key) {
            return new WP_Error('jwt_key', 'Chiave privata Service Account non valida.');
        }

        $sig = '';
        openssl_sign($hdr . '.' . $pay, $sig, $key, OPENSSL_ALGO_SHA256);
        $jwt = $hdr . '.' . $pay . '.' . self::b64url($sig);

        $resp = wp_remote_post(
            $sa['token_uri'] ?? 'https://oauth2.googleapis.com/token',
            [
                'timeout' => 15,
                'body'    => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion'  => $jwt,
                ],
            ]
        );

        if (is_wp_error($resp)) return $resp;

        $body = json_decode(wp_remote_retrieve_body($resp), true);
        if (empty($body['access_token'])) {
            return new WP_Error(
                'token_err',
                $body['error_description'] ?? ($body['error'] ?? 'Errore richiesta token Google.')
            );
        }

        $ttl = max(60, (int) ($body['expires_in'] ?? 3600) - 60);
        set_transient($tk, $body['access_token'], $ttl);
        return $body['access_token'];
    }

    // ── Google Analytics Data API v1beta ──────────────────────────────────

    /**
     * Esegue un report GA4.
     *
     * @param array $payload Corpo della richiesta (dateRanges, metrics, dimensions, …)
     */
    public static function ga4_run_report(array $payload): array|WP_Error {
        $sa = self::service_account();
        if (!$sa) return new WP_Error('not_cfg', 'Service Account Google non configurato.');

        $property_id = self::ga4_property_id();
        if ($property_id === '') return new WP_Error('no_prop', 'GA4 Property ID non configurato.');

        $token = self::access_token($sa, 'https://www.googleapis.com/auth/analytics.readonly');
        if (is_wp_error($token)) return $token;

        $resp = wp_remote_post(
            "https://analyticsdata.googleapis.com/v1beta/{$property_id}:runReport",
            [
                'timeout' => 20,
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => wp_json_encode($payload),
            ]
        );

        if (is_wp_error($resp)) return $resp;
        $body = json_decode(wp_remote_retrieve_body($resp), true);
        if (isset($body['error'])) {
            return new WP_Error('ga4_api', $body['error']['message'] ?? 'Errore API GA4');
        }
        return (array) $body;
    }

    // ── Google Search Console API ─────────────────────────────────────────

    /**
     * Interroga Search Console Analytics.
     *
     * @param array $payload Corpo della richiesta (startDate, endDate, dimensions, …)
     */
    public static function gsc_query(array $payload): array|WP_Error {
        $sa = self::service_account();
        if (!$sa) return new WP_Error('not_cfg', 'Service Account Google non configurato.');

        $token = self::access_token($sa, 'https://www.googleapis.com/auth/webmasters.readonly');
        if (is_wp_error($token)) return $token;

        $site = self::gsc_site_url();
        $resp = wp_remote_post(
            'https://www.googleapis.com/webmasters/v3/sites/' . urlencode($site) . '/searchAnalytics/query',
            [
                'timeout' => 20,
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => wp_json_encode($payload),
            ]
        );

        if (is_wp_error($resp)) return $resp;
        $body = json_decode(wp_remote_retrieve_body($resp), true);
        if (isset($body['error'])) {
            return new WP_Error('gsc_api', $body['error']['message'] ?? 'Errore API GSC');
        }
        return (array) $body;
    }

    // ── Fetch dati con cache transient ────────────────────────────────────

    public static function fetch_ga4_data(bool $force = false): array|WP_Error {
        $tk = 'alm_ga4_widget_v1';
        if (!$force) {
            $c = get_transient($tk);
            if ($c !== false) return (array) $c;
        }

        $end   = gmdate('Y-m-d');
        $start = gmdate('Y-m-d', strtotime('-28 days'));

        // Report 1: totali + top pagine
        $pages_report = self::ga4_run_report([
            'dateRanges'         => [['startDate' => $start, 'endDate' => $end]],
            'metrics'            => [
                ['name' => 'sessions'],
                ['name' => 'totalUsers'],
                ['name' => 'screenPageViews'],
                ['name' => 'bounceRate'],
                ['name' => 'averageSessionDuration'],
            ],
            'dimensions'         => [['name' => 'pagePath']],
            'orderBys'           => [['metric' => ['metricName' => 'screenPageViews'], 'desc' => true]],
            'limit'              => 8,
            'metricAggregations' => ['TOTAL'],
        ]);

        if (is_wp_error($pages_report)) return $pages_report;

        // Report 2: top paesi
        $country_report = self::ga4_run_report([
            'dateRanges' => [['startDate' => $start, 'endDate' => $end]],
            'metrics'    => [['name' => 'sessions']],
            'dimensions' => [['name' => 'country']],
            'orderBys'   => [['metric' => ['metricName' => 'sessions'], 'desc' => true]],
            'limit'      => 6,
        ]);

        $data = [
            'pages'     => $pages_report,
            'countries' => is_wp_error($country_report) ? [] : $country_report,
            'fetched'   => gmdate('d/m/Y H:i'),
            'range'     => $start . ' → ' . $end,
        ];

        set_transient($tk, $data, 2 * HOUR_IN_SECONDS);
        return $data;
    }

    public static function fetch_gsc_data(bool $force = false): array|WP_Error {
        $tk = 'alm_gsc_widget_v1';
        if (!$force) {
            $c = get_transient($tk);
            if ($c !== false) return (array) $c;
        }

        $end   = gmdate('Y-m-d', strtotime('-3 days')); // GSC ha 3gg di ritardo
        $start = gmdate('Y-m-d', strtotime('-31 days'));

        // Totali (nessuna dimensione)
        $totals = self::gsc_query([
            'startDate' => $start,
            'endDate'   => $end,
        ]);

        // Top query
        $queries = self::gsc_query([
            'startDate'  => $start,
            'endDate'    => $end,
            'dimensions' => ['query'],
            'rowLimit'   => 8,
            'orderBy'    => [['fieldName' => 'clicks', 'sortOrder' => 'DESCENDING']],
        ]);

        // Top pagine
        $pages = self::gsc_query([
            'startDate'  => $start,
            'endDate'    => $end,
            'dimensions' => ['page'],
            'rowLimit'   => 6,
            'orderBy'    => [['fieldName' => 'clicks', 'sortOrder' => 'DESCENDING']],
        ]);

        if (is_wp_error($totals)) return $totals;

        $data = [
            'totals'  => $totals,
            'queries' => is_wp_error($queries) ? [] : $queries,
            'pages'   => is_wp_error($pages)   ? [] : $pages,
            'fetched' => gmdate('d/m/Y H:i'),
            'range'   => $start . ' → ' . $end,
        ];

        set_transient($tk, $data, 2 * HOUR_IN_SECONDS);
        return $data;
    }
}
