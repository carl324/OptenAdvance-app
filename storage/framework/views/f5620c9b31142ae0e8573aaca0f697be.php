<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre de caja</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 24px; }
        h1 { font-size: 18px; margin-bottom: 8px; }
        .row { display: flex; justify-content: space-between; margin: 6px 0; }
        .label { color: #6b7280; }
        .value { font-weight: 600; }
        .box { border-top: 1px solid #e5e7eb; padding-top: 12px; margin-top: 12px; }
    </style>
</head>
<body onload="window.print()">
    <h1>Cierre de caja</h1>
    <div class="row">
        <span class="label">Caja ID</span>
        <span class="value">#<?php echo e($caja->id); ?></span>
    </div>
    <div class="row">
        <span class="label">Apertura</span>
        <span class="value"><?php echo e(optional($caja->fecha_apertura)->format('d/m/Y H:i')); ?></span>
    </div>
    <div class="row">
        <span class="label">Cierre</span>
        <span class="value"><?php echo e(optional($caja->fecha_cierre)->format('d/m/Y H:i')); ?></span>
    </div>

    <div class="box">
        <div class="row">
            <span class="label">Monto apertura</span>
            <span class="value">$ <?php echo e(number_format($caja->monto_apertura, 2, ',', '.')); ?></span>
        </div>
        <div class="row">
            <span class="label">Total ventas</span>
            <span class="value">$ <?php echo e(number_format($caja->total_ventas, 2, ',', '.')); ?></span>
        </div>
        <div class="row">
            <span class="label">Total efectivo</span>
            <span class="value">$ <?php echo e(number_format($caja->total_efectivo, 2, ',', '.')); ?></span>
        </div>
        <div class="row">
            <span class="label">Monto cierre calculado</span>
            <span class="value">$ <?php echo e(number_format($caja->monto_cierre_calculado, 2, ',', '.')); ?></span>
        </div>
        <div class="row">
            <span class="label">Monto cierre real</span>
            <span class="value">$ <?php echo e(number_format($caja->monto_cierre_real, 2, ',', '.')); ?></span>
        </div>
        <div class="row">
            <span class="label">Diferencia</span>
            <span class="value">$ <?php echo e(number_format($caja->diferencia, 2, ',', '.')); ?></span>
        </div>
    </div>

    <?php if($caja->nota_cierre): ?>
        <div class="box">
            <div class="label">Nota de cierre</div>
            <div class="value"><?php echo e($caja->nota_cierre); ?></div>
        </div>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\OptenAdvance\app\www\resources\views\caja\cierre-print.blade.php ENDPATH**/ ?>