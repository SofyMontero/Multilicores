<?php
/**
 * Helpers de imágenes de promociones (compartidos API + vistas PHP).
 */

require_once __DIR__ . '/../models/database.php';
require_once __DIR__ . '/../models/ProductoModel.php';

function promo_https_base(): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'multilicoreschapinero.com';

    return $scheme . '://' . $host . '/sistema';
}

function promo_find_product($codigoProducto = null, string $descripcion = ''): ?array
{
    static $producto = null;
    static $cache = [];

    $codigo = trim((string)$codigoProducto);
    $cacheKey = $codigo . "\0" . $descripcion;
    if (array_key_exists($cacheKey, $cache)) {
        return $cache[$cacheKey];
    }

    try {
        if ($producto === null) {
            $producto = new Producto();
        }

        if ($codigo !== '' && ctype_digit($codigo)) {
            $prod = $producto->obtenerProductoPorId((int)$codigo);
            if ($prod) {
                return $cache[$cacheKey] = $prod;
            }
        }

        if ($codigo !== '') {
            $prod = $producto->obtenerProductoPorCodigo($codigo);
            if ($prod) {
                return $cache[$cacheKey] = $prod;
            }
        }

        $needle = trim(preg_split('/\s*[+×xX*]\s*/u', $descripcion)[0] ?? '');
        $needle = preg_replace('/\s+/', ' ', $needle);
        if (function_exists('mb_strlen') ? mb_strlen($needle) >= 4 : strlen($needle) >= 4) {
            $sugerencias = $producto->buscarSugerencias($needle);
            if (!empty($sugerencias[0]['id_producto'])) {
                $prod = $producto->obtenerProductoPorId((int)$sugerencias[0]['id_producto']);
                if ($prod) {
                    return $cache[$cacheKey] = $prod;
                }
            }
        }
    } catch (Exception $e) {
        error_log('promo_find_product: ' . $e->getMessage());
    }

    return $cache[$cacheKey] = null;
}

function promo_image_filename(?string $filename): string
{
    $filename = str_replace('\\', '/', trim((string)$filename));
    if ($filename === '') {
        return '';
    }

    return basename($filename);
}

/**
 * Las promociones se guardan en la misma carpeta física que los productos.
 * Es la ubicación que ya se comprobó estable en Hostinger.
 */
function promo_directorio_persistente(): string
{
    return dirname(__DIR__) . '/assets/img/licores/';
}

function promo_asegurar_directorio(string $dir): bool
{
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return false;
    }

    @chmod($dir, 0755);

    $prueba = $dir . '.escritura';
    if (@file_put_contents($prueba, '1') === false) {
        return false;
    }

    @unlink($prueba);
    return true;
}

function promo_directorio_disco(): string
{
    return promo_directorio_persistente();
}

function promo_directorios_busqueda(): array
{
    $base = dirname(__DIR__);

    return [
        promo_directorio_persistente(),
        $base . '/uploads/promos/',
        $base . '/assets/img/promos/',
        $base . '/assets/img/licores/promos/',
        $base . '/uploads/',
    ];
}

/**
 * Valida y reescribe la subida como JPEG nuevo para no conservar archivos
 * dañados, extensiones falsas ni metadatos innecesarios.
 */
function promo_guardar_subida(string $tmpPath): array
{
    if (!is_uploaded_file($tmpPath)) {
        return ['ok' => false, 'error' => 'No se recibio el archivo de la imagen.'];
    }

    if (!function_exists('imagecreatetruecolor') || !function_exists('imagejpeg')) {
        return ['ok' => false, 'error' => 'El servidor no puede procesar imagenes.'];
    }

    $info = @getimagesize($tmpPath);
    $mime = is_array($info) ? (string)($info['mime'] ?? '') : '';
    $lectores = [
        'image/jpeg' => 'imagecreatefromjpeg',
        'image/png' => 'imagecreatefrompng',
        'image/gif' => 'imagecreatefromgif',
        'image/webp' => 'imagecreatefromwebp',
    ];

    if (!isset($lectores[$mime]) || !function_exists($lectores[$mime])) {
        return ['ok' => false, 'error' => 'El archivo no es una imagen valida. Usa JPG, PNG, GIF o WEBP.'];
    }

    $directorio = promo_directorio_persistente();
    if (!promo_asegurar_directorio($directorio)) {
        return ['ok' => false, 'error' => 'No se pudo escribir en la carpeta de imagenes de licores. Revisa los permisos del hosting.'];
    }

    $src = @$lectores[$mime]($tmpPath);
    if (!$src) {
        return ['ok' => false, 'error' => 'No se pudo leer la imagen subida.'];
    }

    $ancho = imagesx($src);
    $alto = imagesy($src);
    $max = 1600;
    $destinoAncho = $ancho;
    $destinoAlto = $alto;
    if ($ancho > $max || $alto > $max) {
        $ratio = min($max / $ancho, $max / $alto);
        $destinoAncho = max(1, (int)round($ancho * $ratio));
        $destinoAlto = max(1, (int)round($alto * $ratio));
    }

    $lienzo = imagecreatetruecolor($destinoAncho, $destinoAlto);
    $blanco = imagecolorallocate($lienzo, 255, 255, 255);
    imagefill($lienzo, 0, 0, $blanco);
    imagecopyresampled($lienzo, $src, 0, 0, 0, 0, $destinoAncho, $destinoAlto, $ancho, $alto);
    imagedestroy($src);

    do {
        $nombre = 'promo-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.jpg';
    } while (is_file($directorio . $nombre));

    $destino = $directorio . $nombre;
    $guardado = imagejpeg($lienzo, $destino, 85);
    imagedestroy($lienzo);

    if (!$guardado) {
        if (is_file($destino)) {
            @unlink($destino);
        }
        return ['ok' => false, 'error' => 'No se pudo guardar la imagen en el servidor.'];
    }

    @chmod($destino, 0644);
    clearstatcache(true, $destino);

    if (!is_file($destino) || filesize($destino) < 100) {
        if (is_file($destino)) {
            @unlink($destino);
        }
        return ['ok' => false, 'error' => 'La imagen no quedo guardada en el hosting.'];
    }

    return ['ok' => true, 'archivo' => $nombre, 'error' => ''];
}

/**
 * Ruta relativa desde /sistema/views/. El script busca el archivo en la carpeta
 * persistente y, si no está, en las rutas anteriores.
 */
function promo_image_web_path(?string $filename, $codigoProducto = null, string $descripcion = '', $promoId = 0): string
{
    $filename = promo_image_filename($filename);
    if ($filename !== '') {
        return '../helpers/promo_imagen.php?f=' . rawurlencode($filename);
    }

    $prod = promo_find_product($codigoProducto, $descripcion);
    $img = $prod['imagen_producto'] ?? '';
    if ($img !== '') {
        return '../assets/img/licores/' . ltrim(str_replace('\\', '/', $img), '/');
    }

    return '../assets/img/logoM.png';
}

/**
 * URL absoluta (API / React). Misma carpeta y mismo nombre de archivo.
 */
function promo_image_absolute_url(?string $filename, $codigoProducto = null, string $descripcion = '', $promoId = 0): string
{
    $filename = promo_image_filename($filename);
    $base = promo_https_base();

    if ($filename !== '') {
        return $base . '/helpers/promo_imagen.php?f=' . rawurlencode($filename);
    }

    $prod = promo_find_product($codigoProducto, $descripcion);
    $img = $prod['imagen_producto'] ?? '';
    if ($img !== '') {
        return $base . '/assets/img/licores/' . ltrim(str_replace('\\', '/', $img), '/');
    }

    return $base . '/assets/img/logoM.png';
}
