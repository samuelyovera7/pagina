<?php
// admin/dashboard.php - Panel de Control del Administrador
session_start();

// 1. Verificación de Autenticación
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Incluir el archivo de conexión a la BD
require_once "db_connect.php"; 

// 2. Control de Acceso por Rol (Middleware)
$rol = $_SESSION['rol'] ?? 'visualizador';
$es_superadmin = $rol === 'superadmin';

// Si el usuario no es superadmin, lo redirigimos forzosamente a view_data.php
// (Asumiendo que es la única página permitida para 'visualizador')
if (!$es_superadmin) {
    // Si no es superadmin y el archivo actual no es view_data.php, redirige.
    if (basename($_SERVER['PHP_SELF']) !== 'view_data.php') {
        header("location: view_data.php");
        exit;
    }
}

// NOTA: Si este script es view_data.php, el 'visualizador' puede continuar.
// Si este script es dashboard.php, solo el 'superadmin' puede continuar.

// Simulando la obtención de datos para el dashboard (deberías reemplazar esto con consultas reales a la BD)
function column_exists($mysqli, $table, $column) {
    $t = $mysqli->real_escape_string($table);
    $c = $mysqli->real_escape_string($column);
    $chk = $mysqli->query("SHOW COLUMNS FROM `" . $t . "` LIKE '" . $c . "'");
    return ($chk && $chk->num_rows > 0);
}

function obtener_datos_resumen($mysqli) {
    $data = [
        'nuevos_contactos' => 0,
        'miembros_activos' => 0,
        'donaciones' => 0,
    ];

    // Comprobar tablas antes de consultar
    $tables = ['contactos_nuevos','miembros_activos','donaciones'];
    $exists = [];
    foreach ($tables as $t) {
        $chk = $mysqli->query("SHOW TABLES LIKE '" . $mysqli->real_escape_string($t) . "'");
        $exists[$t] = ($chk && $chk->num_rows > 0);
    }

    // Contactos nuevos (último mes) - solo si la tabla y la columna fecha_registro existen
    if ($exists['contactos_nuevos']) {
        if (column_exists($mysqli, 'contactos_nuevos', 'fecha_registro')) {
            $res = $mysqli->query("SELECT COUNT(*) FROM contactos_nuevos WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
            if ($res) $data['nuevos_contactos'] = (int)$res->fetch_row()[0];
        } else {
            // Si no existe la columna, hacemos un COUNT total para evitar fallo
            $res = $mysqli->query("SELECT COUNT(*) FROM contactos_nuevos");
            if ($res) $data['nuevos_contactos'] = (int)$res->fetch_row()[0];
        }
    }

    // Miembros activos (total)
    if ($exists['miembros_activos']) {
        $res = $mysqli->query("SELECT COUNT(*) FROM miembros_activos");
        if ($res) $data['miembros_activos'] = (int)$res->fetch_row()[0];
    }

    // Donaciones (total) — solo si la tabla existe
    if ($exists['donaciones']) {
        $res = $mysqli->query("SELECT COUNT(*) FROM donaciones");
        if ($res) $data['donaciones'] = (int)$res->fetch_row()[0];
    }

    return $data;
}

$dashboard_data = obtener_datos_resumen($mysqli);

// Obtener registros recientes de auditoría (solo para superadmin)
$recent_logs = [];
if ($es_superadmin) {
    $res = $mysqli->query("SELECT id, user_id, username, event_type, event_detail, created_at FROM admin_audit_logs ORDER BY created_at DESC LIMIT 8");
    if ($res) {
        while ($r = $res->fetch_assoc()) $recent_logs[] = $r;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Maranatha Aragua</title>
    <link rel="shortcut icon" href="../img/logo pequeño.jpeg" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin_styles.css"> 
</head>
<body class="dashboard-body">

    <div id="sidebar">
        <div class="text-center p-3 mb-4">
            <!-- Nota: la ruta de la imagen debe ser relativa a dashboard.php -->
            <img src="../img/Logo Maranatha Aragua.png" alt="Logo Admin" style="width: 100px;" class="mb-2">
            <h5 class="text-white">Admin: <?php echo htmlspecialchars($_SESSION["nombre"]); ?></h5>
            <small class="text-info"><?php echo $es_superadmin ? 'Super Administrador' : 'Visualizador'; ?></small>
        </div>

        <ul class="nav flex-column">
            <!-- Solo SuperAdmin ve la mayoría de las opciones -->
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php">
                    <i class="fa-solid fa-gauge me-2"></i> Dashboard
                </a>
            </li>
            
            <?php if ($es_superadmin): ?>
            <li class="nav-item">
                <a class="nav-link" href="view_data.php"> 
                    <i class="fa-solid fa-database me-2"></i> Base de Datos (Formularios)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="audit_logs.php">
                    <i class="fa-solid fa-shield-alt me-2"></i> Auditoría
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="user_management.php">
                    <i class="fa-solid fa-users-gear me-2"></i> Gestión de Usuarios
                </a>
            </li>
            <?php else: ?>
            <!-- El visualizador solo ve la opción de ver datos -->
            <li class="nav-item">
                <a class="nav-link" href="view_data.php"> 
                    <i class="fa-solid fa-database me-2"></i> Ver Datos
                </a>
            </li>
            <?php endif; ?>

            <li class="nav-item mt-auto border-top pt-2">
                <a class="nav-link" href="../logout.php">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </div>
    
    <div class="container-fluid flex-grow-1 p-0">
        <main id="content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1>Panel de Control</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-calendar-days me-1"></i> Hoy
                    </button>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
                <div class="col">
                    <a href="view_data.php?tab=contactos" class="text-decoration-none">
                    <div class="card text-white bg-primary shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fa-solid fa-user-plus me-2"></i> Contactos Nuevos</h5>
                            <p class="card-text fs-3"><?php echo $dashboard_data['nuevos_contactos']; ?></p>
                            <small>En el último mes</small>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col">
                    <a href="view_data.php?tab=miembros" class="text-decoration-none">
                    <div class="card text-white bg-success shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fa-solid fa-users me-2"></i> Miembros Actualizados</h5>
                            <p class="card-text fs-3"><?php echo $dashboard_data['miembros_activos']; ?></p>
                            <small>Última semana</small>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col">
                    <a href="view_data.php?tab=donaciones" class="text-decoration-none">
                    <div class="card text-white bg-warning shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fa-solid fa-hand-holding-dollar me-2"></i> Donaciones</h5>
                            <p class="card-text fs-3"><?php echo $dashboard_data['donaciones']; ?></p>
                            <small>Total</small>
                        </div>
                    </div>
                    </a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h4>Registro de Actividad Reciente</h4>
                </div>
                <div class="card-body">
                    <?php if ($es_superadmin): ?>
                        <?php if (count($recent_logs) === 0): ?>
                            <p class="mb-0">No hay actividad registrada aún.</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($recent_logs as $log): ?>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong><?php echo htmlspecialchars($log['event_type']); ?></strong>
                                                <div class="small text-muted"><?php echo htmlspecialchars($log['username'] ?? '—'); ?></div>
                                                <div><?php echo nl2br(htmlspecialchars($log['event_detail'] ?? '')); ?></div>
                                            </div>
                                            <div class="text-end small text-muted"><?php echo htmlspecialchars($log['created_at']); ?></div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Acceso restringido. Solo Super Administradores pueden ver el registro de auditoría.</p>
                    <?php endif; ?>
                </div>
            </div>

        </main>
    </div>

    <!-- Script de Boostrap debe ir al final del body -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="admin-functions.js?t=<?php echo time(); ?>"></script>
</body>
</html>