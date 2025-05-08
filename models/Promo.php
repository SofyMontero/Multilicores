<?php
require_once "database.php";

class Promo {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // public function getUserByUsername($usuario) {
    //     $query = $this->db->connect()->prepare("SELECT * FROM users WHERE usuario = :usuario");
    //     $query->execute(["usuario" => $usuario]);
    //     return $query->fetch(PDO::FETCH_ASSOC);
    // }
    public function registerPromo($titulo, $patrocinador, $estado, $imagen, $descripcion) {
        
        $query = $this->db->connect()->prepare("INSERT INTO `promos`( `pro_nombre`, `pro_patrocinador`, `pro_estado`, `pro_descripcion`, `pro_imagen`) VALUES (:titulo,:patrocinador,:estado,:descripcion,:imagen)");
        return $query->execute([
            "titulo" => $titulo,
            "patrocinador" => $patrocinador,
            "estado" => $estado,
            "imagen" => $imagen,
            "descripcion" => $descripcion
        ]);
    }
    public function updatePromo($id, $titulo, $patrocinador, $estado, $imagen, $descripcion) {
      
        $query = $this->db->connect()->prepare(" UPDATE users 
        SET usuario = :usuario, 
            password = :password, 
            pro_nombre = :titulo, 
            pro_patrocinador = :patrocinador, 
            pro_estado = :estado, 
            pro_descripcion = :descripcion 
            pro_imagen=:imagen
        WHERE id = :id");
        return $query->execute([
            "id" => $id,
            "titulo" => $titulo,
            "patrocinador" => $patrocinador,
            "estado" => $estado,
            "imagen" => $imagen,
            "descripcion" => $descripcion
        ]);
    }
}


?>