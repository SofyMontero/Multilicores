<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../models/ProductoModel.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["archivo_excel"])) {
    $archivo = $_FILES["archivo_excel"]["tmp_name"];
    $extension = strtolower(pathinfo($_FILES["archivo_excel"]["name"], PATHINFO_EXTENSION));

    if (is_uploaded_file($archivo)) {
        try {
            $datos = [];
            
            // Si es CSV, leer directamente
            if ($extension === 'csv') {
                if (($handle = fopen($archivo, "r")) !== FALSE) {
                    while (($fila = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $datos[] = $fila;
                    }
                    fclose($handle);
                }
            } 
            // Si es Excel, necesitas convertirlo a CSV primero
            else if ($extension === 'xlsx' || $extension === 'xls') {
                // Mensaje para el usuario
                echo "<script>
                    alert('Para archivos Excel (.xlsx/.xls), por favor:\n1. Abra el archivo en Excel\n2. Guárdelo como CSV (delimitado por comas)\n3. Suba el archivo CSV');
                    window.history.back();
                </script>";
                exit;
            }

            // Remover la primera fila si es encabezado
            if (!empty($datos)) {
                array_shift($datos);
            }

            $producto = new Producto();
            $insertados = 0;
            $actualizados = 0;

            foreach ($datos as $fila) {
                // Asegúrate de que haya al menos 10 columnas
                if (count($fila) >= 10) {
                    // Convertir campos vacíos a valores apropiados
                    $id_producto             = !empty($fila[0]) && is_numeric($fila[0]) ? (int)$fila[0] : null;
                    $codigo_producto         = !empty($fila[1]) ? trim($fila[1]) : '';
                    $descripcion_producto    = !empty($fila[2]) ? trim($fila[2]) : '';
                    $cantidad_paca_producto  = !empty($fila[3]) && is_numeric($fila[3]) ? $fila[3] : 0;
                    $precio_unidad           = !empty($fila[4]) && is_numeric($fila[4]) ? $fila[4] : 0;
                    $precio_paca             = !empty($fila[5]) && is_numeric($fila[5]) ? $fila[5] : 0; 
                    $id_cate_producto        = !empty($fila[6]) && is_numeric($fila[6]) ? $fila[6] : 1;
                    $acti_Unidad             = !empty($fila[7]) ? trim($fila[7]) : 'U'; 
                    $imagen_producto         = !empty($fila[8]) ? trim($fila[8]) : ''; 
                    $estado_producto         = !empty($fila[9]) ? trim($fila[9]) : 'Activo'; 

                    // Solo procesar si al menos hay descripción del producto
                    if (!empty($descripcion_producto)) {
                        // Verificar si el producto ya existe
                        if ($id_producto && $producto->existeProducto($id_producto)) {
                            // Actualizar producto existente
                            if ($producto->actualizarProducto(
                                $id_producto,
                                $codigo_producto,
                                $descripcion_producto,
                                $cantidad_paca_producto,
                                $precio_unidad,
                                $precio_paca,
                                $id_cate_producto,
                                $acti_Unidad,
                                $imagen_producto,
                                $estado_producto
                            )) {
                                $actualizados++;
                            }
                        } else {
                            // Insertar nuevo producto
                            if ($producto->insertarProducto(
                                $codigo_producto,
                                $descripcion_producto,
                                $cantidad_paca_producto,
                                $precio_unidad,
                                $precio_paca,
                                $id_cate_producto,
                                $acti_Unidad,
                                $imagen_producto,
                                $estado_producto
                            )) {
                                $insertados++;
                            }
                        }
                    }
                }
            }
            
            // Redirigir con información de insertados y actualizados
            header("Location: ../views/Subir_excel_producto.php?importados=$insertados&actualizados=$actualizados");
            exit;

        } catch (Exception $e) {
            echo "❌ Error al procesar el archivo: " . $e->getMessage();
        }
    } else {
        echo "❌ No se pudo subir el archivo.";
    }
}
?>