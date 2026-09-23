<?php
// Variables esperadas antes de incluir:
// $activePage   (string) 'inicio'|'reportajes'|'noticias'|'boletines'|'podcasts'|'videos'|'contacto'
// $tituloPagina (string) título de la pestaña
// $bandaTitulo  (string, opcional) título grande de la banda superior (breadcrumb-area)
// $bandaMigas   (array, opcional) [['texto'=>'Reportajes','url'=>null], ...]
function activoSi(string $clave, string $activa): string { return $clave === $activa ? 'nav-item active' : 'nav-item'; }
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= htmlspecialchars($tituloPagina) ?> - Revista Digital NTEP</title>
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600&amp;subset=latin-ext,vietnamese" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-starter.css">
</head>
<body>
<!-- header -->
<header id="site-header" class="fixed-top">
  <div class="container">
      <nav class="navbar navbar-expand-lg stroke">
      <a class="navbar-brand" href="index.php">
          <img src="assets/images/logo.png" alt="Revista Digital NTEP" title="Revista Digital NTEP" style="height:75px;">
      </a>
          <button class="navbar-toggler collapsed bg-gradient" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon fa icon-expand fa-bars"></span>
              <span class="navbar-toggler-icon fa icon-close fa-times"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
              <ul class="navbar-nav ml-auto">
                  <li class="<?= activoSi('inicio', $activePage) ?>">
                      <a class="nav-link" href="index.php">Inicio <span class="sr-only">(current)</span></a>
                  </li>
                  <li class="<?= activoSi('reportajes', $activePage) ?>">
                      <a class="nav-link" href="reportajes.php">Reportajes</a>
                  </li>
                  <li class="<?= activoSi('noticias', $activePage) ?>">
                      <a class="nav-link" href="noticias.php">Actualidad</a>
                  </li>
                  <li class="<?= activoSi('podcasts', $activePage) ?>">
                      <a class="nav-link" href="podcasts.php">Podcast</a>
                  </li>
                  <li class="<?= activoSi('boletines', $activePage) ?>">
                      <a class="nav-link" href="boletines.php">Boletín NTEP</a>
                  </li>
                  <li class="<?= activoSi('videos', $activePage) ?>">
                      <a class="nav-link" href="videos.php">Videos</a>
                  </li>
                  <li class="<?= activoSi('contacto', $activePage) ?>">
                      <a class="nav-link" href="contacto.php">Sobre NTEP</a>
                  </li>
                  <li class="ml-2">
                      <a href="../login.php" class="btn btn-style btn-outline-secondary">Ingresar</a>
                  </li>
              </ul>
          </div>
      </nav>
  </div>
</header>
<!-- //header -->
<?php if (!empty($bandaTitulo)): ?>
<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big"><?= htmlspecialchars($bandaTitulo) ?></h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="index.php">Inicio</a></li>
                            <?php foreach (($bandaMigas ?? []) as $miga): ?>
                                <li class="<?= empty($miga['url']) ? 'active' : '' ?>">
                                    <?php if (!empty($miga['url'])): ?><a href="<?= htmlspecialchars($miga['url']) ?>"><?= htmlspecialchars($miga['texto']) ?></a>
                                    <?php else: ?> <?= htmlspecialchars($miga['texto']) ?><?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
