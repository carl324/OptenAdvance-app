@extends('layouts.app')

@section('title', 'Registrar Producto')

@section('content')
  <style>
    body{font-family:Arial,Helvetica,sans-serif;background:#f7f7f7;padding:20px}
    .card{background:#fff;padding:16px;border-radius:6px;max-width:1200px;margin:0 auto}
    .row{display:flex;gap:12px;align-items:end;flex-wrap:wrap}
    label{font-weight:600;font-size:14px;display:block;margin-bottom:4px}
    select,input{padding:8px;border:1px solid #ddd;border-radius:4px}
    button{padding:8px 12px;border-radius:4px;border:0;background:#1976d2;color:#fff;cursor:pointer}
    button:hover{background:#1565c0}
    table{width:100%;border-collapse:collapse;margin-top:12px;font-size:13px}
    th,td{padding:8px;border:1px solid #eee;text-align:left}
    th{background:#fafafa;font-weight:600}
    .text-right{text-align:right}
    .text-center{text-align:center}
    .actions{display:flex;gap:8px;margin-top:12px}
    .badge{display:inline-block;padding:4px 8px;border-radius:3px;font-size:12px;font-weight:500}
    .badge-entrada{background:#e8f5e9;color:#2e7d32}
    .badge-salida{background:#ffebee;color:#c62828}
    .badge-completada{background:#e3f2fd;color:#1565c0}
    .badge-anulada{background:#fce4ec;color:#c2185b}
    .pagination{margin-top:12px;display:flex;align-items:center;gap:8px;justify-content:flex-end}
    .pagination a{padding:6px 10px;background:#eee;border-radius:4px;color:#333;text-decoration:none}
    .pagination a:hover{background:#ddd}
    .pagination .active{background:#1976d2;color:#fff}
    .btn-link{background:transparent;color:#1976d2;border:1px solid #1976d2;padding:4px 8px;border-radius:3px;cursor:pointer;font-size:12px}
    .btn-link:hover{background:#e3f2fd}

    /* Modal */
    .modal{display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);align-items:center;justify-content:center}
    .modal.show{display:flex}
    .modal-content{background:#fff;padding:20px;border-radius:8px;max-width:800px;width:90%;max-height:80vh;overflow-y:auto;position:relative}
    .modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;border-bottom:2px solid #eee;padding-bottom:12px}
    .modal-title{font-size:18px;font-weight:600;margin:0}
    .modal-close{background:transparent;border:0;font-size:24px;cursor:pointer;color:#666;padding:0;width:30px;height:30px;line-height:30px}
    .modal-close:hover{color:#333}
    .modal-body{margin-bottom:16px}
    .modal-footer{display:flex;justify-content:flex-end;border-top:1px solid #eee;padding-top:12px}
  </style>
</head>
<body>
  <div class="card">
    <h2>Reportes</h2>
    @if(isset($es_dato_historico) && $es_dato_historico)
      <div style="font-size:12px;color:#666;margin-bottom:8px">Nota: es_dato_historico = true — Los reportes muestran datos históricos (no reflejan necesariamente el estado actual en tiempo real).</div>
    @endif

    <form method="get" action="/reportes">
      <div class="row">
        <div>
          <label for="tipo">Tipo de Reporte</label>
          <select id="tipo" name="tipo">
            <option value="ventas" {{ ($tipo ?? '') === 'ventas' ? 'selected' : '' }}>Ventas</option>
            <option value="inventario_movimientos" {{ ($tipo ?? '') === 'inventario_movimientos' ? 'selected' : '' }}>Movimientos de Inventario</option>
          </select>
        </div>

        <div>
          <label for="fecha_inicio">Fecha inicio</label>
          <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ $fecha_inicio ?? '' }}">
        </div>

        <div>
          <label for="fecha_fin">Fecha fin</label>
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
        <input type="hidden" name="estado" value="{{ request()->input('estado', '') }}">
        <input type="hidden" name="order" value="{{ request()->input('order', 'desc') }}">
        <button type="submit">Exportar Excel</button>
      </form>
    </div>

    <div style="margin-top:16px">
      @if(($tipo ?? '') === 'ventas')
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>N° Factura</th>
              <th>Cliente</th>
              <th class="text-right">Total</th>
              <th class="text-center">Estado</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($data as $r)
            <tr data-origen_reporte="{{ $r->origen_reporte ?? '' }}">
              <td>{{ $r->id }}</td>
              <td title="Origen: {{ $r->origen_reporte ?? 'n/a' }}">{{ optional($r->fecha)->format('Y-m-d H:i') }}</td>
              <td>{{ $r->factura->numero ?? '-' }}</td>
              <td>{{ $r->factura->cliente_nombre ?? $r->cliente ?? '-' }}</td>
              <td class="text-right">{{ number_format($r->total, 0, ',', '.') }}</td>
              <td class="text-center">
                @if($r->estado === 'completada')
                  <span class="badge badge-completada">Completada</span>
                @elseif($r->estado === 'anulada')
                  <span class="badge badge-anulada">Anulada</span>
                @else
                  <span class="badge">{{ $r->estado }}</span>
                @endif
              </td>
              <td class="text-center">
                <button class="btn-link" onclick="verDetalles({{ $r->id }})">Ver Detalles</button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" style="text-align:center;padding:20px;color:#999">No hay datos para el rango seleccionado</td>
            </tr>
            @endforelse
          </tbody>
        </table>

      @elseif(($tipo ?? '') === 'inventario_movimientos')
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Producto</th>
              <th class="text-center">Tipo</th>
              <th class="text-right">Cantidad</th>
              <th>Origen</th>
              <th>Referencia</th>
              <th>Descripción</th>
            </tr>
          </thead>
          <tbody>
            @forelse($data as $r)
            <tr data-origen_reporte="{{ $r->origen_reporte ?? '' }}">
              <td>{{ $r->id }}</td>
              <td title="Origen: {{ $r->origen_reporte ?? 'n/a' }}">{{ $r->created_at ? date('Y-m-d H:i', strtotime($r->created_at)) : '-' }}</td>
              <td title="Producto histórico en el movimiento">{{ $r->producto_nombre ?? ('#' . $r->producto_id) }}</td>
              <td class="text-center">
                @if($r->tipo === 'entrada')
                  <span class="badge badge-entrada">Entrada</span>
                @elseif($r->tipo === 'salida')
                  <span class="badge badge-salida">Salida</span>
                @else
                  <span class="badge">{{ $r->tipo }}</span>
                @endif
              </td>
              <td class="text-right">{{ number_format($r->cantidad, 0, ',', '.') }}</td>
              <td>{{ $r->origen ?? '-' }}</td>
              <td>{{ $r->referencia_id ?? '-' }}</td>
              <td>{{ $r->descripcion ?? '-' }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="8" style="text-align:center;padding:20px;color:#999">No hay datos para el rango seleccionado</td>
            </tr>
            @endforelse
          </tbody>
        </table>

      @else
        <p style="text-align:center;padding:20px;color:#999">Selecciona un tipo de reporte para continuar.</p>
      @endif
    </div>

    {{-- Paginación --}}
    @if(isset($data) && method_exists($data, 'links'))
      <div class="pagination">
        <div style="font-size:13px;color:#555">
          Página {{ $data->currentPage() }} de {{ $data->lastPage() }} 
          ({{ $data->total() }} registros)
        </div>
        @if($data->previousPageUrl())
          <a href="{{ $data->previousPageUrl() }}">« Anterior</a>
        @endif
        @if($data->nextPageUrl())
          <a href="{{ $data->nextPageUrl() }}" class="active">Siguiente »</a>
        @endif
      </div>
    @endif
  </div>

  {{-- Modal de Detalles --}}
  <div id="modalDetalles" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Detalles de Venta #<span id="modalVentaId"></span></h3>
        <button class="modal-close" onclick="cerrarModal()">&times;</button>
      </div>
      <div class="modal-body">
        <div id="modalLoading" style="text-align:center;padding:20px;color:#999">Cargando detalles...</div>
        <div id="modalContent" style="display:none">
          <table style="font-size:12px">
            <thead>
              <tr>
                <th>Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Precio Unit.</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right" id="headerIva" style="display:none">IVA</th>
                <th class="text-right">Total</th>
              </tr>
            </thead>
            <tbody id="modalTableBody"></tbody>
            <tfoot id="modalTableFoot"></tfoot>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button onclick="cerrarModal()">Cerrar</button>
      </div>
    </div>
  </div>

  <script>
    function verDetalles(ventaId) {
      const modal = document.getElementById('modalDetalles');
      const loading = document.getElementById('modalLoading');
      const content = document.getElementById('modalContent');
      const ventaIdSpan = document.getElementById('modalVentaId');
      
      // Mostrar modal
      modal.classList.add('show');
      ventaIdSpan.textContent = ventaId;
      loading.style.display = 'block';
      content.style.display = 'none';

      // Hacer petición AJAX
      fetch('/reportes/ventas/' + ventaId + '/detalles')
        .then(response => {
          if (!response.ok) throw new Error('Error al cargar detalles');
          return response.json();
        })
        .then(data => {
          mostrarDetalles(data);
          loading.style.display = 'none';
          content.style.display = 'block';
        })
        .catch(error => {
          loading.innerHTML = '<p style="color:#c62828">Error al cargar los detalles</p>';
          console.error(error);
        });
    }

    function mostrarDetalles(data) {
      const tbody = document.getElementById('modalTableBody');
      const tfoot = document.getElementById('modalTableFoot');
      const headerIva = document.getElementById('headerIva');
      const cobraIva = data.cobra_iva;

      tbody.innerHTML = '';
      tfoot.innerHTML = '';

      // Mostrar/ocultar columna IVA
      if (cobraIva) {
        headerIva.style.display = '';
      } else {
        headerIva.style.display = 'none';
      }

      let totalCantidad = 0;
      let totalSubtotal = 0;
      let totalIva = 0;
      let totalTotal = 0;

      data.detalles.forEach(d => {
        const total = parseFloat(d.subtotal) + (parseFloat(d.iva) || 0);
        totalCantidad += parseFloat(d.cantidad);
        totalSubtotal += parseFloat(d.subtotal);
        totalIva += parseFloat(d.iva) || 0;
        totalTotal += total;

        const tr = document.createElement('tr');
        let html = `
          <td>${d.producto ? d.producto.nombre : '#' + d.producto_id}</td>
          <td class="text-center">${d.cantidad}</td>
          <td class="text-right">${formatNumber(d.precio_unitario)}</td>
          <td class="text-right">${formatNumber(d.subtotal)}</td>
        `;
        
        if (cobraIva) {
          html += `<td class="text-right">${formatNumber(d.iva || 0)}</td>`;
        }
        
        html += `<td class="text-right">${formatNumber(total)}</td>`;
        tr.innerHTML = html;
        tbody.appendChild(tr);
      });

      // Agregar totales
      const trTotal = document.createElement('tr');
      trTotal.style.fontWeight = 'bold';
      let htmlTotal = `
        <td>TOTALES</td>
        <td class="text-center">${formatNumber(totalCantidad, 0)}</td>
        <td></td>
        <td class="text-right">${formatNumber(totalSubtotal)}</td>
      `;
      
      if (cobraIva) {
        htmlTotal += `<td class="text-right">${formatNumber(totalIva)}</td>`;
      }
      
      htmlTotal += `<td class="text-right">${formatNumber(totalTotal)}</td>`;
      trTotal.innerHTML = htmlTotal;
      tfoot.appendChild(trTotal);
    }

    function cerrarModal() {
      const modal = document.getElementById('modalDetalles');
      modal.classList.remove('show');
    }

    function formatNumber(num, decimals = 0) {
      const n = parseFloat(num) || 0;
      return n.toLocaleString('es-CO', {minimumFractionDigits: decimals, maximumFractionDigits: decimals});
    }

    // Cerrar modal al hacer clic fuera
    window.onclick = function(event) {
      const modal = document.getElementById('modalDetalles');
      if (event.target === modal) {
        cerrarModal();
      }
    }
  </script>
@endsection