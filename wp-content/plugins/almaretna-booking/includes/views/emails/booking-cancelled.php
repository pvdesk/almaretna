<?php
/**
 * Email template — Prenotazione annullata (guest e host)
 * Il template è condiviso: viene reso neutro rispetto al destinatario.
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

// Se inviato all'host (email mittente = host) il tono è più amministrativo
$to_host = isset($data['host_email']) && $data['host_email'] === ($data['recipient_email'] ?? '');
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8" />
<title>Prenotazione annullata</title>
<style>
  body{margin:0;padding:0;background:#f5f5f0;font-family:Georgia,'Times New Roman',serif;color:#2C2C2C;}
  .wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
  .header{background:#c62828;padding:28px 40px;text-align:center;}
  .header h1{margin:0;color:#fff;font-size:22px;}
  .header p{margin:6px 0 0;color:rgba(255,255,255,.8);font-size:13px;}
  .body{padding:40px;}
  .box{background:#ffebee;border-left:4px solid #c62828;border-radius:4px;padding:20px 24px;margin:24px 0;}
  .box h2{margin:0 0 12px;font-size:15px;color:#c62828;text-transform:uppercase;letter-spacing:.4px;}
  .row{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #fcd8d8;font-size:14px;}
  .row:last-child{border-bottom:none;}
  .row .label{color:#666;font-style:italic;}
  .row .value{font-weight:bold;text-align:right;}
  .note{background:#f9f7f3;border-radius:6px;padding:16px 20px;font-size:13px;line-height:1.6;color:#555;margin:24px 0;}
  .footer{background:#1A4E6B;padding:24px 40px;text-align:center;font-size:12px;color:rgba(255,255,255,.7);}
  .footer a{color:#D4A017;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <h1>Prenotazione annullata</h1>
    <p><?php echo esc_html($data['ref']); ?></p>
  </div>

  <div class="body">
    <p>Ciao <?php echo esc_html($data['first_name']); ?>,</p>

    <p>La seguente prenotazione è stata <strong>annullata</strong>.</p>

    <div class="box">
      <h2>Dettagli prenotazione annullata</h2>
      <div class="row">
        <span class="label">N° prenotazione</span>
        <span class="value"><?php echo esc_html($data['ref']); ?></span>
      </div>
      <div class="row">
        <span class="label">Camera</span>
        <span class="value"><?php echo esc_html($data['room_name']); ?></span>
      </div>
      <div class="row">
        <span class="label">Check-in</span>
        <span class="value"><?php echo esc_html($data['checkin_display']); ?></span>
      </div>
      <div class="row">
        <span class="label">Check-out</span>
        <span class="value"><?php echo esc_html($data['checkout_display']); ?></span>
      </div>
      <div class="row">
        <span class="label">Notti</span>
        <span class="value"><?php echo esc_html((string) $data['nights']); ?></span>
      </div>
      <div class="row">
        <span class="label">Totale</span>
        <span class="value"><?php echo esc_html($data['total_display']); ?></span>
      </div>
    </div>

    <div class="note">
      <strong>Rimborso:</strong> se hai effettuato un pagamento, il rimborso verrà elaborato
      entro 5-7 giorni lavorativi secondo la nostra
      <a href="<?php echo esc_url(home_url('/cancellazione/')); ?>" style="color:#1A4E6B;">politica di cancellazione</a>.<br><br>
      Per qualsiasi chiarimento scrivi a
      <a href="mailto:<?php echo esc_attr($data['host_email']); ?>" style="color:#1A4E6B;"><?php echo esc_html($data['host_email']); ?></a>
    </div>

    <p>Speriamo di poterti accogliere in un'altra occasione.<br>
    Cordiali saluti,<br>
    <strong><?php echo esc_html($data['host_name']); ?></strong></p>
  </div>

  <div class="footer">
    <p>© <?php echo esc_html((string) gmdate('Y')); ?> Almaretna · Via Scorciavacca Montarsi, 48 · Nunziata di Mascali (CT)</p>
    <p><a href="<?php echo esc_url($data['site_url']); ?>">www.almaretna.com</a></p>
  </div>

</div>
</body>
</html>
