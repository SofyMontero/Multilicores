<?php
require_once "database.php";

class Promo {
    private $db;

    // public function __construct() {
    //     $this->db = new Database();
    // }
    public function __construct() {
        $database = new Database();     // Esto crea el objeto de tu clase
        $this->db = $database->connect(); // Esto guarda el PDO
    }

    // public function getUserByUsername($usuario) {
    //     $query = $this->db->connect()->prepare("SELECT * FROM users WHERE usuario = :usuario");
    //     $query->execute(["usuario" => $usuario]);
    //     return $query->fetch(PDO::FETCH_ASSOC);
    // }
    // public function registerPromo($titulo, $patrocinador, $estado, $imagen, $descripcion) {
        
    //     $query = $this->db->connect()->prepare("INSERT INTO `promos`( `pro_nombre`, `pro_patrocinador`, `pro_estado`, `pro_descripcion`, `pro_imagen`) VALUES (:titulo,:patrocinador,:estado,:descripcion,:imagen)");
    //     return $query->execute([
    //         "titulo" => $titulo,
    //         "patrocinador" => $patrocinador,
    //         "estado" => $estado,
    //         "imagen" => $imagen,
    //         "descripcion" => $descripcion
    //     ]);
    // }

    public function registerPromo($titulo, $patrocinador, $estado, $imagen, $descripcion) {
        $query = $this->db->prepare("INSERT INTO `promos`(`pro_nombre`, `pro_patrocinador`, `pro_estado`, `pro_descripcion`, `pro_imagen`) 
                                     VALUES (:titulo, :patrocinador, :estado, :descripcion, :imagen)");
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

    public function getAllPromos() {
        $stmt = $this->db->prepare("SELECT * FROM promos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function enviarPromo($idPromo, $descripcion, $imagen): array {
        $clientes = $this->getClientes();

        $resultados = [];

        foreach ($clientes as $cliente) {
            $telefono = $cliente['cli_telefono'];

            $postData = [
                'telefono' => $telefono,
                'texto' => $descripcion,
                'imagen1' => $imagen,
                'plantilla' => '2' // opcional, según tu lógica
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "../enviarWhatsapp.php");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/x-www-form-urlencoded",
                "Authorization: Bearer Multilicoreslicor25"
            ]);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            $resultados[] = [
                'cliente' => $cliente['cli_nombre'],
                'telefono' => $telefono,
                'resultado' => $error ?: $response
            ];
        }

        return $resultados;
    }
    
    public function getClientes() {
    $stmt = $this->db->prepare("SELECT * FROM clientes");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}


?>