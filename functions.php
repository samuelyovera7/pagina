<?php
// functions.php (puedes ponerlo en raíz o en admin/)
if (!isset($mysqli)) {
    require_once "admin/db_connect.php";
}

function get_contenido($seccion_clave) {
    global $mysqli;
    
    $stmt = $mysqli->prepare("SELECT contenido FROM contenido_web WHERE seccion_clave = ?");
    $stmt->bind_param("s", $seccion_clave);
    $stmt->execute();
    $stmt->bind_result($contenido);
    
    if ($stmt->fetch()) {
        $stmt->close();
        return $contenido;
    }
    
    $stmt->close();
    return "<p style='color:red;'>[Sección no configurada: $seccion_clave]</p>";
}
?>