<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../models/ProductoModel.php";

// Función para detectar el delimitador del CSV
function detectarDelimitador($archivo) {
    $handle = fopen($archivo, "r");
    if ($handle) {
        $primeraLinea = fgets($handle);
        fclose($handle);
        
        $delimitadores = [',', ';', '\t', '|'];
        $maxColumnas = 0;
        $mejorDelimitador = ',';
        
        foreach ($delimitadores as $delim) {
            $columnas = str_getcsv($primeraLinea, $delim);
            if (count($columnas) > $maxColumnas) {
                $maxColumnas = count($columnas);
                $mejorDelimitador = $delim;
            }
        }
        
        return $mejorDelimitador;
    }
    return ',';
}

// Función para limpiar y convertir valores
function limpiarValor($valor, $tipo = 'string') {
    $valor = trim($valor);
    
    switch ($tipo) {
        case 'int':
            return is_numeric($valor) && $valor != '' ? (int)$valor : null;
        case 'float':
            // Manejar diferentes formatos de números
            $valor = str_replace([','], ['.'], $valor); // Cambiar coma decimal por punto
            $valor = preg_replace('/[^\d.-]/', '', $valor); // Quitar caracteres no numéricos excepto punto y guión
            return is_numeric($valor) && $valor != '' ? (float)$valor : 0;
        default:
            return $valor;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["archivo_excel"])) {
    
    // Verificaciones básicas
    if (!isset($_FILES["archivo_excel"]) || $_FILES["archivo_excel"]["error"] !== UPLOAD_ERR_OK) {
        header("Location: ../views/Subir_excel_producto.php?error=" . urlencode("Error al subir archivo. Código: " . $_FILES["archivo_excel"]["error"]));
        exit;
    }
    
    $archivo = $_FILES["archivo_excel"]["tmp_name"];
    $nombreArchivo = $_FILES["archivo_excel"]["name"];
    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
    
    // Verificar extensión
    if (!in_array($extension, ['csv'])) {
        header("Location: ../views/Subir_excel_producto.php?error=" . urlencode("Solo se permiten archivos CSV"));
        exit;
    }
    
    // Verificar tamaño (5MB máximo)
    if ($_FILES["archivo_excel"]["size"] > 5 * 1024 * 1024) {
        header("Location: ../views/Subir_excel_producto.php?error=" . urlencode("El archivo no debe superar los 5MB"));
        exit;
    }

    if (is_uploaded_file($archivo)) {
        try {
            $datos = [];
            
            // Detectar delimitador automáticamente
            $delimitador = detectarDelimitador($archivo);
            
            // Leer archivo CSV con el delimitador detectado
            if (($handle = fopen($archivo, "r")) !== FALSE) {
                while (($fila = fgetcsv($handle, 1000, $delimitador)) !== FALSE) {
                    $datos[] = $fila;
                }
                fclose($handle);
            } else {
                header("Location: ../views/Subir_excel_producto.php?error=" . urlencode("Error al leer el archivo CSV"));
                exit;
            }
            
            if (empty($datos)) {
                header("Location: ../views/Subir_excel_producto.php?error=" . urlencode("El archivo está vacío"));
                exit;
            }
            
            // Detectar y remover encabezado
            $primeraFila = $datos[0];
            $tieneEncabezado = false;
            
            // Verificar si la primera fila es encabezado
            if (!empty($primeraFila[0])) {
                $primerValor = strtolower(trim($primeraFila[0]));
                // Es encabezado si contiene palabras clave
                if (!is_numeric($primerValor) || 
                    strpos($primerValor, 'id') !== false || 
                    strpos($primerValor, 'codigo') !== false ||
                    strpos($primerValor, 'producto') !== false ||
                    strpos($primerValor, 'descripcion') !== false) {
                    $tieneEncabezado = true;
                    array_shift($datos);
                }
            }
            
            $producto = new Producto();
            $insertados = 0;
            $actualizados = 0;
            $errores = 0;
            $erroresDetalle = [];
            $filasVacias = 0;
            $preciosActualizados = 0;

            foreach ($datos as $indice => $fila) {
                $filaNumero = $indice + 1 + ($tieneEncabezado ? 1 : 0);
                
                try {
                    // Saltar filas completamente vacías
                    if (empty(array_filter($fila, function($val) { return trim($val) !== ''; }))) {
                        $filasVacias++;
                        continue;
                    }
                    
                    // Expandir array si tiene menos de 10 columnas
                    while (count($fila) < 10) {
                        $fila[] = '';
                    }
                    
                    // Mapear datos según tu estructura de BD
                    $id_producto             = limpiarValor($fila[0], 'int'); // ID específico (opcional)
                    $codigo_productos        = limpiarValor($fila[1]); // CÓDIGO - IDENTIFICADOR PRINCIPAL
                    $descripcion_producto    = limpiarValor($fila[2]); // Descripción
                    $cantidad_paca_producto  = limpiarValor($fila[3], 'float'); // Cantidad
                    $precio_unidad_producto  = limpiarValor($fila[4], 'float'); // Precio unitario
                    $precio_paca_producto    = limpiarValor($fila[5], 'float'); // Precio por paca
                    $id_cate_producto        = limpiarValor($fila[6], 'int') ?: 1; // Categoría
                    $acti_Unidad             = limpiarValor($fila[7]) ?: '1'; // Unidad
                    $imagen_producto         = limpiarValor($fila[8]); // Imagen
                    $estado_producto         = limpiarValor($fila[9]) ?: '1'; // Estado

                    // Validaciones básicas
                    $erroresFila = [];
                    
                    if (empty($codigo_productos)) {
                        $erroresFila[] = "código de producto vacío";
                    }
                    
                    if (empty($descripcion_producto)) {
                        $erroresFila[] = "descripción vacía";
                    }
                    
                    if ($precio_unidad_producto <= 0) {
                        $erroresFila[] = "precio_unidad_producto debe ser mayor a 0";
                    }
                    
                    if (!empty($erroresFila)) {
                        $errores++;
                        $erroresDetalle[] = "Fila $filaNumero: " . implode(', ', $erroresFila);
                        continue;
                    }

                    // LÓGICA PRINCIPAL: Buscar por código de producto
                    $productoExistente = null;
                    
                    // 1. Si hay ID específico, verificar por ID
                    if ($id_producto && $producto->existeProducto($id_producto)) {
                        $productoExistente = $producto->obtenerProductoPorId($id_producto);
                    }
                    
                    // 2. Si no se encontró por ID o no hay ID, buscar por código
                    if (!$productoExistente) {
                        $productoExistente = $producto->obtenerProductoPorCodigo($codigo_productos);
                    }
                    
                    if ($productoExistente) {
                        // ACTUALIZAR producto existente
                        $actualizar = false;
                        $cambios = [];
                        
                        // Verificar si hay cambios en los precios
                        if (abs($productoExistente['precio_unidad_producto'] - $precio_unidad_producto) > 0.01) {
                            $cambios[] = "precio unitario: {$productoExistente['precio_unidad_producto']} → $precio_unidad_producto";
                            $actualizar = true;
                            $preciosActualizados++;
                        }
                        
                        if (abs($productoExistente['precio_paca_producto'] - $precio_paca_producto) > 0.01) {
                            $cambios[] = "precio paca: {$productoExistente['precio_paca_producto']} → $precio_paca_producto";
                            $actualizar = true;
                        }
                        
                        // Verificar otros cambios importantes
                        if ($productoExistente['descripcion_producto'] !== $descripcion_producto) {
                            $cambios[] = "descripción actualizada";
                            $actualizar = true;
                        }
                        
                        if ($productoExistente['cantidad_paca_producto'] != $cantidad_paca_producto) {
                            $cambios[] = "cantidad paca actualizada";
                            $actualizar = true;
                        }
                        
                        // Siempre actualizar si hay diferencias o forzar actualización
                        $actualizar = true; // FORZAR ACTUALIZACIÓN SIEMPRE
                        
                        if ($producto->actualizarProductoPorCodigo(
                            $codigo_productos,
                            $descripcion_producto,
                            $cantidad_paca_producto, 
                            $precio_unidad_producto, 
                            $precio_paca_producto,
                            $id_cate_producto, 
                            $acti_Unidad, 
                            $imagen_producto, 
                            $estado_producto
                        )) {
                            $actualizados++;
                        } else {
                            $errores++;
                            $erroresDetalle[] = "Fila $filaNumero: error al actualizar producto código '$codigo_productos'";
                        }
                        
                    } else {
                        // INSERTAR nuevo producto
                        if ($producto->insertarProducto(
                            $codigo_productos, 
                            $descripcion_producto, 
                            $cantidad_paca_producto,
                            $precio_unidad_producto, 
                            $precio_paca_producto, 
                            $id_cate_producto,
                            $acti_Unidad, 
                            $imagen_producto, 
                            $estado_producto
                        )) {
                            $insertados++;
                        } else {
                            $errores++;
                            $erroresDetalle[] = "Fila $filaNumero: error al insertar producto código '$codigo_productos'";
                        }
                    }
                    
                } catch (Exception $e) {
                    $errores++;
                    $erroresDetalle[] = "Fila $filaNumero: " . $e->getMessage();
                }
            }
            
            // Crear parámetros para la URL
            $params = [
                'importados' => $insertados,
                'actualizados' => $actualizados,
                'errores' => $errores,
                'success' => 1,
                'filas_vacias' => $filasVacias,
                'total_procesadas' => count($datos),
                'precios_actualizados' => $preciosActualizados
            ];
            
            // Agregar algunos errores (máximo 5) si los hay
            if (!empty($erroresDetalle)) {
                $params['errores_detalle'] = implode('|', array_slice($erroresDetalle, 0, 5));
            }
            
            $queryString = http_build_query($params);
            header("Location: ../views/Subir_excel_producto.php?$queryString");
            exit;

        } catch (Exception $e) {
            header("Location: ../views/Subir_excel_producto.php?error=" . urlencode("Error: " . $e->getMessage()));
            exit;
        }
    } else {
        header("Location: ../views/Subir_excel_producto.php?error=" . urlencode("Error al procesar archivo"));
        exit;
    }
} else {
    header("Location: ../views/Subir_excel_producto.php?error=" . urlencode("Solicitud inválida"));
    exit;
}
?>