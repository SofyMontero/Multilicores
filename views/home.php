<?php
include_once "header.php";
require_once "../models/database.php";
?>

			<!-- Page header -->
			<div class="full-box page-header">
				<h3 class="text-left">
					<i class="fab fa-dashcube fa-fw"></i> &nbsp; HOME
				</h3>
				<p class="text-justify">
					Bienvenido a Multilicores.
				</p>
			</div>
			
			<!-- Content -->
			<div class="full-box tile-container">

				<a href="clientes.php" class="tile">
					<div class="tile-tittle">Clientes</div>
					<div class="tile-icon">
						<i class="fas fa-address-book fa-fw"></i>
						
					</div>
				</a>

				<a href="promo.php" class="tile">
					<div class="tile-tittle">Promociones</div>
					<div class="tile-icon">
						<i class="fas fa-bullhorn fa-fw"></i>
						
					</div>
				</a>

				<a href="cartera.php" class="tile">
					<div class="tile-tittle">Pedidos</div>
					<div class="tile-icon">
						<i class="fas fa-file-invoice-dollar fa-fw"></i>
						
					</div>
				</a>

				<a href="user-list.php" class="tile">
					<div class="tile-tittle">Usuarios</div>
					<div class="tile-icon">
						<i class="fas fa-user fa-fw"></i>						
					</div>
				</a>

				<a href="company.php" class="tile">
					<div class="tile-tittle">Solicitudes</div>
					<div class="tile-icon">						
					<i class="fas fa-shopping-cart fa-fw"></i>
		
					</div>
				</a>
				<a href="categorias.php" class="tile">
				    <div class="tile-tittle">Catálogo</div>
 					   <div class="tile-icon">
 					       <i class="fas fa-th-large fa-fw"></i> <!-- Icono tipo grid para categorías -->
 					   </div>
				</a>

				<a href="Subir_excel_producto.php" class="tile">
   				 <div class="tile-tittle">Cargar Productos</div>
   					 <div class="tile-icon">
   					     <i class="fas fa-file-upload fa-fw"></i> <!-- Icono de carga de archivo -->
  					  </div>
				</a>
				
			</div>
			

		</section>
	</main>

<?php
include_once "footer.php";

?>
