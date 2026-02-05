
<?php $__env->startSection('title', 'ventas'); ?>
<?php $__env->startSection('content'); ?>
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
          <input type="text" placeholder="Factura, total, estado..." id="buscadorVentas">
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

    <?php if($ventas->isEmpty()): ?>
      <div class="sales-table">
        <div class="table-header">
          <h3>Historial de Ventas</h3>
        </div>
        <div style="padding: 40px; text-align: center; color: #64748b;">
          <p>No hay ventas registradas.</p>
        </div>
      </div>
    <?php else: ?>
      <!-- ========== sales table start ========== -->
      <div class="sales-table">
        <div class="table-header">
          <h3>Historial de Ventas</h3>
        </div>
        
        <div id="noCoincidencias" style="display:none; padding: 40px; text-align: center; color: #ef4444; font-weight: 500;">
          No se encontraron ventas para esta búsqueda.
        </div>

        <div id="ventasError" style="display:none; padding: 16px 24px; color: #ef4444; font-weight: 500;">
        </div>
        
        <div class="table-responsive" data-registros-pagina="<?php echo e($registrosPorPagina ?? 10); ?>">
          <table id="tablaVentas">
            <thead>
              <tr>
                <th>Número Factura</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Forma de Pago</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr data-venta-id="<?php echo e($venta->id); ?>">
                <td>
                  <span class="invoice-number">
                    <?php echo e(optional($venta->factura)->numero ?? '-'); ?>

                  </span>
                </td>
                <td>
                  <span class="truncate truncate-medium"
      data-bs-toggle="tooltip"
      data-bs-title="<?php echo e(formatoHoraInteligente($venta->fecha) ?? '--:--'); ?>">
    <?php echo e(formatoHoraInteligente($venta->fecha) ?? '--:--'); ?>

</span>

                </td>
                <td>
                  <span class="client-name truncate truncate-long"
                        data-bs-toggle="tooltip"
                        data-bs-title="<?php echo e(optional($venta->factura)->cliente_nombre ?? 'Consumidor final'); ?>">
                    <?php echo e(optional($venta->factura)->cliente_nombre ?? 'Consumidor final'); ?>

                  </span>
                </td>
                <td><span class="amount">$<?php echo e(number_format($venta->total ?? 0, 0, ',', '.')); ?></span></td>
                <td>
                  <span class="payment-method truncate truncate-short"
                        data-bs-toggle="tooltip"
                        data-bs-title="<?php echo e(optional($venta->factura)->forma_pago ?? '-'); ?>">
                    <?php
                      $formaPago = optional($venta->factura)->forma_pago ?? '-';
                      $icono = '';
                      if(stripos($formaPago, 'tarjeta') !== false) $icono = '';
                      elseif(stripos($formaPago, 'transferencia') !== false) $icono = '';
                    ?>
                    <?php echo e($icono); ?> <?php echo e($formaPago); ?>

                  </span>
                </td>
                <td>
                  <?php
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
                  ?>
                  <span class="status-badge <?php echo e($estadoClass); ?>"><?php echo e($estadoTexto); ?></span>
                </td>
                <td>
                  <?php
                    $puedeAnular = (
                      ($venta->estado === 'completada') &&
                      ($venta->factura && optional($venta->factura)->fecha_emision && \Carbon\Carbon::parse($venta->factura->fecha_emision)->isSameDay(\Carbon\Carbon::now()))
                    );
                  ?>
                  <div class="action-buttons">
                    <a href="<?php echo e(route('ventas.detalle', $venta)); ?>" class="btn-action btn-print">
                      <i class="mdi mdi-file-document-outline"></i> Detalles
                    </a>
                    <?php if($puedeAnular): ?>
                      <button class="btn-action btn-cancel btn-anular" 
                              data-url="<?php echo e(route('ventas.devolucion.confirmar', $venta)); ?>"
                              data-invoice="<?php echo e($venta->factura->numero ?? 'FA-'.$venta->id); ?>">
                        <i class="lni lni-close"></i> Anular
                      </button>
                    <?php else: ?>
                      <?php if($venta->estado !== 'anulada'): ?>
                        <button class="btn-action btn-disabled " data-bs-toggle="tooltip" data-bs-title="Solo se puede anular el mismo día">
                          <i class="lni lni-close"></i> Anular
                        </button>
                      <?php endif; ?>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
    <?php endif; ?>
  </div>
</section>

<!-- ========== Modal Anular start ========== -->
<div class="modal-overlay" id="cancelModal">
  <div class="modal-conten">
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
        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
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

/* ========== TRUNCADO DE TEXTO ========== */
.truncate {
  display: block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}

.truncate-long {
  max-width: 180px;
}

.truncate-medium {
  max-width: 120px;
}

.truncate-short {
  max-width: 80px;
}

/* Tooltip Bootstrap */
.bs-tooltip-auto[data-popper-placement^="top"] > .tooltip-arrow,
.bs-tooltip-top > .tooltip-arrow {
  bottom: calc(-1 * var(--bs-tooltip-arrow-height));
}

.tooltip-inner {
  max-width: 300px;
  word-wrap: break-word;
  white-space: normal;
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

.modal-conten {
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
  const ventasError = document.getElementById('ventasError');

  let currentUrl = null;
  let currentRow = null;
  let currentPage = 1;
  let currentFetchController = null;
  let currentFetchId = 0;
  const registrosPorPagina = parseInt(document.querySelector('.table-responsive').getAttribute('data-registros-pagina')) || 10;
  const empresaCobraIva = <?php echo e($empresa && $empresa->cobra_iva ? 'true' : 'false'); ?>;

  const paginationPrevBtn = document.querySelector('.pagination button:first-child');
  const paginationNextBtn = document.querySelector('.pagination button:last-child');
  const pageInfo = document.querySelector('.page-info');
  const tbody = tabla.querySelector('tbody');

  let debounceTimer = null;
  const DEBOUNCE_MS = 400; // 300-500ms sugerido

  // Escape regex safe
  function escapeRegex(text) {
    return text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  }

  function formatCurrency(n) {
    try {
      return '$' + new Intl.NumberFormat('es-CL').format(n || 0);
    } catch (e) {
      return '$' + (n || 0);
    }
  }

  function highlightText(text, term) {
    if (!term) return text;
    const safe = escapeRegex(term).slice(0, 50);
    if (!safe) return text;
    const re = new RegExp('(' + safe + ')', 'gi');
    return text.replace(re, '<mark class="highlight">$1</mark>');
  }

  function showVentasError(message) {
    if (!ventasError) return;
    ventasError.textContent = message;
    ventasError.style.display = '';
  }

  function hideVentasError() {
    if (!ventasError) return;
    ventasError.textContent = '';
    ventasError.style.display = 'none';
  }

  function handleSessionExpired() {
    showVentasError('Tu sesión ha expirado');
    setTimeout(() => {
      window.location.reload();
    }, 500);
  }

  // ========== TOOLTIP TRUNCADO ==========
  function initializeTooltipsInRow(element) {
    if (!element) return;

    const tooltipElements = element.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipElements.forEach(el => {
      if (el._bsTooltip) return;
      if (el.classList.contains('truncate')) {
        if (el.scrollWidth <= el.clientWidth) return;
      }
      try {
        new bootstrap.Tooltip(el, {
          placement: 'top',
          trigger: 'hover focus',
          boundary: 'viewport'
        });
      } catch (e) {
        // Bootstrap no disponible
      }
    });
  }

  function initializeAllTooltips() {
    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipElements.forEach(el => {
      if (el._bsTooltip) return;
      if (el.classList.contains('truncate')) {
        if (el.scrollWidth <= el.clientWidth) return;
      }
      try {
        new bootstrap.Tooltip(el, {
          placement: 'top',
          trigger: 'hover focus',
          boundary: 'viewport'
        });
      } catch (e) {
        // Bootstrap no disponible
      }
    });
  }

  // Abrir / cerrar modal
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

  cancelBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });

  // Enviar anulación (igual que antes)
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

      if (res.status === 419) {
        messageBox.style.display = 'block';
        messageBox.style.background = '#fee2e2';
        messageBox.style.color = '#991b1b';
        messageBox.textContent = 'Tu sesión ha expirado';
        setTimeout(() => window.location.reload(), 500);
        return;
      }

      let json = null;
      try {
        json = await res.json();
      } catch (e) {
        throw new Error('JSON inválido');
      }

      if (res.ok && json.success) {
        // Refrescar página actual para reflejar cambios
        fetchVentas();
        messageBox.style.display = 'block';
        messageBox.style.background = '#d1fae5';
        messageBox.style.color = '#065f46';
        messageBox.textContent = json.message || 'Venta anulada correctamente';
        setTimeout(closeModal, 1200);
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

  // Render filas desde respuesta JSON
  function renderRows(items, searchTerm) {
    tbody.innerHTML = '';
    if (!items || !items.length) {
      noCoincidencias.style.display = '';
      document.querySelector('.table-responsive').style.display = 'none';
      document.querySelector('.pagination').style.display = 'none';
      return;
    }

    noCoincidencias.style.display = 'none';
    document.querySelector('.table-responsive').style.display = '';
    document.querySelector('.pagination').style.display = '';

    items.forEach(item => {
      const tr = document.createElement('tr');
      tr.setAttribute('data-venta-id', item.id);

      const numeroFactura = item.numero_factura || ('FA-' + item.id);
      const estado = (item.estado || '---').toLowerCase();
      let estadoClass = 'status-completed';
      let estadoTexto = 'Completada';
      if (estado === 'anulada') { estadoClass = 'status-cancelled'; estadoTexto = 'Anulada'; }
      else if (estado === 'pendiente') { estadoClass = 'status-pending'; estadoTexto = 'Pendiente'; }
      else if (estado === 'completada') { estadoClass = 'status-completed'; estadoTexto = 'Completada'; }
      else { estadoTexto = estado.charAt(0).toUpperCase() + estado.slice(1); }

      const cols = [];
      cols.push('<td><span class="invoice-number">' + highlightText(numeroFactura, searchTerm) + '</span></td>');
      cols.push('<td><span class="truncate truncate-medium" data-bs-toggle="tooltip" data-bs-title="' + (item.fecha || '-') + '">' + (item.fecha || '-') + '</span></td>');
      cols.push('<td><span class="client-name truncate truncate-long" data-bs-toggle="tooltip" data-bs-title="' + (item.cliente || 'Consumidor final') + '">' + highlightText(item.cliente || 'Consumidor final', searchTerm) + '</span></td>');
      cols.push('<td><span class="amount">' + formatCurrency(item.total) + '</span></td>');

      cols.push('<td><span class="payment-method truncate truncate-short" data-bs-toggle="tooltip" data-bs-title="' + (item.forma_pago || '-') + '">' + (item.forma_pago || '-') + '</span></td>');
      cols.push('<td><span class="status-badge ' + estadoClass + '">' + estadoTexto + '</span></td>');

      // Acciones
      let acciones = '<div class="action-buttons">';
      acciones += '<a href="/ventas/' + item.id + '/detalle"  class="btn-action btn-print"><i class="mdi mdi-file-document-outline"></i> Detalles</a>';
      if (item.puede_anular) {
        acciones += '<button class="btn-action btn-cancel btn-anular" data-url="/ventas/' + item.id + '/devolucion" data-invoice="' + numeroFactura + '"><i class="lni lni-close"></i> Anular</button>';
      } else {
        if (estado !== 'anulada') {
          acciones += '<button class="btn-action btn-disabled " disabled data-bs-toggle="tooltip" data-bs-title="Solo se puede anular el mismo día"><i class="lni lni-close"></i> Anular</button>';
        }
      }
      acciones += '</div>';

      cols.push('<td>' + acciones + '</td>');

      tr.innerHTML = cols.join('');
      tbody.appendChild(tr);

      initializeTooltipsInRow(tr);
    });

    // Re-attach anular listeners
    attachAnularHandlers();
  }

  function attachAnularHandlers() {
    document.querySelectorAll('.btn-anular').forEach(btn => {
      btn.removeEventListener('click', btn._anularHandler);
      const handler = function() {
        const url = btn.dataset.url || btn.getAttribute('data-url');
        const invoice = btn.dataset.invoice || btn.getAttribute('data-invoice');
        const row = btn.closest('tr');
        openModal(url, row, invoice);
      };
      btn.addEventListener('click', handler);
      btn._anularHandler = handler;
    });
  }

  function updatePaginationFromResponse(resp) {
    const current = resp.current_page || resp.currentPage || 1;
    const last = resp.last_page || resp.lastPage || resp.last_page || 1;
    currentPage = current;
    pageInfo.textContent = `Página ${current} de ${last || 1}`;
    paginationPrevBtn.disabled = current <= 1;
    paginationNextBtn.disabled = current >= last;
  }

  async function fetchVentas() {
    const params = new URLSearchParams();
    params.set('page', currentPage);
    params.set('per_page', registrosPorPagina);
    const s = (buscador.value || '').trim();
    if (s) params.set('search', s);
    if (fechaInicio.value) params.set('date_from', fechaInicio.value);
    if (fechaFin.value) params.set('date_to', fechaFin.value);

    const requestId = ++currentFetchId;
    if (currentFetchController) {
      currentFetchController.abort();
    }
    currentFetchController = new AbortController();

    try {
      const res = await fetch(window.location.pathname + '?' + params.toString(), {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        signal: currentFetchController.signal
      });
      if (requestId !== currentFetchId) return;

      if (res.status === 419) {
        handleSessionExpired();
        return;
      }

      if (!res.ok) {
        throw new Error('HTTP ' + res.status);
      }

      let json = null;
      try {
        json = await res.json();
      } catch (e) {
        throw new Error('JSON inválido');
      }

      if (requestId !== currentFetchId) return;

      hideVentasError();
      const items = json.data || json.data || json; // handle different shapes
      // Laravel paginator returns an object with 'data' array
      const dataArray = json.data || json.data || (Array.isArray(json) ? json : (json.data || []));
      renderRows(dataArray, s);
      updatePaginationFromResponse(json);
    } catch (err) {
      if (err && err.name === 'AbortError') return;
      showVentasError('Error al cargar ventas. Intenta de nuevo.');
    }
  }

  // Debounced search + reset page
  buscador.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function() {
      currentPage = 1;
      fetchVentas();
    }, DEBOUNCE_MS);
  });

  fechaInicio.addEventListener('change', function() { currentPage = 1; fetchVentas(); });
  fechaFin.addEventListener('change', function() { currentPage = 1; fetchVentas(); });

  paginationPrevBtn.addEventListener('click', function() {
    if (currentPage > 1) { currentPage--; fetchVentas(); }
  });
  paginationNextBtn.addEventListener('click', function() {
    currentPage++; fetchVentas();
  });

  // Inicializar: cargar datos server-side (mantiene compatibilidad con render inicial)
  initializeAllTooltips();
  fetchVentas();
});
</script><?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\optenadvance\laragon\www\optenadvance\resources\views/ventas/index.blade.php ENDPATH**/ ?>