<?php 
include_once "header.php"; 
require_once "../models/database.php";

// Procesamiento del formulario de inserción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'insert') {
    // Obtener datos del formulario
    $razon_social = $_POST['cliente_nombre'] ?? '';
    $telefono = $_POST['cliente_telefono'] ?? '';
    $direccion = $_POST['cliente_direccion'] ?? '';
    $zona = $_POST['cliente_zona'] ?? '';
    
    // Validación básica
    $errores = [];
    if (empty($razon_social)) {
        $errores[] = "La razón social es obligatoria";
    }
    
    // Si no hay errores, proceder con la inserción
    if (empty($errores)) {
        // Preparar la consulta SQL
        // $sql_insert = "INSERT INTO clientes (razon_social, telefono, direccion, zona, fecha_registro) 
        //                VALUES ('$razon_social', '$telefono', '$direccion', '$zona', NOW())";
        
        // if ($conexion->query($sql_insert) === TRUE) {
        //     $mensaje_exito = "El cliente ha sido registrado exitosamente";
        // } else {
        //     $errores[] = "Error al insertar: " . $conexion->error;
        // }
        
        // Simulación (eliminar en producción)
        $mensaje_exito = "El cliente ha sido registrado exitosamente";
    }
}

// Consulta para obtener los clientes existentes
// $sql = "SELECT id, razon_social, telefono, direccion, zona, fecha_registro FROM clientes ORDER BY id DESC";
// $resultado = $conexion->query($sql);
?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-user-tie fa-fw"></i> &nbsp; CLIENTES
    </h3>
    <p class="text-justify">
        Nota: Tenga en cuenta que la información registrada será utilizada para todas las operaciones comerciales. Complete todos los campos con información precisa y actualizada.
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

<!-- Content here-->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-plus"></i> &nbsp; Nuevo Cliente</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST" class="form-neon" autocomplete="off">
                        <input type="hidden" name="action" value="insert">
                        <fieldset>
                            <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
                            <div class="container-fluid">
                                <div class="row">								
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="cliente_nombre" class="bmd-label-floating">Razón Social <span class="text-danger">*</span></label>
                                            <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}" class="form-control" name="cliente_nombre" id="cliente_nombre" maxlength="40" required>
                                        </div>
                                    </div>								
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="cliente_telefono" class="bmd-label-floating">Teléfono</label>
                                            <input type="text" pattern="[0-9()+]{1,20}" class="form-control" name="cliente_telefono" id="cliente_telefono" maxlength="20">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="cliente_direccion" class="bmd-label-floating">Dirección</label>
                                            <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ#- ]{1,150}" class="form-control" name="cliente_direccion" id="cliente_direccion" maxlength="150">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="cliente_zona" class="bmd-label-floating">Zona</label>
                                            <input type="text" pattern="[a-zA-Z0-9-]{1,27}" class="form-control" name="cliente_zona" id="cliente_zona" maxlength="27">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <p class="text-center" style="margin-top: 40px;">
                            <button type="reset" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR</button>
                            &nbsp; &nbsp;
                            <button type="submit" class="btn btn-raised btn-info btn-sm"><i class="far fa-save"></i> &nbsp; GUARDAR</button>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<br>

<!-- Tabla de clientes -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-list"></i> &nbsp; Lista de Clientes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-sm">
                            <thead>
                                <tr class="text-center roboto-medium">
                                    <th>#</th>
                                    <th>RAZÓN SOCIAL</th>
                                    <th>TELÉFONO</th>
                                    <th>DIRECCIÓN</th>
                                    <th>ZONA</th>
                                    <th>FECHA REGISTRO</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Mostrar los datos de clientes desde la base de datos
                                // if($resultado && $resultado->num_rows > 0) {
                                //     $contador = 1;
                                //     while($row = $resultado->fetch_assoc()) {
                                //         echo '<tr class="text-center">';
                                //         echo '<td>'.$contador.'</td>';
                                //         echo '<td>'.$row['razon_social'].'</td>';
                                //         echo '<td>'.$row['telefono'] ? $row['telefono'] : 'N/A'.'</td>';
                                //         echo '<td>'.$row['direccion'] ? $row['direccion'] : 'N/A'.'</td>';
                                //         echo '<td>'.$row['zona'] ? $row['zona'] : 'N/A'.'</td>';
                                //         echo '<td>'.date('d/m/Y', strtotime($row['fecha_registro'])).'</td>';
                                //         echo '<td>';
                                //         echo '<a href="cliente-edit.php?id='.$row['id'].'" class="btn btn-success btn-sm mr-1" title="Editar">';
                                //         echo '<i class="fas fa-edit"></i>';
                                //         echo '</a>';
                                //         echo '<button type="button" class="btn btn-danger btn-sm delete-btn" data-id="'.$row['id'].'" title="Eliminar">';
                                //         echo '<i class="fas fa-trash-alt"></i>';
                                //         echo '</button>';
                                //         echo '</td>';
                                //         echo '</tr>';
                                //         $contador++;
                                //     }
                                // } else {
                                //     echo '<tr><td colspan="7" class="text-center">No hay clientes registrados</td></tr>';
                                // }
                                ?>
                                
                                <!-- Ejemplos estáticos -->
                                <tr class="text-center">
                                    <td>1</td>
                                    <td>Empresa XYZ S.A.</td>
                                    <td>+57 301 234 5678</td>
                                    <td>Calle 123 #45-67</td>
                                    <td>Norte</td>
                                    <td>25/04/2025</td>
                                    <td>
                                        <a href="cliente-edit.php?id=1" class="btn btn-success btn-sm mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="1" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td>2</td>
                                    <td>Comercial ABC Ltda.</td>
                                    <td>+57 320 987 6543</td>
                                    <td>Avenida Principal #78-90</td>
                                    <td>Sur</td>
                                    <td>20/04/2025</td>
                                    <td>
                                        <a href="cliente-edit.php?id=2" class="btn btn-success btn-sm mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="2" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td>3</td>
                                    <td>Distribuidora Centro</td>
                                    <td>+57 310 456 7890</td>
                                    <td>Carrera 45 #12-34</td>
                                    <td>Centro</td>
                                    <td>18/04/2025</td>
                                    <td>
                                        <a href="cliente-edit.php?id=3" class="btn btn-success btn-sm mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="3" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td>4</td>
                                    <td>Almacenes del Valle</td>
                                    <td>+57 315 789 0123</td>
                                    <td>Diagonal 67 #89-12</td>
                                    <td>Occidente</td>
                                    <td>15/04/2025</td>
                                    <td>
                                        <a href="cliente-edit.php?id=4" class="btn btn-success btn-sm mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="4" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
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

<!-- Modal para confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar este cliente? Esta acción no se puede deshacer y puede afectar a registros relacionados.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form id="delete-form" method="POST" action="">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="cliente_id" id="cliente_id" value="">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Script para manejar la confirmación de eliminación
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar los botones de eliminación para abrir el modal
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('cliente_id').value = id;
                $('#deleteModal').modal('show');
            });
        });
    });
</script>

</section>
</main>
	
<?php
include_once "footer.php";
?>