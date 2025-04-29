<?php
include_once "header.php";
require_once "../models/database.php";


$fecha = date('Y-m-d');

// Procesamiento del formulario de inserción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'insert') {
	// Obtener datos del formulario
	$fecha = $_POST['fecha'] ?? '';
	$cliente_id = $_POST['cliente_id'] ?? '';
	$num_factura = $_POST['num_factura'] ?? '';
	$valor = $_POST['valor'] ?? '';
	$estado = $_POST['estado'] ?? '';
	$medio_pago = $_POST['medio_pago'] ?? '';
	$validado = isset($_POST['validado']) ? 1 : 0;

	// Validar datos (implementar validación según sea necesario)

	// Insertar en la base de datos
	// $sql_insert = "INSERT INTO caja (fecha, cliente_id, num_factura, valor, estado, medio_pago, validado) 
	//                VALUES ('$fecha', '$cliente_id', '$num_factura', '$valor', '$estado', '$medio_pago', '$validado')";

	// if ($conexion->query($sql_insert) === TRUE) {
	//     // Redirigir para evitar reenvío del formulario
	//     header("Location: " . $_SERVER['PHP_SELF'] . "?inserted=true");
	//     exit;
	// } else {
	//     $error_message = "Error al insertar: " . $conexion->error;
	// }
}

// Inicializar variables de filtro
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$nit_cliente = isset($_GET['nit_cliente']) ? $_GET['nit_cliente'] : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Construir la consulta SQL base para consultar registros
$sql = "SELECT c.id, c.fecha, cl.nombre AS nombre_cliente, c.num_factura, c.valor, c.estado, 
               c.medio_pago, c.validado 
        FROM caja c 
        INNER JOIN clientes cl ON c.cliente_id = cl.id 
        WHERE 1=1";

// Añadir condiciones de filtro si se proporcionan
if (!empty($fecha_inicio)) {
	$sql .= " AND c.fecha >= '$fecha_inicio'";
}
if (!empty($fecha_fin)) {
	$sql .= " AND c.fecha <= '$fecha_fin'";
}
if (!empty($nit_cliente)) {
	$sql .= " AND cl.nit = '$nit_cliente'";
}
if (!empty($estado)) {
	$sql .= " AND c.estado = '$estado'";
}

// Ordenar por ID o fecha
$sql .= " ORDER BY c.id DESC";

// Ejecutar consulta
// $resultado = $conexion->query($sql);

// Consulta para obtener clientes para el dropdown
// $sql_clientes = "SELECT id, nombre FROM clientes ORDER BY nombre";
// $clientes = $conexion->query($sql_clientes);
?>
<!-- Page header -->
<div class="full-box page-header">
	<h3 class="text-left">
		<i class="fas fa-coins fa-fw"></i> &nbsp; Caja y Pedidos
	</h3>
</div>

<!-- Mensaje de éxito después de insertar -->
<?php if (isset($_GET['inserted']) && $_GET['inserted'] === 'true'): ?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<strong>¡Éxito!</strong> El registro ha sido añadido correctamente.
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
<?php endif; ?>

<!-- Mensaje de error si hay problemas al insertar -->
<?php if (isset($error_message)): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<strong>¡Error!</strong> <?php echo $error_message; ?>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
<?php endif; ?>

<!-- FILTROS -->
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<h5 style="margin: 0;">Filtros</h5>
						<button type="button" class="btn btn-primary btn-sm" style="border: 2px solid #4caf50; padding: 5px 10px; color: #4caf50; background-color: transparent; font-weight: bold;">
							Cierre de Caja
						</button>
					</div>
				</div>
				<div class="card-body">
					<form method="GET" action="">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label for="fecha_inicio">Fecha Inicio:</label>
									<input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="fecha_fin">Fecha Fin:</label>
									<input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo $fecha_fin; ?>">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="nit_cliente">NIT Cliente:</label>
									<input type="text" class="form-control" id="nit_cliente" name="nit_cliente" value="<?php echo $nit_cliente; ?>" placeholder="Ingrese el NIT">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="estado">Estado:</label>
									<select class="form-control" id="estado" name="estado">
										<option value="">Todos</option>
										<option value="Pendiente" <?php echo ($estado == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
										<option value="Pagado" <?php echo ($estado == 'Pagado') ? 'selected' : ''; ?>>Pagado</option>
										<option value="Anulado" <?php echo ($estado == 'Anulado') ? 'selected' : ''; ?>>Anulado</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row mt-3">
							<div class="col-12 text-center">
								<button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrar</button>
								<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Limpiar</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<br>

<!--CONTENT-->
<div class="container-fluid">
	<div class="table-responsive">
		<table class="table table-dark table-sm">
			<thead>
				<tr class="text-center roboto-medium">
					<th>#</th>
					<th>FECHA</th>
					<th>CLIENTE</th>
					<th># FACTURA</th>
					<th>VALOR</th>
					<th>ESTADO</th>
					<th>MEDIO DE PAGO</th>
					<th>VALIDADO</th>
					<th>ACCIONES</th>
				</tr>
			</thead>
			<tbody>
				<!-- Fila para insertar nuevos datos -->
				<tr class="text-white" style="background-color:rgb(168, 185, 196);">
					<form method="POST" action="" id="insert-form">
						<input type="hidden" name="action" value="insert">
						<td><strong>Regitro</strong></td>
						<td>
							<input type="date" class="form-control form-control-sm" name="fecha" value="<?= $fecha ?>" style="text-align: center;" disabled>
						</td>
						<td>
							<select class="form-control form-control-sm" name="cliente_id" style="text-align: center;" required>
								<option value="">Seleccione...</option>
								<?php
								// if ($clientes && $clientes->num_rows > 0) {
								//     while($row_cliente = $clientes->fetch_assoc()) {
								//         echo '<option value="'.$row_cliente['id'].'">'.$row_cliente['nombre'].'</option>';
								//     }
								// }
								?>
								<!-- Ejemplos estáticos -->
								<option value="1">Cliente 1</option>
								<option value="2">Cliente 2</option>
								<option value="3">Cliente 3</option>
							</select>
						</td>
						<td>
							<input type="text" class="form-control form-control-sm" name="num_factura" placeholder="# Factura" style="text-align: center;" required>
						</td>
						<td>
							<input type="number" step="0.01" class="form-control form-control-sm" name="valor" placeholder="0.00" style="text-align: center;" required>
						</td>
						<td>
							<select class="form-control form-control-sm" name="estado" style="text-align: center;" required>
								<option value="">Seleccione...</option>
								<option value="Pendiente">Pendiente</option>
								<option value="Pagado">Pagado</option>
								<option value="Anulado">Anulado</option>
							</select>
						</td>
						<td>
							<select class="form-control form-control-sm" name="medio_pago" style="text-align: center;" required>
								<option value="">Seleccione...</option>
								<option value="Efectivo">Efectivo</option>
								<option value="Tarjeta">Tarjeta</option>
								<option value="Transferencia">Transferencia</option>
								<option value="Cheque">Cheque</option>
							</select>
						</td>
						<td class="text-center">
							<div style="display: flex; justify-content: center; align-items: center; height: 100%;">
								<input
									type="checkbox"
									id="validado"
									name="validado"
									style="width: 20px; height: 22px; cursor: pointer;">
							</div>
						</td>
						<td class="text-center">
							<button type="submit" class="btn btn-success btn-sm badge">
								<i class="fas fa-save"></i> Guardar
							</button>
						</td>
					</form>
				</tr>

				<?php
				// Aquí deberías hacer un bucle con los resultados de tu consulta
				// Este es un ejemplo estático, cámbialo por tus datos reales

				// Ejemplo:
				// if($resultado && $resultado->num_rows > 0) {
				//     while($row = $resultado->fetch_assoc()) {
				//         echo '<tr class="text-center">';
				//         echo '<td>'.$row['id'].'</td>';
				//         echo '<td>'.date('d/m/Y', strtotime($row['fecha'])).'</td>';
				//         echo '<td>'.$row['nombre_cliente'].'</td>';
				//         echo '<td>'.$row['num_factura'].'</td>';
				//         echo '<td>$'.number_format($row['valor'], 2).'</td>';
				//         
				//         $estado_class = '';
				//         switch($row['estado']) {
				//             case 'Pendiente': $estado_class = 'warning'; break;
				//             case 'Pagado': $estado_class = 'success'; break;
				//             case 'Anulado': $estado_class = 'danger'; break;
				//         }
				//         
				//         echo '<td><span class="badge badge-'.$estado_class.'">'.$row['estado'].'</span></td>';
				//         echo '<td>'.$row['medio_pago'].'</td>';
				//         
				//         $validado_icon = $row['validado'] ? 
				//             '<i class="fas fa-check-circle text-success"></i>' : 
				//             '<i class="fas fa-times-circle text-danger"></i>';
				//         
				//         echo '<td class="text-center">'.$validado_icon.'</td>';
				//         echo '<td class="text-center">';
				//         echo '<a href="caja-edit.php?id='.$row['id'].'" class="btn btn-success btn-sm mr-1"><i class="fas fa-edit"></i></a>';
				//         echo '<button type="button" class="btn btn-danger btn-sm delete-btn" data-id="'.$row['id'].'"><i class="fas fa-trash-alt"></i></button>';
				//         echo '</td>';
				//         echo '</tr>';
				//     }
				// }
				?>

				<!-- Ejemplos estáticos -->
				<tr class="text-center">
					<td>1</td>
					<td>28/04/2025</td>
					<td>Cliente A</td>
					<td>F-001234</td>
					<td>$1,250.00</td>
					<td><span class="badge badge-success">Pagado</span></td>
					<td>Efectivo</td>
					<td class="text-center"><i class="fas fa-check-circle text-success"></i></td>
					<td class="text-center">
						<a href="caja-edit.php?id=1" class="btn btn-success btn-sm mr-1"><i class="fas fa-edit"></i></a>
						<button type="button" class="btn btn-danger btn-sm delete-btn" data-id="1"><i class="fas fa-trash-alt"></i></button>
					</td>
				</tr>
				<tr class="text-center">
					<td>2</td>
					<td>27/04/2025</td>
					<td>Cliente B</td>
					<td>F-001233</td>
					<td>$875.50</td>
					<td><span class="badge badge-warning">Pendiente</span></td>
					<td>Transferencia</td>
					<td class="text-center"><i class="fas fa-times-circle text-danger"></i></td>
					<td class="text-center">
						<a href="caja-edit.php?id=2" class="btn btn-success btn-sm mr-1"><i class="fas fa-edit"></i></a>
						<button type="button" class="btn btn-danger btn-sm delete-btn" data-id="2"><i class="fas fa-trash-alt"></i></button>
					</td>
				</tr>
				<tr class="text-center">
					<td>3</td>
					<td>25/04/2025</td>
					<td>Cliente C</td>
					<td>F-001232</td>
					<td>$320.75</td>
					<td><span class="badge badge-danger">Anulado</span></td>
					<td>Tarjeta</td>
					<td class="text-center"><i class="fas fa-check-circle text-success"></i></td>
					<td class="text-center">
						<a href="caja-edit.php?id=3" class="btn btn-success btn-sm mr-1"><i class="fas fa-edit"></i></a>
						<button type="button" class="btn btn-danger btn-sm delete-btn" data-id="3"><i class="fas fa-trash-alt"></i></button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
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
</section>
</main>

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
				¿Está seguro de que desea eliminar este registro? Esta acción no se puede deshacer.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<form id="delete-form" method="POST" action="">
					<input type="hidden" name="action" value="delete">
					<input type="hidden" name="delete_id" id="delete_id" value="">
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
				document.getElementById('delete_id').value = id;
				$('#deleteModal').modal('show');
			});
		});
	});
</script>

<?php

include_once "footer.php";
?>

<style>
	.custom-control-label::before {
		background-color: #28a745;
		border-color: #28a745;
	}
</style>