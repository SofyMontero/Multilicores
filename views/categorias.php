<?php include_once "header.php"; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4"><i class="fas fa-tags"></i> Selecciona una Categoría</h2>
    <div class="row text-center">
        <div class="col-md-4 mb-3">
            <a href="catalogo.php?categoria=ron" class="btn btn-dark btn-lg w-100">Rones</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="catalogo.php?categoria=cerveza" class="btn btn-warning btn-lg w-100">Cervezas</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="catalogo.php?categoria=aguardiente" class="btn btn-success btn-lg w-100">Aguardientes</a>
        </div>
    </div>
</div>

<?php include_once "footer.php"; ?>