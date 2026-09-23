<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$esEdicion = $id > 0;
$error = '';
$autor = ['nombres' => '', 'ap_paterno' => '', 'ap_materno' => '', 'nickname' => '', 'es_nickname' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $autor = [
        'nombres'     => trim($_POST['nombres'] ?? ''),
        'ap_paterno'  => trim($_POST['ap_paterno'] ?? ''),
        'ap_materno'  => trim($_POST['ap_materno'] ?? ''),
        'nickname'    => trim($_POST['nickname'] ?? ''),
        'es_nickname' => isset($_POST['es_nickname']) ? 1 : 0,
    ];
    if ($autor['nombres'] === '') {
        $error = 'El nombre es obligatorio.';
    } elseif ($autor['es_nickname'] && $autor['nickname'] === '') {
        $error = 'Si marcas "usar apodo", debes escribir el apodo.';
    } else {
        if ($esEdicion) {
            $stmt = $pdo->prepare('UPDATE autores SET nombres=?, ap_paterno=?, ap_materno=?, nickname=?, es_nickname=? WHERE id=?');
            $stmt->execute([$autor['nombres'], $autor['ap_paterno'] ?: null, $autor['ap_materno'] ?: null, $autor['nickname'] ?: null, $autor['es_nickname'], $id]);
            header('Location: autores.php?ok=editado'); exit;
        } else {
            $stmt = $pdo->prepare('INSERT INTO autores (nombres, ap_paterno, ap_materno, nickname, es_nickname) VALUES (?,?,?,?,?)');
            $stmt->execute([$autor['nombres'], $autor['ap_paterno'] ?: null, $autor['ap_materno'] ?: null, $autor['nickname'] ?: null, $autor['es_nickname']]);
            header('Location: autores.php?ok=creado'); exit;
        }
    }
} elseif ($esEdicion) {
    $stmt = $pdo->prepare('SELECT * FROM autores WHERE id=?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) $autor = $found;
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $esEdicion ? 'Editar' : 'Nuevo' ?> autor · Revista Digital NTEP</title><script>!function(){try{var t=localStorage.getItem("dash26-theme"),e=window.matchMedia("(prefers-color-scheme: dark)").matches;document.documentElement.setAttribute("data-theme",t||(e?"dark":"light"))}catch(t){document.documentElement.setAttribute("data-theme","light")}}()</script><script defer="defer" src="runtime.js"></script><script defer="defer" src="vendor-fullcalendar.js"></script><script defer="defer" src="vendor-chartjs.js"></script><script defer="defer" src="vendors.js"></script><script defer="defer" src="2026.js"></script><link href="style.css" rel="stylesheet"></head>
<body data-active="autores" data-crumbs="Revista | Autores | <?= $esEdicion ? 'Editar' : 'Nuevo' ?>">
<div class="shell">
  <div data-shell-sidebar></div>
  <div class="main">
    <div data-shell-topbar></div>
    <main class="content">
      <section class="hero">
        <div class="hero-text">
          <span class="eyebrow">Contenido</span>
          <h1 class="hero-title"><?= $esEdicion ? 'Editar autor' : 'Nuevo autor' ?></h1>
        </div>
      </section>

      <?php if ($error): ?>
        <div style="background:#fdecea;color:#b3261e;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13.5px;"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="grid">
        <section class="col-12 card">
          <form method="post" action="autor_form.php" style="display:flex;flex-direction:column;gap:16px;max-width:560px">
            <?php if ($esEdicion): ?><input type="hidden" name="id" value="<?= (int)$id ?>"><?php endif; ?>
            <div class="field"><label class="field-label" for="nombres">Nombres</label><input class="input" id="nombres" name="nombres" required value="<?= htmlspecialchars($autor['nombres']) ?>"></div>
            <div style="display:flex;gap:16px;flex-wrap:wrap">
              <div class="field" style="flex:1;min-width:200px"><label class="field-label" for="ap_paterno">Apellido paterno</label><input class="input" id="ap_paterno" name="ap_paterno" value="<?= htmlspecialchars($autor['ap_paterno'] ?? '') ?>"></div>
              <div class="field" style="flex:1;min-width:200px"><label class="field-label" for="ap_materno">Apellido materno</label><input class="input" id="ap_materno" name="ap_materno" value="<?= htmlspecialchars($autor['ap_materno'] ?? '') ?>"></div>
            </div>
            <div class="field"><label class="field-label" for="nickname">Apodo / firma pública</label><input class="input" id="nickname" name="nickname" placeholder='Ej. "Redacción NTEP"' value="<?= htmlspecialchars($autor['nickname'] ?? '') ?>"></div>
            <label class="check"><input type="checkbox" name="es_nickname" <?= !empty($autor['es_nickname']) ? 'checked' : '' ?>><span class="box"></span> Mostrar el apodo en vez del nombre completo</label>
            <div style="display:flex;gap:10px">
              <button class="btn btn--primary" type="submit"><?= $esEdicion ? 'Guardar cambios' : 'Crear autor' ?></button>
              <a class="btn btn--ghost" href="autores.php">Cancelar</a>
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
