<?php include_once "header.php"; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4"><i class="fas fa-tags"></i> Selecciona una Categoría</h2>
    <div class="row">
        <!-- RON -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <a href="catalogo.php?categoria=ron">
                    <img src="https://cdn.pixabay.com/photo/2020/03/20/15/50/rum-4951796_960_720.jpg" class="card-img-top" alt="Rones">
                </a>
                <div class="card-body text-center">
                    <h5 class="card-title">Rones</h5>
                    <a href="catalogo.php?categoria=ron" class="btn btn-dark w-100">Ver productos</a>
                </div>
            </div>
        </div>

        <!-- CERVEZA -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <a href="catalogo.php?categoria=cerveza">
                    <img src="https://cdn.pixabay.com/photo/2017/05/07/08/56/beer-2293436_960_720.jpg" class="card-img-top" alt="Cervezas">
                </a>
                <div class="card-body text-center">
                    <h5 class="card-title">Cervezas</h5>
                    <a href="catalogo.php?categoria=cerveza" class="btn btn-warning w-100">Ver productos</a>
                </div>
            </div>
        </div>

        <!-- AGUARDIENTE -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <a href="catalogo.php?categoria=aguardiente">
                    <img src="https://cdn.pixabay.com/photo/2022/03/15/16/55/liquor-7070651_960_720.jpg" class="card-img-top" alt="Aguardientes">
                </a>
                <div class="card-body text-center">
                    <h5 class="card-title">Aguardientes</h5>
                    <a href="catalogo.php?categoria=aguardiente" class="btn btn-success w-100">Ver productos</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once "footer.php"; ?>