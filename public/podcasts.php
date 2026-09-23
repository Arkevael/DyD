<?php
require __DIR__ . '/includes/db_publico.php';

$podcasts = $pdo->query(
    'SELECT id, titulo, url_embed, fecha_publicacion FROM podcasts ORDER BY fecha_publicacion DESC'
)->fetchAll();

$tituloPagina = 'Podcast'; $activePage = 'podcasts'; $bandaTitulo = 'Podcast';
$bandaMigas = [['texto' => 'Podcast']];
require __DIR__ . '/includes/header.php';
?>
<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php if (!$podcasts): ?>
                    <div class="col-12 text-center py-5"><p>No hay podcast publicados todavía.</p></div>
                <?php else: foreach ($podcasts as $i => $x): ?>
                    <div class="col-lg-6 grids5-info<?= $i >= 2 ? ' mt-5' : '' ?>">
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="<?= htmlspecialchars($x['url_embed']) ?>" allowfullscreen loading="lazy" title="<?= htmlspecialchars($x['titulo']) ?>"></iframe>
                        </div>
                        <div class="blog-info">
                            <h5><?= fechaBonita($x['fecha_publicacion']) ?></h5>
                            <h4 class="d-block"><?= htmlspecialchars($x['titulo']) ?></h4>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
