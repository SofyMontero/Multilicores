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

            // $postData = [
            //     'telefono' => "$telefono",
            //     'texto' => "$descripcion",
            //     'imagen1' => "$imagen",
            //     'plantilla' => '2' // opcional, según tu lógica
            // ];



            	if (preg_match('/^\d{10}$/', $telefono)) {
                    // echo "La variable tiene exactamente 10 números.";

                        // URL de la API
                    $url = "https://multilicoreschapinero.com/sistema/services/enviarWhatsapp.php";

                    // Datos que enviarás en la solicitud
                    $data = array(
                            'telefono' => "3125215864",
                            'texto' => "hola si",
                            'imagen1' => "hola no hay",
                            'plantilla' => '2'
                    );


                    // Convertir los datos a formato JSON
                    $data_json = json_encode($data);

                    // Iniciar una sesión cURL
                    $curl = curl_init();

                    // Configurar las opciones cURL
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url, // URL de la API
                        CURLOPT_RETURNTRANSFER => true, // Retorna el resultado como cadena
                        CURLOPT_POST => true, // Indica que la solicitud será POST
                        CURLOPT_POSTFIELDS => $data_json, // Los datos que se envían en la solicitud
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json', // Tipo de contenido
                            'Authorization: Bearer Multilicoreslicor25' // Si la API requiere autenticación
                        ),
                    ));

                    // Ejecutar la solicitud y obtener la respuesta
                    $response = curl_exec($curl);
                    $error = curl_error($curl);
                    // Manejar errores cURL

                     $resultados[] = [
                        'cliente' => $cliente['cli_nombre'],
                        'telefono' => $telefono,
                        'resultado' => $error ?: $response
                     ];


                    // Cerrar la sesión cURL
                    curl_close($curl);
                } else {
                    echo "La variable no cumple con el formato.";
                }

            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, "https://multilicoreschapinero.com/sistema/services/enviarWhatsapp.php");
            // curl_setopt($ch, CURLOPT_POST, true);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            // curl_setopt($ch, CURLOPT_HTTPHEADER, [
            //     "Content-Type: application/x-www-form-urlencoded",
            //     "Authorization: Bearer Multilicoreslicor25"
            // ]);

            // $response = curl_exec($ch);
            // $error = curl_error($ch);
            // curl_close($ch);

            // $resultados[] = [
            //     'cliente' => $cliente['cli_nombre'],
            //     'telefono' => $telefono,
            //     'resultado' => $error ?: $response
            // ];
        }

        return $resultados;
    }
    
    public function getClientes() {
    $stmt = $this->db->prepare("SELECT * FROM clientes");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

function enviarAlertaWhat($numguia,$telefono,$tipo,$idservi){

	if (preg_match('/^\d{10}$/', $telefono)) {
		// echo "La variable tiene exactamente 10 números.";

			// URL de la API
		$url = "https://multilicoreschapinero.com/sistema/services/enviarWhatsapp.php";

		// Datos que enviarás en la solicitud
		$data = array(
			"numero_guia" => "$numguia", // Número de guía
			"telefono" => "$telefono",  // Número de teléfono 3160490959
			// "telefono" => "3107781913",  // Número de teléfono 3160490959
			"tipo_alerta" => "$tipo",
			"id_guia" => "$idservi"
		);


		// Convertir los datos a formato JSON
		$data_json = json_encode($data);

		// Iniciar una sesión cURL
		$curl = curl_init();

		// Configurar las opciones cURL
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // URL de la API
			CURLOPT_RETURNTRANSFER => true, // Retorna el resultado como cadena
			CURLOPT_POST => true, // Indica que la solicitud será POST
			CURLOPT_POSTFIELDS => $data_json, // Los datos que se envían en la solicitud
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json', // Tipo de contenido
				'Authorization: Bearer Multilicoreslicor25' // Si la API requiere autenticación
			),
		));

		// Ejecutar la solicitud y obtener la respuesta
		$response = curl_exec($curl);

		// Manejar errores cURL
		if($response === false) {
			$error = curl_error($curl);
			echo "Error en la solicitud: $error";
		} else {
			// Decodificar la respuesta (si es JSON)
			$response_data = json_decode($response, true);
			
			// Mostrar la respuesta
			echo "Respuesta de la API: ";
			print_r($response_data);
		}

		// Cerrar la sesión cURL
		curl_close($curl);
	} else {
		echo "La variable no cumple con el formato.";
	}




 }	


?>