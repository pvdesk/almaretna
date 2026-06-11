<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_SMTP
 *
 * Configura PHPMailer tramite l'hook phpmailer_init di WordPress,
 * usando le credenziali SMTP salvate nelle opzioni del plugin.
 *
 * Credenziali: priorità costanti wp-config.php → fallback DB (alm_smtp_settings).
 *   define('ALM_SMTP_HOST',       'smtp.gmail.com');
 *   define('ALM_SMTP_PORT',       587);
 *   define('ALM_SMTP_ENCRYPTION', 'tls');  // 'tls' | 'ssl' | ''
 *   define('ALM_SMTP_USERNAME',   'noreply@example.com');
 *   define('ALM_SMTP_PASSWORD',   'app_password_here');
 *   define('ALM_SMTP_FROM_EMAIL', 'noreply@example.com');
 *   define('ALM_SMTP_FROM_NAME',  'Almaretna');
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

class ALM_SMTP {

    private const OPTION_KEY = 'alm_smtp_settings';

    // ── Config ──────────────────────────────────────────────────────────────

    public static function get_config(): array {
        $db = get_option(self::OPTION_KEY, []);

        return [
            'host'       => defined('ALM_SMTP_HOST')       ? ALM_SMTP_HOST       : ($db['host']       ?? ''),
            'port'       => defined('ALM_SMTP_PORT')        ? (int) ALM_SMTP_PORT : (int) ($db['port'] ?? 587),
            'encryption' => defined('ALM_SMTP_ENCRYPTION')  ? ALM_SMTP_ENCRYPTION : ($db['encryption'] ?? 'tls'),
            'username'   => defined('ALM_SMTP_USERNAME')    ? ALM_SMTP_USERNAME   : ($db['username']   ?? ''),
            'password'   => defined('ALM_SMTP_PASSWORD')    ? ALM_SMTP_PASSWORD   : ($db['password']   ?? ''),
            'from_email' => defined('ALM_SMTP_FROM_EMAIL')  ? ALM_SMTP_FROM_EMAIL : ($db['from_email'] ?? ''),
            'from_name'  => defined('ALM_SMTP_FROM_NAME')   ? ALM_SMTP_FROM_NAME  : ($db['from_name']  ?? ''),
        ];
    }

    public static function is_configured(): bool {
        $cfg = self::get_config();
        return $cfg['host'] !== '' && $cfg['username'] !== '' && $cfg['password'] !== '';
    }

    public static function config_source(): string {
        return defined('ALM_SMTP_HOST') && ALM_SMTP_HOST !== '' ? 'config' : 'db';
    }

    // ── PHPMailer hook ───────────────────────────────────────────────────────

    public static function configure_phpmailer(PHPMailer\PHPMailer\PHPMailer $mailer): void {
        if (!self::is_configured()) {
            return;
        }

        $cfg = self::get_config();

        $mailer->isSMTP();
        $mailer->Host       = $cfg['host'];
        $mailer->Port       = $cfg['port'];
        $mailer->SMTPAuth   = true;
        $mailer->Username   = $cfg['username'];
        $mailer->Password   = $cfg['password'];

        if ($cfg['encryption'] === 'ssl') {
            $mailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($cfg['encryption'] === 'tls') {
            $mailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mailer->SMTPSecure = '';
            $mailer->SMTPAutoTLS = false;
        }

        if ($cfg['from_email'] !== '') {
            $mailer->setFrom($cfg['from_email'], $cfg['from_name'] ?: $cfg['from_email']);
        }
    }

    // ── Salva impostazioni (usato da AJAX admin) ──────────────────────────────

    public static function save(array $data): void {
        $current = get_option(self::OPTION_KEY, []);

        $settings = [
            'host'       => sanitize_text_field($data['host']       ?? ''),
            'port'       => (int) ($data['port']                    ?? 587),
            'encryption' => in_array($data['encryption'] ?? '', ['tls', 'ssl', ''], true)
                                ? ($data['encryption'] ?? 'tls') : 'tls',
            'username'   => sanitize_text_field($data['username']   ?? ''),
            'from_email' => sanitize_email($data['from_email']      ?? ''),
            'from_name'  => sanitize_text_field($data['from_name']  ?? ''),
        ];

        // Password: preserva il valore corrente se il campo arriva vuoto
        $pw = $data['password'] ?? '';
        $settings['password'] = $pw !== '' ? $pw : ($current['password'] ?? '');

        update_option(self::OPTION_KEY, $settings);
    }
}
