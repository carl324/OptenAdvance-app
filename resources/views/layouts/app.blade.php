<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'POS')</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Meta CSRF centralizado -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

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
      <a href="index.html">
        <img src="assets/images/logo/logo.svg" alt="logo" />
      </a>
    </div>
    <nav class="sidebar-nav">
      <ul class="d-flex flex-column gap-1">

        <li class="nav-item {{ activeRoute('ventas.create') }}">
          <a href="{{ route('ventas.create') }}" class="d-flex align-items-center px-3 py-3 rounded">
            <span class="icon fs-4 me-3">
              <i class="lni lni-wallet"></i>
            </span>
            <span class="text fw-semibold">Caja</span>
          </a>
        </li>

        <li class="nav-item {{ activeRoute(['ventas.index', 'ventas.show', 'ventas.devolucion', 'ventas.factura*']) }}">
          <a href="{{ route('ventas.index') }}" class="d-flex align-items-center px-3 py-3 rounded">
            <span class="icon fs-4 me-3">
              <i class="lni lni-revenue"></i>
            </span>
            <span class="text fw-semibold">Ventas</span>
          </a>
        </li>

        <li class="nav-item {{ activeRoute(['productos.index', 'productos.create']) }}">
          <a href="{{ route('productos.index') }}" class="d-flex align-items-center px-3 py-3 rounded">
            <span class="icon fs-4 me-3">
              <i class="lni lni-package"></i>
            </span>
            <span class="text fw-semibold">Productos</span>
          </a>
        </li>

        <li class="nav-item {{ activeRoute('reportes.index') }}">
          <a href="{{ route('reportes.index') }}" class="d-flex align-items-center px-3 py-3 rounded">
            <span class="icon fs-4 me-3">
              <i class="lni lni-bar-chart"></i>
            </span>
            <span class="text fw-semibold">Reportes</span>
          </a>
        </li>

        <li class="nav-item {{ activeRoute(['empresa.index', 'empresa.edit']) }}">
          <a href="{{ route('empresa.index') }}" class="d-flex align-items-center px-3 py-3 rounded">
            <span class="icon fs-4 me-3">
              <i class="lni lni-apartment"></i>
            </span>
            <span class="text fw-semibold">Empresa</span>
          </a>
        </li>

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
            </div>
          </div>
        </div>
      </div>
    </header>
    <!-- ========== header end =========== -->

    <!-- ========== MAIN CONTENT AREA ========== -->
    <!-- INSERTAR CONTENIDO PRINCIPAL AQUI -->

    <!-- ========== MAIN CONTENT AREA ========== -->

    <!-- ========== footer start =========== -->

    @yield('content')
    <footer class="footer">
      <div class="container-fluid">
        <div class="row align-items-center justify-content-between">
          <div class="col-md-6 order-last order-md-first">
            <div class="copyright text-center text-md-start">
              <p class="text-sm">
                Designed and Developed by
                <a href="https://plainadmin.com" rel="nofollow" target="_blank">
                  PlainAdmin
                </a>
              </p>
            </div>
          </div>
          <!-- end col-->
          <div class="col-md-6">
            <div class="terms d-flex justify-content-center justify-content-md-end">
              <a href="#0" class="text-sm">Term & Conditions</a>
              <a href="#0" class="text-sm ml-15">Privacy & Policy</a>
            </div>
          </div>
        </div>
        <!-- end row -->
      </div>
      <!-- end container -->
    </footer>
  </main>
  <script src="/assets/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/Chart.min.js"></script>
  <script src="/assets/js/dynamic-pie-chart.js"></script>
  <script src="/assets/js/moment.min.js"></script>
  <script src="/assets/js/fullcalendar.js"></script>
  <script src="/assets/js/jvectormap.min.js"></script>
  <script src="/assets/js/world-merc.js"></script>
  <script src="/assets/js/polyfill.js"></script>
  <script src="/assets/js/main.js"></script>


</body>

</html>