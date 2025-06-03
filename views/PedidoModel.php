<?php
class Pedido {
    private $pdo;
    
    public function __construct() {
        // Asumiendo que tienes una conexión PDO en database.php
        global $pdo; // o como tengas configurada tu conexión
        $this->pdo = $pdo;
    }
    
    /**
     * Crear un nuevo pedido
     * @param array $datosCliente - Información del cliente
     * @param array $productos - Array de productos del pedido
     * @param float $total - Total del pedido
     * @return int|false - ID del pedido creado o false si falla
     */
    public function crearPedido($datosCliente, $productos, $total) {
        try {
            $this->pdo->beginTransaction();
            
            // 1. Insertar pedido principal usando tu estructura actual
            $sqlPedido = "INSERT INTO pedidos (
                ped_cliente, 
                ped_estado, 
                ped_fecha, 
                ped_numfac
            ) VALUES (?, ?, NOW(), ?)";
            
            // Generar número de factura único
            $numeroFactura = 'PED-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));
            
            $stmtPedido = $this->pdo->prepare($sqlPedido);
            $stmtPedido->execute([
                $datosCliente['nombre'] . ' (' . ($datosCliente['email'] ?: 'Sin email') . ')',
                'pendiente',
                $numeroFactura
            ]);
            
            $idPedido = $this->pdo->lastInsertId();
            
            // 2. Insertar detalles del pedido
            $sqlDetalle = "INSERT INTO detalle_pedidos (
                id_pedido, 
                id_producto, 
                nombre_producto,
                tipo_producto, 
                cantidad, 
                precio_unitario, 
                subtotal
            ) VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmtDetalle = $this->pdo->prepare($sqlDetalle);
            
            foreach ($productos as $producto) {
                $stmtDetalle->execute([
                    $idPedido,
                    $producto['id'] ?? null,
                    $producto['nombre'] ?? '',
                    $producto['tipo'] ?? '',
                    $producto['cantidad'] ?? 0,
                    $producto['precio_unitario'] ?? 0,
                    $producto['subtotal'] ?? 0
                ]);
            }
            
            $this->pdo->commit();
            return $idPedido;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error en crearPedido: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener pedidos de un cliente
     * @param string $nombreCliente - Nombre del cliente
     * @return array - Array de pedidos
     */
    public function obtenerPedidosCliente($nombreCliente) {
        try {
            $sql = "SELECT * FROM pedidos WHERE ped_cliente LIKE ? ORDER BY ped_fecha DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['%' . $nombreCliente . '%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerPedidosCliente: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener detalles de un pedido específico
     * @param int $idPedido - ID del pedido
     * @return array - Array con detalles del pedido
     */
    public function obtenerDetallePedido($idPedido) {
        try {
            $sql = "SELECT dp.*, p.ped_cliente, p.ped_estado, p.ped_fecha, p.ped_numfac
                    FROM detalle_pedidos dp 
                    INNER JOIN pedidos p ON dp.id_pedido = p.id_pedido 
                    WHERE dp.id_pedido = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idPedido]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerDetallePedido: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Actualizar estado de un pedido
     * @param int $idPedido - ID del pedido
     * @param string $nuevoEstado - Nuevo estado
     * @param string $comentario - Comentario opcional del cambio
     * @return bool - true si se actualiza correctamente
     */
    public function actualizarEstadoPedido($idPedido, $nuevoEstado, $comentario = '') {
        try {
            $this->pdo->beginTransaction();
            
            // Obtener estado actual
            $sqlActual = "SELECT ped_estado FROM pedidos WHERE id_pedido = ?";
            $stmtActual = $this->pdo->prepare($sqlActual);
            $stmtActual->execute([$idPedido]);
            $estadoActual = $stmtActual->fetchColumn();
            
            // Actualizar estado
            $sql = "UPDATE pedidos SET ped_estado = ? WHERE id_pedido = ?";
            $stmt = $this->pdo->prepare($sql);
            $resultado = $stmt->execute([$nuevoEstado, $idPedido]);
            
            // Insertar en historial si existe la tabla
            if ($resultado && $estadoActual && $estadoActual !== $nuevoEstado) {
                try {
                    $sqlHistorial = "INSERT INTO historial_pedidos (id_pedido, estado_anterior, estado_nuevo, comentario) 
                                    VALUES (?, ?, ?, ?)";
                    $stmtHistorial = $this->pdo->prepare($sqlHistorial);
                    $comentarioFinal = $comentario ?: "Estado cambiado de $estadoActual a $nuevoEstado";
                    $stmtHistorial->execute([$idPedido, $estadoActual, $nuevoEstado, $comentarioFinal]);
                } catch (Exception $e) {
                    // Si no existe la tabla historial, continuar sin error
                    error_log("Historial no disponible: " . $e->getMessage());
                }
            }
            
            $this->pdo->commit();
            return $resultado;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error en actualizarEstadoPedido: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener todos los pedidos (para administración)
     * @param int $limite - Límite de resultados
     * @param int $offset - Offset para paginación
     * @return array - Array de pedidos
     */
    public function obtenerTodosPedidos($limite = 50, $offset = 0) {
        try {
            $sql = "SELECT p.*, 
                           COUNT(dp.id_detalle) as total_productos
                    FROM pedidos p 
                    LEFT JOIN detalle_pedidos dp ON p.id_pedido = dp.id_pedido 
                    GROUP BY p.id_pedido 
                    ORDER BY p.ped_fecha DESC 
                    LIMIT ? OFFSET ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$limite, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerTodosPedidos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de pedidos
     * @return array - Array con estadísticas
     */
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_pedidos,
                        COUNT(CASE WHEN ped_estado = 'pendiente' THEN 1 END) as pedidos_pendientes,
                        COUNT(CASE WHEN ped_estado = 'entregado' THEN 1 END) as pedidos_entregados,
                        COUNT(CASE WHEN ped_estado = 'procesando' THEN 1 END) as pedidos_procesando
                    FROM pedidos 
                    WHERE ped_fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Buscar pedidos por criterios
     * @param array $criterios - Array con criterios de búsqueda
     * @return array - Array de pedidos que coinciden
     */
    public function buscarPedidos($criterios = []) {
        try {
            $sql = "SELECT * FROM pedidos WHERE 1=1";
            $params = [];
            
            if (!empty($criterios['estado'])) {
                $sql .= " AND ped_estado = ?";
                $params[] = $criterios['estado'];
            }
            
            if (!empty($criterios['fecha_desde'])) {
                $sql .= " AND ped_fecha >= ?";
                $params[] = $criterios['fecha_desde'];
            }
            
            if (!empty($criterios['fecha_hasta'])) {
                $sql .= " AND ped_fecha <= ?";
                $params[] = $criterios['fecha_hasta'];
            }
            
            if (!empty($criterios['cliente'])) {
                $sql .= " AND ped_cliente LIKE ?";
                $params[] = '%' . $criterios['cliente'] . '%';
            }
            
            if (!empty($criterios['numero_factura'])) {
                $sql .= " AND ped_numfac LIKE ?";
                $params[] = '%' . $criterios['numero_factura'] . '%';
            }
            
            $sql .= " ORDER BY ped_fecha DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en buscarPedidos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener pedido por ID
     * @param int $idPedido - ID del pedido
     * @return array|false - Datos del pedido o false si no existe
     */
    public function obtenerPedidoPorId($idPedido) {
        try {
            $sql = "SELECT * FROM pedidos WHERE id_pedido = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idPedido]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerPedidoPorId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Calcular total del pedido desde detalles
     * @param int $idPedido - ID del pedido
     * @return float - Total calculado
     */
    public function calcularTotalPedido($idPedido) {
        try {
            $sql = "SELECT SUM(subtotal) as total FROM detalle_pedidos WHERE id_pedido = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idPedido]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return floatval($resultado['total'] ?? 0);
        } catch (Exception $e) {
            error_log("Error en calcularTotalPedido: " . $e->getMessage());
            return 0;
        }
    }
}
?>