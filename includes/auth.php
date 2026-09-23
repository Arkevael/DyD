<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Datos del usuario logueado, disponibles en cualquier página que
// incluya este archivo.
$usuarioActual = [
    'id'      => $_SESSION['usuario_id'],
    'nombres' => $_SESSION['usuario_nombres'],
    'rol'     => $_SESSION['usuario_rol'],
    'email'   => $_SESSION['usuario_email'],
];
