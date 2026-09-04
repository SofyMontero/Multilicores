<?php
$servername = "localhost";
$username = "u633742531_Multilicores25";
$password = "Multilicores2025";
$dbname = "u633742531_Multilicores";

mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli($servername, $username, $password, $dbname, 3306);

if ($conn->connect_error) {
    $conn = @new mysqli('127.0.0.1', $username, $password, $dbname, 3306);
}

if ($conn->connect_error) {
    throw new Exception('Error de conexión: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');