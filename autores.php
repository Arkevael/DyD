<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';

$mensaje = '';
if (isset($_GET['ok']) && $_GET['ok'] === 'creado') $mensaje = 'Autor creado correctamente.';
if (isset($_GET['ok']) && $_GET['ok'] === 'editado') $mensaje = 'Autor actualizado correctamente.';
if (isset($_GET['ok']) && $_GET['ok'] === 'eliminado') $mensaje = 'Autor eliminado.';
$errorMsg = '';
if (isset($_GET['error']) && $_GET['error'] === 'tiene_reportajes') {
    $errorMsg = 'No se puede eliminar: este autor tiene reportajes asociados. Reasígnalos o elimínalos primero.';
}

$autores = $pdo->query(
    'SELECT a.*, (SELECT COUNT(*) FROM reportajes r WHERE r.autor_id = a.id) AS total_reportajes
     FROM autores a ORDER BY a.nombres'
)->fetchAll();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Autores · Revista Digital NTEP</title><script>!function(){try{var t=localStorage.getItem("dash26-theme"),e=window.matchMedia("(prefers-color-scheme: dark)").matches;document.documentElement.setAttribute("data-theme",t||(e?"dark":"light"))}catch(t){document.documentElement.setAttribute("data-theme","light")}}()</script><script defer="defer" src="runtime.js"></script><script defer="defer" src="vendor-fullcalendar.js"></script><script defer="defer" src="vendor-chartjs.js"></script><script defer="defer" src="vendors.js"></script><script defer="defer" src="2026.js"></script><link href="style.css" rel="stylesheet"></head>
<body data-active="autores" data-crumbs="Revista | Autores">
<div class="shell">
  <div data-shell-sidebar></div>
  <div class="main">
    <div data-shell-topbar></div>
    <main class="content">
      <section class="hero">
        <div class="hero-text">
          <span class="eyebrow">Contenido</span>
          <h1 class="hero-title">Autores</h1>
          <p class="hero-sub">Personas o firmas que aparecen como autoras de los reportajes (tabla <code>autores</code>).</p>
        </div>
        <div class="hero-actions">
          <a class="btn btn--primary" href="autor_form.php"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Nuevo autor</a>
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
          <div class="card-head"><div class="card-title-wrap"><h2 class="card-title">Todos los autores (<?= count($autores) ?>)</h2></div></div>
          <table class="table">
            <thead><tr><th>Nombre</th><th>Se muestra como</th><th>Reportajes</th><th style="text-align:right">Acciones</th></tr></thead>
            <tbody>
              <?php if (!$autores): ?>
                <tr><td colspan="4" style="text-align:center;color:var(--t-muted);padding:24px 0">No hay autores todavía.</td></tr>
              <?php else: foreach ($autores as $a): ?>
                <tr>
                  <td class="cell-name"><?= htmlspecialchars(trim($a['nombres'] . ' ' . ($a['ap_paterno'] ?? '') . ' ' . ($a['ap_materno'] ?? ''))) ?></td>
                  <td><?php if ($a['es_nickname']): ?><?= htmlspecialchars($a['nickname']) ?> <span class="tag t-new">apodo</span><?php else: ?><span style="color:var(--t-muted)">nombre real</span><?php endif; ?></td>
                  <td><?= (int) $a['total_reportajes'] ?></td>
                  <td style="text-align:right;white-space:nowrap">
                    <a class="btn btn--ghost" style="padding:4px 10px;font-size:12px" href="autor_form.php?id=<?= (int)$a['id'] ?>">Editar</a>
                    <a class="btn btn--ghost" style="padding:4px 10px;font-size:12px;color:#b3261e" href="autor_delete.php?id=<?= (int)$a['id'] ?>" onclick="return confirm('¿Eliminar este autor?');">Eliminar</a>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
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
