<?php
/**
 * Email template — Conferma prenotazione (guest)
 * Variabile disponibile: $data (array da ALM_Notifications::prepare_data)
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Prenotazione confermata</title>
<style>
  body{margin:0;padding:0;background:#f5f5f0;font-family:Georgia,'Times New Roman',serif;color:#2C2C2C;}
  .wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
  .header{background:#1A4E6B;padding:32px 40px;text-align:center;}
  .header h1{margin:0;color:#fff;font-size:24px;letter-spacing:.5px;}
  .header p{margin:6px 0 0;color:rgba(255,255,255,.8);font-size:14px;}
  .body{padding:40px;}
  .greeting{font-size:18px;margin-bottom:24px;}
  .box{background:#f9f7f3;border-left:4px solid #D4A017;border-radius:4px;padding:20px 24px;margin:24px 0;}
  .box h2{margin:0 0 16px;font-size:16px;color:#1A4E6B;text-transform:uppercase;letter-spacing:.5px;}
  .row{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #e8e3da;font-size:14px;}
  .row:last-child{border-bottom:none;}
  .row .label{color:#666;font-style:italic;}
  .row .value{font-weight:bold;text-align:right;}
  .total-row{padding-top:12px;margin-top:8px;font-size:16px;}
  .total-row .label{color:#2C2C2C;font-style:normal;font-weight:bold;}
  .total-row .value{color:#1A4E6B;font-size:18px;}
  .info-box{background:#e8f5e9;border-radius:6px;padding:20px 24px;margin:24px 0;font-size:14px;line-height:1.6;}
  .info-box strong{color:#1A4E6B;}
  .btn{display:inline-block;background:#D4A017;color:#fff;text-decoration:none;padding:12px 28px;border-radius:6px;font-weight:bold;font-size:15px;margin:8px 0;}
  .footer{background:#1A4E6B;padding:24px 40px;text-align:center;font-size:12px;color:rgba(255,255,255,.7);}
  .footer a{color:#D4A017;text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <h1>Almaretna</h1>
    <p>Villa con piscina — Nunziata di Mascali (CT)</p>
  </div>

  <div class="body">
    <p class="greeting">Ciao <?php echo esc_html($data['first_name']); ?>,</p>

    <p>La tua prenotazione è <strong>confermata</strong>. Non vediamo l'ora di accoglierti!</p>

    <div class="box">
      <h2>Riepilogo prenotazione</h2>
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
        <span class="value"><?php echo esc_html($data['checkin_display']); ?> dalle ore <?php echo esc_html($data['checkin_time']); ?></span>
      </div>
      <div class="row">
        <span class="label">Check-out</span>
        <span class="value"><?php echo esc_html($data['checkout_display']); ?> entro le ore <?php echo esc_html($data['checkout_time']); ?></span>
      </div>
      <div class="row">
        <span class="label">Durata</span>
        <span class="value"><?php echo esc_html((string) $data['nights']); ?> <?php echo $data['nights'] === 1 ? 'notte' : 'notti'; ?></span>
      </div>
      <div class="row">
        <span class="label">Ospiti</span>
        <span class="value">
          <?php echo esc_html((string) $data['adults']); ?> <?php echo $data['adults'] === 1 ? 'adulto' : 'adulti'; ?>
          <?php if ($data['children'] > 0) : ?>
            + <?php echo esc_html((string) $data['children']); ?> <?php echo $data['children'] === 1 ? 'bambino' : 'bambini'; ?>
          <?php endif; ?>
        </span>
      </div>
      <div class="row total-row">
        <span class="label">Totale pagato</span>
        <span class="value"><?php echo esc_html($data['total_display']); ?></span>
      </div>
    </div>

    <?php if (!empty($data['special_requests'])) : ?>
    <div class="box">
      <h2>Richieste speciali</h2>
      <p style="margin:0;font-size:14px;"><?php echo nl2br(esc_html($data['special_requests'])); ?></p>
    </div>
    <?php endif; ?>

    <div class="info-box">
      <strong>Come arrivarci</strong><br>
      Via Scorciavacca Montarsi, 48 — Nunziata di Mascali (CT)<br><br>
      <strong>Check-in:</strong> dalle ore <?php echo esc_html($data['checkin_time']); ?><br>
      <strong>Check-out:</strong> entro le ore <?php echo esc_html($data['checkout_time']); ?><br><br>
      Hai bisogno di aiuto? Scrivici a
      <a href="mailto:<?php echo esc_attr($data['host_email']); ?>"><?php echo esc_html($data['host_email']); ?></a>
    </div>

    <p style="text-align:center;">
      <a href="<?php echo esc_url($data['site_url']); ?>" class="btn">Visita il sito</a>
    </p>
  </div>

  <div class="footer">
    <p>© <?php echo esc_html((string) gmdate('Y')); ?> Almaretna · Via Scorciavacca Montarsi, 48 · Nunziata di Mascali (CT)</p>
    <p>Email inviata a <?php echo esc_html($data['email']); ?></p>
  </div>

</div>
</body>
</html>
