<?php
include_once "header.php";
require_once "../models/database.php";

// Procesamiento del formulario de inserción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'insert') {
    // Obtener datos del formulario
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $patrocinador = $_POST['patrocinador'] ?? '';
    
    // Validación básica
    $errores = [];
    if (empty($titulo)) {
        $errores[] = "El título de la promoción es obligatorio";
    }
    if (empty($descripcion)) {
        $errores[] = "La descripción es obligatoria";
    }
    
    // Si no hay errores, proceder con la inserción
    if (empty($errores)) {
        // Preparar la consulta SQL
        // $sql_insert = "INSERT INTO promociones (titulo, descripcion, patrocinador, estado, fecha_creacion) 
        //                VALUES ('$titulo', '$descripcion', '$patrocinador', 'Activa', NOW())";
        
        // if ($conexion->query($sql_insert) === TRUE) {
        //     $mensaje_exito = "La promoción ha sido creada exitosamente";
        // } else {
        //     $errores[] = "Error al insertar: " . $conexion->error;
        // }
        
        // Simulación (eliminar en producción)
        $mensaje_exito = "La promoción ha sido creada exitosamente";
    }
}

// Consulta para obtener las promociones existentes
// $sql = "SELECT id, titulo AS promo, descripcion, patrocinador, estado FROM promociones ORDER BY id DESC";
// $resultado = $conexion->query($sql);
?>
<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-gift fa-fw"></i> &nbsp; Promociones
    </h3>
    <p class="text-justify">
        Nota: Tenga en cuenta que las promociones activas serán visibles a los clientes. Complete todos los campos necesarios y revise la información antes de guardar.
    </p>
</div>

<!-- Mensaje de éxito o error -->
<?php if (isset($mensaje_exito)): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>¡Éxito!</strong> <?php echo $mensaje_exito; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<?php if (!empty($errores)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>¡Error!</strong>
    <ul>
        <?php foreach ($errores as $error): ?>
            <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<!-- Formulario para insertar nueva promoción -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-plus"></i> &nbsp; Nueva Promoción</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" id="form-promocion">
                        <input type="hidden" name="action" value="insert">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="titulo">Título de la Promoción <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Ej: Descuento Verano 2025" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="patrocinador">Patrocinador (Opcional)</label>
                                    <input type="text" class="form-control" id="patrocinador" name="patrocinador" placeholder="Ej: Empresa XYZ">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="estado">Estado</label>
                                    <select class="form-control" id="estado" name="estado">
                                        <option value="Activa" selected>Activa</option>
                                        <option value="Inactiva">Inactiva</option>
                                        <option value="Programada">Programada</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="descripcion">Descripción de la Promoción <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Descripción detallada de la promoción" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Promoción</button>
                                <button type="reset" class="btn btn-secondary"><i class="fas fa-broom"></i> Limpiar Campos</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<br>

<!--CONTENT - Tabla de promociones existentes-->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-list"></i> &nbsp; Lista de Promociones</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-sm">
                            <thead>
                                <tr class="text-center roboto-medium">
                                    <th>#</th>
                                    <th>Promoción</th>
                                    <th>Descripción</th>
                                    <th>Patrocinador</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>                                
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Mostrar los datos de promociones desde la base de datos
                                // if($resultado && $resultado->num_rows > 0) {
                                //     $contador = 1;
                                //     while($row = $resultado->fetch_assoc()) {
                                //         echo '<tr class="text-center">';
                                //         echo '<td>'.$contador.'</td>';
                                //         echo '<td>'.$row['promo'].'</td>';
                                //         echo '<td>'.$row['descripcion'].'</td>';
                                //         echo '<td>'.$row['patrocinador'] ? $row['patrocinador'] : 'N/A'.'</td>';
                                //         
                                //         $estado_badge = '';
                                //         switch($row['estado']) {
                                //             case 'Activa': $estado_badge = 'success'; break;
                                //             case 'Inactiva': $estado_badge = 'secondary'; break;
                                //             case 'Programada': $estado_badge = 'info'; break;
                                //             default: $estado_badge = 'primary';
                                //         }
                                //         
                                //         echo '<td><span class="badge badge-'.$estado_badge.'">'.$row['estado'].'</span></td>';
                                //         echo '<td>';
                                //         echo '<a href="promocion-edit.php?id='.$row['id'].'" class="btn btn-success btn-sm mr-1" title="Editar">';
                                //         echo '<i class="fas fa-edit"></i>';
                                //         echo '</a>';
                                //         
                                //         if($row['estado'] == 'Inactiva') {
                                //             echo '<button type="button" class="btn btn-info btn-sm reactivar-btn" data-id="'.$row['id'].'" title="Activar">';
                                //             echo '<i class="fas fa-sync-alt"></i>';
                                //             echo '</button>';
                                //         } else {
                                //             echo '<button type="button" class="btn btn-warning btn-sm desactivar-btn" data-id="'.$row['id'].'" title="Desactivar">';
                                //             echo '<i class="fas fa-power-off"></i>';
                                //             echo '</button>';
                                //         }
                                //         
                                //         echo '</td>';
                                //         echo '</tr>';
                                //         $contador++;
                                //     }
                                // } else {
                                //     echo '<tr><td colspan="6" class="text-center">No hay promociones registradas</td></tr>';
                                // }
                                ?>
                                
                                <!-- Ejemplos estáticos -->
                                <tr class="text-center">
                                    <td>1</td>
                                    <td>Descuento 50% Primavera</td>
                                    <td>Descuento del 50% en todos los productos de temporada primavera 2025</td>
                                    <td>Fashion Company</td>
                                    <td><span class="badge badge-success">Activa</span></td>
                                    <td>
                                        <a href="promocion-edit.php?id=1" class="btn btn-success btn-sm mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-warning btn-sm desactivar-btn" data-id="1" title="Desactivar">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td>2</td>
                                    <td>2x1 en Accesorios</td>
                                    <td>Lleva 2 accesorios por el precio de 1 durante toda la semana</td>
                                    <td>N/A</td>
                                    <td><span class="badge badge-success">Activa</span></td>
                                    <td>
                                        <a href="promocion-edit.php?id=2" class="btn btn-success btn-sm mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-warning btn-sm desactivar-btn" data-id="2" title="Desactivar">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td>3</td>
                                    <td>Envío Gratis</td>
                                    <td>Envío gratis en compras superiores a $50.000</td>
                                    <td>Transportadora Rápida</td>
                                    <td><span class="badge badge-secondary">Inactiva</span></td>
                                    <td>
                                        <a href="promocion-edit.php?id=3" class="btn btn-success btn-sm mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-info btn-sm reactivar-btn" data-id="3" title="Activar">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td>4</td>
                                    <td>Black Friday 2024</td>
                                    <td>Descuentos especiales en toda la tienda por Black Friday</td>
                                    <td>Centro Comercial Plaza</td>
                                    <td><span class="badge badge-info">Programada</span></td>
                                    <td>
                                        <a href="promocion-edit.php?id=4" class="btn btn-success btn-sm mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-warning btn-sm desactivar-btn" data-id="4" title="Desactivar">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Anterior</a>
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
</section>
</main>

<!-- Modal para confirmación de cambio de estado -->
<div class="modal fade" id="estadoModal" tabindex="-1" role="dialog" aria-labelledby="estadoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="estadoModalLabel">Confirmar cambio de estado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-mensaje">
                ¿Está seguro de que desea cambiar el estado de esta promoción?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form id="estado-form" method="POST" action="">
                    <input type="hidden" name="action" value="cambiar_estado">
                    <input type="hidden" name="promocion_id" id="promocion_id" value="">
                    <input type="hidden" name="nuevo_estado" id="nuevo_estado" value="">
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Script para manejar los botones de activar/desactivar
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar los botones de desactivar
        const desactivarButtons = document.querySelectorAll('.desactivar-btn');
        desactivarButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('promocion_id').value = id;
                document.getElementById('nuevo_estado').value = 'Inactiva';
                document.getElementById('modal-mensaje').innerText = '¿Está seguro de que desea desactivar esta promoción?';
                $('#estadoModal').modal('show');
            });
        });
        
        // Configurar los botones de reactivar
        const reactivarButtons = document.querySelectorAll('.reactivar-btn');
        reactivarButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('promocion_id').value = id;
                document.getElementById('nuevo_estado').value = 'Activa';
                document.getElementById('modal-mensaje').innerText = '¿Está seguro de que desea activar esta promoción?';
                $('#estadoModal').modal('show');
            });
        });
    });
</script>

<?php
include_once "footer.php";
?>