<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: index.html?error=Debes%20iniciar%20sesión");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <script>
        window.location.href = "../views/home.php";
    </script>
</head>
<body>
</body>
</html>