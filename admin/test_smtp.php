<?php
// admin/test_smtp.php — Página para probar envío SMTP y mail() desde el panel admin
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || ($_SESSION['rol'] ?? 'visualizador') !== 'superadmin') {
    header('Location: ../login.php'); exit;
}

require_once __DIR__ . '/db_connect.php';

$message = '';
$error = '';
$to = RECOVERY_EMAIL;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = trim($_POST['to'] ?? RECOVERY_EMAIL);
    $subject = 'Prueba de envío - Maranatha';
    $body = "Este es un correo de prueba enviado desde admin/test_smtp.php en " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\n\nSi recibes este correo, la configuración SMTP/mail() funciona.\n";

    $mail_sent = false;
    $vendor = __DIR__ . '/vendor/autoload.php';
    if (file_exists($vendor)) {
        require_once $vendor;
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            if (!empty(SMTP_HOST) && !empty(SMTP_USER) && !empty(SMTP_PASS)) {
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USER;
                $mail->Password = SMTP_PASS;
                $mail->SMTPSecure = SMTP_SECURE ?: 'tls';
                $mail->Port = SMTP_PORT ?: 587;
            }
            $mail->setFrom('no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'), 'Maranatha Test');
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $body;
            $mail_sent = $mail->send();
            if ($mail_sent) $message = "Correo enviado correctamente a {$to} (PHPMailer).";
        } catch (Exception $e) {
            $error = 'PHPMailer error: ' . $e->getMessage();
        }
    } else {
        // Fallback to mail()
        $headers = 'From: no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n" .
                   'Content-Type: text/plain; charset=utf-8';
        $mail_sent = @mail($to, $subject, $body, $headers);
        if ($mail_sent) $message = "Correo enviado correctamente a {$to} (mail()).";
        else $error = 'La función mail() devolvió false. En XAMPP local es frecuente que mail() no esté configurada.';
    }

    // Log audit
    if (function_exists('admin_audit_log')) {
        $actor_id = $_SESSION['id'] ?? null;
        $actor_name = $_SESSION['usuario'] ?? null;
        if ($mail_sent) admin_audit_log('smtp_test_sent', "Correo de prueba enviado a {$to}", $actor_id, $actor_name);
        else admin_audit_log('smtp_test_failed', "Fallo envío prueba a {$to}: " . ($error ?: 'unknown'), $actor_id, $actor_name);
    }
}

?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prueba SMTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Prueba de envío de correo</h4>
                    <p class="text-muted">Envía un correo de prueba a la dirección configurada en `RECOVERY_EMAIL` o a la que indiques.</p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if ($message): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <form method="post" action="test_smtp.php">
                        <div class="mb-3">
                            <label for="to" class="form-label">Enviar a</label>
                            <input type="email" id="to" name="to" class="form-control" required value="<?php echo htmlspecialchars($to); ?>">
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary">Enviar correo de prueba</button>
                            <a href="dashboard.php" class="btn btn-link">Volver al dashboard</a>
                        </div>
                    </form>

                    <hr>
                    <h6>Notas</h6>
                    <ul>
                        <li>Si quieres usar PHPMailer y SMTP, instala las dependencias con Composer: <code>composer require phpmailer/phpmailer</code>.</li>
                        <li>Asegúrate de configurar `RECOVERY_EMAIL` y parámetros SMTP en <code>admin/db_connect.php</code>.</li>
                        <li>En entornos locales, `mail()` frecuentemente no está operativo; se recomienda SMTP.</li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>