@extends('layouts.app')

@section('title', 'Devolución — Venta ' . ($venta->factura->numero ?? '#' . $venta->id))

@section('content')
<section class="section">
    <div class="container-fluid">
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-auto">
                    <a href="{{ route('ventas.detalle', $venta) }}" class="btn btn-light btn-sm">
                        <i class="lni lni-arrow-left me-1"></i> Volver
                    </a>
                </div>
                <div class="col">
                    <h4 class="mb-0">Devolución — {{ $venta->factura->numero ?? '#' . $venta->id }}</h4>
                    <p class="text-sm text-gray mb-0">{{ Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }} · {{ $venta->factura->cliente_nombre ?? 'Consumidor final' }}</p>
                </div>
            </div>
        </div>

        {{-- Alerta éxito --}}
        <div id="alerta-exito" class="alert alert-success d-none mt-3" role="alert">
            <i class="lni lni-checkmark-circle me-2"></i>
            <span id="texto-exito"></span>
        </div>

        {{-- Alerta error --}}
        <div id="alerta-error" class="alert alert-danger d-none mt-3" role="alert">
            <i class="lni lni-warning me-2"></i>
            <span id="texto-error"></span>
        </div>

        <div class="row mt-20">
            {{-- LEFT: Productos --}}
            <div class="col-lg-7">
                <div class="card-style mb-30">
                    <div class="title mb-20">
                        <h6 class="text-medium">Productos de la venta</h6>
                        <p class="text-xs text-gray">Selecciona los productos a devolver y la cantidad</p>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width:40px;"></th>
                                    <th><h6 class="text-sm text-medium">Producto</h6></th>
                                    <th class="text-center"><h6 class="text-sm text-medium">Comprado</h6></th>
                                    <th class="text-center"><h6 class="text-sm text-medium">Devuelto</h6></th>
                                    <th class="text-center"><h6 class="text-sm text-medium">A devolver</h6></th>
                                    <th class="text-end"><h6 class="text-sm text-medium">Subtotal</h6></th>
                                </tr>
                            </thead>
                            <tbody id="tabla-productos">
                                @foreach($venta->detalles as $detalle)
                                    @php
                                        $yaDevuelto = $devuelto[$detalle->id] ?? 0;
                                        $disponible = $detalle->cantidad - $yaDevuelto;
                                        $agotado = $disponible <= 0;
                                    @endphp
                                    <tr id="fila-{{ $detalle->id }}" class="{{ $agotado ? 'opacity-50' : '' }}">
                                        <td>
                                            @if(!$agotado)
                                                <input type="checkbox" class="form-check-input producto-check"
                                                    data-id="{{ $detalle->id }}"
                                                    data-precio="{{ $detalle->precio_unitario }}"
                                                    data-disponible="{{ $disponible }}"
                                                    data-nombre="{{ $detalle->producto->nombre }}"
                                                    onchange="toggleProducto(this)">
                                            @else
                                                <i class="lni lni-checkmark-circle text-success" title="Totalmente devuelto"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-sm">{{ $detalle->producto->nombre }}</p>
                                            @if($agotado)
                                                <span class="badge bg-success" style="font-size:10px;">Devuelto</span>
                                            @elseif($yaDevuelto > 0)
                                                <span class="badge bg-warning text-dark" style="font-size:10px;">Parcial</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-sm">{{ $detalle->cantidad }}</td>
                                        <td class="text-center text-sm">{{ $yaDevuelto > 0 ? $yaDevuelto : '—' }}</td>
                                        <td class="text-center">
                                            @if(!$agotado)
                                                <input type="number"
                                                    id="cantidad-{{ $detalle->id }}"
                                                    class="form-control form-control-sm text-center cantidad-input"
                                                    style="width:80px;margin:0 auto;"
                                                    min="0.01" max="{{ $disponible }}"
                                                    step="{{ in_array($detalle->producto->unidad ?? 'Unidad', ['Unidad','Par','Docena','Caja','Paquete','Sobre','Frasco','Botella','Lata','Tubo']) ? '1' : '0.01' }}"
                                                    value="{{ $disponible }}"
                                                    data-id="{{ $detalle->id }}"
                                                    onchange="recalcularSubtotal({{ $detalle->id }}, {{ $detalle->precio_unitario }})"
                                                    disabled>
                                            @else
                                                <span class="text-sm text-gray">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end text-sm" id="subtotal-{{ $detalle->id }}">—</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Formulario --}}
            <div class="col-lg-5">
                <div class="card-style mb-30">
                    <div class="title mb-20">
                        <h6 class="text-medium">Detalles de la devolución</h6>
                    </div>

                    {{-- Motivo --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Motivo <span class="text-danger">*</span></label>
                        <select id="motivo_devolucion_id" class="form-select form-select-sm">
                            <option value="">Selecciona un motivo...</option>
                            @foreach($motivos as $motivo)
                                <option value="{{ $motivo->id }}">{{ $motivo->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="text-danger small mt-1 d-none" id="error-motivo">Selecciona un motivo</div>
                    </div>

                    {{-- Observación --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Observación <span class="text-muted">(opcional)</span></label>
                        <textarea id="observacion" class="form-control form-control-sm" rows="2" maxlength="500" placeholder="Detalle adicional..."></textarea>
                    </div>

                    {{-- Método reembolso --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Método de reembolso <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            <button type="button" class="metodo-btn flex-fill" data-metodo="efectivo" onclick="seleccionarMetodo('efectivo')"
                                style="padding:10px;border:2px solid #e0e0e0;background:white;border-radius:8px;cursor:pointer;font-size:13px;">
                                <i class="lni lni-money-location d-block mb-1" style="font-size:20px;"></i> Efectivo
                            </button>
                            <button type="button" class="metodo-btn flex-fill" data-metodo="transferencia" onclick="seleccionarMetodo('transferencia')"
                                style="padding:10px;border:2px solid #e0e0e0;background:white;border-radius:8px;cursor:pointer;font-size:13px;">
                                <i class="lni lni-apartment d-block mb-1" style="font-size:20px;"></i> Transferencia
                            </button>
                            <button type="button" class="metodo-btn flex-fill" data-metodo="nota_credito" onclick="seleccionarMetodo('nota_credito')"
                                style="padding:10px;border:2px solid #e0e0e0;background:white;border-radius:8px;cursor:pointer;font-size:13px;">
                                <i class="lni lni-credit-cards d-block mb-1" style="font-size:20px;"></i> Nota crédito
                            </button>
                        </div>
                        <input type="hidden" id="metodo_reembolso" value="">
                        <div class="text-danger small mt-1 d-none" id="error-metodo">Selecciona un método</div>
                    </div>

                    {{-- Montos --}}
                    <div class="mb-3" style="background:#f8fafc;border-radius:12px;padding:16px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm text-gray">Monto calculado:</span>
                            <strong class="text-sm" id="monto-calculado-display">$0</strong>
                        </div>
                        <div>
                            <label class="form-label fw-semibold" style="font-size:13px;">Monto real entregado <span class="text-danger">*</span></label>
                            <input type="text" id="monto_real" class="form-control form-control-sm"
                                placeholder="0" oninput="formatMontoReal(this)">
                            <div class="text-danger small mt-1 d-none" id="error-monto">Ingresa el monto real</div>
                        </div>
                    </div>

                    {{-- Botón --}}
                    <button type="button" id="btn-confirmar" onclick="confirmarDevolucion()"
                        class="main-btn primary-btn btn-hover w-100" disabled>
                        Confirmar devolución
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const VENTA_ID = {{ $venta->id }};
let metodoSeleccionado = '';
let montoCalculado = 0;

function formatoPrecio(n) {
    return '$' + Math.round(n).toLocaleString('es-CO');
}

function formatMontoReal(input) {
    const digits = input.value.replace(/\D/g, '');
    input.value = digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function seleccionarMetodo(metodo) {
    metodoSeleccionado = metodo;
    document.getElementById('metodo_reembolso').value = metodo;
    document.querySelectorAll('.metodo-btn').forEach(btn => {
        btn.style.borderColor = btn.dataset.metodo === metodo ? '#3b82f6' : '#e0e0e0';
        btn.style.background = btn.dataset.metodo === metodo ? '#eff6ff' : 'white';
    });
    document.getElementById('error-metodo').classList.add('d-none');
    verificarBoton();
}

function toggleProducto(checkbox) {
    const id = checkbox.dataset.id;
    const input = document.getElementById(`cantidad-${id}`);
    const precio = parseFloat(checkbox.dataset.precio);
    input.disabled = !checkbox.checked;
    if (!checkbox.checked) {
        document.getElementById(`subtotal-${id}`).textContent = '—';
    } else {
        recalcularSubtotal(id, precio);
    }
    recalcularTotal();
    verificarBoton();
}

function recalcularSubtotal(id, precio) {
    const input = document.getElementById(`cantidad-${id}`);
    const cantidad = parseFloat(input.value) || 0;
    const subtotal = cantidad * precio;
    document.getElementById(`subtotal-${id}`).textContent = formatoPrecio(subtotal);
    recalcularTotal();
}

function recalcularTotal() {
    let total = 0;
    document.querySelectorAll('.producto-check:checked').forEach(chk => {
        const id = chk.dataset.id;
        const precio = parseFloat(chk.dataset.precio);
        const cantidad = parseFloat(document.getElementById(`cantidad-${id}`).value) || 0;
        total += cantidad * precio;
    });
    montoCalculado = total;
    document.getElementById('monto-calculado-display').textContent = formatoPrecio(total);

    // Autocompletar monto real
    const montoRealInput = document.getElementById('monto_real');
    if (montoRealInput) {
        const digits = Math.round(total).toString();
        montoRealInput.value = digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    verificarBoton();
}

function verificarBoton() {
    const hayProductos = document.querySelectorAll('.producto-check:checked').length > 0;
    const hayMetodo = metodoSeleccionado !== '';
    document.getElementById('btn-confirmar').disabled = !(hayProductos && hayMetodo);
}

async function confirmarDevolucion() {
    // Validaciones
    const motivoId = document.getElementById('motivo_devolucion_id').value;
    if (!motivoId) {
        document.getElementById('error-motivo').classList.remove('d-none');
        return;
    }

    if (!metodoSeleccionado) {
        document.getElementById('error-metodo').classList.remove('d-none');
        return;
    }

    const montoRealRaw = document.getElementById('monto_real').value.replace(/\./g, '');
    if (!montoRealRaw) {
        document.getElementById('error-monto').classList.remove('d-none');
        return;
    }

    const productos = [];
    document.querySelectorAll('.producto-check:checked').forEach(chk => {
        const id = chk.dataset.id;
        const cantidad = parseFloat(document.getElementById(`cantidad-${id}`).value);
        productos.push({
            venta_detalle_id: parseInt(id),
            cantidad_devuelta: cantidad
        });
    });

    if (productos.length === 0) {
        mostrarError('Selecciona al menos un producto');
        return;
    }

    const btn = document.getElementById('btn-confirmar');
    btn.disabled = true;
    btn.textContent = 'Procesando...';

    try {
        const res = await fetch(`/ventas/${VENTA_ID}/devolucion`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                motivo_devolucion_id: parseInt(motivoId),
                observacion: document.getElementById('observacion').value || null,
                metodo_reembolso: metodoSeleccionado,
                monto_calculado: montoCalculado,
                monto_real: parseInt(montoRealRaw),
                productos
            })
        });

        const data = await res.json();

        if (data.success) {
            mostrarExito('Devolución registrada correctamente. Monto: ' + formatoPrecio(data.monto_calculado));
            
            // Marcar productos devueltos como opacos
            document.querySelectorAll('.producto-check:checked').forEach(chk => {
                const fila = document.getElementById(`fila-${chk.dataset.id}`);
                fila.classList.add('opacity-50');
                chk.disabled = true;
                chk.checked = false;
                document.getElementById(`cantidad-${chk.dataset.id}`).disabled = true;
            });

            // Resetear formulario
            document.getElementById('motivo_devolucion_id').value = '';
            document.getElementById('observacion').value = '';
            metodoSeleccionado = '';
            document.querySelectorAll('.metodo-btn').forEach(b => {
                b.style.borderColor = '#e0e0e0';
                b.style.background = 'white';
            });
            document.getElementById('monto_real').value = '';
            document.getElementById('monto-calculado-display').textContent = '$0';
            btn.disabled = true;
            btn.textContent = 'Confirmar devolución';
        } else {
            mostrarError(data.message || 'Error al registrar la devolución');
            btn.disabled = false;
            btn.textContent = 'Confirmar devolución';
        }

    } catch(e) {
        mostrarError('Error de conexión');
        btn.disabled = false;
        btn.textContent = 'Confirmar devolución';
    }
}

function mostrarExito(texto) {
    const el = document.getElementById('alerta-exito');
    document.getElementById('texto-exito').textContent = texto;
    el.classList.remove('d-none');
    document.getElementById('alerta-error').classList.add('d-none');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function mostrarError(texto) {
    const el = document.getElementById('alerta-error');
    document.getElementById('texto-error').textContent = texto;
    el.classList.remove('d-none');
    document.getElementById('alerta-exito').classList.add('d-none');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
@endsection