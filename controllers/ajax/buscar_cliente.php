<?php
require_once '../../models/database.php'; // tu conexión PDO
$telefono = $_GET['telefono'] ?? '';


header('Content-Type: application/json');



if (!empty($telefono)) {
    try {
        // Crear instancia de la clase Database y conectar
        $db = new Database();     // Asegúrate de que tu clase se llama así
        $pdo = $db->connect();

        // Consulta
        $stmt = $pdo->prepare("SELECT cli_nombre FROM clientes WHERE cli_telefono = ?");
        $stmt->execute([$telefono]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            echo json_encode(['existe' => true, 'nombre' => $cliente['cli_nombre']]);
        } else {
            echo json_encode(['existe' => false]);
        }

    } catch (Exception $e) {
        echo json_encode([
            'existe' => false,
            'error' => 'Error en la base de datos: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['existe' => false, 'error' => 'Número no proporcionado']);
}