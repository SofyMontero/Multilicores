<?php
// WhatsappSender.php

class WhatsappSender {
    private $conn;
    private $logFile = 'whatsapp_log.txt';

    public function __construct($conn) {
        $this->conn = $conn;
        $this->log("Instancia creada de WhatsappSender");
    }

    private function log($msg) {
        $fecha = date("Y-m-d H:i:s");
        file_put_contents($this->logFile, "[$fecha] $msg\n", FILE_APPEND);
    }

    public function enviar($recibido, $enviado, $idWA, $timestamp, $telefonoCliente) {
        $this->log("Intentando enviar mensaje a $telefonoCliente con ID WA $idWA");

        $sqlCantidad = "SELECT COUNT(id) AS cantidad FROM registro WHERE id_wa='" . $idWA . "';";
        $resultCantidad = $this->conn->query($sqlCantidad);

        if ($resultCantidad) {
            $rowCantidad = $resultCantidad->fetch_row();
            $cantidad = $rowCantidad[0];
        } else {
            $this->log("Error en consulta SQL: " . $this->conn->error);
            return;
        }

        if ($cantidad == 0) {
            $token = 'TOKEN_AQUI';
            $telefonoID = '599178349953891';
            $url = 'https://graph.facebook.com/v22.0/' . $telefonoID . '/messages';

            $mensaje = json_encode([
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $telefonoCliente,
                "type" => "text",
                "text" => [
                    "body" => $enviado,
                    "preview_url" => true
                ]
            ]);

            $header = [
                "Authorization: Bearer " . $token,
                "Content-Type: application/json"
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $mensaje);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $this->log("Respuesta del API: $response - Status: $status");

            $fechaHora = date('Y-m-d H:i:s');
            $sql = "INSERT INTO registro (mensaje_recibido, mensaje_enviado, id_wa, timestamp_wa, telefono_wa, fecha_hora) 
                    VALUES ('$recibido', '$enviado', '$idWA', '$timestamp', '$telefonoCliente', '$fechaHora')";

            if (!$this->conn->query($sql)) {
                $this->log("Error al insertar en la base de datos: " . $this->conn->error);
            }
        } else {
            $this->log("Mensaje con ID WA $idWA ya fue enviado previamente.");
        }
    }
}