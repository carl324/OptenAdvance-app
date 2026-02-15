
<?php $__env->startSection('title', 'Detalles de venta'); ?>
<?php $__env->startSection('content'); ?>
<section class="section">
            <div class="container-fluid">
                <!-- ========== Header compacto ========== -->
                <!-- ========== Header compacto ========== -->
                <div class="title-wrapper pt-30">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="title mb-30">
                               
                                <div class="d-flex align-items-center gap-4 flex-wrap">
                                        <div class="d-flex align-items-center gap-2" style="background: white; padding: 8px 14px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                                        <i class="lni lni-calendar" style="color: #4A6CF7; font-size: 16px;"></i>
                                        <span class="text-sm fw-500" style="color: #364a63;"><?php echo e(\Carbon\Carbon::parse($venta->fecha)->locale('es')->translatedFormat('l d F Y, h:i a')); ?>

</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2" style="background: white; padding: 8px 14px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                                        <?php
                                            $formaPagoRaw = strtolower(trim((string) ($factura->forma_pago ?? $venta->forma_pago ?? '')));
                                            $iconClass = 'lni lni-wallet';
                                            if (strpos($formaPagoRaw, 'efectivo') !== false || $formaPagoRaw === 'efectivo') {
                                                $iconClass = 'lni lni-money-location';
                                            } elseif (strpos($formaPagoRaw, 'transferencia') !== false || $formaPagoRaw === 'transferencia') {
                                                $iconClass = 'lni lni-apartment';
                                            } elseif (strpos($formaPagoRaw, 'tarjeta') !== false || $formaPagoRaw === 'tarjeta') {
                                                $iconClass = 'lni lni-credit-cards';
                                            }
                                        ?>
                                        <i class="<?php echo e($iconClass); ?>" style="color: #0f9e5a; font-size: 16px;"></i>
                                        <span class="text-sm fw-500" style="color: #364a63;"><?php echo e($factura->forma_pago ?? $venta->forma_pago ?? '-'); ?></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2" >
                                        <span class="status-btn <?php echo e($venta->estado === 'completada' ? 'success-btn' : ($venta->estado === 'anulada' ? 'status-btn close-btn' : 'deactive-btn')); ?>"><?php echo e(ucwords($venta->estado ?? '-')); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                                <a href="<?php echo e(route('ventas.factura', $venta)); ?>" class="main-btn primary-btn btn-hover btn-sm">
                                    <i class="lni lni-printer"></i> Factura
                                </a>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <!-- ========== Productos ========== -->
                    <div class="col-lg-8">
                        <div class="card-style mb-30">
                            <div class="d-flex justify-content-between align-items-center mb-20">
                                <h6 class="mb-0"><?php echo e($venta->detalles->count()); ?>ㅤProductos Vendidos</h6>
                            </div>
                            <?php
                                $showIva = (
                                    ($empresa && $empresa->cobra_iva) ||
                                    $venta->detalles->contains(function ($d) { return (($d->iva ?? 0) > 0); })
                                );
                            ?>
                            <div class="table-wrapper table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><span class="text-sm">Producto</span></th>
                                            <th class="text-center"><span class="text-sm">Cantidad</span></th>
                                            <th><span class="text-sm">Precio</span></th>
                                            <?php if($showIva): ?>
                                            <th><span class="text-sm">IVA</span></th>
                                            <?php endif; ?>
                                            <th><span class="text-sm">Subtotal</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $venta->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <div>
                                                    <p class="text-sm fw-500 mb-0"><?php echo e(optional($d->producto)->nombre ?? 'Producto #' . $d->producto_id); ?></p>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="status-btn primary-btn-light"><?php echo e($d->cantidad); ?></span>
                                            </td>
                                            <td class="text-sm">
                                                <span class="text-sm">$<?php echo e(number_format($d->precio_unitario,0,',','.')); ?></span>
                                            </td>
                                            <?php if($showIva): ?>
                                            <td class="text-sm">
                                                <span class="text-sm text-gray">$<?php echo e(number_format($d->iva ?? 0,0,',','.')); ?></span>
                                            </td>
                                            <?php endif; ?>
                                            <td class="text-sm">
                                                <span class="text-sm fw-500">$<?php echo e(number_format($d->subtotal,0,',','.')); ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                     <!-- ========== Cliente ========== -->
                    <div class="col-lg-4">
                        <div class="card-style mb-30">
                            <div class="d-flex align-items-center mb-20">
                                <div class="icon-box me-3" style="width: 48px; height: 48px; background: #f3f6f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="lni lni-user" style="font-size: 24px; color: #4A6CF7;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1"><?php echo e($factura->cliente_nombre ?? $venta->cliente ?? '-'); ?></h6>
                                    <p class="text-sm text-gray mb-0"><?php echo e($factura->cliente_nit ?? '-'); ?></p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-20">
                                <div class="icon-box me-3" style="width: 48px; height: 48px; background: #f3f6f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="lni lni-briefcase" style="font-size: 24px; color: #4A6CF7;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1"><?php echo e($vendedorNombre ?? '-'); ?></h6>
                                    <p class="text-sm text-gray mb-0">Vendedor</p>
                                </div>
                            </div>
                        </div>

                        <!-- ========== Resumen compacto ========== -->
                        <div class="card-style mb-30">
                            <h6 class="mb-20">Resumen</h6>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-sm text-gray">Subtotal</span>
                                    <span class="text-sm">$<?php echo e(number_format($subtotal,0,',','.')); ?></span>
                                </div>
                                <?php if($showIva): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-sm text-gray">IVA</span>
                                    <span class="text-sm">$<?php echo e(number_format($totalIva ?? 0,0,',','.')); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="pt-3 border-top mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-500">Total</span>
                                    <h5 class="mb-0 fw-bold">$<?php echo e(number_format($total ?? $factura->total ?? $venta->total,0,',','.')); ?></h5>
                                </div>
                            </div>
                            <div class="pt-3 border-top">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-sm text-gray">Pagado</span>
                                    <span class="text-sm fw-500"><?php echo e(is_null($totalPagado) ? '-' : ('$' . number_format($totalPagado,0,',','.'))); ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <?php
                                        $isDeuda = (!is_null($cambio) && $cambio < 0);
                                    ?>
                                    <span class="text-sm fw-500"><?php echo e($isDeuda ? 'Por cobrar' : 'Cambio'); ?></span>
                                    <?php if(!is_null($cambio)): ?>
                                    <span class="text-sm fw-bold" style="color: <?php echo e($cambio >= 0 ? '#0f9e5a' : '#d9534f'); ?>;">$<?php echo e(number_format($isDeuda ? abs($cambio) : $cambio,0,',','.')); ?></span>
                                    <?php else: ?>
                                    <span class="text-sm fw-bold" style="color: #6c757d;">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>

            </div>
                        <?php if(strtolower($venta->estado ?? '') === 'anulada'): ?>
                        <?php
                                // Preferir motivo en la propia venta si existe, si no, tomar de los detalles
                                $motivo = $venta->motivo_anulacion ?? null;
                                if (empty($motivo)) {
                                        $motivos = $venta->detalles->pluck('motivo_anulacion')->filter()->unique()->values();
                                        $motivo = $motivos->isNotEmpty() ? $motivos->implode('\n') : null;
                                }
                        ?>
                        <div class="note-wrapper warning-alert py-4 px-sm-3 px-lg-5">
                                <div class="alert">
                                    <h5 class="text-bold mb-15">Motivo de anulación</h5>
                                    <p class="text-sm text-gray">
                                        <?php echo e($motivo ?? 'No especificado'); ?>

                                    </p>
                                </div>
                        </div>
                        <?php endif; ?>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\optenadvance\laragon\www\resources\views/ventas/detalle.blade.php ENDPATH**/ ?>