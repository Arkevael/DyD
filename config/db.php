<?php
// ============================================================
// Configuración de conexión a la base de datos (Railway)
// Lee las variables de entorno. Si no existen, usa valores por defecto.
// ============================================================

// Railway a veces usa nombres como MYSQLHOST, MYSQLPORT, etc.
// Usamos getenv() para leer ambos formatos por seguridad.
$DB_HOST = getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: '127.0.0.1';
$DB_PORT = getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '3306';
$DB_NAME = getenv('DB_DATABASE') ?: getenv('MYSQLDATABASE') ?: 'railway';
$DB_USER = getenv('DB_USERNAME') ?: getenv('MYSQLUSER') ?: 'root';
$DB_PASS = getenv('DB_PASSWORD') ?: getenv('MYSQLPASSWORD') ?: '';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("No se pudo conectar a la base de datos. "
        . "Verifica que las variables de entorno en Railway estén correctas. "
        . "Detalle técnico: " . $e->getMessage());
}
?>