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
// Paginación
$por_pagina = 15;
$pagina_actual = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

// Pestaña activa
$tab = $_GET['tab'] ?? 'contactos';

// Calcular colspan según pestaña (usado para mensaje "No hay registros aún")
$colspan_map = [
    'contactos' => 11,
    'miembros' => 5,
    'donaciones' => 5,
];
$colspan = $colspan_map[$tab] ?? 5;

// Errores acumulados
$errors = [];

// Búsqueda
$busqueda = $_GET['q'] ?? '';
$busqueda_like = '%' . $mysqli->real_escape_string($busqueda) . '%';

// Comprobar existencia de tablas y contar totales para badges (evitar fatal error si falta alguna tabla)
$tables_to_check = ['contactos_nuevos','miembros_activos','donaciones'];
$table_exists = [];
foreach ($tables_to_check as $t) {
    $chk = $mysqli->query("SHOW TABLES LIKE '" . $mysqli->real_escape_string($t) . "'");
    $table_exists[$t] = ($chk && $chk->num_rows > 0);
}

$total_contactos = $table_exists['contactos_nuevos'] ? (int)$mysqli->query("SELECT COUNT(*) FROM contactos_nuevos")->fetch_row()[0] : 0;
$total_miembros = $table_exists['miembros_activos'] ? (int)$mysqli->query("SELECT COUNT(*) FROM miembros_activos")->fetch_row()[0] : 0;
$total_donaciones = $table_exists['donaciones'] ? (int)$mysqli->query("SELECT COUNT(*) FROM donaciones")->fetch_row()[0] : 0;

// Preparar mensajes/SQL para tablas faltantes
$missing = [];
if (!$table_exists['miembros_activos']) {
    $missing[] = 'miembros_activos';
    $create_miembros_sql = "CREATE TABLE miembros_activos (\n" .
        "  id INT AUTO_INCREMENT PRIMARY KEY,\n" .
        "  nombre_apellido VARCHAR(255) NOT NULL,\n" .
        "  edad INT NOT NULL,\n" .
        "  telefono VARCHAR(20) NOT NULL,\n" .
        "  area_servicio VARCHAR(100) NOT NULL,\n" .
        "  direccion VARCHAR(300) NOT NULL,\n" .
        "  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n" .
        ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
}
if (!$table_exists['contactos_nuevos']) $missing[] = 'contactos_nuevos';
if (!$table_exists['donaciones']) $missing[] = 'donaciones';
if (!empty($missing)) {
    $errors[] = "Faltan tablas: " . implode(', ', $missing) . ". Algunas vistas estarán vacías hasta crearlas.";
    if (isset($create_miembros_sql)) {
        $errors[] = "SQL para crear 'miembros_activos':\n" . $create_miembros_sql;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base de Datos - Maranatha Aragua</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin_styles.css">
    <!-- Styles moved to admin/admin_styles.css -->
</head>
<body class="dashboard-body">
    <?php include 'sidebar_admin.php'; ?>

    <div id="content">
        <div class="container-fluid py-4">
            <h2 class="mb-4"><i class="fa-solid fa-database me-2"></i> Base de Datos - Formularios</h2>

            <!-- Barra de búsqueda -->
            <form class="mb-4">
                <div class="input-group search-box">
                    <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Buscar por nombre o teléfono..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <?php if ($busqueda): ?>
                        <a href="view_data.php?tab=<?php echo $tab; ?>" class="btn btn-outline-secondary">Limpiar</a>
                    <?php endif; ?>
                </div>
            </form>

                    <?php if (!empty($errors)): ?>
                        <div class="container-fluid mb-3">
                            <?php foreach ($errors as $err): ?>
                                <div class="alert alert-warning"><?php echo nl2br(htmlspecialchars($err)); ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <input type="hidden" name="tab" value="<?php echo $tab; ?>">

            <!-- Pestañas -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo $tab==='contactos'?'active':''; ?>" href="?tab=contactos&q=<?php echo urlencode($busqueda); ?>">
                        Contactos Nuevos <span class="badge bg-primary badge-large"><?php echo $total_contactos; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $tab==='miembros'?'active':''; ?>" href="?tab=miembros&q=<?php echo urlencode($busqueda); ?>">
                        Miembros Activos <span class="badge bg-success badge-large"><?php echo $total_miembros; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $tab==='donaciones'?'active':''; ?>" href="?tab=donaciones&q=<?php echo urlencode($busqueda); ?>">
                        Donaciones <span class="badge bg-warning badge-large"><?php echo $total_donaciones; ?></span>
                    </a>
                </li>
            </ul>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <?php if ($tab === 'contactos'): ?>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Edad</th>
                                        <th>Dirección</th>
                                        <th>Teléfono</th>
                                        <th>Correo</th>
                                        <th>Petición</th>
                                        <th>Petición Texto</th>
                                        <th>Cómo se enteró</th>
                                        <th>Cómo se enteró (otro)</th>
                                        <th>Fecha</th>
                                    <?php elseif ($tab === 'miembros'): ?>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Teléfono</th>
                                        <th>Área de Servicio</th>
                                        <th>Fecha</th>
                                    <?php elseif ($tab === 'donaciones'): ?>
                                        <th>ID</th>
                                        <th>Donante</th>
                                        <th>Referencia</th>
                                        <th>Comentario</th>
                                        <th>Fecha</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "";

                                $result = null;
                                // Inicializar rows por defecto para evitar warnings si no se ejecuta la consulta
                                $rows = [];
                                try {
                                        if ($tab === 'contactos') {
                                        // Seleccionar los campos completos de contactos_nuevos (usar columna `fecha_registro`)
                                        $sql = "SELECT id, nombre_apellido, edad, direccion, telefono, correo, peticion_oracion, peticion_texto, como_enteraste, como_enteraste_otro, fecha_registro \
                                            FROM contactos_nuevos \
                                            WHERE nombre_apellido LIKE ? OR telefono LIKE ?\n+                                            ORDER BY fecha_registro DESC LIMIT ? OFFSET ?";
                                        $stmt = $mysqli->prepare($sql);
                                        $stmt->bind_param("ssii", $busqueda_like, $busqueda_like, $por_pagina, $offset);
                                    } elseif ($tab === 'miembros') {
                                        $sql = "SELECT id, nombre_apellido, telefono, area_servicio, fecha_registro 
                                                FROM miembros_activos 
                                                WHERE nombre_apellido LIKE ? OR telefono LIKE ?
                                                ORDER BY fecha_registro DESC LIMIT ? OFFSET ?";
                                        $stmt = $mysqli->prepare($sql);
                                        $stmt->bind_param("ssii", $busqueda_like, $busqueda_like, $por_pagina, $offset);
                                    } elseif ($tab === 'donaciones') {
                                        // La tabla 'donaciones' en el esquema contiene: id, nombre_donante, id_referencia_pago, comentario, fecha_registro
                                        $sql = "SELECT id, nombre_donante, id_referencia_pago, comentario, fecha_registro \
                                                    FROM donaciones \
                                                    WHERE nombre_donante LIKE ? OR id_referencia_pago LIKE ?\n+                                                    ORDER BY fecha_registro DESC LIMIT ? OFFSET ?";
                                        $stmt = $mysqli->prepare($sql);
                                        $stmt->bind_param("ssii", $busqueda_like, $busqueda_like, $por_pagina, $offset);
                                    }

                                    // Ejecutar sólo si se pudo preparar la consulta y la tabla exista
                                    if ($stmt && (
                                        ($tab==='contactos' && $table_exists['contactos_nuevos']) ||
                                        ($tab==='miembros' && $table_exists['miembros_activos']) ||
                                        ($tab==='donaciones' && $table_exists['donaciones'])
                                    )) {
                                        if (!$stmt->execute()) {
                                            $errors[] = "Error al ejecutar la consulta: " . $mysqli->error;
                                        } else {
                                            // Intentar obtener resultado con get_result (requiere mysqlnd)
                                            $result = $stmt->get_result();
                                            $rows = [];
                                            if ($result) {
                                                $rows = $result->fetch_all(MYSQLI_ASSOC);
                                            } else {
                                                // Fallback: usar bind_result/ fetch si get_result no está disponible
                                                $stmt->store_result();
                                                $meta = $stmt->result_metadata();
                                                if ($meta) {
                                                    $fields = [];
                                                    $bind = [];
                                                    while ($f = $meta->fetch_field()) {
                                                        $fields[] = $f->name;
                                                        $bind[] = &${'col_'.$f->name};
                                                    }
                                                    // Bind variables
                                                    call_user_func_array(array($stmt, 'bind_result'), $bind);
                                                    while ($stmt->fetch()) {
                                                        $r = [];
                                                        foreach ($fields as $idx => $fname) {
                                                            $r[$fname] = ${'col_'.$fname};
                                                        }
                                                        $rows[] = $r;
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        if (!$stmt) {
                                            $errors[] = "Error al preparar la consulta: " . $mysqli->error;
                                        }
                                    }
                                } catch (mysqli_sql_exception $e) {
                                    $result = null;
                                    $errors[] = "Error en la consulta para la pestaña '" . $tab . "': " . $e->getMessage();
                                }

                                // Usar $rows si está disponible (llenado arriba), o usar $result si es un mysqli_result
                                if (!empty($rows)) {
                                    $use_rows = $rows;
                                } elseif ($result && $result->num_rows > 0) {
                                    $use_rows = $result->fetch_all(MYSQLI_ASSOC);
                                } else {
                                    $use_rows = [];
                                }

                                // Fallback diagnóstico: si la consulta preparada no devolvió filas
                                if (empty($use_rows)) {
                                    if ($tab === 'contactos' && $table_exists['contactos_nuevos']) {
                                        $fb = $mysqli->query("SELECT * FROM contactos_nuevos ORDER BY fecha_registro DESC LIMIT 10");
                                        if ($fb && $fb->num_rows > 0) {
                                            $use_rows = $fb->fetch_all(MYSQLI_ASSOC);
                                        } else {
                                            $errors[] = "Diagnóstico: la tabla 'contactos_nuevos' existe pero no devolvió filas en la consulta preparada.";
                                        }
                                    }
                                    if ($tab === 'donaciones' && $table_exists['donaciones']) {
                                        $fb2 = $mysqli->query("SELECT * FROM donaciones ORDER BY fecha_registro DESC LIMIT 10");
                                        if ($fb2 && $fb2->num_rows > 0) {
                                            $use_rows = $fb2->fetch_all(MYSQLI_ASSOC);
                                        } else {
                                            $errors[] = "Diagnóstico: la tabla 'donaciones' existe pero no devolvió filas en la consulta preparada.";
                                        }
                                    }
                                }

                                if (empty($use_rows)):
                                    echo "<tr><td colspan='" . (int)$colspan . "' class='text-center py-5'>No hay registros aún.</td></tr>";
                                else:
                                    foreach ($use_rows as $row):
                                        if ($tab === 'contactos'):
                                            $direccion = htmlspecialchars($row['direccion'] ?? '');
                                            $telefono = htmlspecialchars($row['telefono'] ?? '');
                                            $correo = htmlspecialchars($row['correo'] ?? '');
                                            $peticion = htmlspecialchars($row['peticion_oracion'] ?? '');
                                            $peticion_texto = htmlspecialchars($row['peticion_texto'] ?? '');
                                            $como = htmlspecialchars($row['como_enteraste'] ?? '');
                                            $como_otro = htmlspecialchars($row['como_enteraste_otro'] ?? '');
                                            // Usar siempre `fecha_registro` (esquema actual)
                                            $fecha_contacto = !empty($row['fecha_registro']) ? date('d/m/Y H:i', strtotime($row['fecha_registro'])) : '';
                                            echo "<tr>
                                                <td>{$row['id']}</td>
                                                <td>" . htmlspecialchars($row['nombre_apellido'] ?? '') . "</td>
                                                <td>" . htmlspecialchars($row['edad'] ?? '') . "</td>
                                                <td>$direccion</td>
                                                <td>$telefono</td>
                                                <td>$correo</td>
                                                <td>$peticion</td>
                                                <td>$peticion_texto</td>
                                                <td>$como</td>
                                                <td>$como_otro</td>
                                                <td>$fecha_contacto</td>
                                            </tr>";
                                        elseif ($tab === 'miembros'):
                                            echo "<tr>
                                                <td>{$row['id']}</td>
                                                <td>" . htmlspecialchars($row['nombre_apellido']) . "</td>
                                                <td>" . htmlspecialchars($row['telefono']) . "</td>
                                                <td>" . htmlspecialchars($row['area_servicio']) . "</td>
                                                <td>" . date('d/m/Y', strtotime($row['fecha_registro'])) . "</td>
                                            </tr>";
                                        elseif ($tab === 'donaciones'):
                                            // Renderizar: mostrar referencia, comentario y fecha
                                            $referencia = htmlspecialchars($row['id_referencia_pago'] ?? '');
                                            $comentario = empty($row['comentario']) ? '—' : htmlspecialchars(substr($row['comentario'], 0, 60)) . "...";
                                            // Usar `fecha_registro` según el esquema de la tabla
                                            $fecha = !empty($row['fecha_registro']) ? date('d/m/Y H:i', strtotime($row['fecha_registro'])) : '';
                                            echo "<tr>
                                                <td>{$row['id']}</td>
                                                <td>" . htmlspecialchars($row['nombre_donante'] ?? '') . "</td>
                                                <td>" . (!empty($referencia) ? $referencia : '—') . "</td>
                                                <td>$comentario</td>
                                                <td>$fecha</td>
                                            </tr>";
                                        endif;
                                    endforeach;
                                endif;
                                if (!empty($stmt) && $stmt instanceof mysqli_stmt) {
                                    $stmt->close();
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación simple -->
                    <?php if (!empty($use_rows)): ?>
                    <div class="card-footer bg-light">
                        <nav>
                            <ul class="pagination justify-content-center mb-0">
                                <li class="page-item <?php echo $pagina_actual <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?tab=<?php echo $tab; ?>&q=<?php echo urlencode($busqueda); ?>&p=<?php echo $pagina_actual-1; ?>">Anterior</a>
                                </li>
                                <li class="page-item active">
                                    <span class="page-link">Página <?php echo $pagina_actual; ?></span>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?tab=<?php echo $tab; ?>&q=<?php echo urlencode($busqueda); ?>&p=<?php echo $pagina_actual+1; ?>">Siguiente</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="admin-functions.js?t=<?php echo time(); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>