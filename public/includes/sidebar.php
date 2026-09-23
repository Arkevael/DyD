<?php
// Requiere $pdo. Opcional: $excluirId para no repetir el reportaje actual.
$excluirId = $excluirId ?? 0;

$ultimos = $pdo->prepare(
    'SELECT id, titulo, fecha_publicacion FROM reportajes WHERE id != ? ORDER BY fecha_publicacion DESC LIMIT 5'
);
$ultimos->execute([$excluirId]);
$ultimosReportajes = $ultimos->fetchAll();

$meses = $pdo->query(
    "SELECT DISTINCT DATE_FORMAT(fecha_publicacion, '%Y-%m') AS ym FROM reportajes ORDER BY ym DESC LIMIT 8"
)->fetchAll();
$nombresMes = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
?>
<div class="left-top-9 mt-5 pt-sm-3">
    <h6 class="heading-small-text-9 mb-3">Últimos reportajes</h6>
    <?php if (!$ultimosReportajes): ?>
        <p class="text-muted" style="font-size:.9rem">Todavía no hay más reportajes publicados.</p>
    <?php else: foreach ($ultimosReportajes as $u): ?>
        <a href="reportaje.php?id=<?= (int) $u['id'] ?>" class="p-post d-block py-2">
            <h6 class="text-left-inner-9"><?= htmlspecialchars($u['titulo']) ?></h6>
            <span class="sub-inner-text-9"><?= fechaBonita($u['fecha_publicacion']) ?></span>
        </a>
    <?php endforeach; endif; ?>
</div>
<div class="categories mt-5 pt-sm-3">
    <h6 class="heading-small-text-9">Archivos</h6>
    <ul>
        <?php if (!$meses): ?>
            <li><span class="text-muted" style="font-size:.9rem">Sin publicaciones todavía.</span></li>
        <?php else: foreach ($meses as $m): [$anio, $mes] = explode('-', $m['ym']); ?>
            <li><a href="reportajes.php?mes=<?= htmlspecialchars($m['ym']) ?>"><?= $nombresMes[$mes] ?> <?= $anio ?></a></li>
        <?php endforeach; endif; ?>
    </ul>
</div>
