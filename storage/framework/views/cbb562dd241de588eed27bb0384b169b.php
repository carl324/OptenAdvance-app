<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Abono</title>
      <link rel="stylesheet" href="/assets/css/lineicons.css" />
      <link rel="stylesheet" href="/assets/css/materialdesignicons.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            background: #ffffff;
            color: #1e293b;
            line-height: 1.5;
            padding: 20px;
        }

        .comprobante {
            max-width: 700px;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
            padding: 40px 50px;
            background: white;
        }

        .seccion-titulo {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .campo { margin-bottom: 16px; }

        .campo .label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .campo .valor {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }

        .saldos { margin-bottom: 30px; }

        .saldos table {
            width: 100%;
            border-collapse: collapse;
        }

        .saldos th {
            background: #f8fafc;
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
        }

        .saldos td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }

        .saldos .valor { font-weight: 700; text-align: right; }
        .saldos .positivo { color: #166534; }
        .saldos .negativo { color: #b91c1c; }

        .mensaje-gracias {
            text-align: center;
            font-size: 13px;
            color: #475569;
            font-style: italic;
            margin: 30px 0;
            padding: 20px;
            border-top: 1px dashed #e2e8f0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #64748b;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        @media print {
            body { padding: 0; background: white; }
            .comprobante { border: none; padding: 30px 40px; max-width: 100%; }
            @page { size: A4; margin: 15mm; }
        }
    </style>
</head>
<body>
<div class="comprobante">

    <!-- Header -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:32px;padding-bottom:24px;border-bottom:2px solid #dad9d9;">
        <div style="width:80px;height:80px;background:#f8fafc;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:10px;color:#94a3b8;text-align:center;border:1px solid #e2e8f0;">
            LOGO<br>EMPRESA
        </div>
        <div style="text-align:right;">
            <h1 style="font-size:24px;font-weight:800;color:#0f172a;letter-spacing:-0.5px;margin-bottom:4px;">Comprobante de Abono</h1>
            <p style="font-size:12px;color:#64748b;">
    <?php echo e($abono->created_at->translatedFormat('d \d\e F \d\e Y, g:i a')); ?>

</p>
        </div>
    </div>

    <!-- Monto -->
    <div style="display:flex;align-items:center;justify-content:space-between;background:#f0fdf4;border-radius:10px;padding:24px 32px;margin-bottom:36px;border:1px solid #bbf7d0;">
        <div>
            <p style="font-size:11px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;">Total abonado</p>
            <p style="font-size:40px;font-weight:800;color:#166534;letter-spacing:-1px;line-height:1;">$<?php echo e(number_format($abono->monto, 0, ',', '.')); ?></p>
            
        </div>
<div style="text-align:center;">
    <div style="width:56px;height:56px;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
        <span style="color:#166534;font-size:48px;">✓</span>
    </div>
    <p style="font-size:11px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:1px;">Pago recibido</p>
</div>
    </div>

    <!-- Información del pago -->
    <div>
        <p class="seccion-titulo">Información del pago</p>
        <div class="grid">
            <div class="campo">
                <div class="label">Cliente</div>
                <div class="valor"><?php echo e($cliente->nombre); ?></div>
            </div>
        
            <div class="campo">
                <div class="label">NIT / CC</div>
                <div class="valor">
    <?php echo e(filled($cliente->nit) ? $cliente->nit : 'No aplica'); ?>

</div>
            </div>
            <div class="campo">
                <div class="label">Factura #</div>
                <div class="valor"><?php echo e(optional($venta->factura)->numero ?? '#'.str_pad($venta->id, 6, '0', STR_PAD_LEFT)); ?></div>
            </div>
            <div class="campo">
                <div class="label">Forma de pago</div>
                <div class="valor" style="text-transform:capitalize;"><?php echo e($abono->forma_pago); ?></div>
            </div>
            
        </div>
    </div>

    <!-- Saldos -->
    <div class="saldos">
        <p class="seccion-titulo">Estado de cuenta</p>
        <table>
            <tr>
                <th>Concepto</th>
                <th class="valor">Monto</th>
            </tr>
            <tr>
                <td>Saldo restante de esta factura</td>
                <td class="valor <?php echo e($venta->saldo_pendiente <= 0 ? 'positivo' : 'negativo'); ?>">
                    $<?php echo e(number_format($venta->saldo_pendiente, 0, ',', '.')); ?>

                </td>
            </tr>
            <tr>
                <td>Deuda total del cliente</td>
                <td class="valor <?php echo e($cliente->saldo_pendiente <= 0 ? 'positivo' : 'negativo'); ?>">
                    $<?php echo e(number_format($cliente->saldo_pendiente, 0, ',', '.')); ?>

                </td>
            </tr>
        </table>
    </div>

    <!-- Mensaje -->
    <div class="mensaje-gracias">
        Gracias por su pago. Su compromiso nos permite seguir brindándole un mejor servicio.
    </div>

    <!-- Footer -->
    <div class="footer">
        Documento generado el <?php echo e(now()->format('d/m/Y H:i')); ?><br>
        Este comprobante es válido como soporte oficial de pago ante la empresa. Conserve este documento para cualquier consulta futura.
    </div>

</div>
</body>
</html><?php /**PATH C:\optenadvance\app\www\resources\views/clientes/comprobante-abono.blade.php ENDPATH**/ ?>