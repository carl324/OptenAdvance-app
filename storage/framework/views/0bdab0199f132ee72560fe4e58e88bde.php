<?php $__empty_1 = true; $__currentLoopData = $abonos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $abono): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

<tr>
  <td class="min-width">
    <p class="text-sm mb-0"><?php echo e($abono->created_at->format('d/m/Y H:i')); ?></p>
  </td>
  <td class="min-width">
    <span style="color:#16a34a;font-weight:600;">$<?php echo e(number_format($abono->monto, 0, ',', '.')); ?></span>
  </td>
  <td class="min-width">
    <p class="text-sm mb-0 text-capitalize"><?php echo e($abono->forma_pago); ?></p>
  </td>
  <td>
    <p class="text-sm mb-0 text-gray"><?php echo e($abono->observacion ?? '—'); ?></p>
  </td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr>
  <td colspan="4" class="text-center py-4 text-gray">
    <i class="lni lni-empty-file" style="font-size:32px;display:block;margin-bottom:8px;"></i>
    Sin abonos registrados
  </td>
</tr>
<?php endif; ?><?php /**PATH C:\OptenAdvance\app\www\resources\views\clientes\_table-abonos.blade.php ENDPATH**/ ?>