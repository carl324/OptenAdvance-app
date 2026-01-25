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
        <img src="/assets/images/logo/horizontal.svg" alt="logo" />
      </a>
    </div>
    <nav class="sidebar-nav">
      <ul class="d-flex flex-column gap-1">
        @php $role = Auth::user()->role ?? 'empleado'; @endphp
        @if($role === 'admin')
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
          <span class="divider"><hr /></span>
          <li class="nav-item {{ activeRoute('reportes.index') }}">
            <a href="{{ route('reportes.index') }}" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="lni lni-bar-chart"></i>
              </span>
              <span class="text fw-semibold">Reportes</span>
            </a>
          </li>
          <li class="nav-item {{ activeRoute('personal.index') }}">
            <a href="{{ route('personal.index') }}" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="mdi mdi-briefcase-account"></i>
              </span>
              <span class="text fw-semibold">Personal</span>
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
          <span class="divider"><hr /></span>
          <li class="nav-item {{ activeRoute('soporte.index') }}">
            <a href="{{ route('soporte.index') }}" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="lni lni-cogs"></i>
              </span>
              <span class="text fw-semibold">Soporte</span>
            </a>
          </li>
        @else
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
          <span class="divider"><hr /></span>
          <li class="nav-item {{ activeRoute('soporte.index') }}">
            <a href="{{ route('soporte.index') }}" class="d-flex align-items-center px-3 py-3 rounded">
              <span class="icon fs-4 me-3">
                <i class="lni lni-cogs"></i>
              </span>
              <span class="text fw-semibold">Soporte</span>
            </a>
          </li>
        @endif
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
                    @if($cajaAbierta)
                      <span style="background-color: #10b981; color: #fff;   "></span>
                    @else
                      <span style="background-color: #f32035; color: #fff;   "></span>
                    @endif
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="message">
                    <!-- Estado de la caja -->
                    <li class="caja-estado-item">
                      <div class="caja-estado-header">
                        <div class="estado-badge {{ $cajaAbierta ? 'estado-abierta' : 'estado-cerrada' }}">
                          <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                            <circle cx="5" cy="5" r="5" fill="currentColor"/>
                          </svg>
                          <span>{{ $cajaAbierta ? 'Caja Abierta' : 'Caja Cerrada' }}</span>
                        </div>
                        <span class="estado-hora">{{ $cajaHoraApertura ?? '--:--' }}</span>
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
                            {{ $cajaAbierta ? ($ventasHoy ?? 0) . ' ventas' : '--' }}
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
                            {{ $cajaAbierta ? '$ ' . number_format($ingresosHoy ?? 0, 2, ',', '.') : '--' }}
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
                        @if($cajaAbierta)
                          <button class="main-btn danger-btn btn-hover btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#modalCerrarCaja">
                            <i class="lni lni-lock"></i> Cerrar caja
                          </button>
                        @endif
                      </div>
                    </li>
                  </ul>





                </div>
                <!-- message end -->
                <!-- profile start -->
                <div class="profile-box ml-15">
                  @php
                    $user = auth()->user();
                    $userName = $user->name ?? $user->username ?? 'Usuario';
                    $userRole = $user->role ?? 'empleado';
                    $profileImage = $userRole === 'admin'
                      ? '/assets/images/profile/admin.png'
                      : '/assets/images/profile/empleado.png';
                  @endphp
                  <button class="dropdown-toggle bg-transparent border-0" type="button" id="profile"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="profile-info">
                      <div class="info">
                        <div class="image">
                          <img src="{{ $profileImage }}" alt="" />
                        </div>
                        <div>
                          <h6 class="fw-500">{{ $userName }}</h6>
                          <p>{{ ucfirst($userRole) }}</p>
                        </div>
                      </div>
                    </div>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profile">
                    <li>
                      <a href="{{ route('soporte.index') }}">
                        <i class="lni lni-cog"></i> Soporte
                      </a>
                    </li>
                    @if($userRole === 'admin')
                      <li>
                        <a href="{{ route('empresa.index') }}"> <i class="lni lni-apartment"></i> Empresa </a>
                      </li>
                    @endif
                    <li class="divider"></li>
                    <li>
                      <form method="POST" action="{{ route('logout') }}" >
                        @csrf
                        <button type="submit" >
                          <i class="lni lni-exit"></i> Sign Out
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
    <!-- ========== header end =========== -->

    <!-- ========== MAIN CONTENT AREA ========== -->
    <!-- INSERTAR CONTENIDO PRINCIPAL AQUI -->

    <!-- ========== MAIN CONTENT AREA ========== -->

    <!-- ========== footer start =========== -->

    @yield('content')

    @if($cajaAbierta)
    <div class="modal fade" id="modalCerrarCaja" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content alegra-modal-square px-2">
          <div class="modal-header border-0 pb-3">
            <h6 class="text-medium mb-0">Cierre de caja</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body pt-0">
            <div id="cierre-error" class="alert alert-danger d-none mb-3" role="alert"></div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label text-xs text-gray">Total de ventas del día</label>
                <input type="text" class="form-control form-control-sm alegra-input" id="cierre-total-ventas" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label text-xs text-gray">Total ingresos del día</label>
                <input type="text" class="form-control form-control-sm alegra-input" id="cierre-total-ingresos" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label text-xs text-gray">Total efectivo</label>
                <input type="text" class="form-control form-control-sm alegra-input" id="cierre-total-efectivo" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label text-xs text-gray">Total tarjeta</label>
                <input type="text" class="form-control form-control-sm alegra-input" id="cierre-total-tarjeta" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label text-xs text-gray">Total transferencias</label>
                <input type="text" class="form-control form-control-sm alegra-input" id="cierre-total-transferencia" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label text-xs text-gray">Total otros métodos</label>
                <input type="text" class="form-control form-control-sm alegra-input" id="cierre-total-otros" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label text-xs text-gray">Monto cierre calculado</label>
                <input type="text" class="form-control form-control-sm alegra-input" id="cierre-monto-calculado" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label text-xs text-gray">Monto cierre real</label>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm alegra-input" id="cierre-monto-real" required>
              </div>
              <div class="col-md-6">
                <label class="form-label text-xs text-gray">Diferencia</label>
                <input type="text" class="form-control form-control-sm alegra-input" id="cierre-diferencia" readonly>
              </div>
              <div class="col-12">
                <label class="form-label text-xs text-gray">Nota de cierre (opcional)</label>
                <textarea class="form-control form-control-sm alegra-input" id="cierre-nota" rows="2" maxlength="255"></textarea>
              </div>
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="cierre-imprimir">
                  <label class="form-check-label" for="cierre-imprimir">Imprimir reporte de cierre</label>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 pt-3">
            <div class="d-flex gap-3 w-100">
              <button class="main-btn light-btn btn-hover flex-fill" type="button" data-bs-dismiss="modal">Cancelar</button>
              <button class="main-btn danger-btn btn-hover flex-fill" type="button" id="btn-confirmar-cierre">Cerrar caja</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif
    <footer class="footer">
  <div class="container-fluid">
    <div class="row align-items-center justify-content-between">
      <div class="col-md-6 order-last order-md-first">
        <div class="copyright text-center text-md-start">
          <p class="text-sm mb-0">
            © {{ date('Y') }} OptenAdvance · v1.0.0
          </p>
        </div>
      </div>

      <div class="col-md-6">
        <div class="terms d-flex justify-content-center justify-content-md-end">
          <a href="/terminos-y-condiciones" class="text-sm">Términos y Condiciones</a>
          <a href="/politica-de-privacidad" class="text-sm ml-15">Política de Privacidad</a>
        </div>
      </div>
    </div>
  </div>
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

  @if($cajaAbierta)
  <script>
    const modalCerrarCaja = document.getElementById('modalCerrarCaja');
    const cierreError = document.getElementById('cierre-error');
    const cierreMontoReal = document.getElementById('cierre-monto-real');
    const cierreDiferencia = document.getElementById('cierre-diferencia');
    const btnConfirmarCierre = document.getElementById('btn-confirmar-cierre');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function formatoMoneda(valor) {
      const numero = Number(valor || 0);
      return '$' + numero.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function setCierreError(mensaje) {
      cierreError.textContent = mensaje;
      cierreError.classList.remove('d-none');
    }

    function limpiarCierreError() {
      cierreError.classList.add('d-none');
      cierreError.textContent = '';
    }

    async function cargarResumenCierre() {
      limpiarCierreError();
      try {
        const res = await fetch('{{ route('caja.cierre.resumen') }}', {
          headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
          throw new Error(data.message || 'No se pudo cargar el resumen de cierre');
        }

        document.getElementById('cierre-total-ventas').value = data.total_ventas_cantidad ?? 0;
        document.getElementById('cierre-total-ingresos').value = formatoMoneda(data.total_ingresos);
        document.getElementById('cierre-total-efectivo').value = formatoMoneda(data.total_efectivo);
        document.getElementById('cierre-total-tarjeta').value = formatoMoneda(data.total_tarjeta);
        document.getElementById('cierre-total-transferencia').value = formatoMoneda(data.total_transferencia);
        document.getElementById('cierre-total-otros').value = formatoMoneda(data.total_otros);
        document.getElementById('cierre-monto-calculado').value = formatoMoneda(data.monto_cierre_calculado);

        cierreMontoReal.value = '';
        cierreDiferencia.value = formatoMoneda(0);
        cierreMontoReal.dataset.montoCalculado = data.monto_cierre_calculado;
      } catch (error) {
        setCierreError(error.message);
      }
    }

    function calcularDiferencia() {
      const calculado = Number(cierreMontoReal.dataset.montoCalculado || 0);
      const real = Number(cierreMontoReal.value || 0);
      const diferencia = real - calculado;
      cierreDiferencia.value = formatoMoneda(diferencia);
    }

    if (modalCerrarCaja) {
      modalCerrarCaja.addEventListener('shown.bs.modal', cargarResumenCierre);
    }

    if (cierreMontoReal) {
      cierreMontoReal.addEventListener('input', calcularDiferencia);
    }

    if (btnConfirmarCierre) {
      btnConfirmarCierre.addEventListener('click', async () => {
        limpiarCierreError();
        const montoReal = cierreMontoReal.value;
        if (!montoReal) {
          setCierreError('Debes ingresar el monto de cierre real');
          return;
        }

        const nota = document.getElementById('cierre-nota').value || null;
        const imprimir = document.getElementById('cierre-imprimir').checked;

        try {
          const res = await fetch('{{ route('caja.cerrar') }}', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              monto_cierre_real: montoReal,
              nota_cierre: nota,
              imprimir: imprimir
            })
          });

          const data = await res.json();
          if (!res.ok || !data.success) {
            throw new Error(data.message || 'No se pudo cerrar la caja');
          }

          if (data.print_url) {
            window.open(data.print_url, '_blank');
          }

          const modalInstance = bootstrap.Modal.getInstance(modalCerrarCaja);
          if (modalInstance) modalInstance.hide();

          window.location.href = '{{ route('ventas.create') }}';
        } catch (error) {
          setCierreError(error.message);
        }
      });
    }
  </script>
  @endif


</body>

</html>