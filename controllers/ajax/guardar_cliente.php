<?php
require_once '../../models/database.php'; // Asegúrate que este archivo incluye tu clase con connect()
header('Content-Type: application/json');

try {
    $db = new Database();
    $pdo = $db->connect();

    $stmt = $pdo->prepare("INSERT INTO clientes (cli_identificacion, cli_nombre, cli_telefono, cli_direccion) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['cli_identificacion'],
        $_POST['cli_nombre'],
        $_POST['cli_telefono'],
        $_POST['cli_direccion']
    ]);

    echo json_encode(['exito' => true]);
} catch (Exception $e) {
    echo json_encode([
        'exito' => false,
        'mensaje' => $e->getMessage()
    ]);
}
