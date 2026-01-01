<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Reportes</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;background:#f7f7f7;padding:20px}
    .card{background:#fff;padding:16px;border-radius:6px;max-width:1100px;margin:0 auto}
    .row{display:flex;gap:12px;align-items:end}
    label{font-weight:600;font-size:14px}
    select,input{padding:8px;border:1px solid #ddd;border-radius:4px}
    button{padding:8px 12px;border-radius:4px;border:0;background:#1976d2;color:#fff;cursor:pointer}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{padding:8px;border:1px solid #eee;text-align:left}
    th{text-background:#fafafa;background:#fafafa}
    .actions{display:flex;gap:8px;margin-top:12px}
  </style>
</head>
<body>
  <div class="card">
    <h2>Reportes</h2>

    <form method="get" action="/reportes">
      <div class="row">
        <div>
          <label for="tipo">Tipo</label><br>
          <select id="tipo" name="tipo">
            <option value="ventas" {{ ($tipo ?? '') === 'ventas' ? 'selected' : '' }}>Ventas</option>
            <option value="ventas_detalle" {{ ($tipo ?? '') === 'ventas_detalle' ? 'selected' : '' }}>Ventas (detalle)</option>
            <option value="inventario" {{ ($tipo ?? '') === 'inventario' ? 'selected' : '' }}>Inventario</option>
          </select>
        </div>

        <div>
          <label for="fecha_inicio">Fecha inicio</label><br>
          <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ $fecha_inicio ?? '' }}">
        </div>

        <div>
          <label for="fecha_fin">Fecha fin</label><br>
          <input type="date" id="fecha_fin" name="fecha_fin" value="{{ $fecha_fin ?? '' }}">
        </div>

        <div style="margin-left:auto">
          <button type="submit">Consultar</button>
        </div>
      </div>
    </form>

    <div class="actions">
      <form method="get" action="/reportes/export" style="margin:0">
        <input type="hidden" name="tipo" value="{{ $tipo ?? 'ventas' }}">
        <input type="hidden" name="fecha_inicio" value="{{ $fecha_inicio ?? '' }}">
        <input type="hidden" name="fecha_fin" value="{{ $fecha_fin ?? '' }}">
        <button type="submit">Exportar CSV</button>
      </form>
    </div>

    <div style="margin-top:12px">
      @if(($tipo ?? '') === 'ventas')
        <table>
          <thead>
            <tr><th>ID</th><th>Fecha</th><th>Estado</th><th class="text-right">Total</th></tr>
          </thead>
    </div>

    {{-- Paginación simple para la vista cuando $data es paginador --}}
    @if(method_exists($data, 'links'))
      <div style="margin-top:12px; display:flex; align-items:center; gap:8px; justify-content:flex-end">
        <div style="font-size:13px;color:#555">Página {{ $data->currentPage() }} de {{ $data->lastPage() }}</div>
        @if($data->previousPageUrl())
          <a href="{{ $data->previousPageUrl() }}" style="padding:6px 10px;background:#eee;border-radius:4px;color:#333;text-decoration:none">Anterior</a>
        @endif
        @if($data->nextPageUrl())
          <a href="{{ $data->nextPageUrl() }}" style="padding:6px 10px;background:#1976d2;color:#fff;border-radius:4px;text-decoration:none">Siguiente</a>
        @endif
      </div>
    @endif
          <tbody>
            @foreach($data as $r)
            <tr>
              <td>{{ $r->id }}</td>
              <td>{{ optional($r->fecha)->format('Y-m-d H:i') }}</td>
              <td>{{ $r->estado }}</td>
              <td style="text-align:right">{{ number_format($r->total ?? 0,0,',','.') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>

      @elseif(($tipo ?? '') === 'ventas_detalle')
        <table>
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Precio unit.</th>
              <th>Subtotal</th>
              @if($empresa && $empresa->cobra_iva)
              <th>IVA</th>
              @endif
              <th>Venta ID</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            @foreach($data as $r)
            <tr>
              <td>{{ optional($r->producto)->nombre ?? ('#' . $r->producto_id) }}</td>
              <td>{{ $r->cantidad }}</td>
              <td>{{ number_format($r->precio_unitario,0,',','.') }}</td>
              <td>{{ number_format($r->subtotal,0,',','.') }}</td>
              @if($empresa && $empresa->cobra_iva)
              <td>{{ number_format($r->iva ?? 0,2,'.',',') }}</td>
              @endif
              <td>{{ $r->venta_id }}</td>
              <td>{{ optional($r->venta->fecha)->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>

      @elseif(($tipo ?? '') === 'inventario')
        <table>
          <thead><tr><th>Producto</th><th>Stock</th></tr></thead>
          <tbody>
            @foreach($data as $r)
            <tr>
              <td>{{ $r->nombre }}</td>
              <td>{{ $r->stock }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p>No hay datos para mostrar.</p>
      @endif
    </div>
  </div>
</body>
</html>
