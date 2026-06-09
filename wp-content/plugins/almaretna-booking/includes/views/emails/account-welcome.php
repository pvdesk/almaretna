<?php
/**
 * Email template — Benvenuto: account creato automaticamente
 *
 * Variabili disponibili (in scope da ALM_Notifications::send_account_welcome):
 *   $first_name       — nome ospite
 *   $booking_ref      — riferimento prenotazione
 *   $set_password_url — URL per impostare la password
 *   $dashboard_url    — URL dashboard "Le mie prenotazioni"
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
<title>Benvenuto su Almaretna</title>
<style type="text/css">
  body { margin: 0 !important; padding: 0 !important; background-color: #EFEBE4; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
  .email-wrapper { background-color: #EFEBE4; padding: 32px 16px; }
  .email-card { max-width: 600px; margin: 0 auto; background-color: #FAF8F5; border-radius: 4px; overflow: hidden; box-shadow: 0 2px 16px rgba(59,47,34,0.10); }
  .header { background-color: #3B2F22; padding: 40px 48px; text-align: center; }
  .header-title { font-family: Georgia, 'Times New Roman', serif; font-size: 28px; letter-spacing: 4px; color: #FAF8F5; margin: 0; text-transform: uppercase; }
  .header-subtitle { font-family: Georgia, 'Times New Roman', serif; font-size: 13px; color: #C5B49C; letter-spacing: 2px; margin: 8px 0 0; text-transform: uppercase; }
  .divider-gold { height: 2px; background-color: #D4A96A; margin: 0; }
  .body-content { padding: 40px 48px; }
  .greeting { font-family: Georgia, 'Times New Roman', serif; font-size: 20px; color: #1A1714; margin: 0 0 12px; }
  .intro-text { font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #1A1714; line-height: 1.7; margin: 0 0 28px; }
  .info-block { background-color: #EFEBE4; border-radius: 4px; padding: 22px 28px; margin: 0 0 28px; }
  .info-block-title { font-family: Georgia, 'Times New Roman', serif; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #3B2F22; margin: 0 0 10px; }
  .info-block-text { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1A1714; line-height: 1.7; margin: 0; }
  .info-block-text strong { color: #3B2F22; }
  .cta-primary { display: inline-block; background-color: #D4A96A; color: #FAF8F5; text-decoration: none; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 14px 36px; border-radius: 2px; }
  .cta-secondary { display: inline-block; background-color: transparent; border: 2px solid #3B2F22; color: #3B2F22; text-decoration: none; font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 11px 28px; border-radius: 2px; }
  .cta-wrapper { text-align: center; margin: 0 0 16px; }
  .cta-wrapper-secondary { text-align: center; margin: 0 0 28px; }
  .security-note { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #8A7B68; text-align: center; line-height: 1.6; margin: 0 0 28px; }
  .divider { height: 1px; background-color: #EFEBE4; margin: 0 0 28px; }
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
    <h1 class="header-title">Almaretna</h1>
    <p class="header-subtitle">Villa con Piscina &mdash; Sicilia</p>
  </div>
  <div class="divider-gold"></div>

  <!-- Body -->
  <div class="body-content">

    <p class="greeting">Ciao <?php echo esc_html($first_name); ?>,</p>
    <p class="intro-text">
      Grazie per aver prenotato ad Almaretna!<br><br>
      Per facilitare la gestione del tuo soggiorno, abbiamo creato automaticamente
      un account personale associato alla tua prenotazione
      (<strong><?php echo esc_html($booking_ref); ?></strong>).<br><br>
      Con il tuo account puoi consultare i dettagli della prenotazione, visualizzare
      lo stato del pagamento e gestire i tuoi futuri soggiorni da Almaretna.
    </p>

    <!-- Info account -->
    <div class="info-block">
      <p class="info-block-title">Il tuo account</p>
      <p class="info-block-text">
        L&rsquo;account &egrave; gi&agrave; attivo. Per accedere devi prima
        <strong>impostare una password</strong> cliccando il pulsante qui sotto.<br><br>
        Il link per impostare la password &egrave; valido per <strong>24 ore</strong>.
      </p>
    </div>

    <!-- CTA primario: imposta password -->
    <div class="cta-wrapper">
      <a href="<?php echo esc_url($set_password_url); ?>" class="cta-primary">
        Imposta la tua password
      </a>
    </div>

    <p class="security-note">
      Se non hai effettuato questa prenotazione, puoi ignorare questa email in sicurezza.
    </p>

    <div class="divider"></div>

    <!-- CTA secondario: vai alle prenotazioni -->
    <div class="cta-wrapper-secondary">
      <a href="<?php echo esc_url($dashboard_url); ?>" class="cta-secondary">
        Vai alle mie prenotazioni
      </a>
    </div>

    <p class="sign-off">
      A presto ad Almaretna!<br><br>
      Con affetto,<br>
      <strong>Il team di Almaretna</strong>
    </p>

  </div>

  <!-- Footer -->
  <div class="footer">
    <p class="footer-text">Via Scorciavacca Montarsi, 48 &mdash; Nunziata di Mascali (CT) &mdash; Sicilia</p>
    <p class="footer-text">&copy; <?php echo esc_html((string) gmdate('Y')); ?> Almaretna &mdash; <a href="<?php echo esc_url(home_url('/')); ?>">www.almaretna.com</a></p>
    <p class="footer-text" style="margin-top:8px;font-size:11px;color:#8A7B68;">
      Questa email &egrave; stata inviata perch&eacute; &egrave; stata effettuata una prenotazione su Almaretna con questo indirizzo email.
    </p>
  </div>

</div>
</div>
</body>
</html>
