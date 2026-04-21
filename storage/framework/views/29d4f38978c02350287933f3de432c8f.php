

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
      overflow: visible;
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
    /* ========== DROPDOWN TRES PUNTOS ========== */
.producto-dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-trigger {
    background: none;
    border: none;
    cursor: pointer;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 18px;
    transition: all 0.2s;
}

.dropdown-trigger:hover {
    background: #f1f5f9;
    color: #475569;
}

.dropdown-menu-custom {
    display: none;
    position: absolute;
    right: 0;
    top: 36px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    z-index: 999;
    min-width: 140px;
    overflow: hidden;
    animation: dropdownFadeIn 0.15s ease-out;
}

.dropdown-menu-custom.open {
    display: block;
}

@keyframes dropdownFadeIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.dropdown-menu-custom button {
    width: 100%;
    padding: 10px 16px;
    background: none;
    border: none;
    text-align: left;
    font-size: 14px;
    font-weight: 500;
    color: #334155;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.15s;
    border-radius: 0;
}

.dropdown-menu-custom button:hover {
    background: #f8fafc;
}

.dropdown-menu-custom button.danger {
    color: #dc2626;
}

.dropdown-menu-custom button.danger:hover {
    background: #fef2f2;
}
.dropdown-menu-custom {
    min-width: 140px;
    max-width: 140px;
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
                            
            <!-- Tabla de Productos -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-style mb-30">
                        <div class="title d-flex align-items-center justify-content-between">
  
  <!-- IZQUIERDA -->
  <div>
    <h6>Tabla de Productos</h6>
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
    
<!-- DERECHA -->
  <div class="ms-auto d-flex align-items-center gap-2">
    <?php if(auth()->user()->role === 'admin'): ?>
    <button type="button" onclick="abrirModalAgregar()" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
      <i class="lni lni-plus"></i> Agregar Producto
    </button>
    <?php endif; ?>
    
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
<!-- Modal Agregar Producto -->
<div id="agregarProductoModal" class="modal-overlay" style="background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(8px);  align-items: center; justify-content: center; position: fixed; inset: 0; z-index: 1000; padding: 24px;">
  
  <div style="background: #ffffff; width: 100%; max-width: 820px; border-radius: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden; border: 1px solid #f1f5f9;">
    
    <div style="padding: 32px 40px 24px; display: flex; align-items: flex-start; justify-content: space-between;">
      <div style="display: flex; gap: 16px; align-items: center;">
        <div style="width: 48px; height: 48px; background: #2478ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(36, 120, 255, 0.2);">
          <i class="lni lni-package" style="font-size: 22px; color: #ffffff;"></i>
        </div>
        <div>
          <h3 style="margin: 0; font-size: 1.35rem; font-weight: 700; color: #1e293b; letter-spacing: -0.02em;">Registrar Producto</h3>
          <p style="margin: 2px 0 0; font-size: 0.875rem; color: #64748b;">Ingresa los detalles técnicos y comerciales</p>
        </div>
      </div>
      <button type="button" onclick="cerrarModalAgregar()" style="background: #f8fafc; border: 1px solid #e2e8f0; cursor: pointer; width: 36px; height: 36px; border-radius: 10px; color: #94a3b8; display: flex; align-items: center; justify-content: center; transition: 0.2s;">
        <i class="lni lni-close" style="font-size: 12px;"></i>
      </button>
    </div>

    <div style="padding: 0 40px 32px;">
      <div id="alertContainerModal"></div>

      <form id="productForm">
        <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 32px;">
          
          <div style="display: flex; flex-direction: column; gap: 20px;">
            
            <div style="display: flex; flex-direction: column; gap: 6px;">
              <label style="font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.025em;">Nombre del producto</label>
              <input type="text" placeholder="Ej. Coca Cola Zero 500ml" id="productName" required 
                     style="width: 100%; padding: 12px 16px; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #ffffff; color: #1e293b; font-size: 0.95rem; outline: none; transition: all 0.2s;"
                     onfocus="this.style.borderColor='#2478ff'; this.style.boxShadow='0 0 0 3px rgba(36, 120, 255, 0.1)'"
                     onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
            </div>

            <div style="display: flex; flex-direction: column; gap: 6px;">
              <label style="font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.025em;">Código de Barras</label>
              <div style="position: relative;">
                <i class="lni lni-barcode" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                <input type="text" placeholder="Escanea aquí..." id="codigoBarras" autocomplete="off"
                       style="width: 100%; padding: 12px 12px 12px 42px; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #f8fafc; font-size: 0.95rem; outline: none;">
              </div>
            </div>
<div style="display: flex; flex-direction: column; gap: 6px;">
  <label style="font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.025em;">Unidad de Medida</label>
  <select id="unidadMedida"
    style="width: 100%; padding: 12px 16px; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #ffffff; color: #1e293b; font-size: 0.95rem; outline: none;">
    <optgroup label="Básicas">
      <option value="Unidad">Unidad (und)</option>
      <option value="Par">Par</option>
      <option value="Docena">Docena</option>
      <option value="Caja">Caja</option>
      <option value="Paquete">Paquete</option>
      <option value="Sobre">Sobre</option>
      <option value="Frasco">Frasco</option>
      <option value="Botella">Botella</option>
      <option value="Lata">Lata</option>
      <option value="Tubo">Tubo</option>
    </optgroup>
    <optgroup label="Peso">
      <option value="Gramo">Gramo (g)</option>
      <option value="Kilogramo">Kilogramo (kg)</option>
      <option value="Libra">Libra (lb)</option>
      <option value="Tonelada">Tonelada (t)</option>
      <option value="Onza">Onza (oz)</option>
    </optgroup>
    <optgroup label="Volumen">
      <option value="Mililitro">Mililitro (ml)</option>
      <option value="Litro">Litro (L)</option>
      <option value="Galón">Galón (gal)</option>
      <option value="Metro cúbico">Metro cúbico (m³)</option>
    </optgroup>
    <optgroup label="Longitud">
      <option value="Milímetro">Milímetro (mm)</option>
      <option value="Centímetro">Centímetro (cm)</option>
      <option value="Metro">Metro (m)</option>
      <option value="Metro lineal">Metro lineal</option>
      <option value="Kilómetro">Kilómetro (km)</option>
      <option value="Pulgada">Pulgada (in)</option>
      <option value="Pie">Pie (ft)</option>
    </optgroup>
    <optgroup label="Área">
      <option value="Metro cuadrado">Metro cuadrado (m²)</option>
      <option value="Centímetro cuadrado">Centímetro cuadrado (cm²)</option>
      <option value="Hectárea">Hectárea (ha)</option>
    </optgroup>
  </select>
</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
               <div style="display: flex; flex-direction: column; gap: 6px;">
                  <label style="font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase;">Precio Compra</label>
                  <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 14px; color: #94a3b8; font-weight: 500;">$</span>
                    <input type="text" id="precioCompra" placeholder="0" style="width: 100%; padding: 12px 12px 12px 28px; border-radius: 10px; border: 1.5px solid #e2e8f0; font-weight: 600; outline: none;">
                  </div>
               </div>
               <div style="display: flex; flex-direction: column; gap: 6px;">
                  <label style="font-size: 0.75rem; font-weight: 700; color: #2478ff; text-transform: uppercase;">Precio Venta</label>
                  <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 14px; color: #2478ff; font-weight: 500;">$</span>
                    <input type="text" id="precioVenta" placeholder="0" style="width: 100%; padding: 12px 12px 12px 28px; border-radius: 10px; border: 1.5px solid #2478ff55; font-weight: 600; outline: none; background: #f0f7ff;">
                  </div>
               </div>
            </div>
          </div>

          <div style="display: flex; flex-direction: column; gap: 16px;">
            
            <div style="background: #ffffff; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; justify-content: center; height: 100px;">
              <span id="gananciaLabel" style="font-size: 0.75rem; font-weight: 600; color: #64748b;  margin-bottom: 4px;">Ganancia por   und</span>
              <div style="display: flex; align-items: center; justify-content: space-between;">
                <span id="gananciaValor" style="font-size: 1.75rem; font-weight: 800; color: #10b981;">$0</span>
                <span id="gananciaMargen" style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 8px; font-size: 0.8rem; font-weight: 700;">0%</span>
              </div>
            </div>

            <div style="background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; flex-grow: 1;">
              <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 16px;">Stock Inicial</label>
              <div style="display: flex; align-items: center; background: #ffffff; padding: 8px; border-radius: 12px; border: 1px solid #e2e8f0; justify-content: space-between;">
                <button type="button" onclick="decreaseStock(event)" style="width: 32px; height: 32px; border-radius: 8px; border: none; background: #f1f5f9; color: #64748b; cursor: pointer;"><i class="lni lni-minus"></i></button>
                <input type="text" id="stockValue" value="0" style="width: 50px; text-align: center; border: none; font-size: 1.25rem; font-weight: 700; color: #1e293b; outline: none;">
                <button type="button" onclick="increaseStock(event)" style="width: 32px; height: 32px; border-radius: 8px; border: none; background: #2478ff; color: #ffffff; cursor: pointer;"><i class="lni lni-plus"></i></button>
              </div>
              <p style="text-align: center; margin: 12px 0 0; font-size: 0.8rem; color: #94a3b8;">Cantidad en Inventario</p>
            </div>
          </div>
        </div>
       <?php if($empresa && $empresa->cobra_iva): ?>
<div style="display: flex; flex-direction: column; gap: 6px; margin-top: 8px;">
  <label style="font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.025em;">IVA (%)</label>
  <div style="position: relative; display: flex; align-items: center;">
    <input type="number" id="ivaPercent" placeholder="19" step="1" value="19" min="0" max="100"
           style="width: 100%; padding: 12px 36px 12px 16px; border-radius: 10px; border: 1.5px solid #e2e8f0; font-weight: 600; outline: none;">
    <span style="position: absolute; right: 14px; color: #94a3b8; font-weight: 500;">%</span>
  </div>
</div>
<?php else: ?>
<input type="hidden" id="ivaPercent" value="0">
<?php endif; ?>
        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #f1f5f9; display: flex; gap: 12px;">
          <button type="button" onclick="resetForm(event)" style="padding: 0 16px; height: 48px; border-radius: 10px; border: 1px solid #e2e8f0; background: #ffffff; color: #64748b; font-weight: 600; cursor: pointer; transition: 0.2s;">
            <i class="lni lni-reload"></i>
          </button>
          <button type="button" id="btnAddProduct" onclick="addProduct(event)" 
                  style="flex: 1; height: 48px; border-radius: 10px; border: none; background: #2478ff; color: #ffffff; font-size: 0.95rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(36, 120, 255, 0.2); display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s;"
                  onmouseover="this.style.background='#1d63d3'" 
                  onmouseout="this.style.background='#2478ff'">
            Guardar Producto
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal Editar Producto -->
<div id="editarProductoModal" class="modal-overlay">
  <div style="background: #ffffff; width: 100%; max-width: 920px; border-radius: 24px; padding: 0; box-shadow: 0 8px 32px rgba(0,0,0,0.10); border: 1px solid #f1f5f9; overflow: hidden; position: relative;">
    
    <button type="button" onclick="cerrarModalEditar()" style="position: absolute; top: 16px; right: 16px; background: #f8fafc; border: 1px solid #e2e8f0; cursor: pointer; width: 36px; height: 36px; border-radius: 10px; color: #94a3b8; display: flex; align-items: center; justify-content: center; z-index: 10;">
        <i class="lni lni-close" style="font-size: 12px;"></i>
    </button>
  
  <div style="background: #ffffff; width: 100%; max-width: 920px; border-radius: 24px; padding: 0; box-shadow: 0 8px 32px rgba(0,0,0,0.10); border: 1px solid #f1f5f9; overflow: hidden;">
    
    <form id="editProductForm">
      <input type="hidden" id="editProductId">

      <div style="display: flex; min-height: 420px;">
        
        <!-- Columna izquierda -->
        <div style="flex: 1.2; padding: 32px 40px; border-right: 1px solid #f1f5f9;">
          <div style="margin-bottom: 24px;">
            <span style="background: #eff6ff; color: #3b82f6; padding: 4px 10px; border-radius: 8px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Editor</span>
            <h3 style="margin: 10px 0 4px; font-size: 1.6rem; font-weight: 800; color: #0f172a; letter-spacing: -0.03em;">Editar Producto</h3>
            <p style="font-size: 0.875rem; color: #94a3b8; line-height: 1.4; margin: 0;">Actualiza los datos del producto.</p>
            <div id="alertContainerEditar" style="margin-top: 12px;"></div>
          </div>

          <div style="display: flex; flex-direction: column; gap: 24px;">
            <div>
              <label style="font-size: 0.7rem; font-weight: 700; color: #0f172a; text-transform: uppercase; display: block; margin-bottom: 8px;">Nombre del Producto</label>
              <input type="text" id="editNombre" placeholder="Ej. Coca Cola 350ml" required
                     style="width: 100%; padding: 10px 0; border: none; border-bottom: 2px solid #f1f5f9; font-size: 1.1rem; color: #0f172a; font-weight: 500; outline: none; transition: 0.3s;"
                     onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#f1f5f9'">
            </div>

            <div>
              <label style="font-size: 0.7rem; font-weight: 700; color: #0f172a; text-transform: uppercase; display: block; margin-bottom: 8px;">Código de Barras</label>
              <input type="text" id="editCodigoBarras" placeholder="000000000000"
                     style="width: 100%; padding: 10px 0; border: none; border-bottom: 2px solid #f1f5f9; font-size: 1rem; color: #64748b; font-family: monospace; outline: none;"
                     onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#f1f5f9'">
            </div>
          </div>
          <div>
            <br>
  <label style="font-size: 0.7rem; font-weight: 700; color: #0f172a; text-transform: uppercase; display: block; margin-bottom: 8px;">Unidad de Medida</label>
  <select id="editUnidad"
    style="width: 100%; padding: 10px 12px; border: none; border-bottom: 2px solid #f1f5f9; font-size: 1rem; color: #0f172a; outline: none; background: transparent;">
    <optgroup label="Básicas">
      <option value="Unidad">Unidad (und)</option>
      <option value="Par">Par</option>
      <option value="Docena">Docena</option>
      <option value="Caja">Caja</option>
      <option value="Paquete">Paquete</option>
      <option value="Sobre">Sobre</option>
      <option value="Frasco">Frasco</option>
      <option value="Botella">Botella</option>
      <option value="Lata">Lata</option>
      <option value="Tubo">Tubo</option>
    </optgroup>
    <optgroup label="Peso">
      <option value="Gramo">Gramo (g)</option>
      <option value="Kilogramo">Kilogramo (kg)</option>
      <option value="Libra">Libra (lb)</option>
      <option value="Tonelada">Tonelada (t)</option>
      <option value="Onza">Onza (oz)</option>
    </optgroup>
    <optgroup label="Volumen">
      <option value="Mililitro">Mililitro (ml)</option>
      <option value="Litro">Litro (L)</option>
      <option value="Galón">Galón (gal)</option>
      <option value="Metro cúbico">Metro cúbico (m³)</option>
    </optgroup>
    <optgroup label="Longitud">
      <option value="Milímetro">Milímetro (mm)</option>
      <option value="Centímetro">Centímetro (cm)</option>
      <option value="Metro">Metro (m)</option>
      <option value="Metro lineal">Metro lineal</option>
      <option value="Kilómetro">Kilómetro (km)</option>
      <option value="Pulgada">Pulgada (in)</option>
      <option value="Pie">Pie (ft)</option>
    </optgroup>
    <optgroup label="Área">
      <option value="Metro cuadrado">Metro cuadrado (m²)</option>
      <option value="Centímetro cuadrado">Centímetro cuadrado (cm²)</option>
      <option value="Hectárea">Hectárea (ha)</option>
    </optgroup>
  </select>
</div>
        </div>

        <!-- Columna derecha -->
        <div style="flex: 1; background: #f8fafc; padding: 32px 36px; display: flex; flex-direction: column; justify-content: space-between;">
          
          <div style="display: flex; flex-direction: column; gap: 18px;">

            <!-- Precios -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
              <div>
                <label style="font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 6px;">Costo</label>
                <div style="font-size: 1.4rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 4px;">
                  <span style="color: #cbd5e1;">$</span>
                  <input type="text" id="editPrecioCompra" value="0"
       style="border: none; border-bottom: 2px solid #cdcdce; background: transparent; font-weight: inherit; font-size: inherit; color: inherit; width: 100%; outline: none; transition: 0.3s;"
       onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#cdcdce'">
                </div>
              </div>
              <div>
                <label style="font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 6px;">Venta</label>
                <div style="font-size: 1.4rem; font-weight: 700; color: #3b82f6; display: flex; align-items: center; gap: 4px;">
                  <span style="color: #cbd5e1;">$</span>
                  <input type="text" id="editPrecioVenta" value="0"
       style="border: none; border-bottom: 2px solid #cdcdce; background: transparent; font-weight: inherit; font-size: inherit; color: inherit; width: 100%; outline: none; transition: 0.3s;"
       onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#cdcdce'">
                </div>
              </div>
            </div>

            <!-- Stock -->
            <div style="background: #ffffff; padding: 14px 18px; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); display: flex; align-items: center; justify-content: space-between;">
              <div>
                <span style="display: block; font-size: 0.6rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 2px;">Stock</span>
                <input type="text" id="editStock" value="0"
                       style="width: 98%; border: none; font-size: 1.4rem; font-weight: 800; color: #0f172a; outline: none; background: transparent;">
              </div>
              <div style="display: flex; gap: 8px;">
                <button type="button" onclick="decreaseEditStock(event)" style="width: 38px; height: 38px; border-radius: 10px; border: 1px solid #f1f5f9; background: #fff; cursor: pointer; font-size: 16px; font-weight: bold; color: #64748b;">-</button>
                <button type="button" onclick="increaseEditStock(event)" style="width: 38px; height: 38px; border-radius: 10px; border: none; background: #3b82f6; color: #fff; cursor: pointer; font-size: 16px; font-weight: bold;">+</button>
              </div>
            </div>

            <!-- Ganancia -->
            <div style="text-align: center; border: 2px dashed #e2e8f0; border-radius: 14px; padding: 14px;">
              <span style="font-size: 0.65rem; font-weight: 600; color: #94a3b8; text-transform: uppercase;">Ganancia</span>
              <h4 id="editGananciaValor" style="margin: 4px 0 2px; font-size: 1.5rem; font-weight: 800; color: #10b981;">$0</h4>
              <span id="editGananciaMargen" style="font-size: 0.8rem; font-weight: 700; color: #10b981;">0%</span>
            </div>

            <!-- IVA -->
            <?php if($empresa && $empresa->cobra_iva): ?>
            <div style="display: flex; align-items: center; gap: 12px;">
              <label style="font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; white-space: nowrap;">IVA (%)</label>
              <input type="number" id="editIva" step="1" min="0" max="100"
                     style="width: 70px; padding: 6px 10px; border-radius: 8px; border: 1.5px solid #e2e8f0; font-weight: 600; outline: none;">
            </div>
            <?php else: ?>
            <input type="hidden" id="editIva" value="0">
            <?php endif; ?>

          </div>

          <!-- Botones -->
          <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
            <button type="button" id="btnGuardarEdicion" onclick="guardarEdicion(event)"
                    style="width: 100%; height: 52px; border-radius: 14px; border: none; background: #3b82f6; color: #ffffff; font-size: 1rem; font-weight: 700; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 12px rgba(59,130,246,0.2);"
                    onmouseover="this.style.background='#2563eb'"
                    onmouseout="this.style.background='#3b82f6'">
              Guardar Cambios
            </button>
            <button type="button" onclick="cerrarModalEditar()"
                    style="background: none; border: none; color: #94a3b8; font-weight: 600; font-size: 0.875rem; cursor: pointer; padding: 6px;">
              Descartar y volver
            </button>
          </div>

        </div>
      </div>
    </form>
  </div>
</div></div>
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
const UNIDAD_ABREV = {
  'Unidad':'und','Par':'par','Docena':'doc','Caja':'caja','Paquete':'paq',
  'Sobre':'sob','Frasco':'fco','Botella':'bot','Lata':'lata','Tubo':'tubo',
  'Gramo':'g','Kilogramo':'kg','Libra':'lb','Tonelada':'t','Onza':'oz',
  'Mililitro':'ml','Litro':'L','Galón':'gal','Metro cúbico':'m³',
  'Milímetro':'mm','Centímetro':'cm','Metro':'m','Metro lineal':'m lineal',
  'Kilómetro':'km','Pulgada':'in','Pie':'ft','Metro cuadrado':'m²',
  'Centímetro cuadrado':'cm²','Hectárea':'ha'
};

document.getElementById('unidadMedida')?.addEventListener('change', function() {
    const abrev = UNIDAD_ABREV[this.value] || this.value;
    const label = document.getElementById('gananciaLabel');
    if (label) label.textContent = `Ganancia por ${abrev}`;
});
function abrirModalAgregar() {
  document.body.style.overflow = 'hidden';
    document.getElementById('agregarProductoModal').classList.add('active');
    setTimeout(() => {
        const input = document.getElementById('productName');
        if (input) input.focus();
    }, 150);

}

function cerrarModalAgregar() {
  document.body.style.overflow = '';
    document.getElementById('agregarProductoModal').classList.remove('active');
}

// Cerrar modal al hacer click fuera
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('agregarProductoModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) cerrarModalAgregar();
        });
    }
});
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
    this.value = this.value.replace(/[^0-9,]/g, '');
    currentStock = parseFloat(this.value.replace(',', '.') || 0);
});

stockInput.addEventListener('blur', function() {
    currentStock = parseFloat(this.value.replace(',', '.') || 0);
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
  document.getElementById('unidadMedida').value = 'Unidad';
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
    const ac = document.getElementById('alertContainerModal');
    if (ac) ac.innerHTML = '';
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
    <tr id="producto-${p.id}" data-unidad="${escapeHtml(p.unidad || 'Unidad')}">
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
        <div class="producto-dropdown" id="dropdown-${p.id}">
          <button type="button" class="dropdown-trigger" onclick="toggleDropdown(${p.id}, event)">
            <i class="lni lni-more-alt"></i>
          </button>
          <div class="dropdown-menu-custom" id="dropdown-menu-${p.id}">
            <button type="button" onclick="abrirModalEditar(${p.id}); cerrarTodosDropdowns()">
              <i class="lni lni-pencil"></i> Editar
            </button>
            <button type="button" class="danger" onclick="eliminarProducto(${p.id}); cerrarTodosDropdowns()">
              <i class="lni lni-trash-can"></i> Eliminar
            </button>
          </div>
        </div>
        <span class="msg" id="msg-${p.id}"></span>
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
    const unidad = document.getElementById('unidadMedida').value;
    const nombre = document.getElementById('productName').value.trim();
    const precioCompra = parseCOP(document.getElementById('precioCompra').value);
    const precioVenta = parseCOP(document.getElementById('precioVenta').value);
    const iva = parseFloat(document.getElementById('ivaPercent').value) || 0;
    const stock = parseFloat(String(currentStock).replace(',', '.')) || 0;

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
        const codigoBarras = document.getElementById('codigoBarras').value.trim();
        const res = await fetch('/productos', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ nombre, codigo_barras: codigoBarras || null, precio_compra: precioCompra, precio_venta: precioVenta, iva, stock, unidad })
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
    const mensaje = error.errors?.nombre?.[0] 
        || error.errors?.precio_venta?.[0]
        || (error.message === 'El producto ya existe' ? 'El producto ya existe' : error.message || 'No se pudo agregar el producto');
    showAlert(mensaje, 'error');
} finally {
        btn.disabled = false;
    }
}

function showAlert(mensaje, tipo) {
    const container = document.getElementById('alertContainerModal');
    if (!container) return;
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
  document.body.style.overflow = 'hidden';
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
    document.body.style.overflow = '';
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
      document.body.style.overflow = '';
    }
  });
}
<?php endif; ?>

// ========== DROPDOWN ==========

function toggleDropdown(id, event) {
    event.stopPropagation();
    const menu = document.getElementById(`dropdown-menu-${id}`);
    const isOpen = menu.classList.contains('open');
    cerrarTodosDropdowns();
    if (!isOpen) {
        menu.classList.add('open');
    }
}

function cerrarTodosDropdowns() {
    document.querySelectorAll('.dropdown-menu-custom').forEach(m => {
        m.classList.remove('open');
        m.removeAttribute('style');
    });
}

document.addEventListener('click', function() {
    cerrarTodosDropdowns();
});

// ========== MODAL EDITAR ==========

let currentEditStock = 0;

function abrirModalEditar(id) {
  document.body.style.overflow = 'hidden';
    const tr = document.getElementById(`producto-${id}`);
    if (!tr) return;

    // Leer datos de la fila
   const unidad = tr.dataset.unidad || 'Unidad'; document.getElementById('editUnidad').value = unidad;
    const nombre = tr.querySelector('span.view[data-field="nombre"]')?.innerText?.trim() || '';
    const precioCompra = tr.querySelector('span.view[data-field="precio_compra"]')?.innerText?.trim() || '0';
    const precioVenta = tr.querySelector('span.view[data-field="precio_venta"]')?.innerText?.trim() || '0';
    const iva = tr.querySelector('span.view[data-field="iva"]')?.innerText?.replace('%', '').trim() || '0';
    const stock = tr.querySelector('.stock_view')?.innerText?.trim() || '0';
    const codigoBarras = tr.dataset.codigoBarras || '';

    // Rellenar modal
    document.getElementById('editProductId').value = id;
    document.getElementById('editNombre').value = nombre;
    document.getElementById('editCodigoBarras').value = codigoBarras;
    document.getElementById('editPrecioCompra').value = precioCompra.replace('$', '');
    document.getElementById('editPrecioVenta').value = precioVenta.replace('$', '');

    const editIva = document.getElementById('editIva');
    if (editIva) editIva.value = iva === '-' ? '0' : iva;

    currentEditStock = parseFloat(String(stock).replace(',', '.')) || 0;
    document.getElementById('editStock').value = currentEditStock;

    // Subtitle con nombre
    

    // Limpiar alerta
    document.getElementById('alertContainerEditar').innerHTML = '';

    // Calcular ganancia inicial
    calcularGananciaEditar();

    // Abrir
    document.getElementById('editarProductoModal').classList.add('active');
    setTimeout(() => document.getElementById('editNombre').focus(), 150);

    // Cerrar al click fuera
    document.getElementById('editarProductoModal').addEventListener('click', function(e) {
        if (e.target === this) cerrarModalEditar();
    }, { once: true });
}

function cerrarModalEditar() {
  document.body.style.overflow = '';
    document.getElementById('editarProductoModal').classList.remove('active');
    document.getElementById('alertContainerEditar').innerHTML = '';
}

function increaseEditStock(e) {
    e.preventDefault();
    currentEditStock++;
    document.getElementById('editStock').value = currentEditStock;
}

function decreaseEditStock(e) {
    e.preventDefault();
    if (currentEditStock > 0) {
        currentEditStock--;
        document.getElementById('editStock').value = currentEditStock;
    }
}

function calcularGananciaEditar() {
    const compra = parseCOP(document.getElementById('editPrecioCompra').value);
    const venta = parseCOP(document.getElementById('editPrecioVenta').value);
    const ganancia = venta - compra;
    const margen = compra > 0 ? ((ganancia / compra) * 100).toFixed(1) : 0;

    const valEl = document.getElementById('editGananciaValor');
    const margenEl = document.getElementById('editGananciaMargen');

    valEl.textContent = '$' + formatCOP(ganancia);
    margenEl.textContent = margen + '%';

    if (ganancia < 0) {
        valEl.style.color = '#dc2626';
        margenEl.style.background = '#fef2f2';
        margenEl.style.color = '#dc2626';
    } else if (ganancia === 0) {
        valEl.style.color = '#64748b';
        margenEl.style.background = '#f1f5f9';
        margenEl.style.color = '#64748b';
    } else {
        valEl.style.color = '#10b981';
        margenEl.style.background = '#dcfce7';
        margenEl.style.color = '#166534';
    }
}

// Listeners tiempo real en modal editar
document.addEventListener('DOMContentLoaded', function() {
    const editCompra = document.getElementById('editPrecioCompra');
    const editVenta = document.getElementById('editPrecioVenta');
    const editStockInput = document.getElementById('editStock');

    if (editCompra) {
        editCompra.addEventListener('input', function() {
            const digits = this.value.replace(/\D/g, '');
            const val = parseInt(digits || '0', 10);
            this.value = formatCOP(val);
            calcularGananciaEditar();
        });
    }
    if (editVenta) {
        editVenta.addEventListener('input', function() {
            const digits = this.value.replace(/\D/g, '');
            const val = parseInt(digits || '0', 10);
            this.value = formatCOP(val);
            calcularGananciaEditar();
        });
    }
    if (editStockInput) {
        editStockInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            currentEditStock = parseInt(this.value || 0);
        });
    }
});

async function guardarEdicion(e) {
    e.preventDefault();
    const unidad = document.getElementById('editUnidad').value;   
    const id = document.getElementById('editProductId').value;
    const nombre = document.getElementById('editNombre').value.trim();
    const precioCompra = parseCOP(document.getElementById('editPrecioCompra').value);
    const precioVenta = parseCOP(document.getElementById('editPrecioVenta').value);
    const iva = parseFloat(document.getElementById('editIva').value) || 0;
    const stock = currentEditStock;
    const codigoBarras = document.getElementById('editCodigoBarras').value.trim();

    if (!nombre) {
        showAlertEditar('El nombre es requerido', 'error');
        return;
    }
    if (precioVenta <= 0) {
        showAlertEditar('El precio de venta debe ser mayor a 0', 'error');
        return;
    }

    const btn = document.getElementById('btnGuardarEdicion');
    btn.disabled = true;

    try {
        const res = await fetch(`/productos/${id}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ nombre, precio_compra: precioCompra, precio_venta: precioVenta, iva, stock, codigo_barras: codigoBarras || null, unidad })
        });

        if (res.status === 419) {
            showAlertEditar('Sesión expirada. Redirigiendo...', 'error');
            setTimeout(() => window.location.href = '/login', 1500);
            return;
        }

        const result = await res.json();
        if (!res.ok) throw result;

        // Actualizar fila en tabla
        const tr = document.getElementById(`producto-${id}`);
        if (tr) {
            const nombreSpan = tr.querySelector('span.view[data-field="nombre"]');
            if (nombreSpan) nombreSpan.innerText = nombre;

            const compraSpan = tr.querySelector('span.view[data-field="precio_compra"]');
            if (compraSpan) compraSpan.innerText = '$' + formatCOP(precioCompra);

            const ventaSpan = tr.querySelector('span.view[data-field="precio_venta"]');
            if (ventaSpan) ventaSpan.innerText = '$' + formatCOP(precioVenta);

            const ivaSpan = tr.querySelector('span.view[data-field="iva"]');
            if (ivaSpan) ivaSpan.innerText = iva > 0 ? iva + '%' : '-';

            if (result.producto?.precio_con_iva !== undefined) {
                const pcIva = tr.querySelector('.precio_con_iva_span');
                if (pcIva) pcIva.innerText = '$' + formatCOP(result.producto.precio_con_iva);
            }

            const stockView = tr.querySelector('.stock_view');
            if (stockView) stockView.innerText = stock;
            const stockInput = tr.querySelector('.stock_input');
            if (stockInput) { stockInput.value = stock; stockInput.dataset.originalStock = stock; }

            tr.dataset.codigoBarras = codigoBarras;
            tr.dataset.unidad = unidad;

            tr.style.background = '#eff6ff';
            setTimeout(() => { tr.style.background = ''; }, 1600);
        }

        showAlertEditar('Producto actualizado correctamente', 'success');
        setTimeout(() => cerrarModalEditar(), 1200);

    } catch (error) {
    const mensaje = error.errors?.nombre?.[0] || error.message || 'Error al actualizar';
    showAlertEditar(mensaje, 'error');
} finally {
        btn.disabled = false;
    }
}

function showAlertEditar(mensaje, tipo) {
    const container = document.getElementById('alertContainerEditar');
    if (!container) return;
    container.innerHTML = `<div class="alert-custom ${tipo} show">${mensaje}</div>`;
    if (tipo === 'success') return;
    setTimeout(() => { container.innerHTML = ''; }, 4000);
}
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