<?php
// admin/audit_logs.php - Página de auditoría (solo SuperAdmin)
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php'); exit;
}
$rol = $_SESSION['rol'] ?? 'visualizador';
if ($rol !== 'superadmin') {
    header('Location: dashboard.php'); exit;
}

// Handle clear action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'clear_all') {
        $mysqli->query('TRUNCATE TABLE admin_audit_logs');
        $msg = 'Registros limpiados.';
    }
}

// Filters
$user_filter = trim($_GET['user'] ?? '');
$type_filter = trim($_GET['type'] ?? '');
$start = trim($_GET['start'] ?? '');
$end = trim($_GET['end'] ?? '');

$where = [];
$params = [];
$types = '';

if ($user_filter !== '') { $where[] = 'username = ?'; $params[] = $user_filter; $types .= 's'; }
if ($type_filter !== '') { $where[] = 'event_type = ?'; $params[] = $type_filter; $types .= 's'; }
if ($start !== '') { $where[] = 'created_at >= ?'; $params[] = $start . ' 00:00:00'; $types .= 's'; }
if ($end !== '') { $where[] = 'created_at <= ?'; $params[] = $end . ' 23:59:59'; $types .= 's'; }

$sql = 'SELECT id, user_id, username, event_type, event_detail, created_at FROM admin_audit_logs';
if (count($where)) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY created_at DESC LIMIT 500';

$stmt = $mysqli->prepare($sql);
if ($stmt) {
    if (count($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $logs = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $logs = [];
}

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auditoría - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Auditoría del sistema</h3>
        <a href="dashboard.php" class="btn btn-secondary">Volver</a>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <form class="row g-2 mb-3" method="get" action="audit_logs.php">
        <div class="col-auto">
            <input type="text" name="user" class="form-control" placeholder="Usuario" value="<?php echo htmlspecialchars($user_filter); ?>">
        </div>
        <div class="col-auto">
            <input type="text" name="type" class="form-control" placeholder="Tipo de evento" value="<?php echo htmlspecialchars($type_filter); ?>">
        </div>
        <div class="col-auto">
            <input type="date" name="start" class="form-control" value="<?php echo htmlspecialchars($start); ?>">
        </div>
        <div class="col-auto">
            <input type="date" name="end" class="form-control" value="<?php echo htmlspecialchars($end); ?>">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    <div class="mb-3">
        <form method="post" onsubmit="return confirm('¿Limpiar todos los registros de auditoría? Esta acción es irreversible.');">
            <input type="hidden" name="action" value="clear_all">
            <button class="btn btn-danger">Limpiar todo</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Evento</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($log['username'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($log['event_type']); ?></td>
                        <td style="max-width:400px; white-space:normal;"><?php echo nl2br(htmlspecialchars($log['event_detail'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
