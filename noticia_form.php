<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/upload.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$esEdicion = $id > 0;
$error = '';

$noticia = ['titulo' => '', 'link_externo' => '', 'fecha_publicacion' => date('Y-m-d'), 'foto' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noticia['titulo']            = trim($_POST['titulo'] ?? '');
    $noticia['link_externo']      = trim($_POST['link_externo'] ?? '');
    $noticia['fecha_publicacion'] = $_POST['fecha_publicacion'] ?? date('Y-m-d');

    if ($noticia['titulo'] === '') {
        $error = 'El título es obligatorio.';
    } else {
        try {
            $nuevaFoto = subirArchivo('foto', 'noticias', ['jpg', 'jpeg', 'png', 'webp']);

            if ($esEdicion) {
                $actual = $pdo->prepare('SELECT foto FROM noticias WHERE id = ?');
                $actual->execute([$id]);
                $existente = $actual->fetch();
                $fotoFinal = $nuevaFoto ?? $existente['foto'];
                if ($nuevaFoto) borrarArchivo($existente['foto']);

                $stmt = $pdo->prepare('UPDATE noticias SET titulo=?, foto=?, link_externo=?, fecha_publicacion=? WHERE id=?');
                $stmt->execute([$noticia['titulo'], $fotoFinal, $noticia['link_externo'] ?: null, $noticia['fecha_publicacion'], $id]);
                header('Location: noticias.php?ok=editado');
                exit;
            } else {
                $stmt = $pdo->prepare('INSERT INTO noticias (titulo, foto, link_externo, fecha_publicacion, usuario_id) VALUES (?,?,?,?,?)');
                $stmt->execute([$noticia['titulo'], $nuevaFoto, $noticia['link_externo'] ?: null, $noticia['fecha_publicacion'], $usuarioActual['id']]);
                header('Location: noticias.php?ok=creado');
                exit;
            }
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        }
    }
} elseif ($esEdicion) {
    $stmt = $pdo->prepare('SELECT * FROM noticias WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) $noticia = $found;
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $esEdicion ? 'Editar' : 'Nueva' ?> noticia · Revista Digital NTEP</title><script>!function(){try{var t=localStorage.getItem("dash26-theme"),e=window.matchMedia("(prefers-color-scheme: dark)").matches;document.documentElement.setAttribute("data-theme",t||(e?"dark":"light"))}catch(t){document.documentElement.setAttribute("data-theme","light")}}()</script><script defer="defer" src="runtime.js"></script><script defer="defer" src="vendor-fullcalendar.js"></script><script defer="defer" src="vendor-chartjs.js"></script><script defer="defer" src="vendors.js"></script><script defer="defer" src="2026.js"></script><link href="style.css" rel="stylesheet"></head>
<body data-active="noticias" data-crumbs="Revista | Noticias | <?= $esEdicion ? 'Editar' : 'Nueva' ?>">
<div class="shell">
  <div data-shell-sidebar></div>
  <div class="main">
    <div data-shell-topbar></div>
    <main class="content">
      <section class="hero">
        <div class="hero-text">
          <span class="eyebrow">Contenido</span>
          <h1 class="hero-title"><?= $esEdicion ? 'Editar noticia' : 'Nueva noticia' ?></h1>
        </div>
      </section>

      <?php if ($error): ?>
        <div style="background:#fdecea;color:#b3261e;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13.5px;"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="grid">
        <section class="col-12 card">
          <form method="post" action="noticia_form.php" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:16px;max-width:600px">
            <?php if ($esEdicion): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>

            <div class="field">
              <label class="field-label" for="titulo">Título</label>
              <input class="input" id="titulo" name="titulo" type="text" required value="<?= htmlspecialchars($noticia['titulo']) ?>">
            </div>

            <div class="field">
              <label class="field-label" for="link_externo">Enlace externo</label>
              <input class="input" id="link_externo" name="link_externo" type="url" placeholder="https://..." value="<?= htmlspecialchars($noticia['link_externo'] ?? '') ?>">
            </div>

            <div style="display:flex;gap:16px;flex-wrap:wrap">
              <div class="field" style="flex:1;min-width:200px">
                <label class="field-label" for="fecha_publicacion">Fecha de publicación</label>
                <input class="input" id="fecha_publicacion" name="fecha_publicacion" type="date" required value="<?= htmlspecialchars(substr($noticia['fecha_publicacion'], 0, 10)) ?>">
              </div>
              <div class="field" style="flex:1;min-width:220px">
                <label class="field-label" for="foto">Foto</label>
                <?php if (!empty($noticia['foto'])): ?>
                  <div style="margin-bottom:6px"><img src="<?= htmlspecialchars($noticia['foto']) ?>" alt="" style="max-width:140px;border-radius:8px;display:block"></div>
                <?php endif; ?>
                <input class="input" id="foto" name="foto" type="file" accept="image/png,image/jpeg,image/webp">
              </div>
            </div>

            <div style="display:flex;gap:10px">
              <button class="btn btn--primary" type="submit"><?= $esEdicion ? 'Guardar cambios' : 'Crear noticia' ?></button>
              <a class="btn btn--ghost" href="noticias.php">Cancelar</a>
            </div>
          </form>
        </section>
      </div>
    </main>
    <div data-shell-footer></div>
  </div>
</div>
</body></html>
