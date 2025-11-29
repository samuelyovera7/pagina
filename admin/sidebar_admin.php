<?php
// sidebar_admin.php
// Este archivo contiene el menú lateral del administrador con control de roles

// Determinamos si el usuario es superadmin o solo visualizador
$es_superadmin = ($_SESSION['rol'] ?? 'visualizador') === 'superadmin';
?>

<div id="sidebar">
    <div class="text-center p-3 mb-4">
        <img src="../img/Logo Maranatha Aragua.png" alt="Logo Admin" style="width: 100px;" class="mb-2">
        <h5 class="text-white">Admin: <?php echo htmlspecialchars($_SESSION["nombre"] ?? 'Usuario'); ?></h5>
        <small class="text-info">
            <?php echo $es_superadmin ? 'Super Administrador' : 'Visualizador de Datos'; ?>
        </small>
    </div>

    <ul class="nav flex-column">
        <!-- DASHBOARD - Todos pueden ver el dashboard -->
        <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" 
               href="dashboard.php">
                <i class="fa-solid fa-gauge me-2"></i> Dashboard
            </a>
        </li>

        <!-- EDITAR CONTENIDO WEB: opción removida -->

        <!-- BASE DE DATOS (FORMULARIOS) - Todos pueden ver -->
        <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'view_data.php') ? 'active' : ''; ?>" 
            href="view_data.php">
                <i class="fa-solid fa-database me-2"></i> Base de Datos (Formularios)
            </a>
        </li>

        <!-- GESTIÓN DE USUARIOS - SOLO SUPERADMIN (futuro) -->
        <?php if ($es_superadmin): ?>
        <li class="nav-item">
            <a class="nav-link" href="user_management.php">
                <i class="fa-solid fa-users-gear me-2"></i> Gestión de Usuarios
            </a>
        </li>
        <?php endif; ?>

        <!-- CERRAR SESIÓN - Todos -->
        <li class="nav-item mt-auto border-top pt-3">
            <a class="nav-link text-danger" href="../logout.php">
                <i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión
            </a>
        </li>
    </ul>
</div>