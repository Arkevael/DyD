<?php
/**
 * Convierte un link "normal" (el que la gente copia del navegador) a su
 * versión embebible en un <iframe>. Si el link ya viene en formato embed,
 * o no se reconoce el proveedor, se devuelve tal cual.
 *
 * Por qué hacía falta: YouTube y Spotify bloquean (X-Frame-Options /
 * frame-ancestors) que sus páginas normales de "ver" o "escuchar" se
 * carguen dentro de un iframe de otro sitio. Solo sus URLs /embed/
 * están pensadas para eso; por eso el iframe mostraba
 * "la página ha rechazado la conexión" cuando se guardaba el link normal.
 */
function normalizarUrlEmbed(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return $url;
    }

    $host = parse_url($url, PHP_URL_HOST) ?: '';
    $path = parse_url($url, PHP_URL_PATH) ?: '';
    parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
    $host = strtolower($host);

    // --- YouTube ---
    if (strpos($host, 'youtube.com') !== false || strpos($host, 'youtu.be') !== false) {
        // Ya es un link /embed/
        if (strpos($path, '/embed/') !== false) {
            return $url;
        }

        $videoId = null;

        if (strpos($host, 'youtu.be') !== false) {
            // https://youtu.be/VIDEOID
            $videoId = trim($path, '/');
        } elseif (isset($query['v'])) {
            // https://www.youtube.com/watch?v=VIDEOID
            $videoId = $query['v'];
        } elseif (preg_match('#/shorts/([^/?]+)#', $path, $m)) {
            // https://www.youtube.com/shorts/VIDEOID
            $videoId = $m[1];
        } elseif (preg_match('#/live/([^/?]+)#', $path, $m)) {
            // https://www.youtube.com/live/VIDEOID
            $videoId = $m[1];
        }

        if (isset($query['list']) && !$videoId) {
            // Solo playlist, sin video puntual
            return 'https://www.youtube.com/embed/videoseries?list=' . urlencode($query['list']);
        }

        if ($videoId) {
            $embed = 'https://www.youtube.com/embed/' . urlencode($videoId);
            if (isset($query['list'])) {
                $embed .= '?list=' . urlencode($query['list']);
            }
            return $embed;
        }

        return $url;
    }

    // --- Spotify ---
    if (strpos($host, 'spotify.com') !== false) {
        if (strpos($path, '/embed/') !== false) {
            return $url;
        }

        // Tipos válidos: track, episode, show, album, playlist, artist
        if (preg_match('#/(track|episode|show|album|playlist|artist)/([A-Za-z0-9]+)#', $path, $m)) {
            return 'https://open.spotify.com/embed/' . $m[1] . '/' . $m[2];
        }

        return $url;
    }

    // Otro proveedor: se guarda tal cual, se asume que el usuario ya
    // pegó un link embebible.
    return $url;
}
