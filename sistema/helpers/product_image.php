<?php
/**
 * Entrega imágenes persistentes de productos y conserva compatibilidad con
 * las imágenes antiguas incluidas en el repositorio.
 */

require_once __DIR__ . '/product_images.php';

$filename = isset($_GET['f'])
    ? product_image_filename((string)$_GET['f'])
    : '';

$path = null;
$mime = '';

if ($filename !== '') {
    foreach (product_image_directories() as $directorio) {
        $candidato = $directorio . $filename;
        if (!is_file($candidato)) {
            continue;
        }

        $info = @getimagesize($candidato);
        $mimeCandidato = is_array($info) ? (string)($info['mime'] ?? '') : '';
        if (in_array($mimeCandidato, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)) {
            $path = $candidato;
            $mime = $mimeCandidato;
            break;
        }
    }
}

$esRespaldo = false;
if ($path === null) {
    $esRespaldo = true;
    $path = dirname(__DIR__) . '/assets/img/licores/placeholder.jpg';
    if (!is_file($path)) {
        $path = dirname(__DIR__) . '/assets/img/logoM.png';
    }

    if (!is_file($path)) {
        http_response_code(404);
        exit;
    }

    $info = @getimagesize($path);
    $mime = is_array($info) ? (string)($info['mime'] ?? 'image/png') : 'image/png';
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . (string)filesize($path));
header('Cache-Control: ' . ($esRespaldo ? 'no-store' : 'public, max-age=86400'));
header('X-Content-Type-Options: nosniff');
readfile($path);
exit;
