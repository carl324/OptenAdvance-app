@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('content')

<style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 8px; padding: 30px; }
        h1 { margin-bottom: 30px; }
        
        .search-box { position: relative; margin-bottom: 20px; }
        #buscar-producto { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 16px; }
        #buscar-producto:focus { outline: none; border-color: #4CAF50; }
        
        .search-results { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 2px solid #4CAF50; border-top: none; max-height: 350px; overflow-y: auto; z-index: 1000; display: none; }
        .search-results.active { display: block; }
        .search-item { padding: 12px; cursor: pointer; border-bottom: 1px solid #f0f0f0; }
        .search-item:hover, .search-item.selected { background: #f0f9ff; }
        .search-item-name { font-weight: 600; }
        .search-item-details { font-size: 13px; color: #666; margin-top: 4px; }
        .search-item-stock { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-left: 10px; }
        .stock-ok { background: #e8f5e9; color: #2e7d32; }
        .stock-bajo { background: #fff3e0; color: #e65100; }
        .stock-cero { background: #ffebee; color: #c62828; }
        .no-results { padding: 20px; text-align: center; color: #999; }
        
        .cliente-section { margin-bottom: 20px; }
        .cliente-section label { display: block; margin-bottom: 8px; font-weight: 500; }
        #cliente, #cliente_nit, #forma_pago { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; margin-bottom: 10px; }
        
        .carrito-section { margin-bottom: 20px; }
        .carrito-vacio { text-align: center; padding: 40px; color: #999; }
        .carrito-tabla { width: 100%; border-collapse: collapse; }
        .carrito-tabla th { background: #f8f9fa; padding: 10px; text-align: left; border-bottom: 2px solid #ddd; }
        .carrito-tabla td { padding: 12px 10px; border-bottom: 1px solid #f0f0f0; }
        .cantidad-input { width: 70px; padding: 6px; border: 1px solid #ddd; border-radius: 4px; text-align: center; }
        .btn-eliminar { background: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
        
        .total-section { background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 15px; }
        .total-row { display: flex; justify-content: space-between; font-size: 22px; font-weight: bold; }
        .total-amount { color: #4CAF50; }
        
        .actions { display: flex; gap: 10px; }
        .btn { flex: 1; padding: 12px; font-size: 16px; font-weight: 600; border: none; border-radius: 6px; cursor: pointer; }
        .btn-primary { background: #4CAF50; color: white; }
        .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-secondary { background: #757575; color: white; }
        
        .mensaje { padding: 12px 15px; border-radius: 6px; margin-bottom: 15px; display: none; }
        .mensaje.success { background: #e8f5e9; color: #2e7d32; border-left: 4px solid #4CAF50; }
        .mensaje.error { background: #ffebee; color: #c62828; border-left: 4px solid #f44336; }
        .mensaje.show { display: block; }
        
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal { background: white; border-radius: 8px; padding: 25px; max-width: 500px; width: 90%; }
        .modal-title { font-size: 18px; font-weight: bold; margin-bottom: 15px; color: #333; }
        .modal-content { margin-bottom: 20px; color: #555; }
        .modal-actions { display: flex; gap: 10px; }
        .modal-btn { flex: 1; padding: 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 15px; }
        .modal-btn-confirm { background: #4CAF50; color: white; }
        .modal-btn-cancel { background: #e0e0e0; color: #333; }
        
        .venta-resumen { background: #f8f9fa; padding: 15px; border-radius: 6px; margin: 15px 0; }
        .venta-items { max-height: 200px; overflow-y: auto; margin-bottom: 15px; }
        .venta-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e0e0e0; }
        .venta-item:last-child { border-bottom: none; }
        .venta-item-nombre { font-weight: 500; }
        .venta-item-detalle { color: #666; font-size: 14px; }
        .venta-total { display: flex; justify-content: space-between; padding-top: 15px; border-top: 2px solid #ddd; font-size: 20px; font-weight: bold; }
        .venta-total-label { color: #333; }
        .venta-total-monto { color: #4CAF50; }
        
        .loading { display: inline-block; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.6s linear infinite; margin-right: 8px; }
        @keyframes spin { to { transform: rotate(360deg); } }
        
        /* Ticket de impresión */
        @media print {
            body * { visibility: hidden; }
            #ticket-print, #ticket-print * { visibility: visible; }
            #ticket-print { position: absolute; left: 0; top: 0; width: 80mm; }
        }
        
        #ticket-print { display: none; }
        .ticket { font-family: 'Courier New', monospace; width: 80mm; padding: 10mm; }
        .ticket-header { text-align: center; margin-bottom: 15px; border-bottom: 2px dashed #000; padding-bottom: 10px; }
        .ticket-title { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .ticket-info { font-size: 12px; margin: 3px 0; }
        .ticket-items { margin: 15px 0; }
        .ticket-item { display: flex; justify-content: space-between; margin: 8px 0; font-size: 13px; }
        .ticket-item-desc { flex: 1; }
        .ticket-item-qty { margin: 0 10px; }
        .ticket-separator { border-top: 1px dashed #000; margin: 10px 0; }
        .ticket-total { display: flex; justify-content: space-between; font-size: 16px; font-weight: bold; margin-top: 15px; padding-top: 10px; border-top: 2px solid #000; }
        .ticket-footer { text-align: center; margin-top: 20px; padding-top: 15px; border-top: 2px dashed #000; font-size: 12px; }
    </style>

<div class="container">
    <h1>🛒 Nueva Venta</h1>

    <div id="mensaje" class="mensaje"></div>

    <div class="search-box">
        <input type="text" id="buscar-producto" placeholder="Buscar producto..." autocomplete="off" autofocus>
        <div id="resultados" class="search-results"></div>
    </div>

    <div class="cliente-section">
        <label>Cliente (opcional):</label>
        <input type="text" id="cliente" placeholder="Nombre del cliente">
        <input type="text" id="cliente_nit" placeholder="NIT (opcional)">
        <label for="forma_pago">Forma de pago</label>
        <select id="forma_pago">
            <option value="efectivo" selected>efectivo</option>
            <option value="transferencia">transferencia</option>
            <option value="tarjeta">tarjeta</option>
        </select>
    </div>

    <div class="carrito-section">
        <h2>Carrito</h2>
        <div id="carrito-contenido">
            <div class="carrito-vacio">El carrito está vacío</div>
        </div>
    </div>

    <div class="total-section">
        <div class="total-row">
            <span>TOTAL:</span>
            <span class="total-amount" id="total">$0</span>
        </div>
    </div>

    <div class="actions">
        <button type="button" class="btn btn-secondary" onclick="limpiarVenta()">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btn-finalizar" onclick="confirmarVenta()" disabled>Finalizar Venta</button>
    </div>
</div>

<!-- Ticket para imprimir (oculto) -->
<div id="ticket-print">
    <div class="ticket">
        <div class="ticket-header">
            <div class="ticket-title">TICKET DE VENTA</div>
            <div class="ticket-info" id="ticket-fecha"></div>
            <div class="ticket-info" id="ticket-numero"></div>
        </div>
        
        <div class="ticket-info" id="ticket-cliente"></div>
        
        <div class="ticket-items" id="ticket-items"></div>
        
        <div class="ticket-total">
            <span>TOTAL:</span>
            <span id="ticket-total"></span>
        </div>
        
        <div class="ticket-footer">
            ¡Gracias por su compra!
        </div>
    </div>
</div>

<!-- Modal profesional -->
<div class="modal-overlay" id="modal">
    <div class="modal">
        <div class="modal-title" id="modal-title"></div>
        <div class="modal-content" id="modal-content"></div>
        <div class="modal-actions" id="modal-actions"></div>
    </div>
</div>

<script>
let carrito = [];
let productoSeleccionado = null;
let indexSeleccionado = -1;
let busquedaTimeout = null;

const inputBuscar = document.getElementById('buscar-producto');
const resultadosDiv = document.getElementById('resultados');
const carritoDiv = document.getElementById('carrito-contenido');
const totalSpan = document.getElementById('total');
const btnFinalizar = document.getElementById('btn-finalizar');
const mensajeDiv = document.getElementById('mensaje');

// Bug #25: Escapar HTML para evitar que comillas rompan atributos
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

// Formato colombiano sin decimales
function formatoPrecio(precio) {
    return '$' + Math.round(precio).toLocaleString('es-CO');
}

function mostrarModal(titulo, contenido, botones) {
    document.getElementById('modal-title').textContent = titulo;
    document.getElementById('modal-content').innerHTML = contenido;
    document.getElementById('modal-actions').innerHTML = botones;
    document.getElementById('modal').classList.add('active');
}

function cerrarModal() {
    document.getElementById('modal').classList.remove('active');
}

inputBuscar.addEventListener('input', function() {
    clearTimeout(busquedaTimeout);
    const query = this.value.trim();

    if (query.length < 2) {
        resultadosDiv.classList.remove('active');
        return;
    }

    busquedaTimeout = setTimeout(() => buscarProductos(query), 300);
});

inputBuscar.addEventListener('keydown', function(e) {
    const items = resultadosDiv.querySelectorAll('.search-item');
    
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        indexSeleccionado = Math.min(indexSeleccionado + 1, items.length - 1);
        actualizarSeleccion(items);
    } 
    else if (e.key === 'ArrowUp') {
        e.preventDefault();
        indexSeleccionado = Math.max(indexSeleccionado - 1, -1);
        actualizarSeleccion(items);
    } 
    else if (e.key === 'Enter') {
        e.preventDefault();
        if (productoSeleccionado) {
            agregarAlCarrito(productoSeleccionado);
        }
    }
    else if (e.key === 'Escape') {
        resultadosDiv.classList.remove('active');
        indexSeleccionado = -1;
    }
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('.search-box')) {
        resultadosDiv.classList.remove('active');
    }
});

async function buscarProductos(query) {
    try {
        const res = await fetch(`/api/productos/buscar?q=${encodeURIComponent(query)}`);
        const productos = await res.json();
        mostrarResultados(productos);
    } catch (error) {
        console.error('Error:', error);
    }
}

function mostrarResultados(productos) {
    indexSeleccionado = -1;
    productoSeleccionado = null;

    if (productos.length === 0) {
        resultadosDiv.innerHTML = '<div class="no-results">No se encontraron productos</div>';
        resultadosDiv.classList.add('active');
        return;
    }
    @if($empresa && $empresa->cobra_iva)
    const html = productos.map((p, index) => {
        let stockClass = 'stock-ok';
        let stockText = `Stock: ${p.stock}`;
        
        if (p.stock === 0) {
            stockClass = 'stock-cero';
            stockText = 'Sin stock';
        } else if (p.stock <= 10) {
            stockClass = 'stock-bajo';
        }
        
        return `
            <div class="search-item" data-index="${index}" data-producto="${escapeHtml(JSON.stringify(p))}">
                <div class="search-item-name">${p.nombre}</div>
                <div class="search-item-details">
                    Precio: ${formatoPrecio(p.precio)} ${p.iva ? '(+IVA)' : '(sin IVA)'}
                    <span class="search-item-stock ${stockClass}">${stockText}</span>
                </div>
            </div>
        `;
    }).join('');
    @else
    const html = productos.map((p, index) => {
        let stockClass = 'stock-ok';
        let stockText = `Stock: ${p.stock}`;
        
        if (p.stock === 0) {
            stockClass = 'stock-cero';
            stockText = 'Sin stock';
        } else if (p.stock <= 10) {
            stockClass = 'stock-bajo';
        }
        
        return `
            <div class="search-item" data-index="${index}" data-producto="${escapeHtml(JSON.stringify(p))}">
                <div class="search-item-name">${p.nombre}</div>
                <div class="search-item-details">
                    Precio: ${formatoPrecio(p.precio)}
                    <span class="search-item-stock ${stockClass}">${stockText}</span>
                </div>
            </div>
        `;
    }).join('');
    @endif

    resultadosDiv.innerHTML = html;
    resultadosDiv.classList.add('active');

    resultadosDiv.querySelectorAll('.search-item').forEach(item => {
        item.addEventListener('click', function() {
            const producto = JSON.parse(this.dataset.producto);
            agregarAlCarrito(producto);
        });
    });
}

function actualizarSeleccion(items) {
    items.forEach((item, index) => {
        item.classList.toggle('selected', index === indexSeleccionado);
    });

    if (indexSeleccionado >= 0 && items[indexSeleccionado]) {
        items[indexSeleccionado].scrollIntoView({ block: 'nearest' });
        productoSeleccionado = JSON.parse(items[indexSeleccionado].dataset.producto);
    } else {
        productoSeleccionado = null;
    }
}

function agregarAlCarrito(producto) {
    if (producto.stock === 0) {
        mostrarMensaje(`${producto.nombre} no tiene stock disponible`, 'error');
        return;
    }

    const existe = carrito.find(item => item.id === producto.id);
    
    if (existe) {
        if (existe.cantidad < producto.stock) {
            existe.cantidad++;
        } else {
            mostrarMensaje(`No hay más stock de ${producto.nombre}`, 'error');
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

    inputBuscar.value = '';
    resultadosDiv.classList.remove('active');
    inputBuscar.focus();

    actualizarCarrito();
}

function actualizarCarrito() {
    if (carrito.length === 0) {
        carritoDiv.innerHTML = '<div class="carrito-vacio">El carrito está vacío</div>';
        btnFinalizar.disabled = true;
        totalSpan.textContent = '$0';
        return;
    }

    @if($empresa && $empresa->cobra_iva)
    const html = `
        <table class="carrito-tabla">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th style="width: 100px;">Cantidad</th>
                    <th style="width: 100px; text-align: right;">Precio</th>
                    <th style="width: 100px; text-align: right;">Subtotal</th>
                    <th style="width: 70px;"></th>
                </tr>
            </thead>
            <tbody>
                ${carrito.map((item, index) => {
                    const totalConIva = item.cantidad * item.subtotalConIva;
                    return `
                    <tr>
                        <td>${item.nombre}${item.iva ? `<br><small>IVA ${item.iva}%</small>` : ''}<br><small style="color: #999;">Stock: ${item.stock}</small></td>
                        <td>
                            <input 
                                type="number" 
                                class="cantidad-input" 
                                value="${item.cantidad}" 
                                min="1" 
                                max="${item.stock}"
                                onchange="cambiarCantidad(${index}, this.value)"
                            >
                        </td>
                        <td style="text-align: right;">${formatoPrecio(item.precio)}</td>
                        <td style="text-align: right;"><strong>${formatoPrecio(totalConIva)}</strong></td>
                        <td>
                            <button class="btn-eliminar" onclick="confirmarEliminar(${index})">✕</button>
                        </td>
                    </tr>
                `}).join('')}
            </tbody>
        </table>
    `;
    @else
    const html = `
        <table class="carrito-tabla">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th style="width: 100px;">Cantidad</th>
                    <th style="width: 100px; text-align: right;">Precio</th>
                    <th style="width: 100px; text-align: right;">Subtotal</th>
                    <th style="width: 70px;"></th>
                </tr>
            </thead>
            <tbody>
                ${carrito.map((item, index) => {
                    const totalConIva = item.cantidad * item.subtotalConIva;
                    return `
                    <tr>
                        <td>${item.nombre}<br><small style="color: #999;">Stock: ${item.stock}</small></td>
                        <td>
                            <input 
                                type="number" 
                                class="cantidad-input" 
                                value="${item.cantidad}" 
                                min="1" 
                                max="${item.stock}"
                                onchange="cambiarCantidad(${index}, this.value)"
                            >
                        </td>
                        <td style="text-align: right;">${formatoPrecio(item.precio)}</td>
                        <td style="text-align: right;"><strong>${formatoPrecio(totalConIva)}</strong></td>
                        <td>
                            <button class="btn-eliminar" onclick="confirmarEliminar(${index})">✕</button>
                        </td>
                    </tr>
                `}).join('')}
            </tbody>
        </table>
    `;
    @endif

    carritoDiv.innerHTML = html;
    btnFinalizar.disabled = false;
    actualizarTotal();
}

function cambiarCantidad(index, nuevaCantidad) {
    nuevaCantidad = parseInt(nuevaCantidad);
    
    if (nuevaCantidad < 1) {
        mostrarMensaje('La cantidad debe ser al menos 1', 'error');
        actualizarCarrito();
        return;
    }

    if (nuevaCantidad > carrito[index].stock) {
        mostrarMensaje(`Solo hay ${carrito[index].stock} unidades disponibles`, 'error');
        actualizarCarrito();
        return;
    }

    carrito[index].cantidad = nuevaCantidad;
    actualizarTotal();
}

function confirmarEliminar(index) {
    const producto = carrito[index];
    mostrarModal(
        '¿Eliminar producto?',
        `¿Seguro que deseas eliminar <strong>${producto.nombre}</strong> del carrito?`,
        `
            <button class="modal-btn modal-btn-cancel" onclick="cerrarModal()">Cancelar</button>
            <button class="modal-btn modal-btn-confirm" onclick="eliminarDelCarrito(${index}); cerrarModal()">Eliminar</button>
        `
    );
}

function eliminarDelCarrito(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
}

function actualizarTotal() {
    // El total es la suma simple de cada item con su IVA ya calculado
    const total = carrito.reduce((sum, item) => {
        return sum + (item.cantidad * item.subtotalConIva);
    }, 0);
    totalSpan.textContent = formatoPrecio(total);
}

function confirmarVenta() {
    if (carrito.length === 0) {
        mostrarMensaje('El carrito está vacío', 'error');
        return;
    }

    // El total es la suma simple de cada item con su IVA ya calculado
    const total = carrito.reduce((sum, item) => {
        return sum + (item.cantidad * item.subtotalConIva);
    }, 0);

    const cliente = document.getElementById('cliente').value.trim();

    // ...eliminar advertencia modal independiente de precio 0...
    
    const tienePrecioCero = carrito.some(item => Number(item.precio) === 0);
    let advertenciaHTML = '';
    if (tienePrecioCero) {
        advertenciaHTML = `
            <div style="background: #fffbe6; color: #b26a00; border-left: 6px solid #ffe066; padding: 14px 18px; border-radius: 6px; margin-bottom: 18px; font-size: 16px; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 22px;">⚠</span>
                <div>
                    <strong>Advertencia:</strong> Esta venta contiene uno o más productos con precio en $0.<br>
                    Verifique los datos antes de continuar.
                </div>
            </div>
        `;
    }
    const detallesHTML = `
        <div class="venta-resumen">
            ${cliente ? `<p style="margin-bottom: 10px;"><strong>Cliente:</strong> ${cliente}</p>` : ''}
            <div class="venta-items">
                ${carrito.map(item => {
                    const totalConIva = item.cantidad * item.subtotalConIva;
                    // Marcar productos con precio 0 visualmente (se detalla en el siguiente paso)
                    // Marcar productos con precio 0
                    const esPrecioCero = Number(item.precio) === 0;
                    return `
                    <div class="venta-item">
                        <div>
                            <div class="venta-item-nombre">
                                ${item.nombre}
                                ${esPrecioCero ? '<span title="Precio 0" style="color: #b26a00; font-size: 18px; margin-left: 6px; vertical-align: middle;">⚠ <span style=\'font-size:12px; color:#b26a00; font-weight:600;\'>Precio 0</span></span>' : ''}
                            </div>
                            <div class="venta-item-detalle">${item.cantidad} x ${formatoPrecio(item.precio)}${item.iva ? ` (IVA ${item.iva}%)` : ''}</div>
                        </div>
                        <div><strong>${formatoPrecio(totalConIva)}</strong></div>
                    </div>
                `}).join('')}
            </div>
            <div class="venta-total">
                <span class="venta-total-label">TOTAL:</span>
                <span class="venta-total-monto">${formatoPrecio(total)}</span>
            </div>
        </div>
        ${advertenciaHTML}
        <p style="text-align: center; color: #666;">¿Confirmar esta venta?</p>
    `;
    mostrarModal(
        '📋 Confirmar Venta',
        detallesHTML,
        `
            <button class="modal-btn modal-btn-cancel" onclick="cerrarModal()">Cancelar</button>
            <button class="modal-btn modal-btn-confirm" onclick="finalizarVenta()">Confirmar Venta</button>
        `
    );
}

async function finalizarVenta() {
    cerrarModal();
    
    btnFinalizar.disabled = true;
    btnFinalizar.innerHTML = '<span class="loading"></span>Procesando...';

    const data = {
        cliente: document.getElementById('cliente').value.trim() || null,
        cliente_nit: document.getElementById('cliente_nit').value.trim() || null,
        forma_pago: document.getElementById('forma_pago').value.trim() || null,
        productos: carrito.map(item => ({
            id: item.id,
            cantidad: item.cantidad,
            precio: item.precio,
            iva: item.iva || 0
        }))
    };

    try {
        const res = await fetch('/ventas', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await res.json();

        if (!res.ok) throw result;

        mostrarMensaje(
            `✅ ${result.message} - Venta #${result.venta_id} - Total: ${formatoPrecio(result.total)}`, 
            'success'
        );

        mostrarModalImpresion(result.venta_id, result.total, data.cliente, carrito);

        limpiarVenta();
        
    } catch (error) {
        mostrarMensaje(error.message || 'Error al procesar la venta', 'error');
    } finally {
        btnFinalizar.disabled = false;
        btnFinalizar.innerHTML = 'Finalizar Venta';
    }
}

function limpiarVenta() {
    carrito = [];
    document.getElementById('cliente').value = '';
    document.getElementById('cliente_nit').value = '';
    document.getElementById('forma_pago').value = 'efectivo';
    inputBuscar.value = '';
    actualizarCarrito();
    inputBuscar.focus();
}

function mostrarMensaje(texto, tipo) {
    mensajeDiv.innerHTML = texto;
    mensajeDiv.className = `mensaje ${tipo} show`;
    
    setTimeout(() => {
        mensajeDiv.classList.remove('show');
    }, 5000);
}

function mostrarModalImpresion(ventaId, total, cliente, items) {
    const ahora = new Date();
    const fecha = ahora.toLocaleDateString('es-CO', { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    document.getElementById('ticket-fecha').textContent = fecha;
    document.getElementById('ticket-numero').textContent = `Venta #${ventaId}`;
    document.getElementById('ticket-cliente').textContent = cliente ? `Cliente: ${cliente}` : '';
    document.getElementById('ticket-total').textContent = formatoPrecio(total);
    
    const itemsHTML = items.map(item => {
        const totalConIva = item.cantidad * item.subtotalConIva;
        return `
            <div class="ticket-item">
                <span class="ticket-item-desc">${item.nombre}</span>
                <span class="ticket-item-qty">${item.cantidad}x</span>
                <span>${formatoPrecio(totalConIva)}</span>
            </div>
        `;
    }).join('');
    
    document.getElementById('ticket-items').innerHTML = itemsHTML;
    
    mostrarModal(
        '✅ Venta Registrada',
        `
            <div style="text-align: center; margin: 20px 0;">
                <p style="font-size: 18px; font-weight: bold; color: #4CAF50; margin-bottom: 10px;">
                    Venta #${ventaId}
                </p>
                <p style="font-size: 24px; font-weight: bold; margin-bottom: 20px;">
                    Total: ${formatoPrecio(total)}
                </p>
                <p style="color: #666;">Puedes ver la factura de esta venta para imprimirla o compartirla.</p>
            </div>
        `,
        `
            <button class="modal-btn modal-btn-cancel" onclick="cerrarModal()">Cerrar</button>
            <button class="modal-btn modal-btn-confirm" onclick="window.open('/ventas/${ventaId}/factura', '_blank')">Ver factura</button>
        `
    );
}

inputBuscar.focus();

</script>

@endsection