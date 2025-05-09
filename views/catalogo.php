<?php 
include_once "header.php";
require_once "../models/database.php";

// Simulación de productos (puedes adaptar esto a tu base de datos real)
$productos = [
    [
        "id" => 1,
        "nombre" => "Ron Medellín 750ml",
        "descripcion" => "Ron añejado colombiano.",
        "precio_unidad" => 35000,
        "precio_paca" => 200000, // Por ejemplo, 6 botellas
        "imagen" => "https://via.placeholder.com/150"
    ],
    [
        "id" => 2,
        "nombre" => "Aguardiente Antioqueño 375ml",
        "descripcion" => "Tradición paisa en una botella.",
        "precio_unidad" => 18000,
        "precio_paca" => 100000,
        "imagen" => "https://via.placeholder.com/150"
    ],
    [
        "id" => 3,
        "nombre" => "Cerveza Poker Lata",
        "descripcion" => "Cerveza rubia refrescante.",
        "precio_unidad" => 3500,
        "precio_paca" => 40000,
        "imagen" => "https://via.placeholder.com/150"
    ]
];
?>

<div class="container mt-5">
    <h2 class="text-center mb-4"><i class="fas fa-store"></i> Catálogo de Licores</h2>
    <form method="POST" action="procesar_pedido.php">
        <div class="row">
            <?php foreach ($productos as $producto): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= $producto['imagen'] ?>" class="card-img-top" alt="<?= $producto['nombre'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $producto['nombre'] ?></h5>
                            <p class="card-text"><?= $producto['descripcion'] ?></p>
                            <p class="card-text">
                                <strong>Precio Unidad:</strong> $<?= number_format($producto['precio_unidad']) ?> COP<br>
                                <strong>Precio Paca:</strong> $<?= number_format($producto['precio_paca']) ?> COP
                            </p>

                            <div class="form-group">
                                <label for="tipo_<?= $producto['id'] ?>">Tipo de venta</label>
                                <select name="productos[<?= $producto['id'] ?>][tipo]" class="form-control" required>
                                    <option value="unidad">Unidad</option>
                                    <option value="paca">Paca</option>
                                </select>
                            </div>

                            <div class="form-group mt-2">
                                <label for="cantidad_<?= $producto['id'] ?>">Cantidad</label>
                                <input type="number" name="productos[<?= $producto['id'] ?>][cantidad]" class="form-control" min="1" required>
                            </div>

                            <input type="hidden" name="productos[<?= $producto['id'] ?>][id]" value="<?= $producto['id'] ?>">
                            <input type="hidden" name="productos[<?= $producto['id'] ?>][nombre]" value="<?= $producto['nombre'] ?>">

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success btn-lg">Enviar Pedido</button>
        </div>
    </form>
</div>

<?php include_once "footer.php"; ?>