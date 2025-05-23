<?php
include_once "header.php";
require_once "../models/ProductoModel.php";

$producto = new Producto();
$listaProductos = $producto->obtenerProductos();

$importados = $_GET['importados'] ?? null;
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-box"></i> &nbsp; PRODUCTOS
    </h3>
    <p class="text-justify">
        A continuación se muestra la lista de productos registrados. Puede importar nuevos productos desde un archivo Excel o agregar manualmente.
    </p>
</div>

<?php if ($importados): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>¡Importación exitosa!</strong> Se importaron <?php echo htmlspecialchars($importados); ?> productos.
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-upload"></i> &nbsp; Importar Productos</h5>
                </div>
                <div class="card-body">
                    <form action="../controllers/importarExcel.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="archivo_excel">Seleccione un archivo Excel (.xlsx):</label>
                            <input type="file" class="form-control-file" name="archivo_excel" id="archivo_excel" required>
                        </div>
                        <button type="submit" class="btn btn-info btn-sm"><i class="fas fa-file-upload"></i> Importar</button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
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
                                    <th>CATEGORÍA</th>
                                    <th>PRECIO UNIDAD</th>
                                    <th>PRECIO PACA</th>
                                    <th>CANT. PACA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($listaProductos)): ?>
                                    <?php $contador = 1; ?>
                                    <?php foreach ($listaProductos as $prod): ?>
                                        <tr class="text-center">
                                            <td><?php echo $contador++; ?></td>
                                            <td><?php echo htmlspecialchars($prod['descripcion']); ?></td>
                                            <td><?php echo htmlspecialchars($prod['id_categoria']); ?></td>
                                            <td>$<?php echo number_format($prod['precio_unidad'], 2); ?></td>
                                            <td>$<?php echo number_format($prod['precio_paca'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($prod['cantidad_paca']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No hay productos registrados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <nav aria-label="Paginación">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#">Anterior</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once "footer.php"; ?>
