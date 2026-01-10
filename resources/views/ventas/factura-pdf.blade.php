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
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 8.5in;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .company-info h1 {
            font-size: 18px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .company-info p {
            font-size: 10px;
            margin: 2px 0;
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-details h2 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #666;
        }

        .invoice-details p {
            font-size: 10px;
            margin: 2px 0;
        }

        .invoice-number {
            font-weight: bold;
            font-size: 11px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #ddd;
        }

        .client-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .client-section, .invoice-date {
            flex: 1;
        }

        .client-section p {
            margin: 3px 0;
            font-size: 10px;
        }

        .client-label {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 5px;
        }

        .invoice-date {
            text-align: right;
        }

        .invoice-date p {
            margin: 3px 0;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background-color: #f5f5f5;
        }

        thead th {
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border-bottom: 2px solid #333;
        }

        tbody td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals {
            width: 100%;
            margin-top: 20px;
        }

        .totals-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .totals-label {
            width: 150px;
            text-align: right;
            padding-right: 10px;
            font-weight: bold;
        }

        .totals-value {
            width: 100px;
            text-align: right;
        }

        .totals-row.total {
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            padding: 8px 0;
            font-weight: bold;
            font-size: 12px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .note {
            margin-top: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #333;
            font-size: 10px;
        }

        @page {
            margin: 0.5in;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>{{ $empresa->nombre ?? 'Mi Empresa' }}</h1>
                <p>NIT: {{ $empresa->nit ?? '---' }}</p>
                <p>{{ $empresa->direccion ?? '' }}</p>
                <p>Tel: {{ $empresa->telefono ?? '' }}</p>
                <p>Email: {{ $empresa->email ?? '' }}</p>
            </div>
            <div class="invoice-details">
                <h2>FACTURA</h2>
                <p class="invoice-number">
                    Número: {{ $venta->factura->numero ?? 'FA-' . str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}
                </p>
                <p>Fecha: {{ $venta->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- Cliente -->
        <div class="client-info">
            <div class="client-section">
                <div class="client-label">PARA:</div>
                <p><strong>{{ $venta->cliente_nombre ?? 'Cliente' }}</strong></p>
                <p>Documento: {{ $venta->cliente_documento ?? '---' }}</p>
            </div>
            <div class="invoice-date">
                <div class="client-label">FECHA Y HORA:</div>
                <p>{{ $venta->created_at->format('d/m/Y') }}</p>
                <p>{{ $venta->created_at->format('H:i:s') }}</p>
            </div>
        </div>

        <!-- Tabla de productos -->
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Producto</th>
                    <th class="text-right" style="width: 12%;">Cantidad</th>
                    <th class="text-right" style="width: 18%;">Precio Unitario</th>
                    @if ($venta->factura && $venta->factura->cobra_iva)
                        <th class="text-right" style="width: 10%;">IVA</th>
                    @endif
                    <th class="text-right" style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($venta->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td class="text-right">{{ $detalle->cantidad }}</td>
                        <td class="text-right">
                            {{ $empresa->moneda ?? '$' }}
                            {{ number_format($detalle->precio_unitario, 2, ',', '.') }}
                        </td>
                        @if ($venta->factura && $venta->factura->cobra_iva)
                            <td class="text-right">
                                {{ $empresa->moneda ?? '$' }}
                                {{ number_format($detalle->iva, 2, ',', '.') }}
                            </td>
                        @endif
                        <td class="text-right">
                            {{ $empresa->moneda ?? '$' }}
                            {{ number_format($detalle->total, 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Sin productos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Totales -->
        <div class="totals">
            <div class="totals-row">
                <div class="totals-label">Subtotal:</div>
                <div class="totals-value">
                    {{ $empresa->moneda ?? '$' }}
                    {{ number_format($venta->subtotal, 2, ',', '.') }}
                </div>
            </div>

            @if ($venta->factura && $venta->factura->cobra_iva && $venta->impuestos > 0)
                <div class="totals-row">
                    <div class="totals-label">IVA ({{ $venta->factura->iva_porcentaje ?? 19 }}%):</div>
                    <div class="totals-value">
                        {{ $empresa->moneda ?? '$' }}
                        {{ number_format($venta->impuestos, 2, ',', '.') }}
                    </div>
                </div>
            @endif

            <div class="totals-row total">
                <div class="totals-label">TOTAL:</div>
                <div class="totals-value">
                    {{ $empresa->moneda ?? '$' }}
                    {{ number_format($venta->total, 2, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Método de pago -->
        <div class="section">
            <p><strong>Método de Pago:</strong> {{ $venta->metodo_pago ?? 'No especificado' }}</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Gracias por su compra</p>
            <p style="margin-top: 10px;">Este documento fue generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
