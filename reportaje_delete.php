<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare('DELETE FROM reportajes WHERE id = ?');
    $stmt->execute([$id]);
}
header('Location: reportajes.php?ok=eliminado');
exit;
