<?php
require_once "../models/database.php";
require_once "../models/ProductoModel.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$numCliente = $_GET['idCli'] ?? '';

// Obtener promociones usando la función existente en Producto
function obtenerPromociones()
{
    try {
        $producto = new Producto();
        $todasLasPromociones = $producto->getConnection(); // Retorna array con todas las promociones

        // Filtrar solo las promociones activas (estado = 1)
        $promocionesActivas = array_filter($todasLasPromociones, function ($promo) {
            return isset($promo['estado']) && $promo['estado'] == 1;
        });

        // Ordenar por prioridad y fecha
        usort($promocionesActivas, function ($a, $b) {
            // Primero por prioridad (desc)
            if ($a['prioridad'] != $b['prioridad']) {
                return $b['prioridad'] - $a['prioridad'];
            }
            // Luego por fecha de creación (desc)
            return strtotime($b['creado_en']) - strtotime($a['creado_en']);
        });

        return $promocionesActivas;
    } catch (Exception $e) {
        error_log("Error obteniendo promociones: " . $e->getMessage());
        return [];
    }
}

$promociones = obtenerPromociones();

function hayPromocionesActivas()
{
    try {
        $producto = new Producto();
        $promociones = $producto->getConnection();

        $promocionesActivas = array_filter($promociones, function ($promo) {
            return isset($promo['estado']) && $promo['estado'] == 1;
        });

        return count($promocionesActivas) > 0;
    } catch (Exception $e) {
        error_log("Error en hayPromocionesActivas: " . $e->getMessage());
        return false;
    }
}

$tienePromociones = hayPromocionesActivas();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promociones - Multilicores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/categoria.css" rel="stylesheet" type="text/css" />
    <style>
        .promocion-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: white;
            margin-bottom: 2rem;
        }

        .promocion-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .promocion-header {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .promocion-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        .promocion-titulo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .promocion-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .promocion-imagen {
            width: 100%;
            height: 200px;
            object-fit: contain;
            background-color: #f8f9fa;
            padding: 5px;
            border-radius: 8px;
        }

        .back-button {
            color: #6c757d;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-button:hover {
            color: #ff6b6b;
        }

        .no-promociones {
            text-align: center;
            padding: 4rem 2rem;
            color: #6c757d;
        }

        .no-promociones .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
</head>

<body>
    <header class="header-modern bg-white border-bottom">
        <div class="container py-3">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">

                <!-- Logo + título -->
                <div class="d-flex align-items-center gap-2">
                    <img src="../assets/img/logoM.png" alt="Logo Multilicores" class="logo-img" style="height: 50px;">
                    <div class="d-flex flex-column">
                        <h1 class="company-title m-0 fs-6">Multilicores</h1>
                        <p class="company-subtitle m-0 small">Distribución especializada en Licores</p>
                    </div>
                </div>

                <!-- Menú -->
                <nav class="navbar navbar-expand-lg navbar-light p-0 flex-grow-1">
                    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-center mt-3 mt-lg-0" id="navbarMenu">
                        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-4">
                            <li class="nav-item">
                                <a class="nav-link fw-semibold text-muted" href="categorias.php?idCli=<?php echo urlencode($numCliente); ?>">Categorías</a>
                            </li>
                            <?php if ($tienePromociones): ?>
                                <li class="nav-item">
                                    <a class="nav-link fw-semibold" href="promociones.php?idCli=<?php echo urlencode($numCliente); ?>" style="color: #ff6b6b;">
                                        <i class="fas fa-fire me-1"></i>Promociones
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a class="nav-link fw-semibold text-muted" href="catalogo.php?idCli=<?php echo urlencode($numCliente); ?>">Productos</a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Buscador + carrito -->
                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0 flex-shrink-0" style="min-width: 250px;">
                    <input type="text" class="form-control search-input" placeholder="Buscar promos..." id="searchInput" autocomplete="off">
                    <button class="btn btn-outline-secondary position-relative" type="button">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success" id="cartCount">0</span>
                    </button>
                </div>

            </div>
        </div>
    </header>

    <section style="padding: clamp(2rem, 5vw, 4rem) 0;">
        <div class="container">

            <!-- Breadcrumb -->
            <div class="mb-4">
                <a href="categorias.php?idCli=<?php echo urlencode($numCliente); ?>" class="back-button">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Categorías
                </a>
            </div>

            <!-- Título -->
            <div class="text-center mb-5">
                <h1 class="fw-bold mb-3" style="color: #ff6b6b;">
                    <i class="fas fa-fire me-3"></i>Promociones Especiales
                </h1>
                <p class="text-muted">Descubre nuestras mejores ofertas y descuentos únicos</p>
            </div>

            <?php if (!empty($promociones)): ?>
                <div class="row">
                    <?php foreach ($promociones as $pIndex => $promocion): ?>
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <!-- Agregamos también la clase .product-card para que el JS la encuentre -->
                            <div class="product-card promocion-card h-100">
                                <div class="promocion-header">
                                    <div class="promocion-badge">
                                        <i class="fas fa-percentage me-1"></i>Oferta Especial
                                    </div>
                                    <h3 class="promocion-titulo"><?php echo htmlspecialchars($promocion['titulo']); ?></h3>
                                </div>

                                <?php if (!empty($promocion['imagen'])): ?>
                                    <img src="../assets/img/licores/promos/<?php echo htmlspecialchars($promocion['imagen']); ?>"
                                        class="w-100 promocion-imagen"
                                        alt="<?php echo htmlspecialchars($promocion['titulo']); ?>"
                                        onerror="this.style.display='none'">
                                <?php endif; ?>

                                <div class="card-body p-4">
                                    <p class="card-text text-muted mb-4" style="line-height: 1.6;">
                                        <?php echo htmlspecialchars($promocion['descripcion']); ?>
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            Válida desde <?php echo date('d/m/Y', strtotime($promocion['creado_en'])); ?>
                                        </small>
                                        <span class="badge bg-success">Activa</span>
                                    </div>

                                    <!-- Controles mínimos para que tu JS funcione tal cual -->
                                    <div class="row align-items-end g-2 mt-3">
                                        <div class="col-6">
                                            <label class="form-label small">Tipo</label>
                                            <select
                                                name="productos[<?php echo $pIndex; ?>][tipo]"
                                                class="form-select tipo-select form-select-sm"
                                                data-index="<?php echo $pIndex; ?>">
                                                <option value="unidad">Unidad</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">Cant.</label>
                                            <input type="number"
                                                name="productos[<?php echo $pIndex; ?>][cantidad]"
                                                class="form-control form-control-sm cantidad-input"
                                                min="1"
                                                placeholder="1"
                                                data-index="<?php echo $pIndex; ?>">
                                        </div>
                                    </div>

                                    <!-- Botón Agregar -->
                                    <div class="text-end mt-3">
                                        <button type="button"
                                            class="btn btn-outline-success w-100 agregar-btn"
                                            data-id="<?php echo $promocion['codigo']; ?>"
                                            data-nombre="<?php echo htmlspecialchars($promocion['descripcion']); ?>">
                                            <i class="fas fa-cart-plus me-1"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-promociones">
                    <div class="icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3>No hay promociones activas</h3>
                    <p class="mb-4">Actualmente no tenemos promociones disponibles, pero mantente atento a futuras ofertas.</p>
                    <a href="categorias.php?idCli=<?php echo urlencode($numCliente); ?>" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Categorías
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- Este input es requerido por tu JS al procesar el pedido -->
    <input type="text" id="numCliente" data-numcliente="<?php echo htmlspecialchars($numCliente); ?>" hidden />

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Deja tu JS tal cual -->
    <script src="../js/catalogo.js"></script>
</body>

</html>