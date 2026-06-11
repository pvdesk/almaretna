<?php
/**
 * Email template — Ricevuta di pagamento (guest)
 * Variabile disponibile: $data (array da ALM_Notifications::prepare_data)
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;
?>
<!DOCTYPE html>
<html lang="it" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title>Ricevuta di pagamento</title>
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
  .intro-text { font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #1A1714; line-height: 1.6; margin: 0 0 28px; }
  .receipt-box { background-color: #FAF8F5; border: 1px solid #EFEBE4; border-top: 3px solid #D4A96A; border-radius: 4px; padding: 24px 28px; margin: 0 0 28px; }
  .receipt-title { font-family: Georgia, 'Times New Roman', serif; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #8A7B68; margin: 0 0 18px; }
  .receipt-row { border-bottom: 1px solid #EFEBE4; padding: 9px 0; }
  .receipt-row:last-child { border-bottom: none; padding-bottom: 0; }
  .receipt-label { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #8A7B68; }
  .receipt-value { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1A1714; font-weight: bold; text-align: right; }
  .receipt-total-label { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #3B2F22; font-weight: bold; }
  .receipt-total-value { font-family: Georgia, 'Times New Roman', serif; font-size: 22px; color: #D4A96A; font-weight: bold; text-align: right; }
  .paid-badge { background-color: #D4EDDA; color: #155724; font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 4px 12px; border-radius: 12px; display: inline-block; }
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
    <p class="header-subtitle">Ricevuta di pagamento</p>
  </div>
  <div class="divider-gold"></div>

  <!-- Body -->
  <div class="body-content">

    <p class="greeting">Ciao <?php echo esc_html($data['first_name']); ?>,</p>
    <p class="intro-text">
      Abbiamo ricevuto il tuo pagamento.<br>
      Conserva questa ricevuta come conferma del pagamento effettuato per il soggiorno ad Almaretna.
    </p>

    <!-- Riepilogo ricevuta -->
    <div class="receipt-box">
      <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:14px;">
        <tr>
          <td>
            <p class="receipt-title" style="margin:0;">Riepilogo pagamento</p>
          </td>
          <td style="text-align:right;">
            <span class="paid-badge">PAGATO</span>
          </td>
        </tr>
      </table>
      <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr class="receipt-row">
          <td class="receipt-label">N&deg; prenotazione</td>
          <td class="receipt-value"><?php echo esc_html($data['ref']); ?></td>
        </tr>
        <tr class="receipt-row">
          <td class="receipt-label">Data pagamento</td>
          <td class="receipt-value"><?php echo esc_html(gmdate('d/m/Y H:i')); ?></td>
        </tr>
        <tr class="receipt-row">
          <td class="receipt-label">Metodo</td>
          <td class="receipt-value">Carta di credito / debito</td>
        </tr>
        <tr class="receipt-row">
          <td class="receipt-label">Camera</td>
          <td class="receipt-value"><?php echo esc_html($data['room_name']); ?></td>
        </tr>
        <tr class="receipt-row">
          <td class="receipt-label">Check-in</td>
          <td class="receipt-value"><?php echo esc_html($data['checkin_display']); ?></td>
        </tr>
        <tr class="receipt-row">
          <td class="receipt-label">Check-out</td>
          <td class="receipt-value"><?php echo esc_html($data['checkout_display']); ?></td>
        </tr>
        <tr class="receipt-row">
          <td class="receipt-label">Durata</td>
          <td class="receipt-value"><?php echo esc_html((string) $data['nights']); ?> <?php echo (int) $data['nights'] === 1 ? 'notte' : 'notti'; ?></td>
        </tr>
        <tr style="border-top: 2px solid #EFEBE4;">
          <td style="padding-top: 16px;" class="receipt-total-label">Totale addebitato</td>
          <td style="padding-top: 16px;" class="receipt-total-value"><?php echo esc_html($data['total_display']); ?></td>
        </tr>
      </table>
    </div>

    <p style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#8A7B68;line-height:1.6;margin:0 0 8px;">
      Il pagamento &egrave; stato elaborato in modo sicuro tramite Stripe.<br>
      Per qualsiasi domanda scrivi a
      <a href="mailto:<?php echo esc_attr($data['host_email']); ?>" style="color:#8A7B68;"><?php echo esc_html($data['host_email']); ?></a>
    </p>

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
