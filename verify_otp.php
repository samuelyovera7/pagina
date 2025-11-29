<?php
// verify_otp.php — verify code and allow one-time login (no password change)
session_start();
require_once __DIR__ . '/admin/db_connect.php';

$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $code = trim($_POST['code'] ?? '');

    if ($username === '' || $code === '') {
        $error = 'Usuario y código son obligatorios.';
    } else {
        // Buscar el último código no usado y no expirado para este usuario
        $stmt = $mysqli->prepare("SELECT id, code_hash, expires_at, used FROM admin_recovery_codes WHERE username = ? ORDER BY id DESC LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stmt->close();

            if ($row) {
                if (intval($row['used']) === 1) {
                    $error = 'Este código ya fue usado.';
                } elseif (strtotime($row['expires_at']) < time()) {
                    $error = 'El código expiró. Solicita uno nuevo.';
                } else {
                    // Verificar código
                    if (password_verify($code, $row['code_hash'])) {
                        // Marcar como usado
                        $u = $mysqli->prepare("UPDATE admin_recovery_codes SET used = 1 WHERE id = ?");
                        if ($u) { $u->bind_param('i', $row['id']); $u->execute(); $u->close(); }

                        // Logear al usuario (sin cambiar contraseña)
                        $q = $mysqli->prepare("SELECT id, usuario, nombre, rol FROM usuarios_admin WHERE usuario = ? LIMIT 1");
                        if ($q) {
                            $q->bind_param('s', $username);
                            $q->execute();
                            $res2 = $q->get_result();
                            $user = $res2->fetch_assoc();
                            $q->close();

                            if ($user) {
                                session_regenerate_id(true);
                                $_SESSION['loggedin'] = true;
                                $_SESSION['id'] = $user['id'];
                                $_SESSION['usuario'] = $user['usuario'];
                                $_SESSION['nombre'] = $user['nombre'];
                                $_SESSION['rol'] = $user['rol'] ?? 'visualizador';

                                    // Registrar auditoría: login via código OTP
                                    if (function_exists('admin_audit_log')) {
                                        admin_audit_log('login_success_otp', 'Ingreso con código OTP', $user['id'], $user['usuario']);
                                    }

                                    header('Location: admin/dashboard.php');
                                    exit;
                            } else {
                                $error = 'Usuario no encontrado.';
                            }
                        }
                    } else {
                        $error = 'Código incorrecto.';
                        if (function_exists('admin_audit_log')) admin_audit_log('login_otp_failed', "Código incorrecto para usuario: {$username}", null, $username);
                    }
                }
            } else {
                $error = 'No hay códigos válidos para este usuario. Solicita uno nuevo.';
                if (function_exists('admin_audit_log')) admin_audit_log('login_otp_failed', "No hay códigos válidos para usuario: {$username}", null, $username);
            }
        } else {
            $error = 'Error del sistema. Inténtalo más tarde.';
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar código</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Ingresar código de recuperación</h4>
                    <p class="text-muted">Introduce el usuario y el código que recibiste en el correo de recuperación.</p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form method="post" action="verify_otp.php">
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario</label>
                            <input type="text" id="username" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">Código</label>
                            <input type="text" id="code" name="code" class="form-control" required maxlength="6">
                        </div>
                        <button class="btn btn-primary">Ingresar con código</button>
                        <a href="login.php" class="btn btn-link">Volver al login</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center mt-3 text-muted small">Código válido 15 minutos.</div>
</div>
</body>
</html>
