<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/db.php';

if ($usuarioActual['rol'] !== 'admin') {
    http_response_code(403);
    die('No tienes permisos para gestionar usuarios.');
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0 && $id !== (int) $usuarioActual['id']) {
    // Los reportajes/noticias/boletines/podcasts/videos referencian usuario_id con
    // ON DELETE RESTRICT, así que si el usuario tiene contenido publicado, MySQL
    // rechazará el borrado para no dejar contenido huérfano. Lo capturamos aquí
    // y mostramos un mensaje claro en vez de un error crudo de MySQL.
    try {
        $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
        header('Location: usuarios.php?ok=eliminado');
        exit;
    } catch (PDOException $e) {
        header('Location: usuarios.php?error=tiene_contenido');
        exit;
    }
}

header('Location: usuarios.php');
exit;
