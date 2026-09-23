<?php
// Configuración de conexión a la base de datos revista_digital
// Ajusta estos valores solo si tu instalación de MySQL/XAMPP usa
// un usuario, contraseña o host distintos.

$DB_HOST = '127.0.0.1';
$DB_NAME = 'revista_digital';
$DB_USER = 'root';
$DB_PASS = '';          // en XAMPP por defecto no hay contraseña

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('No se pudo conectar a la base de datos "revista_digital". '
        . 'Verifica que MySQL esté iniciado en XAMPP y que hayas importado sql/revista_digital.sql. '
        . 'Detalle técnico: ' . $e->getMessage());
}
