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

				<a href="client-new.php" class="tile">
					<div class="tile-tittle">Clientes</div>
					<div class="tile-icon">
						<i class="fas fa-users fa-fw"></i>
						
					</div>
				</a>

				<a href="item-list.php" class="tile">
					<div class="tile-tittle">Promociones</div>
					<div class="tile-icon">
						<i class="fas fa-pallet fa-fw"></i>
						
					</div>
				</a>

				<a href="reservation-list.php" class="tile">
					<div class="tile-tittle">Pedidos</div>
					<div class="tile-icon">
						<i class="fas fa-file-invoice-dollar fa-fw"></i>
						
					</div>
				</a>

				<a href="user-list.php" class="tile">
					<div class="tile-tittle">Usuarios</div>
					<div class="tile-icon">
						<i class="fas fa-user-secret fa-fw"></i>
						
					</div>
				</a>

				<a href="company.php" class="tile">
					<div class="tile-tittle">Empresa</div>
					<div class="tile-icon">
						<i class="fas fa-store-alt fa-fw"></i>
						
					</div>
				</a>
				
			</div>
			

		</section>
	</main>

<?php
include_once "footer.php";

?>
