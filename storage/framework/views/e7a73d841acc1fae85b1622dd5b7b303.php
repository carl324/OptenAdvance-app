

<?php $__env->startSection('title', 'Notificaciones'); ?>

<?php $__env->startSection('content'); ?>

<div class="notification-wrapper">
    <div class="container-fluid">

        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                
            </div>
        </div>

        <div class="card-style mb-30">

            
            <div class="d-flex align-items-center justify-content-between mb-20">
                <div class="d-flex align-items-center gap-3">
                    
                    <?php if($notifications->count() > 0): ?>
                    <div class="form-check checkbox-style mb-0">
                        <input class="form-check-input" type="checkbox" id="select-all">
                        <label class="form-check-label text-sm text-gray" for="select-all">
                            Seleccionar todo
                        </label>
                    </div>
                    <?php endif; ?>
                    
                </div>

                
                <?php if($notifications->count() > 0): ?>
                <button id="btn-delete-selected" style="display:none; background:none; border:none; color:#dc2626; font-size:13px; font-weight:600; cursor:pointer; padding: 6px 12px; border-radius:6px; transition: background 0.2s;" 
                    onmouseover="this.style.background='#fee2e2'" 
                    onmouseout="this.style.background='none'">
                    <i class="lni lni-trash-can me-1"></i> Eliminar seleccionadas
                </button>
                <?php endif; ?>
            </div>

            
            <div id="lista-notificaciones">
            <?php if($notifications->isEmpty()): ?>
                <div style="text-align: center; padding: 60px 0;">
                    <i class="lni lni-checkmark-circle" style="font-size: 48px; color: #10b981; display: block; margin-bottom: 12px;"></i>
                    <h6 style="color: #1e293b; font-size: 16px; margin-bottom: 6px;">Todo al día</h6>
                    <p class="text-sm text-gray">No tienes notificaciones pendientes</p>
                </div>

            
            <?php else: ?>
                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $bgClass = match($notification->tipo) {
                            'error'   => 'danger-bg',
                            'warning' => 'warning-bg',
                            'info'    => 'info-bg',
                            default   => 'success-bg',
                        };
                        $icono = match($notification->modulo) {
    'licencia' => 'lni lni-certificate',
    'backup'   => 'lni lni-database',
    'sistema'  => 'lni lni-warning',
    default    => 'lni lni-information',
};
                    ?>

                    <div class="single-notification <?php echo e($notification->leida ? 'readed' : ''); ?>" id="notification-<?php echo e($notification->id); ?>">
                        <div class="checkbox">
                            <div class="form-check checkbox-style mb-20">
                                <input class="form-check-input notification-checkbox" type="checkbox" value="<?php echo e($notification->id); ?>">
                            </div>
                        </div>
                        <div class="notification">
                            <div class="image <?php echo e($bgClass); ?>">
                                <i class="<?php echo e($icono); ?>" style="font-size: 20px;"></i>
                            </div>
                            <div class="content">
                                <h6><?php echo e($notification->titulo); ?></h6>
                                <p class="text-sm text-gray"><?php echo e($notification->mensaje); ?></p>
                                <span class="text-sm text-medium text-gray">
                                    <?php echo e($notification->created_at->diffForHumans()); ?>

                                </span>
                            </div>
                        </div>
                        <div class="action">
                            <button class="delete-btn" onclick="eliminarNotificacion(<?php echo e($notification->id); ?>)" title="Eliminar">
                                <i class="lni lni-trash-can"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
            </div>

<div class="d-flex justify-content-between align-items-center mt-20" id="paginacion-notificaciones">
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

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;
let paginaActual = 1;

function renderPaginacion(current, last, from, to, total) {
    document.getElementById('paginacion-info').textContent =
        total > 0 ? `Mostrando ${from}–${to} de ${total}` : '';
    document.getElementById('paginacion-paginas').textContent =
        total > 0 ? `Página ${current} de ${last}` : '';
    document.getElementById('btn-prev').disabled = current <= 1;
    document.getElementById('btn-next').disabled = current >= last;
    document.getElementById('paginacion-notificaciones').style.display = total > 0 ? 'flex' : 'none';
}

function renderLista(items) {
    const contenedor = document.getElementById('lista-notificaciones');

    if (!items.length) {
        contenedor.innerHTML = `
            <div style="text-align: center; padding: 60px 0;">
                <i class="lni lni-checkmark-circle" style="font-size: 48px; color: #10b981; display: block; margin-bottom: 12px;"></i>
                <h6 style="color: #1e293b; font-size: 16px; margin-bottom: 6px;">Todo al día</h6>
                <p class="text-sm text-gray">No tienes notificaciones pendientes</p>
            </div>`;
        return;
    }

    const bgMap   = { error: 'danger-bg', warning: 'warning-bg', info: 'info-bg' };
    const iconMap = { licencia: 'lni lni-certificate', backup: 'lni lni-database', sistema: 'lni lni-warning' };

    contenedor.innerHTML = items.map(n => `
        <div class="single-notification ${n.leida ? 'readed' : ''}" id="notification-${n.id}">
            <div class="checkbox">
                <div class="form-check checkbox-style mb-20">
                    <input class="form-check-input notification-checkbox" type="checkbox" value="${n.id}">
                </div>
            </div>
            <div class="notification">
                <div class="image ${bgMap[n.tipo] ?? 'success-bg'}">
                    <i class="${iconMap[n.modulo] ?? 'lni lni-information'}" style="font-size: 20px;"></i>
                </div>
                <div class="content">
                    <h6>${n.titulo}</h6>
                    <p class="text-sm text-gray">${n.mensaje}</p>
                    <span class="text-sm text-medium text-gray">${n.created_at_human ?? ''}</span>
                </div>
            </div>
            <div class="action">
                <button class="delete-btn" onclick="eliminarNotificacion(${n.id})" title="Eliminar">
                    <i class="lni lni-trash-can"></i>
                </button>
            </div>
        </div>
    `).join('');

    // Re-bind checkboxes
    document.querySelectorAll('.notification-checkbox').forEach(cb => {
        cb.addEventListener('change', actualizarBotonEliminar);
    });
}

function cargarPagina(pagina) {
    paginaActual = pagina;
    fetch(`<?php echo e(route('notifications.index')); ?>?page=${pagina}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        renderLista(data.data);
        renderPaginacion(data.current_page, data.last_page, data.from, data.to, data.total);
        actualizarBotonEliminar();
    })
    .catch(err => console.error('Error:', err));
}

window.cambiarPagina = function(dir) {
    const nueva = paginaActual + dir;
    if (nueva < 1) return;
    cargarPagina(nueva);
};

function actualizarBotonEliminar() {
    const seleccionadas = document.querySelectorAll('.notification-checkbox:checked').length;
    const btn = document.getElementById('btn-delete-selected');
    if (btn) btn.style.display = seleccionadas > 0 ? 'inline-block' : 'none';
}

document.getElementById('select-all')?.addEventListener('change', function() {
    document.querySelectorAll('.notification-checkbox').forEach(cb => cb.checked = this.checked);
    actualizarBotonEliminar();
});

document.getElementById('btn-delete-selected')?.addEventListener('click', function() {
    const ids = [...document.querySelectorAll('.notification-checkbox:checked')].map(cb => cb.value);
    if (!ids.length) return;

    fetch(`<?php echo e(route('notifications.destroyAll')); ?>`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
        body: JSON.stringify({ ids })
    })
    .then(r => r.json())
    .then(() => cargarPagina(paginaActual));
});

function eliminarNotificacion(id) {
    fetch(`/notifications/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('notification-' + id)?.remove();
            actualizarBotonEliminar();
            // Si la página quedó vacía, recargar la anterior
            if (!document.querySelector('.single-notification')) {
                cargarPagina(paginaActual > 1 ? paginaActual - 1 : 1);
            }
        }
    });
}

// Init paginación con datos del servidor
renderPaginacion(
    <?php echo e($notifications->currentPage()); ?>,
    <?php echo e($notifications->lastPage()); ?>,
    <?php echo e($notifications->firstItem() ?? 0); ?>,
    <?php echo e($notifications->lastItem() ?? 0); ?>,
    <?php echo e($notifications->total()); ?>

);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OptenAdvance\app\www\resources\views\notifications\index.blade.php ENDPATH**/ ?>