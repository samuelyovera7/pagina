<?php
// admin/view_otps.php — Página protegida para ver/limpiar el log de OTP (solo superadmin)
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || ($_SESSION['rol'] ?? 'visualizador') !== 'superadmin') {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/db_connect.php';

// Esta funcionalidad de ver el log local de OTP ya no está disponible por razones de seguridad.
?><!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>OTP Log - Deshabilitado</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="p-4"><div class="container">
<h3>OTP Log deshabilitado</h3>
<div class="alert alert-info">El registro local de OTP ha sido deshabilitado. Los códigos no se almacenan en texto plano en el servidor. Para recibir códigos por correo, configura los valores SMTP y la dirección `RECOVERY_EMAIL` en `admin/db_connect.php`.</div>
<a href="dashboard.php" class="btn btn-secondary">Volver al dashboard</a>
</div></body></html>
<?php
exit;

$logFile = __DIR__ . '/../otp_log.txt';
$lines = [];
if (file_exists($logFile)) {
    $raw = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($raw !== false) {
        $lines = array_reverse($raw); // mostrar más reciente primero
    }
}

// Manejar limpieza del log
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear') {
    @file_put_contents($logFile, '');
    header('Location: view_otps.php?cleared=1'); exit;
}

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OTP Log - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>pre { white-space: pre-wrap; word-break: break-word; }</style>
</head>
<body class="p-4">
    <div class="container">
        <h3>OTP Log (local)</h3>
        <?php if (isset($_GET['cleared'])): ?>
            <div class="alert alert-success">Registro limpiado.</div>
        <?php endif; ?>

        <p class="text-muted">Este archivo contiene los códigos generados en texto plano para entornos sin SMTP. Bórralo cuando termines las pruebas.</p>

        <form method="post" onsubmit="return confirm('Limpiar el registro (se eliminará todo)?');">
            <input type="hidden" name="action" value="clear">
            <button class="btn btn-danger mb-3">Limpiar registro</button>
            <a href="dashboard.php" class="btn btn-secondary mb-3 ms-2">Volver al dashboard</a>
        </form>

        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead><tr><th>#</th><th>Registro</th></tr></thead>
                <tbody>
                <?php if (empty($lines)): ?>
                    <tr><td colspan="2" class="text-center">No hay registros.</td></tr>
                <?php else: foreach ($lines as $i => $ln): ?>
                    <tr>
                        <td style="width:80px"><?php echo $i+1; ?></td>
                        <td><pre><?php echo htmlspecialchars($ln); ?></pre></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
