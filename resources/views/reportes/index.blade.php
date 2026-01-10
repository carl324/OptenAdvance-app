@extends('layouts.app')

@section('title', 'Reportes')

@section('content')

<section class="section">
  <div class="container-fluid">
    <!-- Title -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="title">
            <h2>📊 Reportes</h2>
          </div>
        </div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reportes</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row" id="statsContainer">
      <div class="col-xl-3 col-lg-4 col-sm-6">
        <div class="icon-card mb-30">
          <div class="icon purple">
            <i class="lni lni-reload"></i>
          </div>
          <div class="content">
            <h3 class="text-bold mb-10" id="statMovimientos">0</h3>
            <p class="text-sm text-success">
              <span class="text-gray">Movimientos</span>
            </p>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-lg-4 col-sm-6">
        <div class="icon-card mb-30">
          <div class="icon primary">
            <i class="lni lni-inbox"></i>
          </div>
          <div class="content">
            <h3 class="text-bold mb-10" id="statEntradas">0</h3>
            <p class="text-sm text-success">
              <span class="text-gray">Entradas</span>
            </p>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-lg-4 col-sm-6">
        <div class="icon-card mb-30">
          <div class="icon orange">
            <i class="lni lni-delivery"></i>
          </div>
          <div class="content">
            <h3 class="text-bold mb-10" id="statSalidas">0</h3>
            <p class="text-sm text-danger">
              <span class="text-gray">Salidas</span>
            </p>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-lg-4 col-sm-6">
        <div class="icon-card mb-30">
          <div class="icon success">
            <i class="lni lni-dollar"></i>
          </div>
          <div class="content">
            <h3 class="text-bold mb-10" id="statIngresos">0</h3>
            <p class="text-sm text-danger">
              <span class="text-gray">Ingresos</span>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters Card -->
    <div class="card-style mb-30">
      <div class="d-flex flex-wrap align-items-start gap-3">

        <!-- Fecha inicio y fin -->
        <div class="d-flex gap-3">
          <div class="select-style-1">
            <label class="text-dark mb-2 d-block">Desde</label>
            <input type="date" class="form-control" id="dateFrom" value="">
          </div>
          <div class="select-style-1">
            <label class="text-dark mb-2 d-block">Hasta</label>
            <input type="date" class="form-control" id="dateTo" value="">
          </div>
        </div>

        <!-- Tipo -->
        <div class="select-style-1">
          <label class="text-dark mb-2 d-block">Tipo de reporte</label>
          <div class="select-position select-sm">
            <select class="light-bg" id="reportType">
              <option value="ventas">Ventas</option>
              <option value="movimientos">Inventario</option>
            </select>
          </div>
        </div>

        <!-- Acciones -->
        <div class="ms-auto mt-4 pt-2">
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" onclick="limpiarFiltros()">
              Limpiar
            </button>

            <button class="btn btn-sm btn-outline-success" onclick="exportarDatos()">
              <i class="lni lni-download me-1"></i>
              Exportar
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- Table Card -->
    <div class="col-lg-12">
      <div class="card-style mb-30">
        <div class="title d-flex flex-wrap align-items-center justify-content-between">
          <div class="left">
            <h6 class="text-medium mb-30" id="tableTitle">Historial de Ventas</h6>
          </div>
          <div class="right">
            <div class="input-group input-group-sm search-pos">
              <span class="input-group-text bg-light border-0">
                <i class="lni lni-search-alt"></i>
              </span>
              <input type="text" class="form-control bg-light border-0" id="searchInput" placeholder="Buscar..." />
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table top-selling-table">
            <thead>
              <tr>
                <th>
                  <h6 class="text-sm text-medium">ID</h6>
                </th>
                <th class="min-width">
                  <h6 class="text-sm text-medium">Fecha</h6>
                </th>
                <th class="min-width" id="colFactura">
                  <h6 class="text-sm text-medium">N° Factura</h6>
                </th>
                <th class="min-width" id="colCliente">
                  <h6 class="text-sm text-medium">Cliente</h6>
                </th>
                <th class="min-width" id="colTotal">
                  <h6 class="text-sm text-medium">Total</h6>
                </th>
                <th class="min-width" id="colEstado" style="display:none">
                  <h6 class="text-sm text-medium">Estado</h6>
                </th>
                <th class="min-width" id="colProducto" style="display:none">
                  <h6 class="text-sm text-medium">Producto</h6>
                </th>
                <th class="min-width" id="colTipo" style="display:none">
                  <h6 class="text-sm text-medium">Tipo</h6>
                </th>
                <th class="min-width" id="colCantidad" style="display:none">
                  <h6 class="text-sm text-medium">Cantidad</h6>
                </th>
                <th>
                  <h6 class="text-sm text-medium text-end">Acciones</h6>
                </th>
              </tr>
            </thead>
            <tbody id="tableBody">
              <tr>
                <td colspan="10" style="text-align: center; padding: 40px; color: #999;">
                  <i class="lni lni-inbox" style="font-size: 32px; margin-bottom: 10px;"></i>
                  <p>Cargando datos...</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
          <div>
            <span class="text-sm text-gray" id="paginationInfo">Página 1 de 1</span>
          </div>
          <div id="paginationButtons">
            <!-- Se generará dinámicamente -->
          </div>
        </div>
      </div>
    </div>

  </div>
</section>



<script>
const csrf = '{{ csrf_token() }}';
let currentPage = 1;
let currentType = 'ventas';
let allData = [];

document.addEventListener('DOMContentLoaded', function() {
  // Establecer fechas por defecto (últimos 30 días)
  const today = new Date();
  const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));

  document.getElementById('dateTo').value = today.toISOString().split('T')[0];
  document.getElementById('dateFrom').value = thirtyDaysAgo.toISOString().split('T')[0];

  // Event listeners
  document.getElementById('dateFrom').addEventListener('change', cargarDatos);
  document.getElementById('dateTo').addEventListener('change', cargarDatos);
  document.getElementById('reportType').addEventListener('change', cambiarTipo);
  document.getElementById('searchInput').addEventListener('input', filtrarTabla);

  // Cargar datos iniciales
  cargarDatos();
});

function cambiarTipo() {
  currentType = document.getElementById('reportType').value;
  currentPage = 1;
  actualizarColumnasTabla();
  cargarDatos();
}

function actualizarColumnasTabla() {
  const isVentas = currentType === 'ventas';
  document.getElementById('tableTitle').textContent = isVentas ? 'Historial de Ventas' : 'Movimientos de Inventario';

  // Mostrar/ocultar columnas según tipo
  document.getElementById('colFactura').style.display = isVentas ? '' : 'none';
  document.getElementById('colCliente').style.display = isVentas ? '' : 'none';
  document.getElementById('colEstado').style.display = isVentas ? '' : 'none';
  document.getElementById('colProducto').style.display = !isVentas ? '' : 'none';
  document.getElementById('colTipo').style.display = !isVentas ? '' : 'none';
  document.getElementById('colCantidad').style.display = !isVentas ? '' : 'none';

  // Cambiar label del total
  document.getElementById('colTotal').querySelector('h6').textContent = isVentas ? 'Total' : 'Cantidad';
}

async function cargarDatos() {
  const dateFrom = document.getElementById('dateFrom').value;
  const dateTo = document.getElementById('dateTo').value;

  // Mostrar loading
  document.getElementById('tableBody').innerHTML = `
    <tr>
      <td colspan="10" style="text-align: center; padding: 40px; color: #999;">
        <i class="lni lni-reload" style="font-size: 32px; animation: spin 1s linear infinite;"></i>
        <p>Cargando datos...</p>
      </td>
    </tr>
  `;

  try {
    const params = new URLSearchParams({
      tipo: currentType,
      fecha_inicio: dateFrom,
      fecha_fin: dateTo,
      page: currentPage
    });

    const response = await fetch(`/api/reportes?${params}`, {
      headers: {
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
      }
    });

    const data = await response.json();

    if (data.success) {
      allData = data.data;
      actualizarEstadisticas(data.stats);
      actualizarTabla(data.data);
      actualizarPaginacion(data.pagination);
    } else {
      mostrarError('Error al cargar datos');
    }
  } catch (error) {
    console.error('Error:', error);
    mostrarError('Error al conectar con el servidor');
  }
}

function actualizarEstadisticas(stats) {
  document.getElementById('statMovimientos').textContent = formatNumber(stats.movimientos, 0);
  document.getElementById('statEntradas').textContent = formatNumber(stats.entradas, 0);
  document.getElementById('statSalidas').textContent = formatNumber(stats.salidas, 0);
  document.getElementById('statIngresos').textContent = formatNumber(stats.ingresos, 0);
}

function actualizarTabla(data) {
  const tbody = document.getElementById('tableBody');
  const isVentas = currentType === 'ventas';

  if (data.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="10" style="text-align: center; padding: 40px; color: #999;">
          <i class="lni lni-inbox" style="font-size: 32px; margin-bottom: 10px;"></i>
          <p>No hay datos para este período</p>
        </td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = data.map(row => {
    if (isVentas) {
      return `
        <tr>
          <td><p class="text-sm">${row.id}</p></td>
          <td><p class="text-sm">${formatDate(row.fecha)}</p></td>
          <td><p class="text-sm">${row.factura_numero || '-'}</p></td>
          <td><p class="text-sm">${row.cliente_nombre || '-'}</p></td>
          <td><p class="text-sm">${formatNumber(row.total)}</p></td>
          <td>
            <span class="status-btn ${row.estado === 'completada' ? 'success-btn' : 'close-btn'}">
              ${row.estado === 'completada' ? 'Completada' : 'Anulada'}
            </span>
          </td>
          <td>
            <div class="action">
              <button onclick="verDetallesVenta(${row.id})" title="Ver detalles">
                <i class="lni lni-eye"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    } else {
      return `
        <tr>
          <td><p class="text-sm">${row.id}</p></td>
          <td><p class="text-sm">${formatDate(row.created_at)}</p></td>
          <td><p class="text-sm">${row.producto_nombre || 'Producto #' + row.producto_id}</p></td>
          <td>
            <span class="status-btn ${row.tipo === 'entrada' ? 'success-btn' : 'close-btn'}">
              ${row.tipo === 'entrada' ? 'Entrada' : 'Salida'}
            </span>
          </td>
          <td><p class="text-sm">${formatNumber(row.cantidad, 0)}</p></td>
          <td>
            <div class="action">
              <button onclick="alert('Detalles del movimiento ID: ' + ${row.id})" title="Ver detalles">
                <i class="lni lni-eye"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    }
  }).join('');
}

function actualizarPaginacion(pagination) {
  const buttonsDiv = document.getElementById('paginationButtons');
  const infoDiv = document.getElementById('paginationInfo');

  infoDiv.textContent = `Página ${pagination.current_page} de ${pagination.last_page}`;

  let html = '';
  if (pagination.current_page > 1) {
    html += `<button class="btn btn-outline-secondary me-1" onclick="irAPagina(${pagination.current_page - 1})"><i class="lni lni-chevron-left"></i></button>`;
  } else {
    html += `<button class="btn btn-outline-secondary me-1" disabled><i class="lni lni-chevron-left"></i></button>`;
  }

  for (let i = 1; i <= pagination.last_page && i <= 5; i++) {
    if (i === pagination.current_page) {
      html += `<button class="btn btn-primary me-1">${i}</button>`;
    } else {
      html += `<button class="btn btn-outline-secondary me-1" onclick="irAPagina(${i})">${i}</button>`;
    }
  }

  if (pagination.current_page < pagination.last_page) {
    html += `<button class="btn btn-outline-secondary" onclick="irAPagina(${pagination.current_page + 1})"><i class="lni lni-chevron-right"></i></button>`;
  } else {
    html += `<button class="btn btn-outline-secondary" disabled><i class="lni lni-chevron-right"></i></button>`;
  }

  buttonsDiv.innerHTML = html;
}

function irAPagina(page) {
  currentPage = page;
  cargarDatos();
}

function filtrarTabla() {
  const searchTerm = document.getElementById('searchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#tableBody tr');

  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(searchTerm) ? '' : 'none';
  });
}

function limpiarFiltros() {
  const today = new Date();
  const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));

  document.getElementById('dateFrom').value = thirtyDaysAgo.toISOString().split('T')[0];
  document.getElementById('dateTo').value = today.toISOString().split('T')[0];
  document.getElementById('reportType').value = 'ventas';
  document.getElementById('searchInput').value = '';
  currentType = 'ventas';
  currentPage = 1;
  actualizarColumnasTabla();
  cargarDatos();
}

function exportarDatos() {
  const dateFrom = document.getElementById('dateFrom').value;
  const dateTo = document.getElementById('dateTo').value;

  const params = new URLSearchParams({
    tipo: currentType,
    fecha_inicio: dateFrom,
    fecha_fin: dateTo
  });

  window.location.href = `/api/reportes/export?${params}`;
}

function verDetallesVenta(ventaId) {
  window.location.href = `/ventas/${ventaId}/factura`;
}

function mostrarError(mensaje) {
  document.getElementById('tableBody').innerHTML = `
    <tr>
      <td colspan="10" style="text-align: center; padding: 40px; color: #c62828;">
        <i class="lni lni-close-circle" style="font-size: 32px; margin-bottom: 10px;"></i>
        <p>${mensaje}</p>
      </td>
    </tr>
  `;
}

function formatNumber(num, decimals = 0) {
  const n = parseFloat(num) || 0;
  return n.toLocaleString('es-CO', {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals
  });
}

function formatDate(dateString) {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('es-CO') + ' ' + date.toLocaleTimeString('es-CO', {hour: '2-digit', minute: '2-digit'});
}
</script>

<style>
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>

@endsection