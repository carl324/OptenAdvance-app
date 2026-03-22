

<?php $__env->startSection('title', 'Auditoría'); ?>

<?php $__env->startSection('content'); ?>

<section class="section">
  <div class="container-fluid">

    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="title"><h2>Auditoría del Sistema</h2></div>
        </div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item active">Auditoría</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>

    
    <div class="row">
      <div class="col-12">
        <div class="card-style mb-30">
          <form method="GET" action="<?php echo e(route('auditoria.index')); ?>">
            <div class="row g-3 align-items-end">
              <div class="col-lg-3 col-md-6">
                <label class="text-sm text-gray mb-1 d-block">Desde</label>
                <input type="date" name="desde" value="<?php echo e(request('desde')); ?>"
                  class="form-control"
                  style="height:40px;border-radius:6px;border:1px solid #e5e5e5;padding:0 12px;font-size:13px;width:100%;" />
              </div>
              <div class="col-lg-3 col-md-6">
                <label class="text-sm text-gray mb-1 d-block">Hasta</label>
                <input type="date" name="hasta" value="<?php echo e(request('hasta')); ?>"
                  class="form-control"
                  style="height:40px;border-radius:6px;border:1px solid #e5e5e5;padding:0 12px;font-size:13px;width:100%;" />
              </div>
              <div class="col-lg-3 col-md-6">
                <label class="text-sm text-gray mb-1 d-block">Tipo de acción</label>
                <div class="select-style-1">
                  <div class="select-position">
                    <select name="tipo_accion" class="light-bg">
                      <option value="">Todos</option>
                      <?php $__currentLoopData = $tiposAccion; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(request('tipo_accion') == $key ? 'selected' : ''); ?>>
                          <?php echo e($label); ?>

                        </option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 d-flex gap-2">
                <button type="submit" class="main-btn primary-btn btn-hover" style="height:40px;flex:1;">
                  <i class="lni lni-search-alt me-1"></i> Filtrar
                </button>
                <?php if(request()->hasAny(['desde','hasta','tipo_accion','search'])): ?>
                  <a href="<?php echo e(route('auditoria.index')); ?>" class="main-btn danger-btn btn-hover" style="height:40px;padding:0 16px;display:flex;align-items:center;">
                    <i class="lni lni-close"></i>
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </form>
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
              
              <form method="GET" action="<?php echo e(route('auditoria.index')); ?>" class="d-flex align-items-center gap-2">
                
                <?php $__currentLoopData = request()->only(['desde','hasta','tipo_accion']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($val); ?>">
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="header-search" style="display:flex!important;">
                  <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Buscar usuario o detalle..." style="min-width:200px;" />
                  <button type="submit"><i class="lni lni-search-alt"></i></button>
                </div>
              </form>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table top-selling-table">
              <thead>
                <tr>
                  <th><h6 class="text-sm text-medium">Fecha / Hora</h6></th>
                  <th><h6 class="text-sm text-medium">Usuario</h6></th>
                  <th><h6 class="text-sm text-medium">Acción</h6></th>
                  <th><h6 class="text-sm text-medium">Detalle</h6></th>
                </tr>
              </thead>
              <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $registros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <?php
                    $badge = match($r->tipo_accion) {
                      'apertura_caja', 'cierre_caja' => 'success-btn',
                      'anulacion_venta'              => 'warning-btn',
                      'eliminacion_producto',
                      'cambio_precio_producto'       => 'close-btn',
                      default                        => 'primary-btn',
                    };
                  ?>
                  <tr>
                    <td>
                      <p class="text-sm"><?php echo e(\Carbon\Carbon::parse($r->created_at)->format('d/m/Y')); ?></p>
                      <p class="text-xs text-gray"><?php echo e(\Carbon\Carbon::parse($r->created_at)->format('H:i')); ?></p>
                    </td>
                    <td><p class="text-sm"><?php echo e($r->usuario_nombre ?? '—'); ?></p></td>
                    <td><span class="status-btn <?php echo e($badge); ?>"><?php echo e($tiposAccion[$r->tipo_accion] ?? $r->tipo_accion); ?></span></td>
                    <td><p class="text-sm"><?php echo e($r->descripcion); ?></p></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr>
                    <td colspan="4" class="text-center">
                      <p class="text-sm text-gray py-3">No hay registros con los filtros aplicados.</p>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          
          <?php if($registros->hasPages()): ?>
            <div class="d-flex justify-content-between align-items-center mt-20">
              <p class="text-sm text-gray">
                Mostrando <?php echo e($registros->firstItem()); ?>–<?php echo e($registros->lastItem()); ?> de <?php echo e($registros->total()); ?>

              </p>
              <?php echo e($registros->appends(request()->query())->links()); ?>

            </div>
          <?php endif; ?>

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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\optenadvance\app\www\resources\views/auditoria/index.blade.php ENDPATH**/ ?>