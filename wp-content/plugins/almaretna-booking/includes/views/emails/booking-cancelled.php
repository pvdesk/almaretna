<?php
/**
 * Email template — Prenotazione annullata (guest)
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
<title>Prenotazione annullata</title>
<style type="text/css">
  body { margin: 0 !important; padding: 0 !important; background-color: #EFEBE4; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
  .email-wrapper { background-color: #EFEBE4; padding: 32px 16px; }
  .email-card { max-width: 600px; margin: 0 auto; background-color: #FAF8F5; border-radius: 4px; overflow: hidden; box-shadow: 0 2px 16px rgba(59,47,34,0.10); }
  .header { background-color: #3B2F22; padding: 36px 48px; text-align: center; }
  .header-title { font-family: Georgia, 'Times New Roman', serif; font-size: 22px; color: #FAF8F5; margin: 0 0 6px; letter-spacing: 1px; }
  .header-ref { font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #C5B49C; margin: 0; letter-spacing: 1px; }
  .divider-muted { height: 2px; background-color: #8A7B68; margin: 0; }
  .body-content { padding: 40px 48px; }
  .greeting { font-family: Georgia, 'Times New Roman', serif; font-size: 20px; color: #1A1714; margin: 0 0 12px; }
  .intro-text { font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #1A1714; line-height: 1.6; margin: 0 0 28px; }
  .summary-box { background-color: #FAF8F5; border: 1px solid #EFEBE4; border-top: 3px solid #8A7B68; border-radius: 4px; padding: 24px 28px; margin: 0 0 28px; }
  .summary-title { font-family: Georgia, 'Times New Roman', serif; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #8A7B68; margin: 0 0 16px; }
  .data-table { width: 100%; border-collapse: collapse; }
  .data-table td { font-family: Arial, Helvetica, sans-serif; font-size: 14px; padding: 9px 0; border-bottom: 1px solid #EFEBE4; vertical-align: top; }
  .data-table td:first-child { color: #8A7B68; }
  .data-table td:last-child { color: #1A1714; font-weight: bold; text-align: right; }
  .data-table tr:last-child td { border-bottom: none; }
  .note-box { background-color: #EFEBE4; border-radius: 4px; padding: 20px 24px; margin: 0 0 28px; }
  .note-box-text { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1A1714; line-height: 1.7; margin: 0; }
  .note-box-text a { color: #3B2F22; text-decoration: underline; }
  .rebook-box { border: 1px solid #D4A96A; border-radius: 4px; padding: 20px 24px; margin: 0 0 28px; text-align: center; }
  .rebook-title { font-family: Georgia, 'Times New Roman', serif; font-size: 16px; color: #3B2F22; margin: 0 0 8px; }
  .rebook-text { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #8A7B68; line-height: 1.6; margin: 0 0 16px; }
  .cta-btn { display: inline-block; background-color: #D4A96A; color: #FAF8F5; text-decoration: none; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 12px 32px; border-radius: 2px; }
  .sign-off { font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #1A1714; line-height: 1.6; margin: 0; }
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
    <h1 class="header-title">Prenotazione annullata</h1>
    <p class="header-ref"><?php echo esc_html($data['ref']); ?></p>
  </div>
  <div class="divider-muted"></div>

  <!-- Body -->
  <div class="body-content">

    <p class="greeting">Ciao <?php echo esc_html($data['first_name']); ?>,</p>
    <p class="intro-text">
      La tua prenotazione ad Almaretna &egrave; stata <strong>annullata</strong>.<br>
      Di seguito trovi il riepilogo del soggiorno cancellato.
    </p>

    <!-- Riepilogo -->
    <div class="summary-box">
      <p class="summary-title">Dettagli prenotazione annullata</p>
      <table class="data-table" role="presentation" cellspacing="0" cellpadding="0" border="0">
        <tr>
          <td>N&deg; prenotazione</td>
          <td><?php echo esc_html($data['ref']); ?></td>
        </tr>
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
          <td>Totale</td>
          <td><?php echo esc_html($data['total_display']); ?></td>
        </tr>
      </table>
    </div>

    <!-- Nota rimborso -->
    <div class="note-box">
      <p class="note-box-text">
        <strong>Rimborso:</strong> se hai effettuato un pagamento, il rimborso verr&agrave; elaborato
        entro 5&ndash;7 giorni lavorativi secondo la nostra politica di cancellazione.<br><br>
        Per qualsiasi chiarimento scrivi a
        <a href="mailto:<?php echo esc_attr($data['host_email']); ?>"><?php echo esc_html($data['host_email']); ?></a>
      </p>
    </div>

    <!-- Invito a riprenotare -->
    <div class="rebook-box">
      <p class="rebook-title">Speriamo di rivederti presto ad Almaretna</p>
      <p class="rebook-text">Le nostre camere sono disponibili per nuove date. Saremo felici di accoglierti in un&rsquo;altra occasione.</p>
      <a href="<?php echo esc_url($data['site_url']); ?>" class="cta-btn">
        Scopri le disponibilit&agrave;
      </a>
    </div>

    <p class="sign-off">
      Cordiali saluti,<br>
      <strong><?php echo esc_html($data['host_name']); ?></strong>
    </p>

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
