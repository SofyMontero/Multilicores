<?php
// include_once "header.php";
require_once "../models/database.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../models/ProductoModel.php";

// Obtener productos desde la base de datos
$producto = new Producto();
$productos = $producto->obtenerCategorias();
$importados = $_GET['importados'] ?? null;
$busqueda = $_GET['busqueda'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multilicores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/categoria.css" rel="stylesheet" type="text/css" />   
</head>

<body>
    <header class="header-empresarial">
        <div class="container-fluid px-3 px-md-4">
            <div class="text-center">
                <div class="mb-3">
                    <i class="fas fa-wine-bottle icono-principal"></i>
                    <h1 class="titulo-principal d-inline">Multilicores</h1>
                </div>
                <p class="text-muted mb-0" style="font-size: clamp(0.9rem, 2.5vw, 1.1rem);">
                    Distribución especializada en Licores 
                </p>
            </div>
        </div>
    </header>

    <section style="padding: clamp(2rem, 5vw, 4rem) 0;">
        <div class="container-fluid px-3 px-md-4">
            <!-- Header con título y buscador -->
            <div class="header-with-search">
                <div class="title-section">
                    <h2 class="titulo-seccion mb-0">Nuestras Categorías</h2>
                    <div class="linea-divisoria"></div>
                </div>
                
                <div class="search-container-right">
                    <form class="search-form-compact" method="GET" action="">
                        <input 
                            type="text" 
                            class="form-control search-input-compact" 
                            id="searchInput"
                            name="busqueda"
                            placeholder="Buscar categorías..."
                            value="<?php echo htmlspecialchars($busqueda); ?>"
                            autocomplete="off">
                        
                        <button type="submit" class="search-btn-compact">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Información de resultados -->
            <div id="searchInfo" class="search-results-info" style="display: none;">
                <i class="fas fa-info-circle me-2"></i>
                <span id="searchInfoText"></span>
            </div>

            <?php if (!empty($productos)): ?>
                <div class="grid-container" id="categoriasContainer">
                    <?php foreach ($productos as $prod): ?>
                        <?php
                        $imagen = !empty($prod['imagen_categoria']) ? $prod['imagen_categoria'] : 'placeholder.jpg';
                        ?>
                        <div class="card-categoria" data-categoria="<?php echo strtolower(htmlspecialchars($prod['nombre_categoria'])); ?>">
                            <div class="card-imagen loading">
                                <img
                                    src="<?php echo '../assets/img/licores/' . $imagen; ?>"
                                    alt="<?php echo htmlspecialchars($prod['nombre_categoria']); ?>"
                                    loading="lazy"
                                    onload="this.classList.add('loaded'); this.parentElement.classList.remove('loading')"
                                    onerror="this.onerror=null; this.src='../assets/img/licores/placeholder.jpg';">
                            </div>
                            <div class="card-body-custom">
                                <h5 class="card-titulo categoria-nombre"><?php echo htmlspecialchars($prod['nombre_categoria']); ?></h5>
                                <p class="card-descripcion">
                                    Selección premium de <?php echo strtolower(htmlspecialchars($prod['nombre_categoria'])); ?>
                                </p>
                                <a href="catalogo.php?categoria=<?php echo urlencode($prod['id_categoria']); ?>&nombre=<?php echo urlencode($prod['nombre_categoria']); ?>"
                                    class="btn-categoria"
                                    role="button"
                                    aria-label="Ver productos de <?php echo htmlspecialchars($prod['nombre_categoria']); ?>">
                                    Ver productos <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <div class="alert alert-info d-inline-block">
                        <i class="fas fa-info-circle me-2"></i>
                        No hay categorías registradas
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchInfo = document.getElementById('searchInfo');
            const searchInfoText = document.getElementById('searchInfoText');
            const categorias = document.querySelectorAll('.card-categoria');
            
            function highlightText(text, search) {
                if (!search) return text;
                const regex = new RegExp(`(${search})`, 'gi');
                return text.replace(regex, '<span class="highlight">$1</span>');
            }
            
            function filterCategorias(searchTerm) {
                const term = searchTerm.toLowerCase().trim();
                let visibleCount = 0;
                
                categorias.forEach(categoria => {
                    const nombreCategoria = categoria.dataset.categoria;
                    const tituloElement = categoria.querySelector('.categoria-nombre');
                    const originalText = tituloElement.textContent;
                    
                    if (nombreCategoria.includes(term)) {
                        categoria.classList.remove('hidden');
                        visibleCount++;
                        
                        if (term) {
                            tituloElement.innerHTML = highlightText(originalText, term);
                        } else {
                            tituloElement.textContent = originalText;
                        }
                    } else {
                        categoria.classList.add('hidden');
                        tituloElement.textContent = originalText;
                    }
                });
                
                if (term) {
                    searchInfo.style.display = 'block';
                    if (visibleCount === 0) {
                        searchInfoText.textContent = 'No se encontraron categorías.';
                        searchInfo.className = 'search-results-info alert alert-warning';
                    } else {
                        searchInfoText.textContent = `${visibleCount} categoría${visibleCount !== 1 ? 's' : ''} encontrada${visibleCount !== 1 ? 's' : ''}`;
                        searchInfo.className = 'search-results-info alert alert-info';
                    }
                } else {
                    searchInfo.style.display = 'none';
                }
            }
            
            searchInput.addEventListener('input', function() {
                filterCategorias(this.value);
            });
            
            if (searchInput.value) {
                filterCategorias(searchInput.value);
            }
        });
    </script>

</body>

</html>

