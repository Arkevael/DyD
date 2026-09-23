<?php
require __DIR__ . '/includes/db_publico.php';

$boletines = $pdo->query(
    'SELECT id, numero_boletin, foto_portada, archivo_pdf, fecha_publicacion FROM boletines ORDER BY fecha_publicacion DESC'
)->fetchAll();

$tituloPagina = 'Boletín NTEP'; $activePage = 'boletines'; $bandaTitulo = 'Boletines NTEP';
$bandaMigas = [['texto' => 'Boletines']];
require __DIR__ . '/includes/header.php';
?>
<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php if (!$boletines): ?>
                    <div class="col-12 text-center py-5"><p>No hay boletines publicados todavía.</p></div>
                <?php else: foreach ($boletines as $i => $b): ?>
                    <div class="col-lg-4 col-md-6 grids5-info<?= $i >= 3 ? ' mt-md-0 mt-5' : '' ?>">
                        <a target="_blank" href="../<?= htmlspecialchars($b['archivo_pdf']) ?>" class="d-block">
                            <?php if ($b['foto_portada']): ?>
                                <img src="../<?= htmlspecialchars($b['foto_portada']) ?>" alt="" class="img-fluid">
                            <?php else: ?>
                                <img src="assets/images/bannerimg.jpg" alt="" class="img-fluid">
                            <?php endif; ?>
                        </a>
                        <div class="blog-info">
                            <h5><?= fechaBonita($b['fecha_publicacion']) ?></h5>
                            <h4 class="d-block"><?= htmlspecialchars($b['numero_boletin']) ?></h4>
                            <a target="_blank" href="../<?= htmlspecialchars($b['archivo_pdf']) ?>" class="btn mt-4 p-0">Ver boletín <span class="fa fa-arrow-right"></span></a>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
