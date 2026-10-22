<?php
/**
 * Almacenamiento para archivos creados desde los formularios.
 *
 * Por defecto queda junto a public_html, no dentro del repositorio desplegado:
 *   domains/dominio/public_html/        (código)
 *   domains/dominio/multilicores-media/ (archivos persistentes)
 *
 * En caso necesario se puede definir MEDIA_STORAGE_PATH en el entorno.
 */

function multilicores_media_root(): string
{
    $configurado = trim((string)getenv('MEDIA_STORAGE_PATH'));
    if ($configurado !== '') {
        return rtrim($configurado, "/\\");
    }

    $documentRoot = trim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''));
    if ($documentRoot !== '') {
        return dirname(rtrim($documentRoot, "/\\"))
            . DIRECTORY_SEPARATOR
            . 'multilicores-media';
    }

    return dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'multilicores-media';
}

function multilicores_media_dir(string $tipo): string
{
    $tipo = preg_replace('/[^a-z0-9_-]/i', '', $tipo);
    return multilicores_media_root()
        . DIRECTORY_SEPARATOR
        . $tipo
        . DIRECTORY_SEPARATOR;
}

function multilicores_ensure_media_dir(string $tipo): array
{
    $dir = multilicores_media_dir($tipo);

    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return [
            'ok' => false,
            'dir' => $dir,
            'error' => 'No se pudo crear el almacenamiento persistente.',
        ];
    }

    @chmod($dir, 0755);

    $prueba = $dir . '.escritura-' . bin2hex(random_bytes(4));
    if (@file_put_contents($prueba, '1') === false) {
        return [
            'ok' => false,
            'dir' => $dir,
            'error' => 'El almacenamiento persistente no tiene permisos de escritura.',
        ];
    }

    @unlink($prueba);

    return ['ok' => true, 'dir' => $dir, 'error' => ''];
}
