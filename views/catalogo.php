<?php 
include_once "header.php";
require_once "../models/database.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../models/ProductoModel.php";
$categoria=$_GET['categoria'] ;
$nombre=$_GET['nombre'] ;

// Obtener productos desde la base de datos
$producto = new Producto();
$productos = $producto->obtenerProductos($categoria); // Asegúrate de tener este método en tu modelo

$importados = $_GET['importados'] ?? null;

// Simulación de productos (puedes adaptar esto a tu base de datos real)
// $productos = [
//     [
//         "id" => 1,
//         "nombre" => "Ron Medellín 750ml",
//         "descripcion" => "Ron añejado colombiano.",
//         "precio_unidad" => 35000,
//         "precio_paca" => 200000, // Por ejemplo, 6 botellas
//         "imagen" => "https://via.placeholder.com/150"
//     ],
//     [
//         "id" => 2,
//         "nombre" => "Aguardiente Antioqueño 375ml",
//         "descripcion" => "Tradición paisa en una botella.",
//         "precio_unidad" => 18000,
//         "precio_paca" => 100000,
//         "imagen" => "https://via.placeholder.com/150"
//     ],
//     [
//         "id" => 3,
//         "nombre" => "Cerveza Poker Lata",
//         "descripcion" => "Cerveza rubia refrescante.",
//         "precio_unidad" => 3500,
//         "precio_paca" => 40000,
//         "imagen" => "https://via.placeholder.com/150"
//     ]
// ];
?>

<div class="container mt-5">
    <h2 class="text-center mb-4"><i class="fas fa-store"></i> Catálogo de Licores</h2>
    <form method="POST" action="procesar_pedido.php">
        <div class="row">
            <?php if (!empty($productos)): ?>
                            <?php $contador = 1; foreach ($productos as $prod): ?>




                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 shadow-sm">
                                        <img src="assets/img/licores/<?php echo$nombre."/".$prod['imagen_producto']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($prod['descripcion']); ?>">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo $prod['descripcion_producto']; ?></h5>
                                            <p class="card-text"></p>
                                            <p class="card-text">
                                                <strong>Precio Unidad:</strong>$<?php echo number_format($prod['precio_unidad_producto'],2); ?> COP<br>
                                                <strong>Precio Paca:</strong> $<?php echo number_format($prod['precio_paca_producto'], 2); ?>COP
                                            </p>

                                            <div class="form-group">
                                                <label for="tipo_1">Tipo de venta</label>
                                                <select name="productos[uno][tipo]" class="form-control" required>
                                                    <option value="unidad">Unidad</option>
                                                    <option value="paca">Paca</option>
                                                </select>
                                            </div>

                                            <div class="form-group mt-2">
                                                <label for="cantidad_">Cantidad</label>
                                                <input type="number" name="productos[uno][cantidad]" class="form-control" min="1" required>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No hay productos registrados</td></tr>
                        <?php endif; ?>













            
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success btn-lg">Enviar Pedido</button>
        </div>
    </form>
</div>

<?php include_once "footer.php"; ?>