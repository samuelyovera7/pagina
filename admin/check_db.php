<?php
// admin/check_db.php - Página de diagnóstico rápido para tablas y conteos
require_once 'db_connect.php';

$tables = ['contactos_nuevos','miembros_activos','donaciones','usuarios_admin'];

function table_exists($mysqli, $table) {
    $t = $mysqli->real_escape_string($table);
    $res = $mysqli->query("SHOW TABLES LIKE '".$t."'");
    return ($res && $res->num_rows > 0);
}

?><!DOCTYPE html>
<html lang="es">
<head>
</meta>
<meta charset="utf-8">
<title>Check DB - Diagnóstico</title>
<!-- Styles moved to admin/admin_styles.css -->
<link rel="stylesheet" href="admin_styles.css">
</head>
<body class="admin-simple">
<h2>Diagnóstico rápido de la base de datos</h2>
<?php
// Compatibilidad con las constantes definidas en db_connect.php
$db_host = defined('DB_SERVER') ? constant('DB_SERVER') : (defined('DB_HOST') ? constant('DB_HOST') : 'desconocido');
$db_name = defined('DB_NAME') ? constant('DB_NAME') : (defined('DB_DATABASE') ? constant('DB_DATABASE') : 'desconocido');
?>
<p>Host: <strong><?php echo htmlspecialchars($db_host); ?></strong> — DB: <strong><?php echo htmlspecialchars($db_name); ?></strong></p>
<table>
<thead><tr><th>Tabla</th><th>Existe</th><th>Filas</th><th>Ejemplo (hasta 5 filas)</th></tr></thead>
<tbody>
<?php foreach ($tables as $tbl): ?>
<tr>
    <td><?php echo htmlspecialchars($tbl); ?></td>
    <td><?php echo table_exists($mysqli, $tbl) ? '<strong style="color:green">Sí</strong>' : '<strong style="color:red">No</strong>'; ?></td>
    <td>
        <?php
        if (table_exists($mysqli, $tbl)) {
            $r = $mysqli->query("SELECT COUNT(*) FROM `".$mysqli->real_escape_string($tbl)."`");
            if ($r) {
                echo (int)$r->fetch_row()[0];
            } else {
                echo 'Error: '.htmlspecialchars($mysqli->error);
            }
        } else echo '-';
        ?>
    </td>
    <td>
        <?php
        if (table_exists($mysqli, $tbl)) {
            $s = $mysqli->query("SELECT * FROM `".$mysqli->real_escape_string($tbl)."` LIMIT 5");
            if ($s && $s->num_rows>0) {
                echo '<table><thead>';
                // cabeceras
                $finfo = $s->fetch_fields();
                echo '<tr>'; foreach ($finfo as $f) echo '<th>'.htmlspecialchars($f->name).'</th>'; echo '</tr>';
                echo '</thead><tbody>';
                $s->data_seek(0);
                while ($row = $s->fetch_assoc()) {
                    echo '<tr>';
                    foreach ($row as $v) echo '<td>'.htmlspecialchars((string)$v).'</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<em>No hay filas</em>';
            }
        } else echo '<em>-</em>';
        ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<p style="margin-top:18px">Sugerencia: si las tablas existen pero no hay filas, envía el formulario de contacto o de donaciones en el sitio para que se creen.</p>
</body>
</html>