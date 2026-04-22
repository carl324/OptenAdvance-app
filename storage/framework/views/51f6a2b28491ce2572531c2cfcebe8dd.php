<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title><?php echo $__env->yieldContent('title', 'Registro'); ?></title>
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
                  <h6 class="mb-15">Crea tu cuenta por primera vez</h6>
                  <p class="text-sm mb-25">
                    Crea una cuenta de administrador para comenzar a usar el sistema.
                  </p>
                  <form id="setup-form" data-endpoint="<?php echo e(route('setup.store')); ?>" novalidate>
                    <div id="setup-alerts"></div>
                    <?php echo csrf_field(); ?>
                    <div class="row">

                        <div class="col-12">
                        <div class="input-style-1">
                          <label>Nombre</label>
                          <input type="text" name="name" value="<?php echo e(old('name')); ?>" placeholder="Nombre" />
                          <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger" style="font-size:13px; margin-top:6px;"><?php echo e($message); ?></div>
                          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                      </div>

                      
                      
                      <div class="col-12">
                        <div class="input-style-1">
                          <label>Email</label>
                          <input type="text" name="email" inputmode="email" autocomplete="email" value="<?php echo e(old('email')); ?>" placeholder="Email" />
                          <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger" style="font-size:13px; margin-top:6px;"><?php echo e($message); ?></div>
                          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
  <br>
 <div class="col-12">
  <div class="form-check mb-25" style="display: flex; align-items: flex-start; gap: 10px;">
    <input 
      class="form-check-input" 
      type="checkbox" 
      name="terms" 
      id="terms" 
      style="width: 18px; height: 18px; cursor: pointer; border: 1px solid #d1d5db;"
      required
    />
    <label class="form-check-label" for="terms" style="font-size: 14px; color: #5d657b; cursor: pointer; line-height: 1.4;">
      Al continuar, aceptas nuestros 
      <a href="<?php echo e(route('legal.terminos')); ?>" class="text-primary" style="text-decoration: none; font-weight: 600;">Términos y Condiciones</a> 
      y la 
      <a href="<?php echo e(route('legal.privacidad')); ?>" class="text-primary" style="text-decoration: none; font-weight: 600;">Política de Privacidad</a>.
    </label>
  </div>
  <?php $__errorArgs = ['terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
    <div class="text-danger small" style="margin-top: -15px; margin-bottom: 15px;"><?php echo e($message); ?></div>
  <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>



                      </div>
                      <!-- end col -->
                      
                      <!-- end col -->
                     
                      <!-- end col -->
                      <div class="col-12">
                        <div class="button-group d-flex justify-content-center flex-wrap">
                          <button type="button" id="btn-create-account" class="main-btn primary-btn btn-hover w-100 text-center">
                            Crear  Cuenta
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
      pwd.type = 'text'; 
      icon.className = 'mdi mdi-eye-off';
      this.setAttribute('aria-label','Ocultar contraseña'); 
    } else { 
      pwd.type = 'password'; 
      icon.className = 'mdi mdi-eye';
      this.setAttribute('aria-label','Mostrar contraseña'); 
    }
  }); 
})(); 

// Envío AJAX seguro para setup-form (evitar validación nativa)
(function(){
  var form = document.getElementById('setup-form');
  var btn = document.getElementById('btn-create-account');
  var termsCheckbox = document.getElementById('terms');
  var termsLabel = document.querySelector('label[for="terms"]');
  
  if (!form || !btn || !termsCheckbox) return;
  
  form.noValidate = true;
  form.addEventListener('submit', function(e){ e.preventDefault(); e.stopImmediatePropagation(); });
  form.addEventListener('keydown', function(e){ if (e.key === 'Enter') { e.preventDefault(); e.stopImmediatePropagation(); } });

  // Función para validar términos
  function validateTerms() {
    if (!termsCheckbox.checked) {
      // Marcar en rojo
      termsCheckbox.style.borderColor = '#eb6060';
      termsCheckbox.style.outline = '#dc2626';
      if (termsLabel) {
        termsLabel.style.color = '#dc2626';
      }
      
      // Mostrar mensaje de error
      var alerts = document.getElementById('setup-alerts');
      if (alerts) {
        alerts.innerHTML = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
          '<strong>¡Atención!</strong> Debes aceptar los Términos y Condiciones para continuar.' +
          '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>' +
          '</div>';
      }
      
      // Scroll al checkbox
      termsCheckbox.scrollIntoView({ behavior: 'smooth', block: 'center' });
      
      return false;
    }
    
    // Limpiar estilos de error si está marcado
    termsCheckbox.style.borderColor = '';
    termsCheckbox.style.outline = '';
    if (termsLabel) {
      termsLabel.style.color = '';
    }
    
    return true;
  }

  // Limpiar error cuando se marque el checkbox
  termsCheckbox.addEventListener('change', function() {
    if (this.checked) {
      this.style.borderColor = '';
      this.style.outline = '';
      if (termsLabel) {
        termsLabel.style.color = '';
      }
      // Limpiar alerta si existe
      var alerts = document.getElementById('setup-alerts');
      if (alerts) {
        alerts.innerHTML = '';
      }
    }
  });

  btn.addEventListener('click', function(){
    // Primero validar términos
    if (!validateTerms()) {
      return;
    }
    
    // Guardar texto original del botón
    var originalText = btn.innerHTML;
    
    // Cambiar a estado de carga
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creando cuenta...';
    
    var fd = new FormData(form);
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
        var firstKey = Object.keys(errors)[0];
        if (firstKey) {
          var el = form.querySelector('[name="' + firstKey + '"]');
          if (el && typeof el.focus === 'function') el.focus();
        }
      });
      if (alerts) alerts.innerHTML = '<div class="alert alert-danger" role="alert">Ocurrió un error. Intenta nuevamente.</div>';
    }).catch(function(){
      if (alerts) alerts.innerHTML = '<div class="alert alert-danger" role="alert">Ocurrió un error de red.</div>';
    })
    .finally(function(){ 
      btn.disabled = false; 
      btn.innerHTML = originalText;
    });
  });
})();
</script>
  </body>
</html><?php /**PATH C:\optenadvance\app\www\resources\views/setup/index.blade.php ENDPATH**/ ?>