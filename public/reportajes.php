<?php
require __DIR__ . '/includes/db_publico.php';

$mesFiltro = $_GET['mes'] ?? '';
$where = '';
$params = [];
if (preg_match('/^\d{4}-\d{2}$/', $mesFiltro)) {
    $where = "WHERE DATE_FORMAT(r.fecha_publicacion, '%Y-%m') = ?";
    $params[] = $mesFiltro;
}

$stmt = $pdo->prepare(
    "SELECT r.id, r.titulo, r.foto_principal, r.fecha_publicacion
     FROM reportajes r
     $where
     ORDER BY r.fecha_publicacion DESC"
);
$stmt->execute($params);
$reportajes = $stmt->fetchAll();

$tituloPagina = 'Reportajes'; $activePage = 'reportajes'; $bandaTitulo = 'Reportajes';
$bandaMigas = [['texto' => 'Reportajes']];
require __DIR__ . '/includes/header.php';
?>
<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <?php if ($mesFiltro): ?>
                <p class="mb-4">Mostrando reportajes de <strong><?= htmlspecialchars($mesFiltro) ?></strong> — <a href="reportajes.php">ver todos</a></p>
            <?php endif; ?>
            <div class="row">
                <?php if (!$reportajes): ?>
                    <div class="col-12 text-center py-5"><p>No hay reportajes para mostrar.</p></div>
                <?php else: foreach ($reportajes as $i => $r): ?>
                    <div class="col-lg-4 col-md-6 grids5-info<?= $i >= 3 ? ' mt-5' : '' ?>">
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
        </div>
    </section>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
