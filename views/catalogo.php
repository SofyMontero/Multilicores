<?php
//include_once "header.php";
require_once "../models/database.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../models/ProductoModel.php";
$categoria = $_GET['categoria'] ?? '';
$nombre = $_GET['nombre'] ?? '';
$busqueda = $_GET['id'] ?? '';

// Obtener productos desde la base de datos
$producto = new Producto();
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

$totalProductos = $producto->contarProductos($categoria);
$totalPaginas = ceil($totalProductos / $limit);

// Llamada al nuevo método paginado
$productos = $producto->obtenerProductos($categoria, $busqueda, $limit, $offset);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multilicores - Catálogo de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/catalogo.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <!-- Header Moderno -->
    <header class="header-modern bg-white border-bottom">
        <div class="container py-3">
            <nav class="navbar navbar-expand-lg navbar-light p-0 w-100">

                <!-- Logo + Título -->
                <div class="d-flex align-items-center gap-2">
                    <img src="../assets/img/logoM.png" alt="Logo Multilicores" class="logo-img" style="height: 50px;">
                    <div class="d-flex flex-column">
                        <h1 class="company-title m-0 fs-6">Multilicores</h1>
                        <p class="company-subtitle m-0 small">Distribución especializada en Licores</p>
                    </div>
                </div>

                <!-- Botón hamburguesa -->
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </nav>

            <!-- Segundo bloque: buscador y carrito -->
            <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between gap-3 mt-3">

                <!-- Menú (colapsable solo en móvil) -->
                <div class="collapse navbar-collapse" id="navbarMenu">
                    <ul class="navbar-nav flex-column flex-lg-row gap-2 gap-lg-4">
                        <li class="nav-item"><a class="nav-link fw-semibold text-muted" href="categorias.php">Categorías</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-muted" href="promociones.php">Promociones</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-decoration-underline" href="catalogo.php">Productos</a></li>
                    </ul>
                </div>

                <!-- Buscador y carrito -->
                <div class="d-flex flex-grow-1 align-items-center gap-2">
                    <input type="text" class="form-control search-input" placeholder="Buscar productos..." id="searchInput" autocomplete="off">
                    <div class="position-relative" id="cartIcon">
                        <button class="btn btn-outline-secondary position-relative" type="button">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success" id="cartCount">0</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </header>
    <!-- Catálogo de Productos -->
    <div class="container">
        <div class="catalog-header">
        </div>

        <form method="POST" action="procesar_pedido.php" id="orderForm">
            <div class="row" id="productGrid">
                <?php if (!empty($productos)): ?>
                    <?php foreach ($productos as $index => $prod): ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4 product-item" data-name="<?php echo strtolower($prod['descripcion_producto']); ?>">
                            <div class="product-card">
                                <div class="position-relative">
                                    <div class="    ">
                                        <img src="<?php echo '../assets/img/licores/' . $prod['imagen_producto']; ?>"
                                            class="product-image"
                                            loading="lazy"
                                            alt="<?php echo htmlspecialchars($prod['descripcion_producto']); ?>"
                                            onerror="this.src='/placeholder.svg?height=220&width=300&text=Producto'">
                                    </div>
                                    <div class="category-badge">
                                        <?php echo strtoupper($categoria ?: 'LICOR'); ?>
                                    </div>
                                </div>

                                <div class="p-3">
                                    <h5 class="product-title"><?php echo htmlspecialchars($prod['descripcion_producto']); ?></h5>

                                    <div class="price-container">
                                        <div class="price-row">
                                            <span class="price-label">Precio Unidad:</span>
                                            <span class="price-unidad">$<?php echo number_format($prod['precio_unidad_producto'], 0, ',', '.'); ?> COP</span>
                                        </div>
                                        <div class="price-row">
                                            <span class="price-label">Precio Paca:</span>
                                            <span class="price-paca">$<?php echo number_format($prod['precio_paca_producto'], 0, ',', '.'); ?> COP</span>
                                        </div>
                                    </div>

                                    <div class="row align-items-end g-2">
                                        <div class="col-6">
                                            <label class="form-label small">Tipo</label>
                                            <select name="productos[<?php echo $index; ?>][tipo]"
                                                class="form-select tipo-select form-select-sm"
                                                data-index="<?php echo $index; ?>"
                                                data-precio-unidad="<?php echo $prod['precio_unidad_producto']; ?>"
                                                data-precio-paca="<?php echo $prod['precio_paca_producto']; ?>">
                                                <option value="">Tipo</option>
                                                <option value="unidad">Unidad</option>
                                                <option value="paca">Paca</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">Cant.</label>
                                            <input type="number"
                                                name="productos[<?php echo $index; ?>][cantidad]"
                                                class="form-control form-control-sm cantidad-input"
                                                min="1"
                                                placeholder="1"
                                                data-index="<?php echo $index; ?>">
                                        </div>
                                    </div>

                                    <!-- Botón Agregar -->
                                    <div class="text-end mt-2">
                                        <button type="button"
                                            class="btn btn-outline-success w-100 agregar-btn"
                                            data-index="<?php echo $index; ?>">
                                            <i class="fas fa-cart-plus me-1"></i> Agregar
                                        </button>
                                    </div>


                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No hay productos registrados</h4>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Resumen del Pedido -->
            <div class="order-summary" id="orderSummary" style="display: none;">
                <div class="summary-header">
                    <h3 class="summary-title">Resumen del Pedido</h3>
                    <span class="items-badge" id="itemsCount">0 productos</span>
                </div>
                <div class="total-amount">
                    Total: $<span id="totalAmount">0</span> COP
                </div>
            </div>

            <div class="text-center mb-5">
                <button type="submit" class="btn btn-success submit-btn" id="submitBtn" disabled>
                    <i class="fas fa-shopping-cart me-2"></i>
                    Enviar Pedido <span id="btnItemCount"></span>
                </button>
            </div>
            <div id="noResults" class="text-center text-muted py-4" style="display: none;">
                <i class="fas fa-search fa-2x mb-2"></i>
                <p>No se encontraron productos.</p>
            </div>
        </form>

        <?php if ($totalPaginas > 1): ?>
            <nav class="d-flex justify-content-center mb-5">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?categoria=<?php echo urlencode($categoria); ?>&page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let pedido = {};
        let totalGeneral = 0;
        let totalItems = 0;

        // Función para calcular subtotal
        function calcularSubtotal(index) {
            const tipoSelect = document.querySelector(`select[data-index="${index}"]`);
            const cantidadInput = document.querySelector(`input[data-index="${index}"]`);
            const subtotalContainer = document.getElementById(`subtotal-${index}`);
            const subtotalAmount = subtotalContainer.querySelector('.subtotal-amount');

            const tipo = tipoSelect.value;
            const cantidad = parseInt(cantidadInput.value) || 0;
            const precioUnidad = parseFloat(tipoSelect.dataset.precioUnidad);
            const precioPaca = parseFloat(tipoSelect.dataset.precioPaca);

            if (tipo && cantidad > 0) {
                const precio = tipo === 'paca' ? precioPaca : precioUnidad;
                const subtotal = precio * cantidad;

                subtotalAmount.textContent = subtotal.toLocaleString('es-CO');
                subtotalContainer.style.display = 'block';

                // Actualizar pedido
                pedido[index] = {
                    tipo: tipo,
                    cantidad: cantidad,
                    subtotal: subtotal
                };
            } else {
                subtotalContainer.style.display = 'none';
                delete pedido[index];
            }

            actualizarResumen();
        }

        // Función para actualizar resumen del pedido
        function actualizarResumen() {
            totalGeneral = 0;
            totalItems = 0;

            Object.values(pedido).forEach(item => {
                totalGeneral += item.subtotal;
                totalItems += item.cantidad;
            });

            const orderSummary = document.getElementById('orderSummary');
            const totalAmount = document.getElementById('totalAmount');
            const itemsCount = document.getElementById('itemsCount');
            const submitBtn = document.getElementById('submitBtn');
            const btnItemCount = document.getElementById('btnItemCount');

            if (totalItems > 0) {
                orderSummary.style.display = 'block';
                totalAmount.textContent = totalGeneral.toLocaleString('es-CO');
                itemsCount.textContent = `${totalItems} ${totalItems === 1 ? 'producto' : 'productos'}`;
                submitBtn.disabled = false;
                btnItemCount.textContent = `(${totalItems})`;
            } else {
                orderSummary.style.display = 'none';
                submitBtn.disabled = true;
                btnItemCount.textContent = '';
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Listeners para selects de tipo
            document.querySelectorAll('.tipo-select').forEach(select => {
                select.addEventListener('change', function() {
                    const index = this.dataset.index;
                    calcularSubtotal(index);
                });
            });

            // Listeners para inputs de cantidad
            document.querySelectorAll('.cantidad-input').forEach(input => {
                input.addEventListener('input', function() {
                    const index = this.dataset.index;
                    calcularSubtotal(index);
                });
            });

            // Funcionalidad de búsqueda
            const initialSearch = "<?php echo htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>";
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const productItems = document.querySelectorAll('.product-item');

                if (isset($_GET['busqueda'])) {
                    const productItems = $_GET['busqueda'];

                }

                productItems.forEach(item => {
                    const productName = item.dataset.name;
                    if (productName.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Validación del formulario
            document.getElementById('orderForm').addEventListener('submit', function(e) {
                if (totalItems === 0) {
                    e.preventDefault();
                    alert('Por favor selecciona al menos un producto antes de enviar el pedido.');
                    return false;
                }

                // Confirmar pedido
                const confirmMessage = `¿Confirmas el envío del pedido?\n\nTotal de productos: ${totalItems}\nTotal a pagar: $${totalGeneral.toLocaleString('es-CO')} COP`;
                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                    return false;
                }
            });
        });

        const noResults = document.getElementById('noResults');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const productItems = document.querySelectorAll('.product-item');
            let found = 0;

            productItems.forEach(item => {
                const productName = item.dataset.name;
                if (productName.includes(searchTerm)) {
                    item.style.display = 'block';
                    found++;
                } else {
                    item.style.display = 'none';
                }
            });

            noResults.style.display = found === 0 ? 'block' : 'none';
        });
    </script>
</body>

</html>