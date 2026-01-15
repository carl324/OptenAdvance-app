@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('content')
<style>
  .alegra-modal-square {
    border-radius: 8px;
  }

  /* Estados del modal */
  .estado-modal {
    display: flex;
    flex-direction: column;
  }

  /* Inputs limpios */
  .alegra-input {
    background: #f9fafb;
    border: 1px solid #e6e8ee;
    box-shadow: none;
  }

  /* Medios de pago grandes */
  .pago-card-lg {
    background: #f9fafb;
    border: 1px solid #e6e8ee;
    border-radius: 6px;
    padding: 14px 8px;
    text-align: center;
    cursor: default;
  }

  .pago-card-lg i {
    font-size: 22px;
    display: block;
    margin-bottom: 6px;
    color: #365cf5;
  }

  .pago-card-lg span {
    font-size: 12px;
    font-weight: 500;
  }

  /* Total limpio y firme */
  .total-box-square {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 12px;
    background: #f4f6fb;
    border-radius: 6px;
    font-size: 14px;
  }

  .table .btn-sm {
    line-height: 1;
  }

  .table .lni-trash-can {
    color: #8a8fa7;
  }

  .modal-content {
    border-radius: 12px;
  }

  .modal-body .btn-light {
    padding: 10px 12px;
  }

  .search-pos {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  }

  .search-pos .form-control:focus {
    box-shadow: none;
  }

  .search-pos .input-group-text {
    color: #8a8fa7;
  }

  /* Alerta del carrito pequeña y elegante */
  #alerta-carrito {
    padding: 8px 12px !important;
    margin-bottom: 12px !important;
    font-size: 13px !important;
    border-radius: 6px !important;
  }

  #alerta-carrito .btn-close {
    padding: 0.25rem !important;
  }

  /* Spinner del modal */
  #estado-loading .spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f0f0f0;
    border-top-color: #365cf5;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }

  @keyframes spin {
    to { transform: rotate(360deg); }
  }
</style>

<section class="section">
    <div class="container-fluid">
        <div class="title-wrapper pt-30"></div>
        
        <div id="mensaje" class="alert alert-info alert-dismissible fade d-none mb-3" role="alert" style="margin: 15px 0;">
            <span id="texto-mensaje"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <div class="row">
            <!-- LEFT: Tabla de Productos -->
            <div class="col-lg-7">
                <div class="card-style mb-30">
                    <div class="title d-flex flex-wrap align-items-center justify-content-between">
                        <div class="right">
                            <div class="select-style-1">
                                <div class="input-group input-group-sm search-pos">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="lni lni-search-alt"></i>
                                    </span>
                                    <input type="text" id="buscar-producto" class="form-control bg-light border-0" placeholder="Buscar producto..." autocomplete="off" autofocus />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table top-selling-table">
                            <thead>
                                <tr>
                                    <th><h6 class="text-sm text-medium">Producto</h6></th>
                                    <th class="min-width"><h6 class="text-sm text-medium">Precio</h6></th>
                                    <th class="min-width"><h6 class="text-sm text-medium">IVA</h6></th>
                                    <th class="min-width"><h6 class="text-sm text-medium">Stock</h6></th>
                                </tr>
                            </thead>
                            <tbody id="tabla-productos">
                                <tr><td colspan="4" style="text-align: center; padding: 20px;">Cargando productos...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Carrito actual -->
            <div class="col-lg-5">
                <div class="card-style mb-30">
                    <div class="title mb-20">
                        <h6 class="text-medium">Venta actual</h6>
                        <p class="text-xs text-gray">Productos listos para vender</p>
                    </div>

                    <div class="table-responsive">
                        <div id="alerta-carrito" class="alert alert-danger fade d-none mb-3" role="alert">
                            <span id="texto-alerta-carrito"></span>
                        </div>
                        <table class="table">
                            <thead id="carrito-header">
                                <tr>
                                    <td class="text-sm">Sin productos</td>
                                </tr>
                            </thead>
                            <tbody id="carrito-contenido">
                                <tr>
                                    <td class="text-sm" colspan="3" style="text-align: center; padding: 20px; color: #999;">El carrito está vacío</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-20">
                        <span class="text-sm">Total</span>
                        <strong class="text-medium" id="total">$0</strong>
                    </div>

                    <div class="d-flex gap-2 mt-20">
                        <button class="main-btn light-btn btn-hover w-100" type="button" onclick="limpiarVenta()">
                            Cancelar
                        </button>
                        <button class="main-btn primary-btn btn-hover w-100" id="btn-finalizar" type="button" onclick="confirmarVenta()" disabled>
                            Vender
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de pago - Basado en estados -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content alegra-modal-square px-2">
            
            <!-- Estado: Formulario -->
            <div id="estado-formulario" class="estado-modal">
                <div class="modal-header border-0 pb-3">
                    <h6 class="text-medium mb-0">Finalizar venta</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-0">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <input type="text" id="cliente" class="form-control form-control-sm alegra-input-rounded" placeholder="Cliente" maxlength="25" oninput="validarCaracteres(this, 25, 'contador-cliente')" />
                            <small class="text-muted" id="contador-cliente" style="font-size: 11px; display: none;"></small>
                        </div>
                        <div class="col-6">
                            <input type="text" id="cliente_nit" class="form-control form-control-sm alegra-input-rounded" placeholder="NIT / Documento" maxlength="25" oninput="validarCaracteres(this, 25, 'contador-nit')" />
                            <small class="text-muted" id="contador-nit" style="font-size: 11px; display: none;"></small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <p class="text-xs text-gray mb-2">Método de pago</p>
                        <div class="row g-3">
                            <div class="col-4">
                                <div class="pago-card-lg py-4" style="cursor: pointer; border: 2px solid transparent; text-align: center;" onclick="seleccionarPago('efectivo')" id="pago-efectivo">
                                    <i class="lni lni-money-location mb-2" style="font-size: 24px; display: block;"></i>
                                    <span>Efectivo</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="pago-card-lg py-4" style="cursor: pointer; border: 2px solid transparent; text-align: center;" onclick="seleccionarPago('tarjeta')" id="pago-tarjeta">
                                    <i class="lni lni-credit-cards mb-2" style="font-size: 24px; display: block;"></i>
                                    <span>Tarjeta</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="pago-card-lg py-4" style="cursor: pointer; border: 2px solid transparent; text-align: center;" onclick="seleccionarPago('transferencia')" id="pago-transferencia">
                                    <i class="lni lni-apartment" style="font-size: 24px; display: block;"></i>
                                    <span>Transferencia</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="forma_pago" value="efectivo" />
                    </div>

                    <div class="total-box-square mt-3">
                        <span>Total</span>
                        <strong id="modal-total">$0</strong>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-3">
                    <div class="d-flex gap-3 w-100">
                        <button class="main-btn light-btn btn-hover flex-fill" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button class="main-btn primary-btn btn-hover flex-fill" id="btn-confirmar-pago" type="button" onclick="finalizarVenta()">
                            Finalizar venta
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estado: Loading -->
            <div id="estado-loading" class="estado-modal d-none">
                <div class="modal-body py-5 text-center">
                    <div class="spinner mb-3" style="margin: 0 auto;"></div>
                    <p class="text-sm text-gray">Procesando venta...</p>
                </div>
            </div>

            <!-- Estado: Éxito -->
            <div id="estado-exito" class="estado-modal d-none">
                <div class="modal-body py-5 text-center">
                    <div style="font-size: 64px; color: #4CAF50; margin-bottom: 16px;">
                        <i class="lni lni-checkmark-circle"></i>
                    </div>
                    <h5 class="text-medium" style="font-size: 18px;">¡Venta registrada correctamente!</h5>
                </div>

                <div class="modal-footer border-0 flex-column gap-3">
                    <div class="total-box-square w-100">
                        <span>Total</span>
                        <strong id="resultado-total">$0</strong>
                    </div>
                    <div class="d-flex gap-2 w-100">
                        <button class="main-btn light-btn btn-hover flex-fill" onclick="irAFactura()">
                            <i class="lni lni-eye me-2"></i> Ver factura
                        </button>
                        <button class="main-btn primary-btn btn-hover flex-fill" onclick="nuevaVenta()">
                            <i class="lni lni-plus me-2"></i> Nueva venta
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estado: Error -->
            <div id="estado-error" class="estado-modal d-none">
                <div class="modal-body py-5 text-center">
                    <div style="font-size: 48px; color: #d9534f; margin-bottom: 20px;">
                        <i class="lni lni-cross-circle"></i>
                    </div>
                    <h6 class="text-medium mb-2">Algo salió mal</h6>
                    <p class="text-sm text-gray" id="resultado-error"></p>
                </div>

                <div class="modal-footer border-0 pt-3">
                    <div class="d-flex gap-3 w-100">
                        <button class="main-btn light-btn btn-hover flex-fill" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button class="main-btn primary-btn btn-hover flex-fill" onclick="modalEstado('formulario')">
                            <i class="lni lni-reload me-2"></i> Intentar de nuevo
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
// ========== VALIDACIÓN DE ELEMENTOS DOM AL INICIAR ==========
function validarElementosDOM() {
    const elementos = {
        'buscar-producto': 'input de búsqueda',
        'tabla-productos': 'tabla de productos',
        'carrito-contenido': 'contenido del carrito',
        'total': 'elemento total',
        'btn-finalizar': 'botón finalizar',
        'mensaje': 'contenedor de mensajes',
        'modalPago': 'modal de pago',
        'cliente': 'input cliente',
        'cliente_nit': 'input NIT',
        'forma_pago': 'input forma de pago'
    };

    for (const [id, descripcion] of Object.entries(elementos)) {
        if (!document.getElementById(id)) {
            console.error(`❌ Error crítico: Elemento "${descripcion}" (id="${id}") no encontrado en el DOM`);
            return false;
        }
    }
    return true;
}

// Validar CSRF token
function validarCSRFToken() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken || !csrfToken.content) {
        console.error('❌ Error crítico: Meta tag csrf-token no encontrado o sin contenido');
        return null;
    }
    return csrfToken.content;
}

// ========== INICIALIZACIÓN CON VALIDACIONES ==========
let carrito = [];
let productoSeleccionado = null;
let todosProductos = [];
let busquedaTimeout = null;
let ventaEnProceso = false; // Flag para prevenir race condition
let csrfTokenGlobal = null; // Almacenar token de forma segura

// Obtener elementos DOM de forma segura
const inputBuscar = document.getElementById('buscar-producto');
const tablaProductos = document.getElementById('tabla-productos');
const carritoDiv = document.getElementById('carrito-contenido');
const totalSpan = document.getElementById('total');
const btnFinalizar = document.getElementById('btn-finalizar');
const mensajeDiv = document.getElementById('mensaje');

// Validar caracteres en tiempo real
function validarCaracteres(input, limite, contadorId) {
    const contador = document.getElementById(contadorId);
    const actual = input.value.length;
    
    if (contador) {
        // Si el input está vacío, nunca mostrar el mensaje
        if (actual === 0) {
            contador.style.display = 'none';
            input.classList.remove('border-danger');
            return;
        }
        
        // Si llegó al 90% del límite, mostrar advertencia
        if (actual >= limite * 0.9) {
            if (actual >= limite) {
                contador.textContent = '⚠️ Límite de caracteres alcanzado';
                contador.style.color = '#dc2626'; // Rojo
            } else {
                contador.textContent = `⚠️ Casi al límite (${actual}/${limite})`;
                contador.style.color = '#f59e0b'; // Naranja
            }
            contador.style.display = 'block'; // Mostrar
            input.classList.add('border-danger');
        } else {
            // Ocultar si está dentro del límite
            contador.style.display = 'none';
            input.classList.remove('border-danger');
        }
    }
}

// Limpiar mensajes de validación de caracteres
function limpiarMensajesCaracteres() {
    const contador_cliente = document.getElementById('contador-cliente');
    const contador_nit = document.getElementById('contador-nit');
    
    if (contador_cliente) contador_cliente.style.display = 'none';
    if (contador_nit) contador_nit.style.display = 'none';
    
    const cliente = document.getElementById('cliente');
    const cliente_nit = document.getElementById('cliente_nit');
    
    if (cliente) cliente.classList.remove('border-danger');
    if (cliente_nit) cliente_nit.classList.remove('border-danger');
}

// Escapar HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Formato colombiano
function formatoPrecio(precio) {
    return '$' + Math.round(precio).toLocaleString('es-CO');
}

// Cargar todos los productos al iniciar
async function cargarProductos() {
    try {
        const res = await fetch('/api/productos');
        todosProductos = await res.json();
        actualizarTablaProductos();
    } catch (error) {
        console.error('Error cargando productos:', error);
        tablaProductos.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #d9534f; padding: 20px;">Error al cargar productos</td></tr>';
    }
}

// Actualizar tabla de productos
function actualizarTablaProductos(filtrados = null) {
    let productos = filtrados || todosProductos;
    
    // Si no hay búsqueda, mostrar solo los primeros 4 con más stock
    if (!filtrados && todosProductos.length > 0) {
        productos = todosProductos.slice(0, 4);
    }
    
    if (productos.length === 0) {
        tablaProductos.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px; color: #999;">No hay productos</td></tr>';
        return;
    }

    const html = productos.map(p => {
        let statusClass = 'success-btn';
        let statusText = p.stock;
        
        if (p.stock < 5) {
            statusClass = 'danger-btn-light';
        } else if (p.stock < 10) {
            statusClass = 'primary-btn-light';
        }
        
        return `
            <tr>
                <td>
                    <div class="product">
                        <p class="text-sm" style="cursor: pointer;" onclick="agregarAlCarrito({id: ${p.id}, nombre: '${escapeHtml(p.nombre)}', precio: ${p.precio}, stock: ${p.stock}, iva: ${p.iva || 0}})">
                            ${p.nombre}
                        </p>
                    </div>
                </td>
                <td><p class="text-sm">${formatoPrecio(p.precio)}</p></td>
                <td><p class="text-sm">${p.iva || 0}%</p></td>
                <td><span class="status-btn ${statusClass}">${statusText}</span></td>
            </tr>
        `;
    }).join('');

    tablaProductos.innerHTML = html;
}

// Búsqueda de productos
inputBuscar.addEventListener('input', function() {
    clearTimeout(busquedaTimeout);
    const query = this.value.trim().toLowerCase();

    if (query.length === 0) {
        actualizarTablaProductos();
        return;
    }

    busquedaTimeout = setTimeout(() => {
        const filtrados = todosProductos.filter(p => 
            p.nombre.toLowerCase().includes(query)
        );
        actualizarTablaProductos(filtrados);
    }, 300);
});

// Agregar al carrito
function agregarAlCarrito(producto) {
    if (producto.stock === 0) {
        mostrarAlertaCarrito(`${producto.nombre} no tiene stock disponible`);
        return;
    }

    const existe = carrito.find(item => item.id === producto.id);
    
    if (existe) {
        if (existe.cantidad < producto.stock) {
            existe.cantidad++;
        } else {
            mostrarAlertaCarrito(`Stock máximo de ${producto.nombre} alcanzado`);
            return;
        }
    } else {
        const precioBase = parseFloat(producto.precio);
        const ivaRate = producto.iva || 0;
        const ivaValor = ivaRate > 0 ? precioBase * ivaRate / 100 : 0;
        const subtotalConIva = precioBase + ivaValor;
        
        carrito.push({
            id: producto.id,
            nombre: producto.nombre,
            precio: precioBase,
            cantidad: 1,
            stock: producto.stock,
            iva: ivaRate,
            subtotalConIva: subtotalConIva
        });
    }

    actualizarCarrito();
}

// Actualizar carrito
function actualizarCarrito() {
    if (carrito.length === 0) {
        document.getElementById('carrito-header').innerHTML = '<tr><td class="text-sm">Sin productos</td></tr>';
        carritoDiv.innerHTML = '<tr><td class="text-sm" colspan="3" style="text-align: center; padding: 20px; color: #999;">El carrito está vacío</td></tr>';
        btnFinalizar.disabled = true;
        totalSpan.textContent = '$0';
        return;
    }

    const headerHtml = `
        <tr>
            <td class="text-sm">Producto</td>
            <td class="text-sm text-center" style="width: 100px;">Cantidad</td>
            <td class="text-sm text-end" style="width: 150px;">Subtotal</td>
        </tr>
    `;

    const bodyHtml = carrito.map((item, index) => {
        const totalConIva = item.cantidad * item.subtotalConIva;
        return `
            <tr data-item-id="${item.id}">
                <td class="text-sm">${item.nombre}</td>
                <td class="text-sm text-center">
                    <div class="d-inline-flex align-items-center gap-1">
                        <button class="btn btn-light btn-sm px-2" onclick="cambiarCantidad(${index}, ${item.cantidad - 1})" ${item.cantidad === 1 ? 'disabled' : ''}>−</button>
                        <span class="px-2 cantidad-display">${item.cantidad}</span>
                        <button class="btn btn-light btn-sm px-2" onclick="cambiarCantidad(${index}, ${item.cantidad + 1})">+</button>
                    </div>
                </td>
                <td class="text-sm text-end d-flex justify-content-end align-items-center gap-2">
                    <span>${formatoPrecio(totalConIva)}</span>
                    <button class="btn btn-light btn-sm" onclick="confirmarEliminar(${index})" title="Eliminar">
                        <i class="lni lni-trash-can"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');

    document.getElementById('carrito-header').innerHTML = headerHtml;
    carritoDiv.innerHTML = bodyHtml;
    btnFinalizar.disabled = false;
    actualizarTotal();
}

// Cambiar cantidad
function cambiarCantidad(index, nuevaCantidad) {
    nuevaCantidad = parseInt(nuevaCantidad);
    
    // VALIDACIÓN 1: Verificar que el índice exista en el carrito
    if (index < 0 || index >= carrito.length) {
        console.warn(`⚠️ Intento de acceso a índice inválido: ${index}. Carrito actual: ${carrito.length} items`);
        mostrarAlertaCarrito('Error al modificar cantidad. Por favor, intenta de nuevo.');
        return;
    }
    
    // VALIDACIÓN 2: Verificar que la cantidad sea válida
    if (isNaN(nuevaCantidad) || nuevaCantidad < 1) {
        return; // Bloquear si intenta ir por debajo de 1
    }

    // VALIDACIÓN 3: Verificar stock disponible
    if (nuevaCantidad > carrito[index].stock) {
        mostrarAlertaCarrito(`${carrito[index].nombre}  No tiene más stock disponible`);
        return;
    }

    carrito[index].cantidad = nuevaCantidad;
    actualizarCarrito();
}

// Confirmar eliminar
function confirmarEliminar(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
}

// Actualizar total
function actualizarTotal() {
    const total = carrito.reduce((sum, item) => {
        return sum + (item.cantidad * item.subtotalConIva);
    }, 0);
    totalSpan.textContent = formatoPrecio(total);
    document.getElementById('modal-total').textContent = formatoPrecio(total);
}

// Seleccionar método de pago
function seleccionarPago(metodo) {
    document.getElementById('forma_pago').value = metodo;
    document.getElementById('pago-efectivo').style.borderColor = metodo === 'efectivo' ? '#4CAF50' : 'transparent';
    document.getElementById('pago-tarjeta').style.borderColor = metodo === 'tarjeta' ? '#4CAF50' : 'transparent';
    document.getElementById('pago-transferencia').style.borderColor = metodo === 'transferencia' ? '#4CAF50' : 'transparent';
}

// Confirmar venta
function confirmarVenta() {
    if (carrito.length === 0) {
        mostrarMensaje('El carrito está vacío', 'error');
        return;
    }

    const total = carrito.reduce((sum, item) => {
        return sum + (item.cantidad * item.subtotalConIva);
    }, 0);

    document.getElementById('modal-total').textContent = formatoPrecio(total);
    
    const modal = new bootstrap.Modal(document.getElementById('modalPago'));
    modal.show();
}

// Finalizar venta
async function finalizarVenta() {
    // ========== VALIDACIÓN 1: PREVENIR RACE CONDITION ==========
    if (ventaEnProceso) {
        console.warn('⚠️ Una venta ya está siendo procesada. Ignora este clic.');
        mostrarAlertaCarrito('La venta ya está siendo procesada. Por favor, espera...');
        return;
    }

    // ========== VALIDACIÓN 2: VERIFICAR CSRF TOKEN ==========
    if (!csrfTokenGlobal) {
        console.error('❌ Error: CSRF token no disponible');
        mostrarMensaje('Error de seguridad. Por favor, recarga la página.', 'error');
        return;
    }

    const cliente = document.getElementById('cliente').value.trim() || null;
    const cliente_nit = document.getElementById('cliente_nit').value.trim() || null;
    const forma_pago = document.getElementById('forma_pago').value;

    // ========== VALIDACIÓN 4: VALIDAR LONGITUD DE CARACTERES ==========
    if (cliente && cliente.length > 25) {
        mostrarMensaje('El nombre del cliente no puede exceder 25 caracteres', 'error');
        return;
    }
    
    if (cliente_nit && cliente_nit.length > 25) {
        mostrarMensaje('El NIT/Documento no puede exceder 25 caracteres', 'error');
        return;
    }

    // ========== VALIDACIÓN 3: VALIDAR CARRITO NO ESTÉ VACÍO ==========
    if (!carrito || carrito.length === 0) {
        mostrarMensaje('El carrito está vacío', 'error');
        return;
    }

    const data = {
        cliente: cliente,
        cliente_nit: cliente_nit,
        forma_pago: forma_pago,
        productos: carrito.map(item => ({
            id: item.id,
            cantidad: item.cantidad,
            precio: item.precio,
            iva: item.iva || 0
        }))
    };

    // ========== MARCAR VENTA COMO EN PROCESO ==========
    ventaEnProceso = true;
    const btnConfirmar = document.getElementById('btn-confirmar-pago');
    if (btnConfirmar) {
        btnConfirmar.disabled = true;
    }

    // Timeout para mostrar loading (solo si tarda más de 300ms)
    let loadingTimeout = setTimeout(() => {
        modalEstado('loading');
    }, 300);

    try {
        const res = await fetch('/ventas', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfTokenGlobal,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        // Cancelar timeout si aún no se mostró el loading
        clearTimeout(loadingTimeout);

        // ========== MANEJO DE RESPUESTA CON VALIDACIÓN ==========
        let result;
        try {
            result = await res.json();
        } catch (e) {
            console.error('Error al parsear respuesta JSON:', e);
            throw new Error('Respuesta inválida del servidor');
        }

        if (!res.ok) throw result;

        // Mostrar pantalla de éxito
        const resultadoTotal = document.getElementById('resultado-total');
        if (resultadoTotal) {
            resultadoTotal.textContent = formatoPrecio(result.total);
        }
        
        // Guardar ID de venta para ver factura
        window.ultimaVentaId = result.venta_id;
        
        modalEstado('exito');
        
    } catch (error) {
        // Cancelar timeout
        clearTimeout(loadingTimeout);
        
        console.error('Error en venta:', error);
        const resultadoError = document.getElementById('resultado-error');
        if (resultadoError) {
            resultadoError.textContent = 'No se pudo procesar la venta. Por favor, intenta de nuevo.';
        }
        modalEstado('error');
        
    } finally {
        // ========== LIMPIAR FLAG Y BOTONES ==========
        ventaEnProceso = false;
        if (btnConfirmar) {
            btnConfirmar.disabled = false;
        }
        
        // Limpiar mensajes de caracteres
        limpiarMensajesCaracteres();
    }
}

// Cambiar estado del modal
function modalEstado(estado) {
    const estados = ['formulario', 'loading', 'exito', 'error'];
    
    // Ocultar todos
    estados.forEach(e => {
        document.getElementById(`estado-${e}`).classList.add('d-none');
    });
    
    // Mostrar el seleccionado
    document.getElementById(`estado-${estado}`).classList.remove('d-none');
}

// Ver factura
function irAFactura() {
    if (window.ultimaVentaId) {
        window.open(`/ventas/${window.ultimaVentaId}/factura`, '_blank');
    }
}

// Nueva venta
function nuevaVenta() {
    // Cerrar modal
    bootstrap.Modal.getInstance(document.getElementById('modalPago')).hide();
    
    // Volver al estado de formulario y limpiar
    modalEstado('formulario');
    limpiarVenta();
    
    // Recargar productos para actualizar stock
    cargarProductos();
}

// Limpiar venta
function limpiarVenta() {
    carrito = [];
    document.getElementById('cliente').value = '';
    document.getElementById('cliente_nit').value = '';
    document.getElementById('forma_pago').value = 'efectivo';
    inputBuscar.value = '';
    actualizarCarrito();
    actualizarTablaProductos();
    seleccionarPago('efectivo');
}

// Mostrar mensaje
function mostrarMensaje(texto, tipo) {
    const mensajeDiv = document.getElementById('mensaje');
    const textoMensaje = document.getElementById('texto-mensaje');
    
    // Mapear tipo de mensaje a clase de Bootstrap
    const claseAlerta = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'info': 'alert-info',
        'warning': 'alert-warning'
    };
    
    textoMensaje.textContent = texto;
    mensajeDiv.className = `alert alert-dismissible fade show mb-3 ${claseAlerta[tipo] || 'alert-info'}`;
    mensajeDiv.style.margin = '15px 0';
    
    // Auto-cerrar después de 4 segundos
    setTimeout(() => {
        mensajeDiv.classList.remove('show');
        mensajeDiv.classList.add('d-none');
    }, 4000);
}

// Mostrar alerta en el carrito
function mostrarAlertaCarrito(texto) {
    const alertaDiv = document.getElementById('alerta-carrito');
    const textoAlerta = document.getElementById('texto-alerta-carrito');
    
    textoAlerta.textContent = texto;
    alertaDiv.classList.remove('d-none');
    alertaDiv.classList.add('show');
    
    // Auto-cerrar después de 2.5 segundos
    setTimeout(() => {
        alertaDiv.classList.add('d-none');
        alertaDiv.classList.remove('show');
    }, 2500);
}

// Limpiar carrito cuando se cierra el modal
document.getElementById('modalPago').addEventListener('hidden.bs.modal', function() {
    // ⚠️ IMPORTANTE: NO limpiamos el carrito aquí
    // Solo volvemos al estado de formulario
    // El carrito solo se limpia si la venta fue exitosa (en nuevaVenta())
    modalEstado('formulario');
    // Recargar productos para actualizar stock
    cargarProductos();
});

// ========== INICIALIZACIÓN CON VALIDACIONES DE SEGURIDAD ==========
document.addEventListener('DOMContentLoaded', function() {
    // PASO 1: Validar todos los elementos del DOM
    if (!validarElementosDOM()) {
        console.error('❌ Inicialización fallida: Faltan elementos esenciales en el HTML');
        mostrarMensaje('Error en la inicialización de la página. Por favor, recarga.', 'error');
        return;
    }

    // PASO 2: Validar y guardar CSRF token
    csrfTokenGlobal = validarCSRFToken();
    if (!csrfTokenGlobal) {
        console.error('❌ Inicialización fallida: No se pudo obtener el CSRF token');
        mostrarMensaje('Error de seguridad. Por favor, recarga la página.', 'error');
        return;
    }

    // PASO 3: Inicializar funcionalidad normal
    cargarProductos();
    seleccionarPago('efectivo');

    console.log('✅ Inicialización completada exitosamente');
});
</script>

@endsection