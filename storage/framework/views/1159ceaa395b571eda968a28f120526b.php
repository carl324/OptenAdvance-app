<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title><?php echo $__env->yieldContent('title', 'OptenAdvance'); ?></title>
  <link rel="icon" type="image/png" href="/assets/images/logo/icon.png" />
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Meta CSRF centralizado -->
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

  <!-- CSS global y navbar simple (offline, sin librerías) -->
  <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/assets/css/lineicons.css" />
  <link rel="stylesheet" href="/assets/css/materialdesignicons.min.css" />
  <link rel="stylesheet" href="/assets/css/fullcalendar.css" />
  <link rel="stylesheet" href="/assets/css/main.css" />
  <style>
    /* Sidebar width adjustment */
    .sidebar-nav-wrapper {
      width: 210px;
      /* antes suele estar en 260–280 */
    }

    /* Ajuste del contenido principal */
    .main-wrapper {
      margin-left: 210px;
    }

    /* Responsive: en pantallas pequeñas no tocar */
    @media (max-width: 991px) {
      .sidebar-nav-wrapper {
        width: 260px;
      }

      .main-wrapper {
        margin-left: 0;
      }
    }

    /* Evitar scroll interno feo en el sidebar */
    .sidebar-nav-wrapper {
      overflow: hidden;
    }

    /* El nav interno solo scrollea si realmente es necesario */
    .sidebar-nav {
      overflow-y: auto;
      max-height: calc(100vh - 80px);
      /* resta logo/header */
    }

    /* Quitar flechas en navegadores antiguos */
    .sidebar-nav::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar-nav::-webkit-scrollbar-thumb {
      background: rgba(0, 0, 0, 0.15);
      border-radius: 4px;
    }

    .sidebar-nav::-webkit-scrollbar-track {
      background: transparent;
    }

    .caja-estado-item,
.caja-resumen-item,
.caja-acciones-item {
  padding: 0;
  margin: 0;
  border: none;
}

.caja-estado-item:hover,
.caja-resumen-item:hover,
.caja-acciones-item:hover {
  background: transparent !important;
}

/* Estado de la caja */
.caja-estado-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid #e6e8ee;
  background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
}

.estado-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 12px;
  border-radius: 100px;
  font-size: 12px;
  font-weight: 600;
}

.estado-abierta {
  background: rgba(16, 185, 129, 0.1);
  color: #059669;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.estado-abierta svg {
  color: #10b981;
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.estado-cerrada {
  background: rgba(239, 68, 68, 0.1);
  color: #dc2626;
  border: 1px solid rgba(239, 68, 68, 0.2);
}

.estado-hora {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  letter-spacing: 0.02em;
}

/* Cards de resumen */
.resumen-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 20px;
  background: #ffffff;
  transition: all 0.2s ease;
}

.resumen-card:hover {
  background: #f8fafc;
}

.caja-resumen-item + .caja-resumen-item .resumen-card {
  border-top: 1px solid #f1f3f9;
}

.resumen-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  border-radius: 10px;
  flex-shrink: 0;
}

.resumen-icon i {
  font-size: 20px;
}

.resumen-ventas {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
  color: #3b82f6;
}

.resumen-ingresos {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
  color: #10b981;
}

.resumen-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.resumen-label {
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
}

.resumen-valor {
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
  letter-spacing: -0.01em;
}

/* Botones de acción */
.caja-acciones {
  display: flex;
  gap: 8px;
  padding: 16px 20px;
  border-top: 1px solid #e6e8ee;
  background: #f8fafc;
}

.caja-acciones .main-btn {
  flex: 1;
  font-size: 13px;
}

/* Ajuste para el dropdown menu */
#message + .dropdown-menu {
  min-width: 320px;
  padding: 0;
  border: 1px solid #e6e8ee;
  box-shadow: 0 10px 40px -10px rgba(15, 23, 42, 0.15);
  border-radius: 12px;
  overflow: hidden;
}

 .modal-alegra-final {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .modal-alegra-final .modal-content {
        border: none;
        border-radius: 24px;
        background-color: #ffffff;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    @media (min-width: 992px) {
        .modal-alegra-width { max-width: 820px !important; }
    }

    .cierre-wrapper {
        display: grid;
        grid-template-columns: 1.1fr 1fr;
        gap: 0;
    }

    /* Columna de Información (Izquierda) */
    .cierre-sidebar {
        padding: 45px;
        background-color: #f9fbff; /* Un azul casi imperceptible */
        border-right: 1px solid #edf2f7;
    }

    .cierre-main-action {
        padding: 45px;
        background: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Etiquetas y Textos */
    .text-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #94a3b8;
        margin-bottom: 10px;
        display: block;
    }

    .total-amount {
        font-size: 32px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .total-amount::before {
        content: '';
        width: 4px;
        height: 24px;
        background: #3b82f6; /* Acento azul sutil */
        border-radius: 10px;
    }

    /* Grid de Métodos */
    .grid-methods {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    .method-card-minimal {
        display: flex;
        flex-direction: column;
    }

    .method-card-minimal .m-title {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .method-card-minimal .m-price {
        font-size: 16px;
        font-weight: 700;
        color: #334155;
    }

    /* Inputs Refinados */
    .group-input {
        margin-bottom: 25px;
    }

    .input-clean {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 20px;
        font-size: 15px;
        font-weight: 500;
        color: #1e293b;
        transition: all 0.3s ease;
        background-color: #ffffff;
    }

    .input-clean:focus {
        outline: none;
        border-color: #3b82f6;
        background-color: #fff;
        box-shadow: 0 0 0 5px rgba(59, 130, 246, 0.08);
    }

    .input-focus-blue {
        border-color: #3b82f6;
        font-size: 24px;
        font-weight: 800;
        color: #059669;
        text-align: center;
    }

    /* Diferencia con color sutil */
    .badge-diff {
        display: inline-block;
        margin-top: 10px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        padding: 4px 12px;
        border-radius: 8px;
        background: #f1f5f9;
    }

    /* Botones */
    .btn-finalizar {
        background-color: #2e6cff;
        color: white;
        border: none;
        padding: 18px;
        border-radius: 14px;
        font-size: 15px;
        font-weight: 700;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 15px -3px rgba(0, 76, 255, 0.1);
    }

    .btn-finalizar:hover {
        background-color: #0f6bff;
        transform: translateY(-2px);
        box-shadow: 0 15px 20px -3px rgba(0, 59, 196, 0.15);
    }

    .btn-exit {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 14px;
        font-weight: 600;
        margin-top: 20px;
        width: 100%;
        cursor: pointer;
        transition: color 0.2s;
    }

    .btn-exit:hover { color: #64748b; }

    /* Checkbox estilo Switch sutil */
    .custom-switch {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        user-select: none;
        font-size: 13px;
        color: #64748b;
    }
    .lic-modal-container .modal-content {
    border: none;
    border-radius: 28px;
    overflow: hidden;
    box-shadow: 0 40px 100px -20px rgba(14, 39, 97, 0.2);
    background: #ffffff;
}

@media (min-width: 992px) {
    .lic-modal-size { max-width: 820px !important; }
}

.lic-modal-grid {
    display: grid;
    grid-template-columns: 0.85fr 1.15fr;
    min-height: 480px;
}

/* Lado izquierdo - Fondo con Animación de Luces */
.lic-modal-image-side {
    background: #004cff;
    /* Gradiente animado de fondo */
    background: linear-gradient(135deg, #004cff 0%, #002db3 100%);
    padding: 40px 30px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

/* Esfera de luz 1 */
.lic-modal-image-side::before {
    content: '';
    position: absolute;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
    top: -100px;
    left: -100px;
    animation: lic-float-light 8s infinite alternate ease-in-out;
}

/* Esfera de luz 2 */
.lic-modal-image-side::after {
    content: '';
    position: absolute;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    bottom: -50px;
    right: -50px;
    animation: lic-float-light 12s infinite alternate-reverse ease-in-out;
}

/* Animación de las luces de fondo */
@keyframes lic-float-light {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(30px, 40px) scale(1.2); }
}

.lic-modal-cert-icon {
    width: 95px;
    height: 95px;
    background: rgba(255, 255, 255, 0.07);
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    position: relative;
    z-index: 2;
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    /* Sutil pulso al icono */
}

@keyframes lic-icon-pulse {
    0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.1); }
    50% { transform: scale(1.03); box-shadow: 0 0 20px 5px rgba(255, 255, 255, 0.05); }
}

.lic-modal-cert-icon i {
    font-size: 45px;
    color: #ffffff;
    text-shadow: 0 0 15px rgba(255, 255, 255, 0.4);
}

.lic-modal-cert-title {
    font-size: 25px;
    font-weight: 800;
    color: #ffffff;
    text-align: center;
    z-index: 2;
    letter-spacing: -0.5px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Lado derecho - Info */
.lic-modal-info-side {
    padding: 40px 45px;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.lic-modal-status-badge {
    padding: 6px 12px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 15px;
}

.lic-modal-status-active {
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #dcfce7;
}
.lic-modal-status-trial {
    background: #eff6ff;   /* azul clarito */
    color: #3b82f6;        /* azul */
    border: 1px solid #bfdbfe; /* borde azulito */
}

.lic-modal-status-expired {
    background: #fef2f2;   /* rojo clarito */
    color: #ef4444;        /* rojo */
    border: 1px solid #fecaca; /* borde rojo */
}
.lic-modal-title-main {
    font-size: 26px;
    font-weight: 900;
    color: #0f172a;
    letter-spacing: -1px;
    margin-bottom: 8px;
}

.lic-modal-info-card {
    padding: 16px 20px;
    border-radius: 16px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    transition: all 0.3s ease;
}

.lic-modal-info-card:hover {
    background: #fff;
    border-color: #e2e8f0;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.02);
}

.lic-modal-info-label {
    font-size: 10px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

.lic-modal-info-value {
    font-size: 15px;
    font-weight: 700;
    color: #1e293b;
    margin-top: 3px;
}

.lic-modal-permission-item {
    border: none;
    background: #fff;
    padding: 6px 0;
    font-size: 14px;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 10px;
}

.lic-modal-check-icon {
    color: #0f6bff;
    font-size: 16px;
}

.lic-modal-btn-close {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 14px;
    background: #0f6bff;
    color: #ffffff;
    font-weight: 700;
    font-size: 15px;
    transition: all 0.3s;
    margin-top: 10px;
    box-shadow: 0 4px 15px rgba(46, 108, 255, 0.2);
}

.lic-modal-btn-close:hover {
    background: #0f6bff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(46, 108, 255, 0.3);
}
/* ============================================
   MODAL LICENCIA EXPIRADA - CLEAN RED
   ============================================ */

.lic-expired-size { max-width: 750px !important; }

.lic-expired-grid {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    min-height: 400px;
    overflow: hidden;
}

/* Lado Izquierdo: IMAGEN */
.lic-expired-img-side {
    background-image: url('/assets/images/cards/image.jpg'); 
    background-size: cover;
    background-position: center;
    position: relative;
}

.lic-expired-img-side::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to right, rgba(220, 38, 38, 0.2), transparent);
}

/* Lado Derecho */
.lic-expired-info-side {
    padding: 50px;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: left;
}

/* Header icon + title */
.lic-expired-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 15px;
}

.lic-expired-icon-inline {
    width: 48px;
    height: 48px;
    background: #fef2f2;
    color: #dc2626;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.lic-expired-title {
    font-size: 28px;
    font-weight: 900;
    color: #0f172a;
    letter-spacing: -1px;
    margin: 0;
}

.lic-expired-text {
    font-size: 15px;
    color: #64748b;
    line-height: 1.6;
    margin-bottom: 30px;
}
.lic-expired-btn-renew {
    background: #1062fa;
    color: white;
    border: none;
    padding: 15px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 15px;
    transition: all 0.3s;
    text-decoration: none;
    text-align: center;
    box-shadow: 0 10px 15px -3px rgba(38, 87, 220, 0.2);
}
  </style>
</head>

<body>
  <div id="preloader">
    <div class="spinner"></div>
  </div>
  <!-- ======== Preloader =========== -->

  <!-- ======== sidebar-nav start =========== -->
  <aside class="sidebar-nav-wrapper">
    <div class="navbar-logo">
      <a href="/">
        <img src="/assets/images/logo/horizontal.png" alt="logo" />
      </a>
    </div>
    <nav class="sidebar-nav">
      <ul class="d-flex flex-column gap-0">
        <?php $role = Auth::user()->role ?? 'empleado'; ?>
        <?php if($role === 'admin'): ?>
          <li class="nav-item <?php echo e(activeRoute('ventas.create')); ?>">
            <a href="<?php echo e(route('ventas.create')); ?>" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="mdi mdi-cash-register"></i>
              </span>
              <span class="text fw-semibold">Caja</span>
            </a>
          </li>

          <li class="nav-item <?php echo e(activeRoute(['ventas.index', 'ventas.show', 'ventas.devolucion', 'ventas.factura*'])); ?>">
            <a href="<?php echo e(route('ventas.index')); ?>" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="lni lni-revenue"></i>
              </span>
              <span class="text fw-semibold">Ventas</span>
            </a>
          </li>

          <li class="nav-item <?php echo e(activeRoute(['productos.index', 'productos.create'])); ?>">
            <a href="<?php echo e(route('productos.index')); ?>" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="lni lni-package"></i>
              </span>
              <span class="text fw-semibold">Productos</span>
            </a>
          </li>
          <span class="divider"><hr /></span>
          <li class="nav-item <?php echo e(activeRoute('reportes.index')); ?>">
            <a href="<?php echo e(route('reportes.index')); ?>" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="lni lni-bar-chart"></i>
              </span>
              <span class="text fw-semibold">Reportes</span>
            </a>
          </li>
          <li class="nav-item <?php echo e(activeRoute('personal.index')); ?>">
            <a href="<?php echo e(route('personal.index')); ?>" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="mdi mdi-briefcase-account"></i>
              </span>
              <span class="text fw-semibold">Personal</span>
            </a>
          </li>
          <li class="nav-item <?php echo e(activeRoute(['empresa.index', 'empresa.edit'])); ?>">
            <a href="<?php echo e(route('empresa.index')); ?>" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="lni lni-apartment"></i>
              </span>
              <span class="text fw-semibold">Empresa</span>
            </a>
          </li>
          <span class="divider"><hr /></span>
          <li class="nav-item <?php echo e(activeRoute('soporte.index')); ?>">
            <a href="<?php echo e(route('soporte.index')); ?>" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="lni lni-cogs"></i>
              </span>
              <span class="text fw-semibold">Soporte</span>
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item <?php echo e(activeRoute('ventas.create')); ?>">
            <a href="<?php echo e(route('ventas.create')); ?>" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="mdi mdi-cash-register"></i>
              </span>
              <span class="text fw-semibold">Caja</span>
            </a>
          </li>

          <li class="nav-item <?php echo e(activeRoute(['ventas.index', 'ventas.show', 'ventas.devolucion', 'ventas.factura*'])); ?>">
            <a href="<?php echo e(route('ventas.index')); ?>" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="lni lni-revenue"></i>
              </span>
              <span class="text fw-semibold">Ventas</span>
            </a>
          </li>

          <li class="nav-item <?php echo e(activeRoute(['productos.index', 'productos.create'])); ?>">
            <a href="<?php echo e(route('productos.index')); ?>" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="lni lni-package"></i>
              </span>
              <span class="text fw-semibold">Productos</span>
            </a>
          </li>
          <span class="divider"><hr /></span>
          <li class="nav-item <?php echo e(activeRoute('soporte.index')); ?>">
            <a href="<?php echo e(route('soporte.index')); ?>" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="lni lni-cogs"></i>
              </span>
              <span class="text fw-semibold">Soporte</span>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>

  </aside>
  <div class="overlay"></div>
  <!-- ======== sidebar-nav end =========== -->

  <!-- ======== main-wrapper start =========== -->
  <main class="main-wrapper">
    <!-- ========== header start ========== -->
    <header class="header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-5 col-md-5 col-6">
            <div class="header-left d-flex align-items-center">
              <div class="menu-toggle-btn mr-15">
                <button id="menu-toggle" class="main-btn primary-btn btn-hover">
                  <i class="lni lni-chevron-left me-2"></i> Menu
                </button>
              </div>
              <div class="header-search d-none d-md-flex"></div>
            </div>
          </div>
          <div class="col-lg-7 col-md-7 col-6">
            <div class="header-right">
              <div class="header-message-box ml-15 d-none d-md-flex">
                  <button class="dropdown-toggle" type="button" id="message" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="mdi mdi-cash-register" style="font-size: 32px;"></i>
                    <?php if($cajaAbierta): ?>
                      <span style="background-color: #10b981; color: #fff;   "></span>
                    <?php else: ?>
                      <span style="background-color: #f32035; color: #fff;   "></span>
                    <?php endif; ?>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="message">
                    <!-- Estado de la caja -->
                    <li class="caja-estado-item">
                        <div class="caja-estado-header">
                        <div class="estado-badge <?php echo e($cajaAbierta ? 'estado-abierta' : 'estado-cerrada'); ?>">
                          <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                            <circle cx="5" cy="5" r="5" fill="currentColor"/>
                          </svg>
                          <span><?php echo e($cajaAbierta ? 'Caja Abierta' : 'Caja Cerrada'); ?></span>
                        </div>
                        <span class="estado-hora"><?php echo e(formatoHoraInteligente($cajaHoraApertura) ?? '--:--'); ?></span>
                      </div>
                    </li>

                    <!-- Resumen de ventas -->
                    <li class="caja-resumen-item">
                      <div class="resumen-card">
                        <div class="resumen-icon resumen-ventas">
                          <i class="lni lni-shopping-basket"></i>
                        </div>
                        <div class="resumen-info">
                          <span class="resumen-label">Ventas del día</span>
                          <strong class="resumen-valor">
                            <?php echo e($cajaAbierta ? ($ventasHoy ?? 0) . ' ventas' : '--'); ?>

                          </strong>
                        </div>
                      </div>
                    </li>

                    <!-- Resumen de ingresos -->
                    <li class="caja-resumen-item">
                      <div class="resumen-card">
                        <div class="resumen-icon resumen-ingresos">
                          <i class="lni lni-revenue"></i>
                        </div>
                        <div class="resumen-info">
                          <span class="resumen-label">Ingresos totales</span>
                          <strong class="resumen-valor">
                            <?php echo e($cajaAbierta ? '$ ' . number_format(round($ingresosHoy ?? 0), 0, ',', '.') : '--'); ?>

                          </strong>
                        </div>
                      </div>
                    </li>

                    <!-- Botones de acción -->
                    <li class="caja-acciones-item">
                      <div class="caja-acciones">
                        <button class="main-btn light-btn btn-hover btn-sm" onclick="window.location.href='/ventas'">
                          <i class="lni lni-eye"></i> Ver ventas
                        </button>
                        <?php if($cajaAbierta): ?>
                          <button class="main-btn danger-btn btn-hover btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#modalCerrarCaja">
                            <i class="lni lni-lock"></i> Cerrar caja
                          </button>
                        <?php endif; ?>
                      </div>
                    </li>
                  </ul>





                </div>
                <!-- message end -->
                <!-- profile start -->
                <div class="profile-box ml-15">
                  <?php
                    $user = auth()->user();
                    $userName = $user->name ?? $user->username ?? 'Usuario';
                    $userRole = $user->role ?? 'empleado';
                    $profileImage = $userRole === 'admin'
                      ? '/assets/images/profile/admin.png'
                      : '/assets/images/profile/empleado.png';
                  ?>
                  <button class="dropdown-toggle bg-transparent border-0" type="button" id="profile"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="profile-info">
                      <div class="info">
                        <div class="image">
                          <img src="<?php echo e($profileImage); ?>" alt="" />
                        </div>
                        <div>
                          <h6 class="fw-500"><?php echo e($userName); ?></h6>
                          <p><?php echo e(ucfirst($userRole)); ?></p>
                        </div>
                      </div>
                    </div>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profile">
                    
                    <?php if($userRole === 'admin'): ?>
                      <li>
                        <a href="<?php echo e(route('empresa.index')); ?>"> <i class="lni lni-apartment"></i> Empresa </a>
                      </li>
                      <li>
  <a href="#" data-bs-toggle="modal" data-bs-target="#modalLicencia"> 
    <i class="mdi mdi-certificate"></i> Licencia 
  </a>
</li>
                    <?php endif; ?>
                    
                    <li>
                      <a href="<?php echo e(route('soporte.index')); ?>">
                        <i class="lni lni-cog"></i> Soporte
                      </a>
                    </li>
                    <li class="divider"></li>
                    <li>
  <form method="POST" action="<?php echo e(route('logout')); ?>">
    <?php echo csrf_field(); ?>
    <button type="submit" 
        style="background: none; border: none; padding: 0; margin: 0 0 0 8px; font: inherit; color: inherit; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;"
        onmouseover="this.style.textDecoration='none';" 
        onmouseout="this.style.textDecoration='none';">
      <i class="lni lni-exit"></i> Salir
    </button>
  </form>
</li>

                  </ul>
                </div>
            </div>
          </div>
          
        </div>
      </div>
    </header>

    <?php echo $__env->yieldContent('content'); ?>
    <div class="modal fade lic-modal-container" id="modalLicencia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered lic-modal-size">
        <div class="modal-content">
            <div class="lic-modal-grid">
                
                <div class="lic-modal-image-side">
                    <div class="lic-modal-cert-icon">
                        <i class="mdi mdi-certificate-outline"></i>
                    </div>
                    <h3 class="lic-modal-cert-title">OptenAdvance</h3>
                    <p style="color: #94a3b8; font-size: 14px; margin-top: 5px;">
                        <?php if($data['status'] === 'active'): ?>
                            Licencia Corporativa
                        <?php elseif($data['status'] === 'expired'): ?>
                            Licencia Vencida
                        <?php else: ?>
                            Prueba Gratuita
                        <?php endif; ?>
                    </p>
                </div>

                <div class="lic-modal-info-side">
                    <div class="lic-modal-header text-center text-md-start">
                        <span class="lic-modal-status-badge 
    <?php echo e($data['status'] === 'active' ? 'lic-modal-status-active' : ($data['status'] === 'expired' ? 'lic-modal-status-expired' : 'lic-modal-status-trial')); ?>">
    <span style="width: 8px; height: 8px; 
        background: <?php echo e($data['status'] === 'active' ? '#16a34a' : ($data['status'] === 'expired' ? '#ef4444' : '#3b82f6')); ?>;
        border-radius: 50%;"></span>
    <?php if($data['status'] === 'active'): ?>
        Suscripción Activa
    <?php elseif($data['status'] === 'expired'): ?>
        Licencia Vencida
    <?php else: ?>
        Modo de Prueba
    <?php endif; ?>
</span>

                        <h2 class="lic-modal-title-main">Tu Licencia</h2>
                        <p style="color: #64748b; font-size: 15px; margin-bottom: 30px;">
                            Estos son los detalles de tu plan actual.
                        </p>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="lic-modal-info-card">
                                <span class="lic-modal-info-label">Fecha de Activación</span>
                                <div class="lic-modal-info-value">
                                    <?php echo e($data['start_at'] ?? '-'); ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="lic-modal-info-card">
                                <span class="lic-modal-info-label">Fecha de Vencimiento</span>
                                <div class="lic-modal-info-value">
                                    <?php echo e($data['end_at'] ?? '-'); ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
    <h4 style="font-size: 13px; font-weight: 800; text-transform: uppercase; color: #1e293b; letter-spacing: 0.5px; margin-bottom: 15px;">
        Descripción de tu plan actual
    </h4>
    <div class="lic-modal-permissions-list">
        <?php if($data['status'] === 'active'): ?>
            <div class="lic-modal-permission-item">
                <i class="mdi mdi-check-circle-outline lic-modal-check-icon"></i>
                <span>Acceso ilimitado a todas las funciones de OptenAdvance</span>
            </div>
            <div class="lic-modal-permission-item">
                <i class="mdi mdi-check-circle-outline lic-modal-check-icon"></i>
                <span>Soporte Técnico VIP y acceso a futuras actualizaciones (primeros 3 meses)</span>
            </div>
        <?php elseif($data['status'] === 'trial' || $data['status'] === 'trial_active' || $data['status'] === 'trial_first'): ?>
            <div class="lic-modal-permission-item d-flex align-items-start">
    <i class="mdi mdi-information-outline lic-modal-check-icon me-2 mt-1"></i>
    <span>
        Actualmente estás usando una licencia de prueba. Puedes explorar todas las funciones, pero recuerda que una vez terminado el periodo de prueba, deberás contactar con soporte para renovación.
    </span>
</div>

        <?php elseif($data['status'] === 'expired'): ?>
            <div class="lic-modal-permission-item d-flex align-items-start">
                <i style="color: #ef4444" class="mdi mdi-alert-circle-outline lic-modal-check-icon me-2 mt-1"></i>
                <span>Tu licencia ha terminado. Solo puedes ver las ventas, pero no agregar productos ni exportar a Excel. Contacta con soporte para reactivar tu suscripción y desbloquear todas las funciones.</span>
            </div>
        <?php endif; ?>
    </div>
</div>


                    <div class="lic-modal-footer">
                        <button type="button" class="lic-modal-btn-close" data-bs-dismiss="modal">
                            Entendido
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalExpirado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered lic-expired-size">
        <div class="modal-content" style="border: none; border-radius: 24px; overflow: hidden;">
            <div class="lic-expired-grid">
                
                <div class="lic-expired-img-side"></div>

                <div class="lic-expired-info-side">

                    <div class="lic-expired-header">
                        <div class="lic-expired-icon-inline">
                            <i class="mdi mdi-alert-circle-outline"></i>
                        </div>
                        <h2 class="lic-expired-title">Licencia expirada</h2>
                    </div>

                    <p class="lic-expired-text">
                        Tu periodo de licencia ha finalizado.  
                        Actualmente el sistema se encuentra en <strong>modo lectura</strong> para proteger tu información.
                        <br><br>
                        Renueva tu suscripción y recupera acceso completo a ventas, facturación, reportes y exportaciones sin interrupciones.
                    </p>

                    <a href="/soporte" class="lic-expired-btn-renew">
                        Renovar Ahora
                    </a>

                </div>

            </div>
        </div>
    </div>
</div>


<?php if($cajaAbierta): ?>
<div class="modal fade" id="modalConfirmarCierreSesion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
    <div class="modal-content" style="border: none; border-radius: 28px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15); overflow: hidden; background: #ffffff;">
            
     

      <div class="modal-body" style="padding: 40px 30px 30px 30px;">
        <div style="display: flex; gap: 20px; align-items: flex-start;">
                    
          <div style="flex-shrink: 0; width: 54px; height: 54px; background: #fff7ed; border-radius: 16px; display: flex; align-items: center; justify-content: center; border: 1px solid #ffedd5;">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
              <line x1="12" y1="9" x2="12" y2="13"></line>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
          </div>

          <div style="flex-grow: 1;">
            <h5 style="font-weight: 800; color: #0f172a; margin: 0 0 8px 0; font-size: 19px; letter-spacing: -0.5px;">Caja Abierta</h5>
            <p style="color: #64748b; font-size: 14px; line-height: 1.5; margin: 0;">
              Tu turno sigue activo. Cierra la caja antes de salir para evitar inconsistencias.
            </p>
          </div>
        </div>

        <div style="margin-top: 25px; padding: 15px; background: #f8fafc; border-radius: 16px; border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 12px;">
          <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; animation: pulse 2s infinite;"></div>
          <span style="font-size: 13px; font-weight: 600; color: #475569;">Estado actual: Caja abierta con movimientos</span>
        </div>
      </div>

      <div class="modal-footer border-0" style="padding: 0 30px 30px 30px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; width: 100%;">
          <button type="button" data-bs-dismiss="modal" 
            style="padding: 14px; border-radius: 14px; border: 1.5px solid #e2e8f0; background: white; color: #64748b; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s;">
            Regresar
          </button>
          <button class="main-btn" type="button" 
            style="padding: 14px; border-radius: 14px; border: none; background: #2563EB; color: #ffffff; font-weight: 700; font-size: 14px; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);" data-logout-confirm>
            Cerrar sesion 
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade modal-alegra-final" id="modalCerrarCaja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-alegra-width">
        <div class="modal-content">
            <div class="cierre-wrapper">
                
                <div class="cierre-sidebar">
                    <span class="text-label">Monto de cierre calculado</span>
                    <div class="total-amount">$0</div>

                    <div style="margin-bottom: 30px; padding: 12px 16px; background: #f1f5f9; border-radius: 12px; border: 1px dashed #cbd5e1;">
                      <span style="display: block; font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Monto de Apertura</span>
                      <span class="m-apertura" style="font-size: 16px; font-weight: 700; color: #475569;">$0</span>
                    </div>
                    <div class="grid-methods">
                        <div class="method-card-minimal">
                            <span class="m-title">Efectivo</span>
                            <span class="m-price">$0</span>
                        </div>
                        <div class="method-card-minimal">
                            <span class="m-title">Tarjetas</span>
                            <span class="m-price">$0</span>
                        </div>
                        <div class="method-card-minimal">
                            <span class="m-title">Transferencias</span>
                            <span class="m-price">$0</span>
                        </div>
                        <div class="method-card-minimal">
                            <span class="m-title">Ventas Totales</span>
                            <span class="m-price">0 ventas</span>
                        </div>
                    </div>

                    <div style="margin-top: 60px;">
                        <div style="padding: 20px; background: #ffffff; border-radius: 16px; border: 1px solid #edf2f7; display: flex; align-items: center; gap: 15px;">
                            <div class="status-dot" style="width: 10px; height: 10px; background: #3b82f6; border-radius: 50%;"></div>
                            <div>
                                <span style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Estado de Caja</span>
                                <span style="font-size: 14px; font-weight: 600; color: #334155;">Pendiente</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cierre-main-action">
                    <div class="group-input">
                        <label class="text-label">Efectivo Físico en Caja</label>
                        <input type="text" inputmode="numeric" class="input-clean input-focus-blue" value="0" placeholder="0" autofocus data-raw="0">
                        
                        <div class="badge-diff">
                            Diferencia detectada: <span style="color: #f43f5e;">$0</span>
                        </div>
                    </div>

                    <div class="group-input">
                        <label class="text-label">Observaciones de Cierre</label>
                        <textarea class="input-clean" rows="2" placeholder="Notas sobre el turno..."></textarea>
                    </div>

                    <button class="btn-finalizar">Confirmar Cierre de Caja</button>
                    <button class="btn-exit" data-bs-dismiss="modal">Regresar a Vender</button>
                </div>

            </div>
        </div>
    </div>
</div>
<?php endif; ?>
    
<footer class="footer">
  <div class="container-fluid">
    <div class="row align-items-center justify-content-between">
      <div class="col-md-6 order-last order-md-first">
        <div class="copyright text-center text-md-start">
          <p class="text-sm mb-0">
            © <?php echo e(date('Y')); ?> OptenAdvance · v<?php echo e(config('app.version')); ?>


          </p>
        </div>
      </div>

      <div class="col-md-6">
        <div class="terms d-flex justify-content-center justify-content-md-end">
          <a href="<?php echo e(route('legal.terminos')); ?>" class="text-sm">Términos y Condiciones</a>
          <a href="<?php echo e(route('legal.privacidad')); ?>" class="text-sm ml-15">Política de Privacidad</a>
        </div>
      </div>
    </div>
  </div>
</footer>

  </main>
  <script src="/assets/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/opten-helpers.js"></script>
  <script src="/assets/js/dynamic-pie-chart.js"></script>
  <script src="/assets/js/moment.min.js"></script>
  <script src="/assets/js/main.js"></script>
  
<script>
// Flag desde backend: si el usuario tiene caja abierta
const CAJA_ABIERTA = <?php echo json_encode((bool)($cajaAbierta ?? false), 15, 512) ?>;

OptenHelpers.waitForBootstrap(function() {
  // Buscar forms cuyo action contenga 'logout'
  const logoutForms = Array.from(document.querySelectorAll('form')).filter(f => {
    const action = (f.getAttribute('action') || '').toLowerCase();
    return action.includes('/logout') || action.endsWith('logout');
  });

  logoutForms.forEach(function(form) {
    form.addEventListener('submit', function (ev) {
      // Si la caja no está abierta, permitir submit normal
      if (!CAJA_ABIERTA) return;

      ev.preventDefault();
      ev.stopPropagation(); // Importante: detener propagación
      
      // Guardar referencia del form
      window._pendingLogoutForm = form;

      // Mostrar modal
      var modalEl = document.getElementById('modalConfirmarCierreSesion');
      if (modalEl) {
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
      } else {
        console.error('[OptenAdvance] Modal de confirmación no encontrado');
        form.submit(); // Fallback
      }
    });
  });

  // Botón del modal que confirma logout
  var modalConfirm = document.querySelector('#modalConfirmarCierreSesion [data-logout-confirm]');
  if (modalConfirm) {
    modalConfirm.addEventListener('click', function () {
      var f = window._pendingLogoutForm;
      if (f) {
        // Ocultar modal primero
        var modalEl = document.getElementById('modalConfirmarCierreSesion');
        if (modalEl) {
          var modal = bootstrap.Modal.getInstance(modalEl);
          if (modal) modal.hide();
        }
        // Hacer submit después de un pequeño delay
        setTimeout(function() {
          f.submit();
        }, 300);
      }
    });
  }
});
</script>

  <?php if($cajaAbierta): ?>
  <script>
    OptenHelpers.waitForBootstrap(function() {
      (function () {
        var modal = document.getElementById('modalCerrarCaja');
        if (!modal) {
          console.warn('[OptenAdvance] Modal cerrar caja no encontrado');
          return;
        }

        var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function fmtInt(v) {
          var n = Math.round(Number(v || 0));
          return n.toLocaleString('es-CO');
        }

        function formatMoneyNoDecimals(v) {
          return '$ ' + fmtInt(v);
        }

        function cleanDigits(value) {
          // remove all non-digits, but allow empty string
          return String(value).replace(/\D+/g, '');
        }

        function formatThousandsFromDigits(digits) {
          if (!digits) return '';
          var n = String(Number(digits));
          return n.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function formatInputField(el) {
          var rawSource = (el.value !== undefined && el.value !== null) ? el.value : (el.dataset.raw || '');
          var digits = cleanDigits(rawSource);
          el.dataset.raw = digits;
          el.value = digits === '' ? '' : formatThousandsFromDigits(digits);
        }

        function showError(container, message) {
          var err = container.querySelector('.ajax-error-msg');
          if (!err) {
            err = document.createElement('div');
            err.className = 'alert alert-danger ajax-error-msg';
            container.insertBefore(err, container.firstChild);
          }
          err.textContent = message;
          err.classList.remove('d-none');
        }

        function clearError(container) {
          var err = container.querySelector('.ajax-error-msg');
          if (err) {
            err.classList.add('d-none');
            err.textContent = '';
          }
        }

        function loadResumen() {
          var main = modal.querySelector('.cierre-main-action');
          clearError(main);
          fetch('<?php echo e(route('caja.cierre.resumen')); ?>', { headers: { Accept: 'application/json' } })
            .then(function(res) {
              return res.json().then(function(data) {
                return { res: res, data: data };
              });
            })
            .then(function(result) {
              var res = result.res;
              var data = result.data;
              if (!res.ok || !data.success) throw new Error(data.message || 'No se pudo cargar el resumen de cierre');

              var totalAmountEl = modal.querySelector('.total-amount');
              if (totalAmountEl) totalAmountEl.textContent = formatMoneyNoDecimals(data.monto_cierre_calculado || 0);

              var cards = modal.querySelectorAll('.grid-methods .method-card-minimal');
              if (cards && cards.length >= 4) {
                var ef = cards[0].querySelector('.m-price');
                var ta = cards[1].querySelector('.m-price');
                var tr = cards[2].querySelector('.m-price');
                var vt = cards[3].querySelector('.m-price');
                if (ef) ef.textContent = formatMoneyNoDecimals(data.total_efectivo || 0);
                if (ta) ta.textContent = formatMoneyNoDecimals(data.total_tarjeta || 0);
                if (tr) tr.textContent = formatMoneyNoDecimals(data.total_transferencia || 0);
                if (vt) vt.textContent = (data.total_ventas_cantidad || 0) + ' Ventas';
              }

              var montoApertura = Number(data.monto_apertura || data.monto_apertura_caja || 0);
              var totalEfectivo = Number(data.total_efectivo || 0);
              var montoCalculado = montoApertura + totalEfectivo;

              var aperturaEl = modal.querySelector('.m-apertura');
              if (aperturaEl) aperturaEl.textContent = formatMoneyNoDecimals(montoApertura);

              modal.dataset.montoCalculado = Number(montoCalculado);
              modal.dataset.montoApertura = Number(montoApertura);

              var inputEfectivo = modal.querySelector('.input-focus-blue');
              var badgeDiff = modal.querySelector('.badge-diff');
              if (inputEfectivo) {
                inputEfectivo.value = '';
                inputEfectivo.dataset.raw = '';
              }
              if (badgeDiff) badgeDiff.innerHTML = 'Diferencia detectada: <span style="color: #64748b;">$ 0</span>';

              updateStatus(null);
            })
            .catch(function(err) {
              showError(main, err.message);
            });
        }

        function updateStatus(diferencia) {
          var badge = modal.querySelector('.badge-diff');
          var statusDot = modal.querySelector('.status-dot');
          var sidebar = modal.querySelector('.cierre-sidebar');

          if (diferencia === null || diferencia === undefined) {
            if (badge) badge.innerHTML = 'Diferencia detectada: <span style="color: #64748b;">$ 0</span>';
            if (statusDot) statusDot.style.background = '#3b82f6';
            if (sidebar) {
              var spans = Array.from(sidebar.querySelectorAll('span'));
              var statusSpan = spans.find(function(s) { return /(Balance|Sistema|Descuadre|Pendiente)/i.test(s.textContent); }) || spans[spans.length - 1];
              if (statusSpan) statusSpan.textContent = 'Pendiente';
            }
            return;
          }

          var color = diferencia === 0 ? '#059669' : '#f43f5e';
          if (badge) {
            var formatted = '$ ' + fmtInt(Math.abs(diferencia));
            badge.innerHTML = 'Diferencia detectada: <span style="color: ' + color + '">' + (diferencia < 0 ? '-' : '') + formatted + '</span>';
          }

          if (statusDot) statusDot.style.background = diferencia === 0 ? '#10b981' : '#f43f5e';

          if (sidebar) {
            var spans = Array.from(sidebar.querySelectorAll('span'));
            var statusSpan = spans.find(function(s) { return /(Balance|Sistema|Descuadre|Pendiente)/i.test(s.textContent); }) || spans[spans.length - 1];
            if (statusSpan) {
              statusSpan.textContent = diferencia === 0 ? 'Balance correcto' : 'Descuadre';
            }
          }
        }

        function bindInputListener() {
          var inputEfectivo = modal.querySelector('.input-focus-blue');
          if (!inputEfectivo) return;
          inputEfectivo.addEventListener('input', function () {
            formatInputField(this);
            var raw = this.dataset.raw;
            if (raw === '' || raw === null || raw === undefined) {
              updateStatus(null);
              return;
            }
            var real = Number(raw || 0);
            var calculado = Number(modal.dataset.montoCalculado || 0);
            var diferencia = real - calculado;
            updateStatus(diferencia);
          });
        }

        function bindConfirm() {
          var btn = modal.querySelector('.btn-finalizar');
          var main = modal.querySelector('.cierre-main-action');
          if (!btn) return;
          btn.addEventListener('click', function () {
            clearError(main);
            btn.disabled = true;
            var inputEfectivo = modal.querySelector('.input-focus-blue');
            var raw = inputEfectivo && inputEfectivo.dataset.raw;
            if (!inputEfectivo || raw === '' || raw === undefined) {
              showError(main, 'Ingresa el monto de efectivo en caja');
              btn.disabled = false;
              return;
            }
            var montoReal = Number(raw || 0);
            var nota = modal.querySelector('textarea') && modal.querySelector('textarea').value || null;

            fetch('<?php echo e(route('caja.cerrar')); ?>', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
              },
              body: JSON.stringify({
                monto_cierre_real: montoReal,
                nota_cierre: nota
              })
            })
              .then(function(res) {
                return res.json().then(function(data) {
                  return { res: res, data: data };
                });
              })
              .then(function(result) {
                var res = result.res;
                var data = result.data;
                if (!res.ok || !data.success) {
                  throw new Error(data.message || 'No se pudo cerrar la caja');
                }

                var modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) modalInstance.hide();

                window.location.reload();
              })
              .catch(function(err) {
                showError(main, err.message);
                btn.disabled = false;
              });
          });
        }

        // Bind events
        modal.addEventListener('shown.bs.modal', function () {
          loadResumen();
        });

        // Setup listeners once
        bindInputListener();
        bindConfirm();
      })();
    });
  </script>
  <?php endif; ?>

<script>
// Fix para warning de aria-hidden en modales
OptenHelpers.waitForBootstrap(function() {
  document.addEventListener('DOMContentLoaded', function() {
    // Lista de todos los modales que necesitan fix
    var modalIds = [
      'modalLicencia',
      'modalMensaje', 
      'modalExpirado',
      'modalConfirmarCierreSesion',
      'modalCerrarCaja'
    ];

    modalIds.forEach(function(modalId) {
      var modal = document.getElementById(modalId);
      if (modal) {
        // Cuando el modal se cierra completamente
        modal.addEventListener('hidden.bs.modal', function() {
          // Quita el foco de cualquier elemento activo
          if (document.activeElement) {
            document.activeElement.blur();
          }
        });
      }
    });
  });
});
</script>
<script>
OptenHelpers.waitForBootstrap(function() {
  // 1. Interceptor global para fetch/AJAX
  const originalFetch = window.fetch;
  window.fetch = function(...args) {
    return originalFetch.apply(this, args).then(function(response) {
      // Si es 403 y hay JSON, verificar si debe mostrar modal
      if (response.status === 403) {
        response.clone().json().then(function(data) {
          if (data.show_modal) {
            var modalEl = document.getElementById('modalExpirado');
            if (modalEl) {
              var modal = new bootstrap.Modal(modalEl);
              modal.show();
            }
          }
        }).catch(function() {
          // No es JSON, ignorar
        });
      }
      return response;
    });
  };

  // 2. Verificar si hay flash message de licencia expirada
  <?php if(session('license_expired')): ?>
    var modalEl = document.getElementById('modalExpirado');
    if (modalEl) {
      var modal = new bootstrap.Modal(modalEl);
      modal.show();
    }
  <?php endif; ?>

  // 3. Interceptor para formularios que retornan con error
  document.addEventListener('DOMContentLoaded', function() {
    // Si la página se recargó con el flash message, el código de arriba ya lo manejó
  });
});
</script>
</body>
</body>

</html><?php /**PATH C:\Users\User\Documents\optenadvance\laragon\www\resources\views/layouts/app.blade.php ENDPATH**/ ?>