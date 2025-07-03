<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.2/dist/index.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php
include_once "header.php";
require_once "../models/database.php";
require_once "../models/solicitudModel.php";

// Instanciar la clase Solicitud
$solicitudModel = new solicitud();

// Procesamiento del formulario de aceptar pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'aceptar_pedido') {
    $pedido_id = $_POST['pedido_id'] ?? '';
    $numCliente = $_POST['numCliente'] ?? '';
    $observaciones = $_POST['observaciones'] ?? '';
    // if ($Observaciones!="") {
    //     $Observaciones="Observaciones: ".$observaciones;
    // }

    if (!empty($pedido_id)) {
        try {
            if ($solicitudModel->aceptarPedido($pedido_id)) {
                $mensaje_exito = "El pedido ha sido aceptado exitosamente";
                $plantilla="recibido";

                $respuesta = $solicitudModel->enviarPromo($pedido_id, "$observaciones", "", "$numCliente", $plantilla);
                error_log(print_r($respuesta, true));
                echo json_encode($respuesta);
                exit;
            } else {
                $errores[] = "Error al actualizar el pedido";
            }
        } catch (Exception $e) {
            $errores[] = "Error: " . $e->getMessage();
        }
    } else {
        $errores[] = "ID de pedido requerido";
    }
}

// Procesamiento del formulario de rechazar pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'rechazar_pedido') {
    $pedido_id = $_POST['pedido_id'] ?? '';
    $numCliente = $_POST['numCliente'] ?? '';
    $observaciones = $_POST['motivo_rechazo'] ?? '';
    // if ($Observaciones!="") {
    //     $Observaciones="Observaciones: ".$observaciones;
    // }
    
    if (!empty($pedido_id)) {
        try {
            if ($solicitudModel->rechazarPedido($pedido_id)) {
                $mensaje_exito = "El pedido ha sido aceptado exitosamente";
                $plantilla="rechazado";

                $respuesta = $solicitudModel->enviarPromo($pedido_id, "$observaciones", "", "$numCliente", $plantilla);
                error_log(print_r($respuesta, true));
                echo json_encode($respuesta);
                exit;
            } else {
                $errores[] = "Error al rechazar el pedido";
            }
        } catch (Exception $e) {
            $errores[] = "Error: " . $e->getMessage();
        }
    } else {
        $errores[] = "ID de pedido requerido";
    }
}

// Obtener pedidos usando la clase
$pedidos_pendientes = $solicitudModel->obtenerPedidosPendientes();
$pedidos_aceptados = $solicitudModel->obtenerPedidosAceptados();
$pedidos_rechazados = $solicitudModel->obtenerPedidosRechazados();

?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-shopping-cart fa-fw"></i> &nbsp; Recepción de Pedidos
    </h3>
    <p class="text-justify">
        Gestione las solicitudes de pedidos de los clientes. Revise los detalles y acepta o rechace cada pedido.
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

<!-- Tabs para todos los pedidos con filtros por estado -->
<div class="container-fluid">
    <ul class="nav nav-tabs" id="pedidosTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="todos-tab" data-toggle="tab" href="#todos" role="tab">
                <i class="fas fa-list"></i> Todos los Pedidos
                <span class="badge badge-info ml-2"><?php echo count($pedidos_pendientes) + count($pedidos_aceptados) + count($pedidos_rechazados); ?></span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="pendientes-tab" data-toggle="tab" href="#pendientes" role="tab">
                <i class="fas fa-clock"></i> Pendientes
                <span class="badge badge-warning ml-2"><?php echo count($pedidos_pendientes); ?></span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="aceptados-tab" data-toggle="tab" href="#aceptados" role="tab">
                <i class="fas fa-check-circle"></i> Aceptados
                <span class="badge badge-success ml-2"><?php echo count($pedidos_aceptados); ?></span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="rechazados-tab" data-toggle="tab" href="#rechazados" role="tab">
                <i class="fas fa-times-circle"></i> Rechazados
                <span class="badge badge-danger ml-2"><?php echo count($pedidos_rechazados); ?></span>
            </a>
        </li>
    </ul>

    <div class="tab-content" id="pedidosTabContent">
        <!-- Tab de Todos los Pedidos -->
        <div class="tab-pane fade show active" id="todos" role="tabpanel">
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-list"></i> &nbsp; Todas las Solicitudes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-sm" id="tabla-todos">
                            <thead>
                                <tr class="text-center roboto-medium">
                                    <th>ID Pedido</th>
                                    <th>N° Factura</th>
                                    <th>Cliente</th>
                                    <th>Fecha y Hora</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Ver Productos</th>
                                    <th>Acciones</th>
                                    <th>Cliente</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Mostrar pedidos pendientes -->
                                <?php if (!empty($pedidos_pendientes)): ?>
                                    <?php foreach ($pedidos_pendientes as $pedido): ?>
                                        <tr class="table-warning">
                                            <td class="text-center"><?php echo $pedido['id_pedido']; ?></td>
                                            <td class="text-center"><?php echo $pedido['ped_numfac']; ?></td>
                                            <td><?php echo $pedido['ped_cliente']; ?></td>
                                            <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($pedido['ped_fecha'])); ?></td>
                                            <td class="text-center">$<?php echo number_format($pedido['ped_total'], 2); ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-warning">Pendiente</span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-info btn-sm ver-productos" data-pedido="<?php echo $pedido['id_pedido']; ?>">
                                                    <i class="fas fa-eye"></i> Ver Más
                                                </button>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-success btn-sm aceptar-pedido" data-pedido="<?php echo $pedido['id_pedido']; ?>">
                                                    <i class="fas fa-check"></i> Aceptar
                                                </button>
                                                <input type="hidden" id="numCliente<?php echo $pedido['id_pedido']; ?>" data-numcliente=" <?php echo$pedido['ped_numCliente']; ?>" />

                                                <button class="btn btn-danger btn-sm rechazar-pedido" data-pedido="<?php echo $pedido['id_pedido']; ?>">
                                                    <i class="fas fa-times"></i> Rechazar
                                                </button>
                                            </td>
                                            <td><?php echo $pedido['nombre_bar']; ?></td>
                                            
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <!-- Mostrar pedidos aceptados -->
                                <?php if (!empty($pedidos_aceptados)): ?>
                                    <?php foreach ($pedidos_aceptados as $pedido): ?>
                                        <tr class="table-success">
                                            <td class="text-center"><?php echo $pedido['id_pedido']; ?></td>
                                            <td class="text-center"><?php echo $pedido['ped_numfac']; ?></td>
                                            <td><?php echo $pedido['ped_cliente']; ?></td>
                                            <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($pedido['ped_fecha'])); ?></td>
                                            <td class="text-center">$<?php echo number_format($pedido['ped_total'], 2); ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-success">Aceptado</span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-info btn-sm ver-productos" data-pedido="<?php echo $pedido['id_pedido']; ?>">
                                                    <i class="fas fa-eye"></i> Ver Más
                                                </button>
                                            </td>
                                            <td class="text-center">
                                                <span class="text-success">
                                                    <i class="fas fa-check-circle"></i> Procesado
                                                </span>
                                            </td>
                                            <td><?php echo $pedido['nombre_bar']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <!-- Mostrar pedidos rechazados -->
                                <?php if (!empty($pedidos_rechazados)): ?>
                                    <?php foreach ($pedidos_rechazados as $pedido): ?>
                                        <tr class="table-danger">
                                            <td class="text-center"><?php echo $pedido['id_pedido']; ?></td>
                                            <td class="text-center"><?php echo $pedido['ped_numfac']; ?></td>
                                            <td><?php echo $pedido['ped_cliente']; ?></td>
                                            <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($pedido['ped_fecha'])); ?></td>
                                            <td class="text-center">$<?php echo number_format($pedido['ped_total'], 2); ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-danger">Rechazado</span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-info btn-sm ver-productos" data-pedido="<?php echo $pedido['id_pedido']; ?>">
                                                    <i class="fas fa-eye"></i> Ver Más
                                                </button>
                                            </td>
                                            <td class="text-center">
                                                <span class="text-danger">
                                                    <i class="fas fa-times-circle"></i> Rechazado
                                                </span>
                                            </td>
                                            <td><?php echo $pedido['nombre_bar']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <!-- Mensaje si no hay pedidos -->
                                <?php if (empty($pedidos_pendientes) && empty($pedidos_aceptados) && empty($pedidos_rechazados)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay pedidos registrados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab de Pedidos Aceptados -->
        <div class="tab-pane fade" id="aceptados" role="tabpanel">
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-check-circle"></i> &nbsp; Pedidos Aceptados</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-success table-sm" id="tabla-aceptados">
                            <thead>
                                <tr class="text-center roboto-medium">
                                    <th>ID Pedido</th>
                                    <th>N° Factura</th>
                                    <th>Cliente</th>
                                    <th>Fecha Pedido</th>
                                    <th>Total</th>
                                    <th>Ver Productos</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pedidos_aceptados)): ?>
                                    <?php foreach ($pedidos_aceptados as $pedido): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $pedido['id_pedido']; ?></td>
                                            <td class="text-center"><?php echo $pedido['ped_numfac']; ?></td>
                                            <td><?php echo $pedido['ped_cliente']; ?></td>
                                            <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($pedido['ped_fecha'])); ?></td>
                                            <td class="text-center">$<?php echo number_format($pedido['ped_total'], 2); ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-info btn-sm ver-productos" data-pedido="<?php echo $pedido['id_pedido']; ?>">
                                                    <i class="fas fa-eye"></i> Ver Más
                                                </button>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-success">Aceptado</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No hay pedidos aceptados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab de Pedidos Rechazados -->
        <div class="tab-pane fade" id="rechazados" role="tabpanel">
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-times-circle"></i> &nbsp; Solo Rechazados</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-danger table-sm" id="tabla-rechazados">
                            <thead>
                                <tr class="text-center roboto-medium">
                                    <th>ID Pedido</th>
                                    <th>N° Factura</th>
                                    <th>Cliente</th>
                                    <th>Fecha Pedido</th>
                                    <th>Total</th>
                                    <th>Ver Productos</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pedidos_rechazados)): ?>
                                    <?php foreach ($pedidos_rechazados as $pedido): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $pedido['id_pedido']; ?></td>
                                            <td class="text-center"><?php echo $pedido['ped_numfac']; ?></td>
                                            <td><?php echo $pedido['ped_cliente']; ?></td>
                                            <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($pedido['ped_fecha'])); ?></td>
                                            <td class="text-center">$<?php echo number_format($pedido['ped_total'], 2); ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-info btn-sm ver-productos" data-pedido="<?php echo $pedido['id_pedido']; ?>">
                                                    <i class="fas fa-eye"></i> Ver Más
                                                </button>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-danger">Rechazado</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No hay pedidos rechazados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver productos del pedido -->
<div class="modal fade" id="productosModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Productos del Pedido</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="productos-content">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin"></i> Cargando productos...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para rechazar pedido -->
<div class="modal fade" id="rechazarModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rechazar Pedido</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-rechazar" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="rechazar_pedido">
                    <input type="hidden" name="pedido_id" id="pedido_id_rechazar">
                    <input type="hidden" name="numCliente" id="numClientePedRechaza">
                    <div class="form-group">
                        <label>Motivo del rechazo:</label>
                        <textarea name="motivo_rechazo" class="form-control" rows="3" required
                                placeholder="Explique por qué se rechaza este pedido..."></textarea>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Atención:</strong> Esta acción cambiará el estado del pedido a "Rechazado".
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Rechazar Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para aceptar pedido -->
<div class="modal fade" id="aceptarModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aceptar Pedido</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-aceptar" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="aceptar_pedido">
                    <input type="hidden" name="pedido_id" id="pedido_id_aceptar">
                    <input type="hidden" name="numCliente" id="numClientePedAcepta">
                    
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <strong>Confirmar:</strong> ¿Está seguro de que desea aceptar este pedido?
                    </div>
                    
                    <div class="form-group">
                        <label>Observaciones (opcional):</label>
                        <textarea name="observaciones" class="form-control" rows="2" 
                                placeholder="Comentarios adicionales sobre el pedido..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Confirmar Aceptación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clic en "Ver Más" productos
    document.querySelectorAll('.ver-productos').forEach(button => {
        button.addEventListener('click', function() {
            const pedidoId = this.getAttribute('data-pedido');
            cargarProductosPedido(pedidoId);
        });
    });
    
    // Manejar clic en "Aceptar" pedido
    document.querySelectorAll('.aceptar-pedido').forEach(button => {
        button.addEventListener('click', function() {
            const pedidoId = this.getAttribute('data-pedido');
            const valorNumCliente = document.getElementById('numCliente'+pedidoId).dataset.numcliente;
            document.getElementById('pedido_id_aceptar').value = pedidoId;
            document.getElementById('numClientePedAcepta').value = valorNumCliente;
            
            $('#aceptarModal').modal('show');
        });
    });
    
    // Manejar clic en "Rechazar" pedido
    document.querySelectorAll('.rechazar-pedido').forEach(button => {
        button.addEventListener('click', function() {
            const pedidoId = this.getAttribute('data-pedido');
            const valorNumCliente = document.getElementById('numCliente'+pedidoId).dataset.numcliente;
            document.getElementById('pedido_id_rechazar').value = pedidoId;
            document.getElementById('numClientePedRechaza').value = valorNumCliente;

            $('#rechazarModal').modal('show');
        });
    });
    
    // Manejar envío del formulario de aceptar
    document.getElementById('form-aceptar').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                location.reload(); // Recargar la página para ver los cambios
            } else {
                alert('Error al procesar el pedido');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión');
        });
    });
    
    // Manejar envío del formulario de rechazar
    document.getElementById('form-rechazar').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                location.reload(); // Recargar la página para ver los cambios
            } else {
                alert('Error al procesar el pedido');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión');
        });
    });
});

function cargarProductosPedido(pedidoId) {
    const content = document.getElementById('productos-content');
    content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando productos...</div>';
    
    fetch('../controllers/pedidoController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=obtener_productos&pedido_id=${pedidoId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.productos.length > 0) {
            let html = '<div class="table-responsive">';
            html += '<table class="table table-sm">';
            html += '<thead><tr><th>Producto</th><th>Cantidad</th><th>Precio Unit.</th><th>Subtotal</th></tr></thead>';
            html += '<tbody>';
            
            data.productos.forEach(producto => {
                html += `<tr>
                    <td>${producto.codigo_productos}</td>
                    <td>${producto.nombre_producto}</td>
                    <td class="text-center">${producto.tipo_producto}</td>
                    <td class="text-center">${producto.cantidad}</td>
                    <td class="text-right">$${parseFloat(producto.precio_unitario).toFixed(2)}</td>
                    <td class="text-right">$${parseFloat(producto.subtotal).toFixed(2)}</td>
                    
                </tr>`;
            });
            if (data.productos[0].ped_observacion) {
                    html += `
                        <tr class="table-active">
                            <td colspan="6" class="text-muted">
                                <i class="fas fa-comment-dots"></i> 
                                <em>${data.productos[0].ped_observacion}</em>
                            </td>
                        </tr>
                    `;
                }
            
            html += '</tbody></table></div>';
            content.innerHTML = html;
        } else {
            content.innerHTML = '<div class="alert alert-warning">No se encontraron productos para este pedido.</div>';
        }
        
        $('#productosModal').modal('show');
    })
    .catch(error => {
        console.error('Error:', error);
        content.innerHTML = '<div class="alert alert-danger">Error al cargar los productos.</div>';
        $('#productosModal').modal('show');
    });
}
</script>

<?php include_once "footer.php"; ?>