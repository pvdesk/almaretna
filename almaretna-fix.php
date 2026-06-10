<?php
/**
 * ALMARETNA — File temporaneo diagnostica + reset OPcache
 * ELIMINARE DOPO L'USO.
 *
 * Come usare:
 *   1. Carica questo file nella root WordPress (dove c'è wp-config.php)
 *   2. Vai su https://www.almaretna.it/almaretna-fix.php mentre sei loggato come admin
 *   3. Ricarica il backend WP
 *   4. ELIMINA questo file dal server
 */
define('WPINC', 'wp-includes');
require_once dirname(__FILE__) . '/wp-load.php';

if (!current_user_can('manage_options')) {
    wp_die('Accesso riservato agli amministratori.', 403);
}

header('Content-Type: text/html; charset=utf-8');

$plugin_dir  = WP_CONTENT_DIR . '/plugins/almaretna-booking';
$settings    = $plugin_dir . '/admin/views/settings.php';
$class_admin = $plugin_dir . '/admin/class-admin.php';

$opcache_cleared = false;
$opcache_avail   = function_exists('opcache_reset');

if ($opcache_avail) {
    $opcache_cleared = opcache_reset();
}
?>
<!DOCTYPE html>
<html lang="it">
<head><meta charset="utf-8"><title>Almaretna Fix</title>
<style>body{font-family:sans-serif;max-width:700px;margin:40px auto;padding:0 20px;color:#333;}
h2{color:#1d2327;} table{border-collapse:collapse;width:100%;margin:16px 0;}
td,th{padding:8px 12px;border:1px solid #ddd;text-align:left;} th{background:#f6f7f7;}
.ok{color:#2e7d32;font-weight:600;} .warn{color:#c62828;font-weight:600;}
.box{padding:16px;border-radius:6px;margin:20px 0;}
.box-ok{background:#f0f9f0;border:1px solid #a8d5a2;}
.box-err{background:#fef2f2;border:1px solid #fca5a5;}
</style>
</head>
<body>
<h2>🔧 Almaretna — Diagnostica produzione</h2>

<table>
    <tr><th>File</th><th>Esiste?</th><th>Ultima modifica</th><th>MD5</th></tr>
    <tr>
        <td>settings.php</td>
        <td><?php echo file_exists($settings) ? '<span class="ok">✓ SÌ</span>' : '<span class="warn">✗ NO</span>'; ?></td>
        <td><?php echo file_exists($settings) ? date('d/m/Y H:i:s', filemtime($settings)) : '—'; ?></td>
        <td style="font-size:11px;"><?php echo file_exists($settings) ? md5_file($settings) : '—'; ?></td>
    </tr>
    <tr>
        <td>class-admin.php</td>
        <td><?php echo file_exists($class_admin) ? '<span class="ok">✓ SÌ</span>' : '<span class="warn">✗ NO</span>'; ?></td>
        <td><?php echo file_exists($class_admin) ? date('d/m/Y H:i:s', filemtime($class_admin)) : '—'; ?></td>
        <td style="font-size:11px;"><?php echo file_exists($class_admin) ? md5_file($class_admin) : '—'; ?></td>
    </tr>
</table>

<?php if ($opcache_avail) : ?>
    <div class="box <?php echo $opcache_cleared ? 'box-ok' : 'box-err'; ?>">
        <?php if ($opcache_cleared) : ?>
            <strong class="ok">✓ OPcache svuotata!</strong> Le modifiche PHP sono ora attive.
        <?php else : ?>
            <strong class="warn">✗ opcache_reset() ha restituito FALSE</strong> — potrebbe essere necessario riavviare PHP-FPM.
        <?php endif; ?>
    </div>
<?php else : ?>
    <div class="box box-ok">
        <strong class="ok">OPcache non attiva</strong> — questo server non usa OPcache, quindi le modifiche PHP sono sempre immediate.
    </div>
<?php endif; ?>

<p style="margin-top:30px;font-size:13px;color:#888;">
    ⚠️ <strong>Ricorda di eliminare questo file</strong> dopo aver verificato il corretto funzionamento del backend.
</p>
</body>
</html>
<?php
// Self-destruct opzionale: decommentare la riga sotto per eliminare il file dopo la prima visita
// @unlink(__FILE__);
