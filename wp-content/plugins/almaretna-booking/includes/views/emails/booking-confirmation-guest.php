<?php
/**
 * Email template — Conferma prenotazione (guest)
 * Variabile disponibile: $data (array da ALM_Notifications::prepare_data)
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

$dashboard_url = !empty($data['dashboard_url']) ? $data['dashboard_url'] : $data['site_url'];
?>
<!DOCTYPE html>
<html lang="it" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title>Prenotazione confermata</title>
<style type="text/css">
  body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
  table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
  img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
  body { margin: 0 !important; padding: 0 !important; background-color: #EFEBE4; }
  .email-wrapper { background-color: #EFEBE4; padding: 32px 16px; }
  .email-card { max-width: 600px; margin: 0 auto; background-color: #FAF8F5; border-radius: 4px; overflow: hidden; box-shadow: 0 2px 16px rgba(59,47,34,0.10); }
  .header { background-color: #3B2F22; padding: 40px 48px; text-align: center; }
  .header-title { font-family: Georgia, 'Times New Roman', serif; font-size: 28px; letter-spacing: 4px; color: #FAF8F5; margin: 0; text-transform: uppercase; }
  .header-subtitle { font-family: Georgia, 'Times New Roman', serif; font-size: 13px; color: #C5B49C; letter-spacing: 2px; margin: 8px 0 0; text-transform: uppercase; }
  .divider-gold { height: 2px; background-color: #D4A96A; margin: 0; }
  .body-content { padding: 40px 48px; }
  .greeting { font-family: Georgia, 'Times New Roman', serif; font-size: 20px; color: #1A1714; margin: 0 0 12px; }
  .intro-text { font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #1A1714; line-height: 1.6; margin: 0 0 32px; }
  .summary-box { background-color: #FAF8F5; border: 1px solid #EFEBE4; border-top: 3px solid #D4A96A; border-radius: 4px; padding: 24px 28px; margin: 0 0 28px; }
  .summary-title { font-family: Georgia, 'Times New Roman', serif; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #8A7B68; margin: 0 0 18px; }
  .summary-row { border-bottom: 1px solid #EFEBE4; padding: 9px 0; }
  .summary-row:last-child { border-bottom: none; padding-bottom: 0; }
  .summary-label { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #8A7B68; }
  .summary-value { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1A1714; font-weight: bold; text-align: right; }
  .summary-total-label { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #3B2F22; font-weight: bold; }
  .summary-total-value { font-family: Georgia, 'Times New Roman', serif; font-size: 20px; color: #D4A96A; font-weight: bold; text-align: right; }
  .info-block { background-color: #EFEBE4; border-radius: 4px; padding: 22px 28px; margin: 0 0 28px; }
  .info-block-title { font-family: Georgia, 'Times New Roman', serif; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #3B2F22; margin: 0 0 12px; }
  .info-block-text { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1A1714; line-height: 1.7; margin: 0; }
  .info-block-text a { color: #8A7B68; text-decoration: underline; }
  .cta-wrapper { text-align: center; padding: 8px 0 28px; }
  .cta-btn { display: inline-block; background-color: #D4A96A; color: #FAF8F5; text-decoration: none; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 14px 36px; border-radius: 2px; }
  .special-box { background-color: #FAF8F5; border-left: 3px solid #C5B49C; padding: 18px 22px; margin: 0 0 28px; border-radius: 2px; }
  .special-label { font-family: Georgia, 'Times New Roman', serif; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #8A7B68; margin: 0 0 8px; }
  .special-text { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1A1714; line-height: 1.6; margin: 0; }
  .footer { background-color: #3B2F22; padding: 28px 48px; text-align: center; }
  .footer-text { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #C5B49C; line-height: 1.6; margin: 0 0 6px; }
  .footer-text a { color: #D4A96A; text-decoration: none; }
</style>
</head>
<body>
<div class="email-wrapper">
<div class="email-card">

  <!-- Header -->
  <div class="header">
    <h1 class="header-title">Almaretna</h1>
    <p class="header-subtitle">Villa con Piscina &mdash; Sicilia</p>
  </div>
  <div class="divider-gold"></div>

  <!-- Body -->
  <div class="body-content">

    <p class="greeting">Ciao <?php echo esc_html($data['first_name']); ?>,</p>
    <p class="intro-text">
      La tua prenotazione ad Almaretna &egrave; <strong>confermata</strong>.<br>
      Non vediamo l&rsquo;ora di accoglierti nella nostra villa in Sicilia.
    </p>

    <!-- Riepilogo prenotazione -->
    <div class="summary-box">
      <p class="summary-title">Riepilogo prenotazione</p>
      <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr class="summary-row">
          <td class="summary-label">N&deg; prenotazione</td>
          <td class="summary-value"><?php echo esc_html($data['ref']); ?></td>
        </tr>
        <tr class="summary-row">
          <td class="summary-label">Camera</td>
          <td class="summary-value"><?php echo esc_html($data['room_name']); ?></td>
        </tr>
        <tr class="summary-row">
          <td class="summary-label">Check-in</td>
          <td class="summary-value"><?php echo esc_html($data['checkin_display']); ?> &mdash; ore <?php echo esc_html($data['checkin_time']); ?></td>
        </tr>
        <tr class="summary-row">
          <td class="summary-label">Check-out</td>
          <td class="summary-value"><?php echo esc_html($data['checkout_display']); ?> &mdash; entro le <?php echo esc_html($data['checkout_time']); ?></td>
        </tr>
        <tr class="summary-row">
          <td class="summary-label">Durata</td>
          <td class="summary-value"><?php echo esc_html((string) $data['nights']); ?> <?php echo (int) $data['nights'] === 1 ? 'notte' : 'notti'; ?></td>
        </tr>
        <tr class="summary-row">
          <td class="summary-label">Ospiti</td>
          <td class="summary-value">
            <?php echo esc_html((string) $data['adults']); ?> <?php echo (int) $data['adults'] === 1 ? 'adulto' : 'adulti'; ?>
            <?php if ((int) $data['children'] > 0) : ?>
              + <?php echo esc_html((string) $data['children']); ?> <?php echo (int) $data['children'] === 1 ? 'bambino' : 'bambini'; ?>
            <?php endif; ?>
          </td>
        </tr>
        <tr style="border-top: 2px solid #EFEBE4;">
          <td style="padding-top: 14px;" class="summary-total-label">Totale pagato</td>
          <td style="padding-top: 14px;" class="summary-total-value"><?php echo esc_html($data['total_display']); ?></td>
        </tr>
      </table>
    </div>

    <?php if (!empty($data['special_requests'])) : ?>
    <!-- Richieste speciali -->
    <div class="special-box">
      <p class="special-label">Richieste speciali</p>
      <p class="special-text"><?php echo nl2br(esc_html($data['special_requests'])); ?></p>
    </div>
    <?php endif; ?>

    <!-- Come arrivarci -->
    <div class="info-block">
      <p class="info-block-title">Come arrivarci</p>
      <p class="info-block-text">
        Via Scorciavacca Montarsi, 48<br>
        Nunziata di Mascali (CT) &mdash; Sicilia<br><br>
        <strong>Check-in:</strong> dalle ore <?php echo esc_html($data['checkin_time']); ?><br>
        <strong>Check-out:</strong> entro le ore <?php echo esc_html($data['checkout_time']); ?><br><br>
        Per qualsiasi necessit&agrave; scrivi a
        <a href="mailto:<?php echo esc_attr($data['host_email']); ?>"><?php echo esc_html($data['host_email']); ?></a>
      </p>
    </div>

    <!-- CTA -->
    <div class="cta-wrapper">
      <a href="<?php echo esc_url($dashboard_url); ?>" class="cta-btn">
        Visualizza la tua prenotazione
      </a>
    </div>

  </div>

  <!-- Footer -->
  <div class="footer">
    <p class="footer-text">Via Scorciavacca Montarsi, 48 &mdash; Nunziata di Mascali (CT) &mdash; Sicilia</p>
    <p class="footer-text">Email inviata a <?php echo esc_html($data['email']); ?></p>
    <p class="footer-text">&copy; <?php echo esc_html((string) gmdate('Y')); ?> Almaretna &mdash; <a href="<?php echo esc_url($data['site_url']); ?>">www.almaretna.com</a></p>
  </div>

</div>
</div>
</body>
</html>
