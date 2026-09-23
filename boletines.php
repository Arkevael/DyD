<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';

$mensaje = '';
if (isset($_GET['ok']) && $_GET['ok'] === 'creado') $mensaje = 'Boletín creado correctamente.';
if (isset($_GET['ok']) && $_GET['ok'] === 'editado') $mensaje = 'Boletín actualizado correctamente.';
if (isset($_GET['ok']) && $_GET['ok'] === 'eliminado') $mensaje = 'Boletín eliminado.';

$boletines = $pdo->query(
    'SELECT b.*, u.nombres AS publicado_por FROM boletines b LEFT JOIN usuarios u ON u.id = b.usuario_id ORDER BY b.fecha_publicacion DESC'
)->fetchAll();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Boletines · Revista Digital NTEP</title><script>!function(){try{var t=localStorage.getItem("dash26-theme"),e=window.matchMedia("(prefers-color-scheme: dark)").matches;document.documentElement.setAttribute("data-theme",t||(e?"dark":"light"))}catch(t){document.documentElement.setAttribute("data-theme","light")}}()</script><script defer="defer" src="runtime.js"></script><script defer="defer" src="vendor-fullcalendar.js"></script><script defer="defer" src="vendor-chartjs.js"></script><script defer="defer" src="vendors.js"></script><script defer="defer" src="2026.js"></script><link href="style.css" rel="stylesheet"></head>
<body data-active="boletines" data-crumbs="Revista | Boletines">
<div class="shell">
  <div data-shell-sidebar></div>
  <div class="main">
    <div data-shell-topbar></div>
    <main class="content">
      <section class="hero">
        <div class="hero-text">
          <span class="eyebrow">Contenido</span>
          <h1 class="hero-title">Boletines</h1>
          <p class="hero-sub">Boletines mensuales con portada y PDF adjunto (tabla <code>boletines</code>).</p>
        </div>
        <div class="hero-actions">
          <a class="btn btn--primary" href="boletin_form.php"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Nuevo boletín</a>
        </div>
      </section>

      <?php if ($mensaje): ?>
        <div style="background:#e6f7ee;color:#1a7f4e;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13.5px;"><?= htmlspecialchars($mensaje) ?></div>
      <?php endif; ?>

      <div class="grid">
        <section class="col-12 card">
          <div class="card-head"><div class="card-title-wrap"><h2 class="card-title">Todos los boletines (<?= count($boletines) ?>)</h2></div></div>
          <table class="table">
            <thead><tr><th>Portada</th><th>N.° boletín</th><th>Publicado por</th><th>Fecha</th><th>PDF</th><th style="text-align:right">Acciones</th></tr></thead>
            <tbody>
              <?php if (!$boletines): ?>
                <tr><td colspan="6" style="text-align:center;color:var(--t-muted);padding:24px 0">No hay boletines todavía.</td></tr>
              <?php else: foreach ($boletines as $b): ?>
                <tr>
                  <td><?php if (!empty($b['foto_portada'])): ?><img src="<?= htmlspecialchars($b['foto_portada']) ?>" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:8px"><?php else: ?><div style="width:40px;height:40px;border-radius:8px;background:var(--bg-muted)"></div><?php endif; ?></td>
                  <td class="cell-name"><?= htmlspecialchars($b['numero_boletin']) ?><br><span style="color:var(--t-muted);font-size:12px"><?= htmlspecialchars($b['resumen'] ?? '') ?></span></td>
                  <td><?= htmlspecialchars($b['publicado_por'] ?? '—') ?></td>
                  <td class="cell-date"><?= htmlspecialchars(date('d M Y', strtotime($b['fecha_publicacion']))) ?></td>
                  <td><?php if (!empty($b['archivo_pdf'])): ?><a href="<?= htmlspecialchars($b['archivo_pdf']) ?>" target="_blank">Ver PDF</a><?php else: ?><span style="color:var(--t-muted)">—</span><?php endif; ?></td>
                  <td style="text-align:right;white-space:nowrap">
                    <a class="btn btn--ghost" style="padding:4px 10px;font-size:12px" href="boletin_form.php?id=<?= (int)$b['id'] ?>">Editar</a>
                    <a class="btn btn--ghost" style="padding:4px 10px;font-size:12px;color:#b3261e" href="boletin_delete.php?id=<?= (int)$b['id'] ?>" onclick="return confirm('¿Eliminar este boletín?');">Eliminar</a>
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
