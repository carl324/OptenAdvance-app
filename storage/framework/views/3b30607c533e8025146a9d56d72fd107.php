

<?php $__env->startSection('title', 'Devolución — Venta ' . ($venta->factura->numero ?? '#' . $venta->id)); ?>

<?php $__env->startSection('content'); ?>
<?php
$unidadAbrev = [
    'Unidad'=>'und','Par'=>'par','Docena'=>'doc','Caja'=>'caja','Paquete'=>'paq',
    'Sobre'=>'sob','Frasco'=>'fco','Botella'=>'bot','Lata'=>'lata','Tubo'=>'tubo',
    'Gramo'=>'g','Kilogramo'=>'kg','Libra'=>'lb','Tonelada'=>'t','Onza'=>'oz',
    'Mililitro'=>'ml','Litro'=>'L','Galón'=>'gal','Metro cúbico'=>'m³',
    'Milímetro'=>'mm','Centímetro'=>'cm','Metro'=>'m','Metro lineal'=>'m lineal',
    'Kilómetro'=>'km','Pulgada'=>'in','Pie'=>'ft','Metro cuadrado'=>'m²',
    'Centímetro cuadrado'=>'cm²','Hectárea'=>'ha'
];
?>
<style>
    .form-check-input {
    width: 18px;
    height: 18px;
    border: 2px solid #94a3b8;
    cursor: pointer;
}


.form-check-input:focus {
    border-color: #4A6CF7;
}
</style>
<section class="section">
    <div class="container-fluid">
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="mb-0">Devolución: <?php echo e($venta->factura->numero ?? '#' . $venta->id); ?></h4>
                    <p class="text-sm text-gray mb-0"><?php echo e(Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i')); ?> · <?php echo e($venta->factura->cliente_nombre ?? 'Consumidor final'); ?></p>
                </div>
            </div>
        </div>

        
        <div id="alerta-exito" class="alert alert-success d-none mt-3" role="alert">
            <i class="lni lni-checkmark-circle me-2"></i>
            <span id="texto-exito"></span>
        </div>

        
        <div id="alerta-error" class="alert alert-danger d-none mt-3" role="alert">
            <i class="lni lni-warning me-2"></i>
            <span id="texto-error"></span>
        </div>

        <div class="row mt-20">
            
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
                                    <th class="text-center" style="white-space:nowrap;padding:12px 16px;"><h6 class="text-sm text-medium">Comprado</h6></th>
<th class="text-center" style="white-space:nowrap;padding:12px 16px;"><h6 class="text-sm text-medium">Devuelto</h6></th>
<th class="text-center" style="white-space:nowrap;padding:12px 16px;"><h6 class="text-sm text-medium">A devolver</h6></th>
<th class="text-end" style="white-space:nowrap;padding:12px 16px;"><h6 class="text-sm text-medium">Subtotal</h6></th>
                                </tr>
                            </thead>
                            <tbody id="tabla-productos">
                                <?php $__currentLoopData = $venta->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $yaDevuelto = $devuelto[$detalle->id] ?? 0;
                                        $disponible = $detalle->cantidad - $yaDevuelto;
                                        $agotado = $disponible <= 0;
                                    ?>
                                    <tr id="fila-<?php echo e($detalle->id); ?>" class="<?php echo e($agotado ? 'opacity-50' : ''); ?>">
                                        <td>
                                            <?php if(!$agotado): ?>
                                                <input type="checkbox" class="form-check-input producto-check"
                                                    data-id="<?php echo e($detalle->id); ?>"
                                                    data-precio="<?php echo e($detalle->precio_unitario); ?>"
                                                    data-disponible="<?php echo e($disponible); ?>"
                                                    data-nombre="<?php echo e($detalle->producto->nombre); ?>"
                                                    onchange="toggleProducto(this)">
                                            <?php else: ?>
                                                <i class="lni lni-checkmark-circle text-success" title="Totalmente devuelto"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <p class="text-sm"><?php echo e($detalle->producto->nombre); ?></p>
                                            <?php if($agotado): ?>
                                                <span class="badge bg-success" style="font-size:10px;">Devuelto</span>
                                            <?php elseif($yaDevuelto > 0): ?>
                                                <span class="badge bg-warning text-dark" style="font-size:10px;">Parcial</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-sm"><?php echo e($detalle->cantidad); ?></td>
                                        <td class="text-center text-sm"><?php echo e($yaDevuelto > 0 ? $yaDevuelto : '0'); ?></td>
                                        <td class="text-center">
                                            <?php if(!$agotado): ?>
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <input type="number"
    id="cantidad-<?php echo e($detalle->id); ?>"
    class="form-control form-control-sm text-center cantidad-input"
    style="width:<?php echo e(strlen((string)$disponible) * 12 + 20); ?>px;min-width:50px;max-width:80px;margin:0 auto;"
    min="<?php echo e(in_array($detalle->producto->unidad ?? 'Unidad', ['Unidad','Par','Docena','Caja','Paquete','Sobre','Frasco','Botella','Lata','Tubo']) ? '1' : '0.01'); ?>"
    max="<?php echo e($disponible); ?>"
    step="<?php echo e(in_array($detalle->producto->unidad ?? 'Unidad', ['Unidad','Par','Docena','Caja','Paquete','Sobre','Frasco','Botella','Lata','Tubo']) ? '1' : '0.01'); ?>"
    value="<?php echo e($disponible); ?>"
    data-id="<?php echo e($detalle->id); ?>"
    oninput="
        if(<?php echo e(in_array($detalle->producto->unidad ?? 'Unidad', ['Unidad','Par','Docena','Caja','Paquete','Sobre','Frasco','Botella','Lata','Tubo']) ? 'true' : 'false'); ?>) {
            this.value = Math.floor(this.value);
        }
        if(parseFloat(this.value) > <?php echo e($disponible); ?>) this.value = <?php echo e($disponible); ?>;
        if(parseFloat(this.value) < 0) this.value = 0;
        this.style.width = Math.max(50, this.value.length * 12 + 20) + 'px';
        recalcularSubtotal(<?php echo e($detalle->id); ?>, <?php echo e($detalle->precio_unitario); ?>)"
    onchange="recalcularSubtotal(<?php echo e($detalle->id); ?>, <?php echo e($detalle->precio_unitario); ?>)"
    disabled> <span style="color:#94a3b8;font-size:0.75rem;font-weight:600;">
            <?php echo e($unidadAbrev[$detalle->producto->unidad ?? 'Unidad'] ?? $detalle->producto->unidad ?? 'und'); ?>

        </span>
        </div>
                                            <?php else: ?>
                                                <span class="text-sm text-gray">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-sm" id="subtotal-<?php echo e($detalle->id); ?>">—</td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-5">
    <div class="card-style mb-30">
        <h6 class="text-medium mb-25">Detalles de la devolución</h6>

        
        <div class="mb-20">
            <label class="text-sm fw-semibold mb-8 d-block">Motivo <span class="text-danger">*</span></label>
            <select id="motivo_devolucion_id" class="form-select" style="border-radius:10px;padding:10px 14px;border:1.5px solid #e2e8f0;font-size:13px;">
                <option value="">Selecciona un motivo...</option>
                <?php $__currentLoopData = $motivos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $motivo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($motivo->id); ?>"><?php echo e($motivo->nombre); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <div class="text-danger small mt-1 d-none" id="error-motivo">Selecciona un motivo</div>
        </div>

        
        <div class="mb-20">
            <label class="text-sm fw-semibold mb-8 d-block">Observación <span class="text-gray">(opcional)</span></label>
            <textarea id="observacion" rows="3" maxlength="500" placeholder="Detalle adicional..."
                style="width:100%;border-radius:10px;padding:10px 14px;border:1.5px solid #e2e8f0;font-size:13px;resize:none;outline:none;font-family:inherit;"></textarea>
        </div>

        
        <div class="mb-20">
            <label class="text-sm fw-semibold mb-8 d-block">Método de reembolso <span class="text-danger">*</span></label>
            <br><div class="d-flex gap-2">
                <button type="button" class="metodo-btn flex-fill" data-metodo="efectivo" onclick="seleccionarMetodo('efectivo')"
                    style="padding:14px 8px;border:2px solid #e0e0e0;background:white;border-radius:12px;cursor:pointer;font-size:13px;font-weight:500;transition:all 0.2s;">
                    <i class="lni lni-money-location d-block mb-1" style="font-size:22px;"></i> Efectivo
                </button>
                <button type="button" class="metodo-btn flex-fill" data-metodo="transferencia" onclick="seleccionarMetodo('transferencia')"
                    style="padding:14px 8px;border:2px solid #e0e0e0;background:white;border-radius:12px;cursor:pointer;font-size:13px;font-weight:500;transition:all 0.2s;">
                    <i class="lni lni-apartment d-block mb-1" style="font-size:22px;"></i> Transferencia
                </button>
            </div>
            <input type="hidden" id="metodo_reembolso" value="">
            <div class="text-danger small mt-1 d-none" id="error-metodo">Selecciona un método</div>
        </div>

        <div class="mb-20" style="background:#f8fafc;border-radius:12px;padding:16px;">
            <div class="d-flex justify-content-between align-items-center mb-12">
                <span class="text-sm text-gray">Monto calculado</span>
                <strong class="text-sm" id="monto-calculado-display">$0</strong>
            </div>
            <br>
            <label class="text-sm fw-semibold mb-8 d-block">Monto real entregado <span class="text-danger">*</span></label>
            <input type="text" id="monto_real" placeholder="0" oninput="formatMontoReal(this); montoRealModificado = true;"
                style="width:100%;padding:10px 14px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:14px;font-weight:600;outline:none;">
            <div class="text-danger small mt-1 d-none" id="error-monto">Ingresa el monto real</div>
        </div>

        
       <button type="button" id="btn-confirmar" onclick="confirmarDevolucion()"
    class="main-btn primary-btn btn-hover w-100" style="padding:12px;">
    <i class="lni lni-checkmark me-2"></i> Confirmar devolución
</button>
<div id="msg-sin-productos" class="text-center mt-2" style="font-size:12px;color:#4A6CF7;display:none;">
    Selecciona al menos un producto para continuar
</div>
    </div>
</div>
        </div>
    </div>
</section>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const VENTA_ID = <?php echo e($venta->id); ?>;
let metodoSeleccionado = '';
let montoCalculado = 0;
let devolucionEnProceso = false;
let montoRealModificado = false;

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
if (montoRealInput && !montoRealModificado) {
    const digits = Math.round(total).toString();
    montoRealInput.value = digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
    verificarBoton();
}

function verificarBoton() {
    const hayProductos = document.querySelectorAll('.producto-check:checked').length > 0;
    const msg = document.getElementById('msg-sin-productos');
    msg.style.display = hayProductos ? 'none' : 'block';
}

async function confirmarDevolucion() {
    if (devolucionEnProceso) return;
    const hayProductos = document.querySelectorAll('.producto-check:checked').length > 0;
if (!hayProductos) {
    document.getElementById('msg-sin-productos').style.display = 'block';
    return;
}
    // Validaciones
    const motivoId = document.getElementById('motivo_devolucion_id').value;
if (!motivoId) {
    document.getElementById('error-motivo').classList.remove('d-none');
    document.getElementById('motivo_devolucion_id').scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
}

if (!metodoSeleccionado) {
    document.getElementById('error-metodo').classList.remove('d-none');
    document.getElementById('error-metodo').scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
}

const montoRealRaw = document.getElementById('monto_real').value.replace(/\./g, '');
if (!montoRealRaw) {
    document.getElementById('error-monto').classList.remove('d-none');
    document.getElementById('monto_real').scrollIntoView({ behavior: 'smooth', block: 'center' });
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
    devolucionEnProceso = true;
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

        if (!res.ok && res.status !== 422) {
            mostrarError('Error del servidor. Intenta de nuevo.');
            btn.disabled = false;
            btn.textContent = 'Confirmar devolución';
            return;
        }
        const data = await res.json();

        if (data.success) {
            mostrarExito('Devolución registrada correctamente. Monto: ' + formatoPrecio(data.monto_real));
            
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
montoRealModificado = false;
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
    } finally {
        devolucionEnProceso = false;
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\optenadvance\app\www\resources\views/ventas/devolucion.blade.php ENDPATH**/ ?>