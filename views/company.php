<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.2/dist/index.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        <i class="fas fa-gift fa-fw"></i> &nbsp; Recepcion de Pedidos
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

</div>
<br>

<!--CONTENT - Tabla de promociones existentes-->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-list"></i> &nbsp; Lista de Pedidos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-sm" id="tabla-promos" border="1">
                       
                            <thead>
                                <tr class="text-center roboto-medium">
                                    <th>Id</th>
                                    <th>Numero solicitante</th>
                                    <th>Descripción pedido</th>
                                    <th>Responder</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                ?>
                                
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
//     // Script para manejar los botones de activar/desactivar
//     document.addEventListener('DOMContentLoaded', function() {
//         // Configurar los botones de desactivar
//         const desactivarButtons = document.querySelectorAll('.desactivar-btn');
//         desactivarButtons.forEach(button => {
//             button.addEventListener('click', function() {
//                 const id = this.getAttribute('data-id');
//                 document.getElementById('promocion_id').value = id;
//                 document.getElementById('nuevo_estado').value = 'Inactiva';
//                 document.getElementById('modal-mensaje').innerText = '¿Está seguro de que desea desactivar esta promoción?';
//                 $('#estadoModal').modal('show');
//             });
//         });
        
//         // Configurar los botones de reactivar
//         const reactivarButtons = document.querySelectorAll('.reactivar-btn');
//         reactivarButtons.forEach(button => {
//             button.addEventListener('click', function() {
//                 const id = this.getAttribute('data-id');
//                 document.getElementById('promocion_id').value = id;
//                 document.getElementById('nuevo_estado').value = 'Activa';
//                 document.getElementById('modal-mensaje').innerText = '¿Está seguro de que desea activar esta promoción?';
//                 $('#estadoModal').modal('show');
//             });
//         });
//     });

//     document.getElementById('abrir-emojis').addEventListener('click', function (e) {
//     const box = document.getElementById('emoji-box');
//     box.style.display = box.style.display === 'none' ? 'block' : 'none';
// });


// document.getElementById('form-promocion').addEventListener('submit', function(e) {
//     e.preventDefault(); // ⚠️ Esto evita que la página se recargue

//     const form = e.target;
//     const formData = new FormData(form); // Captura texto + archivos

//     fetch('../controllers/promoController.php', {
//         method: 'POST',
//         body: formData
//     })
//     .then(res => res.json())
//     .then(data => {
//         if (data.success) {
//             alert('✅ Promoción guardada con éxito');
//             form.reset();
//         } else {
//             alert('⚠️ Error al guardar: ' + data.message);
//         }
//     })
//     .catch(err => {
//         console.error(err);
//         alert('❌ Error de red o servidor');
//     });
// });


document.addEventListener("DOMContentLoaded", function () {
    fetch('../controllers/promoController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'action=mostrar'
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.querySelector("#tabla-promos tbody");
        data.forEach(promo => {
            const fila = `
                <tr>
                    <td>${promo.pro_id }</td>
                    <td>${promo.pro_nombre}</td>
                    <td>${promo.pro_descripcion}</td>
                    
                    <td>
                        <a href="promocion-edit.php?id=4" class="btn btn-success btn-sm mr-1" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                    
                </tr>`;
            tbody.innerHTML += fila;
        });
    })
    .catch(error => console.error("Error:", error));
});

</script>


<?php
include_once "footer.php";
?>