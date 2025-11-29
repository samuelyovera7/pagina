<?php
// forgot_password.php
session_start();
require_once __DIR__ . '/admin/db_connect.php';

$message = '';
$error = '';
$show_security_form = false;
$lastUsername = '';
$question1 = '';
$question2 = '';

// Ensure legacy codes table exists (no longer used by security-questions flow)
$create = "CREATE TABLE IF NOT EXISTS admin_recovery_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    code_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$mysqli->query($create);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'start_recovery') {
        $username = trim($_POST['username'] ?? '');
        if ($username === '') {
            $error = 'Por favor ingrese su usuario.';
        } else {
            // look up user and security questions
            $stmt = $mysqli->prepare("SELECT ua.id AS uid, s.question1, s.question2 FROM usuarios_admin ua LEFT JOIN admin_security s ON s.user_id = ua.id WHERE ua.usuario = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $res = $stmt->get_result();
                $row = $res->fetch_assoc();
                $stmt->close();

                if (!$row || empty($row['question1']) || empty($row['question2'])) {
                    // If there are no questions configured, and this is the known 'admin' user,
                    // seed the defaults automatically (so the recovery flow can proceed).
                    if (strtolower($username) === 'admin') {
                        $def_q1 = 'quien fue el fundador de maranatha aragua';
                        $def_a1 = 'arquimedes';
                        $def_q2 = 'cuantos años tiene la iglesia';
                        $def_a2 = '8';
                        $def_pin = '12345678';

                        $a1h = password_hash($def_a1, PASSWORD_DEFAULT);
                        $a2h = password_hash($def_a2, PASSWORD_DEFAULT);
                        $pinh = password_hash($def_pin, PASSWORD_DEFAULT);

                        // attempt insert or update
                        $chkins = $mysqli->prepare("SELECT id FROM admin_security WHERE user_id = ? LIMIT 1");
                        if ($chkins) {
                            // need uid: try to fetch from usuarios_admin
                            $q = $mysqli->prepare("SELECT id FROM usuarios_admin WHERE usuario = ? LIMIT 1");
                            if ($q) {
                                $q->bind_param('s', $username);
                                $q->execute();
                                $qr = $q->get_result();
                                $ur = $qr->fetch_assoc();
                                $q->close();
                            }
                            if (!empty($ur) && isset($ur['id'])) {
                                $uid = (int)$ur['id'];
                                $chkins->bind_param('i', $uid);
                                $chkins->execute();
                                $cres = $chkins->get_result();
                                $exists = $cres->fetch_assoc();
                                $chkins->close();

                                if ($exists) {
                                    $up = $mysqli->prepare("UPDATE admin_security SET question1 = ?, answer1_hash = ?, question2 = ?, answer2_hash = ?, pin_hash = ? WHERE user_id = ?");
                                    if ($up) { $up->bind_param('sssssi', $def_q1, $a1h, $def_q2, $a2h, $pinh, $uid); $up->execute(); $up->close(); }
                                } else {
                                    $ins = $mysqli->prepare("INSERT INTO admin_security (user_id, question1, answer1_hash, question2, answer2_hash, pin_hash) VALUES (?, ?, ?, ?, ?, ?)");
                                    if ($ins) { $ins->bind_param('isssss', $uid, $def_q1, $a1h, $def_q2, $a2h, $pinh); $ins->execute(); $ins->close(); }
                                }

                                // reload the questions
                                $stmt2 = $mysqli->prepare("SELECT ua.id AS uid, s.question1, s.question2 FROM usuarios_admin ua LEFT JOIN admin_security s ON s.user_id = ua.id WHERE ua.usuario = ? LIMIT 1");
                                if ($stmt2) { $stmt2->bind_param('s', $username); $stmt2->execute(); $res2 = $stmt2->get_result(); $row = $res2->fetch_assoc(); $stmt2->close(); }
                                if (function_exists('admin_audit_log')) admin_audit_log('admin_security_seeded_auto', "Auto-seeded recovery questions for user: {$username}", $uid, $username);
                            }
                        }

                        // If still no questions after attempt, show error
                        if (!$row || empty($row['question1']) || empty($row['question2'])) {
                            $error = 'No se encontraron preguntas de seguridad para este usuario. Contacta al administrador.';
                            if (function_exists('admin_audit_log')) admin_audit_log('security_recovery_requested', "Recuperación por preguntas fallida (no configurado) para usuario: {$username}", null, $username);
                        } else {
                            // proceed to show questions below
                            $lastUsername = $username;
                            $show_security_form = true;
                            $question1 = $row['question1'];
                            $question2 = $row['question2'];
                            if (function_exists('admin_audit_log')) admin_audit_log('security_recovery_requested', "Recuperación por preguntas iniciada para usuario: {$username}", $row['uid'], $username);
                        }
                    } else {
                        $error = 'No se encontraron preguntas de seguridad para este usuario. Contacta al administrador.';
                        if (function_exists('admin_audit_log')) admin_audit_log('security_recovery_requested', "Recuperación por preguntas fallida (no configurado) para usuario: {$username}", null, $username);
                    }
                } else {
                    // show security form
                    $lastUsername = $username;
                    $show_security_form = true;
                    $question1 = $row['question1'];
                    $question2 = $row['question2'];
                    if (function_exists('admin_audit_log')) admin_audit_log('security_recovery_requested', "Recuperación por preguntas iniciada para usuario: {$username}", $row['uid'], $username);
                }
            } else {
                $error = 'Error del sistema. Inténtalo más tarde.';
            }
        }

    } elseif ($action === 'reset_via_security') {
        $username = trim($_POST['username'] ?? '');
        $a1 = trim($_POST['answer1'] ?? '');
        $a2 = trim($_POST['answer2'] ?? '');
        $pin = trim($_POST['pin'] ?? '');
        $new1 = $_POST['new_password'] ?? '';
        $new2 = $_POST['confirm_password'] ?? '';

        if ($username === '' || $a1 === '' || $a2 === '' || $pin === '' || $new1 === '' || $new2 === '') {
            $error = 'Todos los campos son obligatorios.';
            $show_security_form = true;
            $lastUsername = $username;
        } elseif ($new1 !== $new2) {
            $error = 'Las contraseñas no coinciden.';
            $show_security_form = true;
            $lastUsername = $username;
        } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $new1)) {
            $error = 'La contraseña debe tener mínimo 8 caracteres e incluir letras, números y al menos un carácter especial.';
            $show_security_form = true;
            $lastUsername = $username;
        } elseif (!preg_match('/^\d{8}$/', $pin)) {
            $error = 'El PIN debe ser de 8 dígitos.';
            $show_security_form = true;
            $lastUsername = $username;
        } else {
            // fetch security hashes
            $stmt = $mysqli->prepare("SELECT ua.id AS uid, s.answer1_hash, s.answer2_hash, s.pin_hash FROM usuarios_admin ua LEFT JOIN admin_security s ON s.user_id = ua.id WHERE ua.usuario = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $res = $stmt->get_result();
                $row = $res->fetch_assoc();
                $stmt->close();

                if (!$row || empty($row['answer1_hash']) || empty($row['answer2_hash']) || empty($row['pin_hash'])) {
                    $error = 'No se encontró información de recuperación válida. Contacta al administrador.';
                    if (function_exists('admin_audit_log')) admin_audit_log('security_recovery_failed', "Sin datos de seguridad para usuario: {$username}", null, $username);
                    $show_security_form = true;
                } else {
                    $ok1 = password_verify($a1, $row['answer1_hash']);
                    $ok2 = password_verify($a2, $row['answer2_hash']);
                    $okp = password_verify($pin, $row['pin_hash']);
                    if ($ok1 && $ok2 && $okp) {
                        // update password
                        $hash = password_hash($new1, PASSWORD_DEFAULT);
                        $up = $mysqli->prepare("UPDATE usuarios_admin SET contrasena = ? WHERE usuario = ?");
                        if ($up) {
                            $up->bind_param('ss', $hash, $username);
                            if ($up->execute()) {
                                if (function_exists('admin_audit_log')) {
                                    admin_audit_log('security_recovery_success', "Recuperación por preguntas OK para usuario: {$username}", $row['uid'], $username);
                                    admin_audit_log('password_reset_completed', 'Contraseña restablecida vía preguntas+PIN', $row['uid'], $username);
                                }
                                header('Location: login.php?reset=1'); exit;
                            } else {
                                $error = 'Error al actualizar la contraseña.';
                                $show_security_form = true;
                            }
                            $up->close();
                        }
                    } else {
                        $error = 'Respuestas o PIN incorrectos.';
                        if (function_exists('admin_audit_log')) admin_audit_log('security_recovery_failed', "Respuestas/PIN incorrectos para usuario: {$username}", $row['uid'], $username);
                        $show_security_form = true;
                        $lastUsername = $username;
                    }
                }
            } else {
                $error = 'Error del sistema.';
                $show_security_form = true;
            }
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar contraseña</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Recuperar acceso</h4>
                    <p class="text-muted">Recupera tu cuenta respondiendo las dos preguntas de seguridad y proporcionando tu PIN de 8 dígitos.</p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if ($message): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <?php if ($show_security_form): ?>
                        <form method="post" action="forgot_password.php">
                            <input type="hidden" name="action" value="reset_via_security">
                            <div class="mb-3">
                                <label class="form-label">Usuario</label>
                                <input type="text" name="username" class="form-control" required value="<?php echo htmlspecialchars($lastUsername); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pregunta 1</label>
                                <div class="form-control-plaintext mb-2"><?php echo htmlspecialchars($question1); ?></div>
                                <input type="text" name="answer1" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pregunta 2</label>
                                <div class="form-control-plaintext mb-2"><?php echo htmlspecialchars($question2); ?></div>
                                <input type="text" name="answer2" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">PIN de 8 dígitos</label>
                                <input type="text" name="pin" class="form-control" required pattern="\d{8}" maxlength="8">
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Nueva contraseña</label>
                                <input type="password" name="new_password" class="form-control" required pattern="(?=.{8,})(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).*">
                                <div class="form-text">Mínimo 8 caracteres, letras, números y al menos un carácter especial.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirmar contraseña</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button class="btn btn-primary">Restablecer contraseña</button>
                            <a href="login.php" class="btn btn-link">Volver al login</a>
                        </form>

                    <?php else: ?>
                        <form method="post" action="forgot_password.php">
                            <input type="hidden" name="action" value="start_recovery">
                            <div class="mb-3">
                                <label class="form-label">Usuario</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <button class="btn btn-primary">Continuar</button>
                            <a href="login.php" class="btn btn-link">Volver al login</a>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center mt-3 text-muted small">Si no has configurado preguntas de seguridad, contacta al administrador.</div>
</div>
</body>
</html>
