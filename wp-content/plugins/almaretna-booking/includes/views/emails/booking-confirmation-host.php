<?php
/**
 * Email template — Notifica nuova prenotazione (host/admin)
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8" />
<title>Nuova prenotazione</title>
<style>
  body{margin:0;padding:0;background:#f0f0f0;font-family:Arial,Helvetica,sans-serif;color:#2C2C2C;font-size:14px;}
  .wrap{max-width:580px;margin:24px auto;background:#fff;border-radius:6px;overflow:hidden;}
  .header{background:#1A4E6B;padding:20px 32px;}
  .header h1{margin:0;color:#fff;font-size:20px;}
  .header span{color:#D4A017;font-weight:bold;}
  .body{padding:32px;}
  table{width:100%;border-collapse:collapse;margin:16px 0;}
  td{padding:8px 12px;border-bottom:1px solid #eee;font-size:14px;}
  td:first-child{color:#555;width:40%;}
  td:last-child{font-weight:bold;}
  .total td{background:#f9f7f3;font-size:15px;}
  .footer{background:#1A4E6B;padding:16px 32px;font-size:12px;color:rgba(255,255,255,.7);text-align:center;}
  .badge{display:inline-block;background:#D4A017;color:#fff;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:bold;}
</style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <h1>Nuova prenotazione <span><?php echo esc_html($data['ref']); ?></span></h1>
  </div>

  <div class="body">
    <p>È arrivata una nuova prenotazione tramite il sito Almaretna.</p>

    <h3 style="color:#1A4E6B;margin:0 0 8px;">Dati ospite</h3>
    <table>
      <tr><td>Nome</td><td><?php echo esc_html($data['first_name'] . ' ' . $data['last_name']); ?></td></tr>
      <tr><td>Email</td><td><a href="mailto:<?php echo esc_attr($data['email']); ?>"><?php echo esc_html($data['email']); ?></a></td></tr>
      <tr><td>Telefono</td><td><?php echo esc_html($data['phone']); ?></td></tr>
    </table>

    <h3 style="color:#1A4E6B;margin:0 0 8px;">Dettagli soggiorno</h3>
    <table>
      <tr><td>Camera</td><td><?php echo esc_html($data['room_name']); ?></td></tr>
      <tr><td>Check-in</td><td><?php echo esc_html($data['checkin_display']); ?></td></tr>
      <tr><td>Check-out</td><td><?php echo esc_html($data['checkout_display']); ?></td></tr>
      <tr><td>Notti</td><td><?php echo esc_html((string) $data['nights']); ?></td></tr>
      <tr>
        <td>Ospiti</td>
        <td>
          <?php echo esc_html((string) $data['adults']); ?> adulti
          <?php if ($data['children'] > 0) echo '+ ' . esc_html((string) $data['children']) . ' bambini'; ?>
        </td>
      </tr>
      <tr><td>Canale</td><td><span class="badge"><?php echo esc_html(strtoupper($data['channel'])); ?></span></td></tr>
      <?php if (!empty($data['special_requests'])) : ?>
      <tr><td>Richieste speciali</td><td><?php echo nl2br(esc_html($data['special_requests'])); ?></td></tr>
      <?php endif; ?>
      <tr class="total"><td>Totale</td><td><?php echo esc_html($data['total_display']); ?></td></tr>
    </table>

    <p style="font-size:13px;color:#666;margin-top:24px;">
      Questa email è stata generata automaticamente dal sistema di prenotazione Almaretna.
    </p>
  </div>

  <div class="footer">Almaretna Booking System · <?php echo esc_html(home_url('/')); ?></div>

</div>
</body>
</html>
