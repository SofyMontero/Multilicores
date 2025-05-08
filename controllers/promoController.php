<?php
require_once "../models/Promo.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar los datos del formulario
    $id = isset($_POST["id"]) ? $_POST["id"] : null;
    $metod = isset($_POST["metod"]) ? $_POST["metod"] : null;
    $titulo = $_POST["titulo"];
    $patrocinador = $_POST["patrocinador"];
    $estado = $_POST["estado"];
    // $imagen = $_POST["imagen"];
    $descripcion = $_POST["descripcion"];
    $metod = $_POST["action"];

    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
        $nombreOriginal = $_FILES["imagen"]["name"];
        $tmpPath = $_FILES["imagen"]["tmp_name"];
    
        $nombreImagen = uniqid("img_") . "_" . basename($nombreOriginal); // evita colisiones
        $destino = "../uploads/promociones" . $nombreImagen; // asegúrate de que esta carpeta exista y tenga permisos
    
        if (!move_uploaded_file($tmpPath, $destino)) {
            die("Error al mover la imagen al servidor.");
        }
    } else {
        die("No se subió ninguna imagen o hubo un error al subirla.");
    }



    $user = new Promo();





    if ($metod=="insert") {
        // Si no hay ID, significa que estamos registrando un nuevo usuario
        if ($user->registerPromo($titulo, $patrocinador, $estado, $nombreImagen, $descripcion)) {
            header("Location: ../views/user-list.php?success=1");
        } else {
            header("Location: ../views/user-new.html?error=1");
        }
    }elseif ($metod=="update") {
        // Si el ID está presente, significa que estamos editando
        if ($user->updatePromo($id, $titulo, $patrocinador, $estado, $nombreImagen, $descripcion)) {
            header("Location: ../views/user-list.php?success=edit");
        } else {
            header("Location: ../views/user-edit.php?id=$id&error=1");
        }
    } elseif ($metod=="delete") {
        // Si el ID está presente, significa que estamos editando
        if ($user->deletePromo($id)) {
            header("Location: ../views/user-list.php?success=edit");
        } else {
            header("Location: ../views/user-list.php?id=$id&error=1");
        }
    }
    exit();
}
?>