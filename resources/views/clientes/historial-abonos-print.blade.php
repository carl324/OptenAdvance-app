<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Abonos</title>
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
            max-width: 900px;
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

        .header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 2px solid #dad9d9;
        }

        .logo-placeholder {
            width: 80px;
            height: 80px;
            background: #f8fafc;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #94a3b8;
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        .header-text {
            text-align: right;
        }

        .header-text h1 {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .header-text p {
            font-size: 12px;
            color: #64748b;
        }

        .cliente-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
        }

        .campo {
            margin-bottom: 0;
        }

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

        .table-wrapper {
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8fafc;
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .monto {
            text-align: right;
            font-weight: 700;
        }

        .monto-positivo {
            color: #166534;
        }

        .resumen {
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .resumen-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        .resumen-row:last-child {
            border-bottom: none;
        }

        .resumen-row .label {
            color: #64748b;
            font-weight: 600;
        }

        .resumen-row .valor {
            font-weight: 700;
            color: #1e293b;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #64748b;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .sin-abonos {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
            font-size: 14px;
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
    <div class="header-info">
      @if($empresa && $empresa->logo)
    <img src="{{ asset($empresa->logo) }}" alt="Logo" style="width:60px;height:60px;border-radius:12px;object-fit:contain;" />
@endif
        <div class="header-text">
            <h1>Historial de Abonos</h1>
            <p>Impreso el {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Información del Cliente -->
    <div>
        <p class="seccion-titulo">Información del cliente</p>
        <div class="cliente-info">
            <div class="campo">
                <div class="label">Nombre</div>
                <div class="valor">{{ $cliente->nombre }}</div>
            </div>
            <div class="campo">
                <div class="label">NIT / CC</div>
                <div class="valor">{{ $cliente->nit ?? 'No aplica' }}</div>
            </div>
            <div class="campo">
                <div class="label">Teléfono</div>
                <div class="valor">{{ $cliente->telefono ?? 'No aplica' }}</div>
            </div>
            <div class="campo">
                <div class="label">Email</div>
                <div class="valor">{{ $cliente->email ?? 'No aplica' }}</div>
            </div>
        </div>
    </div>

    <!-- Tabla de Abonos -->
    <div class="table-wrapper">
        <p class="seccion-titulo">Abonos registrados</p>
        @if($abonos->isEmpty())
            <div class="sin-abonos">
                
                Sin abonos registrados en el período seleccionado
            </div>
        @else
            <table>
                <thead>
                    <thead>
    <tr>
        <th>Fecha</th>
        <th>Factura</th>
        <th>Forma de pago</th>
        <th class="monto">Monto</th>
    </tr>
</thead>
                </thead>
                <tbody>
                    @foreach($abonos as $abono)
                    @php
                        $fecha = $abono->created_at;
                        $hora = $fecha->format('g'); // Hora sin cero a izquierda (1-12)
                        $minuto = $fecha->format('i');
                        $ampm = $fecha->format('a'); // 'am' o 'pm'
                        $fechaFormato = $fecha->format('d/m/Y') . ' ' . $hora . ':' . $minuto . ' ' . $ampm;
                    @endphp
                   <tr>
    <td>{{ $fechaFormato }}</td>
    <td>#{{ optional($abono->venta->factura)->numero ?? str_pad($abono->venta_id, 6, '0', STR_PAD_LEFT) }}</td>
    <td style="text-transform:capitalize;">{{ $abono->forma_pago }}</td>
    <td class="monto monto-positivo">${{ number_format($abono->monto, 0, ',', '.') }}</td>
</tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Resumen -->
    @if(!$abonos->isEmpty())
    <div class="resumen">
        <p class="seccion-titulo">Resumen</p>
        <div class="resumen-row">
            <span class="label">Total abonado</span>
            <span class="valor monto-positivo">${{ number_format($abonos->sum('monto'), 0, ',', '.') }}</span>
        </div>
        <div class="resumen-row">
            <span class="label">Cantidad de abonos</span>
            <span class="valor">{{ $abonos->count() }}</span>
        </div>
        <div class="resumen-row">
            <span class="label">Saldo pendiente cliente</span>
            <span class="valor">
                @if($cliente->saldo_pendiente > 0)
                    <span style="color:#b91c1c;">${{ number_format($cliente->saldo_pendiente, 0, ',', '.') }}</span>
                @else
                    <span style="color:#166534;">Al día</span>
                @endif
            </span>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y H:i:s') }}<br>
        Este reporte es válido como soporte oficial de los abonos registrados en el sistema.
    </div>

</div>
</body>
</html>