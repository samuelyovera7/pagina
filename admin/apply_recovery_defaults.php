<?php
// apply_recovery_defaults.php
// Small protected script to seed recovery questions+answers+PIN for a given admin user.
// Usage (from the server / localhost):
//   http://localhost/iglesia/admin/apply_recovery_defaults.php?u=USERNAME
// NOTE: This script should be removed after use or protected behind admin auth.

if (php_sapi_name() === 'cli') {
    echo "Run this from your browser (localhost) to seed recovery data.\n";
    exit;
}

session_start();
require_once __DIR__ . '/db_connect.php';

// Only allow from localhost by default for safety
$remote = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($remote, ['127.0.0.1', '::1'])) {
    http_response_code(403);
    echo "Forbidden: this script may only be run from localhost for safety.";
    exit;
}

$username = trim($_GET['u'] ?? $_POST['username'] ?? '');

// default values (from your message)
$default_q1 = 'quien fue el fundador de maranatha aragua';
$default_a1 = 'arquimedes';
$default_q2 = 'cuantos años tiene la iglesia';
$default_a2 = '8';
$default_pin = '12345678';

$status = '';
$existing_data = null;

if ($username !== '') {
    // Look up the user id
    $stmt = $mysqli->prepare("SELECT id, usuario FROM usuarios_admin WHERE usuario = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
    }

    if (empty($row)) {
        $status = "Usuario no encontrado: " . htmlspecialchars($username);
    } else {
        $uid = (int)$row['id'];

        // show existing admin_security
        $chk = $mysqli->prepare("SELECT question1, question2 FROM admin_security WHERE user_id = ? LIMIT 1");
        if ($chk) {
            $chk->bind_param('i', $uid);
            $chk->execute();
            $cres = $chk->get_result();
            $existing_data = $cres->fetch_assoc();
            $chk->close();
        }

        // If POST action seed then insert/update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do_seed'] ?? '') === '1') {
            $question1 = $default_q1;
            $answer1 = $default_a1;
            $question2 = $default_q2;
            $answer2 = $default_a2;
            $pin = $default_pin;

            // Hash answers and PIN
            $a1h = password_hash($answer1, PASSWORD_DEFAULT);
            $a2h = password_hash($answer2, PASSWORD_DEFAULT);
            $pinh = password_hash($pin, PASSWORD_DEFAULT);

            // Insert or update admin_security
            $chk2 = $mysqli->prepare("SELECT id FROM admin_security WHERE user_id = ? LIMIT 1");
            if ($chk2) {
                $chk2->bind_param('i', $uid);
                $chk2->execute();
                $cres2 = $chk2->get_result();
                $existing2 = $cres2->fetch_assoc();
                $chk2->close();
            }

            if ($existing2) {
                $up = $mysqli->prepare("UPDATE admin_security SET question1 = ?, answer1_hash = ?, question2 = ?, answer2_hash = ?, pin_hash = ? WHERE user_id = ?");
                if ($up) {
                    $up->bind_param('sssssi', $question1, $a1h, $question2, $a2h, $pinh, $uid);
                    $ok = $up->execute();
                    $up->close();
                    $action = 'updated';
                } else {
                    $ok = false;
                }
            } else {
                $ins = $mysqli->prepare("INSERT INTO admin_security (user_id, question1, answer1_hash, question2, answer2_hash, pin_hash) VALUES (?, ?, ?, ?, ?, ?)");
                if ($ins) {
                    $ins->bind_param('isssss', $uid, $question1, $a1h, $question2, $a2h, $pinh);
                    $ok = $ins->execute();
                    $ins->close();
                    $action = 'inserted';
                } else {
                    $ok = false;
                }
            }

            if ($ok) {
                if (function_exists('admin_audit_log')) {
                    admin_audit_log('admin_security_seeded', "Seeded recovery questions+PIN ({$action}) for user: {$username}", $uid, $username);
                }
                $status = "Success: recovery questions and PIN {$action} for user: " . htmlspecialchars($username);
                // reload existing_data
                $chk3 = $mysqli->prepare("SELECT question1, question2 FROM admin_security WHERE user_id = ? LIMIT 1");
                if ($chk3) { $chk3->bind_param('i', $uid); $chk3->execute(); $cres3 = $chk3->get_result(); $existing_data = $cres3->fetch_assoc(); $chk3->close(); }
            } else {
                $status = "Database operation failed.";
            }
        }
    }
}

// Render a simple HTML UI so it's clear what's happening
?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sembrar preguntas de recuperación</title>
  <style>body{font-family:Arial,Helvetica,sans-serif;margin:20px} .card{border:1px solid #ddd;padding:16px;border-radius:6px;max-width:720px}</style>
</head>
<body>
  <div class="card">
    <h3>Sembrar preguntas de recuperación (localhost)</h3>
    <p>Acceso restringido a localhost. Introduce el usuario admin (exacto) en el parámetro <code>?u=USERNAME</code> o usa el formulario.</p>
    <?php if ($status): ?>
      <div style="padding:8px;background:#eef;border:1px solid #cce;margin-bottom:12px"><?php echo htmlspecialchars($status); ?></div>
    <?php endif; ?>

    <form method="get">
      <label>Usuario: <input name="u" value="<?php echo htmlspecialchars($username); ?>"></label>
      <button type="submit">Ver estado</button>
    </form>

    <?php if (!empty($row)): ?>
      <h4>Usuario encontrado: <?php echo htmlspecialchars($row['usuario']); ?></h4>
      <?php if ($existing_data): ?>
        <p><strong>Preguntas ya configuradas:</strong></p>
        <ol>
          <li><?php echo htmlspecialchars($existing_data['question1']); ?></li>
          <li><?php echo htmlspecialchars($existing_data['question2']); ?></li>
        </ol>
        <p>Si quieres sobrescribirlas con los valores por defecto, usa el botón a continuación.</p>
      <?php else: ?>
        <p>No hay preguntas configuradas para este usuario.</p>
        <p>Si quieres sembrar las preguntas/answers/PIN ahora (valores por defecto), pulsa "Sembrar".</p>
      <?php endif; ?>

      <form method="post" style="margin-top:12px">
        <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
        <input type="hidden" name="do_seed" value="1">
        <button type="submit">Sembrar preguntas+PIN por defecto para <?php echo htmlspecialchars($username); ?></button>
      </form>

      <h5 style="margin-top:16px">Valores por defecto que serán aplicados</h5>
      <ul>
        <li><strong>Pregunta 1:</strong> <?php echo htmlspecialchars($default_q1); ?></li>
        <li><strong>Respuesta 1:</strong> <?php echo htmlspecialchars($default_a1); ?></li>
        <li><strong>Pregunta 2:</strong> <?php echo htmlspecialchars($default_q2); ?></li>
        <li><strong>Respuesta 2:</strong> <?php echo htmlspecialchars($default_a2); ?></li>
        <li><strong>PIN:</strong> <?php echo htmlspecialchars($default_pin); ?></li>
      </ul>

      <p style="color:#a00">Recuerda eliminar este archivo cuando termines para evitar riesgos de seguridad.</p>
    <?php else: ?>
      <p>Introduce un usuario primero para continuar.</p>
    <?php endif; ?>
  </div>
</body>
</html>

?>