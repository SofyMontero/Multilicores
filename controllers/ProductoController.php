<?php
require_once "../vendor/autoload.php";      // PhpSpreadsheet
require_once "../models/ProductoModel.php";      // Modelo

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
                    $precio_unidad  = $fila[0];
                    $id_categoria   = $fila[1];
                    $precio_paca    = $fila[2];
                    $descripcion    = $fila[3];
                    $cantidad_paca  = $fila[4];

                    $producto->insertarProducto($precio_unidad, $id_categoria, $precio_paca, $descripcion, $cantidad_paca);
                    $insertados++;
                }
            }

            header("Location: ../views/product-list.php?importados=$insertados");
            exit;

        } catch (Exception $e) {
            echo "❌ Error al procesar el archivo Excel: " . $e->getMessage();
        }
    } else {
        echo "❌ No se pudo subir el archivo.";
    }
}