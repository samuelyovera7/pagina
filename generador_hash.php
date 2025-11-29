<?php
// generador_hash.php - Script para generar el hash de la contraseña

$contrasena_plana = 'admin'; 
$hash_seguro = password_hash($contrasena_plana, PASSWORD_BCRYPT);

echo "El hash de la contraseña '$contrasena_plana' es: <br>";
echo "<strong>" . $hash_seguro . "</strong>";
?>