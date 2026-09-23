<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/upload.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$esEdicion = $id > 0;
$error = '';

$reportaje = [
    'titulo' => '', 'resumen_corto' => '', 'desarrollo' => '',
    'fecha_publicacion' => date('Y-m-d'), 'es_destacado' => 0, 'autor_id' => '',
    'foto_principal' => null, 'pdf_adjunto' => null,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportaje = [
        'titulo'            => trim($_POST['titulo'] ?? ''),
        'resumen_corto'     => trim($_POST['resumen_corto'] ?? ''),
        'desarrollo'        => trim($_POST['desarrollo'] ?? ''),
        'fecha_publicacion' => $_POST['fecha_publicacion'] ?? date('Y-m-d'),
        'es_destacado'      => isset($_POST['es_destacado']) ? 1 : 0,
        'autor_id'          => $_POST['autor_id'] !== '' ? (int) $_POST['autor_id'] : null,
    ];

    if ($reportaje['titulo'] === '' || $reportaje['desarrollo'] === '') {
        $error = 'El título y el desarrollo son obligatorios.';
    } elseif (!$reportaje['autor_id']) {
        $error = 'Selecciona un autor (usa "Redacción" si no tiene autor asignado).';
    } else {
        try {
            $nuevaFoto = subirArchivo('foto_principal', 'reportajes', ['jpg', 'jpeg', 'png', 'webp']);
            $nuevoPdf  = subirArchivo('pdf_adjunto', 'reportajes', ['pdf']);

            if ($esEdicion) {
                $actual = $pdo->prepare('SELECT foto_principal, pdf_adjunto FROM reportajes WHERE id = ?');
                $actual->execute([$id]);
                $existente = $actual->fetch();

                $fotoFinal = $nuevaFoto ?? $existente['foto_principal'];
                $pdfFinal  = $nuevoPdf ?? $existente['pdf_adjunto'];
                if ($nuevaFoto) borrarArchivo($existente['foto_principal']);
                if ($nuevoPdf) borrarArchivo($existente['pdf_adjunto']);

                $stmt = $pdo->prepare(
                    'UPDATE reportajes SET titulo=?, resumen_corto=?, desarrollo=?, fecha_publicacion=?, es_destacado=?, autor_id=?, foto_principal=?, pdf_adjunto=? WHERE id=?'
                );
                $stmt->execute([
                    $reportaje['titulo'], $reportaje['resumen_corto'], $reportaje['desarrollo'],
                    $reportaje['fecha_publicacion'], $reportaje['es_destacado'], $reportaje['autor_id'],
                    $fotoFinal, $pdfFinal, $id,
                ]);
                header('Location: reportajes.php?ok=editado');
                exit;
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO reportajes (titulo, resumen_corto, desarrollo, fecha_publicacion, es_destacado, autor_id, usuario_id, foto_principal, pdf_adjunto) VALUES (?,?,?,?,?,?,?,?,?)'
                );
                $stmt->execute([
                    $reportaje['titulo'], $reportaje['resumen_corto'], $reportaje['desarrollo'],
                    $reportaje['fecha_publicacion'], $reportaje['es_destacado'], $reportaje['autor_id'], $usuarioActual['id'],
                    $nuevaFoto, $nuevoPdf,
                ]);
                header('Location: reportajes.php?ok=creado');
                exit;
            }
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        }
    }
} elseif ($esEdicion) {
    $stmt = $pdo->prepare('SELECT * FROM reportajes WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) $reportaje = $found;
}

$autores = $pdo->query('SELECT id, nombres, ap_paterno, nickname, es_nickname FROM autores ORDER BY nombres')->fetchAll();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $esEdicion ? 'Editar' : 'Nuevo' ?> reportaje · Revista Digital NTEP</title><script>!function(){try{var t=localStorage.getItem("dash26-theme"),e=window.matchMedia("(prefers-color-scheme: dark)").matches;document.documentElement.setAttribute("data-theme",t||(e?"dark":"light"))}catch(t){document.documentElement.setAttribute("data-theme","light")}}()</script><script defer="defer" src="runtime.js"></script><script defer="defer" src="vendor-fullcalendar.js"></script><script defer="defer" src="vendor-chartjs.js"></script><script defer="defer" src="vendors.js"></script><script defer="defer" src="2026.js"></script><link href="style.css" rel="stylesheet"></head>
<body data-active="reportajes" data-crumbs="Revista | Reportajes | <?= $esEdicion ? 'Editar' : 'Nuevo' ?>">
<div class="shell">
  <div data-shell-sidebar></div>
  <div class="main">
    <div data-shell-topbar></div>
    <main class="content">
      <section class="hero">
        <div class="hero-text">
          <span class="eyebrow">Contenido</span>
          <h1 class="hero-title"><?= $esEdicion ? 'Editar reportaje' : 'Nuevo reportaje' ?></h1>
          <p class="hero-sub">Los cambios se guardan directamente en la tabla <code>reportajes</code>.</p>
        </div>
      </section>

      <?php if ($error): ?>
        <div style="background:#fdecea;color:#b3261e;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13.5px;"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="grid">
        <section class="col-12 card">
          <form method="post" action="reportaje_form.php" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:16px;max-width:720px">
            <?php if ($esEdicion): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>

            <div class="field">
              <label class="field-label" for="titulo">Título</label>
              <input class="input" id="titulo" name="titulo" type="text" required value="<?= htmlspecialchars($reportaje['titulo']) ?>">
            </div>

            <div class="field">
              <label class="field-label" for="resumen_corto">Resumen corto</label>
              <input class="input" id="resumen_corto" name="resumen_corto" type="text" maxlength="500" value="<?= htmlspecialchars($reportaje['resumen_corto'] ?? '') ?>">
            </div>

            <div class="field">
              <label class="field-label" for="desarrollo">Desarrollo</label>
              <textarea class="input" id="desarrollo" name="desarrollo" rows="8" required><?= htmlspecialchars($reportaje['desarrollo']) ?></textarea>
            </div>

            <div style="display:flex;gap:16px;flex-wrap:wrap">
              <div class="field" style="flex:1;min-width:200px">
                <label class="field-label" for="fecha_publicacion">Fecha de publicación</label>
                <input class="input" id="fecha_publicacion" name="fecha_publicacion" type="date" required value="<?= htmlspecialchars(substr($reportaje['fecha_publicacion'], 0, 10)) ?>">
              </div>
              <div class="field" style="flex:1;min-width:200px">
                <label class="field-label" for="autor_id">Autor</label>
                <select class="select" id="autor_id" name="autor_id" required>
                  <option value="">— Selecciona —</option>
                  <?php foreach ($autores as $a): $nombre = $a['es_nickname'] && $a['nickname'] ? $a['nickname'] : trim($a['nombres'] . ' ' . $a['ap_paterno']); ?>
                    <option value="<?= (int) $a['id'] ?>" <?= (isset($reportaje['autor_id']) && (int)$reportaje['autor_id'] === (int)$a['id']) ? 'selected' : '' ?>><?= htmlspecialchars($nombre) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div style="display:flex;gap:16px;flex-wrap:wrap">
              <div class="field" style="flex:1;min-width:220px">
                <label class="field-label" for="foto_principal">Foto principal (jpg, png, webp)</label>
                <?php if (!empty($reportaje['foto_principal'])): ?>
                  <div style="margin-bottom:6px"><img src="<?= htmlspecialchars($reportaje['foto_principal']) ?>" alt="" style="max-width:160px;border-radius:8px;display:block"></div>
                <?php endif; ?>
                <input class="input" id="foto_principal" name="foto_principal" type="file" accept="image/png,image/jpeg,image/webp">
              </div>
              <div class="field" style="flex:1;min-width:220px">
                <label class="field-label" for="pdf_adjunto">PDF adjunto (opcional)</label>
                <?php if (!empty($reportaje['pdf_adjunto'])): ?>
                  <div style="margin-bottom:6px;font-size:12.5px"><a href="<?= htmlspecialchars($reportaje['pdf_adjunto']) ?>" target="_blank">Ver PDF actual</a></div>
                <?php endif; ?>
                <input class="input" id="pdf_adjunto" name="pdf_adjunto" type="file" accept="application/pdf">
              </div>
            </div>

            <label class="check">
              <input type="checkbox" name="es_destacado" <?= !empty($reportaje['es_destacado']) ? 'checked' : '' ?>>
              <span class="box"></span> Marcar como destacado
            </label>

            <div style="display:flex;gap:10px">
              <button class="btn btn--primary" type="submit"><?= $esEdicion ? 'Guardar cambios' : 'Crear reportaje' ?></button>
              <a class="btn btn--ghost" href="reportajes.php">Cancelar</a>
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
