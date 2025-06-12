<?php
/**
 * Endpoint para buscar bares - usado para el autocompletado
 */
require_once "../models/database.php";
require_once "../models/BarModel.php";

// Establecer el tipo de contenido como JSON
header('Content-Type: application/json');

// Verificar que se reciba el parámetro 'q' (query)
if (!isset($_POST['query']) || empty(trim($_POST['query']))) {
    echo json_encode([]);
    exit;
}

try {
    $bar = new Bar();
    $termino = trim($_POST['query']);
    
    // Buscar bares que coincidan con el término
  $bares = $bar->buscarBaresPorNombre($termino);
    
    // Formatear los resultados para el autocompletado
    $resultados = [];
    foreach ($bares as $barData) {
        $resultados[] = [
            'id' => $barData['id_bar'],
            'value' => $barData['nombre_bar'],
            'label' => $barData['nombre_bar'],
            'direccion' => $barData['direccion'],
            'telefono' => $barData['telefono'],
            'email' => $barData['email']
        ];
    }
    
    echo json_encode($resultados);

} catch (Exception $e) {
    
    echo json_encode([]);
}