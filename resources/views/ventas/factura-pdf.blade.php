<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura - {{ $venta->factura->numero ?? $venta->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            overflow-x: hidden;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 2.6mm;
            line-height: 1.25;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-bottom: 0.2mm solid #b5b5b5;
            margin-bottom: 4mm;
            padding-bottom: 2mm;
        }

        .header-table td {
            vertical-align: top;
        }

        .company-info h1 {
            font-size: 3.6mm;
            margin-bottom: 1.2mm;
            font-weight: bold;
        }

        .company-info p {
            font-size: 2.4mm;
            margin: 0.7mm 0;
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-details h2 {
            font-size: 3.4mm;
            font-weight: bold;
            margin-bottom: 1.2mm;
            color: #555;
        }

        .invoice-details p {
            font-size: 2.4mm;
            margin: 0.7mm 0;
        }

        .invoice-number {
            font-weight: bold;
            font-size: 2.6mm;
        }

        .client-info {
            margin-bottom: 4mm;
        }

        .client-section p {
            margin: 0.6mm 0;
            font-size: 2.4mm;
        }

        .client-label {
            font-weight: bold;
            font-size: 2.4mm;
            margin-bottom: 1mm;
        }

        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 4mm;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        thead {
            background-color: #f2f2f2;
        }

        thead th {
            padding: 1.4mm 1.2mm;
            text-align: left;
            font-weight: bold;
            font-size: 2.4mm;
            border-bottom: 0.2mm solid #b5b5b5;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        tbody td {
            padding: 1.2mm 1.2mm;
            border-bottom: 0.2mm solid #d3d3d3;
            font-size: 2.4mm;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .text-right {
            text-align: right;
            padding-right: 0.6mm;
        }

        .text-center {
            text-align: center;
        }

        .product-cell {
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .totals {
            width: 100%;
            margin-top: 3mm;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 0.8mm 0;
            font-size: 2.6mm;
        }

        .totals-table .label {
            text-align: right;
            padding-right: 2.5mm;
            font-weight: bold;
        }

        .totals-table .value {
            width: 40mm;
            text-align: right;
            padding-right: 4mm;
        }

        .totals-table .total td {
            border-top: 0.2mm solid #b5b5b5;
            border-bottom: 0.2mm solid #b5b5b5;
            padding: 1.2mm 0;
            font-weight: bold;
            padding-right: 4mm;
            font-size: 4mm;
        }

        .footer {
            margin-top: 4mm;
            text-align: center;
            font-size: 2.2mm;
            color: #666;
            border-top: 0.2mm solid #d3d3d3;
            padding-top: 2mm;
        }

        @page {
            size: A4;
            margin: 10mm;
        }
    </style>
</head>
<body>
<div class="container">
    @php
        $factura = $venta->factura;

        $total = $factura->total ?? $venta->total ?? 0;
        $impuestos = $factura->impuestos ?? $venta->detalles->sum(fn($d) => $d->iva ?? 0);
        $hasIva = ((float) $impuestos) > 0;
        $subtotal = $total - $impuestos;
    @endphp
    @if($venta->estado === 'anulada')
    <div style="
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        width: 100%;
        text-align: center;
        z-index: 0;
        pointer-events: none;
    ">
        <div style="
            display: inline-block;
            border: 6pt double #444; /* Borde doble estilo sello contable */
            padding: 15pt 40pt;
            color: #444;
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 55pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 8pt;
            opacity: 0.12; /* Transparencia ideal para no tapar los datos */
        ">
            Anulada
        </div>
    </div>
@endif
    <!-- Header -->
     <h2 class="text-center">FACTURA COMERCIAL</h2>
     <br><br>
    <table class="header-table">
        <tr>
            <td class="company-info" style="width: 65%;">
             @if($empresa && $empresa->logo)
        <img src="{{ public_path($empresa->logo) }}" alt="Logo" style="max-height:60px;max-width:140px;object-fit:contain;display:block;margin-bottom:6px;" />
    @endif
                <h1>{{ $empresa->nombre ?? 'Mi Empresa' }}</h1>
                <p>NIT: {{ $empresa->nit ?? '---' }}</p>
                <p>{{ $empresa->direccion ?? '' }}</p>
                <p>Tel: {{ $empresa->telefono ?? '' }}</p>
                <p>Email: {{ $empresa->email ?? '' }}</p>
            </td>
            <td class="invoice-details" style="width: 35%;">
                
                <p class="invoice-number">
                    ID de Factura: {{ $factura->numero ?? '—' }}
                </p>
                <p><strong>Fecha de emisión:</strong>
                    {{ optional($factura->created_at)->format('d/m/Y H:i') ?? '—' }}
                </p>
                <p><strong>Método de Pago:</strong> {{ $factura->forma_pago ?? '—' }}</p>
            </td>
        </tr>
    </table>

    <!-- Cliente -->
    <div class="client-info">
        <table style="width:100%;border-collapse:collapse;">
            <tr>
                <td>
                    <div class="client-label">Cliente:</div>
                    <p>
                        <strong>{{ $factura->cliente_nombre ?? 'Consumidor final' }}</strong>
                    </p>
                    @if(!empty($factura->cliente_nit))
                        <p>Documento/NIT: {{ $factura->cliente_nit }}</p>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Productos -->
    <table>
        <thead>
            <tr>
                <th style="width:40%;">Producto</th>
                <th class="text-right" style="width:8%;">Cantidad</th>
                <th class="text-right" style="width:14%;">Precio Unitario</th>
                @if($hasIva)
                    <!-- <th class="text-right" style="width:10%;">Tarifa IVA</th> -->
                    <th class="text-right" style="width:10%;">Valor IVA</th>
                @endif
                <th class="text-right" style="width:18%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($venta->detalles as $detalle)
                @php
                    $tarifaIva = optional($detalle->producto)->iva ?? null;
                    $ivaValor = $detalle->iva ?? 0;
                    $lineSubtotal = ($detalle->precio_unitario ?? 0) * ($detalle->cantidad ?? 1);
                @endphp
                <tr>
                    <td class="product-cell">
                        {{ optional($detalle->producto)->nombre ?? 'Producto #' . $detalle->producto_id }}
                    </td>
                    <td class="text-right">{{ $detalle->cantidad }}</td>
                    <td class="text-right">
                        {{ number_format($detalle->precio_unitario ?? 0, 0, ',', '.') }}
                    </td>
                    @if($hasIva)
                        <!-- <td class="text-right">
                            {{ $tarifaIva ? $tarifaIva.'%' : '—' }}
                        </td> -->
                        <td class="text-right">
                            {{ number_format($ivaValor, 0, ',', '.') }}
                        </td>
                    @endif
                    <td class="text-right">
                        {{ number_format($lineSubtotal + $ivaValor, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $hasIva ? 6 : 4 }}" class="text-center">Sin productos</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Totales -->
    <div class="totals">
        <table class="totals-table">
            <tr>
                <td></td>
                <td class="label">Subtotal:</td>
                <td class="value">{{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>

            @if($hasIva)
            <tr>
                <td></td>
                <td class="label">IVA:</td>
                <td class="value">{{ number_format($impuestos, 0, ',', '.') }}</td>
            </tr>
            @endif

            <tr class="total">
                <td></td>
                <td class="label">TOTAL:</td>
                <td class="value">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Gracias por su compra</p>
    </div>
</div>

</body>
</html>
