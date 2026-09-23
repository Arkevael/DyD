<?php
require __DIR__ . '/includes/db_publico.php';

$destacado = $pdo->query(
    "SELECT r.id, r.titulo, r.resumen_corto, r.foto_principal, r.fecha_publicacion
     FROM reportajes r WHERE r.es_destacado = 1 ORDER BY r.fecha_publicacion DESC LIMIT 1"
)->fetch();
if (!$destacado) {
    $destacado = $pdo->query(
        "SELECT id, titulo, resumen_corto, foto_principal, fecha_publicacion FROM reportajes ORDER BY fecha_publicacion DESC LIMIT 1"
    )->fetch();
}

$recientes = $pdo->prepare(
    "SELECT id, titulo, foto_principal, fecha_publicacion FROM reportajes WHERE id != ? ORDER BY fecha_publicacion DESC LIMIT 3"
);
$recientes->execute([$destacado['id'] ?? 0]);
$recientesReportajes = $recientes->fetchAll();

$ultimasNoticias = $pdo->query('SELECT id, titulo, foto, link_externo, fecha_publicacion FROM noticias ORDER BY fecha_publicacion DESC LIMIT 3')->fetchAll();

$tituloPagina = 'Inicio'; $activePage = 'inicio'; $bandaTitulo = null;
require __DIR__ . '/includes/header.php';
?>

<?php if ($destacado): ?>
<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Reportajes</h2>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="w3l-video w3l-homeblock3" id="video">
    <div class="container-fluid">
        <div class="video-grids-info row">
            <div class="video-gd-right col-lg-6 p-0">
                <div class="position-relative">
                    <a href="reportaje.php?id=<?= (int) $destacado['id'] ?>">
                        <?php if ($destacado['foto_principal']): ?>
                            <img src="../<?= htmlspecialchars($destacado['foto_principal']) ?>" alt="" class="img-fluid">
                        <?php else: ?>
                            <img src="assets/images/video.jpg" alt="" class="img-fluid">
                        <?php endif; ?>
                    </a>
                </div>
            </div>
            <div class="video-gd-left col-lg-6 p-lg-5 p-4 align-self">
                <div class="p-xl-4 p-0 video-wrap">
                    <h5><?= fechaBonita($destacado['fecha_publicacion']) ?></h5>
                    <h3 class="title-big text-left mb-4"><a href="reportaje.php?id=<?= (int) $destacado['id'] ?>"><?= htmlspecialchars($destacado['titulo']) ?></a></h3>
                    <p><?= htmlspecialchars(resumirTexto($destacado['resumen_corto'], 220)) ?></p>
                    <a href="reportaje.php?id=<?= (int) $destacado['id'] ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<div class="grids-block-5 py-1">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php if (!$recientesReportajes): ?>
                    <div class="col-12 text-center py-4"><p>Todavía no hay más reportajes publicados.</p></div>
                <?php else: foreach ($recientesReportajes as $r): ?>
                    <div class="col-lg-4 col-md-6 grids5-info mt-5">
                        <a href="reportaje.php?id=<?= (int) $r['id'] ?>" class="d-block">
                            <?php if ($r['foto_principal']): ?>
                                <img src="../<?= htmlspecialchars($r['foto_principal']) ?>" alt="" class="img-fluid">
                            <?php else: ?>
                                <img src="assets/images/bannerimg.jpg" alt="" class="img-fluid">
                            <?php endif; ?>
                        </a>
                        <div class="blog-info">
                            <h5><?= fechaBonita($r['fecha_publicacion']) ?></h5>
                            <h4><a href="reportaje.php?id=<?= (int) $r['id'] ?>" class="d-block"><?= htmlspecialchars($r['titulo']) ?></a></h4>
                            <a href="reportaje.php?id=<?= (int) $r['id'] ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
            <div class="pagination">
                <ul><li><a href="reportajes.php">Ver todos</a></li></ul>
            </div>
        </div>
    </section>
</div>

<section class="breadcrumb-area py-sm-5 py-1">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Noticias Recientes</h2><a class="anchor" id="actualidad"></a>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php if (!$ultimasNoticias): ?>
                    <div class="col-12 text-center py-4"><p>Todavía no hay noticias publicadas.</p></div>
                <?php else: foreach ($ultimasNoticias as $n): $url = $n['link_externo'] ?: '#'; $externo = $n['link_externo'] ? ' target="_blank" rel="noopener"' : ''; ?>
                    <div class="col-lg-4 col-md-6 grids5-info">
                        <a href="<?= htmlspecialchars($url) ?>"<?= $externo ?> class="d-block">
                            <?php if ($n['foto']): ?>
                                <img src="../<?= htmlspecialchars($n['foto']) ?>" alt="" class="img-fluid">
                            <?php else: ?>
                                <img src="assets/images/bannerimg.jpg" alt="" class="img-fluid">
                            <?php endif; ?>
                        </a>
                        <div class="blog-info">
                            <h5><?= fechaBonita($n['fecha_publicacion']) ?></h5>
                            <h4><a href="<?= htmlspecialchars($url) ?>"<?= $externo ?> class="d-block"><?= htmlspecialchars($n['titulo']) ?></a></h4>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
            <div class="pagination">
                <ul><li><a href="noticias.php">Ver todas</a></li></ul>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
