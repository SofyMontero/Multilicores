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
    public function obtenerProductos($categoria) {
        if ($categoria=="0") {
            $conde="";
        }else{
            $conde="and id_cate_producto='$categoria'";
        }
        
    $query = $this->db->connect()->prepare("
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
        FROM productos where id_producto>0 $conde
        ORDER BY id_producto DESC
    ");
    
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
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
}