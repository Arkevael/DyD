<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';

$mensaje = '';
if (isset($_GET['ok']) && $_GET['ok'] === 'creado') $mensaje = 'Reportaje creado correctamente.';
if (isset($_GET['ok']) && $_GET['ok'] === 'editado') $mensaje = 'Reportaje actualizado correctamente.';
if (isset($_GET['ok']) && $_GET['ok'] === 'eliminado') $mensaje = 'Reportaje eliminado.';

$reportajes = $pdo->query(
    'SELECT r.id, r.titulo, r.resumen_corto, r.fecha_publicacion, r.es_destacado, r.foto_principal,
            COALESCE(a.nickname, CONCAT(a.nombres, " ", COALESCE(a.ap_paterno, ""))) AS autor,
            u.nombres AS publicado_por
     FROM reportajes r
     LEFT JOIN autores a ON a.id = r.autor_id
     LEFT JOIN usuarios u ON u.id = r.usuario_id
     ORDER BY r.fecha_publicacion DESC'
)->fetchAll();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Reportajes · Revista Digital NTEP</title><script>!function(){try{var t=localStorage.getItem("dash26-theme"),e=window.matchMedia("(prefers-color-scheme: dark)").matches;document.documentElement.setAttribute("data-theme",t||(e?"dark":"light"))}catch(t){document.documentElement.setAttribute("data-theme","light")}}()</script><script defer="defer" src="runtime.js"></script><script defer="defer" src="vendor-fullcalendar.js"></script><script defer="defer" src="vendor-chartjs.js"></script><script defer="defer" src="vendors.js"></script><script defer="defer" src="2026.js"></script><link href="style.css" rel="stylesheet"></head>
<body data-active="reportajes" data-crumbs="Revista | Reportajes">
<div class="shell">
  <div data-shell-sidebar></div>
  <div class="main">
    <div data-shell-topbar></div>
    <main class="content">
      <section class="hero">
        <div class="hero-text">
          <span class="eyebrow">Contenido</span>
          <h1 class="hero-title">Reportajes</h1>
          <p class="hero-sub">Gestiona los reportajes publicados en la tabla <code>reportajes</code> de la base de datos.</p>
        </div>
        <div class="hero-actions">
          <a class="btn btn--primary" href="reportaje_form.php"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Nuevo reportaje</a>
        </div>
      </section>

      <?php if ($mensaje): ?>
        <div style="background:#e6f7ee;color:#1a7f4e;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13.5px;"><?= htmlspecialchars($mensaje) ?></div>
      <?php endif; ?>

      <div class="grid">
        <section class="col-12 card">
          <div class="card-head"><div class="card-title-wrap"><h2 class="card-title">Todos los reportajes (<?= count($reportajes) ?>)</h2></div></div>
          <table class="table">
            <thead>
              <tr>
                <th>Título</th>
                <th>Autor</th>
                <th>Publicado por</th>
                <th>Fecha</th>
                <th>Destacado</th>
                <th style="text-align:right">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$reportajes): ?>
                <tr><td colspan="6" style="text-align:center;color:var(--t-muted);padding:24px 0">No hay reportajes todavía. Crea el primero.</td></tr>
              <?php else: foreach ($reportajes as $r): ?>
                <tr>
                  <td class="cell-name">
                    <div style="display:flex;align-items:center;gap:10px">
                      <?php if (!empty($r['foto_principal'])): ?>
                        <img src="<?= htmlspecialchars($r['foto_principal']) ?>" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:8px;flex:none">
                      <?php else: ?>
                        <div style="width:40px;height:40px;border-radius:8px;background:var(--bg-muted);flex:none"></div>
                      <?php endif; ?>
                      <div><?= htmlspecialchars($r['titulo']) ?><br><span style="color:var(--t-muted);font-size:12px"><?= htmlspecialchars($r['resumen_corto'] ?? '') ?></span></div>
                    </div>
                  </td>
                  <td><?= htmlspecialchars(trim($r['autor']) ?: 'Sin autor') ?></td>
                  <td><?= htmlspecialchars($r['publicado_por'] ?? '—') ?></td>
                  <td class="cell-date"><?= htmlspecialchars(date('d M Y', strtotime($r['fecha_publicacion']))) ?></td>
                  <td><?php if ($r['es_destacado']): ?><span class="tag t-new">Sí</span><?php else: ?><span class="tag t-used">No</span><?php endif; ?></td>
                  <td style="text-align:right;white-space:nowrap">
                    <a class="btn btn--ghost" style="padding:4px 10px;font-size:12px" href="reportaje_form.php?id=<?= (int)$r['id'] ?>">Editar</a>
                    <a class="btn btn--ghost" style="padding:4px 10px;font-size:12px;color:#b3261e" href="reportaje_delete.php?id=<?= (int)$r['id'] ?>" onclick="return confirm('¿Eliminar este reportaje?');">Eliminar</a>
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
