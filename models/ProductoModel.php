<?php
require_once "database.php";

class Producto {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Método para insertar producto desde Excel
    public function insertarProducto($precioUnidad, $idCategoria, $precioPaca, $descripcion, $cantidadPaca,$imagen  ) {
        $query = $this->db->connect()->prepare("
            INSERT INTO productos 
            (precio_unidad_producto, id_cate_producto, precio_paca_producto, descripcion_producto, cantidad_paca_producto,imagen_producto)
            VALUES (:precioUnidad, :idCategoria, :precioPaca, :descripcion, :cantidadPaca, :imagen)
        ");

        return $query->execute([
            "precioUnidad"  => $precioUnidad,
            "idCategoria"   => $idCategoria,
            "precioPaca"    => $precioPaca,
            "descripcion"   => $descripcion,
            "cantidadPaca"  => $cantidadPaca,
            "imagen"  => $imagen
        ]);
    }

    // Si necesitas más funciones como listar, actualizar, etc., puedo ayudarte a agregarlas
    public function obtenerProductos() {
    $query = $this->db->connect()->prepare("
        SELECT 
            id_producto,
            descripcion_producto AS descripcion,
            precio_unidad_producto AS precio_unidad,
            precio_paca_producto AS precio_paca,
            cantidad_paca_producto AS cantidad_paca,
            id_cate_producto AS id_categoria,
            imagen_producto AS imagen
        FROM productos
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