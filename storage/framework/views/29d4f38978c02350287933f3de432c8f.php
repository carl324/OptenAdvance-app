

<?php $__env->startSection('title', 'Productos'); ?>

<?php $__env->startSection('content'); ?>

<style>
    .inventory-section {
      padding: 30px 0;
    }

    /* Formulario de Productos */
    .product-form-card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
    }

    .form-header {
      margin-bottom: 20px;
    }

    .form-header h6 {
      font-size: 18px;
      font-weight: 600;
      color: #1a202c;
      margin-bottom: 5px;
    }

    .form-header p {
      font-size: 14px;
      color: #64748b;
      margin: 0;
    }

    .product-form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-field {
      display: flex;
      flex-direction: column;
      min-width: 0;
    }

    .form-field.full-width {
      grid-column: 1 / -1;
    }

    .form-field label {
      font-size: 14px;
      font-weight: 500;
      color: #334155;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .form-field label i {
      font-size: 16px;
      color: #64748b;
    }

    .form-field input, .form-field select {
      padding: 10px 12px;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      font-size: 14px;
      font-family: inherit;
      transition: border 0.2s;
    }

    .form-field input:focus, .form-field select:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .input-with-symbol {
      display: flex;
      align-items: center;
      position: relative;
    }

    .input-with-symbol .input-symbol {
      position: absolute;
      left: 12px;
      color: #64748b;
      font-weight: 500;
      font-size: 14px;
      pointer-events: none;
    }

    .input-with-symbol input {
      padding-left: 30px;
      width: 100%;
    }

    .input-with-suffix {
      position: relative;
    }

    .input-with-suffix .input-suffix {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #64748b;
      font-weight: 500;
      font-size: 14px;
      pointer-events: none;
    }

    .input-with-suffix input {
      padding-right: 35px;
      width: 100%;
    }

    .quantity-control-wrapper {
      display: flex;
      align-items: center;
      gap: 12px;
      background: #f8fafc;
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid #e2e8f0;
    }

        .quantity-btn {
      width: 36px;
      height: 36px;
      min-width: 36px;
      border: none;
      background: white;
      border-radius: 6px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      color: #3b82f6;
      font-weight: 600;
      transition: all 0.2s;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      flex-shrink: 0;
    }

    .quantity-btn:hover {
      background: #3b82f6;
      color: white;
      transform: scale(1.05);
    }

    .quantity-btn:active {
      transform: scale(0.95);
    }

    .quantity-display {
      flex: 1;
      text-align: center;
      font-size: 20px;
      font-weight: 700;
      color: #1a202c;
      padding: 8px;
      background: white;
      border-radius: 6px;
      min-width: 50px;
    }

    .quantity-display:focus {
     background: #f8f9fa; /* Feedback visual al editar */
    }
   
    .submit-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-top: 20px;
    }

    .btn-reset, .btn-submit {
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.2s;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .btn-reset {
      background: #e2e8f0;
      color: #334155;
    }

    .btn-reset:hover {
      background: #cbd5e1;
    }

    .btn-submit {
      background: #2478ff;
      color: white;
    }

    .btn-submit:hover {
      background: #2563eb;
    }

    .btn-submit:disabled {
      background: #cbd5e1;
      cursor: not-allowed;
    }

    /* Alertas */
    .alert-custom {
      padding: 14px 16px;
      border-radius: 8px;
      margin-bottom: 16px;
      font-size: 14px;
      font-weight: 500;
      animation: slideDown 0.3s ease-out;
      opacity: 0;
      transform: translateY(-10px);
      transition: opacity 0.2s, transform 0.2s;
    }

    .alert-custom.show {
      opacity: 1;
      transform: translateY(0);
    }

    .alert-custom.success {
      background: #ecfdf5;
      color: #047857;
      border-left: 4px solid #10b981;
    }

    .alert-custom.error {
      background: #fef2f2;
      color: #dc2626;
      border-left: 4px solid #ef4444;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Tabla de Movimientos */
    .movements-card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .movements-card h6 {
      font-size: 18px;
      font-weight: 600;
      color: #1a202c;
      margin-bottom: 5px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .movements-card h6 i {
      color: #3b82f6;
    }

    .movements-card p {
      font-size: 14px;
      color: #64748b;
      margin-bottom: 20px;
    }

    .table-wrapper {
      overflow-x: auto;
    }

    .striped-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    .striped-table thead {
      background: #f8fafc;
      border-bottom: 2px solid #e2e8f0;
    }

    .striped-table th {
      padding: 12px;
      text-align: left;
      font-weight: 600;
      color: #334155;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .striped-table td {
      padding: 12px;
      border-bottom: 1px solid #e2e8f0;
      color: #475569;
    }

    .striped-table tbody tr:hover {
      background: #f8fafc;
    }

    .badge-entrada {
      display: inline-block;
      background: #ecfdf5;
      color: #047857;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
    }

    .badge-salida {
      display: inline-block;
      background: #fef2f2;
      color: #dc2626;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
    }

    /* Tabla de Productos */
    .card-style {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .card-style h6 {
      font-size: 18px;
      font-weight: 600;
      color: #1a202c;
      margin-bottom: 5px;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    .table thead {
      background: #f8fafc;
      border-bottom: 2px solid #e2e8f0;
    }

    .table th {
      padding: 12px;
      text-align: left;
      font-weight: 600;
      color: #334155;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .table th h6 {
      font-size: 13px;
      margin: 0;
    }

    .table td {
      padding: 12px;
      border-bottom: 1px solid #e2e8f0;
      color: #475569;
    }

    /* Cuando el usuario no tiene acciones (empleado), aumentar altura de filas */
    .table.table-rows-tall td {
      padding-top: 18px;
      padding-bottom: 18px;
    }

    .table td.min-width {
      white-space: nowrap;
      max-width: 150px;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    /* Centrar contenido de stock */
    .table td.min-width.text-centerr {
      text-align: center !important;
      text-overflow: clip;
    }

    .table td.min-width.text-centerr .view,
    .table td.min-width.text-centerr .truncate {
      text-align: center !important;
    }

    .table tbody tr:hover {
      background: #f8fafc;
    }

    .table tbody tr:hover input,
    .table tbody tr:hover select {
      border-color: #3b82f6 !important;
    }

    .table .action {
      display: flex;
      gap: 8px;
    }

    .table button {
      background: none;
      border: none;
      cursor: pointer;
      font-size: 16px;
      padding: 4px 8px;
      border-radius: 4px;
      transition: all 0.2s;
    }

    .table .text-primary {
      color: #3b82f6;
    }
    .icon-yelow {
      color: #f59e0b;
    }
    .icon-red {
      color: #ef4444;
    }
    .icon-green {
      color: #10b981;
    }
    
    .table .text-primary:hover {
      background: #eff6ff;
    }

    .table .text-danger {
      color: #dc2626;
    }

    .table .text-danger:hover {
      background: #fef2f2;
    }

    .table .msg {
      font-size: 12px;
      margin-top: 4px;
      display: block;
      min-height: 16px;
      color: #64748b;
    }

    .table input {
      padding: 6px 8px;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      font-size: 13px;
      width: 100%;
      font-family: inherit;
    }

    .table input:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }

    .table span.view {
      cursor: default;
    }

    .table input.edit {
      margin-top: 4px;
    }

    /* Paginación */
    .pagination {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      padding: 20px;
      margin-top: 20px;
    }

    .pagination button {
      padding: 8px 12px;
      background: white;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
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
      align-items: flex-start;
    }

    .modal-header-text {
      flex: 1;
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

       .icon-warning {
  width: 48px;
  height: 48px;
  background: #fef2f2;          /* rojo claro */
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.icon-warning i {
  font-size: 24px;
  color: #dc2626;               /* rojo crítico */
}
    .warning-message {
  padding: 12px 16px;
  background: #fef2f2;          /* rojo muy claro */
  border-left: 3px solid #dc2626; /* rojo crítico */
  border-radius: 6px;
  font-size: 14px;
  color: #7f1d1d;               /* texto rojo oscuro */
  margin-bottom: 20px;
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
    @media (max-width: 992px) {
      .product-form {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .product-form-card, .movements-card {
        padding: 20px;
      }

      .submit-section {
        grid-template-columns: 1fr;
      }

      .table {
        font-size: 12px;
      }

      .table td, .table th {
        padding: 8px;
      }
    }

    /* ========== TRUNCADO DE TEXTO GLOBAL ========== */
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

    /* Variantes más pequeñas para tabla de movimientos */
    .truncate-xs {
      max-width: 50px;
    }

    .truncate-sm {
      max-width: 100px;
    }

    /* Tooltip personalizado (si es necesario) */
    .bs-tooltip-auto[data-popper-placement^="top"] > .tooltip-arrow,
    .bs-tooltip-top > .tooltip-arrow {
      bottom: calc(-1 * var(--bs-tooltip-arrow-height));
    }

    .tooltip-inner {
      max-width: 300px;
      word-wrap: break-word;
      white-space: normal;
    }
    /* Centrar contenido en columnas específicas */
    td.text-centerr,
    .text-centerr {
      text-align: center !important;
    }

    /* Para la tabla de productos - centrar stock */
    .table td.text-centerr .view,
    .table td.text-centerr .truncate {
      display: block;
      width: 100%;
      text-align: center !important;
    }

    .table td.text-centerr input.edit {
      text-align: center !important;
    }

    /* Para la tabla de movimientos - centrar cantidad */
    .striped-table td.text-centerr,
    .striped-table td.text-centerr span {
      text-align: center !important;
    }
</style>

<section class="main-content">
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        
        <!-- ========== title-wrapper end ========== -->

        <!-- ========== tables-wrapper start ========== -->
        <div class="tables-wrapper">
            <div class="row">
                <div class="col-lg-12 px-0">
                    <div class="inventory-section">
                        <div class="row">
                            <?php if(auth()->user()->role === 'admin'): ?>
                            <!-- Formulario Agregar Producto -->
                            <div class="col-lg-6">
    <div class="product-form-card">
        <div class="form-header">
            <h6> Agregar Nuevo Producto </h6>
            <p>Complete los datos del producto para registrarlo en el inventario</p>
        </div>

        <div id="alertContainer"></div>

        <form id="productForm">
            <div class="product-form">
                <!-- Nombre del Producto -->
                <div class="form-field full-width">
                    <label>
                        <i class="lni lni-text-format"></i>
                        Nombre del Producto
                    </label>
                    <input type="text" placeholder="Ej: Coca Cola 350ml" id="productName" required>
                </div>

                <!-- Precio de Compra -->
                <div class="form-field">
                    <label>
                        <i class="lni lni-shopping-bag"></i>
                        Precio de Compra
                    </label>
                    <div class="input-with-symbol">
                        <span class="input-symbol">$</span>
                        <input type="text" placeholder="0" inputmode="numeric" id="precioCompra" required>
                    </div>
                </div>

                <!-- Precio de Venta -->
                <div class="form-field">
                    <label>
                        <i class="lni lni-money-protection"></i>
                        Precio de Venta
                    </label>
                    <div class="input-with-symbol">
                        <span class="input-symbol">$</span>
                        <input type="text" placeholder="0" inputmode="numeric" id="precioVenta" required>
                    </div>
                </div>

                <!-- Ganancia + IVA (lado a lado si cobra IVA) -->
                <?php if($empresa && $empresa->cobra_iva): ?>
                    <!-- Ganancia -->
                    <div class="form-field">
                        <label>
                            <i class="lni lni-stats-up"></i>
                            Ganancia por Unidad
                        </label>
                        <div style="display: flex; gap: 12px; align-items: center; background: #f8f9fa; padding: 12px; border-radius: 8px;">
                            <span id="gananciaValor" style="font-size: 18px; font-weight: 700; color: #28a745;">$0</span>
                            <span id="gananciaMargen" style="font-size: 14px; font-weight: 600; background: #e9ecef; padding: 4px 10px; border-radius: 6px; color: #495057;">0%</span>
                        </div>
                    </div>

                    <!-- IVA -->
                    <div class="form-field">
                        <label>
                            <i class="lni lni-calculator"></i>
                            IVA (%)
                        </label>
                        <div class="input-with-symbol input-with-suffix">
                            <input type="number" placeholder="19" step="1" id="ivaPercent" value="19" min="0" max="100">
                            <span class="input-suffix">%</span>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Ganancia (ocupa todo el ancho si NO cobra IVA) -->
                    <div class="form-field full-width">
                        <label>
                            <i class="lni lni-stats-up"></i>
                            Ganancia por Unidad
                        </label>
                        <div style="display: flex; gap: 12px; align-items: center; background: #f8f9fa; padding: 12px; border-radius: 8px;">
                            <span id="gananciaValor" style="font-size: 18px; font-weight: 700; color: #28a745;">$0</span>
                            <span id="gananciaMargen" style="font-size: 14px; font-weight: 600; background: #e9ecef; padding: 4px 10px; border-radius: 6px; color: #495057;">0%</span>
                        </div>
                    </div>
                    <input type="hidden" id="ivaPercent" value="0">
                <?php endif; ?>

                <!-- Stock Inicial -->
                <div class="form-field full-width">
                    <label>
                        <i class="lni lni-layers"></i>
                        Stock Inicial
                    </label>
                    <div class="quantity-control-wrapper">
                        <button type="button" class="quantity-btn" onclick="decreaseStock(event)">
                            <i class="lni lni-minus"></i>
                        </button>
                        <input type="text" inputmode="numeric" class="quantity-display" id="stockValue" value="0" placeholder="0"/>
                        <button type="button" class="quantity-btn" onclick="increaseStock(event)">
                            <i class="lni lni-plus"></i>
                        </button>
                        <span class="quantity-label">unidades</span>
                    </div>
                </div>
            </div>

            <div class="submit-section">
                <button type="button" class="btn-reset" onclick="resetForm(event)">
                    <i class="lni lni-reload"></i> Limpiar
                </button>
                <button type="button" class="btn-submit" id="btnAddProduct" onclick="addProduct(event)">
                    <i class="lni lni-checkmark-circle"></i> Agregar Producto
                </button>
            </div>
        </form>
    </div>
    
</div>

                            <!-- Tabla de Movimientos -->
                            <div class="col-lg-6">
                                <div class="movements-card">
                                    <h6><i class="lni lni-reload"></i> Movimientos Recientes</h6>
                                    <p>Registro de entradas y salidas de productos en la tienda</p>

                                    <div class="table-wrapper table-responsive">
                                        <table class="striped-table">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Producto</th>
                                                    <th>Cantidad</th>
                                                    <th>Tipo</th>
                                                    <th>Motivo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__empty_1 = true; $__currentLoopData = $movimientos->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <tr>
                                                        <td>
                                                            <span class="truncate truncate-xs" 
                                                                  data-bs-toggle="tooltip" 
                                                                 data-bs-title="<?php echo e(formatoHoraInteligente($m->created_at) ?? '-'); ?>">
           <?php echo e(formatoHoraInteligente($m->created_at) ?? '-'); ?>

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="truncate truncate-sm" 
                                                                  data-bs-toggle="tooltip" 
                                                                  data-bs-title="<?php echo e($m->producto_nombre ?? 'Producto #' . $m->producto_id); ?>">
                                                                <?php echo e($m->producto_nombre ?? 'Producto #' . $m->producto_id); ?>

                                                            </span>
                                                        </td>
                                                            <td class="text-centerr">
                                                              <span class="truncate truncate-xs" data-bs-toggle="tooltip" data-bs-title="<?php echo e($m->cantidad); ?>">
                                                                <?php echo e($m->cantidad); ?>

                                                              </span>
                                                            </td>
                                                        <td>
                                                            <?php if($m->tipo === 'entrada'): ?>
                                                                <span class="badge-entrada">Entrada</span>
                                                            <?php else: ?>
                                                                <span class="badge-salida">Salida</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                                $origenLower = strtolower($m->origen ?? '');
                                                                if($origenLower === 'registro_producto') {
                                                                    $origenText = 'Registro';
                                                                } elseif($origenLower === 'venta_anulada') {
                                                                    $origenText = 'Anulada';
                                                                } else {
                                                                    $origenText = ucfirst($m->origen ?? '-');
                                                                }
                                                            ?>
                                                            <span class="truncate truncate-sm" 
                                                                  data-bs-toggle="tooltip" 
                                                                  data-bs-title="<?php echo e($origenText); ?>">
                                                                <?php echo e($origenText); ?>

                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <tr>
                                                        <td colspan="5" style="text-align: center; color: #999;">Sin movimientos</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php endif; ?>

            <!-- Tabla de Productos -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-style mb-30">
                        <div class="title d-flex align-items-center justify-content-between">
  
  <!-- IZQUIERDA -->
  <div>
    <h6>Tabla de Productos</h6>
    <p style="font-size:14px; color:#64748b; margin-bottom:0;">
      Lista de productos registrados con información de precios y stock.
    </p>
  </div>
    
  <!-- DERECHA -->
  <div class="ms-auto">
    <div class="input-group input-group-sm search-pos" style="width:240px;">
      <span class="input-group-text bg-light border-0">
        <i class="lni lni-search-alt"></i>
      </span>
      <input
        type="text"
        id="buscar-producto"
        class="form-control bg-light border-0"
        placeholder="Buscar producto..."
        autocomplete="off"
      />
    </div>
  </div>

   </div>
    <br>
                        <div class="table-wrapper table-responsive">
                            <table class="table <?php echo e(auth()->user()->role !== 'admin' ? 'table-rows-tall' : ''); ?>">
<thead>
    <tr>
        <th><h6>ID</h6></th>
        <th><h6>Nombre</h6></th>
        <?php if(auth()->user()->role === 'admin'): ?>
            <th><h6>P. Compra</h6></th>
        <?php endif; ?>
        <th><h6>P. Venta</h6></th>
        <?php if(auth()->user()->role === 'admin'): ?>
            <th><h6>Ganancia</h6></th>
        <?php endif; ?>
        <?php if($empresa && $empresa->cobra_iva): ?>
            <th><h6>IVA %</h6></th>
            <th><h6>Precio final</h6></th>
        <?php endif; ?>
        <th><h6>Stock</h6></th>
        <?php if(auth()->user()->role === 'admin'): ?>
          <th><h6>Acciones</h6></th>
        <?php endif; ?>
    </tr>
</thead>
                                <tbody id="productos-tbody">
                                      <?php echo $__env->make('productos._table', ['productos' => $productos, 'empresa' => $empresa, 'showActions' => (auth()->user()->role === 'admin')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginación Minimalista AJAX -->
                        <?php echo $__env->make('productos._pagination', ['productos' => $productos, 'search' => ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- ========== tables-wrapper end ========== -->
    </div>
</section>

<!-- Modal Eliminar Producto -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal-conten">
    <div class="modal-header">
      <div class="icon-warning">
        <i class="lni lni-warning"></i>
      </div>
      <div class="modal-header-text">
        <h3>¿Desea eliminar este producto?</h3>
        <p id="modalProductName"></p>
      </div>
    </div>
    
    <div class="modal-body">
      <div class="warning-message">
        Esta acción eliminará el producto. No se puede deshacer.
      </div>
    </div>
    
    <div class="modal-footer">
      <button class="modal-btn modal-btn-cancel" id="cancelDelete" type="button">
        Cancelar
      </button>
      <button class="modal-btn modal-btn-confirm" id="confirmDelete" type="button">
        <i class="lni lni-checkmark"></i> Eliminar
      </button>
    </div>
  </div>
</div>

<script>
const csrf = '<?php echo e(csrf_token()); ?>';
let currentStock = 0;
let lastSearchTerm = '';
let clearSearchRequested = false;
const SEARCH_TIMEOUT_MS = 8000;

const COBRA_IVA = <?php echo json_encode((bool)($empresa && $empresa->cobra_iva), 15, 512) ?>;

// ========== FUNCIONES DEL FORMULARIO AGREGAR PRODUCTO ==========

function increaseStock(e) {
    e.preventDefault();
    currentStock++;
    document.getElementById('stockValue').value = currentStock;
}

function decreaseStock(e) {
    e.preventDefault();
    if (currentStock > 0) {
        currentStock--;
        document.getElementById('stockValue').value = currentStock;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const stockInput = document.getElementById('stockValue');
    if (stockInput) {
        stockInput.addEventListener('input', function() {
            // Solo números
            this.value = this.value.replace(/\D/g, '');
            currentStock = parseInt(this.value || 0);
        });
        
        // Actualizar currentStock al perder foco
        stockInput.addEventListener('blur', function() {
            const val = parseInt(this.value || 0);
            this.value = val; // Normalizar
            currentStock = val;
        });
    }
});

function resetForm(e, clearAlert = true) {
  e.preventDefault();
  document.getElementById('productForm').reset();
  document.getElementById('precioCompra').value = '';
  document.getElementById('precioVenta').value = '';
  document.getElementById('ivaPercent').value = <?php if($empresa && $empresa->cobra_iva): ?>'19'<?php else: ?>'0'<?php endif; ?>;
  currentStock = 0;
  document.getElementById('stockValue').value = '0';
  
  // Resetear ganancia
  document.getElementById('gananciaValor').textContent = '$0';
  document.getElementById('gananciaMargen').textContent = '0%';
  document.getElementById('gananciaValor').style.color = '#28a745';
  
  if (clearAlert) {
    document.getElementById('alertContainer').innerHTML = '';
  }
}

<?php if(auth()->user()->role === 'admin'): ?>
// Formatear precios en tiempo real
const precioCompraInput = document.getElementById('precioCompra');
const precioVentaInput = document.getElementById('precioVenta');

if (precioCompraInput) {
  precioCompraInput.addEventListener('input', function() {
    const onlyDigits = this.value.replace(/\D/g, '');
    const intVal = parseInt(onlyDigits || '0', 10);
    this.value = formatCOP(intVal);
    calcularGanancia(); // ← Calcular ganancia
  });
}

if (precioVentaInput) {
  precioVentaInput.addEventListener('input', function() {
    const onlyDigits = this.value.replace(/\D/g, '');
    const intVal = parseInt(onlyDigits || '0', 10);
    this.value = formatCOP(intVal);
    calcularGanancia(); // ← Calcular ganancia
  });
}

// Función para calcular ganancia en tiempo real
function calcularGanancia() {
    const compra = parseCOP(precioCompraInput.value);
    const venta = parseCOP(precioVentaInput.value);
    
    const ganancia = venta - compra;
    const margen = compra > 0 ? ((ganancia / compra) * 100).toFixed(1) : 0;
    
    document.getElementById('gananciaValor').textContent = '$' + formatCOP(ganancia);
    document.getElementById('gananciaMargen').textContent = margen + '%';
    
    // Color verde si positivo, rojo si negativo
    const gananciaEl = document.getElementById('gananciaValor');
    if (ganancia < 0) {
        gananciaEl.style.color = '#dc3545';
    } else if (ganancia === 0) {
        gananciaEl.style.color = '#6c757d';
    } else {
        gananciaEl.style.color = '#28a745';
    }
}
<?php endif; ?>

function insertProductoFila(p) {
  const tbody = document.querySelector('.card-style .table tbody');
  if (!tbody) return;

  // Si existía fila de “No hay productos registrados”, eliminarla
  tbody.querySelectorAll('tr').forEach(tr => {
    const td = tr.querySelector('td[colspan]');
    if (td && td.textContent && td.textContent.toLowerCase().includes('no hay productos')) {
      tr.remove();
    }
  });

  // Evitar duplicados: si ya existe, solo resaltar
  const existing = document.getElementById(`producto-${p.id}`);
  if (existing) {
    existing.style.background = '#ecfdf5';
    setTimeout(() => { existing.style.background = ''; }, 1600);
    return;
  }

const precioCompraInt = Math.round(Number(p.precio_compra) || 0);
const precioVentaInt = Math.round(Number(p.precio_venta) || 0);
const precioConIvaInt = Math.round(Number(p.precio_con_iva) || 0);
const ivaFloat = Number(p.iva) || 0;
const ganancia = precioVentaInt - precioCompraInt;
const margen = precioCompraInt > 0 ? ((ganancia / precioCompraInt) * 100).toFixed(1) : 0;
const gananciaColor = ganancia >= 0 ? '#28a745' : '#dc3545';

  const rowHtml = `
    <tr id="producto-${p.id}">
      <td class="min-width"><p>${p.id}</p></td>
      <td class="min-width">
        <span class="view truncate truncate-long" data-field="nombre" data-bs-toggle="tooltip" data-bs-title="${escapeHtml(p.nombre)}">${escapeHtml(p.nombre)}</span>
        <input class="edit" data-field="nombre" type="text" value="${escapeHtml(p.nombre)}" hidden>
      </td>
      <td class="min-width">
  <span class="view truncate" data-field="precio_compra" data-bs-toggle="tooltip" data-bs-title="$${formatCOP(precioCompraInt)}">$${formatCOP(precioCompraInt)}</span>
  <input class="edit precio_input" data-field="precio_compra" type="text" inputmode="numeric" value="${formatCOP(precioCompraInt)}" hidden>
</td>
<td class="min-width">
  <span class="view truncate" data-field="precio_venta" data-bs-toggle="tooltip" data-bs-title="$${formatCOP(precioVentaInt)}">$${formatCOP(precioVentaInt)}</span>
  <input class="edit precio_input" data-field="precio_venta" type="text" inputmode="numeric" value="${formatCOP(precioVentaInt)}" hidden>
</td>
<td class="min-width">
  <span class="view truncate" style="color: ${gananciaColor}; font-weight: 600;" data-bs-toggle="tooltip" data-bs-title="$${formatCOP(ganancia)} (${margen}%)">$${formatCOP(ganancia)}</span>
</td>
      ${COBRA_IVA ? `
      <td class="min-width">
        <span class="view truncate" data-field="iva" data-bs-toggle="tooltip" data-bs-title="${ivaFloat > 0 ? ivaFloat + '%' : '-'}">${ivaFloat > 0 ? ivaFloat + '%' : '-'}</span>
        <input class="edit iva_input" data-field="iva" type="number" step="1" value="${ivaFloat}" hidden>
      </td>
      <td class="min-width">
        <span class="view truncate precio_con_iva_span" data-field="precio_con_iva" data-bs-toggle="tooltip" data-bs-title="$${formatCOP(precioConIvaInt)}">$${formatCOP(precioConIvaInt)}</span>
        <input class="edit" data-field="precio_con_iva" type="text" value="${formatCOP(precioConIvaInt)}" hidden readonly>
      </td>
      ` : ''}
      <td class="min-width">
        <span class="view stock_view" data-field="stock" data-bs-toggle="tooltip" data-bs-title="${p.stock}">${p.stock}</span>
        <input class="edit stock_input" data-field="stock" type="text" value="${p.stock}" hidden data-original-stock="${p.stock}">
      </td>
      <td>
        <div class="action">
          <button type="button" class="icon-yelow" onclick="editarProducto(${p.id})"data-bs-toggle="tooltip" 
        data-bs-title="Editar">
            <i class="lni lni-pencil"></i>
          </button>
          <button type="button" class="text-danger" onclick="eliminarProducto(${p.id})"data-bs-toggle="tooltip" 
        data-bs-title="Eliminar">
            <i class="lni lni-trash-can"></i>
          </button>
          <button type="button" class="icon-green" onclick="guardarProducto(${p.id})" hidden data-bs-toggle="tooltip" 
        data-bs-title="Guardar">
            <i class="lni lni-checkmark-circle"></i>
          </button>
          <button type="button" class="icon-red" onclick="cancelarEdicion(${p.id})" hidden data-bs-toggle="tooltip" 
        data-bs-title="Cancelar">
            <i class="lni lni-close"></i>
          </button>
        </div>
        <span class="msg"></span>
      </td>
    </tr>
  `;

  tbody.insertAdjacentHTML('afterbegin', rowHtml);
  const newTr = document.getElementById(`producto-${p.id}`);
  if (newTr) {
    newTr.style.background = '#ecfdf5';
    setTimeout(() => { newTr.style.background = ''; }, 1600);
  }
  
  // Inicializar tooltips en la nueva fila
  initializeTooltipsInRow(newTr);
}

  function formatOrigenLabel(origen) {
    const o = String(origen || '').toLowerCase();
    if (o === 'registro_producto') return 'Registro';
    if (o === 'venta_anulada') return 'Anulada';
    if (!o) return '-';
    return o.charAt(0).toUpperCase() + o.slice(1);
  }

  function insertMovimientoRow(m) {
    if (!m) return;
    const tbody = document.querySelector('.striped-table tbody');
    if (!tbody) return;

    // Si existía fila de “Sin movimientos”, eliminarla
    tbody.querySelectorAll('tr').forEach(tr => {
      const td = tr.querySelector('td[colspan]');
      if (td && td.textContent && td.textContent.toLowerCase().includes('sin movimientos')) {
        tr.remove();
      }
    });

    const tipoLower = String(m.tipo || '').toLowerCase();
    const tipoBadge = tipoLower === 'entrada'
      ? '<span class="badge-entrada">Entrada</span>'
      : '<span class="badge-salida">Salida</span>';

    const rowHtml = `
      <tr>
        <td><span class="truncate truncate-xs" data-bs-toggle="tooltip" data-bs-title="${escapeHtml(m.fecha || '-')}">${escapeHtml(m.fecha || '-')}</span></td>
        <td><span class="truncate truncate-sm" data-bs-toggle="tooltip" data-bs-title="${escapeHtml(m.producto_nombre || '-')}">${escapeHtml(m.producto_nombre || '-')}</span></td>
        <td class="text-centerr"><span class="truncate truncate-xs" data-bs-toggle="tooltip" data-bs-title="${escapeHtml(m.cantidad)}">${escapeHtml(m.cantidad)}</span></td>
        <td>${tipoBadge}</td>
        <td><span class="truncate truncate-sm" data-bs-toggle="tooltip" data-bs-title="${escapeHtml(formatOrigenLabel(m.origen))}">${escapeHtml(formatOrigenLabel(m.origen))}</span></td>
      </tr>
    `;

    tbody.insertAdjacentHTML('afterbegin', rowHtml);

    // Resaltar el movimiento insertado
    const firstRow = tbody.querySelector('tr');
    if (firstRow) {
      firstRow.style.background = '#ecfdf5';
      setTimeout(() => { firstRow.style.background = ''; }, 1600);
      
      // Inicializar tooltips en la nueva fila
      initializeTooltipsInRow(firstRow);
    }

    // Mantener máximo 5 filas (como la vista)
    const rows = tbody.querySelectorAll('tr');
    if (rows.length > 5) {
      for (let i = 5; i < rows.length; i++) rows[i].remove();
    }
  }

async function addProduct(e) {
    e.preventDefault();
    
    const nombre = document.getElementById('productName').value.trim();
    const precioCompra = parseCOP(document.getElementById('precioCompra').value);
    const precioVenta = parseCOP(document.getElementById('precioVenta').value);
    const iva = parseFloat(document.getElementById('ivaPercent').value) || 0;
    const stock = currentStock;

    if (!nombre) {
        showAlert('El nombre del producto es requerido', 'error');
        return;
    }

    if (precioCompra < 0) {
        showAlert('El precio de compra no puede ser negativo', 'error');
        return;
    }

    if (precioVenta <= 0) {
        showAlert('El precio de venta debe ser mayor a 0', 'error');
        return;
    }

    
    const btn = document.getElementById('btnAddProduct');
    btn.disabled = true;

    try {
        const res = await fetch('/productos', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ nombre, precio_compra: precioCompra, precio_venta: precioVenta, iva, stock })
        });

        if (res.status === 419) {
          showAlert('Tu sesión ha expirado. Redirigiendo al inicio de sesión...', 'error');
          setTimeout(() => {
            window.location.href = '/login';
          }, 1500);
          return;
        }

        const contentType = (res.headers.get('content-type') || '').toLowerCase();

        // Si el servidor devuelve JSON, parsearlo y usarlo
        let result = null;
        if (contentType.includes('application/json')) {
            result = await res.json();
            if (!res.ok) throw result;

            showAlert('Producto agregado correctamente', 'success');
            resetForm(e, false);

            if (result.producto) insertProductoFila(result.producto);
            if (result.movimiento) {
              insertMovimientoRow(result.movimiento);
            }

        } else {
            // Respuesta inesperada
            throw { message: 'Error al procesar la solicitud' };
        }

    } catch (error) {
        const mensaje = error.message === 'El producto ya existe' ? 'El producto ya existe' : 'No se pudo agregar el producto';
        showAlert(mensaje, 'error');
    } finally {
        btn.disabled = false;
    }
}

function showAlert(mensaje, tipo) {
    const container = document.getElementById('alertContainer');
    container.innerHTML = `<div class="alert-custom ${tipo} show">${mensaje}</div>`;
    setTimeout(() => {
        container.innerHTML = '';
    }, 4000);
}

/**
 * Carga productos con búsqueda y paginación AJAX server-side
 * @param {number} pagina - Número de página a cargar
 * @param {string} search - Término de búsqueda (opcional)
 */
async function cargarPaginaAjax(pagina, search = null) {
    try {
        // Obtener búsqueda actual si no se proporciona
    if (search === null || typeof search === 'undefined') {
      search = lastSearchTerm || '';
    }

        // Construir URL con parámetros
        const url = new URL('/productos', window.location.origin);
        url.searchParams.append('page', pagina);
        if (search) {
            url.searchParams.append('search', search);
        }

        const respuesta = await fetch(url.toString(), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (respuesta.status === 419) {
          showAlert('Tu sesión ha expirado. Redirigiendo al inicio de sesión...', 'error');
          setTimeout(() => {
            window.location.href = '/login';
          }, 1500);
          return;
        }

        if (!respuesta.ok) throw new Error('Error al cargar página');

        const result = await respuesta.json();
        if (!result.success) throw new Error(result.message || 'Error desconocido');

        // Reemplazar tbody con HTML renderizado del servidor
        const tbodyActual = document.querySelector('#productos-tbody');
        if (tbodyActual) {
            tbodyActual.innerHTML = result.html;
            // Reinicializar tooltips en las nuevas filas
            setTimeout(() => initializeAllTooltips(), 100);
        }

        // Reemplazar paginación
        const paginationContainer = document.querySelector('.pagination');
        if (paginationContainer) {
            paginationContainer.outerHTML = result.pagination;
        }

        lastSearchTerm = search || '';

        // Scroll suave al inicio de la tabla
        const tableWrapper = document.querySelector('.card-style');
        if (tableWrapper) {
            tableWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

    } catch (error) {
        showAlert('No se pudo cargar la página. Intenta de nuevo.', 'error');
    }
}

// ========== FUNCIONES DE LA TABLA (EDICIÓN INLINE) ==========

function formatCOP(valueInt) {
    const n = parseInt(valueInt, 10) || 0;
    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function escapeHtml(str) {
  if (str === null || str === undefined) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function parseCOP(formatted) {
    if (!formatted) return 0;
    const digits = String(formatted).replace(/\D/g, '');
    return parseInt(digits || '0', 10);
}

function roundPercentageInteger(precio, iva) {
    return Math.floor((precio * iva + 50) / 100);
}

function editarProducto(id) {
    const tr = document.getElementById(`producto-${id}`);
    tr.querySelectorAll('.view').forEach(e => e.hidden = true);
    tr.querySelectorAll('.edit').forEach(e => e.hidden = false);

    // Sanitizar stock
    const stockInput = tr.querySelector('.stock_input');
    if (stockInput) {
        stockInput.oninput = function() {
            this.value = this.value.replace(/\D/g, '');
        };
    }

    // ← NUEVO: Sanitizar y formatear precios (compra y venta)
    const precioInputs = tr.querySelectorAll('.precio_input');
    precioInputs.forEach(input => {
        input.oninput = function() {
            const onlyDigits = this.value.replace(/\D/g, '');
            const intVal = parseInt(onlyDigits || '0', 10);
            this.value = formatCOP(intVal);
        };
    });

    // Sanitizar IVA
    const ivaInput = tr.querySelector('.iva_input');
    if (ivaInput) {
        ivaInput.oninput = function() {
            let value = this.value.replace(/[^\d.]/g, '');
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            this.value = value;
        };
    }

    attachRecalculo(tr);
    toggleButtons(tr, true);
}

function cancelarEdicion(id) {
    const tr = document.getElementById(`producto-${id}`);
    tr.querySelectorAll('.view').forEach(e => e.hidden = false);
    tr.querySelectorAll('.edit').forEach(e => e.hidden = true);
    tr.querySelector('.msg').innerText = '';

    const precioInput = tr.querySelector('.precio_input');
    const precioView = tr.querySelector('span.view[data-field="precio"]');
    if (precioInput && precioView) precioInput.value = formatCOP(parseCOP(precioView.innerText));

    const stockInput = tr.querySelector('.stock_input');
    if (stockInput) {
        stockInput.value = stockInput.dataset.originalStock;
    }

    toggleButtons(tr, false);
}

function toggleButtons(tr, editing) {
    const btnEdit = tr.querySelector('[onclick*="editarProducto"]');
    const btnDelete = tr.querySelector('[onclick*="eliminarProducto"]');
    const btnSave = tr.querySelector('[onclick*="guardarProducto"]');
    const btnCancel = tr.querySelector('[onclick*="cancelarEdicion"]');

    if (btnEdit) btnEdit.hidden = editing;
    if (btnDelete) btnDelete.hidden = editing;
    if (btnSave) btnSave.hidden = !editing;
    if (btnCancel) btnCancel.hidden = !editing;
}

function attachRecalculo(tr) {
    const precioInput = tr.querySelector('.precio_input');
    const ivaInput = tr.querySelector('.iva_input');
    const precioConIvaSpan = tr.querySelector('.precio_con_iva_span');

    if (!precioInput || !ivaInput || !precioConIvaSpan) return;

    function sanitizeAndFormatPrecio() {
        const onlyDigits = precioInput.value.replace(/\D/g, '');
        const intVal = parseInt(onlyDigits || '0', 10);
        precioInput.value = formatCOP(intVal);
        recalcular(intVal, parseFloat(ivaInput.value) || 0);
    }

    function onIvaChange() {
        const precioInt = parseCOP(precioInput.value);
        recalcular(precioInt, parseFloat(ivaInput.value) || 0);
    }

    function recalcular(precioInt, ivaFloat) {
        precioInt = parseInt(precioInt || 0, 10);
        ivaFloat = parseFloat(ivaFloat || 0);
        
        if (ivaFloat < 0) ivaFloat = 0;
        if (ivaFloat > 100) ivaFloat = 100;
        
        const ivaAmount = roundPercentageInteger(precioInt, ivaFloat);
        const precioConIva = precioInt + ivaAmount;
        precioConIvaSpan.innerText = '$' + formatCOP(precioConIva);
        const precioConIvaInput = tr.querySelector('input[data-field="precio_con_iva"]');
        if (precioConIvaInput) precioConIvaInput.value = formatCOP(precioConIva);
    }

    precioInput.oninput = sanitizeAndFormatPrecio;
    ivaInput.oninput = onIvaChange;

    sanitizeAndFormatPrecio();
}

async function guardarProducto(id) {
  const tr = document.getElementById(`producto-${id}`);
  const msg = tr.querySelector('.msg');
  msg.innerText = 'Guardando...';

  // Construir solo con campos modificados
  const data = {};

  tr.querySelectorAll('.edit').forEach(input => {
    const field = input.dataset.field;
    if (!field) return;
    if (field === 'precio_con_iva') return; // nunca enviar

    const viewEl = tr.querySelector(`span.view[data-field="${field}"]`);

    // Precio: comparar en formato numérico usando parseCOP
    // Precio Compra: comparar en formato numérico usando parseCOP
if (field === 'precio_compra') {
  const newVal = parseCOP(input.value);
  const oldVal = viewEl ? parseCOP(viewEl.innerText) : 0;
  if (String(newVal) !== String(oldVal)) {
    data.precio_compra = newVal;
  }
  return;
}

// Precio Venta: comparar en formato numérico usando parseCOP
if (field === 'precio_venta') {
  const newVal = parseCOP(input.value);
  const oldVal = viewEl ? parseCOP(viewEl.innerText) : 0;
  if (String(newVal) !== String(oldVal)) {
    data.precio_venta = newVal;
  }
  return;
}
    // IVA: comparar en float, eliminar '%'
    if (field === 'iva') {
      let newVal = parseFloat(input.value) || 0;
      if (newVal < 0) newVal = 0;
      if (newVal > 100) newVal = 100;
      let oldRaw = viewEl ? String(viewEl.innerText).replace('%', '') : '0';
      let oldVal = parseFloat(oldRaw) || 0;
      if (String(newVal) !== String(oldVal)) {
        data.iva = newVal;
      }
      return;
    }

    // Stock: comparar como entero, eliminar caracteres no numéricos
    if (field === 'stock') {
      const newVal = parseInt(String(input.value).replace(/\D/g, ''), 10) || 0;
      const oldVal = parseInt((viewEl ? String(viewEl.innerText).replace(/\D/g, '') : ''), 10) || 0;
      if (String(newVal) !== String(oldVal)) {
        data.stock = newVal;
      }
      return;
    }

    // Otros campos (ej. nombre): comparar como string
    const newRaw = String(input.value).trim();
    const oldRaw = viewEl ? String(viewEl.innerText).trim() : '';
    if (String(newRaw) !== String(oldRaw)) {
      data[field] = newRaw;
    }
  });

  // Si no hubo cambios, salir silenciosamente
  if (Object.keys(data).length === 0) {
    msg.innerText = '';
    return;
  }

  // Si se envía precio, validar que sea mayor a 0
  if (typeof data.precio !== 'undefined') {
    const precioVal = Number(data.precio);
    if (!precioVal || precioVal <= 0) {
      msg.style.color = 'red';
      msg.innerText = 'El precio debe ser mayor a 0.';
      return;
    }
  }

  if (tr.dataset.saving === '1') return;
  tr.dataset.saving = '1';

  disableRow(tr, true);

  try {
    const res = await fetch(`/productos/${id}`, {
      method: 'PUT',
      headers: {
        'X-CSRF-TOKEN': csrf,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(data)
    });

    if (res.status === 419) {
      msg.style.color = 'red';
      msg.innerText = 'Sesión expirada.';
      showAlert('Tu sesión ha expirado. Redirigiendo al inicio de sesión...', 'error');
      setTimeout(() => {
      window.location.href = '/login';
      }, 1500);
      return;
    }

    const result = await res.json();

    if (!res.ok) throw result;

    // Actualizar vistas solo para campos que efectivamente enviamos
    if (typeof data.nombre !== 'undefined') {
      const nombreSpan = tr.querySelector('span.view[data-field="nombre"]');
      if (nombreSpan) nombreSpan.innerText = data.nombre;
    }

    if (typeof data.precio_compra !== 'undefined') {
  const precioCompraSpan = tr.querySelector('span.view[data-field="precio_compra"]');
  if (precioCompraSpan) precioCompraSpan.innerText = '$' + formatCOP(data.precio_compra);
}

if (typeof data.precio_venta !== 'undefined') {
  const precioVentaSpan = tr.querySelector('span.view[data-field="precio_venta"]');
  if (precioVentaSpan) precioVentaSpan.innerText = '$' + formatCOP(data.precio_venta);
}
    if (typeof data.iva !== 'undefined') {
      const ivaSpan = tr.querySelector('span.view[data-field="iva"]');
      if (ivaSpan) ivaSpan.innerText = data.iva + '%';
    }

    if (result.producto && typeof result.producto.precio_con_iva !== 'undefined') {
      const precioConIvaSpan = tr.querySelector('.precio_con_iva_span');
      if (precioConIvaSpan) precioConIvaSpan.innerText = '$' + formatCOP(result.producto.precio_con_iva);
      const precioConIvaInput = tr.querySelector('input[data-field="precio_con_iva"]');
      if (precioConIvaInput) precioConIvaInput.value = formatCOP(result.producto.precio_con_iva);
    }

    if (result.producto && typeof result.producto.stock !== 'undefined') {
      const stockView = tr.querySelector('.stock_view');
      const stockInput = tr.querySelector('.stock_input');
      if (stockView) stockView.innerText = result.producto.stock;
      if (stockInput) {
        stockInput.value = result.producto.stock;
        stockInput.dataset.originalStock = result.producto.stock;
      }
    }

    msg.style.color = 'green';
    msg.innerText = 'Guardado';

    setTimeout(() => {
      cancelarEdicion(id);
      msg.innerText = '';
    }, 1500);

  } catch (e) {
    // Mantener manejo de errores existente
    msg.style.color = 'red';
    if (e && e.message) msg.innerText = e.message;
    else msg.innerText = 'Error al actualizar. Intenta de nuevo.';
  } finally {
    disableRow(tr, false);
    tr.dataset.saving = '0';
  }
}

async function eliminarProducto(id) {
    const tr = document.getElementById(`producto-${id}`);
    const nombreSpan = tr.querySelector('span.view[data-field="nombre"]');
    const nombreProducto = nombreSpan ? nombreSpan.innerText : 'Producto #' + id;
    
    // Mostrar modal
    const modal = document.getElementById('deleteModal');
    document.getElementById('modalProductName').textContent = 'Producto: ' + nombreProducto;
    modal.classList.add('active');
    
    // Guardar ID para usar en confirmar
    modal.dataset.productId = id;
}

// ========== EVENT LISTENERS DEL MODAL (SOLO PARA ADMIN) ==========
<?php if(auth()->user()->role === 'admin'): ?>
// Cerrar modal de eliminar
const cancelBtn = document.getElementById('cancelDelete');
if (cancelBtn) {
  cancelBtn.addEventListener('click', function() {
    const modalEl = document.getElementById('deleteModal');
    if (modalEl) modalEl.classList.remove('active');
  });
}

const deleteModalEl = document.getElementById('deleteModal');
if (deleteModalEl) {
  deleteModalEl.addEventListener('click', function(e) {
    if (e.target === this) {
      this.classList.remove('active');
    }
  });
}

// Confirmar eliminación
const confirmBtn = document.getElementById('confirmDelete');
if (confirmBtn) {
  confirmBtn.addEventListener('click', async function() {
    const modal = document.getElementById('deleteModal');
    const id = modal ? modal.dataset.productId : null;
    const tr = id ? document.getElementById(`producto-${id}`) : null;
    const msg = tr ? tr.querySelector('.msg') : null;

    if (!id || !tr || !msg) {
      return;
    }

    confirmBtn.disabled = true;
    msg.innerText = 'Eliminando...';

    try {
      const res = await fetch(`/productos/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json'
        }
      });

      if (res.status === 419) {
        msg.innerText = 'Sesión expirada.';
        showAlert('Tu sesión ha expirado. Redirigiendo al inicio de sesión...', 'error');
        setTimeout(() => {
        window.location.href = '/login';
        }, 1500);
        return;
      }

      const result = await res.json();
      if (!res.ok) throw result;

      tr.remove();
      if (modal) modal.classList.remove('active');

    } catch (error) {
      msg.innerText = 'Error al eliminar. Intenta de nuevo.';
    } finally {
      confirmBtn.disabled = false;
    }
  });
}
<?php endif; ?>

function disableRow(tr, state) {
    tr.querySelectorAll('button, input, select').forEach(e => e.disabled = state);
}

// ========== INICIALIZACIÓN DE TOOLTIPS ==========

/**
 * Inicializa tooltips de Bootstrap en un elemento específico
 * @param {HTMLElement} element - Elemento donde inicializar los tooltips
 */
function initializeTooltipsInRow(element) {
    if (!element) return;
    
    const tooltipElements = element.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipElements.forEach(el => {
        // Si ya tiene tooltip inicializado, no hacer nada
        if (el._bsTooltip) return;
        
        // Si tiene clase truncate, solo mostrar tooltip si está truncado
        if (el.classList.contains('truncate')) {
            // Verificar si el texto está siendo truncado
            if (el.scrollWidth <= el.clientWidth) {
                // No está truncado, no necesita tooltip
                return;
            }
        }
        
        try {
            // Inicializar Bootstrap tooltip
            new bootstrap.Tooltip(el, {
                placement: 'top',
                trigger: 'hover focus',
                boundary: 'viewport'
            });
        } catch (e) {
            // Bootstrap no disponible, silenciar
        }
    });
}

/**
 * Inicializa todos los tooltips en la página
 */
function initializeAllTooltips() {
    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipElements.forEach(el => {
        // Si ya tiene tooltip inicializado, no hacer nada
        if (el._bsTooltip) return;
        
        // Si tiene clase truncate, solo mostrar tooltip si está truncado
        if (el.classList.contains('truncate')) {
            // Verificar si el texto está siendo truncado
            if (el.scrollWidth <= el.clientWidth) {
                // No está truncado, no necesita tooltip
                return;
            }
        }
        
        try {
            new bootstrap.Tooltip(el, {
                placement: 'top',
                trigger: 'hover focus',
                boundary: 'viewport'
            });
        } catch (e) {
            // Bootstrap no disponible, silenciar
        }
    });
}

// Inicializar tooltips cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    initializeAllTooltips();
    initSearchFunctionality();
});

// ========== FUNCIONALIDAD DE BÚSQUEDA ==========

/**
 * Debounce para evitar búsquedas frecuentes
 */
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

/**
 * Realiza búsqueda server-side con debounce
 * El backend retorna HTML con paginación respetando el filtro
 */
async function buscarProductos(termino) {
  const terminoLimpio = termino.trim();

  if (terminoLimpio.length > 100) {
    showAlert('La búsqueda supera los 100 caracteres', 'error');
    return;
  }

  if (terminoLimpio === '' && !clearSearchRequested && lastSearchTerm) {
    return;
  }

  const tbodyActual = document.querySelector('#productos-tbody');
  const previousHtml = tbodyActual ? tbodyActual.innerHTML : '';

  if (tbodyActual) {
    tbodyActual.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #999;">Buscando...</td></tr>';
  }

  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), SEARCH_TIMEOUT_MS);

  try {
    // Construir URL con parámetro de búsqueda
    const url = new URL('/productos', window.location.origin);
    url.searchParams.append('page', 1); // Resetear a página 1
    if (terminoLimpio) {
      url.searchParams.append('search', terminoLimpio);
    }

    const respuesta = await fetch(url.toString(), {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      signal: controller.signal
    });

    if (respuesta.status === 419) {
      showAlert('Tu sesión ha expirado. Redirigiendo al inicio de sesión...', 'error');
      setTimeout(() => {
        window.location.href = '/login';
      }, 1500);
      return;
    }

    if (!respuesta.ok) throw new Error('Error al buscar');

    const result = await respuesta.json();
    if (!result.success) throw new Error(result.message || 'Error desconocido');

    // Reemplazar contenido de la tabla con HTML renderizado del servidor
    if (tbodyActual) {
      tbodyActual.innerHTML = result.html;
      // Reinicializar tooltips en las nuevas filas
      setTimeout(() => initializeAllTooltips(), 100);
    }

    // Reemplazar paginación
    const paginationContainer = document.querySelector('.pagination');
    if (paginationContainer) {
      paginationContainer.outerHTML = result.pagination;
    }

    lastSearchTerm = terminoLimpio;

  } catch (error) {
    if (error && error.name === 'AbortError') {
      showAlert('La búsqueda tardó demasiado. Intenta de nuevo.', 'error');
    } else {
      console.error('Error en búsqueda:', error);
      showAlert('Error en la búsqueda. Intenta de nuevo.', 'error');
    }
    if (tbodyActual) {
      tbodyActual.innerHTML = previousHtml;
    }
  } finally {
    clearTimeout(timeoutId);
    clearSearchRequested = false;
  }
}

/**
 * Filtrar productos (función anterior - se mantiene por compatibilidad pero ya no se usa)
 * DEPRECATED: Usar buscarProductos() en su lugar para server-side
 */
function filtrarProductos(termino) {
    // Esta función ya no se utiliza
    // La búsqueda ahora es server-side mediante buscarProductos()
}

/**
 * Inicializa la funcionalidad de búsqueda con debounce
 * Ahora utiliza búsqueda server-side
 */
function initSearchFunctionality() {
    const inputBusqueda = document.querySelector('#buscar-producto');
    if (!inputBusqueda) return;

    // Crear función con debounce de 300ms
    const busquedaDebounced = debounce(function(e) {
        const termino = e.target.value;
    // Si el campo está vacío, recargar todos los productos automáticamente
    if (termino.trim() === '' && lastSearchTerm !== '') {
      clearSearchRequested = true;
      lastSearchTerm = '';
      buscarProductos('');
      return;
    }

    buscarProductos(termino);
    }, 300);

    // Event listener con debounce
    inputBusqueda.addEventListener('input', busquedaDebounced);

    // Limpiar búsqueda al presionar ESC
    inputBusqueda.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
        clearSearchRequested = true;
        lastSearchTerm = '';
            this.value = '';
            buscarProductos('');
            this.blur();
        }
    });
}

// Actualizar búsqueda después de cargar página (paginación)
// La búsqueda ahora es server-side, no es necesario limpiar el input
// Solo reinicializamos los tooltips de las nuevas filas
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\optenadvance\app\www\resources\views/productos/index.blade.php ENDPATH**/ ?>