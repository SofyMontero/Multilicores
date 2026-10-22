<?php
/**
 * Guardado y lectura de imágenes de productos.
 */

require_once __DIR__ . '/media_storage.php';

function product_image_filename(?string $filename): string
{
    $filename = str_replace('\\', '/', trim((string)$filename));
    return $filename === '' ? '' : basename($filename);
}

function product_image_directories(): array
{
    return [
        multilicores_media_dir('productos'),
        dirname(__DIR__) . '/assets/img/licores/',
    ];
}

function product_image_web_path(?string $filename): string
{
    $filename = product_image_filename($filename);
    if ($filename === '') {
        return '../assets/img/licores/placeholder.jpg';
    }

    return '../helpers/product_image.php?f=' . rawurlencode($filename);
}

function product_store_upload(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'No se recibió correctamente la imagen.'];
    }

    $tmpPath = (string)($file['tmp_name'] ?? '');
    if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
        return ['ok' => false, 'error' => 'El archivo recibido no es una subida válida.'];
    }

    if ((int)($file['size'] ?? 0) > 3 * 1024 * 1024) {
        return ['ok' => false, 'error' => 'La imagen no debe superar los 3MB.'];
    }

    $info = @getimagesize($tmpPath);
    $mime = is_array($info) ? (string)($info['mime'] ?? '') : '';
    $extensiones = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    if (!isset($extensiones[$mime])) {
        return ['ok' => false, 'error' => 'El archivo no es una imagen JPG, PNG, GIF o WEBP válida.'];
    }

    $almacenamiento = multilicores_ensure_media_dir('productos');
    if (empty($almacenamiento['ok'])) {
        return ['ok' => false, 'error' => $almacenamiento['error']];
    }

    $directorio = $almacenamiento['dir'];
    do {
        $nombre = 'prod_' . date('YmdHis') . '_' . bin2hex(random_bytes(4))
            . '.' . $extensiones[$mime];
    } while (is_file($directorio . $nombre));

    $destino = $directorio . $nombre;
    if (!move_uploaded_file($tmpPath, $destino)) {
        return ['ok' => false, 'error' => 'No se pudo guardar la imagen en el almacenamiento persistente.'];
    }

    @chmod($destino, 0644);
    clearstatcache(true, $destino);

    if (!is_file($destino) || filesize($destino) < 1) {
        return ['ok' => false, 'error' => 'La imagen no quedó guardada correctamente.'];
    }

    return ['ok' => true, 'archivo' => $nombre, 'error' => ''];
}
