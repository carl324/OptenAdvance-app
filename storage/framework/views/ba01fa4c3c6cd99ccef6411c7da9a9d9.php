

<?php $__env->startSection('title', 'Auditoría'); ?>

<?php $__env->startSection('content'); ?>

<section class="section">
  <div class="container-fluid">

    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        
        <div class="col-md-6">
          
        </div>
      </div>
    </div>

    
    <div class="row">
      <div class="col-12">

<div class="row mb-30">
    <div class="col-12">
        <div class="card-style" style="padding: 20px 24px;">
            <div class="row g-2 align-items-end">

                <div class="col-lg-3 col-md-6">
                    <label class="text-xs text-gray mb-1 d-block">Desde</label>
                    <input type="date" id="filtro-desde" value="<?php echo e(request('desde')); ?>"
                        class="form-control filtro-auditoria"
                        style="height:38px; border-radius:6px; border:1px solid #e5e5e5; padding:0 12px; font-size:13px; width:100%; background:#fafafa;" />
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="text-xs text-gray mb-1 d-block">Hasta</label>
                    <input type="date" id="filtro-hasta" value="<?php echo e(request('hasta')); ?>"
                        class="form-control filtro-auditoria"
                        style="height:38px; border-radius:6px; border:1px solid #e5e5e5; padding:0 12px; font-size:13px; width:100%; background:#fafafa;" />
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="text-xs text-gray mb-1 d-block">Tipo de acción</label>
                    <select id="filtro-tipo" class="filtro-auditoria"
                        style="height:38px; border-radius:6px; border:1px solid #e5e5e5; padding:0 12px; font-size:13px; width:100%; background:#fafafa; color:#555;">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $tiposAccion; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(request('tipo_accion') == $key ? 'selected' : ''); ?>>
                                <?php echo e($label); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6 d-flex align-items-end">
                    <button id="btn-limpiar-filtros" type="button"
                        class="main-btn danger-btn btn-hover"
                        style="height:38px; padding:0 20px; font-size:13px; display:none;">
                        <i class="lni lni-close me-1"></i> Limpiar
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
      </div>
    </div>

    
    <div class="row">

      
      <div class="col-lg-4">
        <div class="card-style mb-30">
          <div class="title mb-20">
            <h6 class="text-medium">Actividad Reciente</h6>
            <p class="text-sm text-gray mt-1">Últimos eventos</p>
          </div>
          <div class="audit-timeline">
            <?php $__empty_1 = true; $__currentLoopData = $registros->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <?php
                $dot = match($r->tipo_accion) {
                  'apertura_caja', 'cierre_caja' => 'success',
                  'anulacion_venta'              => 'warning',
                  'eliminacion_producto',
                  'cambio_precio_producto'       => 'danger',
                  default                        => 'primary',
                };
              ?>
              <div class="audit-timeline-item">
                <div class="audit-timeline-dot <?php echo e($dot); ?>"></div>
                <div class="audit-timeline-content">
                  <p class="text-sm text-bold"><?php echo e($tiposAccion[$r->tipo_accion] ?? $r->tipo_accion); ?></p>
                  <p class="text-sm text-gray"><?php echo e($r->usuario_nombre ?? 'Sistema'); ?></p>
                  <span class="text-xs text-gray">
                    <?php echo e(\Carbon\Carbon::parse($r->created_at)->format('d/m H:i')); ?>

                  </span>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <p class="text-sm text-gray">Sin actividad registrada.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      
      <div class="col-lg-8">
        <div class="card-style mb-30">
          <div class="title d-flex flex-wrap align-items-center justify-content-between mb-20">
            <div class="left">
              <h6 class="text-medium">Registro de Eventos</h6>
              <p class="text-sm text-gray mt-1"><?php echo e($registros->total()); ?> registros en total</p>
            </div>
            <div class="right">
             
            </div>
          </div>

          <div class="table-responsive">
            <table class="table top-selling-table">
              <thead>
    <tr>
        <th><h6 class="text-sm text-medium">Fecha / Hora</h6></th>
        <th><h6 class="text-sm text-medium">Usuario</h6></th>
        <th><h6 class="text-sm text-medium">Detalle</h6></th>
    </tr>
</thead>
<tbody>
<?php $__empty_1 = true; $__currentLoopData = $registros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr>
    <td>
        <p class="text-sm"><?php echo e(\Carbon\Carbon::parse($r->created_at)->format('d/m/Y')); ?></p>
        <p class="text-xs text-gray"><?php echo e(\Carbon\Carbon::parse($r->created_at)->format('H:i')); ?></p>
    </td>
    <td>
        <p class="text-sm"><?php echo e($r->usuario_nombre ?? '—'); ?></p>
        <p class="text-xs text-gray"><?php echo e($r->usuario_email ?? ''); ?></p>
    </td>
    <td><p class="text-sm"><?php echo e($r->descripcion); ?></p></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr>
    <td colspan="3" class="text-center">
        <p class="text-sm text-gray py-3">No hay registros con los filtros aplicados.</p>
    </td>
</tr>
<?php endif; ?>
              </tbody>
            </table>
          </div>

          
          <div class="d-flex justify-content-between align-items-center mt-20" id="paginacion-auditoria">
    <p class="text-sm text-gray" id="paginacion-info"></p>
    <div class="d-flex align-items-center gap-2">
        <button id="btn-prev" onclick="cambiarPagina(-1)"
            style="width:32px;height:32px;border-radius:6px;border:1px solid #e5e5e5;background:#fafafa;cursor:pointer;display:flex;align-items:center;justify-content:center;">
            <i class="lni lni-chevron-left" style="font-size:14px;"></i>
        </button>
        <span id="paginacion-paginas" class="text-sm text-gray" style="min-width:70px;text-align:center;"></span>
        <button id="btn-next" onclick="cambiarPagina(1)"
            style="width:32px;height:32px;border-radius:6px;border:1px solid #e5e5e5;background:#fafafa;cursor:pointer;display:flex;align-items:center;justify-content:center;">
            <i class="lni lni-chevron-right" style="font-size:14px;"></i>
        </button>
    </div>
</div>

        </div>
      </div>

    </div>

  </div>
</section>

<style>
  .audit-timeline { position: relative; padding-left: 20px; }
  .audit-timeline::before { content:''; position:absolute; left:7px; top:0; bottom:0; width:2px; background:#f0f0f0; }
  .audit-timeline-item { display:flex; gap:14px; align-items:flex-start; margin-bottom:22px; position:relative; }
  .audit-timeline-dot { width:14px; height:14px; border-radius:50%; flex-shrink:0; margin-top:3px; position:relative; z-index:1; }
  .audit-timeline-dot.success { background:#22c55e; }
  .audit-timeline-dot.primary { background:#365CF5; }
  .audit-timeline-dot.warning { background:#f2994a; }
  .audit-timeline-dot.danger  { background:#eb5757; }
  .audit-timeline-content p { margin-bottom:2px; line-height:1.4; }
  .text-xs { font-size:11px; }
  .text-bold { font-weight:600; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const desde  = document.getElementById('filtro-desde');
    const hasta  = document.getElementById('filtro-hasta');
    const tipo   = document.getElementById('filtro-tipo');
    const btnLimpiar = document.getElementById('btn-limpiar-filtros');

    let timer = null;
    let paginaActual = 1;

    function hayFiltros() {
        return desde.value || hasta.value || tipo.value;
    }

    function toggleLimpiar() {
        btnLimpiar.style.display = hayFiltros() ? 'inline-flex' : 'none';
    }

function cargarResultados(pagina = 1) {
    paginaActual = pagina;
    const params = new URLSearchParams();
    if (desde.value) params.append('desde', desde.value);
    if (hasta.value) params.append('hasta', hasta.value);
    if (tipo.value)  params.append('tipo_accion', tipo.value);
    params.append('page', pagina);

    fetch(`<?php echo e(route('auditoria.index')); ?>?${params.toString()}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        toggleLimpiar();
        renderTabla(data.data);
        renderTimeline(data.data);
        renderPaginacion(data.current_page, data.last_page, data.from, data.to, data.total);
    })
    .catch(err => console.error('Error AJAX:', err));
}
function renderPaginacion(current, last, from, to, total) {
    document.getElementById('paginacion-info').textContent = 
        `Mostrando ${from}–${to} de ${total}`;
    document.getElementById('paginacion-paginas').textContent = 
        `Página ${current} de ${last}`;
    document.getElementById('btn-prev').disabled = current <= 1;
    document.getElementById('btn-next').disabled = current >= last;
}

window.cambiarPagina = function(direccion) {
    const ultima = parseInt(document.getElementById('btn-next').disabled ? paginaActual : paginaActual + 1);
    const nueva = paginaActual + direccion;
    if (nueva < 1) return;
    cargarResultados(nueva);
};
    function debounce(fn, ms) {
        clearTimeout(timer);
        timer = setTimeout(fn, ms);
    }

    [desde, hasta, tipo].forEach(el => {
        el.addEventListener('change', () => debounce(cargarResultados, 300));
    });

    btnLimpiar.addEventListener('click', function () {
        desde.value = '';
        hasta.value = '';
        tipo.value  = '';
        cargarResultados();
    });

function renderTabla(registros) {
    const tbody = document.querySelector('.top-selling-table tbody');

    if (!registros.length) {
        tbody.innerHTML = `<tr><td colspan="3" class="text-center">
            <p class="text-sm text-gray py-3">No hay registros con los filtros aplicados.</p>
        </td></tr>`;
        return;
    }

    tbody.innerHTML = registros.map(r => {
        const fecha = new Date(r.created_at);
        const dia   = fecha.toLocaleDateString('es-CO', {day:'2-digit', month:'2-digit', year:'numeric'});
        const hora  = fecha.toLocaleTimeString('es-CO', {hour:'2-digit', minute:'2-digit'});

        return `<tr>
            <td>
                <p class="text-sm">${dia}</p>
                <p class="text-xs text-gray">${hora}</p>
            </td>
            <td>
                <p class="text-sm">${r.usuario_nombre ?? '—'}</p>
                <p class="text-xs text-gray">${r.usuario_email ?? ''}</p>
            </td>
            <td><p class="text-sm">${r.descripcion ?? ''}</p></td>
        </tr>`;
    }).join('');
}

    function renderTimeline(registros) {
        const timeline = document.querySelector('.audit-timeline');
        const dotMap = {
            'apertura_caja': 'success', 'cierre_caja': 'success',
            'anulacion_venta': 'warning',
            'eliminacion_producto': 'danger', 'cambio_precio_producto': 'danger',
        };
        const tiposAccion = <?php echo json_encode($tiposAccion, 15, 512) ?>;
        const slice = registros.slice(0, 8);

        if (!slice.length) {
            timeline.innerHTML = `<p class="text-sm text-gray">Sin actividad registrada.</p>`;
            return;
        }

        timeline.innerHTML = slice.map(r => {
            const dot    = dotMap[r.tipo_accion] ?? 'primary';
            const accion = tiposAccion[r.tipo_accion] ?? r.tipo_accion;
            const fecha  = new Date(r.created_at);
            const label  = fecha.toLocaleDateString('es-CO', {day:'2-digit', month:'2-digit'})
                         + ' ' + fecha.toLocaleTimeString('es-CO', {hour:'2-digit', minute:'2-digit'});

            return `<div class="audit-timeline-item">
                <div class="audit-timeline-dot ${dot}"></div>
                <div class="audit-timeline-content">
                    <p class="text-sm text-bold">${accion}</p>
                    <p class="text-sm text-gray">${r.usuario_nombre ?? 'Sistema'}</p>
                    <span class="text-xs text-gray">${label}</span>
                </div>
            </div>`;
        }).join('');
    }
renderPaginacion(
    <?php echo e($registros->currentPage()); ?>,
    <?php echo e($registros->lastPage()); ?>,
    <?php echo e($registros->firstItem() ?? 0); ?>,
    <?php echo e($registros->lastItem() ?? 0); ?>,
    <?php echo e($registros->total()); ?>

);

});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OptenAdvance\app\www\resources\views\auditoria\index.blade.php ENDPATH**/ ?>