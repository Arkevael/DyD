<?php
require __DIR__ . '/../../config/db.php';
// $pdo queda disponible para todas las páginas públicas.

/** Devuelve el nombre para mostrar de un autor (usa nickname si corresponde). */
function nombreAutor(array $autor): string
{
    if (!empty($autor['es_nickname']) && !empty($autor['nickname'])) {
        return $autor['nickname'];
    }
    return trim(($autor['nombres'] ?? '') . ' ' . ($autor['ap_paterno'] ?? ''));
}

/** Formatea una fecha SQL (YYYY-MM-DD) al estilo "08 Set, 2026". */
function fechaBonita(?string $fechaSql): string
{
    if (!$fechaSql) return '';
    $meses = ['01'=>'Ene','02'=>'Feb','03'=>'Mar','04'=>'Abr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Ago','09'=>'Set','10'=>'Oct','11'=>'Nov','12'=>'Dic'];
    $ts = strtotime($fechaSql);
    return date('d', $ts) . ' ' . $meses[date('m', $ts)] . ', ' . date('Y', $ts);
}

/** Recorta un texto a $limite caracteres sin cortar palabras a la mitad. */
function resumirTexto(?string $texto, int $limite = 140): string
{
    $texto = trim(strip_tags((string) $texto));
    if (mb_strlen($texto) <= $limite) return $texto;
    return mb_substr($texto, 0, $limite - 1) . '…';
}
