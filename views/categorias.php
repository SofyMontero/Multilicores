<?php
include_once "header.php";
require_once "../models/database.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../models/ProductoModel.php";

// Obtener productos desde la base de datos
$producto = new Producto();
$productos = $producto->obtenerCategorias(); // Asegúrate de tener este método en tu modelo

$importados = $_GET['importados'] ?? null;
?>

<div class="container mt-5">
    <h2 class="text-center mb-4"><i class="fas fa-tags"></i> Selecciona una Categoría</h2>
    <div class="row">
       <?php if (!empty($productos)): ?>
            <?php $contador = 1; foreach ($productos as $prod): ?>
                <!-- AGUARDIENTE -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <a href="catalogo.php?categoria=aguardiente">
                            <img src="<?php echo $prod['imagen_categoria']; ?>" class="card-img-top" alt="Aguardientes">
                        </a>
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo $prod['nombre_categoria']; ?></h5>
                            <a href="catalogo.php?categoria=<?php echo $prod['id_categoria']; ?>&nombre=<?php echo $prod['nombre_categoria']; ?>" class="btn btn-success w-100">Ver productos</a>
                        </div>
                    </div>
                </div>


                                    
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No hay productos registrados</td></tr>
        <?php endif; ?>


    </div>
</div>

<?php include_once "footer.php"; ?>