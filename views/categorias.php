<?php
// include_once "header.php";
require_once "../models/database.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../models/ProductoModel.php";
$numCliente = $_GET['idCli'] ?? '';

$mostrarModalCliente = "existe";

if (!empty($numCliente)) {
    $pedido = new Producto(); // Asegúrate de que tu clase tiene $this->pdo inicializado
    if (!$pedido->clienteExistePorTelefono($numCliente)) {
        $mostrarModalCliente = "no_existe"; // cliente no existe
    }
} else {
    $mostrarModalCliente = "no_llego"; // no vino el número
}
// Obtener productos desde la base de datos
$producto = new Producto();
$productos = $producto->obtenerCategorias();
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
   <header class="header-modern bg-white border-bottom">
    <div class="container py-3">
        <nav class="navbar navbar-expand-lg navbar-light p-0">
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <img src="../assets/img/logoM.png" alt="Logo Multilicores" class="logo-img" style="height: 50px;">
                <div class="d-flex flex-column">
                    <h1 class="company-title m-0 fs-5">Multilicores</h1>
                    <p class="company-subtitle m-0 small">Distribución especializada en Licores</p>
                </div>
            </div>

            <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse mt-3 mt-lg-0" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-4">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-muted" href="categorias.php">Categorías</a>
                    </li>
                    <li class="nav-item">
                        <!-- <a class="nav-link fw-semibold text-muted" href="promociones.php">Promociones</a> -->
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-muted" href="catalogo.php">Productos</a>
                    </li>
                </ul>

                <form class="d-flex align-items-center gap-2 mt-3 mt-lg-0" role="search" onsubmit="event.preventDefault();">
                    <div class="position-relative w-100">
                        <input type="text" class="form-control search-input" placeholder="Buscar productos..." id="searchInput" autocomplete="off">
                        <ul id="autocompleteList" class="list-group position-absolute w-100 shadow-sm z-3" style="top: 100%; display: none;"></ul>
                    </div>
                    <button class="btn btn-primary px-3 search-btn" type="button" id="btnBuscarProducto">
                        <i class="fas fa-search text-white"></i>
                    </button>
                </form>
            </div>
        </nav>
    </div>
</header>


    <section style="padding: clamp(2rem, 5vw, 4rem) 0;">
        <div class="container-fluid px-3 px-md-4">

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
                                <a href="catalogo.php?categoria=<?php echo urlencode($prod['id_categoria']); ?>&nombre=<?php echo urlencode($prod['nombre_categoria']); ?>&idCli=<?php echo urlencode($numCliente); ?>"
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
<!-- Modal Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formCliente" method="POST" action="guardar_cliente.php">
        <div class="modal-header">
          <h5 class="modal-title" id="modalClienteLabel">Registra tus datos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">

          <div class="mb-3">
            <label for="cli_identificacion" class="form-label">Identificación</label>
            <input type="text" class="form-control" name="cli_identificacion" id="cli_identificacion" required>
          </div>

          <div class="mb-3">
            <label for="cli_nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" name="cli_nombre" id="cli_nombre" required>
          </div>

          <div class="mb-3">
            <label for="cli_telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" name="cli_telefono" id="cli_telefono" required>
          </div>

          <div class="mb-3">
            <label for="cli_direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" name="cli_direccion" id="cli_direccion" required>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar Cliente</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal de búsqueda de cliente -->
<div class="modal fade" id="modalBuscarTelefono" tabindex="-1" aria-labelledby="modalBuscarTelefonoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalBuscarTelefonoLabel">Ingresar número de teléfono</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <label for="inputTelefono" class="form-label">Teléfono del cliente</label>
        <input type="text" class="form-control" id="inputTelefono" placeholder="Ej: 3001234567" maxlength="15" required>
        <div id="resultadoBusqueda" class="mt-2 text-muted small"></div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    <?php if ($mostrarModalCliente=="no_existe"): ?>
        const modalCliente = new bootstrap.Modal(document.getElementById('modalCliente'));
        modalCliente.show();
    <?php endif; ?>
});
let numcliente = null;

document.addEventListener('DOMContentLoaded', function () {
    const inputTelefono = document.getElementById('inputTelefono');
    const resultadoBusqueda = document.getElementById('resultadoBusqueda');

    // Mostrar el modal si no hay número
    <?php if ($mostrarModalCliente == "no_llego"): ?>
        const modalBuscar = new bootstrap.Modal(document.getElementById('modalBuscarTelefono'));
        modalBuscar.show();
    <?php endif; ?>

    inputTelefono.addEventListener('input', function () {
        const numero = inputTelefono.value.trim();
        resultadoBusqueda.textContent = '';

        if (numero.length >= 10) {
            fetch(`../controllers/ajax/buscar_cliente.php?telefono=${numero}`)
                .then(res => res.json())
                .then(data => {
                    if (data.existe) {
                        resultadoBusqueda.textContent = `Cliente encontrado: ${data.nombre}`;
                        numcliente = numero;

                        setTimeout(() => {
                            bootstrap.Modal.getInstance(document.getElementById('modalBuscarTelefono')).hide();
                            console.log("Cliente asignado:", numcliente);

                            // Actualizar todos los enlaces con ?idCli=
                            const links = document.querySelectorAll('a[href*="catalogo.php"]');
                            links.forEach(link => {
                                const url = new URL(link.href, window.location.origin);
                                url.searchParams.set('idCli', numcliente);
                                link.href = url.toString();
                            });

                        }, 1000);
                    } else {
                        resultadoBusqueda.textContent = 'Cliente no encontrado. Mostrando formulario...';
                        setTimeout(() => {
                            bootstrap.Modal.getInstance(document.getElementById('modalBuscarTelefono')).hide();
                            new bootstrap.Modal(document.getElementById('modalCliente')).show();
                        }, 1000);
                    }
                })
                .catch(() => {
                    resultadoBusqueda.textContent = 'Error al buscar cliente.';
                });
        }
    });
});
document.addEventListener('DOMContentLoaded', function () {
  const formCliente = document.getElementById('formCliente');

  formCliente.addEventListener('submit', function (e) {
    e.preventDefault(); // Evita el envío tradicional

    const formData = new FormData(formCliente);

    fetch('../controllers/ajax/guardar_cliente.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.exito) {
        // Oculta el modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalCliente'));
        modal.hide();

        // Opcional: actualiza variable numcliente y enlaces
        const telefono = document.getElementById('cli_telefono').value;
        numcliente = telefono;

        // Actualiza los links con el nuevo idCli
        const links = document.querySelectorAll('a[href*="catalogo.php"]');
        links.forEach(link => {
          const url = new URL(link.href, window.location.origin);
          url.searchParams.set('idCli', numcliente);
          link.href = url.toString();
        });

        alert('Cliente guardado exitosamente');

      } else {
        alert('Error al guardar el cliente: ' + (data.mensaje || ''));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al guardar el cliente');
    });
  });
});
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.querySelector('.search-btn');

            function redirigirBusqueda() {
                const termino = searchInput.value.trim();
                if (termino.length > 0) {
                    const encodedTerm = encodeURIComponent(termino);
                    window.location.href = `catalogo.php?busqueda=${encodedTerm}`;
                }
            }
            searchButton.addEventListener('click', redirigirBusqueda);

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    redirigirBusqueda();
                }
            });
        });

        // autocompletar

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchList = document.getElementById('autocompleteList');
            const searchButton = document.querySelector('.search-btn');

            function redirigirBusqueda(nombre, id) {
                const productoId = id || null;
                const encoded = encodeURIComponent(nombre || searchInput.value.trim());

                if (productoId) {
                    window.location.href = `catalogo.php?id=${productoId}`;
                } else if (encoded) {
                    window.location.href = `catalogo.php?busqueda=${encoded}`;
                }
            }

            function mostrarSugerencias(sugerencias) {
                searchList.innerHTML = '';
                if (sugerencias.length === 0) {
                    searchList.style.display = 'none';
                    return;
                }

                sugerencias.forEach(producto => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item list-group-item-action';
                    li.textContent = producto.descripcion_producto;
                    li.dataset.id = producto.id_producto;
                    li.addEventListener('click', () => redirigirBusqueda(null, producto.id_producto));
                    searchList.appendChild(li);
                });

                searchList.style.display = 'block';
            }

            let timeout;
            searchInput.addEventListener('input', function() {
                const termino = this.value.trim();

                if (termino.length < 2) {
                    searchList.style.display = 'none';
                    return;
                }

                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    fetch(`../controllers/buscar_sugerencias.php?q=${encodeURIComponent(termino)}`)
                        .then(res => res.json())
                        .then(data => mostrarSugerencias(data))
                        .catch(() => searchList.style.display = 'none');
                }, 200);
            });

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    redirigirBusqueda();
                }
            });

            searchButton.addEventListener('click', () => redirigirBusqueda());

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchList.contains(e.target)) {
                    searchList.style.display = 'none';
                }
            });
        });
    </script>

</body>

</html>