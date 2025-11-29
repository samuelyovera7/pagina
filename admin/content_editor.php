<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}
require_once "db_connect.php";

// NUEVO: Control de acceso por rol
$es_superadmin = ($_SESSION['rol'] ?? 'visualizador') === 'superadmin';

if (!$es_superadmin && basename($_SERVER['PHP_SELF']) !== 'view_data.php') {
    // Si no es superadmin y está intentando entrar a cualquier página que NO sea view_data.php
    header("location: view_data.php");
    exit;
}
// Obtener una lista de páginas únicas asociadas al contenido
$paginas_stmt = $mysqli->prepare("SELECT DISTINCT pagina_asociada FROM contenido_web ORDER BY pagina_asociada");
$paginas_stmt->execute();
$paginas_result = $paginas_stmt->get_result();

// Mapeo de nombres de archivo a nombres amigables
$nombres_amigables = [
    'index.php' => 'Inicio',
    'quienes_somos.php' => 'Quiénes Somos',
    'evangelismo.php' => 'Evangelismo',
    'proximos_eventos.php' => 'Eventos',
    'horario_rrss.php' => 'Contáctanos / Horarios',
    'radio.php' => 'Radio',
    'donaciones.php' => 'Donaciones' // Agrego donaciones.php por si lo tienes en tu nav real
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editor de Contenido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin_styles.css"> 
</head>
<body class="dashboard-body">
    <?php include 'sidebar_admin.php'; // Creamos un archivo sidebar_admin.php en el siguiente paso ?> 

    <div class="container-fluid flex-grow-1 p-0">
        <main id="content">
            <h2><i class="fa-solid fa-pen-to-square me-2"></i> Editor de Contenido Web</h2>
            <p class="text-muted">Selecciona la página que deseas modificar para ver todas sus secciones editables.</p>

            <div class="list-group">
                <?php while ($row = $paginas_result->fetch_assoc()): ?>
                    <?php 
                    $archivo = $row['pagina_asociada'];
                    $nombre_amigable = $nombres_amigables[$archivo] ?? $archivo;
                    ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                            <strong><?php echo htmlspecialchars($nombre_amigable); ?></strong>
                            <span class="badge bg-secondary rounded-pill">Edición deshabilitada</span>
                        </div>
                <?php endwhile; ?>
            </div>
                </main>
            </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="admin-functions.js?t=<?php echo time(); ?>"></script>
</body>
</html>