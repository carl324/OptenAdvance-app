<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Factura {{ $venta->factura->numero ?? '' }}</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    /* Contenedor y tipografía para recibo térmico de 80mm */
    :root{--paper-width:80mm}
    html,body{margin:0;padding:0}
    body{
      font-family:Helvetica, Arial, "Liberation Sans", sans-serif;
      font-size:12px;
      color:#111;
      padding:8px;
      -webkit-print-color-adjust:exact;
      box-sizing:border-box;
    }
    .ticket{
      max-width:var(--paper-width);
      width:100%;
      margin:0 auto;
    }
    .header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;gap:8px}
    .empresa{max-width:58%}
    .cliente{max-width:40%;text-align:right;font-size:11px}
    table{width:100%;border-collapse:collapse;margin-top:6px;font-size:11px}
    th,td{padding:4px 6px;border-bottom:1px dashed #ccc}
    th{font-weight:700;text-align:left}
    .text-right{text-align:right}
    .qty{width:48px;text-align:right}
    .price{width:90px;text-align:right}
    .totales{margin-top:8px;width:100%;max-width:var(--paper-width);}
    .totales table th, .totales table td{border:0;padding:4px 6px}
    .print-hide{display:inline-block;margin-top:8px}
    .muted{color:#666;font-size:11px}

    /* Estilos de impresión */
    @page{size:80mm auto;margin:0}
    @media print{
      html,body{width:var(--paper-width);height:auto;margin:0;padding:0}
      .ticket{max-width:var(--paper-width);padding:4mm}
      .no-print{display:none !important}
      button{display:none}
    }
  </style>
</head>
<body>
  <div class="header">
    <div class="empresa">
      <strong>{{ $empresa->nombre ?? 'Empresa' }}</strong><br>
      NIT: {{ $empresa->nit ?? '-' }}<br>
      Dirección: {{ $empresa->direccion ?? '-' }}<br>
      Tel: {{ $empresa->telefono ?? '-' }}
      <br>Email: {{ $empresa->email ?? '-' }}
      <br>Moneda: {{ $empresa->moneda ?? '-' }}
    </div>
    <div class="cliente">
      <div><strong>Factura:</strong> {{ $venta->factura->numero ?? '-' }}</div>
      <div><strong>Fecha:</strong> {{ optional($venta->factura->fecha_emision ?? $venta->fecha)->format('Y-m-d H:i') }}</div>
      <div style="margin-top:8px"><strong>Cliente:</strong><br>{{ $venta->factura->cliente_nombre ?? $venta->cliente ?? 'Consumidor final' }}<br>{{ $venta->factura->cliente_nit ?? '' }}</div>
    </div>
  </div>

  <div class="ticket">
  <table>
    <thead>
      <tr>
        <th>Producto</th>
        <th class="text-right">Cantidad</th>
        <th class="text-right">Precio</th>
        <th class="text-right">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($venta->detalles as $d)
      <tr>
        <td>{{ optional($d->producto)->nombre ?? 'Producto #' . $d->producto_id }}</td>
        <td class="qty">{{ $d->cantidad }}</td>
        <td class="price">{{ number_format($d->precio_unitario, 0, ',', '.') }}</td>
        <td class="price">{{ number_format($d->subtotal, 0, ',', '.') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div class="totales">
    <table>
      <tbody>
        <tr>
          <td>Subtotal</td>
          <td class="text-right">{{ number_format((($venta->factura->total ?? $venta->total) - ($venta->factura->impuestos ?? 0)), 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td>IVA / impuestos</td>
          <td class="text-right">{{ number_format($venta->factura->impuestos ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <th>Total</th>
          <th class="text-right">{{ number_format($venta->factura->total ?? $venta->total, 0, ',', '.') }}</th>
        </tr>
      </tbody>
    </table>
  </div>

  <div style="margin-top:12px">
    <button onclick="window.print()" class="no-print print-hide">Imprimir</button>
  </div>

</div>

</body>
</html>
