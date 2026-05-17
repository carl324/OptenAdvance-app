

<?php $__env->startSection('content'); ?>
<div class="invoice p-3">
	<div class="d-flex justify-content-between mb-3">
		<div>
			<strong><?php echo e(config('app.name', 'Mi Negocio')); ?></strong><br>
			Dirección: -<br>
			Tel: -
		</div>
		<div class="text-end">
			<strong>Factura:</strong> <?php echo e($factura->numero ?? '-'); ?><br>
			<small>Fecha: <?php echo e(optional($factura->fecha_emision)->format('Y-m-d H:i') ?? optional($venta->fecha)->format('Y-m-d H:i')); ?></small>
		</div>
	</div>

	<div class="mb-3">
		<strong>Cliente:</strong> <?php echo e($factura->cliente_nombre ?? $venta->cliente ?? '-'); ?><br>
		<strong>NIT:</strong> <?php echo e($factura->cliente_nit ?? '-'); ?>

	</div>

	<table class="table table-sm">
		<thead>
			<tr>
				<th>Producto</th>
				<th class="text-end">Cantidad</th>
				<th class="text-end">Precio</th>
				<th class="text-end">Subtotal</th>
			</tr>
		</thead>
		<tbody>
			<?php $__currentLoopData = $venta->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td><?php echo e(optional($d->producto)->nombre ?? 'Producto #' . $d->producto_id); ?></td>
				<td class="text-end"><?php echo e($d->cantidad); ?></td>
				<td class="text-end"><?php echo e(number_format($d->precio_unitario, 0, ',', '.')); ?></td>
				<td class="text-end"><?php echo e(number_format($d->subtotal, 0, ',', '.')); ?></td>
			</tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</tbody>
	</table>

	<div class="d-flex justify-content-end">
		<div class="w-50">
			<?php if($empresa && $empresa->cobra_iva): ?>
			<div class="d-flex justify-content-between">
				<div>IVA</div>
				<div><?php echo e(number_format($factura->impuestos ?? $venta->detalles->sum('iva'), 0, ',', '.')); ?></div>
			</div>
			<?php endif; ?>
			<div class="d-flex justify-content-between fw-bold mt-2">
				<div>Total</div>
				<div><?php echo e(number_format($factura->total ?? $venta->total, 0, ',', '.')); ?></div>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OptenAdvance\app\www\resources\views\ventas\show.blade.php ENDPATH**/ ?>