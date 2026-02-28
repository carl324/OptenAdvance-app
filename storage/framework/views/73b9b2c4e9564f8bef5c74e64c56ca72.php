

<?php $__env->startSection('title', 'Reportes'); ?>

<?php $__env->startSection('content'); ?>


<section class="section">
  <div class="container-fluid">

    <!-- title -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6"></div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper"></div>
        </div>
      </div>
    </div>

    <!-- KPIs -->
    <div class="row">
      <div class="col-xl-3 col-lg-4 col-sm-6">
        <div class="icon-card mb-30">
          <div class="icon success"><i class="lni lni-dollar"></i></div>
          <div class="content">
            <h6 class="mb-10">Total Vendido</h6>
            <h3 class="text-bold mb-10" id="kpiTotalVendido">...</h3>
            <p class="text-sm" id="kpiTotalVendidoVar"><span class="text-gray">Cargando...</span></p>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-4 col-sm-6">
        <div class="icon-card mb-30">
          <div class="icon purple"><i class="lni lni-cart-full"></i></div>
          <div class="content">
            <h6 class="mb-10">Número de Ventas</h6>
            <h3 class="text-bold mb-10" id="kpiNumVentas">...</h3>
            <p class="text-sm" id="kpiNumVentasVar"><span class="text-gray">Cargando...</span></p>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-4 col-sm-6">
        <div class="icon-card mb-30">
          <div class="icon primary"><i class="lni lni-credit-cards"></i></div>
          <div class="content">
            <h6 class="mb-10">Ticket Promedio</h6>
            <h3 class="text-bold mb-10" id="kpiTicket">...</h3>
            <p class="text-sm" id="kpiTicketVar"><span class="text-gray">Cargando...</span></p>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-4 col-sm-6">
        <div class="icon-card mb-30">
          <div class="icon orange"><i class="lni lni-stats-up"></i></div>
          <div class="content">
            <h6 class="mb-10">Ganancia Estimada</h6>
            <h3 class="text-bold mb-10" id="kpiGanancia">...</h3>
            <p class="text-sm" id="kpiGananciaVar"><span class="text-gray">Cargando...</span></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Tendencia + Exportaciones -->
    <div class="row">
      <div class="col-lg-7">
        <div class="card-style mb-30">
          <div class="title d-flex flex-wrap justify-content-between">
            <div class="left">
              <h6 class="text-medium mb-10">Tendencia de Ventas</h6>
              <h3 class="text-bold" id="tendenciaTotal">-</h3>
            </div>
            <div class="right">
              <div class="select-style-1">
                <div class="select-position select-sm">
                  <select class="light-bg" id="selectTendencia">
                    <option value="mensual">Este mes</option>
                    <option value="semanal">Esta semana</option>
                    <option value="diario">Hoy</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="chart">
            <canvas id="Chart1" style="width:100%;height:400px;margin-left:-35px;"></canvas>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card-style mb-30">
          <div class="title mb-20">
            <h6 class="text-medium">Exportar Reportes</h6>
            <p class="text-xs text-gray mt-1 mb-0">Selecciona un rango y descarga en Excel</p>
          </div>

          <div class="d-flex flex-column" style="gap:10px;">

            <!-- Ventas -->
            <div style="padding:16px 18px;border-radius:10px;background:#fff;border:1px solid #ebebeb;">
              <div class="d-flex align-items-center justify-content-between mb-12">
                <div class="d-flex align-items-center" style="gap:12px;">
                  <div style="width:36px;height:36px;border-radius:8px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="lni lni-bar-chart" style="font-size:15px;color:#16a34a;"></i>
                  </div>
                  <div>
                    <p class="text-sm mb-0" style="font-weight:600;color:#1a1a1a;">Ventas</p>
                    <p class="text-xs mb-0" style="color:#bbb;">Transacciones y totales</p>
                  </div>
                </div>
                <button onclick="exportar('ventas', 'export-ventas-desde', 'export-ventas-hasta')"
                        style="height:28px;padding:0 14px;border-radius:5px;border:1px solid #e5e5e5;background:#fff;font-size:11px;font-weight:600;color:#365CF5;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                  <i class="lni lni-download" style="font-size:11px;"></i> .xlsx
                </button>
              </div>
              <div class="d-flex" style="gap:8px;">
                <div style="flex:1;">
                  <label style="font-size:10px;color:#bbb;font-weight:600;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:4px;">Desde</label>
                  <input type="date" id="export-ventas-desde" style="width:100%;height:32px;border-radius:6px;border:1px solid #ebebeb;padding:0 10px;font-size:12px;color:#444;background:#fafafa;outline:none;" />
                </div>
                <div style="flex:1;">
                  <label style="font-size:10px;color:#bbb;font-weight:600;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:4px;">Hasta</label>
                  <input type="date" id="export-ventas-hasta" style="width:100%;height:32px;border-radius:6px;border:1px solid #ebebeb;padding:0 10px;font-size:12px;color:#444;background:#fafafa;outline:none;" />
                </div>
              </div>
            </div>

            <!-- Inventario -->
            <div style="padding:16px 18px;border-radius:10px;background:#fff;border:1px solid #ebebeb;">
              <div class="d-flex align-items-center justify-content-between mb-12">
                <div class="d-flex align-items-center" style="gap:12px;">
                  <div style="width:36px;height:36px;border-radius:8px;background:#faf5ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="lni lni-package" style="font-size:15px;color:#7c3aed;"></i>
                  </div>
                  <div>
                    <p class="text-sm mb-0" style="font-weight:600;color:#1a1a1a;">Inventario</p>
                    <p class="text-xs mb-0" style="color:#bbb;">Stock y movimientos</p>
                  </div>
                </div>
                <button onclick="exportar('movimientos', 'export-inv-desde', 'export-inv-hasta')"
                        style="height:28px;padding:0 14px;border-radius:5px;border:1px solid #e5e5e5;background:#fff;font-size:11px;font-weight:600;color:#365CF5;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                  <i class="lni lni-download" style="font-size:11px;"></i> .xlsx
                </button>
              </div>
              <div class="d-flex" style="gap:8px;">
                <div style="flex:1;">
                  <label style="font-size:10px;color:#bbb;font-weight:600;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:4px;">Desde</label>
                  <input type="date" id="export-inv-desde" style="width:100%;height:32px;border-radius:6px;border:1px solid #ebebeb;padding:0 10px;font-size:12px;color:#444;background:#fafafa;outline:none;" />
                </div>
                <div style="flex:1;">
                  <label style="font-size:10px;color:#bbb;font-weight:600;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:4px;">Hasta</label>
                  <input type="date" id="export-inv-hasta" style="width:100%;height:32px;border-radius:6px;border:1px solid #ebebeb;padding:0 10px;font-size:12px;color:#444;background:#fafafa;outline:none;" />
                </div>
              </div>
            </div>

            <!-- Caja -->
            <div style="padding:16px 18px;border-radius:10px;background:#fff;border:1px solid #ebebeb;">
              <div class="d-flex align-items-center justify-content-between mb-12">
                <div class="d-flex align-items-center" style="gap:12px;">
                  <div style="width:36px;height:36px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="mdi mdi-cash" style="font-size:19px;color:#2563eb;"></i>
                  </div>
                  <div>
                    <p class="text-sm mb-0" style="font-weight:600;color:#1a1a1a;">Caja</p>
                    <p class="text-xs mb-0" style="color:#bbb;">Aperturas y cierres</p>
                  </div>
                </div>
                <button onclick="exportar('cajas', 'export-caja-desde', 'export-caja-hasta')"
                        style="height:28px;padding:0 14px;border-radius:5px;border:1px solid #e5e5e5;background:#fff;font-size:11px;font-weight:600;color:#365CF5;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                  <i class="lni lni-download" style="font-size:11px;"></i> .xlsx
                </button>
              </div>
              <div class="d-flex" style="gap:8px;">
                <div style="flex:1;">
                  <label style="font-size:10px;color:#bbb;font-weight:600;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:4px;">Desde</label>
                  <input type="date" id="export-caja-desde" style="width:100%;height:32px;border-radius:6px;border:1px solid #ebebeb;padding:0 10px;font-size:12px;color:#444;background:#fafafa;outline:none;" />
                </div>
                <div style="flex:1;">
                  <label style="font-size:10px;color:#bbb;font-weight:600;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:4px;">Hasta</label>
                  <input type="date" id="export-caja-hasta" style="width:100%;height:32px;border-radius:6px;border:1px solid #ebebeb;padding:0 10px;font-size:12px;color:#444;background:#fafafa;outline:none;" />
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- Cajeros + Productos -->
    <div class="row">
      <div class="col-lg-5">
        <div class="card-style mb-30">
          <div class="title d-flex flex-wrap align-items-center justify-content-between">
            <div class="left"><h6 class="text-medium mb-2">Ventas por Cajero</h6></div>
            <div class="right">
              <div class="select-style-1 mb-2">
                <div class="select-position select-sm">
                  <select class="light-bg" id="selectCajeros">
                    <option value="mensual">Este mes</option>
                    <option value="semanal">Esta semana</option>
                    <option value="diario">Hoy</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="chart">
            <div style="position:relative;height:300px;width:100%;">
              <canvas id="Chart3"></canvas>
            </div>
            <div id="btnMasCajeros" style="display:none;text-align:center;margin-top:12px;">
              <button onclick="verTodosCajeros()"
                      style="font-size:12px;color:#365CF5;background:none;border:none;cursor:pointer;font-weight:600;">
                Ver todos <i class="lni lni-chevron-down"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-7">
        <div class="card-style mb-30">
          <div class="title d-flex flex-wrap justify-content-between align-items-center">
            <div class="left"><h6 class="text-medium mb-30">Productos Más Vendidos</h6></div>
            <div class="right">
              <div class="select-style-1">
                <div class="select-position select-sm">
                  <select class="light-bg" id="selectProductos">
                    <option value="mensual">Este mes</option>
                    <option value="semanal">Esta semana</option>
                    <option value="diario">Hoy</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table top-selling-table">
              <thead>
                <tr>
                  <th><h6 class="text-sm text-medium">Producto</h6></th>
                  <th class="min-width"><h6 class="text-sm text-medium">Precio venta</h6></th>
                  <th class="min-width"><h6 class="text-sm text-medium">Unidades</h6></th>
                  <th class="min-width"><h6 class="text-sm text-medium">Total vendido</h6></th>
                  <th class="min-width"><h6 class="text-sm text-medium">Ganancia</h6></th>
                </tr>
              </thead>
              <tbody id="tablaProductos">
                <tr>
                  <td colspan="5" style="text-align:center;padding:40px;color:#999;">
                    <i class="lni lni-reload" style="font-size:28px;"></i>
                    <p class="mt-2">Cargando...</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Modal límite exportación -->
<div class="modal-overlay" id="exportLimitModal">
  <div class="modal-conten">
    <div class="modal-header">
      <div class="icon-warning"><i class="lni lni-warning"></i></div>
      <div class="modal-header-text"><h3>Límite de exportación alcanzado</h3></div>
    </div>
    <div class="modal-body">
      <div class="warning-message">
        Por seguridad, el sistema permite exportar hasta 10.000 registros por archivo.
        Para exportaciones mayores, reduzca el rango de fechas o contacte a soporte.
      </div>
    </div>
    <div class="modal-footer">
      <button class="modal-btn modal-btn-cancel" id="closeExportLimitModal" type="button">Cerrar</button>
      <a class="modal-btn modal-btn-primary" href="<?php echo e(route('soporte.index')); ?>">Contactar soporte</a>
    </div>
  </div>
</div>

<style>
.kpi-skeleton { color: #ccc; font-weight: 400; }

.modal-overlay {
  display: none; position: fixed; inset: 0;
  background: rgba(0,0,0,0.5); z-index: 1000;
  align-items: center; justify-content: center;
  backdrop-filter: blur(4px);
}
.modal-overlay.active { display: flex; }
.modal-conten {
  background: white; border-radius: 16px;
  max-width: 520px; width: 90%;
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
  animation: modalSlideIn 0.3s ease-out;
}
@keyframes modalSlideIn {
  from { opacity:0; transform: translateY(-20px) scale(0.95); }
  to   { opacity:1; transform: translateY(0) scale(1); }
}
.modal-header { display:flex; gap:16px; padding:24px 24px 16px; border-bottom:1px solid #f3f4f6; }
.icon-warning { width:48px; height:48px; background:#fef3c7; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.icon-warning i { font-size:24px; color:#f59e0b; }
.modal-header-text h3 { margin:0; font-size:18px; font-weight:600; color:#111827; }
.modal-body { padding:24px; }
.warning-message { padding:12px 16px; background:#fef3c7; border-left:3px solid #f59e0b; border-radius:6px; font-size:14px; color:#92400e; }
.modal-footer { display:flex; gap:12px; padding:16px 24px 24px; }
.modal-btn { flex:1; padding:12px 20px; font-size:14px; font-weight:600; border:none; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center; text-decoration:none; }
.modal-btn-cancel { background:#f3f4f6; color:#374151; }
.modal-btn-cancel:hover { background:#e5e7eb; }
.modal-btn-primary { background:#2563eb; color:white; }
.modal-btn-primary:hover { background:#1d4ed8; }
</style>
<script>
const csrf = '<?php echo e(csrf_token()); ?>';
let chart1 = null;
let chart3 = null;
let todosLosCajeros = [];

// ─── INIT ───────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

  // Fechas por defecto para exportaciones: mes actual
  const hoy = new Date();
  const primerDiaMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
  const hoySt   = hoy.toISOString().split('T')[0];
  const inicioSt = primerDiaMes.toISOString().split('T')[0];

  ['export-ventas-desde','export-inv-desde','export-caja-desde'].forEach(id => {
    document.getElementById(id).value = inicioSt;
  });
  ['export-ventas-hasta','export-inv-hasta','export-caja-hasta'].forEach(id => {
    document.getElementById(id).value = hoySt;
  });

  // Cargar todo
  cargarKpis();
  cargarTendencia('mensual');
  cargarCajeros('mensual');
  cargarProductos('mensual');

  // Selectores
  document.getElementById('selectTendencia').addEventListener('change', function () {
    cargarTendencia(this.value);
  });
  document.getElementById('selectCajeros').addEventListener('change', function () {
    cargarCajeros(this.value);
  });
  document.getElementById('selectProductos').addEventListener('change', function () {
    cargarProductos(this.value);
  });

  // Modal exportación
  const closeBtn = document.getElementById('closeExportLimitModal');
  const modal    = document.getElementById('exportLimitModal');
  if (closeBtn) closeBtn.addEventListener('click', hideExportLimitModal);
  if (modal)    modal.addEventListener('click', e => { if (e.target === modal) hideExportLimitModal(); });
});

// ─── KPIs ────────────────────────────────────────────────
async function cargarKpis() {
  try {
    const res  = await fetch('/api/reportes/kpis', { headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
    const data = await res.json();
    if (!data.success) return;

    const { kpis, variaciones, periodo } = data;

    document.getElementById('kpiTotalVendido').textContent = formatCOP(kpis.total_vendido);
    document.getElementById('kpiNumVentas').textContent    = kpis.num_ventas.toLocaleString('es-CO');
    document.getElementById('kpiTicket').textContent       = formatCOP(kpis.ticket_promedio);
    document.getElementById('kpiGanancia').textContent     = formatCOP(kpis.ganancia);

    renderVariacion('kpiTotalVendidoVar', variaciones.total_vendido, periodo);
    renderVariacion('kpiNumVentasVar',    variaciones.num_ventas,    periodo);
    renderVariacion('kpiTicketVar',       variaciones.ticket_promedio, periodo);
    renderVariacion('kpiGananciaVar',     variaciones.ganancia,      periodo);

  } catch (e) {
    console.error('Error KPIs:', e);
  }
}

function renderVariacion(elId, variacion, periodo) {
  const el = document.getElementById(elId);
  if (variacion === null) {
    el.innerHTML = `<span class="text-gray">${periodo}</span>`;
    return;
  }
  const positivo = variacion >= 0;
  const color    = positivo ? 'text-success' : 'text-danger';
  const icono    = positivo ? 'lni-arrow-up' : 'lni-arrow-down';
  const signo    = positivo ? '+' : '';
  el.innerHTML   = `<span class="${color}"><i class="lni ${icono}"></i> ${signo}${variacion}%</span> <span class="text-gray">vs mes anterior</span>`;
}

// ─── TENDENCIA ───────────────────────────────────────────
async function cargarTendencia(agrupacion) {
  try {
    const res  = await fetch(`/api/reportes/tendencia?agrupacion=${agrupacion}`, {
      headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (!data.success) return;

    const labels  = data.labels;
    const totales = data.totales;

    const totalSum = totales.reduce((a, b) => a + b, 0);
    document.getElementById('tendenciaTotal').textContent = formatCOP(totalSum);

    // Formatear labels según agrupación
    const labelsFormateados = labels.map(l => {
      if (agrupacion === 'diario') return l; // ya viene como HH:00
      const d = new Date(l + 'T00:00:00');
      if (agrupacion === 'semanal' || agrupacion === 'mensual') {
        return d.toLocaleDateString('es-CO', { day: '2-digit', month: 'short' });
      }
      return l;
    });

    if (chart1) chart1.destroy();

    const ctx = document.getElementById('Chart1').getContext('2d');
    chart1 = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labelsFormateados,
        datasets: [{
          label: 'Ventas',
          backgroundColor: 'rgba(54,92,245,0.08)',
          borderColor: '#365CF5',
          fill: true,
          data: totales,
          pointBackgroundColor: '#365CF5',
          pointBorderColor: '#fff',
          pointBorderWidth: 3,
          borderWidth: 3,
          pointRadius: 5,
          pointHoverRadius: 7,
          cubicInterpolationMode: 'monotone',
        }],
      },
      options: {
        plugins: {
          legend: { display: false },
          tooltip: {
            intersect: false,
            backgroundColor: '#f9f9f9',
            titleColor: '#8F92A1',
            bodyColor: '#171717',
            displayColors: false,
            padding: { x: 20, y: 10 },
            bodyFont: { size: 14, weight: 'bold' },
            callbacks: {
              label: ctx => formatCOP(ctx.parsed.y)
            }
          },
        },
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            grid: { color: 'rgba(143,146,161,.08)', drawBorder: false },
            ticks: {
              padding: 15,
              callback: v => '$' + (v >= 1000000 ? (v/1000000).toFixed(1)+'M' : v >= 1000 ? (v/1000).toFixed(0)+'K' : v)
            },
          },
          x: {
            grid: { display: false, drawBorder: false },
            ticks: { padding: 10 },
          },
        },
      },
    });

  } catch (e) {
    console.error('Error tendencia:', e);
  }
}

// ─── CAJEROS ─────────────────────────────────────────────
async function cargarCajeros(agrupacion) {
  try {
    const res  = await fetch(`/api/reportes/cajeros?agrupacion=${agrupacion}`, {
      headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (!data.success) return;

    todosLosCajeros = data.cajeros;

    // Mostrar botón "ver más" si hay más de 5
    document.getElementById('btnMasCajeros').style.display = data.hay_mas ? 'block' : 'none';

    renderChartCajeros(data.cajeros);

  } catch (e) {
    console.error('Error cajeros:', e);
  }
}

function renderChartCajeros(cajeros) {
  if (chart3) chart3.destroy();

  const ctx = document.getElementById('Chart3').getContext('2d');
  chart3 = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: cajeros.map(c => c.cajero),
      datasets: [
        {
          label: 'Ventas',
          backgroundColor: '#365CF5',
          borderRadius: 6,
          barThickness: 14,
          data: cajeros.map(c => c.total_ventas),
        },
        {
          label: 'Anuladas',
          backgroundColor: '#f87171',
          borderRadius: 6,
          barThickness: 14,
          data: cajeros.map(c => c.anuladas),
        },
      ],
    },
    options: {
      indexAxis: 'y',
      plugins: {
        legend: {
          display: true,
          position: 'bottom',
          labels: { usePointStyle: true, padding: 16, font: { size: 12 } }
        },
        tooltip: {
          backgroundColor: '#f9f9f9',
          titleColor: '#8F92A1',
          bodyColor: '#171717',
          displayColors: true,
          padding: { x: 16, y: 10 },
          bodyFont: { size: 13, weight: 'bold' },
          callbacks: {
            label: ctx => {
              if (ctx.datasetIndex === 0) return ' ' + formatCOP(ctx.parsed.x);
              return ' ' + ctx.parsed.x + ' anuladas';
            }
          }
        },
      },
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: {
          grid: { drawBorder: false, color: 'rgba(143,146,161,.1)' },
          ticks: {
            padding: 10,
            callback: v => '$' + (v >= 1000000 ? (v/1000000).toFixed(1)+'M' : v >= 1000 ? (v/1000).toFixed(0)+'K' : v)
          },
        },
        y: {
          grid: { display: false, drawBorder: false },
          ticks: { padding: 10 },
        },
      },
    },
  });
}

function verTodosCajeros() {
  // Recarga el chart con todos los cajeros sin límite
  fetch(`/api/reportes/cajeros?agrupacion=${document.getElementById('selectCajeros').value}&todos=1`, {
    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      renderChartCajeros(data.cajeros);
      document.getElementById('btnMasCajeros').style.display = 'none';
    }
  });
}

// ─── PRODUCTOS ───────────────────────────────────────────
async function cargarProductos(agrupacion) {
  const tbody = document.getElementById('tablaProductos');
  tbody.innerHTML = `
    <tr>
      <td colspan="5" style="text-align:center;padding:30px;color:#999;">
        <i class="lni lni-reload"></i> Cargando...
      </td>
    </tr>`;

  try {
    const res  = await fetch(`/api/reportes/productos?agrupacion=${agrupacion}`, {
      headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (!data.success) return;

    if (data.productos.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="5" style="text-align:center;padding:30px;color:#999;">
            <i class="lni lni-inbox"></i>
            <p class="mt-2">Sin ventas en este período</p>
          </td>
        </tr>`;
      return;
    }

    tbody.innerHTML = data.productos.map((p, i) => `
      <tr>
        <td>
          <div class="d-flex align-items-center" style="gap:10px;">
            <span style="width:22px;height:22px;border-radius:50%;background:#f1f5f9;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#64748b;flex-shrink:0;">${i+1}</span>
            <p class="text-sm mb-0">${p.nombre}</p>
          </div>
        </td>
        <td><p class="text-sm">${formatCOP(p.precio_venta)}</p></td>
        <td><p class="text-sm">${p.unidades.toLocaleString('es-CO')}</p></td>
        <td><p class="text-sm">${formatCOP(p.total_vendido)}</p></td>
        <td><p class="text-sm" style="color:#16a34a;font-weight:600;">${formatCOP(p.ganancia)}</p></td>
      </tr>
    `).join('');

  } catch (e) {
    console.error('Error productos:', e);
    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:30px;color:#c62828;">Error al cargar productos</td></tr>`;
  }
}

// ─── EXPORTAR ────────────────────────────────────────────
function exportar(tipo, desdeId, hastaId) {
  const desde = document.getElementById(desdeId).value;
  const hasta = document.getElementById(hastaId).value;

  if (!desde || !hasta) {
    alert('Selecciona un rango de fechas para exportar.');
    return;
  }
  if (desde > hasta) {
    alert('La fecha "Desde" no puede ser mayor que "Hasta".');
    return;
  }

  const params = new URLSearchParams({ tipo, fecha_inicio: desde, fecha_fin: hasta });

  if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
  }

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
      const cd = response.headers.get('content-disposition');
      let fileName = `reporte_${tipo}.xlsx`;
      if (cd) {
        const m = cd.match(/filename="(.+?)"/);
        if (m) fileName = m[1];
      }
      return response.blob().then(blob => {
        const url  = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        if ('Notification' in window && Notification.permission === 'granted') {
          new Notification('Exportación completada', { body: 'Archivo: ' + fileName });
        }
      });
    }
  })
  .catch(() => console.log('Export error'));
}

// ─── MODAL ───────────────────────────────────────────────
function showExportLimitModal() {
  document.getElementById('exportLimitModal')?.classList.add('active');
}
function hideExportLimitModal() {
  document.getElementById('exportLimitModal')?.classList.remove('active');
}

// ─── HELPERS ─────────────────────────────────────────────
function formatCOP(num) {
  const n = parseFloat(num) || 0;
  return '$' + n.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}
</script>

     <script src="assets/js/Chart.min.js"></script>
    <script src="assets/js/dynamic-pie-chart.js"></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\optenadvance\app\www\resources\views/reportes/index.blade.php ENDPATH**/ ?>