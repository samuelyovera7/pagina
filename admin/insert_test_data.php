<?php
// admin/insert_test_data.php
// Inserta datos de prueba en las tablas para verificar el flujo de inserción.
require_once 'db_connect.php';

$results = [];

// 1) Insertar contacto nuevo de prueba
try {
    $stmt = $mysqli->prepare("INSERT INTO contactos_nuevos (nombre_apellido, edad, direccion, telefono, correo, peticion_oracion, peticion_texto, como_enteraste, como_enteraste_otro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $nombre = 'TEST Contacto';
        $edad = 30;
        $direccion = 'Calle Test 123';
        $telefono = '+5804123456789';
        $correo = 'test@ejemplo.com';
        $peticion_oracion = 'salud';
        $peticion_texto = '';
        $como = 'evento';
        $como_otro = '';
        $stmt->bind_param('sisssssss', $nombre, $edad, $direccion, $telefono, $correo, $peticion_oracion, $peticion_texto, $como, $como_otro);
        $ok = $stmt->execute();
        $results['contacto_id'] = $ok ? $stmt->insert_id : 'ERROR: '.$mysqli->error;
        $stmt->close();
    } else {
        $results['contacto'] = 'PREPARE_ERROR: '.$mysqli->error;
    }
} catch (Exception $e) {
    $results['contacto'] = 'EXCEPTION: '.$e->getMessage();
}

// 2) Insertar miembro activo de prueba
try {
    $stmt = $mysqli->prepare("INSERT INTO miembros_activos (nombre_apellido, edad, telefono, area_servicio, direccion) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $nombre = 'TEST Miembro';
        $edad = 28;
        $telefono = '+5804123456790';
        $area = 'Sonido';
        $direccion = 'Avenida Test 45';
        $stmt->bind_param('sisss', $nombre, $edad, $telefono, $area, $direccion);
        $ok = $stmt->execute();
        $results['miembro_id'] = $ok ? $stmt->insert_id : 'ERROR: '.$mysqli->error;
        $stmt->close();
    } else {
        $results['miembro'] = 'PREPARE_ERROR: '.$mysqli->error;
    }
} catch (Exception $e) {
    $results['miembro'] = 'EXCEPTION: '.$e->getMessage();
}

// 3) Insertar donación de prueba
try {
    $stmt = $mysqli->prepare("INSERT INTO donaciones (nombre_donante, id_referencia_pago, comentario) VALUES (?, ?, ?)");
    if ($stmt) {
        $nombre = 'TEST Donante';
        $ref = 'REF-TEST-'.time();
        $coment = 'Donación de prueba';
        $stmt->bind_param('sss', $nombre, $ref, $coment);
        $ok = $stmt->execute();
        $results['donacion_id'] = $ok ? $stmt->insert_id : 'ERROR: '.$mysqli->error;
        $stmt->close();
    } else {
        $results['donacion'] = 'PREPARE_ERROR: '.$mysqli->error;
    }
} catch (Exception $e) {
    $results['donacion'] = 'EXCEPTION: '.$e->getMessage();
}

$mysqli->close();

?><!DOCTYPE html>
<html lang="es">
<head>
</meta>
<meta charset="utf-8">
<title>Insert Test Data</title>
<!-- Styles moved to admin/admin_styles.css -->
<link rel="stylesheet" href="admin_styles.css">
</head>
<body class="admin-simple">
<h2>Resultado de inserciones de prueba</h2>
<pre><?php echo htmlspecialchars(print_r($results, true)); ?></pre>
<p><a href="check_db.php">Verificar en check_db.php</a></p>
<p><a href="dashboard.php">Volver al dashboard</a></p>
</body>
</html>