<?php
// login.php
session_start();

// Evitar cache del lado del cliente en la página de login
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: admin/dashboard.php");
    exit;
}

require_once "admin/db_connect.php";

// Verificar que la conexión exista
if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    $login_err = "Error de conexión a la base de datos.";
}

// Asegurar tabla para intentos de login (lockout)
$create_attempts_table = "CREATE TABLE IF NOT EXISTS admin_login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    attempts INT NOT NULL DEFAULT 0,
    last_attempt DATETIME DEFAULT NULL,
    locked_until DATETIME DEFAULT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    UNIQUE KEY (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$mysqli->query($create_attempts_table);

$usuario = $contrasena = "";
$usuario_err = $contrasena_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["usuario"]))) {
        $usuario_err = "Por favor ingrese el usuario.";
    } else {
        $usuario = trim($_POST["usuario"]);
    }

    // Comprueba si el usuario está bloqueado temporalmente
    if (empty($usuario_err)) {
        $stmtLock = $mysqli->prepare("SELECT attempts, locked_until FROM admin_login_attempts WHERE username = ? LIMIT 1");
        if ($stmtLock) {
            $stmtLock->bind_param('s', $usuario);
            $stmtLock->execute();
            $resLock = $stmtLock->get_result();
            if ($rowLock = $resLock->fetch_assoc()) {
                if (!empty($rowLock['locked_until']) && strtotime($rowLock['locked_until']) > time()) {
                    $unlockAt = date('Y-m-d H:i:s', strtotime($rowLock['locked_until']));
                    $login_err = "Cuenta bloqueada hasta {$unlockAt} por demasiados intentos fallidos.";
                        // Registrar en auditoría intento sobre cuenta bloqueada
                        if (function_exists('admin_audit_log')) admin_audit_log('login_blocked', "Intento de acceso a cuenta bloqueada", null, $usuario);
                }
            }
            $stmtLock->close();
        }
    }

    if (empty(trim($_POST["contrasena"]))) {
        $contrasena_err = "Por favor ingrese la contraseña.";
    } else {
        $contrasena = trim($_POST["contrasena"]);
    }

    if (empty($usuario_err) && empty($contrasena_err)) {

        $sql = "SELECT id, usuario, contrasena, nombre, rol FROM usuarios_admin WHERE usuario = ?";

        if ($login_err) {
            // Si ya está bloqueado, no procedemos a verificar la contraseña
        } elseif ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $usuario);

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $usuario_bd, $hashed_password, $nombre, $rol);
                    if ($stmt->fetch()) {
                        if (!empty($hashed_password) && password_verify($contrasena, $hashed_password)) {

                            // On successful login: reset attempts for this username
                            $del = $mysqli->prepare("DELETE FROM admin_login_attempts WHERE username = ?");
                            if ($del) { $del->bind_param('s', $usuario); $del->execute(); $del->close(); }

                            session_regenerate_id(true);
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["usuario"] = $usuario_bd;
                            $_SESSION["nombre"] = $nombre;
                            $_SESSION["rol"] = $rol ?? 'visualizador';  // Aquí guardamos el rol correctamente

                            // Registrar auditoría: inicio de sesión
                            if (function_exists('admin_audit_log')) admin_audit_log('login_success', 'Inicio de sesión exitoso', $id, $usuario_bd);

                            header("location: admin/dashboard.php");
                            exit;

                        } else {
                            // Contraseña incorrecta → incrementar contador
                            $login_err = "Credenciales incorrectas.";

                            // Registrar auditoría: password incorrecto
                            if (function_exists('admin_audit_log')) admin_audit_log('login_failed', 'Contraseña incorrecta', null, $usuario);

                            // Actualizar/insertar intento
                            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
                            $now = date('Y-m-d H:i:s');

                            $up = $mysqli->prepare("SELECT id, attempts FROM admin_login_attempts WHERE username = ? LIMIT 1");
                            if ($up) {
                                $up->bind_param('s', $usuario);
                                $up->execute();
                                $r = $up->get_result();
                                if ($row = $r->fetch_assoc()) {
                                    $newAttempts = intval($row['attempts']) + 1;
                                    if ($newAttempts >= 3) {
                                        // Bloquear por 2 horas
                                        $lockedUntil = date('Y-m-d H:i:s', time() + 2 * 3600);
                                        $u2 = $mysqli->prepare("UPDATE admin_login_attempts SET attempts = 0, last_attempt = ?, locked_until = ?, ip = ? WHERE id = ?");
                                        if ($u2) { $u2->bind_param('sssi', $now, $lockedUntil, $ip, $row['id']); $u2->execute(); $u2->close(); }
                                    } else {
                                        $u2 = $mysqli->prepare("UPDATE admin_login_attempts SET attempts = ?, last_attempt = ?, ip = ? WHERE id = ?");
                                        if ($u2) { $u2->bind_param('issi', $newAttempts, $now, $ip, $row['id']); $u2->execute(); $u2->close(); }
                                    }
                                } else {
                                    $ins = $mysqli->prepare("INSERT INTO admin_login_attempts (username, attempts, last_attempt, ip) VALUES (?, 1, ?, ?)");
                                    if ($ins) { $ins->bind_param('sss', $usuario, $now, $ip); $ins->execute(); $ins->close(); }
                                }
                                $up->close();
                            }
                        }
                    }
                } else {
                    $login_err = "Usuario no encontrado.";
                }
            }
            $stmt->close();
        } else {
            $login_err = "Error del sistema. Inténtalo más tarde.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Maranatha Aragua</title>
    <link rel="shortcut icon" href="img/logo pequeño.jpeg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin/admin_styles.css">
</head>
<body>
    <div class="login-container">
        <div class="card login-card">
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="img/Logo Maranatha Aragua.png" alt="Logo" class="logo-admin mb-3">
                    <h4 class="card-title">Panel Administrador</h4>
                </div>

                <?php if($login_err): ?>
                    <div class="alert alert-danger"><?php echo $login_err; ?></div>
                <?php endif; ?>

                <form action="login.php" method="post">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                            <input type="text" name="usuario" class="form-control <?php echo $usuario_err ? 'is-invalid' : ''; ?>" 
                                   placeholder="Usuario" value="<?php echo htmlspecialchars($usuario); ?>" required>
                            <div class="invalid-feedback"><?php echo $usuario_err; ?></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="contrasena" class="form-control <?php echo $contrasena_err ? 'is-invalid' : ''; ?>" 
                                   placeholder="Contraseña" required>
                            <div class="invalid-feedback"><?php echo $contrasena_err; ?></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg">Iniciar Sesión</button>
                    <div class="mt-2 text-center">
                        <a href="forgot_password.php">¿Olvidaste tu contraseña? Recuperar con código</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>