@extends('layouts.app')

@section('title', 'Reportes')

@section('content')

<section class="section">
  <div class="container-fluid">
    <!-- Title -->
    <div class="title-wrapper pt-30">
      
    </div>

    <!-- Stats Cards -->
    <div class="row" id="statsContainer">
      <div class="col-xl-3 col-lg-4 col-sm-6" id="cardMovimientos">
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

      <div class="col-xl-3 col-lg-4 col-sm-6" id="cardEntradas">
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

      <div class="col-xl-3 col-lg-4 col-sm-6" id="cardSalidas">
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

      <div class="col-xl-3 col-lg-4 col-sm-6" id="cardIngresos">
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
              <option value="cajas">Caja</option>
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
                <th class="min-width">
                  <h6 class="text-sm text-medium">Fecha</h6>
                </th>
                <!-- Columnas VENTAS -->
                <th class="min-width" id="colFactura">
                  <h6 class="text-sm text-medium">N° Factura</h6>
                </th>
                <th class="min-width" id="colCliente">
                  <h6 class="text-sm text-medium">Cliente</h6>
                </th>
                <th class="min-width" id="colEstado">
                  <h6 class="text-sm text-medium">Estado</h6>
                </th>
                <!-- Columnas INVENTARIO -->
                <th class="min-width" id="colProducto" style="display:none">
                  <h6 class="text-sm text-medium">Producto</h6>
                </th>
                <th class="min-width" id="colTipo" style="display:none">
                  <h6 class="text-sm text-medium">Tipo</h6>
                </th>
                <th class="min-width" id="colOrigen" style="display:none">
                  <h6 class="text-sm text-medium">Origen</h6>
                </th>
                <!-- Total/Cantidad - común pero cambia label -->
                <th class="min-width" id="colTotal">
                  <h6 class="text-sm text-medium">Total</h6>
                </th>
                <th id="colAcciones">
                  <h6 class="text-sm text-medium text-center">Acciones</h6>
                </th>
              </tr>
            </thead>
            <tbody id="tableBody">
              <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                  <i class="lni lni-inbox" style="font-size: 32px; margin-bottom: 10px;"></i>
                  <p>Cargando datos...</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
          <div>
            <!-- Información vacía para mantener layout -->
          </div>
          <div class="pagination">
            <button id="btnPrevPage">
              <i class="lni lni-chevron-left"></i>
            </button>
            <span class="page-info" id="paginationInfo">Página 1 de 1</span>
            <button id="btnNextPage">
              <i class="lni lni-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ========== Modal Límite Exportación start ========== -->
<div class="modal-overlay" id="exportLimitModal">
  <div class="modal-content">
    <div class="modal-header">
      <div class="icon-warning">
        <i class="lni lni-warning"></i>
      </div>
      <div class="modal-header-text">
        <h3>Límite de exportación alcanzado</h3>
      </div>
    </div>
    
    <div class="modal-body">
      <div class="warning-message">
        Por seguridad, el sistema permite exportar hasta 10.000 registros por archivo.
        Para exportaciones mayores, reduzca el rango de fechas o contacte a soporte.
      </div>
    </div>
    
    <div class="modal-footer">
      <button class="modal-btn modal-btn-cancel" id="closeExportLimitModal" type="button">
        Cerrar
      </button>
      <a class="modal-btn modal-btn-primary" id="contactSupportBtn" href="{{ route('soporte.index') }}">
        Contactar soporte
      </a>
      
    </div>
  </div>
</div>
<!-- ========== Modal Límite Exportación end ========== -->



<script>
const csrf = '{{ csrf_token() }}';
let currentPage = 1;
let currentType = 'ventas';
let allData = [];
let allStats = {
  movimientos: 0,
  entradas: 0,
  salidas: 0,
  ingresos: 0
};

let lastValidDates = { from: '', to: '' };
let currentSearchTerm = '';
let searchTimeout;
let isDebouncingSearch = false;
let isFetchingData = false;
let lastPagination = null;

let dataAbortController = null;
let statsAbortController = null;
let dataRequestSeq = 0;
let statsRequestSeq = 0;

document.addEventListener('DOMContentLoaded', function() {
  // Establecer fechas por defecto (últimos 30 días)
  const today = new Date();
  const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));

  const toValue = today.toISOString().split('T')[0];
  const fromValue = thirtyDaysAgo.toISOString().split('T')[0];

  document.getElementById('dateTo').value = toValue;
  document.getElementById('dateFrom').value = fromValue;
  lastValidDates = { from: fromValue, to: toValue };

  // Event listeners
  document.getElementById('dateFrom').addEventListener('change', () => onDateChange('from'));
  document.getElementById('dateTo').addEventListener('change', () => onDateChange('to'));
  document.getElementById('reportType').addEventListener('change', cambiarTipo);

  // Búsqueda con debounce de 300ms
  const searchInput = document.getElementById('searchInput');
  searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const searchTerm = this.value.trim();
    isDebouncingSearch = true;
    renderPagination();
    searchTimeout = setTimeout(() => {
      isDebouncingSearch = false;
      buscarEnTabla(searchTerm);
      renderPagination();
    }, 300);
  });

  // Cargar estadísticas e inicializar datos
  aplicarFechas();

  const exportLimitModal = document.getElementById('exportLimitModal');
  const closeExportLimitModal = document.getElementById('closeExportLimitModal');
  const contactSupportBtn = document.getElementById('contactSupportBtn');

  if (closeExportLimitModal) {
    closeExportLimitModal.addEventListener('click', hideExportLimitModal);
  }
  if (contactSupportBtn) {
    contactSupportBtn.addEventListener('click', hideExportLimitModal);
  }
  if (exportLimitModal) {
    exportLimitModal.addEventListener('click', (event) => {
      if (event.target === exportLimitModal) {
        hideExportLimitModal();
      }
    });
  }
});

function showExportLimitModal() {
  const modal = document.getElementById('exportLimitModal');
  if (modal) {
    modal.classList.add('active');
  }
}

function hideExportLimitModal() {
  const modal = document.getElementById('exportLimitModal');
  if (modal) {
    modal.classList.remove('active');
  }
}

function onDateChange(sourceField) {
  const validation = validateAndNormalizeDates(true, sourceField);
  if (!validation.valid) {
    restoreDateInputs();
    return;
  }
  lastValidDates = validation.normalized;
  aplicarFechas();
}

function cambiarTipo() {
  cancelSearchDebounce();
  currentType = document.getElementById('reportType').value;
  currentPage = 1;
  actualizarColumnasTabla();
  cargarDatos();
}

/**
 * Aplica los cambios de fecha sin recargar nada - AJAX puro
 * Carga estadísticas y tabla en paralelo
 */
function aplicarFechas() {
  const validation = validateAndNormalizeDates(true);
  if (!validation.valid) {
    restoreDateInputs();
    return;
  }
  lastValidDates = validation.normalized;
  currentPage = 1; // Resetear a página 1 cuando cambian fechas
  cargarEstadisticas(validation.normalized);
  cargarDatos(validation.normalized);
}

function actualizarColumnasTabla() {
  const isVentas = currentType === 'ventas';
  const isMovimientos = currentType === 'movimientos';
  const isCajas = currentType === 'cajas';

  if (isVentas) {
    document.getElementById('tableTitle').textContent = 'Historial de Ventas';
    // Restaurar texto del primer th a 'Fecha'
    const firstTh = document.querySelector('.top-selling-table thead tr th');
    if (firstTh && firstTh.querySelector('h6')) {
      firstTh.querySelector('h6').textContent = 'Fecha';
    }
    // Ventas: mostrar Factura, Cliente, Estado | ocultar Producto, Tipo
    document.getElementById('colFactura').style.display = '';
    document.getElementById('colCliente').style.display = '';
    document.getElementById('colEstado').style.display = '';
    document.getElementById('colProducto').style.display = 'none';
    document.getElementById('colTipo').style.display = 'none';
    document.getElementById('colOrigen').style.display = 'none';
    document.getElementById('colTotal').querySelector('h6').textContent = 'Total';
    document.getElementById('colAcciones').style.display = '';
  } else if (isMovimientos) {
    document.getElementById('tableTitle').textContent = 'Movimientos de Inventario';
    // Restaurar texto del primer th a 'Fecha'
    const firstTh = document.querySelector('.top-selling-table thead tr th');
    if (firstTh && firstTh.querySelector('h6')) {
      firstTh.querySelector('h6').textContent = 'Fecha';
    }
    // Inventario: mostrar Producto, Tipo | ocultar Factura, Cliente, Estado
    document.getElementById('colFactura').style.display = 'none';
    document.getElementById('colCliente').style.display = 'none';
    document.getElementById('colEstado').style.display = 'none';
    document.getElementById('colProducto').style.display = '';
    document.getElementById('colTipo').style.display = '';
    document.getElementById('colOrigen').style.display = '';
    document.getElementById('colTotal').querySelector('h6').textContent = 'Cantidad';
    document.getElementById('colAcciones').style.display = 'none';
  } else if (isCajas) {
    document.getElementById('tableTitle').textContent = 'Cierres de Caja';
    // Mantener el <thead> fijo: mostrar/ocultar columnas existentes y ajustar textos
    const firstTh = document.querySelector('.top-selling-table thead tr th');
    if (firstTh && firstTh.querySelector('h6')) {
      firstTh.querySelector('h6').textContent = 'Fecha apertura';
    }

    // Usar los ids existentes para mostrar las columnas necesarias para 'cajas'
    // Fecha cierre <- colFactura
    document.getElementById('colFactura').style.display = '';
    const hf = document.getElementById('colFactura').querySelector('h6');
    if (hf) hf.textContent = 'Fecha cierre';

    // Usuario <- colCliente
    document.getElementById('colCliente').style.display = '';
    const hc = document.getElementById('colCliente').querySelector('h6');
    if (hc) hc.textContent = 'Usuario';

    // Total ventas <- colEstado
    document.getElementById('colEstado').style.display = '';
    const hEstado = document.getElementById('colEstado').querySelector('h6');
    if (hEstado) hEstado.textContent = 'Total ventas';

    // Total efectivo <- colProducto
    document.getElementById('colProducto').style.display = '';
    const hProd = document.getElementById('colProducto').querySelector('h6');
    if (hProd) hProd.textContent = 'Total efectivo';

    // Monto cierre calculado <- colTipo
    document.getElementById('colTipo').style.display = '';
    const hTipo = document.getElementById('colTipo').querySelector('h6');
    if (hTipo) hTipo.textContent = 'Monto cierre calculado';

    // Monto cierre real <- colOrigen
    document.getElementById('colOrigen').style.display = '';
    const hOrigen = document.getElementById('colOrigen').querySelector('h6');
    if (hOrigen) hOrigen.textContent = 'Monto cierre real';

    // Diferencia <- colTotal
    document.getElementById('colTotal').style.display = '';
    const hTotal = document.getElementById('colTotal').querySelector('h6');
    if (hTotal) hTotal.textContent = 'Diferencia';

    // Ocultar acciones
    document.getElementById('colAcciones').style.display = 'none';
  }
}

function getCurrentColspan() {
  if (currentType === 'ventas') return 6;
  if (currentType === 'movimientos') return 5;
  if (currentType === 'cajas') return 8;
  return 6;
}

async function cargarEstadisticas(overrideDates = null) {
  const dates = overrideDates || getValidatedDates(false);
  if (!dates) {
    return;
  }

  if (statsAbortController) {
    statsAbortController.abort();
  }
  statsAbortController = new AbortController();
  const requestId = ++statsRequestSeq;

  try {
    const params = new URLSearchParams({
      fecha_inicio: dates.from,
      fecha_fin: dates.to
    });

    const response = await fetch(`/api/reportes/stats?${params}`, {
      headers: {
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
      },
      signal: statsAbortController.signal
    });

    const data = await response.json();
    if (requestId !== statsRequestSeq) {
      return;
    }

    if (data.success) {
      allStats = data.stats;
      actualizarValoresStats();
    }
  } catch (error) {
    if (error.name === 'AbortError') {
      return;
    }
    // Error silencioso - mantener valores anteriores
    console.log('No se pudieron cargar las estadísticas');
  }
}

function actualizarValoresStats() {
  document.getElementById('statMovimientos').textContent = formatNumber(allStats.movimientos, 0);
  document.getElementById('statEntradas').textContent = formatNumber(allStats.entradas, 0);
  document.getElementById('statSalidas').textContent = formatNumber(allStats.salidas, 0);
  document.getElementById('statIngresos').textContent = formatNumber(allStats.ingresos, 0);
}

async function cargarDatos(overrideDates = null) {
  const dates = overrideDates || getValidatedDates(true);
  if (!dates) {
    restoreDateInputs();
    return;
  }

  if (dataAbortController) {
    dataAbortController.abort();
  }
  dataAbortController = new AbortController();
  const requestId = ++dataRequestSeq;

  isFetchingData = true;
  renderPagination();

  // Mostrar loading
  document.getElementById('tableBody').innerHTML = `
    <tr>
      <td colspan="${getCurrentColspan()}" style="text-align: center; padding: 40px; color: #999;">
        <i class="lni lni-reload" style="font-size: 32px; animation: spin 1s linear infinite;"></i>
        <p>Cargando datos...</p>
      </td>
    </tr>
  `;

  try {
    const params = new URLSearchParams({
      tipo: currentType,
      fecha_inicio: dates.from,
      fecha_fin: dates.to,
      page: currentPage
    });

    if (currentSearchTerm) {
      params.append('search', currentSearchTerm);
    }

    const response = await fetch(`/api/reportes?${params}`, {
      headers: {
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
      },
      signal: dataAbortController.signal
    });

    const data = await response.json();
    if (requestId !== dataRequestSeq) {
      return;
    }

    if (data.success) {
      allData = data.data;
      actualizarTabla(data.data);
      actualizarPaginacion(data.pagination);
    } else {
      mostrarError('No hay datos disponibles');
    }
  } catch (error) {
    if (error.name === 'AbortError') {
      return;
    }
    // Error silencioso - mantener UI consistente
    console.log('Error cargando datos');
    mostrarError('Error al cargar los datos');
  } finally {
    if (requestId === dataRequestSeq) {
      isFetchingData = false;
      renderPagination();
    }
  }
}

function actualizarTabla(data) {
  const tbody = document.getElementById('tableBody');
  const isVentas = currentType === 'ventas';

  if (data.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="${getCurrentColspan()}" style="text-align: center; padding: 40px; color: #999;">
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
          <td><p class="text-sm">${formatDate(row.fecha)}</p></td>
          <td><p class="text-sm">${row.factura_numero || '-'}</p></td>
          <td>
            <span class="truncate truncate-long" 
                  data-bs-toggle="tooltip" 
                  data-bs-title="${row.cliente_nombre || 'Consumidor final'}">
              ${row.cliente_nombre || 'Consumidor final'}
            </span>
          </td>
          <td>
            <span class="status-btn ${row.estado === 'completada' ? 'success-btn' : 'close-btn'}">
              ${row.estado === 'completada' ? 'Completada' : 'Anulada'}
            </span>
          </td>
          <td><p class="text-sm">${formatNumber(row.total)}</p></td>
          <td style="text-align: center;">
            <div class="action-buttons">
              <button class="btn-action btn-print" onclick="verDetallesVenta(${row.id})" title="Ver factura">
                <i class="lni lni-eye"></i> Ver factura
              </button>
            </div>
          </td>
        </tr>
      `;
    } else if (currentType === 'movimientos') {
      const productoNombre = row.producto_nombre || 'Producto #' + row.producto_id;
      const productoLimite = 'Complete Los Datos Del Producto'.length;
      const productoTruncado = productoNombre.length > productoLimite;
      return `
        <tr>
          <td><p class="text-sm">${formatDate(row.created_at)}</p></td>
          <td>
            <span class="text-sm ${productoTruncado ? 'truncate truncate-long' : ''}" 
                  ${productoTruncado ? 'data-bs-toggle="tooltip" data-bs-title="' + productoNombre.replace(/"/g, '&quot;') + '"' : ''}>
              ${productoNombre}
            </span>
          </td>
          </td>
          <td>
            <span class="status-btn ${row.tipo === 'entrada' ? 'success-btn' : 'close-btn'}">
              ${row.tipo === 'entrada' ? 'Entrada' : 'Salida'}
            </span>
          </td>
          <td><p class="text-sm">${row.origen || '-'}</p></td>
          <td><p class="text-sm">${formatNumber(row.cantidad, 0)}</p></td>
        </tr>
      `;
    } else if (currentType === 'cajas') {
      // Cada fila representa una caja (turno)
      const fechaApertura = row.fecha_apertura ? formatDate(row.fecha_apertura) : '-';
      const fechaCierre = row.fecha_cierre ? formatDate(row.fecha_cierre) : '<span class="text-sm">Abierta</span>';
      const usuario = row.user_nombre || row.user_name || (row.user_id ? ('Usuario #' + row.user_id) : '-');
      const totalVentas = row.total_ventas != null ? formatNumber(row.total_ventas, 0) : '-';
      const totalEfectivo = row.total_efectivo != null ? formatNumber(row.total_efectivo, 0) : '-';
      const montoCierreCalc = row.monto_cierre_calculado != null ? formatNumber(row.monto_cierre_calculado, 0) : '-';
      const montoCierreReal = row.monto_cierre_real != null ? formatNumber(row.monto_cierre_real, 0) : '-';
      const diferenciaVal = parseFloat(row.diferencia) || 0;
      const diferencia = row.diferencia != null ? formatNumber(row.diferencia, 0) : '-';

      return `
        <tr>
          <td><p class="text-sm">${fechaApertura}</p></td>
          <td><p class="text-sm">${fechaCierre}</p></td>
          <td>
            <span class="truncate truncate-long" data-bs-toggle="tooltip" data-bs-title="${usuario}">${usuario}</span>
          </td>
          <td><p class="text-sm">${totalVentas}</p></td>
          <td><p class="text-sm">${totalEfectivo}</p></td>
          <td><p class="text-sm">${montoCierreCalc}</p></td>
          <td><p class="text-sm">${montoCierreReal}</p></td>
          <td><p class="text-sm" style="color: ${diferenciaVal !== 0 ? '#c62828' : 'inherit'}">${diferencia}</p></td>
        </tr>
      `;
    }
    return '';
  }).join('');

  // Inicializar tooltips de Bootstrap
  initializeTooltips();
}

function actualizarPaginacion(pagination) {
  lastPagination = pagination;
  renderPagination();
}

function renderPagination() {
  if (!lastPagination) {
    return;
  }
  const infoDiv = document.getElementById('paginationInfo');
  const btnPrev = document.getElementById('btnPrevPage');
  const btnNext = document.getElementById('btnNextPage');

  infoDiv.textContent = `Página ${lastPagination.current_page} de ${lastPagination.last_page}`;

  const basePrevDisabled = lastPagination.current_page <= 1;
  const baseNextDisabled = lastPagination.current_page >= lastPagination.last_page;
  const lock = isFetchingData || isDebouncingSearch;

  btnPrev.disabled = basePrevDisabled || lock;
  btnNext.disabled = baseNextDisabled || lock;

  btnPrev.onclick = function() {
    if (lock || lastPagination.current_page <= 1) {
      return;
    }
    irAPagina(lastPagination.current_page - 1);
  };

  btnNext.onclick = function() {
    if (lock || lastPagination.current_page >= lastPagination.last_page) {
      return;
    }
    irAPagina(lastPagination.current_page + 1);
  };
}

function irAPagina(page) {
  currentPage = page;
  cargarDatos();
}

/**
 * Realiza búsqueda server-side con debounce
 * Resetea a página 1 cuando busca
 */
async function buscarEnTabla(searchTerm) {
  currentSearchTerm = searchTerm;
  currentPage = 1;  // Resetear a página 1
  cancelSearchDebounce();
  cargarDatos();
}

/**
 * Versión anterior - mantener para compatibilidad pero ya no se usa
 * DEPRECATED: Usar buscarEnTabla() para búsqueda server-side
 */
function filtrarTabla() {
  // Esta función ya no se utiliza
  // La búsqueda ahora es server-side mediante buscarEnTabla()
}

function limpiarFiltros() {
  const today = new Date();
  const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));

  const toValue = today.toISOString().split('T')[0];
  const fromValue = thirtyDaysAgo.toISOString().split('T')[0];

  document.getElementById('dateFrom').value = fromValue;
  document.getElementById('dateTo').value = toValue;
  document.getElementById('reportType').value = 'ventas';
  document.getElementById('searchInput').value = '';
  currentType = 'ventas';
  currentSearchTerm = '';
  currentPage = 1;
  lastValidDates = { from: fromValue, to: toValue };
  clearValidationMessage();
  actualizarColumnasTabla();
  cargarEstadisticas(lastValidDates);
  cargarDatos(lastValidDates);
}

function exportarDatos() {
  const dateFrom = document.getElementById('dateFrom').value;
  const dateTo = document.getElementById('dateTo').value;

  const params = new URLSearchParams({
    tipo: currentType,
    fecha_inicio: dateFrom,
    fecha_fin: dateTo
  });

  // Solicitar permisos de notificación
  if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
  }

  // Crear iframe oculto para descarga
  const iframe = document.createElement('iframe');
  iframe.style.display = 'none';
  document.body.appendChild(iframe);

  // Descargar archivo
  fetch(`/api/reportes/export?${params}`, {
    headers: {
      'X-CSRF-TOKEN': csrf,
      'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    }
  })
  .then(response => {
    if (response.status === 413) {
      showExportLimitModal();
      return;
    }
    if (response.ok) {
      // Obtener nombre del archivo
      const contentDisposition = response.headers.get('content-disposition');
      let fileName = 'reporte.xlsx';
      if (contentDisposition) {
        const matches = contentDisposition.match(/filename="(.+?)"/);
        if (matches) fileName = matches[1];
      }

      return response.blob().then(blob => {
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
          new Notification('Exportación completada', {
            body: 'Archivo: ' + fileName,
            icon: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%231f5fbf"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>'
          });
        }
      });
    } else {
      throw new Error('Error en la exportación');
    }
  })
  .catch(error => {
    // Error silencioso - solo mostrar en consola
    console.log('Export error handled');
  });
}

function verDetallesVenta(ventaId) {
  window.location.href = `/ventas/${ventaId}/factura`;
}

function mostrarError(mensaje) {
  document.getElementById('tableBody').innerHTML = `
    <tr>
      <td colspan="${getCurrentColspan()}" style="text-align: center; padding: 40px; color: #c62828;">
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

function isValidISODate(value) {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
    return false;
  }
  const date = new Date(`${value}T00:00:00`);
  if (Number.isNaN(date.getTime())) {
    return false;
  }
  return date.toISOString().startsWith(value);
}

function validateAndNormalizeDates(showMessage = true, sourceField = null) {
  const dateFromInput = document.getElementById('dateFrom');
  const dateToInput = document.getElementById('dateTo');
  const rawFrom = dateFromInput.value.trim();
  const rawTo = dateToInput.value.trim();

  if (!rawFrom || !rawTo) {
    if (showMessage) {
      showValidationMessage('Debe completar ambas fechas antes de continuar.');
    }
    return { valid: false };
  }

  if (!isValidISODate(rawFrom) || !isValidISODate(rawTo)) {
    if (showMessage) {
      showValidationMessage('Formato de fecha inválido. Use YYYY-MM-DD.');
    }
    return { valid: false };
  }

  if (rawFrom > rawTo) {
    if (showMessage) {
      showValidationMessage('La fecha "Desde" no puede ser mayor que "Hasta".');
    }
    return { valid: false };
  }

  clearValidationMessage();
  return { valid: true, normalized: { from: rawFrom, to: rawTo }, sourceField };
}

function getValidatedDates(showMessage = true) {
  const validation = validateAndNormalizeDates(showMessage);
  if (!validation.valid) {
    return null;
  }
  return validation.normalized;
}

function restoreDateInputs() {
  if (lastValidDates.from) {
    document.getElementById('dateFrom').value = lastValidDates.from;
  }
  if (lastValidDates.to) {
    document.getElementById('dateTo').value = lastValidDates.to;
  }
}

function showValidationMessage(message) {
  const container = ensureValidationContainer();
  container.textContent = message;
  container.style.display = 'block';
}

function clearValidationMessage() {
  const container = document.getElementById('dateValidationMessage');
  if (container) {
    container.style.display = 'none';
    container.textContent = '';
  }
}

function ensureValidationContainer() {
  let container = document.getElementById('dateValidationMessage');
  if (container) {
    return container;
  }
  const filtersCard = document.querySelector('.card-style.mb-30');
  container = document.createElement('div');
  container.id = 'dateValidationMessage';
  container.style.display = 'none';
  container.style.marginTop = '12px';
  container.style.padding = '10px 12px';
  container.style.borderRadius = '6px';
  container.style.background = '#fdecea';
  container.style.color = '#b71c1c';
  container.style.fontSize = '13px';
  if (filtersCard) {
    filtersCard.appendChild(container);
  } else {
    document.body.appendChild(container);
  }
  return container;
}

function cancelSearchDebounce() {
  clearTimeout(searchTimeout);
  isDebouncingSearch = false;
}

/**
 * Inicializa tooltips de Bootstrap solo para elementos truncados
 */
function initializeTooltips() {
  // Destruir tooltips anteriores si existen
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    try {
      const tooltip = bootstrap.Tooltip.getInstance(el);
      if (tooltip) {
        tooltip.dispose();
      }
    } catch (e) {
      // Ignorar errores de dispose
    }
  });

  // Crear nuevos tooltips solo para elementos truncados
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    // Si tiene clase truncate, solo mostrar tooltip si está truncado
    if (el.classList.contains('truncate')) {
      // Verificar si el texto está siendo truncado
      if (el.scrollWidth <= el.clientWidth) {
        // No está truncado, no necesita tooltip
        return;
      }
    }

    try {
      // Verificar que Bootstrap está disponible
      if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        new bootstrap.Tooltip(el, {
          placement: 'top',
          trigger: 'hover focus',
          boundary: 'viewport'
        });
      }
    } catch (e) {
      // Bootstrap no disponible, silenciar
    }
  });
}
</script>

<style>
  .action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: center;
  }

  .btn-action {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 500;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
  }

  .btn-print {
    background: #eff6ff;
    color: #1e40af;
  }

  .btn-print:hover {
    background: #dbeafe;
  }

  .btn-secondary {
    background: #f3f4f6;
    color: #374151;
  }

  .btn-secondary:hover {
    background: #e5e7eb;
  }

  .pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    padding: 20px;
    border-top: 1px solid #e5e7eb;
  }

  .pagination button {
    padding: 8px 12px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
  }

  .pagination button:hover {
    background: #f9fafb;
    border-color: #d1d5db;
  }

  .pagination button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .page-info {
    font-size: 14px;
    color: #6b7280;
  }

  /* Modal */
  .modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
  }

  .modal-overlay.active {
    display: flex;
  }

  .modal-content {
    background: white;
    border-radius: 16px;
    max-width: 520px;
    width: 90%;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    animation: modalSlideIn 0.3s ease-out;
  }

  @keyframes modalSlideIn {
    from {
      opacity: 0;
      transform: translateY(-20px) scale(0.95);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  .modal-header {
    display: flex;
    gap: 16px;
    padding: 24px 24px 16px;
    border-bottom: 1px solid #f3f4f6;
  }

  .icon-warning {
    width: 48px;
    height: 48px;
    background: #fef3c7;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .icon-warning i {
    font-size: 24px;
    color: #f59e0b;
  }

  .modal-header-text h3 {
    margin: 0 0 4px 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
  }

  .modal-body {
    padding: 24px;
  }

  .warning-message {
    padding: 12px 16px;
    background: #fef3c7;
    border-left: 3px solid #f59e0b;
    border-radius: 6px;
    font-size: 14px;
    color: #92400e;
    margin-bottom: 0;
  }

  .modal-footer {
    display: flex;
    gap: 12px;
    padding: 16px 24px 24px;
  }

  .modal-btn {
    flex: 1;
    padding: 12px 20px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .modal-btn-cancel {
    background: #f3f4f6;
    color: #374151;
  }

  .modal-btn-cancel:hover {
    background: #e5e7eb;
  }

  .modal-btn-primary {
    background: #2563eb;
    color: white;
  }

  .modal-btn-primary:hover {
    background: #1d4ed8;
  }

  /* Estilos para truncate y tooltips */
  .truncate {
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
  }

  .truncate-long {
    max-width: 200px;
  }

  .truncate-medium {
    max-width: 150px;
  }

  .truncate-short {
    max-width: 100px;
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>

@endsection