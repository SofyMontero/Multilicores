<?php
// header("Access-Control-Allow-Origin: https://sistema.transmillas.com");
// header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Lista de dominios permitidos
$allowed_origins = [
    'https://8892-179-51-102-105.ngrok-free.app/Multilicores'
    
];

// Obtener el dominio de origen de la solicitud
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Verificar si el dominio está en la lista de permitidos
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}

// Otros encabezados CORS
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejar solicitudes preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Solo responde con el estado 200 para confirmar la configuración CORS
    http_response_code(200);
    exit;
}
// Definir el token esperado (almacenado de forma segura en producción)
$expectedToken = "Multilicoreslicor25";

// Verificar el encabezado Authorization
$headers = getallheaders();
if (!isset($headers['Authorization']) || $headers['Authorization'] !== "Bearer $expectedToken") {
    echo json_encode(['error' => 'Acceso no autorizado. Token inválido.']);
    http_response_code(401); // Código de error para acceso no autorizado
    exit;
}
// Verificar si los datos han sido enviados correctamente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del cuerpo de la solicitud
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar si se enviaron los datos necesarios
    if (!isset($data['telefono']) || !isset($data['numero_guia']) || !isset($data['tipo_alerta'])|| !isset($data['id_guia'])) {
        echo json_encode(['error' => 'Faltan datos: teléfono o número de guía']);
        exit;
    }

    // Datos que recibimos
    $telefonoCliente = $data['telefono'];
    $numeroGuia = $data['numero_guia'];
    $tipo_alerta = $data['tipo_alerta'];
    $id_guia = $data['id_guia'];
    $imagen1 = $data['imagen1'];


    // MENSAJE A ENVIAR

    switch ($tipo_alerta) {
        //se dirigen a recoger
        case '1':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "shipment_confirmation_1",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ]
                ]
            ]);
        break;
        //Servicio recogido
        case '2':


                    // Datos de la plantilla
        $mensaje = json_encode([
            "messaging_product" => "whatsapp",
            "to" => $telefonoCliente,
            "type" => "template",
            "template" => [
                "name" => "servicio_recogido",  // Nombre de la plantilla
                "language" => ["code" => "es"],  // Idioma de la plantilla
                "components" => [
                    [
                        "type" => "body",  // El cuerpo de la plantilla
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $numeroGuia  // Parámetro dinámico para el número de guía
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        break;
        //No se recogio Servicio 
        case '3':


                    $mensaje = json_encode([
                        "messaging_product" => "whatsapp",
                        "to" => $telefonoCliente,  // Número de teléfono del cliente
                        "type" => "template",
                        "template" => [
                            "name" => "no_recogido_3",  // Nombre de tu plantilla en WhatsApp
                            "language" => [
                                "code" => "es"  // Código de idioma, en este caso español (es)
                            ],
                            "components" => [
                                [
                                    "type" => "body",
                                    "parameters" => [
                                        [
                                            "type" => "text",
                                            "text" => $id_guia  // Primer parámetro {{1}}
                                        ],
                                        [
                                            "type" => "text",
                                            "text" => $id_guia  // Segundo parámetro {{2}}
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]);
        break;
                
        //Se dirigen a entrega             
        case '4':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "a_entregar_4",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $numeroGuia  // Pasamos el número de guía como parámetro {{1}}
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        break;
        
        //No se entrego Servicio  
        case '5':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "no_entregado5",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $numeroGuia  // Pasamos el número de guía como parámetro {{1}}
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        break;
        //Se entrego Servicio  
        case '6':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "entregado_6",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $numeroGuia  // Pasamos el número de guía como parámetro {{1}}
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        break;
        //Se entrego Servicio  
        case '7':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "recoger_oficina7",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $numeroGuia  // Pasamos el número de guía como parámetro {{1}}
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        break;
        //Se entrego Servicio  
        case '8':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "se_devolvera8",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $numeroGuia  // Pasamos el número de guía como parámetro {{1}}
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        break;
                //Se entrego Servicio  
   
        case '9':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "9",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ]
                ]
            ]);
        break;
        case '10':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "10",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ]
                ]
            ]);
        break;
        case '11':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "11",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ]
                ]
            ]);
        break;
        case '12':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "12",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ]
                ]
            ]);
        break;
        case '13':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "13",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ]
                ]
            ]);
        break;
        case '14':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "14",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ]
                ]
            ]);
        break;
        case '15':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "15",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ]
                ]
            ]);
        break;
        case '16':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "16",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ]
                ]
            ]);
        break;
        
        case '17':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "statement_available_1",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $id_guia  // Pasamos el número de guía como parámetro {{1}}
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

        break;
        case '18':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "18",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $numeroGuia  // Pasamos el número de guía como parámetro {{1}}
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

        break;
        case '19':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "19",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $numeroGuia  // Pasamos el número de guía como parámetro {{1}}
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

        break;
        case '20':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "27",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ]
                ]
            ]);


        break;
        case '21':

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "21",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ]
                ]
            ]);

        break;
        case '22':
                if (!isset($data['imagen1']) ) {
                    echo json_encode(['error' => 'Faltan datos: telefono, imagen']);
                    exit;
                }
            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $telefonoCliente,  // Número de teléfono del cliente
                "type" => "template",
                "template" => [
                    "name" => "22",  // Nombre de tu plantilla en WhatsApp
                    "language" => [
                        "code" => "es"  // Código de idioma, en este caso español (es)
                    ],
                    "components" => [
                        [
                            "type" => "header",  // El tipo 'header' permite incluir imágenes
                            "parameters" => [
                                [
                                    "type" => "image",
                                    "image" => [
                                        "link" => "https://historico.transmillas.com/". $imagen1.""  // URL de la imagen
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

        break;
        case '23':
                if (!isset($data['imagen1']) ) {
                    echo json_encode(['error' => 'Faltan datos: telefono, imagen']);
                    exit;
                }
                $mensaje = json_encode([
                    "messaging_product" => "whatsapp",
                    "to" => $telefonoCliente,  // Número de teléfono del cliente
                    "type" => "template",
                    "template" => [
                        "name" => "23",  // Nombre de tu plantilla en WhatsApp
                        "language" => [
                            "code" => "es"  // Código de idioma, en este caso español (es)
                        ],
                        "components" => [
                            [
                                "type" => "body",
                                "parameters" => [
                                    [
                                        "type" => "text",
                                        "text" => $data['imagen1']  // Pasamos el número de guía como parámetro {{1}}
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);
                case '24':
                    if (!isset($data['imagen1']) ) {
                        echo json_encode(['error' => 'Faltan datos: telefono, imagen']);
                        exit;
                    }
                    $mensaje = json_encode([
                        "messaging_product" => "whatsapp",
                        "to" => $telefonoCliente,  // Número de teléfono del cliente
                        "type" => "template",
                        "template" => [
                            "name" => "24",  // Nombre de tu plantilla en WhatsApp
                            "language" => [
                                "code" => "es"  // Código de idioma, en este caso español (es)
                            ],
                            "components" => [
                                [
                                    "type" => "body",
                                    "parameters" => [
                                        [
                                            "type" => "text",
                                            "text" => $data['imagen1']  // Pasamos el número de guía como parámetro {{1}}
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]);
    

                break;
                case '25_':
                    if (!isset($data['imagen1']) ) {
                        echo json_encode(['error' => 'Faltan datos: telefono, imagen']);
                        exit;
                    }
                    $mensaje = json_encode([
                        "messaging_product" => "whatsapp",
                        "to" => $telefonoCliente,  // Número de teléfono del cliente
                        "type" => "template",
                        "template" => [
                            "name" => "25_",  // Nombre de tu plantilla en WhatsApp
                            "language" => [
                                "code" => "es"  // Código de idioma, en este caso español (es)
                            ],
                            "components" => [
                                [
                                    "type" => "body",
                                    "parameters" => [
                                        [
                                            "type" => "text",
                                            "text" => $data['imagen1']  // Pasamos el número de guía como parámetro {{1}}
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]);
    

                break;
                //Servicio recogido
                case '26':


                                // Datos de la plantilla
                    $mensaje = json_encode([
                        "messaging_product" => "whatsapp",
                        "to" => $telefonoCliente,
                        "type" => "template",
                        "template" => [
                            "name" => "26",  // Nombre de la plantilla
                            "language" => ["code" => "es"],  // Idioma de la plantilla
                            "components" => [
                                [
                                    "type" => "body",  // El cuerpo de la plantilla
                                    "parameters" => [
                                        [
                                            "type" => "text",
                                            "text" => $numeroGuia  // Parámetro dinámico para el número de guía
                                        ]
                                    ]
                                ]
                            ]
                        ]
                ]);
    }

        // TOKEN QUE NOS DA FACEBOOK
        $token = 'EAAGYjsYz8Q8BO9ZCkhb0tul3JIiNwsJsHZATJC7oRIkJRYSUSHI8PjSRI3ZCoMyVCqpPzHhmvzZCydrtmqZB5ihEtyah6Cfhf2NyQyp80HYOFOKvFToSSKdV2A1WG87zzhF55zS0HT23DL7ZBZACijReFYJAOCFYZCXWPnxhRBHpYsGKnfbCzDLCQqZBbxpCx3xYD';

        // IDENTIFICADOR DE NÚMERO DE TELÉFONO
        //  $telefonoID = '431356130053619';
        $telefonoID = '430240436843311';

        // URL A DONDE SE MANDARÁ EL MENSAJE
        $url = 'https://graph.facebook.com/v15.0/' . $telefonoID . '/messages';
            // Cabeceras para la solicitud
        $header = array(
            "Authorization: Bearer " . $token,
            "Content-Type: application/json",
        );

        // Inicializar cURL
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $mensaje);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = json_decode(curl_exec($curl), true);

        // Obtener el código de respuesta HTTP
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Cerrar cURL
        curl_close($curl);

        // Establecer la zona horaria de Colombia
        date_default_timezone_set('America/Bogota');

        // Obtener la fecha y hora actual en el formato que desees
        $fechaHoraColombia = date('Y-m-d H:i:s');

        // Mostrar la fecha y hora
        // echo "Fecha y hora actual en Colombia: " . $fechaHoraColombia;
        $response_str = print_r($response, true);
        // Verificar el resultado del envío
        if ($status_code == 200) {
            echo json_encode(['success' => 'Mensaje enviado con éxito ']);
            // file_put_contents('alertas_log.txt', "Mensaje tipo ".$tipo_alerta." para telefono ".$telefonoCliente." guia numero ".$numeroGuia." fecha ".$fechaHoraColombia." \n", FILE_APPEND);
            
            $servername = "localhost";
            $username = "u713516042_jose2";
            $password = "Dobarli23@transmillas";
            $dbname = "u713516042_transmillas2";
            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            //INSERTAMOS LOS REGISTROS DEL ENVIO DEL WHATSAPP
            $sql = "INSERT INTO registro "
                . "(mensaje_recibido,mensaje_enviado,id_wa,timestamp_wa,telefono_wa,tipo,id_servicio,fecha_hora) VALUES "
                . "('' ,'" . $tipo_alerta . "','','','" . $telefonoCliente . "','Alerta','".$id_guia."','$fechaHoraColombia');";
            $conn->query($sql);
            $conn->close();
        
        } else {
            // file_put_contents('alertas_log.txt', "Mensaje tipo ".$tipo_alerta." para telefono ".$telefonoCliente." guia numero ".$numeroGuia." fecha ".$fechaHoraColombia." \n Error al enviar el mensaje', 'status_code' => $status_code"." NO ENVIADO ", FILE_APPEND);

            echo json_encode(['error' => 'Error al enviar el mensaje', 'status_code' => $status_code]);
        }


} else {
    echo json_encode(['error' => 'Método no permitido']);
    file_put_contents('alertas_log.txt', "Mensaje tipo ".$tipo_alerta." para telefono ".$telefonoCliente." guia numero ".$numeroGuia." fecha ".$fechaHoraColombia." \n Error al enviar el mensaje', 'status_code' => $status_code"."NO ENVIADO", FILE_APPEND);

}
?>