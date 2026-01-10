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
        <!-- Header -->
        <div class="header">
            <h1>{{ $empresa->nombre ?? 'Factura' }}</h1>
            <p>NIT: {{ $empresa->nit ?? '---' }}</p>
            <p>{{ $empresa->direccion ?? '' }}</p>
            <p>Tel: {{ $empresa->telefono ?? '' }}</p>
        </div>

        <!-- Información de la Factura -->
        <div class="info-section">
            <div class="info-row">
                <span class="label">Factura:</span>
                <span class="value">{{ $venta->factura->numero ?? '#' . str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Fecha:</span>
                <span class="value">{{ $venta->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Cliente:</span>
                <span class="value">{{ $venta->cliente_nombre ?? 'Consumidor final' }}</span>
            </div>
            @if ($venta->cliente_documento)
            <div class="info-row">
                <span class="label">Doc:</span>
                <span class="value">{{ $venta->cliente_documento }}</span>
            </div>
            @endif
        </div>

        <!-- Ítems -->
        <div class="items">
            @foreach ($venta->detalles as $d)
            <div class="item">
                <div class="item-name">{{ $d->producto->nombre }}</div>
                <div class="item-details">
                    <span class="item-qty">{{ $d->cantidad }}x @ ${{ number_format($d->precio_unitario, 0, ',', '.') }}</span>
                    <span class="item-price">${{ number_format($d->subtotal, 0, ',', '.') }}</span>
                </div>
                @php
                    $iva = $d->iva ?? 0;
                @endphp
                @if ($iva > 0)
                <div style="font-size: 8px; text-align: right; color: #666;">
                    IVA: ${{ number_format($iva, 0, ',', '.') }}
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Totales -->
        <div class="totals">
            @php
                $subtotal = $venta->subtotal ?? ($venta->total - array_sum($venta->detalles->pluck('iva')->toArray()));
                $impuestos = $venta->impuestos ?? $venta->detalles->sum('iva');
            @endphp
            
            <div class="total-row">
                <span class="total-label">Subtotal:</span>
                <span class="total-value">${{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>

            @if ($impuestos > 0)
            <div class="total-row">
                <span class="total-label">IVA:</span>
                <span class="total-value">${{ number_format($impuestos, 0, ',', '.') }}</span>
            </div>
            @endif

            <div class="divider"></div>

            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>${{ number_format($venta->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Método de Pago -->
        <div class="payment-method">
            <strong>{{ strtoupper($venta->metodo_pago ?? 'Efectivo') }}</strong>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>¡Gracias por su compra!</p>
            <p style="margin-top: 5px; font-size: 7px;">{{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
