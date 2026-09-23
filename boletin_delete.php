<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/upload.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare('SELECT foto_portada, archivo_pdf FROM boletines WHERE id = ?');
    $stmt->execute([$id]);
    $existente = $stmt->fetch();

    $del = $pdo->prepare('DELETE FROM boletines WHERE id = ?');
    $del->execute([$id]);

    if ($existente) {
        borrarArchivo($existente['foto_portada']);
        borrarArchivo($existente['archivo_pdf']);
    }
}
header('Location: boletines.php?ok=eliminado');
exit;
