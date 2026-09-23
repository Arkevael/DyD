<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';

if ($usuarioActual['rol'] !== 'admin') {
    http_response_code(403);
    die('No tienes permisos para gestionar usuarios. Solo el rol "admin" puede hacerlo.');
}

$mensaje = '';
if (isset($_GET['ok']) && $_GET['ok'] === 'creado') $mensaje = 'Usuario creado correctamente.';
if (isset($_GET['ok']) && $_GET['ok'] === 'editado') $mensaje = 'Usuario actualizado correctamente.';
if (isset($_GET['ok']) && $_GET['ok'] === 'eliminado') $mensaje = 'Usuario eliminado.';
$errorMsg = '';
if (isset($_GET['error']) && $_GET['error'] === 'tiene_contenido') {
    $errorMsg = 'No se puede eliminar: este usuario tiene reportajes, noticias u otro contenido publicado a su nombre.';
}

$usuarios = $pdo->query('SELECT id, nombres, ap_paterno, ap_materno, email, rol, created_at FROM usuarios ORDER BY nombres')->fetchAll();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Usuarios · Revista Digital NTEP</title><script>!function(){try{var t=localStorage.getItem("dash26-theme"),e=window.matchMedia("(prefers-color-scheme: dark)").matches;document.documentElement.setAttribute("data-theme",t||(e?"dark":"light"))}catch(t){document.documentElement.setAttribute("data-theme","light")}}()</script><script defer="defer" src="runtime.js"></script><script defer="defer" src="vendor-fullcalendar.js"></script><script defer="defer" src="vendor-chartjs.js"></script><script defer="defer" src="vendors.js"></script><script defer="defer" src="2026.js"></script><link href="style.css" rel="stylesheet"></head>
<body data-active="usuarios" data-crumbs="Revista | Usuarios">
<div class="shell">
  <div data-shell-sidebar></div>
  <div class="main">
    <div data-shell-topbar></div>
    <main class="content">
      <section class="hero">
        <div class="hero-text">
          <span class="eyebrow">Administración</span>
          <h1 class="hero-title">Usuarios</h1>
          <p class="hero-sub">Cuentas que pueden ingresar al panel (tabla <code>usuarios</code>). Roles: admin, editor, redactor.</p>
        </div>
        <div class="hero-actions">
          <a class="btn btn--primary" href="usuario_form.php"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Nuevo usuario</a>
        </div>
      </section>

      <?php if ($mensaje): ?>
        <div style="background:#e6f7ee;color:#1a7f4e;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13.5px;"><?= htmlspecialchars($mensaje) ?></div>
      <?php endif; ?>
      <?php if ($errorMsg): ?>
        <div style="background:#fdecea;color:#b3261e;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13.5px;"><?= htmlspecialchars($errorMsg) ?></div>
      <?php endif; ?>

      <div class="grid">
        <section class="col-12 card">
          <div class="card-head"><div class="card-title-wrap"><h2 class="card-title">Todos los usuarios (<?= count($usuarios) ?>)</h2></div></div>
          <table class="table">
            <thead>
              <tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Creado</th><th style="text-align:right">Acciones</th></tr>
            </thead>
            <tbody>
              <?php foreach ($usuarios as $u): ?>
                <tr>
                  <td class="cell-name"><?= htmlspecialchars(trim($u['nombres'] . ' ' . $u['ap_paterno'] . ' ' . ($u['ap_materno'] ?? ''))) ?></td>
                  <td><?= htmlspecialchars($u['email']) ?></td>
                  <td><span class="tag <?= $u['rol'] === 'admin' ? 't-new' : 't-used' ?>"><?= htmlspecialchars($u['rol']) ?></span></td>
                  <td class="cell-date"><?= htmlspecialchars(date('d M Y', strtotime($u['created_at']))) ?></td>
                  <td style="text-align:right;white-space:nowrap">
                    <a class="btn btn--ghost" style="padding:4px 10px;font-size:12px" href="usuario_form.php?id=<?= (int)$u['id'] ?>">Editar</a>
                    <?php if ((int)$u['id'] !== (int)$usuarioActual['id']): ?>
                      <a class="btn btn--ghost" style="padding:4px 10px;font-size:12px;color:#b3261e" href="usuario_delete.php?id=<?= (int)$u['id'] ?>" onclick="return confirm('¿Eliminar este usuario?');">Eliminar</a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </section>
      </div>
    </main>
    <div data-shell-footer></div>
  </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
  setTimeout(function () {
    var nombre = <?= json_encode($usuarioActual['nombres']) ?>;
    var rol = <?= json_encode($usuarioActual['rol']) ?>;
    var iniciales = nombre.split(" ").map(function (p) { return p[0] || ""; }).slice(0, 2).join("").toUpperCase();
    document.querySelectorAll(".workspace-name").forEach(function (el) { el.textContent = nombre; });
    document.querySelectorAll(".workspace-role").forEach(function (el) { el.textContent = rol; });
    document.querySelectorAll(".workspace-avatar").forEach(function (el) { el.textContent = iniciales; });
    document.querySelectorAll(".dd-profile-name").forEach(function (el) { el.textContent = nombre; });
    document.querySelectorAll(".dd-profile-email").forEach(function (el) { el.textContent = <?= json_encode($usuarioActual['email']) ?>; });
    document.querySelectorAll(".avatar[data-dropdown]").forEach(function (el) { el.textContent = iniciales; });
  }, 30);
});
</script>
</body></html>
