<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/upload.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$esEdicion = $id > 0;
$error = '';
$boletin = ['numero_boletin' => '', 'resumen' => '', 'fecha_publicacion' => date('Y-m-d'), 'foto_portada' => null, 'archivo_pdf' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $boletin['numero_boletin']    = trim($_POST['numero_boletin'] ?? '');
    $boletin['resumen']           = trim($_POST['resumen'] ?? '');
    $boletin['fecha_publicacion'] = $_POST['fecha_publicacion'] ?? date('Y-m-d');

    if ($boletin['numero_boletin'] === '') {
        $error = 'El número de boletín es obligatorio.';
    } else {
        try {
            $nuevaPortada = subirArchivo('foto_portada', 'boletines', ['jpg', 'jpeg', 'png', 'webp']);
            $nuevoPdf     = subirArchivo('archivo_pdf', 'pdfs', ['pdf']);

            if ($esEdicion) {
                $actual = $pdo->prepare('SELECT foto_portada, archivo_pdf FROM boletines WHERE id = ?');
                $actual->execute([$id]);
                $existente = $actual->fetch();

                $portadaFinal = $nuevaPortada ?? $existente['foto_portada'];
                $pdfFinal     = $nuevoPdf ?? $existente['archivo_pdf'];
                if (!$pdfFinal) {
                    $error = 'Debes adjuntar el PDF del boletín (obligatorio).';
                } else {
                    if ($nuevaPortada) borrarArchivo($existente['foto_portada']);
                    if ($nuevoPdf) borrarArchivo($existente['archivo_pdf']);

                    $stmt = $pdo->prepare('UPDATE boletines SET numero_boletin=?, resumen=?, foto_portada=?, archivo_pdf=?, fecha_publicacion=? WHERE id=?');
                    $stmt->execute([$boletin['numero_boletin'], $boletin['resumen'] ?: null, $portadaFinal, $pdfFinal, $boletin['fecha_publicacion'], $id]);
                    header('Location: boletines.php?ok=editado');
                    exit;
                }
            } else {
                if (!$nuevoPdf) {
                    $error = 'Debes adjuntar el PDF del boletín (obligatorio).';
                } else {
                    $stmt = $pdo->prepare('INSERT INTO boletines (numero_boletin, resumen, foto_portada, archivo_pdf, fecha_publicacion, usuario_id) VALUES (?,?,?,?,?,?)');
                    $stmt->execute([$boletin['numero_boletin'], $boletin['resumen'] ?: null, $nuevaPortada, $nuevoPdf, $boletin['fecha_publicacion'], $usuarioActual['id']]);
                    header('Location: boletines.php?ok=creado');
                    exit;
                }
            }
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        } catch (PDOException $e) {
            $error = str_contains($e->getMessage(), 'uq_boletines_numero')
                ? 'Ya existe un boletín con ese número.'
                : 'Error al guardar: ' . $e->getMessage();
        }
    }
} elseif ($esEdicion) {
    $stmt = $pdo->prepare('SELECT * FROM boletines WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) $boletin = $found;
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $esEdicion ? 'Editar' : 'Nuevo' ?> boletín · Revista Digital NTEP</title><script>!function(){try{var t=localStorage.getItem("dash26-theme"),e=window.matchMedia("(prefers-color-scheme: dark)").matches;document.documentElement.setAttribute("data-theme",t||(e?"dark":"light"))}catch(t){document.documentElement.setAttribute("data-theme","light")}}()</script><script defer="defer" src="runtime.js"></script><script defer="defer" src="vendor-fullcalendar.js"></script><script defer="defer" src="vendor-chartjs.js"></script><script defer="defer" src="vendors.js"></script><script defer="defer" src="2026.js"></script><link href="style.css" rel="stylesheet"></head>
<body data-active="boletines" data-crumbs="Revista | Boletines | <?= $esEdicion ? 'Editar' : 'Nuevo' ?>">
<div class="shell">
  <div data-shell-sidebar></div>
  <div class="main">
    <div data-shell-topbar></div>
    <main class="content">
      <section class="hero">
        <div class="hero-text">
          <span class="eyebrow">Contenido</span>
          <h1 class="hero-title"><?= $esEdicion ? 'Editar boletín' : 'Nuevo boletín' ?></h1>
        </div>
      </section>

      <?php if ($error): ?>
        <div style="background:#fdecea;color:#b3261e;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13.5px;"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="grid">
        <section class="col-12 card">
          <form method="post" action="boletin_form.php" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:16px;max-width:640px">
            <?php if ($esEdicion): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>

            <div class="field">
              <label class="field-label" for="numero_boletin">Número de boletín</label>
              <input class="input" id="numero_boletin" name="numero_boletin" type="text" placeholder="NTEP-2026-09" required value="<?= htmlspecialchars($boletin['numero_boletin']) ?>">
            </div>

            <div class="field">
              <label class="field-label" for="resumen">Resumen</label>
              <textarea class="input" id="resumen" name="resumen" rows="4"><?= htmlspecialchars($boletin['resumen'] ?? '') ?></textarea>
            </div>

            <div style="display:flex;gap:16px;flex-wrap:wrap">
              <div class="field" style="flex:1;min-width:200px">
                <label class="field-label" for="fecha_publicacion">Fecha de publicación</label>
                <input class="input" id="fecha_publicacion" name="fecha_publicacion" type="date" required value="<?= htmlspecialchars(substr($boletin['fecha_publicacion'], 0, 10)) ?>">
              </div>
              <div class="field" style="flex:1;min-width:220px">
                <label class="field-label" for="foto_portada">Portada (imagen)</label>
                <?php if (!empty($boletin['foto_portada'])): ?>
                  <div style="margin-bottom:6px"><img src="<?= htmlspecialchars($boletin['foto_portada']) ?>" alt="" style="max-width:140px;border-radius:8px;display:block"></div>
                <?php endif; ?>
                <input class="input" id="foto_portada" name="foto_portada" type="file" accept="image/png,image/jpeg,image/webp">
              </div>
            </div>

            <div class="field">
              <label class="field-label" for="archivo_pdf">PDF del boletín <?= $esEdicion ? '(dejar vacío para conservar el actual)' : '(obligatorio)' ?></label>
              <?php if (!empty($boletin['archivo_pdf'])): ?>
                <div style="margin-bottom:6px"><a href="<?= htmlspecialchars($boletin['archivo_pdf']) ?>" target="_blank">Ver PDF actual</a></div>
              <?php endif; ?>
              <input class="input" id="archivo_pdf" name="archivo_pdf" type="file" accept="application/pdf" <?= $esEdicion ? '' : 'required' ?>>
            </div>

            <div style="display:flex;gap:10px">
              <button class="btn btn--primary" type="submit"><?= $esEdicion ? 'Guardar cambios' : 'Crear boletín' ?></button>
              <a class="btn btn--ghost" href="boletines.php">Cancelar</a>
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
