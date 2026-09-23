<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/upload.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare('SELECT foto FROM noticias WHERE id = ?');
    $stmt->execute([$id]);
    $existente = $stmt->fetch();

    $del = $pdo->prepare('DELETE FROM noticias WHERE id = ?');
    $del->execute([$id]);

    if ($existente) borrarArchivo($existente['foto']);
}
header('Location: noticias.php?ok=eliminado');
exit;
