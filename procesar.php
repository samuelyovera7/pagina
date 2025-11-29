<?php
// =======================================================
//  CONFIGURACIÓN DE BASE DE DATOS
// =======================================================
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "maranatha_aragua";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Helper: asegurar que exista una tabla (si no, intentar crearla)
function ensure_table_exists($conn, $tableName, $createSql) {
    $safeName = $conn->real_escape_string($tableName);
    $chk = $conn->query("SHOW TABLES LIKE '" . $safeName . "'");
    if (!($chk && $chk->num_rows > 0)) {
        // Intentar crear la tabla automáticamente
        if ($conn->query($createSql) === TRUE) {
            return true;
        } else {
            // No se pudo crear la tabla: devolver mensaje de error para mostrar al usuario
            die("❌ La tabla '" . htmlspecialchars($tableName) . "' no existe y no se pudo crear automáticamente: " . htmlspecialchars($conn->error));
        }
    }
    return true;
}

// =======================================================
//  FUNCIÓN DEL MODAL PROFESIONAL (CON ESTILOS Y SCRIPTS)
// =======================================================
function mostrarModalExito($mensaje, $redireccion) {
    echo '
    <div id="overlayExito" class="overlay show">
        <div class="modal-exito animate-in">
            <div class="angel"></div>
            <h2>¡Registro completado!</h2>
            <p>' . htmlspecialchars($mensaje) . '</p>
            <p class="cuenta">Redirigiendo en <span id="contador">5</span>…</p>
            <button id="btnVolver">Volver Ahora</button>
        </div>
    </div>

<style>
/* ==== OVERLAY ==== */
.overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.70);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 999999;
    opacity: 0;
    pointer-events: none;
    transition: opacity .4s ease;
}
.overlay.show {
    opacity: 1;
    pointer-events: all;
}

/* ==== MODAL ==== */
.modal-exito {
    background: #ffffff;
    padding: 40px 35px;
    border-radius: 18px;
    text-align: center;
    width: 350px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.25);
    transform: scale(0.7);
    opacity: 0;
    transition: all .4s ease;
}
.modal-exito.animate-in {
    transform: scale(1);
    opacity: 1;
}
.modal-exito.animate-out {
    transform: scale(0.7);
    opacity: 0;
}

/* ==== ANGEL ==== */
.angel {
    width: 90px;
    height: 90px;
    margin: 0 auto 15px;
    background-image: url("https://cdn-icons-png.flaticon.com/512/4206/4206277.png");
    background-size: contain;
    background-repeat: no-repeat;
    animation: flotar 2.5s infinite ease-in-out;
}
@keyframes flotar {
    0%   { transform: translateY(0px); }
    50%  { transform: translateY(-12px); }
    100% { transform: translateY(0px); }
}

/* ==== TEXTO ==== */
h2 {
    color: #2ecc71;
    margin-bottom: 10px;
}
.cuenta {
    margin-top: 10px;
    font-size: 15px;
    color: #555;
}

/* ==== BOTÓN ==== */
#btnVolver {
    background: #2ecc71;
    border: none;
    padding: 10px 20px;
    color: white;
    margin-top: 20px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 16px;
    transition: .2s;
}
#btnVolver:hover {
    background: #27ae60;
}

/* ==== ANIMACIONES ==== */
@keyframes fadeOut {
    from { opacity: 1; }
    to   { opacity: 0; }
}
</style>

<script>
// === CONTADOR ===
let tiempo = 5;
const contador = document.getElementById("contador");
const overlay = document.getElementById("overlayExito");
const modal = document.querySelector(".modal-exito");

let interval = setInterval(() => {
    tiempo--;
    contador.textContent = tiempo;
    if (tiempo <= 0) cerrarYRedirigir();
}, 1000);

// === BOTÓN ===
document.getElementById("btnVolver").addEventListener("click", cerrarYRedirigir);

// === SALIDA CON ANIMACIÓN ===
function cerrarYRedirigir() {
    clearInterval(interval);
    modal.classList.remove("animate-in");
    modal.classList.add("animate-out");
    overlay.classList.remove("show");
    setTimeout(() => {
        window.location.href = "' . $redireccion . '";
    }, 400);
}
</script>
    ';
}

// =======================================================
//  PROCESAR FORMULARIO NUEVOS USUARIOS
// =======================================================
if (
    !isset($_POST["tipo_formulario"]) ||
    ($_POST["tipo_formulario"] !== "miembros" && $_POST["tipo_formulario"] !== "donaciones")
) {
    // Asumimos que es nuevos usuarios si no es miembros o donaciones
    $nombre = trim($_POST["nombreApellido"] ?? '');
    $edad = (int)($_POST["edad"] ?? 0);
    $direccion = trim($_POST["direccion"] ?? '');
    // Preferir el campo 'telefono' si tiene valor, si no tomar 'telefonoFinal' (hidden) o 'telefonoNumero'
    $telefono = trim($_POST["telefono"] ?? '');
    if (empty($telefono)) {
        $telefono = trim($_POST["telefonoFinal"] ?? $_POST["telefonoNumero"] ?? '');
    }
    $correo = trim($_POST["correo"] ?? '');
    $peticionO = $_POST["peticionOracion"] ?? '';
    $peticionT = trim($_POST["peticionOracionOtroTexto"] ?? '');
    $enteraste = $_POST["comoEnteraste"] ?? '';
    $enterasteOtro = trim($_POST["comoEnterasteOtro"] ?? '');

    // Validación
    if (empty($nombre) || $edad <= 0 || empty($direccion) || empty($telefono)) {
        die("❌ Datos incompletos para realizar registro.");
    }

    // Inserción correcta según tu base de datos actual
    $sql = "INSERT INTO contactos_nuevos 
        (nombre_apellido, edad, direccion, telefono, correo, peticion_oracion, peticion_texto, como_enteraste, como_enteraste_otro) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisssssss", $nombre, $edad, $direccion, $telefono, $correo, $peticionO, $peticionT, $enteraste, $enterasteOtro);

    // Obtener la página actual para NO redirigir siempre a index.php
$paginaActual = $_SERVER['HTTP_REFERER'] ?? 'index.php';

if ($stmt->execute()) {

    mostrarModalExito(
        "¡Gracias " . htmlspecialchars($nombre) . "! Tu aporte será de gran ayuda para nuestra comunidad y futuros eventos!❤️ Dios te bendiga 🙌🏻 ",
        $paginaActual
    );

} else {
    echo "❌ Error al guardar: " . $conn->error;
}


    $stmt->close();
}

// =======================================================
//  PROCESAR FORMULARIO MIEMBROS ACTIVOS
// =======================================================
elseif (isset($_POST["tipo_formulario"]) && $_POST["tipo_formulario"] === "miembros") {
    $nombre = trim($_POST["nombreApellidoMiembro"] ?? '');
    $edad = (int)($_POST["edadMiembro"] ?? 0);
    // Preferir 'telefonoMiembro' si existe y no está vacío, si no intentar los otros campos
    $telefono = trim($_POST["telefonoMiembro"] ?? '');
    if (empty($telefono)) {
        $telefono = trim($_POST["telefono"] ?? $_POST["telefonoFinal"] ?? '');
    }
    $area = $_POST["areaServicio"] ?? '';
    $direccion = trim($_POST["direccionMiembro"] ?? '');

    // Antes de insertar, comprobar que la tabla `miembros_activos` exista (si no, intentar crearla)
    $create_miembros_sql = "CREATE TABLE `miembros_activos` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `nombre_apellido` VARCHAR(255) NOT NULL,
        `edad` INT DEFAULT NULL,
        `telefono` VARCHAR(50) DEFAULT NULL,
        `area_servicio` VARCHAR(255) DEFAULT NULL,
        `direccion` TEXT DEFAULT NULL,
        `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    ensure_table_exists($conn, 'miembros_activos', $create_miembros_sql);

    // Validación
    if (empty($nombre) || $edad <= 0 || empty($telefono) || empty($area) || empty($direccion)) {
        die("❌ Datos incompletos para miembros activos.");
    }

    // Inserción correcta según tu tabla final
    $sql = "INSERT INTO miembros_activos 
        (nombre_apellido, edad, telefono, area_servicio, direccion) 
        VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisss", $nombre, $edad, $telefono, $area, $direccion);

    // Obtener la página actual para NO redirigir siempre a index.php
$paginaActual = $_SERVER['HTTP_REFERER'] ?? 'index.php';

if ($stmt->execute()) {

    mostrarModalExito(
        "¡Gracias " . htmlspecialchars($nombre) . "! Tu aporte será de gran ayuda para nuestra comunidad y futuros eventos!❤️ Dios te bendiga 🙌🏻 ",
        $paginaActual
    );

} else {
    echo "❌ Error al guardar: " . $conn->error;
}


    $stmt->close();
}

// =======================================================
//  PROCESAR FORMULARIO DONACIONES
// =======================================================
elseif (isset($_POST["tipo_formulario"]) && $_POST["tipo_formulario"] === "donaciones") {
    $nombre = trim($_POST["nombreDonante"] ?? '');
    $referencia = trim($_POST["referenciaPago"] ?? '');
    $comentario = trim($_POST["comentarioDonacion"] ?? '');

    // Validación
    if (empty($nombre) || empty($referencia)) {
        die("❌ Complete el formulario para completar su registro, por favor, .");
    }

    // Inserción a la tabla correcta
    // Asegurar que la tabla `donaciones` exista (si no, intentar crearla)
    $create_donaciones_sql = "CREATE TABLE donaciones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre_donante VARCHAR(255) NOT NULL,
        id_referencia_pago VARCHAR(100) NOT NULL,
        comentario TEXT,
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";

    ensure_table_exists($conn, 'donaciones', $create_donaciones_sql);

    // Si la columna id_referencia_pago no existe (por esquema antiguo), intentar agregarla
    $col_chk = $conn->query("SHOW COLUMNS FROM `donaciones` LIKE 'id_referencia_pago'");
    if (!($col_chk && $col_chk->num_rows > 0)) {
        $alter_sql = "ALTER TABLE `donaciones` ADD COLUMN `id_referencia_pago` VARCHAR(100) NOT NULL AFTER `nombre_donante`";
        if ($conn->query($alter_sql) === FALSE) {
            die("❌ La columna 'id_referencia_pago' no existe y no se pudo crear: " . htmlspecialchars($conn->error));
        }
    }

    $sql = "INSERT INTO donaciones (nombre_donante, id_referencia_pago, comentario) 
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nombre, $referencia, $comentario);

    if ($stmt->execute()) {
        mostrarModalExito(
            "¡Gracias " . htmlspecialchars($nombre) . "!Tu aporte será de gran ayuda para nuestra comunidad y futuros eventos!❤️ Dios te bendiga 🙌🏻 ", 
            "index.php"
        );
    } else {
        echo "❌ Error al guardar: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>