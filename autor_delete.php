<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    try {
        $stmt = $pdo->prepare('DELETE FROM autores WHERE id=?');
        $stmt->execute([$id]);
        header('Location: autores.php?ok=eliminado'); exit;
    } catch (PDOException $e) {
        // Fallará si el autor tiene reportajes asociados (FK RESTRICT)
        header('Location: autores.php?error=tiene_reportajes'); exit;
    }
}
header('Location: autores.php'); exit;
