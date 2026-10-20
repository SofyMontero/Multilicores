<?php
require_once __DIR__ . '/../config/database.env.php';

$cfg = multilicores_db_config('default');
$servername = $cfg['host'];
$username = $cfg['username'];
$password = $cfg['password'];
$dbname = $cfg['dbname'];
$port = $cfg['port'];

mysqli_report(MYSQLI_REPORT_OFF);
if ($servername === 'localhost') {
    $conn = @new mysqli($servername, $username, $password, $dbname);
} else {
    $conn = @new mysqli($servername, $username, $password, $dbname, $port);
}

if ($conn->connect_error && $servername === 'localhost' && strpos($conn->connect_error, '2002') !== false) {
    $conn = @new mysqli('127.0.0.1', $username, $password, $dbname, $port);
}

if ($conn->connect_error) {
    throw new Exception('Error de conexión: ' . $conn->connect_error);
}

$conn->set_charset($cfg['charset']);
