


<?php 
include_once "header.php"; 
require_once "../models/ProductoModel.php";

// Obtener productos desde la base de datos
$producto = new Producto();
$productos = $producto->obtenerProductos(); // Asegúrate de tener este método en tu modelo

$importados = $_GET['importados'] ?? null;
?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-box-open fa-fw"></i> &nbsp; PRODUCTOS
    </h3>
    <p class="text-justify">
        A continuación se presenta la lista de productos disponibles. Puede cargar productos en lote desde un archivo Excel o gestionar los existentes.
    </p>
</div>

<!-- Mensaje de productos importados -->
<?php if ($importados): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>¡Importación exitosa!</strong> Se han importado <?php echo (int)$importados; ?> productos.
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<!-- Formulario de carga de Excel -->
<div class="container-fluid">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-primary text-white text-center rounded-top-4">
                        <h4 class="mb-0">📦 Importar Productos desde Excel</h4>
                    </div>

                    <div class="card-body p-4">
                        <form action="../controllers/ProductoController.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="archivo_excel" class="form-label">Selecciona el archivo Excel</label>
                                <input type="file" name="archivo_excel" id="archivo_excel" class="form-control" accept=".xlsx,.xls" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="importar" class="btn btn-success">
                                    📤 Importar Productos
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer text-muted text-center small">
                        Asegúrate de que el archivo esté en formato .xlsx y siga la estructura correcta.
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Lista de productos -->
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><i class="fas fa-list"></i> &nbsp; Lista de Productos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-sm">
                    <thead>
                        <tr class="text-center roboto-medium">
                            <th>#</th>
                            <th>DESCRIPCIÓN</th>
                            <th>PRECIO UNIDAD</th>
                            <th>PRECIO PACA</th>
                            <th>CANTIDAD PACA</th>
                            <th>ID CATEGORÍA</th>
                            <th>IMAGEN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($productos)): ?>
                            <?php $contador = 1; foreach ($productos as $prod): ?>
                            <tr class="text-center">
                                <td><?php echo $contador++; ?></td>
                                <td><?php echo htmlspecialchars($prod['descripcion']); ?></td>
                                <td>$<?php echo number_format($prod['precio_unidad'], 2); ?></td>
                                <td>$<?php echo number_format($prod['precio_paca'], 2); ?></td>
                                <td><?php echo (int)$prod['cantidad_paca']; ?></td>
                                <td><?php echo (int)$prod['id_categoria']; ?></td>
                                <td><?php echo (int)$prod['imagen']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No hay productos registrados</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once "footer.php"; ?>