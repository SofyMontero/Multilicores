<?php
class solicitud
{
    private $db;
    private $pdo;

    public function __construct()
    {
        $this->db = new Database();
        $this->pdo = $this->db->connect();
    }

    /**
     * Obtener todos los pedidos pendientes
     */
     public function obtenerPedidosPendientes()
    {
        $query = $this->pdo->prepare("
            SELECT 
                id_pedido, 
                ped_numfac, 
                ped_cliente, 
                ped_fecha, 
                ped_total, 
                ped_estado,
                ped_numCliente
            FROM pedidos 
            WHERE ped_estado = 'pendiente' OR ped_estado = '1' OR ped_estado = 1
            ORDER BY ped_fecha DESC
        ");
        
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los pedidos aceptados
     */
    public function obtenerPedidosAceptados()
    {
        $query = $this->pdo->prepare("
            SELECT 
                id_pedido, 
                ped_numfac, 
                ped_cliente, 
                ped_fecha, 
                ped_total, 
                ped_estado,
                ped_numCliente
            FROM pedidos 
            WHERE ped_estado = 'aceptado' OR ped_estado = '2' OR ped_estado = 2
            ORDER BY ped_fecha DESC
        ");
        
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener productos de un pedido específico
     */
    public function obtenerProductosPedido($pedido_id)
    {
        $query = $this->pdo->prepare("
            SELECT 
                id_producto,
                nombre_producto,
                cantidad,
                precio_unitario,
                subtotal
            FROM detalle_pedidos
            WHERE id_pedido = :pedido_id
            ORDER BY id_detalle
        ");
        
        $query->execute(['pedido_id' => $pedido_id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un pedido específico por ID
     */
    public function obtenerPedidoPorId($pedido_id)
    {
        $query = $this->pdo->prepare("
            SELECT * FROM pedidos 
            WHERE id_pedido = :pedido_id
        ");
        
        $query->execute(['pedido_id' => $pedido_id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Aceptar un pedido (versión simplificada)
     */
    public function aceptarPedido($pedido_id)
    {
        $query = $this->pdo->prepare("
            UPDATE pedidos 
            SET ped_estado = 'aceptado'
            WHERE id_pedido = :pedido_id
        ");

        return $query->execute(['pedido_id' => $pedido_id]);
    }

    /**
     * Rechazar un pedido (versión simplificada)
     */
    public function rechazarPedido($pedido_id)
    {
        $query = $this->pdo->prepare("
            UPDATE pedidos 
            SET ped_estado = 'rechazado'
            WHERE id_pedido = :pedido_id
        ");

        return $query->execute(['pedido_id' => $pedido_id]);
    }

    /**
     * Cambiar estado de un pedido
     */
    public function cambiarEstadoPedido($pedido_id, $nuevo_estado)
    {
        $query = $this->pdo->prepare("
            UPDATE pedidos 
            SET ped_estado = :nuevo_estado
            WHERE id_pedido = :pedido_id
        ");

        return $query->execute([
            'pedido_id' => $pedido_id,
            'nuevo_estado' => $nuevo_estado
        ]);
    }

    /**
     * Contar pedidos por estado
     */
    public function contarPedidosPorEstado($estado)
    {
        if ($estado === 'pendiente') {
            $query = $this->pdo->prepare("
                SELECT COUNT(*) as total 
                FROM pedidos 
                WHERE ped_estado = 'pendiente' OR ped_estado = '1' OR ped_estado = 1
            ");
        } else {
            $query = $this->pdo->prepare("
                SELECT COUNT(*) as total 
                FROM pedidos 
                WHERE ped_estado = :estado
            ");
            $query->bindParam(':estado', $estado);
        }
        
        $query->execute();
        $resultado = $query->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    /**
     * Obtener estadísticas de pedidos
     */
    public function obtenerEstadisticasPedidos()
    {
        $query = $this->pdo->prepare("
            SELECT 
                ped_estado,
                COUNT(*) as cantidad,
                SUM(ped_total) as total_ventas
            FROM pedidos 
            GROUP BY ped_estado
        ");
        
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar pedidos por cliente
     */
    public function buscarPedidosPorCliente($cliente)
    {
        $query = $this->pdo->prepare("
            SELECT 
                id_pedido, 
                ped_numfac, 
                ped_cliente, 
                ped_fecha, 
                ped_total, 
                ped_estado
            FROM pedidos 
            WHERE ped_cliente LIKE :cliente
            ORDER BY ped_fecha DESC
        ");
        
        $query->execute(['cliente' => "%$cliente%"]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener pedidos por rango de fechas
     */
    public function obtenerPedidosPorFecha($fecha_inicio, $fecha_fin, $estado = null)
    {
        $sql = "
            SELECT 
                id_pedido, 
                ped_numfac, 
                ped_cliente, 
                ped_fecha, 
                ped_total, 
                ped_estado
            FROM pedidos 
            WHERE DATE(ped_fecha) BETWEEN :fecha_inicio AND :fecha_fin
        ";
        
        $params = [
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ];
        
        if ($estado) {
            $sql .= " AND ped_estado = :estado";
            $params['estado'] = $estado;
        }
        
        $sql .= " ORDER BY ped_fecha DESC";
        
        $query = $this->pdo->prepare($sql);
        $query->execute($params);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
       public function obtenerPedidosRechazados()
    {
        $query = $this->pdo->prepare("
            SELECT 
                id_pedido, 
                ped_numfac, 
                ped_cliente, 
                ped_fecha, 
                ped_total, 
                ped_estado,
                ped_numCliente
            FROM pedidos 
            WHERE ped_estado = 'rechazado' OR ped_estado = '3' OR ped_estado = 3
            ORDER BY ped_fecha DESC
        ");
        
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    
}