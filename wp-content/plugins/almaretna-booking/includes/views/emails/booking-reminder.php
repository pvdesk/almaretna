<?php
/**
 * Email template — Promemoria pre-arrivo (48h prima del check-in)
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8" />
<title>Ci vediamo presto!</title>
<style>
  body{margin:0;padding:0;background:#f5f5f0;font-family:Georgia,'Times New Roman',serif;color:#2C2C2C;}
  .wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
  .header{background:#1A4E6B;padding:32px 40px;text-align:center;}
  .header h1{margin:0;color:#fff;font-size:24px;}
  .header p{margin:6px 0 0;color:rgba(255,255,255,.8);font-size:14px;}
  .body{padding:40px;}
  .greeting{font-size:18px;margin-bottom:24px;}
  .checkin-card{background:#1A4E6B;color:#fff;border-radius:8px;padding:24px 32px;margin:24px 0;text-align:center;}
  .checkin-card .date{font-size:32px;font-weight:bold;margin:8px 0;}
  .checkin-card .label{font-size:13px;opacity:.8;text-transform:uppercase;letter-spacing:.5px;}
  .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin:24px 0;}
  .info-item{background:#f9f7f3;border-radius:6px;padding:16px;text-align:center;font-size:14px;}
  .info-item .icon{font-size:24px;margin-bottom:6px;}
  .info-item strong{display:block;color:#1A4E6B;margin-bottom:2px;}
  .info-box{background:#e8f5e9;border-left:4px solid #1A4E6B;border-radius:4px;padding:16px 20px;margin:24px 0;font-size:14px;line-height:1.6;}
  .footer{background:#1A4E6B;padding:24px 40px;text-align:center;font-size:12px;color:rgba(255,255,255,.7);}
  .footer a{color:#D4A017;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <h1>Almaretna</h1>
    <p>Ci vediamo dopodomani! ☀️</p>
  </div>

  <div class="body">
    <p class="greeting">Ciao <?php echo esc_html($data['first_name']); ?>,</p>

    <p>Mancano solo <strong>2 giorni</strong> al tuo soggiorno da Almaretna. Siamo felici di accoglierti!</p>

    <div class="checkin-card">
      <div class="label">Check-in</div>
      <div class="date"><?php echo esc_html($data['checkin_display']); ?></div>
      <div style="font-size:14px;opacity:.9;">dalle ore <?php echo esc_html($data['checkin_time']); ?></div>
    </div>

    <table style="width:100%;border-collapse:collapse;">
      <tr>
        <td style="padding:8px 0;color:#555;">Camera</td>
        <td style="padding:8px 0;font-weight:bold;text-align:right;"><?php echo esc_html($data['room_name']); ?></td>
      </tr>
      <tr>
        <td style="padding:8px 0;color:#555;">Check-out</td>
        <td style="padding:8px 0;font-weight:bold;text-align:right;"><?php echo esc_html($data['checkout_display']); ?> — entro le <?php echo esc_html($data['checkout_time']); ?></td>
      </tr>
      <tr>
        <td style="padding:8px 0;color:#555;">Ospiti</td>
        <td style="padding:8px 0;font-weight:bold;text-align:right;">
          <?php echo esc_html((string) $data['adults']); ?> adulti
          <?php if ($data['children'] > 0) echo '+ ' . esc_html((string) $data['children']) . ' bambini'; ?>
        </td>
      </tr>
      <tr>
        <td style="padding:8px 0;color:#555;">N° prenotazione</td>
        <td style="padding:8px 0;font-weight:bold;text-align:right;"><?php echo esc_html($data['ref']); ?></td>
      </tr>
    </table>

    <div class="info-box">
      <strong>📍 Come raggiungerci</strong><br>
      Via Scorciavacca Montarsi, 48 — Nunziata di Mascali (CT)<br>
      <a href="https://maps.google.com/?q=Nunziata+di+Mascali+CT" style="color:#1A4E6B;">Apri in Google Maps</a><br><br>
      <strong>🚗 Parcheggio</strong> disponibile in struttura, gratuito.<br><br>
      <strong>📞 Contatti</strong><br>
      Per qualsiasi esigenza: <a href="mailto:<?php echo esc_attr($data['host_email']); ?>" style="color:#1A4E6B;"><?php echo esc_html($data['host_email']); ?></a>
    </div>

    <p>Non vediamo l'ora di farti scoprire il nostro angolo di Sicilia!</p>
    <p>A presto,<br><strong><?php echo esc_html($data['host_name']); ?></strong></p>
  </div>

  <div class="footer">
    <p>© <?php echo esc_html((string) gmdate('Y')); ?> Almaretna · Via Scorciavacca Montarsi, 48 · Nunziata di Mascali (CT)</p>
  </div>

</div>
</body>
</html>
