
  

<?php $__env->startSection('title', 'Factura #' . ($venta->factura->numero ?? $venta->id)); ?>

<?php $__env->startSection('content'); ?>

<?php if(strtolower($venta->estado ?? '') === 'anulada'): ?>
<style>
.invoice-wrapper {
    position: relative;
    overflow: hidden;          /* ¡Esto es clave! Recorta la marca de agua que sobresale */
}

.invoice-watermark {
    position: absolute;
    top: 55%;                  /* Movido un poco más abajo para evitar que suba hacia navbar */
    left: 50%;
    transform: translate(-50%, -50%) rotate(-25deg);

    max-width: 95%;            /* Limita ancho para no desbordar horizontalmente */
    max-height: 90%;           /* Limita altura para no desbordar verticalmente */
    white-space: nowrap;
    
    border: 6px double rgba(220, 38, 38, 0.15);
    padding: 10px 30px;
    border-radius: 12px;

    font-size: 4.5em;          /* Reducido un poco para mejor contención (ajusta si quieres más grande) */
    font-family: 'Inter', 'Segoe UI', sans-serif;
    color: rgba(220, 38, 38, 0.15);

    pointer-events: none;
    user-select: none;
    z-index: 1;                /* Bajado para que no tape elementos con z-index mayor (navbar suele tener alto) */

    text-align: center;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    text-shadow: 1px 1px 0 rgba(255,255,255,0.5);
}

@media print {
    .invoice-watermark {
        color: rgba(220, 38, 38, 0.25) !important;     /* Más visible en impresión */
        border-color: rgba(220, 38, 38, 0.25) !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        font-size: 4em;                                /* Ajuste para impresión si se ve muy grande */
    }
    
    .invoice-wrapper {
        overflow: visible !important;                  /* En print a veces hidden corta, pero con visible + z-index bajo suele quedar bien */
    }
}

</style>
<?php endif; ?>

<section>
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    
                </div>
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper"></div>
                </div>
            </div>
        </div>
        <!-- ========== title-wrapper end ========== -->

        <!-- Invoice Wrapper Start -->
        <div class="invoice-wrapper">
            <?php if(strtolower($venta->estado ?? '') === 'anulada'): ?>
                <div class="invoice-watermark">VENTA ANULADA</div>
            <?php endif; ?>
            <div class="row">
                
                <div class="col-12">
                    
                    <div class="invoice-card card-style mb-30">
                        
                        <div class="invoice-header">
                            
                            <div class="address-item">
                                
                                <h2><?php echo e($empresa->nombre ?? 'Empresa'); ?></h2>
                                <p class="text-sm">
                                  <span class="text-medium">Direccion:</span>  <?php echo e($empresa->direccion ?? '-'); ?>

                                </p>
                                <p class="text-sm">
                                    <span class="text-medium">Email:</span> <?php echo e($empresa->email ?? '-'); ?>

                                </p>
                                <p class="text-sm">
                                    <span class="text-medium">Teléfono:</span> <?php echo e($empresa->telefono ?? '-'); ?>

                                </p>
                                <p class="text-sm">
                                    <span class="text-medium">NIT:</span> <?php echo e($empresa->nit ?? '-'); ?>

                                </p>
                            </div>
<div class="invoice-logo">
    <?php if($empresa && $empresa->logo): ?>
        <img src="data:image/png;base64,<?php echo e(base64_encode(file_get_contents(public_path($empresa->logo)))); ?>"  alt="Logo" />
    <?php else: ?>
        <img src="/assets/images/invoice/uideck-logo.svg" alt="" />
    <?php endif; ?>
</div>
                            <div class="invoice-date">
                                <?php
                                    $fechaRaw = $venta->factura->created_at ?? $venta->created_at ?? $venta->fecha ?? $venta->factura->fecha_emision ?? now();
                                    try {
                                        $displayFecha = \Carbon\Carbon::parse($fechaRaw)->format('d/m/Y H:i');
                                    } catch (\Exception $e) {
                                        $displayFecha = (string) $fechaRaw;
                                    }
                                ?>
                                <p><span class="text-medium">Fecha de emisión:</span> <?php echo e($displayFecha); ?></p>
                                <p><span class="text-medium">ID de Factura:</span> <?php echo e($venta->factura->numero ?? '#' . $venta->id); ?></p>
                                <p><span class="text-medium">Medio de pago:</span> <?php echo e($venta->factura->forma_pago ?? $venta->forma_pago ?? 'No especificado'); ?></p>
                            </div>
                        </div>

                        <div class="invoice-address">
                            <div class="address-item">
                                <h5 class="text-bold">Para</h5>
                                <h1><?php echo e($venta->factura->cliente_nombre ?? $venta->cliente ?? 'Consumidor final'); ?></h1>
                                <p class="text-sm">
                                    <span class="text-medium">Documento/NIT:</span> <?php echo e($venta->factura->cliente_nit ?? '-'); ?>

                                </p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="invoice-table table">
                                <thead>
                                    <tr>
                                        <th class="service">
                                            <h6 class="text-sm text-medium">Producto</h6>
                                        </th>
                                        <th class="qty">
                                            <h6 class="text-sm text-medium">Cantidad</h6>
                                        </th>
                                        <th class="amount">
                                            <h6 class="text-sm text-medium">Precio Unitario</h6>
                                        </th>
                                        <?php
                                            $total = $venta->factura->total ?? $venta->total ?? 0;
                                            $impuestos = $venta->factura->impuestos ?? null;
                                            if (is_null($impuestos)) {
                                                $impuestos = $venta->detalles->sum(function($d){ return $d->iva ?? 0; });
                                            }
                                            $hasIva = ((float) $impuestos) > 0;
                                        ?>
                                        <?php if($hasIva): ?>
                                            <th class="amount">
                                                <!--<h6 class="text-sm text-medium">Tarifa IVA</h6>-->
                                            </th> 
                                            <th class="amount">
                                                <h6 class="text-sm text-medium">Valor IVA</h6>
                                            </th>
                                        <?php endif; ?>
                                        <th class="amount">
                                            <h6 class="text-sm text-medium">Total</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $venta->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <p class="text-sm"><?php echo e(optional($d->producto)->nombre ?? 'Producto #' . $d->producto_id); ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm"><?php echo e($d->cantidad); ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm">$<?php echo e(number_format($d->precio_unitario, 0, ',', '.')); ?></p>
                                        </td>
                                        <?php if($hasIva): ?>
                                            <td>
                                               <!-- <p class="text-sm">
                                                    <?php echo e((optional($d->producto)->iva ?? 0) > 0 ? optional($d->producto)->iva . '%' : '—'); ?>

                                                </p>-->
                                            </td> 
                                            <td>
                                                <p class="text-sm">$<?php echo e(number_format($d->iva ?? 0, 0, ',', '.')); ?></p>
                                            </td>
                                        <?php endif; ?>
                                        <td>
    <p class="text-sm">$<?php echo e(number_format(($d->subtotal ?? 0) + ($d->iva ?? 0), 0, ',', '.')); ?></p>
</td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    
                                    <?php
                                        $subtotal = $total - $impuestos;
                                    ?>
                                    
                                    <tr>
                                        <td colspan="<?php echo e($hasIva ? '5' : '3'); ?>" style="text-align: right;">
                                            <h6 class="text-sm text-medium">Subtotal</h6>
                                        </td>
                                        <td style="text-align: right;">
                                            <h6 class="text-sm text-bold">$<?php echo e(number_format($subtotal, 0, ',', '.')); ?></h6>
                                        </td>
                                    </tr>

                                    <?php if($hasIva): ?>
                                    <tr>
                                        <td colspan="<?php echo e($hasIva ? '5' : '3'); ?>" style="text-align: right;">
                                            <h6 class="text-sm text-medium">IVA Total</h6>
                                        </td>
                                        <td style="text-align: right;">
                                            <h6 class="text-sm text-bold">$<?php echo e(number_format($impuestos, 0, ',', '.')); ?></h6>
                                        </td>
                                    </tr>
                                    <?php endif; ?>

                                    <tr>
                                        <td colspan="<?php echo e($hasIva ? '5' : '3'); ?>" style="text-align: right;">
                                            <h4>Total</h4>
                                        </td>
                                        <td style="text-align: right;">
                                            <h4>$<?php echo e(number_format($total, 0, ',', '.')); ?></h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <?php if(in_array($venta->estado, ['devuelta', 'dev_parcial'])): ?>
<div style="margin: 0 0 20px 0; padding: 14px 20px; border-radius: 8px; border-left: 4px solid <?php echo e($venta->estado === 'devuelta' ? '#22c55e' : '#f2994a'); ?>; background: <?php echo e($venta->estado === 'devuelta' ? '#f0fdf4' : '#fff7ed'); ?>; display: flex; align-items: center; gap: 12px;">
    <i class="lni <?php echo e($venta->estado === 'devuelta' ? 'lni-checkmark-circle' : 'lni-warning'); ?>" style="font-size: 20px; color: <?php echo e($venta->estado === 'devuelta' ? '#22c55e' : '#f2994a'); ?>;"></i>
    <p class="text-sm" style="margin: 0; color: #444;">
        <?php if($venta->estado === 'devuelta'): ?>
            Esta venta ha sido reembolsada en su totalidad.
        <?php else: ?>
            Esta venta tiene un reembolso parcial registrado.
        <?php endif; ?>
    </p>
</div>
<?php endif; ?>
                        <div class="invoice-action">
                            <ul class="d-flex flex-wrap align-items-center justify-content-center gap-3">
                                <li>
                                    <button type="button" id="btn-descargar-pdf" class="main-btn primary-btn-outline btn-hover" onclick="descargarPDF()">
                                        <span id="btn-descargar-text">Descargar Factura</span>
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="main-btn primary-btn btn-hover" onclick="imprimirFactura()">Imprimir Factura</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Invoice Wrapper End -->
    </div>
    <!-- end container -->
</section>

<script>
function imprimirFactura() {
    const ventaId = <?php echo e($venta->id); ?>;
    const btn = document.querySelector('button[onclick="imprimirFactura()"]');
    const originalText = btn ? btn.textContent : '';

    if (btn && btn.disabled) {
        return;
    }

    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Cargando...';
    }
    
    // Crear iframe oculto dinámicamente
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.style.position = 'absolute';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = 'none';
    
    // Cargar la vista de impresión en el iframe
    iframe.src = `/ventas/${ventaId}/factura/impresion`;
    
    // Cuando el iframe cargue, ejecutar print
    iframe.onload = function() {
        // Esperar un pequeño delay para asegurar que el contenido esté completamente renderizado
        setTimeout(() => {
            try {
                iframe.contentWindow.print();
            } catch (e) {
                console.error('Error al imprimir:', e);
            }
            
            // Eliminar el iframe después de la impresión
            setTimeout(() => {
                document.body.removeChild(iframe);
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            }, 100);
        }, 300);
    };
    
    // Manejo de errores si no carga
    iframe.onerror = function() {
        console.error('Error al cargar la vista de impresión');
        document.body.removeChild(iframe);
        if (btn) {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    };
    
    // Agregar el iframe al DOM
    document.body.appendChild(iframe);
}

async function descargarPDF() {
    const btn = document.getElementById('btn-descargar-pdf');
    const btnText = document.getElementById('btn-descargar-text');
    const ventaId = <?php echo e($venta->id); ?>;

    if (btn.disabled) {
        return;
    }

    btn.disabled = true;
    btnText.textContent = 'Generando PDF...';

    const controller = new AbortController();
    let timedOut = false;
    let descargaExitosa = false;
    const timeoutId = setTimeout(() => {
        timedOut = true;
        controller.abort();
    }, 30000);

    try {
        const res = await fetch(`/ventas/${ventaId}/factura/pdf`, {
            method: 'GET',
            headers: {
                'Accept': 'application/pdf'
            },
            signal: controller.signal
        });

        if (!res.ok) {
            let mensaje = 'No se pudo descargar la factura.';

            if (res.status === 419) {
                mensaje = 'Sesión expirada. Recarga la página.';
            } else if (res.status >= 500) {
                mensaje = 'Error del servidor. Intenta más tarde.';
            }

            throw new Error(mensaje);
        }

        // Obtener nombre del archivo
        const contentDisposition = res.headers.get('content-disposition');
        let fileName = `factura-${ventaId}.pdf`;
        if (contentDisposition) {
            const matches = contentDisposition.match(/filename="(.+?)"/);
            if (matches) fileName = matches[1];
        }

        // Convertir a blob y crear descarga
        const blob = await res.blob();
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        // Mostrar notificación
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Factura descargada', {
                body: 'Archivo: ' + fileName,
                icon: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%231f5fbf"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>'
            });
        }

        descargaExitosa = true;

    } catch (error) {
        console.error('Error:', error);

        if (timedOut || error.name === 'AbortError') {
            btnText.textContent = 'La descarga tardó demasiado. Intenta de nuevo.';
        } else {
            btnText.textContent = error.message || 'No se pudo descargar la factura.';
        }
    } finally {
        clearTimeout(timeoutId);
        btn.disabled = false;

        if (descargaExitosa) {
            btnText.textContent = 'Descargar Factura';
            return;
        }

        if (btnText.textContent !== 'Descargar Factura') {
            setTimeout(() => {
                if (!btn.disabled) {
                    btnText.textContent = 'Descargar Factura';
                }
            }, 3500);
        }
    }
}

// Solicitar permisos de notificación al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\optenadvance\app\www\resources\views/ventas/factura.blade.php ENDPATH**/ ?>