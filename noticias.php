<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';

$mensaje = '';
if (isset($_GET['ok']) && $_GET['ok'] === 'creado') $mensaje = 'Noticia creada correctamente.';
if (isset($_GET['ok']) && $_GET['ok'] === 'editado') $mensaje = 'Noticia actualizada correctamente.';
if (isset($_GET['ok']) && $_GET['ok'] === 'eliminado') $mensaje = 'Noticia eliminada.';

$noticias = $pdo->query(
    'SELECT n.id, n.titulo, n.foto, n.link_externo, n.fecha_publicacion, u.nombres AS publicado_por
     FROM noticias n LEFT JOIN usuarios u ON u.id = n.usuario_id
     ORDER BY n.fecha_publicacion DESC'
)->fetchAll();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Noticias · Revista Digital NTEP</title><script>!function(){try{var t=localStorage.getItem("dash26-theme"),e=window.matchMedia("(prefers-color-scheme: dark)").matches;document.documentElement.setAttribute("data-theme",t||(e?"dark":"light"))}catch(t){document.documentElement.setAttribute("data-theme","light")}}()</script><script defer="defer" src="runtime.js"></script><script defer="defer" src="vendor-fullcalendar.js"></script><script defer="defer" src="vendor-chartjs.js"></script><script defer="defer" src="vendors.js"></script><script defer="defer" src="2026.js"></script><link href="style.css" rel="stylesheet"></head>
<body data-active="noticias" data-crumbs="Revista | Noticias">
<div class="shell">
  <div data-shell-sidebar></div>
  <div class="main">
    <div data-shell-topbar></div>
    <main class="content">
      <section class="hero">
        <div class="hero-text">
          <span class="eyebrow">Contenido</span>
          <h1 class="hero-title">Noticias</h1>
          <p class="hero-sub">Noticias rápidas con enlace externo (tabla <code>noticias</code>).</p>
        </div>
        <div class="hero-actions">
          <a class="btn btn--primary" href="noticia_form.php"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Nueva noticia</a>
        </div>
      </section>

      <?php if ($mensaje): ?>
        <div style="background:#e6f7ee;color:#1a7f4e;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13.5px;"><?= htmlspecialchars($mensaje) ?></div>
      <?php endif; ?>

      <div class="grid">
        <section class="col-12 card">
          <div class="card-head"><div class="card-title-wrap"><h2 class="card-title">Todas las noticias (<?= count($noticias) ?>)</h2></div></div>
          <table class="table">
            <thead><tr><th>Título</th><th>Enlace</th><th>Publicado por</th><th>Fecha</th><th style="text-align:right">Acciones</th></tr></thead>
            <tbody>
              <?php if (!$noticias): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--t-muted);padding:24px 0">No hay noticias todavía.</td></tr>
              <?php else: foreach ($noticias as $n): ?>
                <tr>
                  <td class="cell-name">
                    <div style="display:flex;align-items:center;gap:10px">
                      <?php if (!empty($n['foto'])): ?>
                        <img src="<?= htmlspecialchars($n['foto']) ?>" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:8px;flex:none">
                      <?php else: ?>
                        <div style="width:40px;height:40px;border-radius:8px;background:var(--bg-muted);flex:none"></div>
                      <?php endif; ?>
                      <?= htmlspecialchars($n['titulo']) ?>
                    </div>
                  </td>
                  <td><?php if ($n['link_externo']): ?><a href="<?= htmlspecialchars($n['link_externo']) ?>" target="_blank" rel="noopener">Ver enlace</a><?php endif; ?></td>
                  <td><?= htmlspecialchars($n['publicado_por'] ?? '—') ?></td>
                  <td class="cell-date"><?= htmlspecialchars(date('d M Y', strtotime($n['fecha_publicacion']))) ?></td>
                  <td style="text-align:right;white-space:nowrap">
                    <a class="btn btn--ghost" style="padding:4px 10px;font-size:12px" href="noticia_form.php?id=<?= (int)$n['id'] ?>">Editar</a>
                    <a class="btn btn--ghost" style="padding:4px 10px;font-size:12px;color:#b3261e" href="noticia_delete.php?id=<?= (int)$n['id'] ?>" onclick="return confirm('¿Eliminar esta noticia?');">Eliminar</a>
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
</body></html>
