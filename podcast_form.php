<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/embed_helper.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$esEdicion = $id > 0;
$error = '';
$podcast = ['titulo' => '', 'url_embed' => '', 'fecha_publicacion' => date('Y-m-d')];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $podcast['titulo']            = trim($_POST['titulo'] ?? '');
    $podcast['url_embed']         = normalizarUrlEmbed(trim($_POST['url_embed'] ?? ''));
    $podcast['fecha_publicacion'] = $_POST['fecha_publicacion'] ?? date('Y-m-d');

    if ($podcast['titulo'] === '' || $podcast['url_embed'] === '') {
        $error = 'El título y el enlace embebido son obligatorios.';
    } else {
        if ($esEdicion) {
            $stmt = $pdo->prepare('UPDATE podcasts SET titulo=?, url_embed=?, fecha_publicacion=? WHERE id=?');
            $stmt->execute([$podcast['titulo'], $podcast['url_embed'], $podcast['fecha_publicacion'], $id]);
            header('Location: podcasts.php?ok=editado');
            exit;
        } else {
            $stmt = $pdo->prepare('INSERT INTO podcasts (titulo, url_embed, fecha_publicacion, usuario_id) VALUES (?,?,?,?)');
            $stmt->execute([$podcast['titulo'], $podcast['url_embed'], $podcast['fecha_publicacion'], $usuarioActual['id']]);
            header('Location: podcasts.php?ok=creado');
            exit;
        }
    }
} elseif ($esEdicion) {
    $stmt = $pdo->prepare('SELECT * FROM podcasts WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) $podcast = $found;
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $esEdicion ? 'Editar' : 'Nuevo' ?> podcast · Revista Digital NTEP</title><script>!function(){try{var t=localStorage.getItem("dash26-theme"),e=window.matchMedia("(prefers-color-scheme: dark)").matches;document.documentElement.setAttribute("data-theme",t||(e?"dark":"light"))}catch(t){document.documentElement.setAttribute("data-theme","light")}}()</script><script defer="defer" src="runtime.js"></script><script defer="defer" src="vendor-fullcalendar.js"></script><script defer="defer" src="vendor-chartjs.js"></script><script defer="defer" src="vendors.js"></script><script defer="defer" src="2026.js"></script><link href="style.css" rel="stylesheet"></head>
<body data-active="podcasts" data-crumbs="Revista | Podcasts | <?= $esEdicion ? 'Editar' : 'Nuevo' ?>">
<div class="shell">
  <div data-shell-sidebar></div>
  <div class="main">
    <div data-shell-topbar></div>
    <main class="content">
      <section class="hero">
        <div class="hero-text">
          <span class="eyebrow">Contenido</span>
          <h1 class="hero-title"><?= $esEdicion ? 'Editar podcast' : 'Nuevo podcast' ?></h1>
        </div>
      </section>

      <?php if ($error): ?>
        <div style="background:#fdecea;color:#b3261e;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13.5px;"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="grid">
        <section class="col-12 card">
          <form method="post" action="podcast_form.php" style="display:flex;flex-direction:column;gap:16px;max-width:600px">
            <?php if ($esEdicion): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>

            <div class="field">
              <label class="field-label" for="titulo">Título</label>
              <input class="input" id="titulo" name="titulo" type="text" required value="<?= htmlspecialchars($podcast['titulo']) ?>">
            </div>

            <div class="field">
              <label class="field-label" for="url_embed">Link de Spotify o YouTube</label>
              <input class="input" id="url_embed" name="url_embed" type="url" placeholder="https://open.spotify.com/episode/..." required value="<?= htmlspecialchars($podcast['url_embed']) ?>">
              <small style="color:var(--t-muted)">Pega el link normal (compartir); se convierte al formato embed automáticamente.</small>
            </div>

            <div class="field">
              <label class="field-label" for="fecha_publicacion">Fecha de publicación</label>
              <input class="input" id="fecha_publicacion" name="fecha_publicacion" type="date" required value="<?= htmlspecialchars(substr($podcast['fecha_publicacion'], 0, 10)) ?>">
            </div>

            <div style="display:flex;gap:10px">
              <button class="btn btn--primary" type="submit"><?= $esEdicion ? 'Guardar cambios' : 'Crear podcast' ?></button>
              <a class="btn btn--ghost" href="podcasts.php">Cancelar</a>
            </div>
          </form>
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
