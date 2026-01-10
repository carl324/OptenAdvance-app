@extends('layouts.app')

@section('title', 'Gestión de Productos')

@section('content')

<style>
    .inventory-section {
      padding: 30px 0;
    }

    /* Formulario de Productos */
    .product-form-card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
    }

    .form-header {
      margin-bottom: 20px;
    }

    .form-header h6 {
      font-size: 18px;
      font-weight: 600;
      color: #1a202c;
      margin-bottom: 5px;
    }

    .form-header p {
      font-size: 14px;
      color: #64748b;
      margin: 0;
    }

    .product-form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-field {
      display: flex;
      flex-direction: column;
      min-width: 0;
    }

    .form-field.full-width {
      grid-column: 1 / -1;
    }

    .form-field label {
      font-size: 14px;
      font-weight: 500;
      color: #334155;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .form-field label i {
      font-size: 16px;
      color: #64748b;
    }

    .form-field input, .form-field select {
      padding: 10px 12px;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      font-size: 14px;
      font-family: inherit;
      transition: border 0.2s;
    }

    .form-field input:focus, .form-field select:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .input-with-symbol {
      display: flex;
      align-items: center;
      position: relative;
    }

    .input-with-symbol .input-symbol {
      position: absolute;
      left: 12px;
      color: #64748b;
      font-weight: 500;
      font-size: 14px;
      pointer-events: none;
    }

    .input-with-symbol input {
      padding-left: 30px;
      width: 100%;
    }

    .input-with-suffix {
      position: relative;
    }

    .input-with-suffix .input-suffix {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #64748b;
      font-weight: 500;
      font-size: 14px;
      pointer-events: none;
    }

    .input-with-suffix input {
      padding-right: 35px;
      width: 100%;
    }

    .quantity-control-wrapper {
      display: flex;
      align-items: center;
      gap: 12px;
      background: #f8fafc;
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid #e2e8f0;
    }

    .quantity-btn {
      background: #e2e8f0;
      border: none;
      border-radius: 6px;
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background 0.2s;
      color: #334155;
      font-size: 16px;
    }

    .quantity-btn:hover {
      background: #cbd5e1;
    }

    .quantity-display {
      font-size: 18px;
      font-weight: 600;
      color: #1a202c;
      min-width: 40px;
      text-align: center;
    }

    .quantity-label {
      font-size: 14px;
      color: #64748b;
      margin-left: 8px;
    }

    .submit-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-top: 20px;
    }

    .btn-reset, .btn-submit {
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.2s;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .btn-reset {
      background: #e2e8f0;
      color: #334155;
    }

    .btn-reset:hover {
      background: #cbd5e1;
    }

    .btn-submit {
      background: #3b82f6;
      color: white;
    }

    .btn-submit:hover {
      background: #2563eb;
    }

    .btn-submit:disabled {
      background: #cbd5e1;
      cursor: not-allowed;
    }

    /* Alertas */
    .alert-custom {
      padding: 14px 16px;
      border-radius: 8px;
      margin-bottom: 16px;
      font-size: 14px;
      font-weight: 500;
      animation: slideDown 0.3s ease-out;
      opacity: 0;
      transform: translateY(-10px);
      transition: opacity 0.2s, transform 0.2s;
    }

    .alert-custom.show {
      opacity: 1;
      transform: translateY(0);
    }

    .alert-custom.success {
      background: #ecfdf5;
      color: #047857;
      border-left: 4px solid #10b981;
    }

    .alert-custom.error {
      background: #fef2f2;
      color: #dc2626;
      border-left: 4px solid #ef4444;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Tabla de Movimientos */
    .movements-card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .movements-card h6 {
      font-size: 18px;
      font-weight: 600;
      color: #1a202c;
      margin-bottom: 5px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .movements-card h6 i {
      color: #3b82f6;
    }

    .movements-card p {
      font-size: 14px;
      color: #64748b;
      margin-bottom: 20px;
    }

    .table-wrapper {
      overflow-x: auto;
    }

    .striped-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    .striped-table thead {
      background: #f8fafc;
      border-bottom: 2px solid #e2e8f0;
    }

    .striped-table th {
      padding: 12px;
      text-align: left;
      font-weight: 600;
      color: #334155;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .striped-table td {
      padding: 12px;
      border-bottom: 1px solid #e2e8f0;
      color: #475569;
    }

    .striped-table tbody tr:hover {
      background: #f8fafc;
    }

    .badge-entrada {
      display: inline-block;
      background: #ecfdf5;
      color: #047857;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
    }

    .badge-salida {
      display: inline-block;
      background: #fef2f2;
      color: #dc2626;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
    }

    /* Tabla de Productos */
    .card-style {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .card-style h6 {
      font-size: 18px;
      font-weight: 600;
      color: #1a202c;
      margin-bottom: 5px;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    .table thead {
      background: #f8fafc;
      border-bottom: 2px solid #e2e8f0;
    }

    .table th {
      padding: 12px;
      text-align: left;
      font-weight: 600;
      color: #334155;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .table th h6 {
      font-size: 13px;
      margin: 0;
    }

    .table td {
      padding: 12px;
      border-bottom: 1px solid #e2e8f0;
      color: #475569;
    }

    .table td.min-width {
      white-space: nowrap;
      max-width: 150px;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .table tbody tr:hover {
      background: #f8fafc;
    }

    .table tbody tr:hover input,
    .table tbody tr:hover select {
      border-color: #3b82f6 !important;
    }

    .table .action {
      display: flex;
      gap: 8px;
    }

    .table button {
      background: none;
      border: none;
      cursor: pointer;
      font-size: 16px;
      padding: 4px 8px;
      border-radius: 4px;
      transition: all 0.2s;
    }

    .table .text-primary {
      color: #3b82f6;
    }

    .table .text-primary:hover {
      background: #eff6ff;
    }

    .table .text-danger {
      color: #dc2626;
    }

    .table .text-danger:hover {
      background: #fef2f2;
    }

    .table .msg {
      font-size: 12px;
      margin-top: 4px;
      display: block;
      min-height: 16px;
      color: #64748b;
    }

    .table input {
      padding: 6px 8px;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      font-size: 13px;
      width: 100%;
      font-family: inherit;
    }

    .table input:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }

    .table span.view {
      cursor: default;
    }

    .table input.edit {
      margin-top: 4px;
    }

    /* Responsive */
    @media (max-width: 992px) {
      .product-form {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .product-form-card, .movements-card {
        padding: 20px;
      }

      .submit-section {
        grid-template-columns: 1fr;
      }

      .table {
        font-size: 12px;
      }

      .table td, .table th {
        padding: 8px;
      }
    }
</style>

<section class="main-content">
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        
        <!-- ========== title-wrapper end ========== -->

        <!-- ========== tables-wrapper start ========== -->
        <div class="tables-wrapper">
            <div class="row">
                <div class="col-lg-12 px-0">
                    <div class="inventory-section">
                        <div class="row">
                            <!-- Formulario Agregar Producto -->
                            <div class="col-lg-6">
                                <div class="product-form-card">
                                    <div class="form-header">
                                        <h6><i class="lni lni-package"></i> Agregar Nuevo Producto</h6>
                                        <p>Complete los datos del producto para registrarlo en el inventario</p>
                                    </div>

                                    <div id="alertContainer"></div>

                                    <form id="productForm">
                                        <div class="product-form">
                                            <!-- Nombre del Producto -->
                                            <div class="form-field full-width">
                                                <label>
                                                    <i class="lni lni-text-format"></i>
                                                    Nombre del Producto
                                                </label>
                                                <input type="text" placeholder="Ej: Coca Cola 350ml" id="productName" required>
                                            </div>

                                            <!-- Precio Base -->
                                            <div class="form-field">
                                                <label>
                                                    <i class="lni lni-money-protection"></i>
                                                    Precio Base (sin IVA)
                                                </label>
                                                <div class="input-with-symbol">
                                                    <span class="input-symbol">$</span>
                                                    <input type="number" placeholder="0" step="1" id="basePrice" required>
                                                </div>
                                            </div>

                                            <!-- IVA -->
                                            @if($empresa && $empresa->cobra_iva)
                                                <div class="form-field">
                                                    <label>
                                                        <i class="lni lni-calculator"></i>
                                                        IVA (%)
                                                    </label>
                                                    <div class="input-with-symbol input-with-suffix">
                                                        <input type="number" placeholder="19" step="0.01" id="ivaPercent" value="19" min="0" max="100">
                                                        <span class="input-suffix">%</span>
                                                    </div>
                                                </div>
                                            @else
                                                <input type="hidden" id="ivaPercent" value="0">
                                            @endif

                                            <!-- Stock Inicial -->
                                            <div class="form-field full-width">
                                                <label>
                                                    <i class="lni lni-layers"></i>
                                                    Stock Inicial
                                                </label>
                                                <div class="quantity-control-wrapper">
                                                    <button type="button" class="quantity-btn" onclick="decreaseStock(event)">
                                                        <i class="lni lni-minus"></i>
                                                    </button>
                                                    <div class="quantity-display" id="stockValue">0</div>
                                                    <button type="button" class="quantity-btn" onclick="increaseStock(event)">
                                                        <i class="lni lni-plus"></i>
                                                    </button>
                                                    <span class="quantity-label">unidades</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="submit-section">
                                            <button type="button" class="btn-reset" onclick="resetForm(event)">
                                                <i class="lni lni-reload"></i> Limpiar
                                            </button>
                                            <button type="button" class="btn-submit" id="btnAddProduct" onclick="addProduct(event)">
                                                <i class="lni lni-checkmark-circle"></i> Agregar Producto
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Tabla de Movimientos -->
                            <div class="col-lg-6">
                                <div class="movements-card">
                                    <h6><i class="lni lni-reload"></i> Movimientos Recientes</h6>
                                    <p>Registro de entradas y salidas de productos en la tienda</p>

                                    <div class="table-wrapper table-responsive">
                                        <table class="striped-table">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Producto</th>
                                                    <th>Cantidad</th>
                                                    <th>Tipo</th>
                                                    <th>Origen</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($movimientos->take(5) as $m)
                                                    <tr>
                                                        <td>{{ $m->created_at ? \Carbon\Carbon::parse($m->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                                        <td>{{ $m->producto_nombre ?? 'Producto #' . $m->producto_id }}</td>
                                                        <td>{{ $m->cantidad }}</td>
                                                        <td>
                                                            @if($m->tipo === 'entrada')
                                                                <span class="badge-entrada">Entrada</span>
                                                            @else
                                                                <span class="badge-salida">Salida</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                $origenLower = strtolower($m->origen ?? '');
                                                            @endphp
                                                            @if($origenLower === 'registro_producto')
                                                                Registro
                                                            @elseif($origenLower === 'venta_anulada')
                                                                Anulada
                                                            @else
                                                                {{ ucfirst($m->origen ?? '-') }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" style="text-align: center; color: #999;">Sin movimientos</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Productos -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-style mb-30">
                        <h6>Tabla de Productos</h6>
                        <p style="font-size: 14px; color: #64748b; margin-bottom: 20px;">
                            Lista de productos registrados con información de precios y stock.
                        </p>
                        <div class="table-wrapper table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><h6 style="margin: 0;">ID</h6></th>
                                        <th><h6 style="margin: 0;">Nombre</h6></th>
                                        <th><h6 style="margin: 0;">Precio s/IVA</h6></th>
                                        @if($empresa && $empresa->cobra_iva)
                                            <th><h6 style="margin: 0;">IVA %</h6></th>
                                            <th><h6 style="margin: 0;">Precio c/IVA</h6></th>
                                        @endif
                                        <th><h6 style="margin: 0;">Stock</h6></th>
                                        <th><h6 style="margin: 0;">Acciones</h6></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productos as $producto)
                                        <tr id="producto-{{ $producto->id }}">
                                            <td class="min-width">
                                                <p>{{ $producto->id }}</p>
                                            </td>
                                            <td class="min-width">
                                                <span class="view" data-field="nombre">{{ $producto->nombre }}</span>
                                                <input class="edit" data-field="nombre" type="text" value="{{ $producto->nombre }}" hidden>
                                            </td>
                                            <td class="min-width">
                                                <span class="view" data-field="precio">${{ number_format($producto->precio, 0, ',', '.') }}</span>
                                                <input class="edit precio_input" data-field="precio" type="text" inputmode="numeric" value="{{ number_format($producto->precio, 0, ',', '.') }}" hidden>
                                            </td>
                                            @if($empresa && $empresa->cobra_iva)
                                                <td class="min-width">
                                                    <span class="view" data-field="iva">{{ $producto->iva > 0 ? $producto->iva . '%' : '-' }}</span>
                                                    <input class="edit iva_input" data-field="iva" type="number" step="0.01" value="{{ $producto->iva }}" hidden>
                                                </td>
                                                <td class="min-width">
                                                    <span class="view precio_con_iva_span" data-field="precio_con_iva">${{ number_format($producto->precio_con_iva, 0, ',', '.') }}</span>
                                                    <input class="edit" data-field="precio_con_iva" type="text" value="{{ number_format($producto->precio_con_iva, 0, ',', '.') }}" hidden readonly>
                                                </td>
                                            @endif
                                            <td class="min-width">
                                                <span class="view stock_view" data-field="stock">{{ $producto->stock }}</span>
                                                <input class="edit stock_input" data-field="stock" type="text" value="{{ $producto->stock }}" hidden>
                                            </td>
                                            <td>
                                                <div class="action">
                                                    <button type="button" class="text-primary" onclick="editarProducto({{ $producto->id }})" title="Editar">
                                                        <i class="lni lni-pencil"></i>
                                                    </button>
                                                    <button type="button" class="text-danger" onclick="eliminarProducto({{ $producto->id }})" title="Eliminar">
                                                        <i class="lni lni-trash-can"></i>
                                                    </button>
                                                    <button type="button" class="text-primary" onclick="guardarProducto({{ $producto->id }})" hidden title="Guardar">
                                                        <i class="lni lni-checkmark-circle"></i>
                                                    </button>
                                                    <button type="button" class="text-danger" onclick="cancelarEdicion({{ $producto->id }})" hidden title="Cancelar">
                                                        <i class="lni lni-close"></i>
                                                    </button>
                                                </div>
                                                <span class="msg"></span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
                                                <i class="lni lni-inbox" style="font-size: 32px; margin-bottom: 10px;"></i>
                                                <p>No hay productos registrados</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ========== tables-wrapper end ========== -->
    </div>
</section>

<script>
const csrf = '{{ csrf_token() }}';
let currentStock = 0;

// ========== FUNCIONES DEL FORMULARIO AGREGAR PRODUCTO ==========

function increaseStock(e) {
    e.preventDefault();
    currentStock++;
    document.getElementById('stockValue').textContent = currentStock;
}

function decreaseStock(e) {
    e.preventDefault();
    if (currentStock > 0) {
        currentStock--;
        document.getElementById('stockValue').textContent = currentStock;
    }
}

function resetForm(e) {
    e.preventDefault();
    document.getElementById('productForm').reset();
    document.getElementById('basePrice').value = '';
    document.getElementById('ivaPercent').value = @if($empresa && $empresa->cobra_iva)'19'@else'0'@endif;
    currentStock = 0;
    document.getElementById('stockValue').textContent = '0';
    document.getElementById('alertContainer').innerHTML = '';
}

async function addProduct(e) {
    e.preventDefault();
    
    const nombre = document.getElementById('productName').value.trim();
    const precio = parseInt(document.getElementById('basePrice').value) || 0;
    const iva = parseFloat(document.getElementById('ivaPercent').value) || 0;
    const stock = currentStock;

    if (!nombre) {
        showAlert('El nombre del producto es requerido', 'error');
        return;
    }

    if (precio <= 0) {
        showAlert('El precio debe ser mayor a 0', 'error');
        return;
    }

    const btn = document.getElementById('btnAddProduct');
    btn.disabled = true;

    try {
        const res = await fetch('/productos', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ nombre, precio, iva, stock })
        });

        const result = await res.json();

        if (!res.ok) throw result;

        showAlert('✅ Producto agregado correctamente', 'success');
        resetForm(e);

        // Recargar tabla después de 1.5s
        setTimeout(() => {
            location.reload();
        }, 1500);

    } catch (error) {
        console.error('Error:', error);
        const mensaje = error.message || error.errors?.[Object.keys(error.errors)[0]]?.[0] || 'Error al agregar el producto';
        showAlert(mensaje, 'error');
    } finally {
        btn.disabled = false;
    }
}

function showAlert(mensaje, tipo) {
    const container = document.getElementById('alertContainer');
    container.innerHTML = `<div class="alert-custom ${tipo} show">${mensaje}</div>`;
    setTimeout(() => {
        container.innerHTML = '';
    }, 4000);
}

// ========== FUNCIONES DE LA TABLA (EDICIÓN INLINE) ==========

function formatCOP(valueInt) {
    const n = parseInt(valueInt, 10) || 0;
    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function parseCOP(formatted) {
    if (!formatted) return 0;
    const digits = String(formatted).replace(/\D/g, '');
    return parseInt(digits || '0', 10);
}

function roundPercentageInteger(precio, iva) {
    return Math.floor((precio * iva + 50) / 100);
}

function editarProducto(id) {
    const tr = document.getElementById(`producto-${id}`);
    tr.querySelectorAll('.view').forEach(e => e.hidden = true);
    tr.querySelectorAll('.edit').forEach(e => e.hidden = false);

    // Sanitizar stock
    const stockInput = tr.querySelector('.stock_input');
    if (stockInput) {
        stockInput.oninput = function() {
            this.value = this.value.replace(/\D/g, '');
        };
    }

    // Sanitizar IVA
    const ivaInput = tr.querySelector('.iva_input');
    if (ivaInput) {
        ivaInput.oninput = function() {
            let value = this.value.replace(/[^\d.]/g, '');
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            this.value = value;
        };
    }

    attachRecalculo(tr);
    toggleButtons(tr, true);
}

function cancelarEdicion(id) {
    const tr = document.getElementById(`producto-${id}`);
    tr.querySelectorAll('.view').forEach(e => e.hidden = false);
    tr.querySelectorAll('.edit').forEach(e => e.hidden = true);
    tr.querySelector('.msg').innerText = '';

    const precioInput = tr.querySelector('.precio_input');
    const precioView = tr.querySelector('span.view[data-field="precio"]');
    if (precioInput && precioView) precioInput.value = formatCOP(parseCOP(precioView.innerText));

    const stockInput = tr.querySelector('.stock_input');
    if (stockInput) {
        stockInput.value = stockInput.dataset.originalStock;
    }

    toggleButtons(tr, false);
}

function toggleButtons(tr, editing) {
    const btnEdit = tr.querySelector('[onclick*="editarProducto"]');
    const btnDelete = tr.querySelector('[onclick*="eliminarProducto"]');
    const btnSave = tr.querySelector('[onclick*="guardarProducto"]');
    const btnCancel = tr.querySelector('[onclick*="cancelarEdicion"]');

    if (btnEdit) btnEdit.hidden = editing;
    if (btnDelete) btnDelete.hidden = editing;
    if (btnSave) btnSave.hidden = !editing;
    if (btnCancel) btnCancel.hidden = !editing;
}

function attachRecalculo(tr) {
    const precioInput = tr.querySelector('.precio_input');
    const ivaInput = tr.querySelector('.iva_input');
    const precioConIvaSpan = tr.querySelector('.precio_con_iva_span');

    if (!precioInput || !ivaInput || !precioConIvaSpan) return;

    function sanitizeAndFormatPrecio() {
        const onlyDigits = precioInput.value.replace(/\D/g, '');
        const intVal = parseInt(onlyDigits || '0', 10);
        precioInput.value = formatCOP(intVal);
        recalcular(intVal, parseFloat(ivaInput.value) || 0);
    }

    function onIvaChange() {
        const precioInt = parseCOP(precioInput.value);
        recalcular(precioInt, parseFloat(ivaInput.value) || 0);
    }

    function recalcular(precioInt, ivaFloat) {
        precioInt = parseInt(precioInt || 0, 10);
        ivaFloat = parseFloat(ivaFloat || 0);
        
        if (ivaFloat < 0) ivaFloat = 0;
        if (ivaFloat > 100) ivaFloat = 100;
        
        const ivaAmount = roundPercentageInteger(precioInt, ivaFloat);
        const precioConIva = precioInt + ivaAmount;
        precioConIvaSpan.innerText = '$' + formatCOP(precioConIva);
        const precioConIvaInput = tr.querySelector('input[data-field="precio_con_iva"]');
        if (precioConIvaInput) precioConIvaInput.value = formatCOP(precioConIva);
    }

    precioInput.oninput = sanitizeAndFormatPrecio;
    ivaInput.oninput = onIvaChange;

    sanitizeAndFormatPrecio();
}

async function guardarProducto(id) {
    const tr = document.getElementById(`producto-${id}`);
    const msg = tr.querySelector('.msg');
    msg.innerText = 'Guardando...';

    const data = {};

    tr.querySelectorAll('.edit').forEach(input => {
        const field = input.dataset.field;
        if (field === 'precio_con_iva') return;
        if (field === 'precio') {
            data.precio = parseCOP(input.value);
            return;
        }
        if (field === 'iva') {
            let ivaValue = parseFloat(input.value) || 0;
            if (ivaValue < 0) ivaValue = 0;
            if (ivaValue > 100) ivaValue = 100;
            data.iva = ivaValue;
            return;
        }
        if (field === 'stock') {
            const cleanValue = input.value.replace(/\D/g, '');
            data.stock = parseInt(cleanValue, 10) || 0;
            return;
        }
        data[field] = input.value;
    });

    const precioVal = typeof data.precio !== 'undefined' ? Number(data.precio) : 0;
    if (!precioVal || precioVal <= 0) {
        msg.style.color = 'red';
        msg.innerText = 'El precio debe ser mayor a 0.';
        return;
    }

    if (tr.dataset.saving === '1') return;
    tr.dataset.saving = '1';

    disableRow(tr, true);

    try {
        const res = await fetch(`/productos/${id}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await res.json();

        if (!res.ok) throw result;

        if (data.nombre !== undefined) {
            const nombreSpan = tr.querySelector('span.view[data-field="nombre"]');
            if (nombreSpan) nombreSpan.innerText = data.nombre;
        }

        if (typeof data.precio !== 'undefined') {
            const precioSpan = tr.querySelector('span.view[data-field="precio"]');
            if (precioSpan) precioSpan.innerText = '$' + formatCOP(data.precio);
        }

        if (typeof data.iva !== 'undefined') {
            const ivaSpan = tr.querySelector('span.view[data-field="iva"]');
            if (ivaSpan) ivaSpan.innerText = data.iva + '%';
        }

        if (result.producto && typeof result.producto.precio_con_iva !== 'undefined') {
            tr.querySelector('.precio_con_iva_span').innerText = '$' + formatCOP(result.producto.precio_con_iva);
            const precioConIvaInput = tr.querySelector('input[data-field="precio_con_iva"]');
            if (precioConIvaInput) precioConIvaInput.value = formatCOP(result.producto.precio_con_iva);
        }

        if (result.producto && typeof result.producto.stock !== 'undefined') {
            const stockView = tr.querySelector('.stock_view');
            const stockInput = tr.querySelector('.stock_input');
            
            stockView.innerText = result.producto.stock;
            stockInput.value = result.producto.stock;
            stockInput.dataset.originalStock = result.producto.stock;
        }

        msg.style.color = 'green';
        msg.innerText = '✅ Guardado';
        
        setTimeout(() => {
            cancelarEdicion(id);
            msg.innerText = '';
        }, 1500);

    } catch (e) {
        console.error('Error:', e);
        msg.style.color = 'red';
        msg.innerText = e.message || 'Error al actualizar';
    } finally {
        disableRow(tr, false);
        tr.dataset.saving = '0';
    }
}

async function eliminarProducto(id) {
    if (!confirm('¿Eliminar este producto?')) return;
    
    const tr = document.getElementById(`producto-${id}`);
    const msg = tr.querySelector('.msg');
    msg.innerText = 'Eliminando...';

    try {
        const res = await fetch(`/productos/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            }
        });

        const result = await res.json();
        if (!res.ok) throw result;

        tr.remove();

    } catch (error) {
        console.error('Error:', error);
        msg.innerText = 'No se pudo eliminar';
    }
}

function disableRow(tr, state) {
    tr.querySelectorAll('button, input, select').forEach(e => e.disabled = state);
}
</script>

@endsection
