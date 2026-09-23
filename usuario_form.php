<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';

if ($usuarioActual['rol'] !== 'admin') {
    http_response_code(403);
    die('No tienes permisos para gestionar usuarios. Solo el rol "admin" puede hacerlo.');
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$esEdicion = $id > 0;
$error = '';

$usuario = ['nombres' => '', 'ap_paterno' => '', 'ap_materno' => '', 'email' => '', 'rol' => 'redactor'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = [
        'nombres'    => trim($_POST['nombres'] ?? ''),
        'ap_paterno' => trim($_POST['ap_paterno'] ?? ''),
        'ap_materno' => trim($_POST['ap_materno'] ?? ''),
        'email'      => trim($_POST['email'] ?? ''),
        'rol'        => $_POST['rol'] ?? 'redactor',
    ];
    $password = $_POST['password'] ?? '';

    if ($usuario['nombres'] === '' || $usuario['ap_paterno'] === '' || $usuario['email'] === '') {
        $error = 'Nombres, apellido paterno y correo son obligatorios.';
    } elseif (!$esEdicion && $password === '') {
        $error = 'La contraseña es obligatoria para un usuario nuevo.';
    } elseif (!in_array($usuario['rol'], ['admin', 'editor', 'redactor'], true)) {
        $error = 'Rol inválido.';
    } else {
        try {
            if ($esEdicion) {
                if ($password !== '') {
                    $stmt = $pdo->prepare(
                        'UPDATE usuarios SET nombres=?, ap_paterno=?, ap_materno=?, email=?, rol=?, password_hash=? WHERE id=?'
                    );
                    $stmt->execute([
                        $usuario['nombres'], $usuario['ap_paterno'], $usuario['ap_materno'] ?: null,
                        $usuario['email'], $usuario['rol'], password_hash($password, PASSWORD_DEFAULT), $id,
                    ]);
                } else {
                    $stmt = $pdo->prepare(
                        'UPDATE usuarios SET nombres=?, ap_paterno=?, ap_materno=?, email=?, rol=? WHERE id=?'
                    );
                    $stmt->execute([
                        $usuario['nombres'], $usuario['ap_paterno'], $usuario['ap_materno'] ?: null,
                        $usuario['email'], $usuario['rol'], $id,
                    ]);
                }
                header('Location: usuarios.php?ok=editado');
                exit;
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO usuarios (nombres, ap_paterno, ap_materno, email, password_hash, rol) VALUES (?,?,?,?,?,?)'
                );
                $stmt->execute([
                    $usuario['nombres'], $usuario['ap_paterno'], $usuario['ap_materno'] ?: null,
                    $usuario['email'], password_hash($password, PASSWORD_DEFAULT), $usuario['rol'],
                ]);
                header('Location: usuarios.php?ok=creado');
                exit;
            }
        } catch (PDOException $e) {
            $error = str_contains($e->getMessage(), 'uq_usuarios_email')
                ? 'Ya existe un usuario con ese correo.'
                : 'Error al guardar: ' . $e->getMessage();
        }
    }
} elseif ($esEdicion) {
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) $usuario = $found;
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $esEdicion ? 'Editar' : 'Nuevo' ?> usuario · Revista Digital NTEP</title><script>!function(){try{var t=localStorage.getItem("dash26-theme"),e=window.matchMedia("(prefers-color-scheme: dark)").matches;document.documentElement.setAttribute("data-theme",t||(e?"dark":"light"))}catch(t){document.documentElement.setAttribute("data-theme","light")}}()</script><script defer="defer" src="runtime.js"></script><script defer="defer" src="vendor-fullcalendar.js"></script><script defer="defer" src="vendor-chartjs.js"></script><script defer="defer" src="vendors.js"></script><script defer="defer" src="2026.js"></script><link href="style.css" rel="stylesheet"></head>
<body data-active="usuarios" data-crumbs="Revista | Usuarios | <?= $esEdicion ? 'Editar' : 'Nuevo' ?>">
<div class="shell">
  <div data-shell-sidebar></div>
  <div class="main">
    <div data-shell-topbar></div>
    <main class="content">
      <section class="hero">
        <div class="hero-text">
          <span class="eyebrow">Administración</span>
          <h1 class="hero-title"><?= $esEdicion ? 'Editar usuario' : 'Nuevo usuario' ?></h1>
          <p class="hero-sub">Los cambios se guardan directamente en la tabla <code>usuarios</code>. La contraseña se guarda con <code>password_hash()</code>.</p>
        </div>
      </section>

      <?php if ($error): ?>
        <div style="background:#fdecea;color:#b3261e;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13.5px;"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="grid">
        <section class="col-12 card">
          <form method="post" action="usuario_form.php" style="display:flex;flex-direction:column;gap:16px;max-width:600px">
            <?php if ($esEdicion): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>

            <div style="display:flex;gap:16px;flex-wrap:wrap">
              <div class="field" style="flex:1;min-width:180px">
                <label class="field-label" for="nombres">Nombres</label>
                <input class="input" id="nombres" name="nombres" type="text" required value="<?= htmlspecialchars($usuario['nombres']) ?>">
              </div>
              <div class="field" style="flex:1;min-width:180px">
                <label class="field-label" for="ap_paterno">Apellido paterno</label>
                <input class="input" id="ap_paterno" name="ap_paterno" type="text" required value="<?= htmlspecialchars($usuario['ap_paterno']) ?>">
              </div>
              <div class="field" style="flex:1;min-width:180px">
                <label class="field-label" for="ap_materno">Apellido materno</label>
                <input class="input" id="ap_materno" name="ap_materno" type="text" value="<?= htmlspecialchars($usuario['ap_materno'] ?? '') ?>">
              </div>
            </div>

            <div class="field">
              <label class="field-label" for="email">Correo</label>
              <input class="input" id="email" name="email" type="email" required value="<?= htmlspecialchars($usuario['email']) ?>">
            </div>

            <div style="display:flex;gap:16px;flex-wrap:wrap">
              <div class="field" style="flex:1;min-width:180px">
                <label class="field-label" for="rol">Rol</label>
                <select class="select" id="rol" name="rol" required>
                  <?php foreach (['admin', 'editor', 'redactor'] as $r): ?>
                    <option value="<?= $r ?>" <?= $usuario['rol'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field" style="flex:1;min-width:220px">
                <label class="field-label" for="password">Contraseña <?= $esEdicion ? '(dejar vacío para no cambiarla)' : '' ?></label>
                <input class="input" id="password" name="password" type="password" placeholder="••••••••" <?= $esEdicion ? '' : 'required' ?>>
              </div>
            </div>

            <div style="display:flex;gap:10px">
              <button class="btn btn--primary" type="submit"><?= $esEdicion ? 'Guardar cambios' : 'Crear usuario' ?></button>
              <a class="btn btn--ghost" href="usuarios.php">Cancelar</a>
            </div>
          </form>
        </section>
      </div>
    </main>
    <div data-shell-footer></div>
  </div>
</div>
</body></html>
