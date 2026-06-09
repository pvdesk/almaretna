<?php
/**
 * Email template — Notifica nuova prenotazione (host/admin)
 * Variabile disponibile: $data (array da ALM_Notifications::prepare_data)
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

$admin_url = admin_url('admin.php?page=alm-bookings');
?>
<!DOCTYPE html>
<html lang="it" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title>Nuova prenotazione ricevuta</title>
<style type="text/css">
  body { margin: 0 !important; padding: 0 !important; background-color: #EFEBE4; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
  .email-wrapper { background-color: #EFEBE4; padding: 28px 16px; }
  .email-card { max-width: 600px; margin: 0 auto; background-color: #FAF8F5; border-radius: 4px; overflow: hidden; box-shadow: 0 2px 16px rgba(59,47,34,0.10); }
  .header { background-color: #3B2F22; padding: 28px 40px; }
  .header-label { font-family: Arial, Helvetica, sans-serif; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #C5B49C; margin: 0 0 6px; }
  .header-title { font-family: Georgia, 'Times New Roman', serif; font-size: 22px; color: #FAF8F5; margin: 0; }
  .header-ref { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #D4A96A; font-weight: bold; }
  .divider-gold { height: 2px; background-color: #D4A96A; margin: 0; }
  .body-content { padding: 36px 40px; }
  .section-title { font-family: Georgia, 'Times New Roman', serif; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #8A7B68; margin: 0 0 14px; border-bottom: 1px solid #EFEBE4; padding-bottom: 8px; }
  .data-table { width: 100%; border-collapse: collapse; margin: 0 0 28px; }
  .data-table td { font-family: Arial, Helvetica, sans-serif; font-size: 14px; padding: 9px 0; border-bottom: 1px solid #EFEBE4; vertical-align: top; }
  .data-table td:first-child { color: #8A7B68; width: 38%; }
  .data-table td:last-child { color: #1A1714; font-weight: bold; text-align: right; }
  .data-table td a { color: #3B2F22; text-decoration: underline; }
  .data-table .total-row td { border-bottom: none; padding-top: 14px; }
  .data-table .total-row td:first-child { font-family: Georgia, 'Times New Roman', serif; font-size: 15px; font-weight: bold; color: #3B2F22; }
  .data-table .total-row td:last-child { font-family: Georgia, 'Times New Roman', serif; font-size: 20px; color: #D4A96A; }
  .badge { display: inline-block; background-color: #3B2F22; color: #D4A96A; font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 3px 10px; border-radius: 2px; }
  .cta-wrapper { text-align: center; padding: 8px 0 12px; }
  .cta-btn { display: inline-block; background-color: #3B2F22; color: #D4A96A; text-decoration: none; font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 12px 32px; border-radius: 2px; }
  .footer { background-color: #3B2F22; padding: 20px 40px; text-align: center; }
  .footer-text { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #C5B49C; margin: 0; line-height: 1.6; }
</style>
</head>
<body>
<div class="email-wrapper">
<div class="email-card">

  <!-- Header -->
  <div class="header">
    <p class="header-label">Almaretna &mdash; Sistema prenotazioni</p>
    <h1 class="header-title">Nuova prenotazione ricevuta</h1>
    <p style="margin:6px 0 0;font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#C5B49C;">
      Riferimento: <span class="header-ref"><?php echo esc_html($data['ref']); ?></span>
    </p>
  </div>
  <div class="divider-gold"></div>

  <!-- Body -->
  <div class="body-content">

    <!-- Dati ospite -->
    <p class="section-title">Dati ospite</p>
    <table class="data-table" role="presentation" cellspacing="0" cellpadding="0" border="0">
      <tr>
        <td>Nome</td>
        <td><?php echo esc_html($data['guest_name']); ?></td>
      </tr>
      <tr>
        <td>Email</td>
        <td><a href="mailto:<?php echo esc_attr($data['email']); ?>"><?php echo esc_html($data['email']); ?></a></td>
      </tr>
      <tr>
        <td>Telefono</td>
        <td><?php echo !empty($data['phone']) ? esc_html($data['phone']) : '&mdash;'; ?></td>
      </tr>
      <?php if (!empty($data['special_requests'])) : ?>
      <tr>
        <td>Richieste speciali</td>
        <td><?php echo nl2br(esc_html($data['special_requests'])); ?></td>
      </tr>
      <?php endif; ?>
    </table>

    <!-- Dettagli soggiorno -->
    <p class="section-title">Dettagli soggiorno</p>
    <table class="data-table" role="presentation" cellspacing="0" cellpadding="0" border="0">
      <tr>
        <td>Camera</td>
        <td><?php echo esc_html($data['room_name']); ?></td>
      </tr>
      <tr>
        <td>Check-in</td>
        <td><?php echo esc_html($data['checkin_display']); ?></td>
      </tr>
      <tr>
        <td>Check-out</td>
        <td><?php echo esc_html($data['checkout_display']); ?></td>
      </tr>
      <tr>
        <td>Notti</td>
        <td><?php echo esc_html((string) $data['nights']); ?></td>
      </tr>
      <tr>
        <td>Ospiti</td>
        <td>
          <?php echo esc_html((string) $data['adults']); ?> adulti
          <?php if ((int) $data['children'] > 0) echo ' + ' . esc_html((string) $data['children']) . ' bambini'; ?>
        </td>
      </tr>
      <tr>
        <td>Canale</td>
        <td><span class="badge"><?php echo esc_html(strtoupper($data['channel'])); ?></span></td>
      </tr>
      <tr>
        <td>Metodo pagamento</td>
        <td>Carta di credito (Stripe)</td>
      </tr>
      <tr class="total-row">
        <td>Totale</td>
        <td><?php echo esc_html($data['total_display']); ?></td>
      </tr>
    </table>

    <!-- Link admin -->
    <div class="cta-wrapper">
      <a href="<?php echo esc_url($admin_url); ?>" class="cta-btn">
        Gestisci nel pannello admin
      </a>
    </div>

    <p style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#8A7B68;text-align:center;margin:16px 0 0;line-height:1.6;">
      Questa email &egrave; stata generata automaticamente dal sistema di prenotazione Almaretna.
    </p>

  </div>

  <!-- Footer -->
  <div class="footer">
    <p class="footer-text">Almaretna Booking System &mdash; <?php echo esc_html(home_url('/')); ?></p>
  </div>

</div>
</div>
</body>
</html>
