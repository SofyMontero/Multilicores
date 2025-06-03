<?php
// ini_set('display_errors', 0);
// ini_set('display_startup_errors', 0);
// error_reporting(E_ALL);
// date_default_timezone_set('America/Bogota');

function write_log($contenido) {
    $fechatiempo = date("Y-m-d H:i:s");
    file_put_contents("log_webhook.txt", "[$fechatiempo] $contenido\n", FILE_APPEND);
}

// VALIDAR WEBHOOK
$token = 'Multilicoreslicor25';
if (isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] === $token) {
    echo $_GET['hub_challenge'];
    write_log("Webhook verificado correctamente.");
    exit;
}

// LEER Y DECODIFICAR MENSAJE ENTRANTE
$inputRaw = file_get_contents("php://input");
$respuesta = json_decode($inputRaw, true);

// Log del JSON recibido completo
write_log("Entrada RAW: " . $inputRaw);

// Validar si el mensaje es de texto
if (!isset($respuesta['entry'][0]['changes'][0]['value']['messages'])) {
    write_log("No es un mensaje entrante válido.");
    exit;
}

$mensajeData = $respuesta['entry'][0]['changes'][0]['value']['messages'][0];
if (!isset($mensajeData['text']['body'])) {
    write_log("El mensaje no contiene texto (puede ser imagen, audio, etc).");
    exit;
}

$mensaje = $mensajeData['text']['body'];
$telefonoCliente = $mensajeData['from'];
$id = $mensajeData['id'];
$timestamp = $mensajeData['timestamp'];

require_once 'conexion.php';
require_once 'WhatsappSender.php';

$sender = new WhatsappSender($conn);

// Log del mensaje recibido
write_log("Mensaje recibido de $telefonoCliente: $mensaje");

if ($mensaje != null) {
    
    $respuestaTexto = "    
    🍷 ¡Bienvenido a Multilicores! 🥂\n
    \nTu tienda favorita de licores está a un clic 🛒
    \nHaz tu pedido fácil y rápido aquí:
    \n👉 https://multilicoreschapinero.com/sistema/views/categorias.php";

    // require_once "enviar.php";
    // enviar($mensaje, $respuestaTexto, $id, $timestamp, $telefonoCliente);
    $sender->enviar($mensaje, $respuestaTexto, $id, $timestamp, $telefonoCliente);
    write_log("Mensaje de respuesta enviado a $telefonoCliente");
}



    


