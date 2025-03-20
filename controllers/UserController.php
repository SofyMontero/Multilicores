<?php
require_once "../models/User.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario_usuario"];
    $password = $_POST["usuario_clave_1"];
    $email = $_POST["usuario_email"];
    $telefono = $_POST["telefono"];
    $direccion = $_POST["direccion"];
    $nombre = $_POST["nombre"];

    $user = new User();
    if ($user->registerUser($usuario, $password, $email,$telefono, $direccion, $nombre)) {
        header("Location: ../views/user-list.php?success=1");
    } else {
        header("Location: ../views/user-new.html?error=1");
    }
    exit();
}
?>