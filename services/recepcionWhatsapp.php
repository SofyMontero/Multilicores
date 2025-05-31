<?php
// DESHABILITAMOS EL MOSTRAR ERRORES
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
date_default_timezone_set('America/Bogota');
$fechatiempo=date("Y-m-d H:i:s"); 
// session_start(); // Iniciar la sesión para mantener el estado
// require 'vendor/autoload.php';
// IMPORTAMOS LAS LIBRERIAS DE Rivescript
// use \Axiom\Rivescript\Rivescript;

/*
 * VERIFICACION DEL WEBHOOK
 */
// TOKEN QUE QUEREMOS PONER 
$token = 'Multilicoreslicor25';
// RETO QUE RECIBIREMOS DE FACEBOOK
$palabraReto = $_GET['hub_challenge'];
// TOKEN DE VERIFICACION QUE RECIBIREMOS DE FACEBOOK
$tokenVerificacion = $_GET['hub_verify_token'];
// SI EL TOKEN QUE GENERAMOS ES EL MISMO QUE NOS ENVIA FACEBOOK RETORNAMOS EL RETO PARA VALIDAR QUE SOMOS NOSOTROS
if ($token === $tokenVerificacion) {
    echo $palabraReto;
    exit;
}

/*
 * RECEPCION DE MENSAJES
 */
// LEEMOS LOS DATOS ENVIADOS POR WHATSAPP
$respuesta = file_get_contents("php://input");
// CONVERTIMOS EL JSON EN ARRAY DE PHP
$respuesta = json_decode($respuesta, true);

// Verificamos si el mensaje contiene una imagen
if (isset($respuesta['entry'][0]['changes'][0]['value']['messages'][0]['image'])) {
    // // Extraemos el ID de la imagen
     $image = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['image'];
    $image_id = $image['id']; // ID de la imagen para hacer la solicitud de descarga
     $image_caption = isset($image['caption']) ? $image['caption'] : ''; // Captura opcional

    // Ahora obtenemos la URL de descarga de la imagen
    $token = 'EAAhhlSyrHkMBOxLZBq1IkxTs3A8O1yUJWOtk58j0BUv7eUHemP3P6lzWRUE9LohfAqdC9um6yjihIsof6ZARhb1ZBlJ7YZC0E0j4LAWHr77DkLD50KaKVPqGjwazQ6FJ8JRolfZBGtrdAAx8ZAVZCoDMi7uLZBgddffFCKKLx7mrfjck6P0P27wFbn1ewwqUkwpkkAZDZD';
    // Reemplaza con tu token real
    $url = 'https://graph.facebook.com/v22.0/' . $image_id;

    // Hacemos una solicitud GET a la API de WhatsApp para obtener la URL de la imagen
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // Verificamos si la respuesta fue exitosa
    if ($http_code == 200) {
        // Decodificamos la respuesta para obtener la URL de la imagen
        $response_data = json_decode($response, true);

        if (isset($response_data['url'])) {
            $image_url = $response_data['url'];  // URL de la imagen obtenida

            // SEGUNDA SOLICITUD: Descargar la imagen
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $image_url);  // URL de la imagen
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $image_data = curl_exec($curl);  // Imagen binaria
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            // Verificamos si la descarga de la imagen fue exitosa
            if ($http_code == 200 && $image_data !== false) {
                // Definimos la ruta para guardar la imagen
                $image_path = 'imageneswhatsapp/' . $image_id . '.jpg';
                $mensaje=$image_path;
                // Guardamos la imagen en el servidor
                if (file_put_contents($image_path, $image_data) !== false) {
                    $mensaje="Imagen guardada exitosamente en: " . $image_path;
                } else {
                    $mensaje="Error al guardar la imagen.";
                }
            } else {
                $mensaje="Error al descargar la imagen. Código HTTP: " . $http_code;
            }
        } else {
            $mensaje="No se pudo obtener la URL de la imagen.";
        }
    } else {
        $mensaje="Error al obtener la URL de la imagen. Código HTTP: " . $http_code;
    }

    $mensaje=$image_id;

    $conimagen = true;
}
// Verificamos si es un mensaje interactivo
else if (isset($respuesta['entry'][0]['changes'][0]['value']['messages'][0]['interactive'])) {
    // Es un mensaje interactivo, revisamos si es un 'list_reply' o 'button_reply'
    $interactive = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['interactive'];

    if (isset($interactive['list_reply'])) {
        // Es un 'list_reply' (respuesta de lista), extraemos el ID de la opción seleccionada
        $mensaje = $interactive['list_reply']['id'];
    } elseif (isset($interactive['button_reply'])) {
        // Es un 'button_reply' (respuesta de botón), extraemos el ID de la opción seleccionada
        $mensaje = $interactive['button_reply']['id'];
    } else {
        // Otro tipo de mensaje interactivo, puedes agregar más validaciones aquí si es necesario
        $mensaje = "Tipo de interacción no reconocido";
    }
} else {
    // Si no es un mensaje interactivo, seguimos procesando el texto normal
    $mensaje = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
}





// // EXTRAEMOS EL MENSAJE DEL ARRAY
// $mensaje = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
// EXTRAEMOS EL TELEFONO DEL ARRAY
$telefonoCliente = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['from'];
// EXTRAEMOS EL ID DE WHATSAPP DEL ARRAY
$id = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['id'];
// EXTRAEMOS EL TIEMPO DE WHATSAPP DEL ARRAY
$timestamp = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['timestamp'];

// SI HAY UN MENSAJE
if ($mensaje != null) {
    // Conectamos a la base de datos
    // $servername = "localhost";
    // $username = "u713516042_jose2";
    // $password = "Dobarli23@transmillas";
    // $dbname = "u713516042_transmillas2";
    // $con = new mysqli($servername, $username, $password, $dbname);
    //$con->close();

    
    // INICIALIZAMOS RIVESCRIPT Y CARGAMOS LA CONVERSACION
        // $rivescript = new Rivescript();
        // $rivescript->load('confChat.rive');
        // $estadoctual = "";
        // $sesion_acepto = "";

        // if ($mensaje=="9") {

            $respuesta="Hola bienvenido a Multilicores en el soguiente link podras hacer tu pedido
            https://multilicoreschapinero.com/sistema/views/categorias.php";
            require_once "envia.php";
            
            enviar($mensaje, $respuesta, $id, $timestamp, $telefonoCliente);
            
        // }


 
}




    


