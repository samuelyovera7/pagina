<?php
// logout.php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) session_start();

// Cargar helper de BD/auditoría
require_once __DIR__ . '/admin/db_connect.php';

// Limpiar todas las variables de sesión
// Registrar auditoría de logout si está disponible
if (isset($_SESSION['usuario']) || isset($_SESSION['id'])) {
	if (function_exists('admin_audit_log')) {
		admin_audit_log('logout', 'Cierre de sesión', $_SESSION['id'] ?? null, $_SESSION['usuario'] ?? null);
	}
}

$_SESSION = array();

// Si existe la cookie de sesión, eliminarla
if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params["path"], $params["domain"],
		$params["secure"], $params["httponly"]
	);
}

// Destruir la sesión en el servidor
session_destroy();

// Enviar cabeceras para evitar cache del lado del cliente
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Redirigir a la página de login
header("Location: login.php");
exit;
?>
