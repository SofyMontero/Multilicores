
<?php
class Pedido
{
    private $db;
    private $pdo;

    public function __construct()
    {
        $this->db = new Database();
        $this->pdo = $this->db->connect();

        // Verificar conexión
        if (!$this->pdo) {
            throw new Exception('No se pudo conectar a la base de datos');
        }
    }


    public function crearPedido($datosCliente, $productos, $total,$numCliente,$observaciones)

    {
        try {
            // Iniciar transacción
            $this->pdo->beginTransaction();

            // obtener id cliente
            $sqlidClinte = "SELECT id_cliente FROM clientes WHERE cli_telefono = ?";
            $stmtCheck = $this->pdo->prepare($sqlidClinte);
            $stmtCheck->execute([$numCliente]);
            $clienteInfo = $stmtCheck->fetchColumn();

            // Generar número de factura único
            $numeroFactura = 'PED-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));

            // Verificar que el número no exista (prevenir duplicados)
            $sqlCheck = "SELECT COUNT(*) FROM pedidos WHERE ped_numfac = ?";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([$numeroFactura]);             

            if ($stmtCheck->fetchColumn() > 0) {
                // Si existe, generar uno nuevo
                $numeroFactura = 'PED-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));
            }

            $telefono = trim($numCliente);
            if (substr($telefono, 0, 2) !== '57') {
                $telefono = '57' . $telefono;
            }
            // Insertar pedido principal
            $sqlPedido = "INSERT INTO pedidos (
                ped_cliente, 
                ped_estado, 
                ped_fecha, 
                ped_numfac,
                ped_total,
                ped_numCliente,
                ped_observacion
            ) VALUES (?, ?, NOW(), ?, ?,?,?)";
           

            $stmtPedido = $this->pdo->prepare($sqlPedido);
            $stmtPedido->execute([
                $clienteInfo,        // ← asegúrate de tener este valor disponible
                1, 
                $numeroFactura,
                $total,
                $telefono,
                $observaciones

            ]);

            $idPedido = $this->pdo->lastInsertId();

            if (!$idPedido) {
                throw new Exception('No se pudo obtener el ID del pedido');
            }

            // Insertar detalles del pedido
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
                // Validar datos del producto
                $idProducto = $producto['id'] ?? null;
                $nombre = $producto['nombre'] ?? '';
                $tipo = $producto['tipo'] ?? '';
                $cantidad = intval($producto['cantidad'] ?? 0);
                $precioUnitario = floatval($producto['precio_unitario'] ?? 0);
                $subtotal = floatval($producto['subtotal'] ?? 0);

                // Verificar que los datos son válidos
                if (empty($nombre) || $cantidad <= 0 || $precioUnitario <= 0) {
                    throw new Exception("Datos inválidos en producto: $nombre");
                }

                $stmtDetalle->execute([
                    $idPedido,
                    $idProducto,
                    $nombre,
                    $tipo,
                    $cantidad,
                    $precioUnitario,
                    $subtotal
                ]);
            }

            // Confirmar transacción
            $this->pdo->commit();

            // Log del pedido creado
            error_log("Pedido creado exitosamente: ID $idPedido, Factura: $numeroFactura, Total: $total");

            return $idPedido;
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->pdo->rollBack();
            error_log("Error en crearPedido: " . $e->getMessage());
            throw $e; // Re-lanzar la excepción para manejo en el controlador
        }
    }

    public function obtenerPedidosCliente($nombreCliente, $limite = 50)
    {
        try {
            $sql = "SELECT p.*, COUNT(dp.id_detalle) as total_productos 
                    FROM pedidos p 
                    LEFT JOIN detalle_pedidos dp ON p.id_pedido = dp.id_pedido 
                    WHERE p.ped_cliente LIKE ? 
                    GROUP BY p.id_pedido 
                    ORDER BY p.ped_fecha DESC 
                    LIMIT ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['%' . $nombreCliente . '%', $limite]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerPedidosCliente: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerDetallePedido($idPedido)
    {
        try {
            $sql = "SELECT dp.*, p.ped_cliente, p.ped_estado, p.ped_fecha, p.ped_numfac, p.ped_total
                    FROM detalle_pedidos dp 
                    INNER JOIN pedidos p ON dp.id_pedido = p.id_pedido 
                    WHERE dp.id_pedido = ?
                    ORDER BY dp.id_detalle";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idPedido]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerDetallePedido: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPedidoPorId($idPedido)
    {
        try {
            $sql = "SELECT p.*, COUNT(dp.id_detalle) as total_productos 
                    FROM pedidos p 
                    LEFT JOIN detalle_pedidos dp ON p.id_pedido = dp.id_pedido 
                    WHERE p.id_pedido = ? 
                    GROUP BY p.id_pedido";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idPedido]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerPedidoPorId: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarEstadoPedido($idPedido, $nuevoEstado, $comentario = '')
    {
        try {
            $this->pdo->beginTransaction();

            // Obtener estado actual
            $sqlActual = "SELECT ped_estado FROM pedidos WHERE id_pedido = ?";
            $stmtActual = $this->pdo->prepare($sqlActual);
            $stmtActual->execute([$idPedido]);
            $estadoActual = $stmtActual->fetchColumn();

            if (!$estadoActual) {
                throw new Exception("Pedido no encontrado: $idPedido");
            }

            // Actualizar estado
            $sql = "UPDATE pedidos SET ped_estado = ?, ped_fecha_actualizacion = NOW() WHERE id_pedido = ?";
            $stmt = $this->pdo->prepare($sql);
            $resultado = $stmt->execute([$nuevoEstado, $idPedido]);

            // Registrar en historial si existe la tabla
            if ($resultado && $estadoActual !== $nuevoEstado) {
                try {
                    $sqlHistorial = "INSERT INTO historial_pedidos (id_pedido, estado_anterior, estado_nuevo, comentario, fecha_cambio) 
                                     VALUES (?, ?, ?, ?, NOW())";
                    $stmtHistorial = $this->pdo->prepare($sqlHistorial);
                    $comentarioFinal = $comentario ?: "Estado cambiado de $estadoActual a $nuevoEstado";
                    $stmtHistorial->execute([$idPedido, $estadoActual, $nuevoEstado, $comentarioFinal]);
                } catch (Exception $e) {
                    error_log("Tabla historial_pedidos no disponible: " . $e->getMessage());
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

    public function obtenerTodosPedidos($limite = 50, $offset = 0)
    {
        try {
            $sql = "SELECT p.*, COUNT(dp.id_detalle) as total_productos
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

    public function obtenerEstadisticas()
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_pedidos,
                        COUNT(CASE WHEN ped_estado = 'pendiente' THEN 1 END) as pedidos_pendientes,
                        COUNT(CASE WHEN ped_estado = 'entregado' THEN 1 END) as pedidos_entregados,
                        COUNT(CASE WHEN ped_estado = 'procesando' THEN 1 END) as pedidos_procesando,
                        COUNT(CASE WHEN ped_estado = 'cancelado' THEN 1 END) as pedidos_cancelados,
                        SUM(CASE WHEN ped_total IS NOT NULL THEN ped_total ELSE 0 END) as total_ventas
                    FROM pedidos 
                    WHERE ped_fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticas: " . $e->getMessage());
            return [
                'total_pedidos' => 0,
                'pedidos_pendientes' => 0,
                'pedidos_entregados' => 0,
                'pedidos_procesando' => 0,
                'pedidos_cancelados' => 0,
                'total_ventas' => 0
            ];
        }
    }

    public function buscarPedidos($criterios = [])
    {
        try {
            $sql = "SELECT p.*, COUNT(dp.id_detalle) as total_productos 
                    FROM pedidos p 
                    LEFT JOIN detalle_pedidos dp ON p.id_pedido = dp.id_pedido 
                    WHERE 1=1";
            $params = [];

            if (!empty($criterios['estado'])) {
                $sql .= " AND p.ped_estado = ?";
                $params[] = $criterios['estado'];
            }

            if (!empty($criterios['fecha_desde'])) {
                $sql .= " AND p.ped_fecha >= ?";
                $params[] = $criterios['fecha_desde'];
            }

            if (!empty($criterios['fecha_hasta'])) {
                $sql .= " AND p.ped_fecha <= ?";
                $params[] = $criterios['fecha_hasta'];
            }

            if (!empty($criterios['cliente'])) {
                $sql .= " AND p.ped_cliente LIKE ?";
                $params[] = '%' . $criterios['cliente'] . '%';
            }

            if (!empty($criterios['numero_factura'])) {
                $sql .= " AND p.ped_numfac LIKE ?";
                $params[] = '%' . $criterios['numero_factura'] . '%';
            }

            $sql .= " GROUP BY p.id_pedido ORDER BY p.ped_fecha DESC";

            if (!empty($criterios['limite'])) {
                $sql .= " LIMIT ?";
                $params[] = intval($criterios['limite']);
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en buscarPedidos: " . $e->getMessage());
            return [];
        }
    }

    public function calcularTotalPedido($idPedido)
    {
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

    public function validarPedido($productos, $total)
    {
        $errores = [];

        // Validar que hay productos
        if (empty($productos)) {
            $errores[] = 'No hay productos en el pedido';
        }

        // Validar cada producto
        foreach ($productos as $index => $producto) {
            if (empty($producto['nombre'])) {
                $errores[] = "Producto en posición $index no tiene nombre";
            }
            if (empty($producto['tipo']) || !in_array($producto['tipo'], ['unidad', 'paca'])) {
                $errores[] = "Producto en posición $index tiene tipo inválido";
            }
            if (!isset($producto['cantidad']) || intval($producto['cantidad']) <= 0) {
                $errores[] = "Producto en posición $index tiene cantidad inválida";
            }
            if (!isset($producto['precio_unitario']) || floatval($producto['precio_unitario']) <= 0) {
                $errores[] = "Producto en posición $index tiene precio inválido";
            }
        }

        // Validar total
        if ($total <= 0) {
            $errores[] = 'El total del pedido debe ser mayor a 0';
        }

        return $errores;
    }

}

