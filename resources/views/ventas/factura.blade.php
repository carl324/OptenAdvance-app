
  @extends('layouts.app')

@section('title', 'Factura #' . ($venta->factura->numero ?? $venta->id))

@section('content')

<section>
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title">
                        <h2>Factura</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper"></div>
                </div>
            </div>
        </div>
        <!-- ========== title-wrapper end ========== -->

        <!-- Invoice Wrapper Start -->
        <div class="invoice-wrapper">
            <div class="row">
                <div class="col-12">
                    <div class="invoice-card card-style mb-30">
                        <div class="invoice-header">
                            <div class="address-item">
                                <h2>{{ $empresa->nombre ?? 'Empresa' }}</h2>
                                <p class="text-sm">
                                    {{ $empresa->direccion ?? '-' }}
                                </p>
                                <p class="text-sm">
                                    <span class="text-medium">Email:</span> {{ $empresa->email ?? '-' }}
                                </p>
                                <p class="text-sm">
                                    <span class="text-medium">Teléfono:</span> {{ $empresa->telefono ?? '-' }}
                                </p>
                                <p class="text-sm">
                                    <span class="text-medium">NIT:</span> {{ $empresa->nit ?? '-' }}
                                </p>
                            </div>

                            <div class="invoice-date">
                                @php
                                    $fechaRaw = $venta->factura->created_at ?? $venta->created_at ?? $venta->fecha ?? $venta->factura->fecha_emision ?? now();
                                    try {
                                        $displayFecha = \Carbon\Carbon::parse($fechaRaw)->format('d/m/Y H:i');
                                    } catch (\Exception $e) {
                                        $displayFecha = (string) $fechaRaw;
                                    }
                                @endphp
                                <p><span class="text-medium">Fecha de emisión:</span> {{ $displayFecha }}</p>
                                <p><span class="text-medium">ID de Factura:</span> {{ $venta->factura->numero ?? '#' . $venta->id }}</p>
                            </div>
                        </div>

                        <div class="invoice-address">
                            <div class="address-item">
                                <h5 class="text-bold">Para</h5>
                                <h1>{{ $venta->factura->cliente_nombre ?? $venta->cliente ?? 'Consumidor final' }}</h1>
                                <p class="text-sm">
                                    <span class="text-medium">Documento/NIT:</span> {{ $venta->factura->cliente_nit ?? '-' }}
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
                                        @php
                                            $total = $venta->factura->total ?? $venta->total ?? 0;
                                            $impuestos = $venta->factura->impuestos ?? null;
                                            if (is_null($impuestos)) {
                                                $impuestos = $venta->detalles->sum(function($d){ return $d->iva ?? 0; });
                                            }
                                            $hasIva = ((float) $impuestos) > 0;
                                        @endphp
                                        @if($hasIva)
                                            <th class="amount">
                                                <h6 class="text-sm text-medium">IVA</h6>
                                            </th>
                                        @endif
                                        <th class="amount">
                                            <h6 class="text-sm text-medium">Total</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($venta->detalles as $d)
                                    <tr>
                                        <td>
                                            <p class="text-sm">{{ optional($d->producto)->nombre ?? 'Producto #' . $d->producto_id }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm">{{ $d->cantidad }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm">${{ number_format($d->precio_unitario, 0, ',', '.') }}</p>
                                        </td>
                                        @if($hasIva)
                                            <td>
                                                <p class="text-sm">${{ number_format($d->iva ?? 0, 0, ',', '.') }}</p>
                                            </td>
                                        @endif
                                        <td>
                                            <p class="text-sm">${{ number_format($d->subtotal, 0, ',', '.') }}</p>
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                    @php
                                        $subtotal = $total - $impuestos;
                                    @endphp
                                    
                                    <tr>
                                        <td colspan="{{ $hasIva ? '4' : '3' }}" style="text-align: right;">
                                            <h6 class="text-sm text-medium">Subtotal</h6>
                                        </td>
                                        <td style="text-align: right;">
                                            <h6 class="text-sm text-bold">${{ number_format($subtotal, 0, ',', '.') }}</h6>
                                        </td>
                                    </tr>

                                    @if($hasIva)
                                    <tr>
                                        <td colspan="{{ $hasIva ? '4' : '3' }}" style="text-align: right;">
                                            <h6 class="text-sm text-medium">IVA Total</h6>
                                        </td>
                                        <td style="text-align: right;">
                                            <h6 class="text-sm text-bold">${{ number_format($impuestos, 0, ',', '.') }}</h6>
                                        </td>
                                    </tr>
                                    @endif

                                    <tr>
                                        <td colspan="{{ $hasIva ? '4' : '3' }}" style="text-align: right;">
                                            <h4>Total</h4>
                                        </td>
                                        <td style="text-align: right;">
                                            <h4>${{ number_format($total, 0, ',', '.') }}</h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>
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
    const ventaId = {{ $venta->id }};
    
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
            }, 100);
        }, 300);
    };
    
    // Manejo de errores si no carga
    iframe.onerror = function() {
        console.error('Error al cargar la vista de impresión');
        document.body.removeChild(iframe);
    };
    
    // Agregar el iframe al DOM
    document.body.appendChild(iframe);
}

async function descargarPDF() {
    const btn = document.getElementById('btn-descargar-pdf');
    const btnText = document.getElementById('btn-descargar-text');
    const ventaId = {{ $venta->id }};

    btn.disabled = true;
    btnText.textContent = 'Generando PDF...';

    try {
        const res = await fetch(`/ventas/${ventaId}/factura/pdf`, {
            method: 'GET',
            headers: {
                'Accept': 'application/pdf'
            }
        });

        if (!res.ok) {
            throw new Error('Error al descargar la factura');
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

    } catch (error) {
        console.error('Error:', error);
        alert('Error: ' + (error.message || 'No se pudo descargar la factura'));
    } finally {
        btn.disabled = false;
        btnText.textContent = 'Descargar Factura';
    }
}

// Solicitar permisos de notificación al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
});
</script>

@endsection
