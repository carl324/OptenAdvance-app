@extends('layouts.app')

@section('content')

<table border="1" cellpadding="5">
    <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio s/IVA</th>
                @if( ($empresa && $empresa->cobra_iva) || (!empty($hayProductosConIVA) && $hayProductosConIVA) )
                <th>IVA %</th>
                @endif
                @if( ($empresa && $empresa->cobra_iva) || (!empty($hayProductosConIVA) && $hayProductosConIVA) )
                <th>Precio c/IVA</th>
                @else
                <th>Precio</th>
                @endif
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
    </thead>
    <tbody>
        @foreach($productos as $producto)
            <tr id="producto-{{ $producto->id }}">
                <td>{{ $producto->id }}</td>

                <td>
                    <span class="view" data-field="nombre">{{ $producto->nombre }}</span>
                    <input class="edit" data-field="nombre" type="text" value="{{ $producto->nombre }}" hidden>
                </td>

                <td>
                    <span class="view" data-field="precio">${{ number_format($producto->precio, 0, ',', '.') }}</span>
                    <input class="edit precio_input" data-field="precio" type="text" inputmode="numeric" pattern="\d*" value="{{ number_format($producto->precio, 0, ',', '.') }}" hidden>
                </td>

                @if( ($empresa && $empresa->cobra_iva) || (!empty($hayProductosConIVA) && $hayProductosConIVA) )
                <td>
                    <span class="view" data-field="iva">{{ $producto->iva > 0 ? $producto->iva . '%' : '-' }}</span>
                    <input 
                        class="edit iva_input" 
                        data-field="iva" 
                        type="text" 
                        inputmode="decimal" 
                        value="{{ $producto->iva }}" 
                        placeholder="Ej: 19"
                        hidden>
                </td>

                <td>
                    <span class="view precio_con_iva_span" data-field="precio_con_iva">${{ number_format($producto->precio_con_iva, 0, ',', '.') }}</span>
                    <input class="edit" data-field="precio_con_iva" type="text" value="{{ number_format($producto->precio_con_iva, 0, ',', '.') }}" hidden readonly>
                </td>
                @else
                <td>
                    <span class="view precio_con_iva_span" data-field="precio_con_iva">${{ number_format($producto->precio, 0, ',', '.') }}</span>
                    <input class="edit" data-field="precio_con_iva" type="text" value="{{ number_format($producto->precio, 0, ',', '.') }}" hidden readonly>
                </td>
                @endif

                <td>
                    <span class="view stock_view" data-field="stock">{{ $producto->stock }}</span>
                    <input 
                        class="edit stock_input" 
                        data-field="stock" 
                        data-original-stock="{{ $producto->stock }}" 
                        type="text"
                        inputmode="numeric"
                        pattern="\d*"
                        value="{{ $producto->stock }}" 
                        hidden>
                </td>

                <td>
                    <button type="button" onclick="editarProducto({{ $producto->id }})">✏️</button>
                    <button type="button" onclick="eliminarProducto({{ $producto->id }})">🗑️</button>
                    <button type="button" onclick="guardarProducto({{ $producto->id }})" hidden>💾</button>
                    <button type="button" onclick="cancelarEdicion({{ $producto->id }})" hidden>❌</button>
                    <div class="msg" style="color:red;font-size:12px;"></div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
const csrf = '{{ csrf_token() }}';

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

    // ✅ Sanitizar stock
    const stockInput = tr.querySelector('.stock_input');
    if (stockInput) {
        stockInput.oninput = function() {
            this.value = this.value.replace(/\D/g, '');
        };
        
        stockInput.onkeydown = function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                guardarProducto(id);
                return false;
            }
        };
    }

    // ✅ Sanitizar IVA (permitir números y punto decimal)
    const ivaInput = tr.querySelector('.iva_input');
    if (ivaInput) {
        ivaInput.oninput = function() {
            // Permitir solo números y un punto decimal
            let value = this.value.replace(/[^\d.]/g, '');
            
            // Evitar múltiples puntos
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            
            this.value = value;
        };
        
        ivaInput.onkeydown = function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                guardarProducto(id);
                return false;
            }
        };
    }

    // Prevenir Enter en otros campos
    tr.querySelectorAll('.edit').forEach(input => {
        if (!input.classList.contains('stock_input') && !input.classList.contains('iva_input')) {
            input.onkeydown = (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    e.stopPropagation();
                    guardarProducto(id);
                    return false;
                }
            };
        }
    });

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
    tr.querySelector('[onclick^="editarProducto"]').hidden = editing;
    tr.querySelector('[onclick^="eliminarProducto"]').hidden = editing;
    tr.querySelector('[onclick^="guardarProducto"]').hidden = !editing;
    tr.querySelector('[onclick^="cancelarEdicion"]').hidden = !editing;
}

function attachRecalculo(tr) {
    const precioInput = tr.querySelector('.precio_input');
    const ivaInput = tr.querySelector('.iva_input'); // ✅ Cambiado de select a input
    const precioConIvaSpan = tr.querySelector('.precio_con_iva_span');

    if (!precioInput || !ivaInput || !precioConIvaSpan) return;

    function sanitizeAndFormatPrecio() {
        const onlyDigits = precioInput.value.replace(/\D/g, '');
        const intVal = parseInt(onlyDigits || '0', 10);
        precioInput.value = formatCOP(intVal);
        recalcular(intVal, parseFloat(ivaInput.value) || 0); // ✅ parseFloat para decimales
    }

    function onIvaChange() {
        const precioInt = parseCOP(precioInput.value);
        recalcular(precioInt, parseFloat(ivaInput.value) || 0); // ✅ parseFloat
    }

    function recalcular(precioInt, ivaFloat) {
        precioInt = parseInt(precioInt || 0, 10);
        ivaFloat = parseFloat(ivaFloat || 0);
        
        // ✅ Limitar IVA entre 0 y 100
        if (ivaFloat < 0) ivaFloat = 0;
        if (ivaFloat > 100) ivaFloat = 100;
        
        const ivaAmount = roundPercentageInteger(precioInt, ivaFloat);
        const precioConIva = precioInt + ivaAmount;
        precioConIvaSpan.innerText = '$' + formatCOP(precioConIva);
        const precioConIvaInput = tr.querySelector('input[data-field="precio_con_iva"]');
        if (precioConIvaInput) precioConIvaInput.value = formatCOP(precioConIva);
    }

    precioInput.oninput = sanitizeAndFormatPrecio;
    precioInput.onpaste = (e) => {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text') || '';
        precioInput.value = paste.replace(/\D/g,'');
        sanitizeAndFormatPrecio();
    };
    ivaInput.oninput = onIvaChange; // ✅ Cambiado de onchange a oninput

    sanitizeAndFormatPrecio();
}

async function guardarProducto(id) {
    const tr = document.getElementById(`producto-${id}`);
    const msg = tr.querySelector('.msg');
    msg.innerText = '';

    const data = {};

    tr.querySelectorAll('.edit').forEach(input => {
        const field = input.dataset.field;
        if (field === 'precio_con_iva') return;
        if (field === 'precio') {
            data.precio = parseCOP(input.value);
            return;
        }
        if (field === 'iva') {
            // ✅ Parseamos como float y limitamos entre 0-100
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

    // Frontend validation: precio must be > 0
    const precioVal = typeof data.precio !== 'undefined' ? Number(data.precio) : 0;
    if (!precioVal || precioVal <= 0) {
        msg.style.color = 'red';
        msg.innerText = 'El precio debe ser mayor a 0.';
        // keep row in edit mode, do not send request
        return;
    }

    // Prevent double submission for this row
    if (tr.dataset.saving === '1') return;
    tr.dataset.saving = '1';

    disableRow(tr, true);

    try {
        console.log('📤 Enviando:', data);

        const res = await fetch(`/productos/${id}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        console.log('📥 Status:', res.status);
        const result = await res.json();
        console.log('📥 Respuesta:', result);

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
        console.error('❌ Error:', e);
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

    } catch {
        msg.innerText = 'No se pudo eliminar';
    }
}

function disableRow(tr, state) {
    tr.querySelectorAll('button, input, select').forEach(e => e.disabled = state);
}
</script>

@endsection