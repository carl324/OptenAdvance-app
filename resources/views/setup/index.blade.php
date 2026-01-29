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
.input-group-password {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  transition: all 0.2s ease;
}

.input-group-password:focus-within {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.input-group-password .form-control {
  border: none !important;
  background: #f9fafb;
  padding: 12px 16px;
  font-size: 14px;
  box-shadow: none !important;
}

.input-group-password .form-control:focus {
  background: #ffffff;
}

.btn-password-toggle {
  border: none !important;
  background: #f9fafb !important;
  color: #64748b !important;
  padding: 0 16px !important;
  transition: all 0.2s ease !important;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-password-toggle:hover {
  background: #f1f5f9 !important;
  color: #0f172a !important;
}

.btn-password-toggle:active {
  transform: scale(0.95);
}

.btn-password-toggle i {
  font-size: 20px;
}
</style>
</head>
<body>
  <!-- ======== main-wrapper start =========== -->
    <section class="signin-section">
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          
          <!-- ========== title-wrapper end ========== -->

          <div class="row g-0 auth-row">
            <div class="col-lg-6">
              <div class="auth-cover-wrapper bg-primary-100">
                <div class="auth-cover">
                  
                  <div class="cover-image">
                    <img src="assets/images/auth/admin.png" alt="" />
                  </div>
                  <div class="shape-image">
                    <img src="assets/images/auth/shape.svg" alt="" />
                  </div>
                </div>
              </div>
            </div>
            <!-- end col -->
            <div class="col-lg-6">
              <div class="signin-wrapper">
                <div class="form-wrapper">
                  <h6 class="mb-15">Crear Cuenta de Administrador</h6>
                  <p class="text-sm mb-25">
                    Crea una cuenta de administrador para comenzar a usar el sistema.
                  </p>
                  <form id="setup-form" data-endpoint="{{ route('setup.store') }}" novalidate>
                    <div id="setup-alerts"></div>
                    @csrf
                    <div class="row">

                        <div class="col-12">
                        <div class="input-style-1">
                          <label>Nombre</label>
                          <input type="text" name="name" value="{{ old('name') }}" placeholder="Nombre" />
                          @error('name')
                            <div class="text-danger" style="font-size:13px; margin-top:6px;">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      
                      
                      <div class="col-12">
                        <div class="input-style-1">
                          <label>Email</label>
                          <input type="text" name="email" inputmode="email" autocomplete="email" value="{{ old('email') }}" placeholder="Email" />
                          @error('email')
                            <div class="text-danger" style="font-size:13px; margin-top:6px;">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>
                      <!-- end col -->
                      <div class="col-12">
                        <div class="mb-3">
  <label class="form-label fw-semibold" style="color: #0f172a; font-size: 14px;">Contraseña</label>
  <div class="input-group input-group-password">
    <input type="password" name="password" id="password-input" class="form-control" placeholder="Ingresa tu contraseña" style="border-right: none; padding-right: 12px;" />
    <button class="btn btn-password-toggle" type="button" id="togglePassword" aria-label="Mostrar contraseña">
      <i class="mdi mdi-eye" id="eye-icon"></i>
    </button>
  </div>
  @error('password') 
  <div class="text-danger small mt-2">{{ $message }}</div> 
  @enderror 
</div>




                      </div>
                      <!-- end col -->
                      
                      <!-- end col -->
                     
                      <!-- end col -->
                      <div class="col-12">
                        <div class="button-group d-flex justify-content-center flex-wrap">
                          <button type="button" id="btn-create-account" class="main-btn primary-btn btn-hover w-100 text-center">
                            Crear Cuenta
                          </button>
                        </div>
                      </div>
                    </div>
                    <!-- end row -->
                  </form>
                </div>
              </div>
            </div>
            <!-- end col -->
          </div>
          <!-- end row -->
        </div>
    </section>

   

 <script src="/assets/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/Chart.min.js"></script>
  <script src="/assets/js/dynamic-pie-chart.js"></script>
  <script src="/assets/js/moment.min.js"></script>
  <script src="/assets/js/fullcalendar.js"></script>
  <script src="/assets/js/jvectormap.min.js"></script>
  <script src="/assets/js/world-merc.js"></script>
  <script src="/assets/js/polyfill.js"></script>
  <script src="/assets/js/main.js"></script>
  
<script> 
(function(){ 
  var btn = document.getElementById('togglePassword'); 
  var icon = document.getElementById('eye-icon');
  var pwd = document.getElementById('password-input');
  if(!btn || !icon || !pwd) return; 
  
  btn.addEventListener('click', function(){ 
    if(pwd.type === 'password'){ 
      // Mostrar contraseña → cambiar a ojo CERRADO
      pwd.type = 'text'; 
      
      // ========== OPCIÓN 1: MDI (Material Design Icons) ==========
      icon.className = 'mdi mdi-eye-off'; // Ojo tachado/cerrado
      
      // ========== OPCIÓN 2: LineIcons ==========
      // icon.className = 'lni lni-lock'; // Candado (alternativa porque LineIcons no tiene eye-off)
      
      this.setAttribute('aria-label','Ocultar contraseña'); 
    } else { 
      // Ocultar contraseña → cambiar a ojo ABIERTO
      pwd.type = 'password'; 
      
      // ========== OPCIÓN 1: MDI (Material Design Icons) ==========
      icon.className = 'mdi mdi-eye'; // Ojo abierto
      
      // ========== OPCIÓN 2: LineIcons ==========
      // icon.className = 'lni lni-eye'; // Ojo abierto
      
      this.setAttribute('aria-label','Mostrar contraseña'); 
    } 
  }); 
})(); 
</script>
<script>
// Envío AJAX seguro para setup-form (evitar validación nativa)
(function(){
  var form = document.getElementById('setup-form');
  var btn = document.getElementById('btn-create-account');
  if (!form || !btn) return;
  form.noValidate = true;
  form.addEventListener('submit', function(e){ e.preventDefault(); e.stopImmediatePropagation(); });
  form.addEventListener('keydown', function(e){ if (e.key === 'Enter') { e.preventDefault(); e.stopImmediatePropagation(); } });

  btn.addEventListener('click', function(){
    btn.disabled = true;
    var fd = new FormData(form);
    // limpiar alertas previas
    var alerts = document.getElementById('setup-alerts');
    if (alerts) alerts.innerHTML = '';

    fetch(form.dataset.endpoint, {
      method: 'POST',
      body: fd,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    }).then(function(res){
      if (res.ok) {
        if (res.redirected) window.location.href = res.url; else window.location.reload();
        return;
      }
      if (res.status === 422) return res.json().then(function(data){
        var errors = data.errors || {};
        var html = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        html += '<strong>Error:</strong><ul style="margin-top:6px; margin-bottom:6px;">';
        Object.keys(errors).forEach(function(k){
          var msg = errors[k][0];
          html += '<li>' + (msg || k) + '</li>';
        });
        html += '</ul>';
        html += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>';
        html += '</div>';
        if (alerts) alerts.innerHTML = html;
        // poner foco en el primer campo con error si existe
        var firstKey = Object.keys(errors)[0];
        if (firstKey) {
          var el = form.querySelector('[name="' + firstKey + '"]');
          if (el && typeof el.focus === 'function') el.focus();
        }
      });
      // otros errores HTTP
      if (alerts) alerts.innerHTML = '<div class="alert alert-danger" role="alert">Ocurrió un error. Intente nuevamente.</div>';
    }).catch(function(){
      if (alerts) alerts.innerHTML = '<div class="alert alert-danger" role="alert">Ocurrió un error de red.</div>';
    })
    .finally(function(){ btn.disabled = false; });
  });
})();
</script>
  </body>
</html>