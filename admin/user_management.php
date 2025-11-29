<?php
// admin/user_management.php

session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION['rol'] ?? 'visualizador') !== 'superadmin') {
    header("location: ../login.php");
    exit;
}

require_once __DIR__ . '/db_connect.php';

// actor info for audit logs
$actor_id = $_SESSION['id'] ?? null;
$actor_name = $_SESSION['usuario'] ?? ($_SESSION['username'] ?? null);

// Audit: page view
if (function_exists('admin_audit_log')) {
    admin_audit_log('user_management_view', 'Accedió a Gestión de Usuarios', $actor_id, $actor_name);
}

$errors = [];
$success = '';

function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $usuario = trim($_POST['username'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $password = $_POST['password'] ?? '';
        $q1 = trim($_POST['question1'] ?? '');
        $a1 = trim($_POST['answer1'] ?? '');
        $q2 = trim($_POST['question2'] ?? '');
        $a2 = trim($_POST['answer2'] ?? '');
        $pin = trim($_POST['pin'] ?? '');
        $role = trim($_POST['role'] ?? 'visualizador');

        if ($usuario === '') $errors[] = 'El nombre de usuario es obligatorio.';
        if ($nombre === '') $errors[] = 'El nombre completo es obligatorio.';
        if ($password === '') $errors[] = 'La contraseña es obligatoria.';

        if (empty($errors)) {
            if (!preg_match('/^(?=.*[A-Za-z])(?=.*\\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password)) {
                $errors[] = 'La contraseña debe tener mínimo 8 caracteres e incluir letras, números y al menos un carácter especial.';
            }
        }

        if (empty($errors)) {
            $chk = $mysqli->prepare("SELECT COUNT(*) AS c FROM usuarios_admin WHERE usuario = ? LIMIT 1");
            if ($chk) {
                $chk->bind_param('s', $usuario);
                $chk->execute();
                $r = $chk->get_result()->fetch_assoc();
                if (!empty($r) && intval($r['c']) > 0) {
                    $errors[] = 'El nombre de usuario ya existe. Elige otro.';
                    // Audit: intento de creación fallido por usuario duplicado
                    if (function_exists('admin_audit_log')) {
                        $actor_id = $_SESSION['id'] ?? null;
                        $actor_name = $_SESSION['usuario'] ?? ($_SESSION['username'] ?? null);
                        admin_audit_log('user_create_failed', "Intento crear usuario duplicado: {$usuario}", $actor_id, $actor_name);
                    }
                }
                $chk->close();
            }
        }

        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO usuarios_admin (usuario, nombre, contrasena, rol, fecha_creacion) VALUES (?, ?, ?, ?, NOW())");
            if ($stmt) {
                $stmt->bind_param('ssss', $usuario, $nombre, $hash, $role);
                if ($stmt->execute()) {
                    $new_user_id = $mysqli->insert_id;
                    // Save security questions + PIN (required)
                    if ($q1 === '' || $a1 === '' || $q2 === '' || $a2 === '' || !preg_match('/^\d{8}$/', $pin)) {
                        // Rollback created user to avoid partial state
                        $mysqli->query("DELETE FROM usuarios_admin WHERE id = " . intval($new_user_id));
                        $errors[] = 'Debe especificar dos preguntas, respuestas y un PIN de 8 dígitos.';
                        if (function_exists('admin_audit_log')) admin_audit_log('user_create_failed', "Faltan datos de seguridad para usuario: {$usuario}", $actor_id, $actor_name);
                    } else {
                        $ah1 = password_hash($a1, PASSWORD_DEFAULT);
                        $ah2 = password_hash($a2, PASSWORD_DEFAULT);
                        $ph = password_hash($pin, PASSWORD_DEFAULT);
                        $inssec = $mysqli->prepare("INSERT INTO admin_security (user_id, question1, answer1_hash, question2, answer2_hash, pin_hash) VALUES (?, ?, ?, ?, ?, ?)");
                        if ($inssec) {
                            $inssec->bind_param('isssss', $new_user_id, $q1, $ah1, $q2, $ah2, $ph);
                            $inssec->execute();
                            $inssec->close();
                            if (function_exists('admin_audit_log')) admin_audit_log('security_recovery_setup', "Preguntas y PIN configurados para usuario id={$new_user_id}", $actor_id, $actor_name);
                        }
                    }
                    // Audit: registro de creación de usuario por el admin en sesión
                    if (function_exists('admin_audit_log')) {
                        $actor_id = $_SESSION['id'] ?? null;
                        $actor_name = $_SESSION['usuario'] ?? ($_SESSION['username'] ?? null);
                        $detail = "Usuario creado: {$usuario}";
                        admin_audit_log('user_create', $detail, $actor_id, $actor_name);
                    }
                    if (empty($errors)) { header('Location: user_management.php?saved=1'); exit; }
                } else {
                    $errors[] = 'Error al crear usuario: ' . $stmt->error;
                    if (function_exists('admin_audit_log')) {
                        $actor_id = $_SESSION['id'] ?? null;
                        $actor_name = $_SESSION['usuario'] ?? ($_SESSION['username'] ?? null);
                        admin_audit_log('user_create_failed', "Error al crear usuario: {$usuario} - " . $stmt->error, $actor_id, $actor_name);
                    }
                }
                $stmt->close();
            } else {
                $errors[] = 'Error en la consulta: ' . $mysqli->error;
            }
        }

    } elseif ($action === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $usuario = trim($_POST['username'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $password = $_POST['password'] ?? null;
        $q1 = trim($_POST['question1'] ?? '');
        $a1 = trim($_POST['answer1'] ?? '');
        $q2 = trim($_POST['question2'] ?? '');
        $a2 = trim($_POST['answer2'] ?? '');
        $pin = trim($_POST['pin'] ?? '');
        $role = trim($_POST['role'] ?? 'visualizador');

        if ($id <= 0) $errors[] = 'ID de usuario inválido.';
        if ($usuario === '') $errors[] = 'El nombre de usuario es obligatorio.';
        if ($nombre === '') $errors[] = 'El nombre completo es obligatorio.';

        if (empty($errors)) {
            if ($password !== null && $password !== '') {
                // Audit: attempt to change password
                if (function_exists('admin_audit_log')) {
                    admin_audit_log('user_password_change_attempt', "Intento cambiar contraseña usuario id={$id}", $actor_id, $actor_name);
                }
               if ($q1 === '' || $a1 === '' || $q2 === '' || $a2 === '' || !preg_match('/^\d{8}$/', $pin)) {
    // Si la seguridad falla, debe revertir la creación del usuario si fue exitosa
    if (isset($user_id_temp)) {
        $mysqli->query("DELETE FROM usuarios_admin WHERE id = {$user_id_temp}");
    }
    $errors[] = 'Debe especificar dos preguntas, respuestas y un PIN de 8 dígitos.';
} else {
    // Si la seguridad es válida, guarda el pin y las preguntas
    $ih = $mysqli->prepare("INSERT INTO admin_security (user_id, question1, answer1_hash, question2, answer2_hash, pin_hash) VALUES (?, ?, ?, ?, ?, ?)");
                    if ($stmt) {
                        $stmt->bind_param('ssssi', $usuario, $nombre, $hash, $role, $id);
                        if ($stmt->execute()) {
                            if (function_exists('admin_audit_log')) {
                                $actor_id = $_SESSION['id'] ?? null;
                                $actor_name = $_SESSION['usuario'] ?? ($_SESSION['username'] ?? null);
                                $detail = "Usuario actualizado: {$usuario} (id={$id})";
                                admin_audit_log('user_update', $detail, $actor_id, $actor_name);
                                // Also log explicit password change event
                                admin_audit_log('user_password_changed', "Contraseña cambiada para usuario id={$id}", $actor_id, $actor_name);
                            }
                            header('Location: user_management.php?saved=1'); exit;
                        } else {
                            $errors[] = 'Error al actualizar: ' . $stmt->error;
                            if (function_exists('admin_audit_log')) {
                                $actor_id = $_SESSION['id'] ?? null;
                                $actor_name = $_SESSION['usuario'] ?? ($_SESSION['username'] ?? null);
                                admin_audit_log('user_update_failed', "Error al actualizar usuario id={$id}: " . $stmt->error, $actor_id, $actor_name);
                            }
                        }
                        $stmt->close();
                            // Update security fields if provided
                            if ($q1 !== '' || $a1 !== '' || $q2 !== '' || $a2 !== '' || $pin !== '') {
                                // Fetch existing
                                $sec_q = null;
                                $qsec = $mysqli->prepare("SELECT id FROM admin_security WHERE user_id = ? LIMIT 1");
                                if ($qsec) { $qsec->bind_param('i', $id); $qsec->execute(); $rsec = $qsec->get_result(); $sec_q = $rsec->fetch_assoc(); $qsec->close(); }
                                $ah1 = $a1 !== '' ? password_hash($a1, PASSWORD_DEFAULT) : null;
                                $ah2 = $a2 !== '' ? password_hash($a2, PASSWORD_DEFAULT) : null;
                                $ph = $pin !== '' ? password_hash($pin, PASSWORD_DEFAULT) : null;
                                if ($sec_q) {
                                    // build update dynamically
                                    $fields = [];
                                    $params = [];
                                    $types = '';
                                    if ($q1 !== '') { $fields[] = 'question1 = ?'; $params[] = $q1; $types .= 's'; }
                                    if ($ah1 !== null) { $fields[] = 'answer1_hash = ?'; $params[] = $ah1; $types .= 's'; }
                                    if ($q2 !== '') { $fields[] = 'question2 = ?'; $params[] = $q2; $types .= 's'; }
                                    if ($ah2 !== null) { $fields[] = 'answer2_hash = ?'; $params[] = $ah2; $types .= 's'; }
                                    if ($ph !== null) { $fields[] = 'pin_hash = ?'; $params[] = $ph; $types .= 's'; }
                                    if (!empty($fields)) {
                                        $sql = "UPDATE admin_security SET " . implode(', ', $fields) . " WHERE user_id = ?";
                                        $params[] = $id; $types .= 'i';
                                        $upsec = $mysqli->prepare($sql);
                                        if ($upsec) {
                                            $upsec->bind_param($types, ...$params);
                                            $upsec->execute();
                                            $upsec->close();
                                        }
                                    }
                                } else {
                                    // create only if full set provided
                                    if ($q1 !== '' && $a1 !== '' && $q2 !== '' && $a2 !== '' && preg_match('/^\d{8}$/', $pin)) {
                                        $ih = $mysqli->prepare("INSERT INTO admin_security (user_id, question1, answer1_hash, question2, answer2_hash, pin_hash) VALUES (?, ?, ?, ?, ?, ?)");
                                        if ($ih) { $ah1 = password_hash($a1, PASSWORD_DEFAULT); $ah2 = password_hash($a2, PASSWORD_DEFAULT); $ph = password_hash($pin, PASSWORD_DEFAULT); $ih->bind_param('isssss', $id, $q1, $ah1, $q2, $ah2, $ph); $ih->execute(); $ih->close(); }
                                }
                                if (function_exists('admin_audit_log')) admin_audit_log('security_recovery_updated', "Preguntas/PIN actualizados para usuario id={$id}", $actor_id, $actor_name);
                            }
                    } else {
                        $errors[] = 'Error en la consulta: ' . $mysqli->error;
                    }
                }}
            } else {
                $stmt = $mysqli->prepare("UPDATE usuarios_admin SET usuario = ?, nombre = ?, rol = ? WHERE id = ?");
                    if ($stmt) {
                        $stmt->bind_param('sssi', $usuario, $nombre, $role, $id);
                        if ($stmt->execute()) {
                            if (function_exists('admin_audit_log')) {
                                $actor_id = $_SESSION['id'] ?? null;
                                $actor_name = $_SESSION['usuario'] ?? ($_SESSION['username'] ?? null);
                                $detail = "Usuario actualizado: {$usuario} (id={$id})";
                                admin_audit_log('user_update', $detail, $actor_id, $actor_name);
                            }
                            header('Location: user_management.php?saved=1'); exit;
                        } else $errors[] = 'Error al actualizar: ' . $stmt->error;
                        $stmt->close();
                    } else {
                        $errors[] = 'Error en la consulta: ' . $mysqli->error;
                    }
            }
        }

    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        // Audit: deletion attempt received
        if (function_exists('admin_audit_log')) {
            admin_audit_log('user_delete_attempt', "Intento eliminar usuario id={$id}", $actor_id, $actor_name);
        }
        if ($id === intval($_SESSION['id'] ?? 0)) {
            $errors[] = 'No puedes eliminar tu propia cuenta mientras estás logueado.';
            if (function_exists('admin_audit_log')) {
                admin_audit_log('user_delete_blocked', "Intento eliminar cuenta propia id={$id}", $actor_id, $actor_name);
            }
        } elseif ($id <= 0) {
            $errors[] = 'ID de usuario inválido.';
            if (function_exists('admin_audit_log')) {
                admin_audit_log('user_delete_failed', "Intento eliminar con id inválido: {$id}", $actor_id, $actor_name);
            }
        }
        if (empty($errors)) {
            $stmt = $mysqli->prepare("DELETE FROM usuarios_admin WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('i', $id);
                if ($stmt->execute()) {
                    if (function_exists('admin_audit_log')) {
                        $actor_id = $_SESSION['id'] ?? null;
                        $actor_name = $_SESSION['usuario'] ?? ($_SESSION['username'] ?? null);
                        $detail = "Usuario eliminado: id={$id}";
                        admin_audit_log('user_delete', $detail, $actor_id, $actor_name);
                    }
                    header('Location: user_management.php?deleted=1'); exit;
                } else {
                    $errors[] = 'Error al eliminar: ' . $stmt->error;
                    if (function_exists('admin_audit_log')) {
                        $actor_id = $_SESSION['id'] ?? null;
                        $actor_name = $_SESSION['usuario'] ?? ($_SESSION['username'] ?? null);
                        admin_audit_log('user_delete_failed', "Error al eliminar usuario id={$id}: " . $stmt->error, $actor_id, $actor_name);
                    }
                }
                $stmt->close();
            } else { $errors[] = 'Error en la consulta: ' . $mysqli->error; }
        }
    }
}

// If editing via GET
$edit_user = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    if ($edit_id > 0) {
        $stmt = $mysqli->prepare("SELECT id, usuario AS username, nombre, rol FROM usuarios_admin WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $edit_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $edit_user = $res->fetch_assoc();
            $stmt->close();
            // Audit: opened edit form for a user
            if (function_exists('admin_audit_log')) {
                admin_audit_log('user_edit_view', "Abrió editor usuario id={$edit_id}", $actor_id, $actor_name);
            }
            // get security questions for display (do not prefill answers)
            $sec = $mysqli->prepare("SELECT question1, question2 FROM admin_security WHERE user_id = ? LIMIT 1");
            if ($sec) { $sec->bind_param('i', $edit_id); $sec->execute(); $rsec = $sec->get_result(); if ($srow = $rsec->fetch_assoc()) { $edit_user['question1'] = $srow['question1']; $edit_user['question2'] = $srow['question2']; } $sec->close(); }
        }
    }
}

// Fetch users list
$users = [];
$check = $mysqli->query("SHOW TABLES LIKE 'usuarios_admin'");
if ($check && $check->num_rows > 0) {
    $res = $mysqli->query("SELECT id, usuario AS username, nombre, rol, fecha_creacion FROM usuarios_admin ORDER BY id DESC");
    if ($res) { while ($row = $res->fetch_assoc()) $users[] = $row; $res->free(); }
} else {
    $errors[] = "La tabla 'usuarios_admin' no existe en la base de datos `" . DB_NAME . "`.";
}

// Render page
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Gestión de usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="admin_styles.css?t=<?php echo time(); ?>">
</head>
<body class="dashboard-body">

<?php include 'sidebar_admin.php'; ?>

<div class="container-fluid flex-grow-1 p-0">
    <main id="content" class="p-4">

        <h2 class="page-title mb-4"><i class="fa-solid fa-users-gear me-2"></i> Gestión de Usuarios</h2>

        <?php if (isset($_GET['saved']) || isset($_GET['deleted'])): ?>
            <div class="alert alert-success" role="alert">Operación realizada correctamente.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><ul class="mb-0">
                <?php foreach ($errors as $err): ?><li><?php echo e($err); ?></li><?php endforeach; ?>
            </ul></div>
        <?php endif; ?>

        <?php
        $is_edit = !empty($edit_user);
        $form_username = $_POST['username'] ?? $edit_user['username'] ?? '';
        $form_nombre = $_POST['nombre'] ?? $edit_user['nombre'] ?? '';
        $form_role = $_POST['role'] ?? $edit_user['rol'] ?? 'visualizador';
        ?>

        <div class="row">
            <div class="col-md-6">
                <section class="card mb-4">
                    <div class="card-header bg-light"><strong><?php echo $is_edit ? 'Editar usuario' : 'Crear usuario'; ?></strong></div>
                    <div class="card-body">
                        <form method="post" action="user_management.php">
                            <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit' : 'add'; ?>">
                            <?php if ($is_edit): ?>
                                <input type="hidden" name="id" value="<?php echo e($edit_user['id']); ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="username" class="form-label">Usuario (login)</label>
                                <input type="text" id="username" name="username" class="form-control" required value="<?php echo e($form_username); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre completo</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" required value="<?php echo e($form_nombre); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña <?php if ($is_edit): ?><small class="text-muted"> (dejar vacío para no cambiar)</small><?php endif; ?></label>
                                <input type="password" id="password" name="password" class="form-control" <?php echo $is_edit ? '' : 'required'; ?> pattern="(?=.*[A-Za-z])(?=.*\\d)(?=.*[^A-Za-z0-9]).{8,}" title="Mínimo 8 caracteres, letras, números y 1 carácter especial">
                                <div class="form-text">Mínimo 8 caracteres, debe incluir letras, números y al menos un carácter especial.</div>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Rol</label>
                                <select id="role" name="role" class="form-select">
                                    <option value="visualizador" <?php echo ($form_role === 'visualizador') ? 'selected' : ''; ?>>Visualizador de Datos</option>
                                    <option value="superadmin" <?php echo ($form_role === 'superadmin') ? 'selected' : ''; ?>>Super Administrador</option>
                                </select>
                            </div>

                            <hr>
                            <h6>Preguntas de seguridad y PIN (recuperación)</h6>
                            <div class="mb-3">
                                <label for="question1" class="form-label">Pregunta 1</label>
                                <input type="text" id="question1" name="question1" class="form-control" value="<?php echo e($_POST['question1'] ?? $edit_user['question1'] ?? ''); ?>" <?php echo $is_edit ? '' : 'required'; ?> >
                            </div>
                            <div class="mb-3">
                                <label for="answer1" class="form-label">Respuesta 1 <?php if ($is_edit): ?><small class="text-muted">(dejar vacío para no cambiar)</small><?php endif; ?></label>
                                <input type="text" id="answer1" name="answer1" class="form-control" <?php echo $is_edit ? '' : 'required'; ?> >
                            </div>

                            <div class="mb-3">
                                <label for="question2" class="form-label">Pregunta 2</label>
                                <input type="text" id="question2" name="question2" class="form-control" value="<?php echo e($_POST['question2'] ?? $edit_user['question2'] ?? ''); ?>" <?php echo $is_edit ? '' : 'required'; ?> >
                            </div>
                            <div class="mb-3">
                                <label for="answer2" class="form-label">Respuesta 2 <?php if ($is_edit): ?><small class="text-muted">(dejar vacío para no cambiar)</small><?php endif; ?></label>
                                <input type="text" id="answer2" name="answer2" class="form-control" <?php echo $is_edit ? '' : 'required'; ?> >
                            </div>

                            <div class="mb-3">
                                <label for="pin" class="form-label">PIN de recuperación (8 dígitos) <?php if ($is_edit): ?><small class="text-muted">(dejar vacío para no cambiar)</small><?php endif; ?></label>
                                <input type="text" id="pin" name="pin" class="form-control" pattern="\d{8}" maxlength="8" <?php echo $is_edit ? '' : 'required'; ?> >
                                <div class="form-text">El PIN debe ser exactamente 8 dígitos. Guárdalo en un lugar seguro.</div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-success me-2"><i class="fa-solid fa-floppy-disk me-1"></i> <?php echo $is_edit ? 'Actualizar' : 'Crear usuario'; ?></button>
                                <?php if ($is_edit): ?><a class="btn btn-outline-secondary" href="user_management.php">Cancelar</a><?php endif; ?>
                            </div>
                        </form>
                    </div>
                </section>
            </div>

            <div class="col-md-6">
                <section class="card mb-4">
                    <div class="card-header bg-light"><strong>Usuarios registrados</strong></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Nombre</th>
                                        <th>Rol</th>
                                        <th>Creado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($users)): ?>
                                    <tr><td colspan="6" class="text-center p-3">No hay usuarios registrados.</td></tr>
                                <?php else: foreach ($users as $u): ?>
                                    <tr>
                                        <td><?php echo e($u['id']); ?></td>
                                        <td><?php echo e($u['username']); ?></td>
                                        <td><?php echo e($u['nombre'] ?? ''); ?></td>
                                        <td><span class="badge <?php echo e($u['rol'] === 'superadmin' ? 'bg-primary' : 'bg-secondary'); ?>"><?php echo e($u['rol'] ?? ''); ?></span></td>
                                        <td><?php echo e(substr($u['fecha_creacion'] ?? '', 0, 10)); ?></td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-outline-primary me-1" href="?edit_id=<?php echo e($u['id']); ?>"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
                                            <?php if (intval($u['id']) !== intval($_SESSION['id'] ?? 0)): ?>
                                            <form method="post" action="user_management.php" class="d-inline-block" onsubmit="return confirm('¿Está seguro de eliminar el usuario <?php echo e($u['username']); ?>?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo e($u['id']); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can"></i> Eliminar</button>
                                            </form>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-warning" disabled><i class="fa-solid fa-user-check"></i> Actual</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>

    </main>
</div>

<script src="admin-functions.js?t=<?php echo time(); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

</body>
</html>