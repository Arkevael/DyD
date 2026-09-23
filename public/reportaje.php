<?php
require __DIR__ . '/includes/db_publico.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare(
    'SELECT r.*, a.nombres AS autor_nombres, a.ap_paterno AS autor_ap_paterno, a.nickname AS autor_nickname, a.es_nickname AS autor_es_nickname
     FROM reportajes r LEFT JOIN autores a ON a.id = r.autor_id
     WHERE r.id = ?'
);
$stmt->execute([$id]);
$reportaje = $stmt->fetch();

if (!$reportaje) {
    http_response_code(404);
    $tituloPagina = 'Reportaje no encontrado'; $activePage = 'reportajes'; $bandaTitulo = 'Reportajes';
    $bandaMigas = [['texto' => 'No encontrado']];
    require __DIR__ . '/includes/header.php';
    echo '<section class="w3l-blog py-5"><div class="container text-center py-5"><p>El reportaje que buscas no existe o fue eliminado.</p><a href="reportajes.php" class="btn mt-3 p-0">Volver a Reportajes <span class="fa fa-arrow-right"></span></a></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$fotos = $pdo->prepare('SELECT * FROM reportajes_fotos WHERE reportaje_id = ? ORDER BY orden ASC, id ASC');
$fotos->execute([$id]);
$galeria = $fotos->fetchAll();

$autorNombre = nombreAutor([
    'nombres' => $reportaje['autor_nombres'], 'ap_paterno' => $reportaje['autor_ap_paterno'],
    'nickname' => $reportaje['autor_nickname'], 'es_nickname' => $reportaje['autor_es_nickname'],
]) ?: 'Redacción NTEP';

$tituloPagina = $reportaje['titulo'];
$activePage = 'reportajes';
$bandaTitulo = 'Reportajes';
$bandaMigas = [['texto' => 'Reportajes']];
require __DIR__ . '/includes/header.php';
?>
<section class="w3l-blog mt-lg-5">
    <div class="text-element-9 py-5 mt-lg-5">
        <div class="container py-lg-3">
            <div class="row grid-text-9">
                <div class="col-lg-8">
                    <div class="blog-single-post">
                        <div class="post-content">
                            <h2 class="title-single mb-3"><?= htmlspecialchars($reportaje['titulo']) ?></h2>
                        </div>
                        <div class="blo-singl mb-4">
                            <ul class="blog-single-author-date d-flex align-items-center">
                                <li>Por <a href="#"><?= htmlspecialchars($autorNombre) ?></a></li>
                                <li><?= fechaBonita($reportaje['fecha_publicacion']) ?></li>
                            </ul>
                        </div>

                        <?php if (!empty($reportaje['foto_principal'])): ?>
                        <div class="single-post-image mb-4 text-center">
                            <?php if (!empty($reportaje['pdf_adjunto'])): ?><a target="_blank" href="../<?= htmlspecialchars($reportaje['pdf_adjunto']) ?>"><?php endif; ?>
                            <img src="../<?= htmlspecialchars($reportaje['foto_principal']) ?>" class="img-fluid w-100 radius-image" alt="<?= htmlspecialchars($reportaje['titulo']) ?>">
                            <?php if (!empty($reportaje['pdf_adjunto'])): ?><br>Clic en la imagen para ver el PDF adjunto</a><?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="single-post-content">
                            <?php if (!empty($reportaje['resumen_corto'])): ?>
                            <blockquote class="blockquote my-5">
                                <q class="mb-3 d-block"><?= htmlspecialchars($reportaje['resumen_corto']) ?></q>
                            </blockquote>
                            <?php endif; ?>

                            <?php foreach (preg_split('/\r?\n\r?\n|\r?\n/', trim($reportaje['desarrollo'])) as $parrafo): if (trim($parrafo) === '') continue; ?>
                                <p align="justify" class="mb-4"><?= nl2br(htmlspecialchars($parrafo)) ?></p>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($galeria): ?>
                        <div class="row mt-2 mb-4">
                            <?php foreach ($galeria as $foto): ?>
                                <div class="col-6 col-md-4 mb-3">
                                    <img src="../<?= htmlspecialchars($foto['url_foto']) ?>" class="img-fluid radius-image" alt="<?= htmlspecialchars($foto['descripcion'] ?? '') ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <nav class="post-navigation row mb-5 py-4">
                            <div class="post-prev col-md-6 pr-sm-5">
                                <span class="nav-title"><span class="fa fa-arrow-left mr-2"></span> <a href="reportajes.php">Reportajes</a></span>
                            </div>
                        </nav>
                    </div>
                </div>

                <div class="col-lg-4 left-text-9 mt-lg-0 mt-5 pl-lg-4">
                    <?php require __DIR__ . '/includes/sidebar.php'; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
