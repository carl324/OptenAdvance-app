@extends('layouts.app')

@section('content')
<section class="section">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      
    </div>
    <!-- ========== title-wrapper end ========== -->

    <!-- ========== filters start ========== -->
    <div class="sales-filters">
      <div class="filter-row">
        <div class="filter-group">
          <label>Buscar venta</label>
          <input type="text" placeholder="Cliente, factura, total, estado..." id="buscadorVentas">
        </div>
        
        <div class="filter-group date-filter">
          <label>Desde</label>
          <input type="date" id="fechaInicio">
        </div>
        
        <div class="filter-group date-filter">
          <label>Hasta</label>
          <input type="date" id="fechaFin">
        </div>
      </div>
    </div>
    <!-- ========== filters end ========== -->

    @if($ventas->isEmpty())
      <div class="sales-table">
        <div class="table-header">
          <h3>Historial de Ventas</h3>
        </div>
        <div style="padding: 40px; text-align: center; color: #64748b;">
          <p>No hay ventas registradas.</p>
        </div>
      </div>
    @else
      <!-- ========== sales table start ========== -->
      <div class="sales-table">
        <div class="table-header">
          <h3>Historial de Ventas</h3>
        </div>
        
        <div id="noCoincidencias" style="display:none; padding: 40px; text-align: center; color: #ef4444; font-weight: 500;">
          No se encontraron ventas para esta búsqueda.
        </div>
        
        <div class="table-responsive" data-registros-pagina="{{ $registrosPorPagina ?? 10 }}">
          <table id="tablaVentas">
            <thead>
              <tr>
                <th>ID</th>
                <th>Número Factura</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                @if($empresa && $empresa->cobra_iva)
                <th>IVA / Impuestos</th>
                @endif
                <th>Forma de Pago</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @foreach($ventas as $venta)
              <tr data-venta-id="{{ $venta->id }}">
                <td>{{ $venta->id }}</td>
                <td><span class="invoice-number">{{ optional($venta->factura)->numero ?? '-' }}</span></td>
                <td>{{ optional($venta->fecha)->format('Y-m-d H:i') ?? '-' }}</td>
                <td><span class="client-name">{{ optional($venta->factura)->cliente_nombre ?? 'Consumidor final' }}</span></td>
                <td><span class="amount">${{ number_format($venta->total ?? 0, 0, ',', '.') }}</span></td>
                @if($empresa && $empresa->cobra_iva)
                <td>${{ number_format(optional($venta->factura)->impuestos ?? 0, 0, ',', '.') }}</td>
                @endif
                <td>
                  <span class="payment-method">
                    @php
                      $formaPago = optional($venta->factura)->forma_pago ?? '-';
                      $icono = '';
                      if(stripos($formaPago, 'tarjeta') !== false) $icono = '';
                      elseif(stripos($formaPago, 'transferencia') !== false) $icono = '';
                    @endphp
                    {{ $icono }} {{ $formaPago }}
                  </span>
                </td>
                <td>
                  @php
                    $estado = strtolower($venta->estado ?? '---');
                    $estadoClass = 'status-completed';
                    $estadoTexto = 'Completada';
                    
                    if($estado === 'anulada') {
                      $estadoClass = 'status-cancelled';
                      $estadoTexto = 'Anulada';
                    } elseif($estado === 'pendiente') {
                      $estadoClass = 'status-pending';
                      $estadoTexto = 'Pendiente';
                    } elseif($estado === 'completada') {
                      $estadoClass = 'status-completed';
                      $estadoTexto = 'Completada';
                    } else {
                      $estadoTexto = ucfirst($estado);
                    }
                  @endphp
                  <span class="status-badge {{ $estadoClass }}">{{ $estadoTexto }}</span>
                </td>
                <td>
                  @php
                    $puedeAnular = (
                      ($venta->estado === 'completada') &&
                      ($venta->factura && optional($venta->factura)->fecha_emision && \Carbon\Carbon::parse($venta->factura->fecha_emision)->isSameDay(\Carbon\Carbon::now()))
                    );
                  @endphp
                  <div class="action-buttons">
                    <a href="{{ route('ventas.factura', $venta) }}" target="_blank" class="btn-action btn-print">
                      <i class="mdi mdi-file-document-outline"></i> Factura
                    </a>
                    @if($puedeAnular)
                      <button class="btn-action btn-cancel btn-anular" 
                              data-url="{{ route('ventas.devolucion.confirmar', $venta) }}"
                              data-invoice="{{ $venta->factura->numero ?? 'FA-'.$venta->id }}">
                        <i class="lni lni-close"></i> Anular
                      </button>
                    @else
                      @if($venta->estado !== 'anulada')
                        <button class="btn-action btn-disabled btn-tooltip" disabled data-tooltip="Solo se puede anular el mismo día">
                          <i class="lni lni-close"></i> Anular
                        </button>
                      @endif
                    @endif
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <div class="pagination">
          <button>
            <i class="lni lni-chevron-left"></i>
          </button>
          <span class="page-info">Página 1 de 1</span>
          <button>
            <i class="lni lni-chevron-right"></i>
          </button>
        </div>
      </div>
      <!-- ========== sales table end ========== -->
    @endif
  </div>
</section>

<!-- ========== Modal Anular start ========== -->
<div class="modal-overlay" id="cancelModal">
  <div class="modal-content">
    <div class="modal-header">
      <div class="icon-warning">
        <i class="lni lni-warning"></i>
      </div>
      <div class="modal-header-text">
        <h3>Anular Venta</h3>
        <p id="modalInvoiceNumber">Factura: -</p>
      </div>
    </div>
    
    <div class="modal-body">
      <div class="warning-message">
       Esta acción anula la venta y revierte el inventario. No se puede deshacer.
      </div>
      
      <form id="formAnular">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group">
          <label>
            Motivo 
            <span class="required">*</span>
          </label>
          <textarea 
            name="motivo"
            placeholder="Describe el motivo de la anulación..."
            id="cancelReason"
            required
          ></textarea>
        </div>
        
        <div id="anularMessage" style="margin-top: 12px; padding: 10px; border-radius: 6px; display: none;"></div>
      </form>
    </div>
    
    <div class="modal-footer">
      <button class="modal-btn modal-btn-cancel" id="cancelAnular" type="button">
        Cancelar
      </button>
      <button class="modal-btn modal-btn-confirm" id="confirmAnular" type="button">
        <i class="lni lni-checkmark"></i> Confirmar Anulación
      </button>
    </div>
  </div>
</div>
<!-- ========== Modal Anular end ========== -->

<style>
.highlight { 
  background: #fef08a; 
  color: #854d0e; 
  padding: 2px 4px; 
  border-radius: 3px;
  font-weight: 500;
}

/* Estilos del diseño moderno */
.sales-filters {
  background: white;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.filter-row {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  align-items: flex-end;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.filter-group label {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  margin: 0;
}

.filter-group input {
  padding: 10px 14px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.2s;
}

.filter-group input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-group:first-child input {
  min-width: 320px;
}

.date-filter input {
  min-width: 160px;
}

.sales-table {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.table-header {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.table-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #111827;
}

.table-responsive {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #f9fafb;
}

th {
  padding: 14px 16px;
  text-align: left;
  font-size: 13px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #e5e7eb;
}

td {
  padding: 16px;
  font-size: 14px;
  color: #374151;
  border-bottom: 1px solid #f3f4f6;
}

tbody tr {
  transition: background 0.15s;
}

tbody tr:hover {
  background: #f9fafb;
}

.client-name {
  font-weight: 500;
}

.amount {
  font-weight: 600;
  color: #059669;
}

.payment-method {
  display: inline-block;
  padding: 4px 10px;
  background: #f3f4f6;
  border-radius: 6px;
  font-size: 13px;
}

.status-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}

.status-completed {
  background: #d1fae5;
  color: #065f46;
}

.status-cancelled {
  background: #fee2e2;
  color: #991b1b;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.action-buttons {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
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

.btn-cancel {
  background: #fef2f2;
  color: #b91c1c;
}

.btn-cancel:hover {
  background: #fee2e2;
}

.btn-disabled {
  background: #f3f4f6;
  color: #9ca3af;
  cursor: not-allowed;
  opacity: 0.6;
}

.btn-disabled:hover {
  background: #f3f4f6;
}

/* Custom Tooltip */
.btn-tooltip {
  position: relative;
}

.btn-tooltip:hover::after {
  content: attr(data-tooltip);
  position: absolute;
  bottom: 100%;
  right: 0;
  transform: none;
  background: #1f2937;
  color: #ffffff;
  padding: 8px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
  margin-bottom: 8px;
  z-index: 1001;
  animation: tooltipFadeIn 0.2s ease-out;
}

.btn-tooltip:hover::before {
  content: '';
  position: absolute;
  bottom: 100%;
  right: 12px;
  transform: none;
  margin-bottom: 0;
  width: 0;
  height: 0;
  border-left: 4px solid transparent;
  border-right: 4px solid transparent;
  border-top: 4px solid #1f2937;
  z-index: 1001;
  animation: tooltipFadeIn 0.2s ease-out;
}

@keyframes tooltipFadeIn {
  from {
    opacity: 0;
    transform: translateY(4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
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
  max-width: 500px;
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

.modal-header-text p {
  margin: 0;
  font-size: 14px;
  color: #6b7280;
  font-family: 'Courier New', monospace;
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
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 0;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.required {
  color: #ef4444;
}

.form-group textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  font-family: inherit;
  resize: vertical;
  min-height: 100px;
  transition: all 0.2s;
}

.form-group textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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

.modal-btn-confirm {
  background: #ef4444;
  color: white;
}

.modal-btn-confirm:hover {
  background: #dc2626;
}

.modal-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .filter-row {
    flex-direction: column;
  }
  
  .filter-group:first-child input,
  .date-filter input {
    width: 100%;
    min-width: unset;
  }
  
  .action-buttons {
    flex-direction: column;
  }
  
  .btn-action {
    width: 100%;
    justify-content: center;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const tabla = document.getElementById('tablaVentas');
  if (!tabla) return;
  
  const buscador = document.getElementById('buscadorVentas');
  const fechaInicio = document.getElementById('fechaInicio');
  const fechaFin = document.getElementById('fechaFin');
  const noCoincidencias = document.getElementById('noCoincidencias');
  const modal = document.getElementById('cancelModal');
  const form = document.getElementById('formAnular');
  const cancelBtn = document.getElementById('cancelAnular');
  const confirmBtn = document.getElementById('confirmAnular');
  const messageBox = document.getElementById('anularMessage');
  const modalInvoiceNumber = document.getElementById('modalInvoiceNumber');
  const cancelReason = document.getElementById('cancelReason');
  
  let currentUrl = null;
  let currentRow = null;
  let paginaActual = 1;
  // Soluciona problema #7: Leer registrosPorPagina desde atributo del servidor
  const registrosPorPagina = parseInt(document.querySelector('.table-responsive').getAttribute('data-registros-pagina')) || 10;
  
  // Guardar texto original de cada celda
  const filas = Array.from(tabla.querySelectorAll('tbody tr'));
  filas.forEach(tr => {
    Array.from(tr.children).forEach(td => {
      td.setAttribute('data-original', td.textContent.trim());
    });
  });
  
  // Función para limpiar resaltado
  function limpiarResaltado(tr) {
    Array.from(tr.children).forEach((td, idx) => {
      if (td.hasAttribute('data-original') && idx !== tr.children.length - 1) {
        td.innerHTML = td.getAttribute('data-original');
      }
    });
  }
  
  // Función para resaltar coincidencias - Soluciona problema #6: Mejorar regex con límite
  function resaltarCoincidencia(texto, filtro) {
    if (!filtro) return texto;
    // Escapar caracteres especiales de regex para evitar inyección
    const regexSafeFilter = filtro.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    // Limitar el tamaño del filtro a 50 caracteres para evitar ReDoS
    if (regexSafeFilter.length > 50) return texto;
    const regex = new RegExp('(' + regexSafeFilter + ')', 'gi');
    return texto.replace(regex, '<mark class="highlight">$1</mark>');
  }
  
  // Función para actualizar paginación
  function actualizarPaginacion() {
    const filasVisibles = filas.filter(tr => tr.getAttribute('data-filtered') !== 'false');
    const totalPaginas = Math.ceil(filasVisibles.length / registrosPorPagina);
    const inicio = (paginaActual - 1) * registrosPorPagina;
    const fin = inicio + registrosPorPagina;
    
    // Mostrar/ocultar filas según página
    filasVisibles.forEach((tr, idx) => {
      tr.style.display = (idx >= inicio && idx < fin) ? '' : 'none';
    });
    
    // Actualizar info de página
    const pageInfo = document.querySelector('.page-info');
    pageInfo.textContent = `Página ${paginaActual} de ${totalPaginas || 1}`;
    
    // Actualizar estado de botones
    const paginationBtns = document.querySelectorAll('.pagination button');
    paginationBtns[0].disabled = paginaActual === 1;
    paginationBtns[1].disabled = paginaActual === totalPaginas || totalPaginas === 0;
  }
  
  // Función de filtrado
  function filtrarTabla() {
    const filtro = buscador.value.trim().toLowerCase();
    const fInicio = fechaInicio.value;
    const fFin = fechaFin.value;
    let visibles = 0;
    
    filas.forEach(tr => {
      limpiarResaltado(tr);
      let mostrar = true;
      
      // Filtrar por texto
      if (filtro) {
        mostrar = false;
        Array.from(tr.children).forEach((td, idx) => {
          if (idx === tr.children.length - 1) return;
          const texto = td.getAttribute('data-original') || '';
          if (texto.toLowerCase().includes(filtro)) {
            mostrar = true;
          }
        });
      }
      
      // Filtrar por fecha
      if (mostrar && (fInicio || fFin)) {
        const fechaTexto = tr.children[2].getAttribute('data-original') || '';
        const fechaVenta = fechaTexto.split(' ')[0];
        if (fInicio && fechaVenta < fInicio) mostrar = false;
        if (fFin && fechaVenta > fFin) mostrar = false;
      }
      
      // Marcar como filtrado o no
      tr.setAttribute('data-filtered', mostrar ? 'true' : 'false');
      
      // Resaltar coincidencias
      if (mostrar && filtro) {
        Array.from(tr.children).forEach((td, idx) => {
          if (idx === tr.children.length - 1) return;
          const texto = td.getAttribute('data-original') || '';
          if (texto.toLowerCase().includes(filtro)) {
            td.innerHTML = resaltarCoincidencia(texto, filtro);
          }
        });
      }
      
      if (mostrar) visibles++;
    });
    
    noCoincidencias.style.display = visibles === 0 ? '' : 'none';
    document.querySelector('.table-responsive').style.display = visibles === 0 ? 'none' : '';
    document.querySelector('.pagination').style.display = visibles === 0 ? 'none' : '';
    paginaActual = 1;
    actualizarPaginacion();
  }
  
  // Event listeners para filtros
  buscador.addEventListener('input', filtrarTabla);
  fechaInicio.addEventListener('change', filtrarTabla);
  fechaFin.addEventListener('change', filtrarTabla);
  
  // Funciones del modal
  function openModal(url, row, invoice) {
    currentUrl = url;
    currentRow = row;
    modalInvoiceNumber.textContent = 'Factura: ' + invoice;
    modal.classList.add('active');
  }
  
  function closeModal() {
    modal.classList.remove('active');
    form.reset();
    messageBox.style.display = 'none';
    messageBox.innerHTML = '';
    currentUrl = null;
    currentRow = null;
  }
  
  // Event listeners del modal
  cancelBtn.addEventListener('click', closeModal);
  
  modal.addEventListener('click', function(e) {
    if (e.target === modal) {
      closeModal();
    }
  });
  
  // Attach click handlers a botones de anular
  document.querySelectorAll('.btn-anular').forEach(btn => {
    btn.addEventListener('click', function() {
      const url = btn.dataset.url || btn.getAttribute('data-url');
      const invoice = btn.dataset.invoice || btn.getAttribute('data-invoice');
      const row = btn.closest('tr');
      openModal(url, row, invoice);
    });
  });
  
  // Submit del formulario de anulación
  confirmBtn.addEventListener('click', async function(e) {
    e.preventDefault();
    
    if (!cancelReason.value.trim()) {
      messageBox.style.display = 'block';
      messageBox.style.background = '#fee2e2';
      messageBox.style.color = '#991b1b';
      messageBox.textContent = 'El motivo es obligatorio';
      return;
    }
    
    if (!currentUrl) return;
    
    confirmBtn.disabled = true;
    const originalText = confirmBtn.innerHTML;
    confirmBtn.innerHTML = '<i class="lni lni-spinner" style="animation: spin 1s linear infinite;"></i> Procesando...';
    messageBox.style.display = 'none';
    messageBox.textContent = '';
    
    const fd = new FormData(form);
    
    try {
      const res = await fetch(currentUrl, {
        method: 'POST',
        body: fd,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      
      const json = await res.json();
      
      if (res.ok && json.success) {
        // Actualizar fila en la tabla
        if (currentRow) {
          const celdas = currentRow.querySelectorAll('td');
          // Actualizar estado
          const estadoCell = celdas[celdas.length - 2];
          if (estadoCell) {
            estadoCell.innerHTML = '<span class="status-badge status-cancelled">Anulada</span>';
          }
          // Actualizar acciones
          const btnCell = celdas[celdas.length - 1];
          if (btnCell) {
            const printBtn = btnCell.querySelector('.btn-print');
            btnCell.innerHTML = '';
            if (printBtn) {
              btnCell.appendChild(printBtn);
            }
          }
        }
        
        messageBox.style.display = 'block';
        messageBox.style.background = '#d1fae5';
        messageBox.style.color = '#065f46';
        messageBox.textContent = json.message || 'Venta anulada correctamente';
        
        setTimeout(closeModal, 1500);
      } else {
        messageBox.style.display = 'block';
        messageBox.style.background = '#fee2e2';
        messageBox.style.color = '#991b1b';
        messageBox.textContent = json.message || 'Error al anular la venta';
      }
    } catch (err) {
      messageBox.style.display = 'block';
      messageBox.style.background = '#fee2e2';
      messageBox.style.color = '#991b1b';
      messageBox.textContent = 'Error de red al intentar anular';
    } finally {
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = originalText;
    }
  });
  
  // Botones de paginación
  const paginationBtns = document.querySelectorAll('.pagination button');
  paginationBtns[0].addEventListener('click', function() {
    if (paginaActual > 1) {
      paginaActual--;
      actualizarPaginacion();
    }
  });
  
  paginationBtns[1].addEventListener('click', function() {
    const filasVisibles = filas.filter(tr => tr.getAttribute('data-filtered') === 'true');
    const totalPaginas = Math.ceil(filasVisibles.length / registrosPorPagina);
    if (paginaActual < totalPaginas) {
      paginaActual++;
      actualizarPaginacion();
    }
  });
  
  // Inicializar todas las filas como filtradas
  filas.forEach(tr => {
    tr.setAttribute('data-filtered', 'true');
  });
  
  // Inicializar paginación
  actualizarPaginacion();
});
</script>@endsection
