<?php
// admin/db_connect.php
// Iniciar sesión y enviar cabeceras anti-cache para páginas admin
if (session_status() === PHP_SESSION_NONE) session_start();

// Evitar que el navegador muestre páginas protegidas desde cache (botón atrás)
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: private, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'maranatha_aragua');

// Dirección de correo única para recuperación de acceso (no editable desde UI)
// Cámbiala por la dirección real que deseas usar para recibir códigos de recuperación.
define('RECOVERY_EMAIL', 'maranatharagua@gmail.com');

/* SMTP opcional (para envío fiable de correos). Si usas Gmail, crea una contraseña de aplicación
    y completa estos valores. Si los dejas vacíos, el sistema intentará usar `mail()` de PHP.
    Ejemplo (Gmail):
    define('SMTP_HOST', 'smtp.gmail.com');
    define('SMTP_PORT', 587);
    define('SMTP_USER', 'tu-cuenta@gmail.com');
    define('SMTP_PASS', 'tu-app-password');
    define('SMTP_SECURE', 'tls');
*/
if (!defined('SMTP_HOST')) define('SMTP_HOST', '');
if (!defined('SMTP_PORT')) define('SMTP_PORT', 587);
if (!defined('SMTP_USER')) define('SMTP_USER', 'maranatharagua@gmail.com');
if (!defined('SMTP_PASS')) define('SMTP_PASS', '');
if (!defined('SMTP_SECURE')) define('SMTP_SECURE', 'tls');

$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");

// Crear tabla de auditoría si no existe
$create_audit = "CREATE TABLE IF NOT EXISTS admin_audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    username VARCHAR(100) DEFAULT NULL,
    event_type VARCHAR(100) NOT NULL,
    event_detail TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$mysqli->query($create_audit);

// Create admin_security table for recovery via security questions + PIN
$create_security = "CREATE TABLE IF NOT EXISTS admin_security (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    question1 VARCHAR(255) DEFAULT NULL,
    answer1_hash VARCHAR(255) DEFAULT NULL,
    question2 VARCHAR(255) DEFAULT NULL,
    answer2_hash VARCHAR(255) DEFAULT NULL,
    pin_hash VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$mysqli->query($create_security);

// Defensive: if the database already has an incorrect FK on admin_audit_logs
// that references usuarios_admin (some installs accidentally added one), drop it.
// This allows writing audit rows even when actors were removed.
$fk_check_sql = "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = '" . DB_NAME . "' AND TABLE_NAME = 'admin_audit_logs' 
    AND REFERENCED_TABLE_NAME = 'usuarios_admin'";
$fk_res = $mysqli->query($fk_check_sql);
if ($fk_res && $fk_res->num_rows > 0) {
    while ($fk_row = $fk_res->fetch_assoc()) {
        $fk_name = $fk_row['CONSTRAINT_NAME'];
        if ($fk_name) {
            @$mysqli->query("ALTER TABLE admin_audit_logs DROP FOREIGN KEY `" . $mysqli->real_escape_string($fk_name) . "`");
        }
    }
}

// Defensive: also drop any incorrect foreign keys on admin_recovery_codes
// that reference usuarios_admin (some installs accidentally added one)
$fk_check_sql2 = "SELECT CONSTRAINT_NAME, TABLE_NAME FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = '" . DB_NAME . "' AND TABLE_NAME = 'admin_recovery_codes' 
    AND REFERENCED_TABLE_NAME = 'usuarios_admin'";
$fk_res2 = $mysqli->query($fk_check_sql2);
if ($fk_res2 && $fk_res2->num_rows > 0) {
    while ($fk_row2 = $fk_res2->fetch_assoc()) {
        $fk_name2 = $fk_row2['CONSTRAINT_NAME'];
        $tbl = $fk_row2['TABLE_NAME'];
        if ($fk_name2 && $tbl) {
            @$mysqli->query("ALTER TABLE `" . $mysqli->real_escape_string($tbl) . "` DROP FOREIGN KEY `" . $mysqli->real_escape_string($fk_name2) . "`");
        }
    }
}

/**
 * Registra un evento de auditoría en la tabla admin_audit_logs.
 * @param string $event_type
 * @param string|null $detail
 * @param int|null $user_id
 * @param string|null $username
 */
function admin_audit_log($event_type, $detail = null, $user_id = null, $username = null) {
    global $mysqli;
    if (!isset($mysqli) || !($mysqli instanceof mysqli)) return false;

    // If a user_id was provided, verify it exists in usuarios_admin.
    $user_exists = false;
    if ($user_id !== null) {
        $chk = $mysqli->prepare("SELECT 1 FROM usuarios_admin WHERE id = ? LIMIT 1");
        if ($chk) {
            $chk->bind_param('i', $user_id);
            $chk->execute();
            $res = $chk->get_result();
            if ($res && $res->num_rows > 0) $user_exists = true;
            $chk->close();
        }
    }

    // If user exists, insert with the user_id; otherwise insert with NULL to avoid FK failures.
    if ($user_exists) {
        $ins = $mysqli->prepare("INSERT INTO admin_audit_logs (user_id, username, event_type, event_detail) VALUES (?, ?, ?, ?)");
        if ($ins) {
            $ins->bind_param('isss', $user_id, $username, $event_type, $detail);
            $res = $ins->execute();
            $ins->close();
            return $res;
        }
    } else {
        $ins = $mysqli->prepare("INSERT INTO admin_audit_logs (user_id, username, event_type, event_detail) VALUES (NULL, ?, ?, ?)");
        if ($ins) {
            $ins->bind_param('sss', $username, $event_type, $detail);
            $res = $ins->execute();
            $ins->close();
            return $res;
        }
    }

    return false;
}
?>