<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare('DELETE FROM videos WHERE id = ?');
    $stmt->execute([$id]);
}
header('Location: videos.php?ok=eliminado');
exit;
