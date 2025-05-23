<?php 
include_once "header.php";
require_once "../models/database.php";
?>
<form action="../controller/ProductoController.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="archivo_excel" accept=".xlsx,.xls" required>
    <button type="submit" name="importar">Importar Productos</button>
</form>


<?php include_once "footer.php"; ?>