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
    try {
        $producto = new Producto();
        $codigo = trim((string)$codigoProducto);

        if ($codigo !== '' && ctype_digit($codigo)) {
            $prod = $producto->obtenerProductoPorId((int)$codigo);
            if ($prod) {
                return $prod;
            }
        }

        if ($codigo !== '') {
            $prod = $producto->obtenerProductoPorCodigo($codigo);
            if ($prod) {
                return $prod;
            }
        }

        $needle = trim(preg_split('/\s*[+×xX*]\s*/u', $descripcion)[0] ?? '');
        $needle = preg_replace('/\s+/', ' ', $needle);
        if (function_exists('mb_strlen') ? mb_strlen($needle) >= 4 : strlen($needle) >= 4) {
            $sugerencias = $producto->buscarSugerencias($needle);
            if (!empty($sugerencias[0]['id_producto'])) {
                $prod = $producto->obtenerProductoPorId((int)$sugerencias[0]['id_producto']);
                if ($prod) {
                    return $prod;
                }
            }
        }
    } catch (Exception $e) {
        error_log('promo_find_product: ' . $e->getMessage());
    }

    return null;
}

function promo_local_file_exists(string $filename): bool
{
    $filename = basename(trim($filename));
    if ($filename === '') {
        return false;
    }
    return is_file(__DIR__ . '/../assets/img/licores/promos/' . $filename);
}

/**
 * Ruta relativa desde /sistema/views/ (catálogo PHP).
 */
function promo_image_web_path(?string $filename, $codigoProducto = null, string $descripcion = ''): string
{
    $filename = basename(trim((string)$filename));
    if ($filename !== '' && promo_local_file_exists($filename)) {
        return '../assets/img/licores/promos/' . rawurlencode($filename);
    }

    $prod = promo_find_product($codigoProducto, $descripcion);
    $img = $prod['imagen_producto'] ?? '';
    if ($img !== '') {
        return '../assets/img/licores/' . ltrim(str_replace('\\', '/', $img), '/');
    }

    return '../assets/img/logoM.png';
}

/**
 * URL absoluta (API / React).
 */
function promo_image_absolute_url(?string $filename, $codigoProducto = null, string $descripcion = ''): string
{
    $filename = basename(trim((string)$filename));
    $base = promo_https_base();

    if ($filename !== '' && promo_local_file_exists($filename)) {
        return $base . '/assets/img/licores/promos/' . rawurlencode($filename);
    }

    $prod = promo_find_product($codigoProducto, $descripcion);
    $img = $prod['imagen_producto'] ?? '';
    if ($img !== '') {
        return $base . '/assets/img/licores/' . ltrim(str_replace('\\', '/', $img), '/');
    }

    return $base . '/assets/img/logoM.png';
}
