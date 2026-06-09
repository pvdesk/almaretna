<?php
/**
 * Email template — Promemoria pre-arrivo (48h prima del check-in)
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
<title>Ci vediamo tra poco!</title>
<style type="text/css">
  body { margin: 0 !important; padding: 0 !important; background-color: #EFEBE4; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
  .email-wrapper { background-color: #EFEBE4; padding: 32px 16px; }
  .email-card { max-width: 600px; margin: 0 auto; background-color: #FAF8F5; border-radius: 4px; overflow: hidden; box-shadow: 0 2px 16px rgba(59,47,34,0.10); }
  .header { background-color: #3B2F22; padding: 40px 48px; text-align: center; }
  .header-title { font-family: Georgia, 'Times New Roman', serif; font-size: 28px; letter-spacing: 4px; color: #FAF8F5; margin: 0; text-transform: uppercase; }
  .header-subtitle { font-family: Georgia, 'Times New Roman', serif; font-size: 14px; color: #D4A96A; letter-spacing: 1px; margin: 10px 0 0; font-style: italic; }
  .divider-gold { height: 2px; background-color: #D4A96A; margin: 0; }
  .body-content { padding: 40px 48px; }
  .greeting { font-family: Georgia, 'Times New Roman', serif; font-size: 20px; color: #1A1714; margin: 0 0 12px; }
  .intro-text { font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #1A1714; line-height: 1.6; margin: 0 0 28px; }
  .checkin-card { background-color: #3B2F22; border-radius: 4px; padding: 28px 32px; margin: 0 0 28px; text-align: center; }
  .checkin-label { font-family: Arial, Helvetica, sans-serif; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #C5B49C; margin: 0 0 8px; }
  .checkin-date { font-family: Georgia, 'Times New Roman', serif; font-size: 34px; color: #FAF8F5; margin: 0 0 6px; font-weight: bold; }
  .checkin-time { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #D4A96A; margin: 0; }
  .data-table { width: 100%; border-collapse: collapse; margin: 0 0 28px; }
  .data-table td { font-family: Arial, Helvetica, sans-serif; font-size: 14px; padding: 10px 0; border-bottom: 1px solid #EFEBE4; vertical-align: middle; }
  .data-table td:first-child { color: #8A7B68; }
  .data-table td:last-child { color: #1A1714; font-weight: bold; text-align: right; }
  .data-table tr:last-child td { border-bottom: none; }
  .info-block { background-color: #EFEBE4; border-radius: 4px; padding: 22px 28px; margin: 0 0 28px; }
  .info-block-title { font-family: Georgia, 'Times New Roman', serif; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #3B2F22; margin: 0 0 12px; }
  .info-block-text { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1A1714; line-height: 1.7; margin: 0; }
  .info-block-text a { color: #8A7B68; text-decoration: underline; }
  .sign-off { font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #1A1714; line-height: 1.6; margin: 0 0 28px; }
  .cta-wrapper { text-align: center; padding: 4px 0 16px; }
  .cta-btn { display: inline-block; background-color: #D4A96A; color: #FAF8F5; text-decoration: none; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 14px 36px; border-radius: 2px; }
  .footer { background-color: #3B2F22; padding: 28px 48px; text-align: center; }
  .footer-text { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #C5B49C; line-height: 1.6; margin: 0 0 4px; }
  .footer-text a { color: #D4A96A; text-decoration: none; }
</style>
</head>
<body>
<div class="email-wrapper">
<div class="email-card">

  <!-- Header -->
  <div class="header">
    <h1 class="header-title">Almaretna</h1>
    <p class="header-subtitle">Ci vediamo tra poco!</p>
  </div>
  <div class="divider-gold"></div>

  <!-- Body -->
  <div class="body-content">

    <p class="greeting">Ciao <?php echo esc_html($data['first_name']); ?>,</p>
    <p class="intro-text">
      Mancano solo <strong>2 giorni</strong> al tuo arrivo ad Almaretna.<br>
      Siamo felici di accoglierti e ci stiamo preparando per rendere il tuo soggiorno indimenticabile.
    </p>

    <!-- Check-in card -->
    <div class="checkin-card">
      <p class="checkin-label">Il tuo check-in</p>
      <p class="checkin-date"><?php echo esc_html($data['checkin_display']); ?></p>
      <p class="checkin-time">dalle ore <?php echo esc_html($data['checkin_time']); ?></p>
    </div>

    <!-- Dettagli soggiorno -->
    <table class="data-table" role="presentation" cellspacing="0" cellpadding="0" border="0">
      <tr>
        <td>Camera</td>
        <td><?php echo esc_html($data['room_name']); ?></td>
      </tr>
      <tr>
        <td>Check-out</td>
        <td><?php echo esc_html($data['checkout_display']); ?> &mdash; entro le <?php echo esc_html($data['checkout_time']); ?></td>
      </tr>
      <tr>
        <td>Ospiti</td>
        <td>
          <?php echo esc_html((string) $data['adults']); ?> adulti
          <?php if ((int) $data['children'] > 0) echo ' + ' . esc_html((string) $data['children']) . ' bambini'; ?>
        </td>
      </tr>
      <tr>
        <td>N&deg; prenotazione</td>
        <td><?php echo esc_html($data['ref']); ?></td>
      </tr>
    </table>

    <!-- Come raggiungerci -->
    <div class="info-block">
      <p class="info-block-title">Come raggiungerci</p>
      <p class="info-block-text">
        Via Scorciavacca Montarsi, 48 &mdash; Nunziata di Mascali (CT)<br>
        <a href="https://maps.google.com/?q=Via+Scorciavacca+Montarsi+48+Nunziata+di+Mascali+CT">Apri in Google Maps</a><br><br>
        <strong>Parcheggio</strong> privato disponibile in struttura, gratuito.<br><br>
        <strong>Contatti:</strong>
        <a href="mailto:<?php echo esc_attr($data['host_email']); ?>"><?php echo esc_html($data['host_email']); ?></a><br>
        Per qualsiasi esigenza siamo a tua disposizione.
      </p>
    </div>

    <p class="sign-off">
      Non vediamo l&rsquo;ora di farti scoprire il nostro angolo di Sicilia!<br><br>
      A presto,<br>
      <strong><?php echo esc_html($data['host_name']); ?></strong>
    </p>

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
    <p class="footer-text">&copy; <?php echo esc_html((string) gmdate('Y')); ?> Almaretna &mdash; <a href="<?php echo esc_url($data['site_url']); ?>">www.almaretna.com</a></p>
  </div>

</div>
</div>
</body>
</html>
