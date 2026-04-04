<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impresión Factura</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background: #fff;
            color: #000;
        }

        .receipt {
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            page-break-after: always;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 9px;
            line-height: 1.2;
            margin: 2px 0;
        }

        .info-section {
            border-bottom: 1px dashed #000;
            padding: 8px 0;
            margin-bottom: 8px;
            font-size: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 9px;
        }

        .label {
            font-weight: bold;
        }

        .value {
            text-align: right;
        }

        .items {
            border-bottom: 1px dashed #000;
            padding: 8px 0;
            margin-bottom: 8px;
            font-size: 9px;
        }

        .item {
            margin-bottom: 8px;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 5px;
        }

        .item-name {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 2px;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 8px;
        }

        .item-qty {
            text-align: left;
        }

        .item-price {
            text-align: right;
        }

        .totals {
            border-bottom: 1px dashed #000;
            padding: 8px 0;
            margin-bottom: 8px;
            font-size: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .total-label {
            font-weight: bold;
        }

        .total-value {
            text-align: right;
            font-weight: bold;
        }

        .grand-total {
            font-size: 12px;
            font-weight: bold;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 5px 0;
        }

        .payment-method {
            text-align: center;
            font-size: 9px;
            padding: 5px 0;
            border-bottom: 1px dashed #000;
            margin-bottom: 8px;
        }

        .footer {
            text-align: center;
            font-size: 8px;
            padding: 5px 0;
            color: #666;
        }

        .divider {
            border-bottom: 1px dashed #000;
            margin: 8px 0;
        }

        .client-info {
            font-size: 9px;
            margin: 5px 0;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .receipt {
                width: 80mm;
                page-break-inside: avoid;
            }

            @page {
                size: 80mm 297mm;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
    <?php
        $factura = $venta->factura;
        $total = $factura->total ?? $venta->total ?? 0;
        $impuestos = $factura->impuestos ?? $venta->detalles->sum(fn($d) => $d->iva ?? 0);
        $hasIva = ((float) $impuestos) > 0;
        $subtotal = $total - $impuestos;
    ?>
<?php if($venta->estado === 'anulada'): ?>
    <div style="
        position: fixed;
        top: 30%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        width: 80mm; /* Ajustado a ancho de papel térmico */
        max-width: 90%; /* Nunca más ancho que la página */
        text-align: center;
        z-index: 0;
        pointer-events: none;
    ">
        <div style="
            display: inline-block;
            border: 4pt double #444; /* Borde doble estilo sello contable */
            padding: 8pt 20pt;
            color: #444;
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 28pt; /* Ajustado para papel térmico */
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 4pt;
            opacity: 0.12; /* Transparencia */
            white-space: nowrap;
        ">
            Anulada
        </div>
    </div>
<?php endif; ?>

    <!-- Header -->
    <div class="header">
      <?php if($empresa && $empresa->logo): ?>
        <img src="<?php echo e(asset($empresa->logo)); ?>" alt="Logo" style="max-height:50px;max-width:120px;object-fit:contain;display:block;margin:0 auto 6px;" />
    <?php endif; ?>
        <h1><?php echo e($empresa->nombre ?? 'Factura'); ?></h1>
        <p>NIT: <?php echo e($empresa->nit ?? '---'); ?></p>
        <p>Direccion:<?php echo e($empresa->direccion ?? ''); ?></p>
        <p>Telefono: <?php echo e($empresa->telefono ?? ''); ?></p>
        <p>Email: <?php echo e($empresa->email ?? ''); ?></p>
    </div>

    <!-- Información de la Factura -->
    <div class="info-section">
        <div class="info-row">
            <span class="label">Factura:</span>
            <span class="value"><?php echo e($factura->numero ?? '#' . str_pad($venta->id, 6, '0', STR_PAD_LEFT)); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Fecha:</span>
            <span class="value"><?php echo e(optional($factura->created_at)->format('d/m/Y H:i') ?? '—'); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Método de pago:</span>
            <span class="value"><?php echo e(strtoupper($factura->forma_pago ?? 'Efectivo')); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Cliente:</span>
            <span class="value"><?php echo e($factura->cliente_nombre ?? 'Consumidor final'); ?></span>
        </div>
        <?php if(!empty($factura->cliente_nit)): ?>
        <div class="info-row">
            <span class="label">Documento/NIT:</span>
            <span class="value"><?php echo e($factura->cliente_nit); ?></span>
        </div>
        <?php endif; ?>
    </div>
       
    <!-- Ítems -->
    <div class="items">
        <?php $__currentLoopData = $venta->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $tarifaIva = optional($detalle->producto)->iva ?? null;
                $ivaValor = $detalle->iva ?? 0;
                $lineTotal = ($detalle->precio_unitario ?? 0) * ($detalle->cantidad ?? 1) + $ivaValor;
            ?>
            <div class="item">
                <div class="item-name"><?php echo e(optional($detalle->producto)->nombre ?? 'Producto #' . $detalle->producto_id); ?></div>
                <div class="item-details">
                    <span class="item-qty"><?php echo e($detalle->cantidad); ?>x $<?php echo e(number_format($detalle->precio_unitario ?? 0, 0, ',', '.')); ?></span>
                    <span class="item-price">$<?php echo e(number_format($lineTotal, 0, ',', '.')); ?></span>
                </div>
                <?php if($ivaValor > 0): ?>
                <div style="font-size: 8px; text-align: right; color: #666;">
                    IVA: $<?php echo e(number_format($ivaValor, 0, ',', '.')); ?> <!--(<?php echo e($tarifaIva ? $tarifaIva.'%' : '—'); ?>)-->
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Totales -->
    <div class="totals">
        <div class="total-row">
            <span class="total-label">Subtotal:</span>
            <span class="total-value">$<?php echo e(number_format($subtotal, 0, ',', '.')); ?></span>
        </div>
        <?php if($hasIva): ?>
        <div class="total-row">
            <span class="total-label">IVA:</span>
            <span class="total-value">$<?php echo e(number_format($impuestos, 0, ',', '.')); ?></span>
        </div>
        <?php endif; ?>
        <div class="total-row ">
            <span class="total-label">TOTAL:</span>
            <span class="total-value">$<?php echo e(number_format($total, 0, ',', '.')); ?></span>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>¡Gracias por su compra!</p>
    </div>
</div>

</body>
</html>
<?php /**PATH C:\optenadvance\app\www\resources\views/ventas/factura-impresion.blade.php ENDPATH**/ ?>