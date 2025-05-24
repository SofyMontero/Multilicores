<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../models/ProductoModel.php";      // Modelo
require_once "../vendor/autoload.php";      // PhpSpreadsheet


use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["archivo_excel"])) {
    $archivo = $_FILES["archivo_excel"]["tmp_name"];

    if (is_uploaded_file($archivo)) {
        try {
            $spreadsheet = IOFactory::load($archivo);
                $hoja = $spreadsheet->getActiveSheet();
                $datos = $hoja->toArray();

                // Remover la primera fila si es encabezado
                array_shift($datos);

                $producto = new Producto();
                $insertados = 0;

                foreach ($datos as $fila) {
                    // Asegúrate de que haya al menos 5 columnas
                    if (count($fila) >= 5) {
                        // Convertir campos vacíos a 0
                        $precio_unidad  = !empty($fila[0]) ? $fila[0] : 0;
                        $id_categoria   = !empty($fila[1]) ? $fila[1] : 0;
                        $precio_paca    = !empty($fila[2]) ? $fila[2] : 0;
                        $descripcion    = !empty($fila[3]) ? $fila[3] : ''; // Dejar string vacío si no hay descripción
                        $cantidad_paca  = !empty($fila[4]) ? $fila[4] : 0;
                        $imagen         = !empty($fila[5]) ? $fila[5] : ''; // Dejar string vacío si no hay imagen

                        $producto->insertarProducto(
                            $precio_unidad,
                            $id_categoria,
                            $precio_paca,
                            $descripcion,
                            $cantidad_paca,
                            $imagen
                        );

                        $insertados++;
                    }
                }
            header("Location: ../views/Subir_excel_producto.php?importados=$insertados");
            exit;

        } catch (Exception $e) {
            echo "❌ Error al procesar el archivo Excel: " . $e->getMessage();
        }
    } else {
        echo "❌ No se pudo subir el archivo.";
    }
}