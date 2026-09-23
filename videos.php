<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';

$mensaje = '';
if (isset($_GET['ok']) && $_GET['ok'] === 'creado') $mensaje = 'Video creado correctamente.';
if (isset($_GET['ok']) && $_GET['ok'] === 'editado') $mensaje = 'Video actualizado correctamente.';
if (isset($_GET['ok']) && $_GET['ok'] === 'eliminado') $mensaje = 'Video eliminado.';

$videos = $pdo->query(
    'SELECT t.*, u.nombres AS publicado_por FROM videos t LEFT JOIN usuarios u ON u.id = t.usuario_id ORDER BY t.fecha_publicacion DESC'
)->fetchAll();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Videos · Revista Digital NTEP</title><script>!function(){try{var t=localStorage.getItem("dash26-theme"),e=window.matchMedia("(prefers-color-scheme: dark)").matches;document.documentElement.setAttribute("data-theme",t||(e?"dark":"light"))}catch(t){document.documentElement.setAttribute("data-theme","light")}}()</script><script defer="defer" src="runtime.js"></script><script defer="defer" src="vendor-fullcalendar.js"></script><script defer="defer" src="vendor-chartjs.js"></script><script defer="defer" src="vendors.js"></script><script defer="defer" src="2026.js"></script><link href="style.css" rel="stylesheet"></head>
<body data-active="videos" data-crumbs="Revista | Videos">
<div class="shell">
  <div data-shell-sidebar></div>
  <div class="main">
    <div data-shell-topbar></div>
    <main class="content">
      <section class="hero">
        <div class="hero-text">
          <span class="eyebrow">Contenido</span>
          <h1 class="hero-title">Videos</h1>
          <p class="hero-sub">Enlaces embebidos (URL embed (YouTube)) conectados a la tabla <code>videos</code>.</p>
        </div>
        <div class="hero-actions">
          <a class="btn btn--primary" href="video_form.php"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Nuevo video</a>
        </div>
      </section>

      <?php if ($mensaje): ?>
        <div style="background:#e6f7ee;color:#1a7f4e;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13.5px;"><?= htmlspecialchars($mensaje) ?></div>
      <?php endif; ?>

      <div class="grid">
        <section class="col-12 card">
          <div class="card-head"><div class="card-title-wrap"><h2 class="card-title">Todos (<?= count($videos) ?>)</h2></div></div>
          <table class="table">
            <thead><tr><th>Título</th><th>Enlace embebido</th><th>Publicado por</th><th>Fecha</th><th style="text-align:right">Acciones</th></tr></thead>
            <tbody>
              <?php if (!$videos): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--t-muted);padding:24px 0">No hay videos todavía.</td></tr>
              <?php else: foreach ($videos as $x): ?>
                <tr>
                  <td class="cell-name"><?= htmlspecialchars($x['titulo']) ?></td>
                  <td><a href="<?= htmlspecialchars($x['url_embed']) ?>" target="_blank" rel="noopener">Ver enlace</a></td>
                  <td><?= htmlspecialchars($x['publicado_por'] ?? '—') ?></td>
                  <td class="cell-date"><?= htmlspecialchars(date('d M Y', strtotime($x['fecha_publicacion']))) ?></td>
                  <td style="text-align:right;white-space:nowrap">
                    <a class="btn btn--ghost" style="padding:4px 10px;font-size:12px" href="video_form.php?id=<?= (int)$x['id'] ?>">Editar</a>
                    <a class="btn btn--ghost" style="padding:4px 10px;font-size:12px;color:#b3261e" href="video_delete.php?id=<?= (int)$x['id'] ?>" onclick="return confirm('¿Eliminar este registro?');">Eliminar</a>
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
