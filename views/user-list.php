<?php
include_once "header.php";
require_once "../models/database.php";
class User {
    private $db;

    public function __construct() {
        $this->db = new Database(); // Crear instancia de la base de datos
    }

    public function getUsers() {
        try {
            $query = $this->db->connect()->prepare("SELECT `id`, `usuario`, `password`, `email`, `nombre`, `telefono`, `direccion` FROM `users` order by id desc");
            $query->execute(); // Ejecutar la consulta
            return $query->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los resultados como array asociativo
        } catch (PDOException $e) {
            die("Error en la consulta: " . $e->getMessage());
        }
    }
}

// Crear instancia de User y obtener los usuarios
$user = new User();
$users = $user->getUsers();
?>




			<!-- Page header -->
			<div class="full-box page-header">
				<h3 class="text-left">
					<i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE USUARIOS
				</h3>
				<p class="text-justify">
					Lorem ipsum dolor sit amet, consectetur adipisicing elit. Suscipit nostrum rerum animi natus beatae ex. Culpa blanditiis tempore amet alias placeat, obcaecati quaerat ullam, sunt est, odio aut veniam ratione.
				</p>
			</div>
			
			<div class="container-fluid">
				<ul class="full-box list-unstyled page-nav-tabs">
					<li>
						<a href="user-new.html"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO USUARIO</a>
					</li>
					<li>
						<a class="active" href="user-list.html"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE USUARIOS</a>
					</li>
					<li>
						<a href="user-search.html"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR USUARIO</a>
					</li>
				</ul>	
			</div>
			
			
			<div class="container-fluid">
				<div class="table-responsive">
					<table class="table table-dark table-sm">
						<thead>
							<tr class="text-center roboto-medium">
								<th>#</th>
								<th>USUARIO</th>
								<th>NOMBRE</th>
								<th>TELÉFONO</th>
								<th>EMAIL</th>
								<th>ROL</th>
								<th>ACTUALIZAR</th>
								<th>ELIMINAR</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($users as $user) {?>
							<tr class="text-center" >

								<th><?php echo $user["id"]; ?></th>
								<th><?php echo $user["usuario"]; ?></th>
								<th><?php echo $user["nombre"]; ?></th>
								<th><?php echo $user["telefono"]; ?></th>
								<th><?php echo $user["email"]; ?></th>
								<th><?php echo $user["usuario"]; ?></th>

								<td>
									<a href="user-update.php?id=<?echo$user["id"];?>" class="btn btn-success">
	  									<i class="fas fa-sync-alt"></i>	
									</a>
								</td>
								<td>
									<form action="">
										<button type="button" class="btn btn-warning">
		  									<i class="far fa-trash-alt"></i>
										</button>
									</form>
								</td>
							</tr>
							<?php }?>
							
						</tbody>
					</table>
				</div>
				<nav aria-label="Page navigation example">
					<ul class="pagination justify-content-center">
						<li class="page-item disabled">
							<a class="page-link" href="#" tabindex="-1">Previous</a>
						</li>
						<li class="page-item"><a class="page-link" href="#">1</a></li>
						<li class="page-item"><a class="page-link" href="#">2</a></li>
						<li class="page-item"><a class="page-link" href="#">3</a></li>
						<li class="page-item">
							<a class="page-link" href="#">Next</a>
						</li>
					</ul>
				</nav>
			</div>

		</section>
	</main>
	
	
	<!--=============================================
	=            Include JavaScript files           =
	==============================================-->
	<!-- jQuery V3.4.1 -->
	<script src="../js/jquery-3.4.1.min.js" ></script>

	<!-- popper -->
	<script src="../js/popper.min.js" ></script>

	<!-- Bootstrap V4.3 -->
	<script src="../js/bootstrap.min.js" ></script>

	<!-- jQuery Custom Content Scroller V3.1.5 -->
	<script src="../js/jquery.mCustomScrollbar.concat.min.js" ></script>

	<!-- Bootstrap Material Design V4.0 -->
	<script src="../js/bootstrap-material-design.min.js" ></script>
	<script>$(document).ready(function() { $('body').bootstrapMaterialDesign(); });</script>

	<script src="../js/main.js" ></script>
</body>
</html>