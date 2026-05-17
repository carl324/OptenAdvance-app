

<?php $__env->startSection('title', 'Detalle Cliente'); ?>

<?php $__env->startSection('content'); ?>

<style>

/* Inputs edición tipo línea */
.campo-editar {
    display: none;
    width: 100%;
    border: none;
    border-bottom: 1.5px solid #3b82f6;
    padding: 2px 0;
    font-size: 13px;
    font-weight: 600;
    background: transparent;
    outline: none;
    text-align: right;
    color: #1e293b;
}
.modo-edicion .campo-ver  { display: none !important; }
.modo-edicion .campo-editar { display: inline-block !important; }

/* Dropdown tres puntos */
.dropdown-tres-puntos { position: relative; display: inline-block; }
.dropdown-tres-puntos .menu {
    display: none;
    position: absolute;
    right: 0; top: 100%;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    min-width: 160px;
    z-index: 999;
    padding: 4px 0;
}
.dropdown-tres-puntos .menu.abierto { display: block; }
.dropdown-tres-puntos .menu a,
.dropdown-tres-puntos .menu button {
    display: flex; align-items: center; gap: 8px;
    width: 100%; padding: 9px 16px;
    font-size: 13px; color: #334155;
    background: none; border: none; cursor: pointer;
    text-decoration: none; transition: background .15s;
}
.dropdown-tres-puntos .menu a:hover,
.dropdown-tres-puntos .menu button:hover { background: #f8fafc; }

@media print {
    .no-print { display: none !important; }
    .col-lg-4, .card-style:first-child { display: none !important; }
    .card-style { box-shadow: none !important; border: 1px solid #e0e0e0; }
}
.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    padding: 20px;
    margin-top: 20px;
}
.pagination button {
    padding: 8px 12px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.pagination button:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}
.pagination button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.table-wrapper {
    overflow: visible !important;
}
.table-responsive {
    overflow: visible !important;
}
#tbody-abonos td:last-child {
    white-space: normal;
    word-break: break-word;
    max-width: 200px;
}
</style>

<section class="table-components">
  <div class="container-fluid">

    <div class="title-wrapper pt-30 no-print">
      <div class="row align-items-center">
        
      </div>
    </div>

    <div class="row">

      
      <div class="col-lg-4 no-print">
        <div class="card-style mb-30">

          
          <div class="d-flex align-items-center justify-content-between mb-25">
            <div class="d-flex align-items-center gap-3">
              <div style="width:56px;height:56px;background:#eff6ff;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="lni lni-user" style="font-size:26px;color:#3b82f6;"></i>
              </div>
              <div>
                <h6 class="mb-1 fw-semibold" id="display-nombre"><?php echo e($cliente->nombre); ?></h6>
                <p class="text-xs text-gray mb-0">Cliente desde <?php echo e($cliente->created_at->format('d/m/Y')); ?></p>
              </div>
            </div>
            <div class="d-flex gap-1">
              <button class="border-0 bg-transparent" id="btn-editar" onclick="activarEdicion()" title="Editar">
                <i class="lni lni-pencil-alt" style="font-size:18px;color:#64748b;"></i>
              </button>
              <button class="border-0 bg-transparent d-none" id="btn-guardar" onclick="guardarEdicion()" title="Guardar cambios">
                <i class="lni lni-checkmark" style="font-size:18px;color:#3b82f6;"></i>
              </button>
              <button class="border-0 bg-transparent d-none" id="btn-cancelar-edicion" onclick="cancelarEdicion()" title="Cancelar">
                <i class="lni lni-close" style="font-size:18px;color:#94a3b8;"></i>
              </button>
            </div>
          </div>

          
          <div id="campos-cliente" style="border-top:1px solid #f1f5f9;padding-top:20px;">

            <?php $__currentLoopData = [
  ['label'=>'Nombre',    'id'=>'nombre',    'valor'=>$cliente->nombre,    'placeholder'=>'Nombre'],
  ['label'=>'Teléfono',  'id'=>'telefono',  'valor'=>$cliente->telefono,  'placeholder'=>'Teléfono'],
  ['label'=>'NIT / CI',  'id'=>'nit',       'valor'=>$cliente->nit,       'placeholder'=>'NIT'],
  ['label'=>'Email',     'id'=>'email',     'valor'=>$cliente->email,     'placeholder'=>'Email'],
  ['label'=>'Dirección', 'id'=>'direccion', 'valor'=>$cliente->direccion, 'placeholder'=>'Dirección'],
]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="d-flex justify-content-between align-items-center mb-15">
  <span class="text-sm text-gray"><?php echo e($campo['label']); ?></span>
  <div style="display:flex;flex-direction:column;align-items:flex-end;">
    <span class="text-sm fw-semibold campo-ver" id="ver-<?php echo e($campo['id']); ?>"><?php echo e($campo['valor'] ?? '—'); ?></span>
    <input class="campo-editar" id="edit-<?php echo e($campo['id']); ?>"
           value="<?php echo e($campo['valor'] ?? ''); ?>"
           placeholder="<?php echo e($campo['placeholder']); ?>" />
    <?php if(in_array($campo['id'], ['telefono', 'nit'])): ?>
      <span id="error-edit-<?php echo e($campo['id']); ?>" style="font-size:11px;color:#ef4444;display:none;"></span>
    <?php endif; ?>
  </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<div class="mb-15">
  <div class="d-flex justify-content-between align-items-center">
    <span class="text-sm text-gray">Cupo crédito</span>
    <span class="text-sm fw-semibold campo-ver" id="ver-cupo">
  <?php if(is_null($cliente->cupo_credito)): ?> Sin crédito
  <?php elseif($cliente->cupo_credito === -1): ?> Sin límite
  <?php else: ?> $<?php echo e(number_format($cliente->cupo_credito, 0, ',', '.')); ?>

  <?php endif; ?>
</span>
  </div>

  
<div id="editor-cupo" style="display:none; margin-top:10px;">

  
  <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:8px;">
    <span style="font-size:13px;font-weight:600;color:#334155;">Permitir crédito</span>
    <label style="position:relative;display:inline-block;width:40px;height:22px;cursor:pointer;">
      <input type="checkbox" id="edit-credito-toggle" onchange="toggleCupoEdicion()" style="opacity:0;width:0;height:0;">
      <span id="edit-credito-slider" style="position:absolute;inset:0;background:#e2e8f0;border-radius:24px;transition:0.3s;"></span>
      <span id="edit-credito-dot" style="position:absolute;left:3px;top:2px;width:18px;height:18px;background:white;border-radius:50%;transition:0.3s;box-shadow:0 1px 3px rgba(0,0,0,0.2);"></span>
    </label>
  </div>

  
<div id="edit-cupo-wrapper" style="display:none;flex-direction:column;gap:8px;">
  <div style="display:flex;gap:16px;padding:4px 0;">
    <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:500;color:#334155;cursor:pointer;">
      <input type="radio" name="edit-cupo-tipo" id="radio-ilimitado" onchange="seleccionarCupoEdicion('ilimitado')" style="accent-color:#2478ff;width:15px;height:15px;">
      Sin límite
    </label>
    <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:500;color:#334155;cursor:pointer;">
      <input type="radio" name="edit-cupo-tipo" id="radio-limitado" onchange="seleccionarCupoEdicion('limitado')" style="accent-color:#2478ff;width:15px;height:15px;">
      Con límite
    </label>
  </div>
  <div id="edit-cupo-monto" style="display:none;align-items:center;border-bottom:2px solid #f1f5f9;transition:0.3s;" onfocusin="this.style.borderColor='#2478ff'" onfocusout="this.style.borderColor='#f1f5f9'">
    <span style="color:#94a3b8;font-weight:600;padding-bottom:6px;">$</span>
    <input type="text" id="edit-cupo" placeholder="0" oninput="formatCOP(this)"
           style="width:100%;padding:6px 0 6px 6px;border:none;font-size:1rem;font-weight:700;color:#0f172a;outline:none;">
  </div>
</div>

</div>
</div>

            <div class="text-danger text-xs mt-1 d-none" id="error-edicion"></div>
          </div>
          <br>
          
        </div>
      </div> 

      
      <div class="col-lg-8">

        
        <div class="card-style mb-30 no-print">
          <h6 class="mb-20 fw-semibold">Ventas a crédito</h6>

          <?php
            $ventasCredito = $cliente->ventas()
              ->whereIn('estado', ['credito', 'parcial'])
              ->orderByDesc('fecha')
              ->get();
          ?>

          <?php if($ventasCredito->isEmpty()): ?>
            <div class="text-center py-4 text-gray">
              <i class="lni lni-checkmark-circle" style="font-size:32px;display:block;margin-bottom:8px;color:#16a34a;"></i>
              Este cliente no tiene deudas pendientes
            </div>
          <?php else: ?>
          <div class="table-wrapper table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th><h6>Fecha</h6></th>
                  <th><h6>Total venta</h6></th>
                  <th><h6>Saldo pendiente</h6></th>
                  <th><h6>Estado</h6></th>
                  <th><h6></h6></th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $ventasCredito; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td class="min-width">
                    <p class="text-sm mb-0"><?php echo e(\Carbon\Carbon::parse($venta->fecha)->format('d/m/Y')); ?></p>
                  </td>
                  <td class="min-width">
                    <p class="text-sm mb-0">$<?php echo e(number_format($venta->total, 0, ',', '.')); ?></p>
                  </td>
                  <td class="min-width">
                    <span style="color:#dc2626;font-weight:600;">$<?php echo e(number_format($venta->saldo_pendiente, 0, ',', '.')); ?></span>
                  </td>
                  <td class="min-width">
                    <?php if($venta->estado === 'credito'): ?>
                      <span class="status-btn close-btn" >Crédito</span>
                    <?php else: ?>
                      <span class="status-btn primary-btn-light">Parcial</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="dropdown-tres-puntos">
                      <button class="border-0 bg-transparent px-2" onclick="toggleMenu(this)" style="font-size:20px;color:#94a3b8;cursor:pointer;line-height:1;">
                        <i class="lni lni-more-alt"></i>
                      </button>
                      <div class="menu">
                        <button onclick="cerrarMenus(); abrirModalAbono(<?php echo e($venta->id); ?>, <?php echo e($venta->saldo_pendiente); ?>)">
                          <i class="lni lni-plus" style="color:#3b82f6;"></i> Abonar
                        </button>
                        <a href="<?php echo e(route('ventas.detalle', $venta->id)); ?>" onclick="cerrarMenus()">
                          <i class="lni lni-eye" style="color:#64748b;"></i> Ver venta
                        </a>
                      </div>
                    </div>
                  </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>



<div class="card-style mb-30">
  
  
  <div class="mb-12">
    <h6 class="fw-semibold mb-0">Historial de abonos</h6>
    <p class="text-xs text-gray mb-0" style="display:none;" id="print-subtitle"><?php echo e($cliente->nombre); ?> · Impreso el <?php echo e(now()->format('d/m/Y')); ?></p>
  </div>

  
  <div class="title d-flex align-items-center justify-content-between mb-20" style="gap: 12px;">
    
    <div class="input-group input-group-sm search-pos" style="width:240px;">
      <span class="input-group-text bg-light border-0">
        <i class="lni lni-search-alt"></i>
      </span>
      <input
        type="text"
        id="buscador-abonos"
        class="form-control bg-light border-0"
        placeholder="Buscar por observacion..."
        autocomplete="off"
      />
    </div>

    
    <div style="display:flex;gap:10px;align-items:center;">
  <input 
    type="date" 
    id="fecha-desde-abonos"
    class="form-control form-control-sm"
    style="width:140px;padding:10px 12px;font-size:0.875rem;border:none;background:transparent;cursor:pointer;color:#334155;font-weight:500;"
  />
  <span style="color:#cbd5e1;font-weight:600;user-select:none;">—</span>
  <input 
    type="date" 
    id="fecha-hasta-abonos"
    class="form-control form-control-sm"
    style="width:140px;padding:10px 12px;font-size:0.875rem;border:none;background:transparent;cursor:pointer;color:#334155;font-weight:500;"
  />
</div>

    
    <button class="border-0 bg-transparent no-print" onclick="imprimirAbonos()" title="Imprimir historial" style="color:#64748b;cursor:pointer;">
      <i class="lni lni-printer" style="font-size:20px;"></i>
    </button>

  </div>

  
  <div class="table-wrapper table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th><h6>Fecha</h6></th>
          <th><h6>Monto</h6></th>
          <th><h6>Forma de pago</h6></th>
          <th><h6>Observación</h6></th>
        </tr>
      </thead>
      <tbody id="tbody-abonos">
        <?php echo $__env->make('clientes._table-abonos', ['abonos' => $abonos ?? []], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      </tbody>
    </table>
  </div>


<div id="paginacion-abonos" class="pagination">
    <button id="btn-prev-abonos" onclick="cambiarPaginaAbonos('prev')" disabled>
        <i class="lni lni-chevron-left"></i>
    </button>
    <span class="text-sm">Página <strong id="current-page-abonos">1</strong> de <strong id="last-page-abonos">1</strong></span>
    <button id="btn-next-abonos" onclick="cambiarPaginaAbonos('next')" disabled>
        <i class="lni lni-chevron-right"></i>
    </button>
</div>
</div>

      </div>
    </div>
  </div>
</section>



<div class="modal fade" id="modal-abono" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
    <div class="modal-content" style="border-radius:24px;overflow:hidden;border:1px solid #f1f5f9;position:relative;">

      
      <div id="abono-estado-formulario">
        <input type="hidden" id="abono-venta-id" />
        <button type="button" onclick="modalAbono.hide()" style="position:absolute;top:16px;right:16px;background:#f8fafc;border:1px solid #e2e8f0;cursor:pointer;width:36px;height:36px;border-radius:10px;color:#94a3b8;display:flex;align-items:center;justify-content:center;z-index:10;">
          <i class="lni lni-close" style="font-size:12px;"></i>
        </button>

        <div style="display:flex;min-height:360px;">

          
          <div style="flex:1.2;padding:32px 36px;border-right:1px solid #f1f5f9;">
            <span style="background:#eff6ff;color:#3b82f6;padding:4px 10px;border-radius:8px;font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;">Abono</span>
            <h3 style="margin:10px 0 4px;font-size:1.4rem;font-weight:800;color:#0f172a;">Registrar Abono</h3>
            <p style="font-size:0.875rem;color:#94a3b8;margin:0 0 24px;">Ingresa los datos del abono.</p>

            <div style="display:flex;flex-direction:column;gap:20px;">
              <div>
                <label style="font-size:0.7rem;font-weight:700;color:#0f172a;text-transform:uppercase;display:block;margin-bottom:8px;">Monto a abonar <span style="color:#ef4444;">*</span></label>
                <div style="display:flex;align-items:center;border-bottom:2px solid #f1f5f9;transition:0.3s;" onfocusin="this.style.borderColor='#2478ff'" onfocusout="this.style.borderColor='#f1f5f9'">
                  <span style="color:#94a3b8;font-weight:600;padding-bottom:8px;">$</span>
                  <input type="text" id="abono-monto" placeholder="0" oninput="formatCOP(this); validarMontoAbono()"
                         style="width:100%;padding:8px 0 8px 6px;border:none;font-size:1.1rem;font-weight:700;color:#0f172a;outline:none;">
                </div>
                <div class="text-danger" id="error-abono-monto" style="font-size:12px;min-height:16px;margin-top:4px;"></div>
              </div>

              <div>
  <label style="font-size:0.7rem;font-weight:700;color:#0f172a;text-transform:uppercase;display:block;margin-bottom:8px;">
    Observación
    
  </label>
  <input type="text" id="abono-observacion" placeholder="Opcional" maxlength="50"
         oninput="document.getElementById('contador-obs').textContent=this.value.length+'/50';document.getElementById('contador-obs').style.color=this.value.length>=50?'#dc2626':'#94a3b8';"
         style="width:100%;padding:10px 0;border:none;border-bottom:2px solid #f1f5f9;font-size:1rem;color:#0f172a;outline:none;transition:0.3s;"
         onfocus="this.style.borderColor='#2478ff'" onblur="this.style.borderColor='#f1f5f9'"> 
         <span id="contador-obs" style="float:right;font-weight:400;color:#94a3b8;font-size:0.775rem;">0/50</span>
</div>
            </div>
          </div>

          
          <div style="flex:1;background:#f8fafc;padding:32px 28px;display:flex;flex-direction:column;justify-content:space-between;">
            <div style="display:flex;flex-direction:column;gap:16px;">

              
              <div style="background:white;padding:16px;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                <span style="display:block;font-size:0.65rem;font-weight:800;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Saldo pendiente</span>
                <span id="abono-saldo-display" style="font-size:1.4rem;font-weight:800;color:#dc2626;"></span>
              </div>

              
              <div>
                <label style="font-size:0.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;display:block;margin-bottom:8px;">Forma de pago</label>
                <select id="abono-forma-pago"
                        style="width:100%;padding:10px 12px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:0.9rem;color:#0f172a;outline:none;background:white;">
                  <option value="efectivo">Efectivo</option>
                  <option value="transferencia">Transferencia</option>
                  <option value="tarjeta">Tarjeta</option>
                </select>
              </div>

            </div>

            
            <div style="display:flex;flex-direction:column;gap:10px;margin-top:20px;">
              <button type="button" id="btn-guardar-abono" onclick="guardarAbono()"
                      style="width:100%;height:52px;border-radius:14px;border:none;background:#2478ff;color:#ffffff;font-size:1rem;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(36,120,255,0.2);">
                <span id="btn-abono-text">Registrar abono</span>
              </button>
              <button type="button" onclick="modalAbono.hide()"
                      style="background:none;border:none;color:#94a3b8;font-weight:600;font-size:0.875rem;cursor:pointer;padding:6px;">
                Cancelar
              </button>
            </div>
          </div>

        </div>
      </div>

      
      <div id="abono-estado-exito" style="display:none;">
        <div style="padding:40px 32px;text-align:center;">
          <div style="font-size:56px;color:#16a34a;margin-bottom:16px;"><i class="lni lni-checkmark-circle"></i></div>
          <h5 style="font-weight:700;margin-bottom:8px;">¡Abono registrado!</h5>
          <p style="font-size:0.9rem;color:#64748b;">Se registró un abono de <strong id="abono-monto-exito" style="color:#16a34a;"></strong></p>
        </div>
        <div style="padding:0 32px 32px;display:flex;gap:12px;">
          <button type="button" class="main-btn light-btn btn-hover flex-fill " onclick="cerrarModalAbono()">
            Cerrar
          </button>
          <button type="button" class="main-btn primary-btn btn-hover flex-fill" onclick="imprimirAbono()">
            <i class="lni lni-printer me-2"></i> Imprimir Comprobante 
          </button>
        </div>
      </div>

      
      <div id="abono-estado-error" style="display:none;">
        <div style="padding:40px 32px;text-align:center;">
          <div style="font-size:48px;color:#dc2626;margin-bottom:16px;"><i class="lni lni-cross-circle"></i></div>
          <h6 style="font-weight:700;margin-bottom:8px;">Algo salió mal</h6>
          <p style="font-size:0.9rem;color:#64748b;" id="abono-error-msg"></p>
        </div>
        <div style="padding:0 32px 32px;display:flex;gap:12px;">
          <button type="button" class="main-btn light-btn btn-hover flex-fill" onclick="modalAbono.hide()">Cancelar</button>
          <button type="button" class="main-btn primary-btn btn-hover flex-fill" onclick="abonoEstado('formulario')">
            <i class="lni lni-reload me-2"></i> Intentar de nuevo
          </button>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const clienteId = <?php echo e($cliente->id); ?>;
let modalAbono;
let abonoSaldoPendiente = 0;

// ===== FORMATO PESO COLOMBIANO =====
function formatCOP(input) {
    const raw = input.value.replace(/\./g, '').replace(/[^0-9]/g, '');
    if (raw === '') { input.value = ''; return; }
    input.value = parseInt(raw).toLocaleString('es-CO').replace(/,/g, '.');
}
function parseCOP(str) {
    return parseInt(String(str).replace(/\./g, '').replace(/[^0-9]/g, '') || '0');
}

// ===== DEBOUNCE =====
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

// ===== DROPDOWN TRES PUNTOS =====
function toggleMenu(btn) {
    const menu = btn.nextElementSibling;
    const estaAbierto = menu.classList.contains('abierto');
    cerrarMenus();
    if (!estaAbierto) menu.classList.add('abierto');
}
function cerrarMenus() {
    document.querySelectorAll('.dropdown-tres-puntos .menu').forEach(m => m.classList.remove('abierto'));
}

// ===== EDICIÓN INLINE =====
function activarEdicion() {
    document.getElementById('campos-cliente').classList.add('modo-edicion');
    document.getElementById('btn-editar').classList.add('d-none');
    document.getElementById('btn-guardar').classList.remove('d-none');
    document.getElementById('btn-cancelar-edicion').classList.remove('d-none');
    document.getElementById('edit-nombre').focus();
    document.getElementById('editor-cupo').style.display = 'block';
    const cupoActual = <?php echo e($cliente->cupo_credito ?? 'null'); ?>;
    const toggle = document.getElementById('edit-credito-toggle');
    if (cupoActual === null) {
        toggle.checked = false;
        toggleCupoEdicion();
    } else {
        toggle.checked = true;
        toggleCupoEdicion();
        if (cupoActual === -1) seleccionarCupoEdicion('ilimitado');
        else {
            seleccionarCupoEdicion('limitado');
            document.getElementById('edit-cupo').value = cupoActual.toLocaleString('es-CO').replace(/,/g, '.');
        }
    }
}
function cancelarEdicion() {
    document.getElementById('campos-cliente').classList.remove('modo-edicion');
    document.getElementById('btn-editar').classList.remove('d-none');
    document.getElementById('btn-guardar').classList.add('d-none');
    document.getElementById('btn-cancelar-edicion').classList.add('d-none');
    document.getElementById('error-edicion').classList.add('d-none');
    document.getElementById('editor-cupo').style.display = 'none';
    document.getElementById('error-edit-telefono').style.display = 'none';
    document.getElementById('error-edit-nit').style.display = 'none';
}
function toggleCupoEdicion() {
    const toggle  = document.getElementById('edit-credito-toggle');
    const slider  = document.getElementById('edit-credito-slider');
    const dot     = document.getElementById('edit-credito-dot');
    const wrapper = document.getElementById('edit-cupo-wrapper');
    if (toggle.checked) {
        slider.style.background = '#2478ff';
        dot.style.left = '20px';
        wrapper.style.display = 'flex';
        seleccionarCupoEdicion('ilimitado');
    } else {
        slider.style.background = '#e2e8f0';
        dot.style.left = '3px';
        wrapper.style.display = 'none';
    }
}
function seleccionarCupoEdicion(tipo) {
    const montoDiv = document.getElementById('edit-cupo-monto');
    document.getElementById('radio-ilimitado').checked = tipo === 'ilimitado';
    document.getElementById('radio-limitado').checked  = tipo === 'limitado';
    montoDiv.style.display = tipo === 'limitado' ? 'flex' : 'none';
    if (tipo === 'limitado') setTimeout(() => document.getElementById('edit-cupo').focus(), 50);
}
async function guardarEdicion() {
    const nombre = document.getElementById('edit-nombre').value.trim();
    if (!nombre) {
        const err = document.getElementById('error-edicion');
        err.textContent = 'El nombre es obligatorio.';
        err.classList.remove('d-none');
        return;
    }
    const payload = {
        nombre,
        telefono:     document.getElementById('edit-telefono').value.trim() || null,
        nit:          document.getElementById('edit-nit').value.trim() || null,
        email:        document.getElementById('edit-email').value.trim() || null,
        direccion:    document.getElementById('edit-direccion').value.trim() || null,
        cupo_credito: (() => {
            const toggle = document.getElementById('edit-credito-toggle');
            if (!toggle.checked) return null;
            if (document.getElementById('radio-ilimitado').checked) return -1;
            return parseCOP(document.getElementById('edit-cupo').value) || 0;
        })(),
    };
    try {
        const res  = await fetch(`/clientes/${clienteId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('display-nombre').textContent = nombre;
            document.getElementById('ver-nombre').textContent     = nombre;
            document.getElementById('ver-telefono').textContent   = payload.telefono || '—';
            document.getElementById('ver-nit').textContent        = payload.nit || '—';
            document.getElementById('ver-email').textContent      = payload.email || '—';
            document.getElementById('ver-direccion').textContent  = payload.direccion || '—';
            const cupoVal = payload.cupo_credito;
            document.getElementById('ver-cupo').textContent =
                cupoVal === null ? 'Sin crédito' :
                cupoVal === -1   ? 'Sin límite' :
                '$' + cupoVal.toLocaleString('es-CO').replace(/,/g, '.');
            cancelarEdicion();
        } else {
            document.getElementById('error-edicion').textContent = data.errors?.nombre?.[0] || '';
            document.getElementById('error-edicion').classList.toggle('d-none', !data.errors?.nombre);
            const errTel = document.getElementById('error-edit-telefono');
            const errNit = document.getElementById('error-edit-nit');
            errTel.textContent = data.errors?.telefono?.[0] || '';
            errTel.style.display = data.errors?.telefono ? 'block' : 'none';
            errNit.textContent = data.errors?.nit?.[0] || '';
            errNit.style.display = data.errors?.nit ? 'block' : 'none';
        }
    } catch(e) {
        const err = document.getElementById('error-edicion');
        err.textContent = 'Error de conexión.';
        err.classList.remove('d-none');
    }
}

// ===== ABONOS =====
async function cargarAbonos(page = 1) {
    const search     = document.getElementById('buscador-abonos').value.trim();
    const fechaDesde = document.getElementById('fecha-desde-abonos').value;
    const fechaHasta = document.getElementById('fecha-hasta-abonos').value;

    const url = new URL(`<?php echo e(route('clientes.listarAbonos', $cliente->id)); ?>`, window.location.origin);
    url.searchParams.append('page', page);
    if (search)     url.searchParams.append('search', search);
    if (fechaDesde) url.searchParams.append('fecha_desde', fechaDesde);
    if (fechaHasta) url.searchParams.append('fecha_hasta', fechaHasta);

    const tbody   = document.getElementById('tbody-abonos');
    const prevHtml = tbody ? tbody.innerHTML : '';
    if (tbody) tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-gray">Cargando...</td></tr>';

    try {
        const res    = await fetch(url.toString(), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }});
        const result = await res.json();
        if (!result.success) throw new Error();
        if (tbody) tbody.innerHTML = result.html;
        const { current_page, last_page } = result.pagination;
        document.getElementById('current-page-abonos').textContent = current_page;
        document.getElementById('last-page-abonos').textContent    = last_page;
        document.getElementById('btn-prev-abonos').disabled = current_page === 1;
        document.getElementById('btn-next-abonos').disabled = current_page === last_page;
    } catch (e) {
        console.error(e);
        if (tbody) tbody.innerHTML = prevHtml;
    }
}
function cambiarPaginaAbonos(direccion) {
    const current = parseInt(document.getElementById('current-page-abonos').textContent);
    const last    = parseInt(document.getElementById('last-page-abonos').textContent);
    const nueva   = direccion === 'prev' ? current - 1 : current + 1;
    if (nueva < 1 || nueva > last) return;
    cargarAbonos(nueva);
}

// ===== MODAL ABONO =====
function abrirModalAbono(ventaId, saldoPendiente) {
    abonoSaldoPendiente = parseInt(saldoPendiente);
    document.getElementById('abono-venta-id').value = ventaId;
    document.getElementById('abono-saldo-display').textContent =
        '$' + parseInt(saldoPendiente).toLocaleString('es-CO').replace(/,/g, '.');
    document.getElementById('abono-monto').value      = '';
    document.getElementById('abono-observacion').value = '';
    document.getElementById('error-abono-monto').textContent = '';
    document.getElementById('abono-forma-pago').value = 'efectivo';
    abonoEstado('formulario');
    modalAbono.show();
}
function validarMontoAbono() {
    const monto = parseCOP(document.getElementById('abono-monto').value);
    const err   = document.getElementById('error-abono-monto');
    const btn   = document.getElementById('btn-guardar-abono');
    if (monto > abonoSaldoPendiente) {
        err.textContent  = `El monto supera el saldo pendiente de $${abonoSaldoPendiente.toLocaleString('es-CO').replace(/,/g, '.')}.`;
        btn.disabled     = true;
        btn.style.cursor = 'not-allowed';
        btn.style.opacity = '0.5';
    } else {
        err.textContent  = '';
        btn.disabled     = false;
        btn.style.cursor = 'pointer';
        btn.style.opacity = '1';
    }
}
async function guardarAbono() {
    const monto = parseCOP(document.getElementById('abono-monto').value);
    document.getElementById('error-abono-monto').textContent = '';
    if (!monto || monto <= 0) {
        document.getElementById('error-abono-monto').textContent = 'Ingresa un monto válido.';
        return;
    }
    const btn = document.getElementById('btn-abono-text');
    btn.textContent = 'Guardando...';
    try {
        const res  = await fetch(`/clientes/${clienteId}/abonar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                venta_id:    document.getElementById('abono-venta-id').value,
                monto,
                forma_pago:  document.getElementById('abono-forma-pago').value,
                observacion: document.getElementById('abono-observacion').value || null,
            })
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('abono-monto-exito').textContent =
                '$' + monto.toLocaleString('es-CO').replace(/,/g, '.');
            window.ultimoComprobanteAbono = data.comprobante;
            abonoEstado('exito');
        } else {
            document.getElementById('abono-error-msg').textContent = data.message || 'Error al registrar el abono.';
            abonoEstado('error');
        }
    } catch(e) {
        document.getElementById('abono-error-msg').textContent = 'Error de conexión.';
        abonoEstado('error');
    } finally {
        btn.textContent = 'Registrar abono';
    }
}
function abonoEstado(estado) {
    ['formulario','exito','error'].forEach(e => {
        document.getElementById(`abono-estado-${e}`).style.display = 'none';
    });
    document.getElementById(`abono-estado-${estado}`).style.display = 'block';
}
function cerrarModalAbono() {
    modalAbono.hide();
    window.location.reload();
}
function imprimirAbono() {
    const c = window.ultimoComprobanteAbono;
    if (!c) return;
    const btn = document.querySelector('button[onclick="imprimirAbono()"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Cargando...'; }
    const iframe = document.createElement('iframe');
    iframe.style.cssText = 'display:none;position:absolute;width:0;height:0;border:none;';
    iframe.src = `/clientes/${clienteId}/abonos/${c.abono_id}/comprobante`;
    iframe.onload = function() {
        setTimeout(() => {
            try { iframe.contentWindow.print(); } catch(e) { console.error(e); }
            setTimeout(() => {
                document.body.removeChild(iframe);
                if (btn) { btn.disabled = false; btn.textContent = 'Imprimir Comprobante'; }
            }, 100);
        }, 300);
    };
    iframe.onerror = function() {
        document.body.removeChild(iframe);
        if (btn) { btn.disabled = false; btn.textContent = 'Imprimir Comprobante'; }
    };
    document.body.appendChild(iframe);
}
function imprimirAbonos() {
  const iframe = document.createElement('iframe');
  iframe.style.display = 'none';
  iframe.style.position = 'absolute';
  iframe.src = `<?php echo e(route('clientes.printAbonos', $cliente->id)); ?>`;
  
  iframe.onload = function() {
    setTimeout(() => {
      iframe.contentWindow.print();
    }, 300);
  };
  
  document.body.appendChild(iframe);
  setTimeout(() => document.body.removeChild(iframe), 5000);
}

// ===== INIT =====
document.addEventListener('DOMContentLoaded', function () {
    modalAbono = new bootstrap.Modal(document.getElementById('modal-abono'));

    cargarAbonos(1);

    const busquedaDebounced = debounce(() => cargarAbonos(1), 300);
    document.getElementById('buscador-abonos').addEventListener('input', busquedaDebounced);
    document.getElementById('buscador-abonos').addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { this.value = ''; cargarAbonos(1); this.blur(); }
    });
    document.getElementById('fecha-desde-abonos').addEventListener('change', () => cargarAbonos(1));
    document.getElementById('fecha-hasta-abonos').addEventListener('change', () => cargarAbonos(1));

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-tres-puntos')) cerrarMenus();
    });
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OptenAdvance\app\www\resources\views\clientes\show.blade.php ENDPATH**/ ?>