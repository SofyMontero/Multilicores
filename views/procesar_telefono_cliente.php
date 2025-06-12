<?php
$productosPOST = [];
foreach ($_POST['productos'] as $index => $producto) {
    $productosPOST[] = $producto; // Normaliza el array
}
?>


<?php
// include_once "header.php";
require_once "../models/database.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../models/ProductoModel.php";
// Verificar que se recibieron datos
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['productos'])) {
    header('Location: catalogo.php?error=sin_productos');
    exit;
}
$numCliente = $_POST['numCliente'] ?? '';

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


<!-- Modal Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow rounded-4 border-0">
      <form id="formCliente" method="POST" action="guardar_cliente.php">
        <div class="modal-header bg-success text-white rounded-top-4 text-center d-flex flex-column justify-content-center w-100">
          <i class="bi bi-person-plus-fill fs-1 mb-2"></i>
          <h5 class="fw-bold mb-1">Tu número no está registrado con nosotros</h5>
          <p class="mb-0">Por favor regístrate</p>
        </div>

        <div class="modal-body p-4">
          <div class="mb-3">
            
            <input type="hidden" class="form-control" name="cli_identificacion" id="cli_identificacion" >
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
          <div class="mb-3">
          <select class="form-control" name="cli_zona" id="cliente_zona" required>
                                                <option value="" disabled selected>Seleccione una zona</option>
                                                <option value="Chapinero">Chapinero</option>
                                                <option value="Centro">Centro</option>
                                                <option value="Zona T">Zona T</option>
                                                <option value="45">45</option>
                                                <option value="Otra">Otra</option>
                                                <!-- Agrega más opciones según tu necesidad -->
                                            </select>
            </div>
          <!-- ✅ Nuevo campo Bar -->
          <div class="mb-3">
            <label for="nombre_bar" class="form-label">Bar <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="cli_bar" id="cli_bar" maxlength="40" required>
          </div>
        </div>

        <div class="modal-footer justify-content-center pb-4">
          <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="submit" class="btn btn-success px-4 rounded-pill">
            Guardar Cliente
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de búsqueda de cliente -->
<div class="modal fade" id="modalBuscarTelefono" tabindex="-1" aria-labelledby="modalBuscarTelefonoLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow rounded-4 border-0">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title mx-auto" id="modalBuscarTelefonoLabel">
          <i class="bi bi-search me-2"></i>Ecribe tu teléfono para completar el pedido
        </h5>
      </div>
      <div class="modal-body p-4 text-center">
        <label for="inputTelefono" class="form-label">Teléfono del cliente</label>
        <input type="text" class="form-control text-center fw-bold fs-5" id="inputTelefono" placeholder="Ej: 3001234567" maxlength="15" required>
        <div id="resultadoBusqueda" class="mt-3 text-muted small"></div>
      </div>
    </div>
  </div>
</div>


<script>
    const totalGeneral = <?php echo json_encode($_POST['total_general'] ?? 0); ?>;
    const productosPOST = <?php echo json_encode($productosPOST); ?>;
    console.log("🧾 ProductosPOST:", productosPOST);

    function enviarPedido() {
        if (!numcliente) {             // Asegúrate de tener el número
            alert('No se encontró numCliente; no se envía el pedido.');
            return;
        }

        // 1. Crea el formulario
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'procesar_pedido.php';   // <-- cámbialo si tu ruta es distinta

        // 2. Agrega productos[]
        productosPOST.forEach((producto, i) => {
        for (const clave in producto) {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = `productos[${i}][${clave}]`;
            input.value = producto[clave];
            form.appendChild(input);
        }
        });

        // 3. Agrega numCliente
        const inputCli = document.createElement('input');
        inputCli.type  = 'hidden';
        inputCli.name  = 'numCliente';      // nombre que procesar_pedido.php recibirá
        inputCli.value = numcliente;
        form.appendChild(inputCli);

            // 4. Agrega total_general
            const inputTotal = document.createElement('input');
            inputTotal.type = 'hidden';
            inputTotal.name = 'total_general';
            inputTotal.value = totalGeneral;
            form.appendChild(inputTotal);

        // 5. Envía
        document.body.appendChild(form);
        form.submit();
    }

document.addEventListener("DOMContentLoaded", function () {
    <?php if ($mostrarModalCliente=="no_existe"): ?>
        const modalCliente = new bootstrap.Modal(document.getElementById('modalCliente'));
        modalCliente.show();
    <?php endif; ?>
});
let numcliente = null;
document.addEventListener("DOMContentLoaded", function () {
    <?php if ($mostrarModalCliente=="existe"): ?>
        numcliente = <?php echo json_encode($numCliente); ?>;
        console.log("Cliente ya existe. Enviando pedido directamente...");
            enviarPedido();
            return;
    <?php endif; ?>
});



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
                        console.log('Cliente asignado:', numcliente);

                        // 🚀  Una vez asignado, enviamos el pedido
                        enviarPedido();
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
    e.preventDefault();

    const formData = new FormData(formCliente);
    const telefonoInput = document.getElementById('cli_telefono');
    const telefono = telefonoInput.value.trim();

    if (telefono.length < 10) {
      alert("Número de teléfono inválido");
      return;
    }

    fetch('../controllers/ajax/guardar_cliente.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.exito) {
        alert('Cliente guardado exitosamente');
        numcliente = telefono;
        enviarPedido();
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