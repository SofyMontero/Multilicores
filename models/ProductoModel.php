<?php
require_once "database.php";

class Producto {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Método para insertar producto desde Excel
    public function insertarProducto(
                        $codigo_producto,
                        $descripcion_producto,
                        $cantidad_paca_producto,
                        $precio_unidad,
                        $precio_paca,
                        $id_cate_producto,
                        $acti_Unidad,
                        $imagen_producto,
                        $estado_producto) {
        $query = $this->db->connect()->prepare("
            INSERT INTO productos 
            (

            `codigo_productos`, 
            `descripcion_producto`,
            `cantidad_paca_producto`,
            `precio_unidad_producto`,
            `precio_paca_producto`,
            `id_cate_producto`,
            `acti_Unidad`, 
            `imagen_producto`,
            `estado_producto`
            
            )
            VALUES (
                :codigo_producto,
                :descripcion_producto,
                :cantidad_paca_producto,
                :precio_unidad,
                :precio_paca,
                :id_cate_producto,
                :acti_Unidad,
                :imagen_producto,
                :estado_producto
            )
        ");

        return $query->execute([
                "codigo_producto" =>$codigo_producto,
                "descripcion_producto" =>$descripcion_producto,
                "cantidad_paca_producto" =>$cantidad_paca_producto,
                "precio_unidad" =>$precio_unidad,
                "precio_paca" =>$precio_paca,
                "id_cate_producto" =>$id_cate_producto,
                "acti_Unidad" =>$acti_Unidad,
                "imagen_producto" =>$imagen_producto,
                "estado_producto" =>$estado_producto
        ]);
    }

    // Si necesitas más funciones como listar, actualizar, etc., puedo ayudarte a agregarlas
    public function obtenerProductos($categoria, $limit = 12, $offset = 0) {
    $condicion = "";
    $params = [];

    if ($categoria !== "0") {
        $condicion = "AND id_cate_producto = ?";
        $params[] = $categoria;
    }

    // Forzamos que limit y offset sean enteros
    $limit = (int)$limit;
    $offset = (int)$offset;

    $sql = "
        SELECT 
            `id_producto`, 
            `precio_unidad_producto`, 
            `id_cate_producto`, 
            `precio_paca_producto`, 
            `descripcion_producto`, 
            `cantidad_paca_producto`, 
            `imagen_producto`, 
            `estado_producto`,
            `acti_Unidad`,
            `codigo_productos`
        FROM productos 
        WHERE id_producto > 0 $condicion
        ORDER BY id_producto DESC
        LIMIT $limit OFFSET $offset
    ";

    $query = $this->db->connect()->prepare($sql);
    $query->execute($params);

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

public function contarProductos($categoria) {
    $condicion = "";
    $params = [];

    if ($categoria !== "0") {
        $condicion = "AND id_cate_producto = ?";
        $params[] = $categoria;
    }

    $sql = "SELECT COUNT(*) FROM productos WHERE id_producto > 0 $condicion";

    $query = $this->db->connect()->prepare($sql);
    $query->execute($params);

    return (int)$query->fetchColumn();
}

 public function obtenerCategorias() {
    $query = $this->db->connect()->prepare("
    SELECT `id_categoria`,
    `nombre_categoria`, 
    `imagen_categoria`
    FROM `categorias` 
    ");
    
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
 public function buscarProducto($producto) {
    $query = $this->db->connect()->prepare("
    SELECT `descripcion_producto`
    FROM `productos` 
    WHERE `descripcion_producto` LIKE :producto
    ");

    $query->execute([
        "producto" => "%$producto%"
    ]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
}