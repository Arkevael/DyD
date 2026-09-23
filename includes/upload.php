<?php
/**
 * Sube un archivo desde $_FILES[$campo] a la carpeta uploads/$subcarpeta.
 * Devuelve la ruta relativa a guardar en la BD (ej. "uploads/reportajes/xxx.jpg")
 * o null si no se envió ningún archivo.
 * Si hay un error de validación, lanza una excepción con un mensaje amigable.
 */
function subirArchivo(string $campo, string $subcarpeta, array $extensionesPermitidas): ?string
{
    if (empty($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // no se seleccionó archivo, está bien (es opcional)
    }

    $archivo = $_FILES[$campo];

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Ocurrió un error al subir el archivo.');
    }

    if ($archivo['size'] > 8 * 1024 * 1024) { // 8 MB máx.
        throw new RuntimeException('El archivo supera el tamaño máximo permitido (8 MB).');
    }

    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $extensionesPermitidas, true)) {
        throw new RuntimeException('Formato no permitido. Usa: ' . implode(', ', $extensionesPermitidas));
    }

    $carpetaDestino = __DIR__ . '/../uploads/' . $subcarpeta;
    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0775, true);
    }

    $nombreUnico = uniqid($subcarpeta . '_', true) . '.' . $extension;
    $rutaCompleta = $carpetaDestino . '/' . $nombreUnico;

    if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
        throw new RuntimeException('No se pudo guardar el archivo en el servidor.');
    }

    return 'uploads/' . $subcarpeta . '/' . $nombreUnico;
}

/** Borra un archivo previamente subido (ignora si no existe). */
function borrarArchivo(?string $rutaRelativa): void
{
    if (!$rutaRelativa) return;
    $ruta = __DIR__ . '/../' . $rutaRelativa;
    if (is_file($ruta)) {
        @unlink($ruta);
    }
}
