// Carrito de compras funcional
let carrito = [];
let totalGeneral = 0;

// Elementos del DOM
const cartCountElement = document.getElementById('cartCount');
const orderSummaryElement = document.getElementById('orderSummary');
const itemsCountElement = document.getElementById('itemsCount');
const totalAmountElement = document.getElementById('totalAmount');
const submitBtnElement = document.getElementById('submitBtn');
const btnItemCountElement = document.getElementById('btnItemCount');
const searchInput = document.getElementById('searchInput');
const productGrid = document.getElementById('productGrid');
const noResults = document.getElementById('noResults');

// Inicializar eventos
document.addEventListener('DOMContentLoaded', function() {
    // Agregar eventos a los botones
    document.querySelectorAll('.agregar-btn').forEach(btn => {
        btn.addEventListener('click', agregarAlCarrito);
    });

    // Evento para el buscador
    searchInput.addEventListener('input', filtrarProductos);

    // Evento para cambio de tipo (unidad/paca)
    document.querySelectorAll('.tipo-select').forEach(select => {
        select.addEventListener('change', actualizarPrecioMostrado);
    });

    // Evento para el botón del carrito
    document.querySelector('.btn-outline-secondary').addEventListener('click', toggleCarrito);

    // Actualizar contador inicial
    actualizarContadores();
});

// Función para agregar productos al carrito
function agregarAlCarrito(event) {
    const btn = event.target.closest('.agregar-btn');
    const productCard = btn.closest('.product-card');
    
    // Obtener datos del producto
    const id = btn.dataset.id;
    const nombre = btn.dataset.nombre;
    const precioUnidad = parseFloat(btn.dataset.precioUnidad);
    const precioPaca = parseFloat(btn.dataset.precioPaca);
    
    // Obtener valores del formulario
    const tipoSelect = productCard.querySelector('.tipo-select');
    const cantidadInput = productCard.querySelector('.cantidad-input');
    
    const tipo = tipoSelect.value;
    const cantidad = parseInt(cantidadInput.value) || 1;
    
    // Validaciones
    if (!tipo) {
        mostrarAlerta('Por favor selecciona el tipo (Unidad o Paca)', 'warning');
        tipoSelect.focus();
        return;
    }
    
    if (cantidad <= 0) {
        mostrarAlerta('La cantidad debe ser mayor a 0', 'warning');
        cantidadInput.focus();
        return;
    }
    
    // Calcular precio según el tipo
    const precioUnitario = tipo === 'paca' ? precioPaca : precioUnidad;
    const precioTotal = precioUnitario * cantidad;
    
    // Crear objeto del producto
    const producto = {
        id: id,
        nombre: nombre,
        tipo: tipo,
        cantidad: cantidad,
        precioUnitario: precioUnitario,
        precioTotal: precioTotal,
        timestamp: Date.now() // Para identificar productos únicos
    };
    
    // Agregar al carrito
    carrito.push(producto);
    
    // Actualizar interfaz
    actualizarContadores();
    mostrarAlerta(`${nombre} agregado al carrito`, 'success');
    
    // Limpiar formulario
    tipoSelect.value = '';
    cantidadInput.value = '';
    
    // Animación del botón
    btn.classList.add('btn-success');
    btn.innerHTML = '<i class="fas fa-check me-1"></i> Agregado';
    
    setTimeout(() => {
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-success');
        btn.innerHTML = '<i class="fas fa-cart-plus me-1"></i> Agregar';
    }, 1500);
}

// Función para actualizar contadores y resumen
function actualizarContadores() {
    const totalItems = carrito.length;
    totalGeneral = carrito.reduce((sum, item) => sum + item.precioTotal, 0);
    
    // Actualizar contador del carrito
    cartCountElement.textContent = totalItems;
    cartCountElement.style.display = totalItems > 0 ? 'block' : 'none';
    
    // Actualizar resumen del pedido
    if (totalItems > 0) {
        orderSummaryElement.style.display = 'block';
        itemsCountElement.textContent = `${totalItems} producto${totalItems !== 1 ? 's' : ''}`;
        totalAmountElement.textContent = new Intl.NumberFormat('es-CO').format(totalGeneral);
        
        // Habilitar botón de envío
        submitBtnElement.disabled = false;
        submitBtnElement.classList.remove('btn-secondary');
        submitBtnElement.classList.add('btn-success');
        btnItemCountElement.textContent = `(${totalItems})`;
    } else {
        orderSummaryElement.style.display = 'none';
        submitBtnElement.disabled = true;
        submitBtnElement.classList.remove('btn-success');
        submitBtnElement.classList.add('btn-secondary');
        btnItemCountElement.textContent = '';
    }
}

// Función para mostrar/ocultar carrito
function toggleCarrito() {
    if (carrito.length === 0) {
        mostrarAlerta('El carrito está vacío', 'info');
        return;
    }
    
    mostrarModalCarrito();
}

// Función para mostrar modal del carrito
function mostrarModalCarrito() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.setAttribute('tabindex', '-1');
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Carrito de Compras (${carrito.length} productos)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${carrito.map((item, index) => `
                                    <tr>
                                        <td class="fw-semibold">${item.nombre}</td>
                                        <td>
                                            <span class="badge bg-${item.tipo === 'paca' ? 'primary' : 'secondary'}">
                                                ${item.tipo.charAt(0).toUpperCase() + item.tipo.slice(1)}
                                            </span>
                                        </td>
                                        <td>${item.cantidad}</td>
                                        <td>$${new Intl.NumberFormat('es-CO').format(item.precioUnitario)}</td>
                                        <td class="fw-bold">$${new Intl.NumberFormat('es-CO').format(item.precioTotal)}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger" onclick="eliminarDelCarrito(${index})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Total General:</th>
                                    <th class="text-success fs-5">$${new Intl.NumberFormat('es-CO').format(totalGeneral)} COP</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" onclick="vaciarCarrito()">
                        <i class="fas fa-trash me-1"></i>
                        Vaciar Carrito
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                    <button type="button" class="btn btn-success" onclick="procesarPedido()">
                        <i class="fas fa-paper-plane me-1"></i>
                        Enviar Pedido
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Limpiar modal cuando se cierre
    modal.addEventListener('hidden.bs.modal', () => {
        document.body.removeChild(modal);
    });
}

// Función para eliminar producto del carrito
function eliminarDelCarrito(index) {
    if (index >= 0 && index < carrito.length) {
        const producto = carrito[index];
        carrito.splice(index, 1);
        actualizarContadores();
        mostrarAlerta(`${producto.nombre} eliminado del carrito`, 'info');
        
        // Si el modal está abierto, actualizarlo
        const modal = document.querySelector('.modal.show');
        if (modal) {
            modal.querySelector('.btn-close').click();
            if (carrito.length > 0) {
                setTimeout(() => mostrarModalCarrito(), 300);
            }
        }
    }
}

// Función para vaciar carrito
function vaciarCarrito() {
    if (carrito.length === 0) {
        mostrarAlerta('El carrito ya está vacío', 'info');
        return;
    }
    
    if (confirm('¿Estás seguro de que quieres vaciar el carrito?')) {
        carrito = [];
        actualizarContadores();
        mostrarAlerta('Carrito vaciado', 'success');
        
        // Cerrar modal si está abierto
        const modal = document.querySelector('.modal.show');
        if (modal) {
            modal.querySelector('.btn-close').click();
        }
    }
}

// Función para procesar pedido
function procesarPedido() {
    if (carrito.length === 0) {
        mostrarAlerta('El carrito está vacío', 'warning');
        return;
    }
    
    // Crear formulario dinámico con los productos del carrito
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'procesar_pedido.php';
    
    // Agregar productos al formulario
    carrito.forEach((item, index) => {
        // ID del producto
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = `productos[${index}][id]`;
        idInput.value = item.id;
        form.appendChild(idInput);
        
        // Nombre del producto
        const nombreInput = document.createElement('input');
        nombreInput.type = 'hidden';
        nombreInput.name = `productos[${index}][nombre]`;
        nombreInput.value = item.nombre;
        form.appendChild(nombreInput);
        
        // Tipo
        const tipoInput = document.createElement('input');
        tipoInput.type = 'hidden';
        tipoInput.name = `productos[${index}][tipo]`;
        tipoInput.value = item.tipo;
        form.appendChild(tipoInput);
        
        // Cantidad
        const cantidadInput = document.createElement('input');
        cantidadInput.type = 'hidden';
        cantidadInput.name = `productos[${index}][cantidad]`;
        cantidadInput.value = item.cantidad;
        form.appendChild(cantidadInput);
        
        // Precio unitario
        const precioInput = document.createElement('input');
        precioInput.type = 'hidden';
        precioInput.name = `productos[${index}][precio_unitario]`;
        precioInput.value = item.precioUnitario;
        form.appendChild(precioInput);
        
        // Subtotal
        const subtotalInput = document.createElement('input');
        subtotalInput.type = 'hidden';
        subtotalInput.name = `productos[${index}][subtotal]`;
        subtotalInput.value = item.precioTotal;
        form.appendChild(subtotalInput);
    });
    
    // Total general
    const totalInput = document.createElement('input');
    totalInput.type = 'hidden';
    totalInput.name = 'total_general';
    totalInput.value = totalGeneral;
    form.appendChild(totalInput);
    
    // Agregar formulario al DOM y enviarlo
    document.body.appendChild(form);
    form.submit();
}

// Función para filtrar productos
function filtrarProductos() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const productItems = document.querySelectorAll('.product-item');
    let visibleCount = 0;
    
    productItems.forEach(item => {
        const productName = item.dataset.name;
        const isVisible = productName.includes(searchTerm);
        
        item.style.display = isVisible ? 'block' : 'none';
        if (isVisible) visibleCount++;
    });
    
    // Mostrar/ocultar mensaje de "no hay resultados"
    noResults.style.display = visibleCount === 0 && searchTerm !== '' ? 'block' : 'none';
    productGrid.style.display = visibleCount === 0 && searchTerm !== '' ? 'none' : 'block';
}

// Función para actualizar precio mostrado según tipo seleccionado
function actualizarPrecioMostrado(event) {
    const select = event.target;
    const productCard = select.closest('.product-card');
    const precioUnidad = parseFloat(select.dataset.precioUnidad);
    const precioPaca = parseFloat(select.dataset.precioPaca);
    
    // Resaltar el precio correspondiente
    const precioUnidadElement = productCard.querySelector('.price-unidad');
    const precioPacaElement = productCard.querySelector('.price-paca');
    
    // Resetear estilos
    precioUnidadElement.style.fontWeight = 'normal';
    precioUnidadElement.style.color = '';
    precioPacaElement.style.fontWeight = 'normal';
    precioPacaElement.style.color = '';
    
    // Resaltar el precio seleccionado
    if (select.value === 'unidad') {
        precioUnidadElement.style.fontWeight = 'bold';
        precioUnidadElement.style.color = '#198754';
    } else if (select.value === 'paca') {
        precioPacaElement.style.fontWeight = 'bold';
        precioPacaElement.style.color = '#198754';
    }
}

// Función para mostrar alertas
function mostrarAlerta(mensaje, tipo = 'info') {
    // Crear alerta
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
    alerta.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    
    alerta.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alerta);
    
    // Auto-remover después de 3 segundos
    setTimeout(() => {
        if (alerta.parentNode) {
            alerta.remove();
        }
    }, 3000);
}

// Función para manejar el envío del formulario original
document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (carrito.length === 0) {
        mostrarAlerta('Agrega productos al carrito antes de enviar el pedido', 'warning');
        return;
    }
    
    procesarPedido();
});

// Función para persistir carrito en localStorage (opcional)
function guardarCarrito() {
    localStorage.setItem('carritoMultilicores', JSON.stringify(carrito));
}

function cargarCarrito() {
    const carritoGuardado = localStorage.getItem('carritoMultilicores');
    if (carritoGuardado) {
        try {
            carrito = JSON.parse(carritoGuardado);
            actualizarContadores();
        } catch (e) {
            console.error('Error al cargar carrito:', e);
            carrito = [];
        }
    }
}

// Cargar carrito al iniciar (opcional)
// cargarCarrito();

const originalAgregarAlCarrito = agregarAlCarrito;
agregarAlCarrito = function(event) {
    originalAgregarAlCarrito(event);
    guardarCarrito();
};

const originalEliminarDelCarrito = eliminarDelCarrito;
eliminarDelCarrito = function(index) {
    originalEliminarDelCarrito(index);
    guardarCarrito();
};

const originalVaciarCarrito = vaciarCarrito;
vaciarCarrito = function() {
    originalVaciarCarrito();
    guardarCarrito();
};
