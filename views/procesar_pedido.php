<?php
require_once "../models/database.php";
require_once "../models/PedidoModel.php";

// Habilitar errores para debugging (eliminar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar conexión a base de datos
if (!isset($pdo)) {
    die('Error: No hay conexión a la base de datos');
}

// Verificar que se recibieron datos
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['productos'])) {
    header('Location: catalogo.php?error=sin_productos');
    exit;
}

try {
    // Obtener datos del formulario
    $productos = $_POST['productos'] ?? [];
    $totalGeneral = floatval($_POST['total_general'] ?? 0);
    
    // Validar que hay productos
    if (empty($productos)) {
        throw new Exception('No se recibieron productos');
    }
    
    // Instanciar modelo de pedidos
    $pedidoModel = new Pedido();
    
    // Preparar datos del pedido
    $datosCliente = [
        'nombre' => $_POST['nombre_cliente'] ?? 'Cliente Web',
        'email' => $_POST['email_cliente'] ?? '',
        'telefono' => $_POST['telefono_cliente'] ?? '',
        'direccion' => $_POST['direccion_cliente'] ?? ''
    ];
    
    // Crear el pedido
    $idPedido = $pedidoModel->crearPedido($datosCliente, $productos, $totalGeneral);
    
    if ($idPedido) {
        // Pedido creado exitosamente
        $mensaje = "Pedido #$idPedido creado exitosamente";
        $tipo = "success";
        $limpiarCarrito = true;
    } else {
        throw new Exception('Error al crear el pedido');
    }
    
} catch (Exception $e) {
    error_log("Error en procesar_pedido.php: " . $e->getMessage());
    $mensaje = "Error al procesar el pedido: " . $e->getMessage();
    $tipo = "error";
    $limpiarCarrito = false;
    $idPedido = null;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Pedido - Multilicores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if ($tipo === 'success'): ?>
                    <div class="card border-success">
                        <div class="card-header bg-success text-white text-center">
                            <h3><i class="fas fa-check-circle me-2"></i>¡Pedido Enviado!</h3>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-4">
                                <i class="fas fa-shopping-cart fa-4x text-success mb-3"></i>
                                <h4>Pedido #<?php echo $idPedido; ?></h4>
                                <p class="lead"><?php echo $mensaje; ?></p>
                                <p class="text-muted">Total: $<?php echo number_format($totalGeneral, 0, ',', '.'); ?> COP</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <a href="catalogo.php" class="btn btn-primary w-100">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Volver al Catálogo
                                    </a>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <a href="mis_pedidos.php" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-list me-1"></i>
                                        Ver Mis Pedidos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white text-center">
                            <h3><i class="fas fa-exclamation-triangle me-2"></i>Error</h3>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-4">
                                <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                                <h4>No se pudo procesar el pedido</h4>
                                <p class="lead text-danger"><?php echo $mensaje; ?></p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <a href="catalogo.php" class="btn btn-secondary w-100">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Volver al Catálogo
                                    </a>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <button type="button" class="btn btn-outline-secondary w-100" onclick="history.back()">
                                        <i class="fas fa-undo me-1"></i>
                                        Intentar de Nuevo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Resumen de productos -->
                <?php if (!empty($productos)): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5><i class="fas fa-list me-2"></i>Resumen del Pedido</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Tipo</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unit.</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($productos as $producto): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($producto['nombre'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo ($producto['tipo'] ?? '') === 'paca' ? 'primary' : 'secondary'; ?>">
                                                        <?php echo ucfirst($producto['tipo'] ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo intval($producto['cantidad'] ?? 0); ?></td>
                                                <td>$<?php echo number_format(floatval($producto['precio_unitario'] ?? 0), 0, ',', '.'); ?></td>
                                                <td>$<?php echo number_format(floatval($producto['subtotal'] ?? 0), 0, ',', '.'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="4" class="text-end">Total General:</th>
                                            <th>$<?php echo number_format($totalGeneral, 0, ',', '.'); ?> COP</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if ($limpiarCarrito): ?>
    <script>
        // Limpiar carrito después de pedido exitoso
        localStorage.removeItem('carritoMultilicores');
        localStorage.removeItem('carritoMultilicores_timestamp');
        console.log('Carrito limpiado después de pedido exitoso');
    </script>
    <?php endif; ?>
</body>
</html>