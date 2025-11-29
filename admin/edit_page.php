<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}
require_once "db_connect.php";

// Edición de contenido web deshabilitada: redirigir a dashboard
header("Location: dashboard.php?error=edicion_deshabilitada");
exit;

// Control de acceso por rol: Solo SuperAdmin tiene permiso para editar
$es_superadmin = ($_SESSION['rol'] ?? 'visualizador') === 'superadmin';

if (!$es_superadmin) { 
    // Si no es superadmin, redirige a la única página permitida
    header("location: view_data.php");
    exit;
} // <--- ¡LA LLAVE DE CIERRE FALTANTE ESTABA AQUÍ!

$pagina_actual = $_GET['pagina'] ?? 'index.php';

// Manejar actualización
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_contenido']) && isset($_POST['contenido'])) {
    $id_contenido = $_POST['id_contenido'];
    $nuevo_contenido = $_POST['contenido'];

    // Para evitar XSS en la previsualización del contenido, aunque CKEditor ayuda,
    // es bueno limpiarlo si no se confía totalmente en el editor. Aquí asumimos
    // que el contenido HTML de CKEditor es seguro.
    
        // Usar sentencia preparada para actualizar el contenido
        $update_stmt = $mysqli->prepare("UPDATE contenido_web SET contenido = ? WHERE id = ?");
        if ($update_stmt) {
            $update_stmt->bind_param("si", $nuevo_contenido, $id_contenido);
            if ($update_stmt->execute()) {
                // Redirección POST-GET para evitar reenvío de formulario
                header("Location: edit_page.php?pagina=" . urlencode($pagina_actual) . "&saved=1");
                exit;
            } else {
                $mensaje = '<div class="alert alert-danger">Error al guardar: ' . htmlspecialchars($update_stmt->error) . '</div>';
            }
            $update_stmt->close();
        } else {
            $mensaje = '<div class="alert alert-danger">Error en la preparación de la consulta: ' . htmlspecialchars($mysqli->error) . '</div>';
        }
}

// Obtener contenido de la BD
// Obtener contenido de la BD
$select_stmt = $mysqli->prepare("SELECT id, titulo, contenido, seccion_clave FROM contenido_web WHERE pagina_asociada = ? ORDER BY id");
if ($select_stmt) {
    $select_stmt->bind_param("s", $pagina_actual);
    $select_stmt->execute();
    $contenido_result = $select_stmt->get_result();
} else {
    $contenido_result = null;
    $mensaje = '<div class="alert alert-danger">Error al obtener contenido: ' . htmlspecialchars($mysqli->error) . '</div>';
}

$nombres_amigables = [
    'index.php' => 'Inicio',
    'quienes_somos.php' => 'Quiénes Somos',
    'evangelismo.php' => 'Evangelismo',
    'proximos_eventos.php' => 'Eventos',
    'horario_rrss.php' => 'Contáctanos / Horarios',
    'radio.php' => 'Radio',
    'donaciones.php' => 'Donaciones'
];
$titulo_pagina = $nombres_amigables[$pagina_actual] ?? $pagina_actual;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar: <?php echo htmlspecialchars($titulo_pagina); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin_styles.css">
    <!-- CKEditor 5 Classic (versión actual 2025) -->
    <script src="https://cdn.ckeditor.com/ckeditor5/43.3.0/ckeditor5-build-classic/ckeditor5-build-classic.js"></script>
    <!-- Styles moved to admin/admin_styles.css -->
</head>
<body class="dashboard-body">
    <?php include 'sidebar_admin.php'; ?>

    <div id="content">
        <div class="container-fluid">
            <h2 class="mt-4"><i class="fa-solid fa-pen-to-square me-2"></i> Editando: <?php echo htmlspecialchars($titulo_pagina); ?></h2>
            <a href="content_editor.php" class="btn btn-secondary mb-3"><i class="fa-solid fa-arrow-left"></i> Volver al listado</a>

            <?php if (isset($_GET['saved'])): ?>
                <div class="alert alert-success">¡Contenido actualizado correctamente!</div>
            <?php endif; ?>
            <?php echo $mensaje ?? ''; // Mostrar mensaje de error si existe ?>

            <?php if ($contenido_result && $contenido_result->num_rows > 0):
                $contents = [];
                while ($row = $contenido_result->fetch_assoc()):
                    // Guardar contenido para inicializar CKEditor via JS (evita problemas con HTML dentro de textarea)
                    $contents[$row['id']] = $row['contenido'];
            ?>
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><?php echo htmlspecialchars($row['titulo']); ?> 
                                <small class="text-muted">(Clave: <?php echo $row['seccion_clave']; ?>)</small>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <!-- Se incluye la página actual para la redirección POST-GET -->
                                <input type="hidden" name="pagina" value="<?php echo htmlspecialchars($pagina_actual); ?>">
                                <input type="hidden" name="id_contenido" value="<?php echo $row['id']; ?>">
                                <textarea name="contenido" class="editor" id="editor-<?php echo $row['id']; ?>"></textarea>
                                <button type="submit" class="btn btn-success mt-3">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                                </button>
                            </form>
                        </div>
                    </div>
                <?php
                endwhile;
                // liberar resultado
                $select_stmt->close();
            else: ?>
                <div class="alert alert-warning">No hay secciones de contenido asociadas a la clave de página: <strong><?php echo htmlspecialchars($pagina_actual); ?></strong></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Script de CKEditor debe ir después de que el DOM esté cargado -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Contenidos precargados (ID => contenido HTML)
        const editorContents = <?php echo json_encode($contents ?? [], JSON_UNESCAPED_UNICODE); ?>;

        document.querySelectorAll('.editor').forEach(textarea => {
            const id = textarea.id.replace('editor-', '');
            ClassicEditor.create(textarea).then(editor => {
                // Asignar contenido (protegido por JSON encoding)
                if (editorContents[id] !== undefined) {
                    editor.setData(editorContents[id]);
                }
                // Al enviar el formulario, transferimos datos del editor al textarea
                const form = textarea.closest('form');
                if (form) {
                    form.addEventListener('submit', (e) => {
                        // set the textarea's value to editor data so it is posted
                        textarea.value = editor.getData();
                    });
                }
            }).catch(error => console.error(error));
        });
    </script>
</body>
</html>