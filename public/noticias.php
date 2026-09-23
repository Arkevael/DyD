<?php
require __DIR__ . '/includes/db_publico.php';

$noticias = $pdo->query(
    'SELECT id, titulo, foto, link_externo, fecha_publicacion FROM noticias ORDER BY fecha_publicacion DESC'
)->fetchAll();

$tituloPagina = 'Noticias'; $activePage = 'noticias'; $bandaTitulo = 'Noticias Recientes';
$bandaMigas = [['texto' => 'Noticias']];
require __DIR__ . '/includes/header.php';
?>
<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php if (!$noticias): ?>
                    <div class="col-12 text-center py-5"><p>No hay noticias publicadas todavía.</p></div>
                <?php else: foreach ($noticias as $i => $n): $url = $n['link_externo'] ?: '#'; $externo = $n['link_externo'] ? ' target="_blank" rel="noopener"' : ''; ?>
                    <div class="col-lg-4 col-md-6 grids5-info<?= $i >= 3 ? ' mt-5' : '' ?>">
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
                            <?php if ($n['link_externo']): ?>
                                <a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
