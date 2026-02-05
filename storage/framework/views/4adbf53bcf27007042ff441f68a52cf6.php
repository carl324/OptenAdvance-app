<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title><?php echo $__env->yieldContent('title', 'Inicia sesión'); ?></title>
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
/* Chrome / Edge (WebKit / Blink) */
input[type="password"]::-webkit-credentials-auto-fill-button {
  visibility: hidden;
  position: absolute;
  right: 0;
}

/* Edge / IE */
input[type="password"]::-ms-reveal,
input[type="password"]::-ms-clear {
  display: none;
}

/* Opcional: refuerzo general */
input[type="password"] {
  appearance: none;
  -webkit-appearance: none;
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
                    <img src="assets/images/auth/login.png" alt="" />
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
                  <h6 class="mb-15">Formulario de inicio de sesión</h6>
                  <p class="text-sm mb-25">
                    Ingresa tus credenciales para acceder al sistema.
                  </p>
                  <form method="POST" autocomplete="off" action="<?php echo e(route('login.submit')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row">

                      <?php if($errors->has('auth')): ?>
                        <div class="col-12">
                          <div class="alert alert-danger" role="alert"><?php echo e($errors->first('auth')); ?></div>
                        </div>
                      <?php endif; ?>

                        <div class="col-12">
                        <div class="input-style-1">
                          <label>Correo electrónico</label>
                          <input type="text" autocomplete="off"
  autocorrect="off"
  autocapitalize="off"
  spellcheck="false" name="email" value="<?php echo e(old('email')); ?>" placeholder="Ingresa tu correo electrónico" />
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
                      <!-- Reemplaza toda la sección del password en login.blade.php -->

<div class="col-12">
  <div class="mb-3">
    <label class="form-label fw-semibold" style="color: #0f172a; font-size: 14px;">Contraseña</label>
    <div class="input-group input-group-password">
      <input type="password"  autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" name="password" id="password-input" class="form-control" placeholder="Ingresa tu contraseña" style="border-right: none; padding-right: 12px;" />
      <button class="btn btn-password-toggle" type="button" id="togglePassword" aria-label="Mostrar contraseña">
        <i class="mdi mdi-eye" id="eye-icon"></i>
      </button>
    </div>
    
    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
    <div class="text-danger small mt-2"><?php echo e($message); ?></div> 
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> 
  </div>
</div>
                      
                      <!-- end col -->
                      
                      <!-- end col -->
                     
                      <!-- end col -->
                      <div class="col-12"><br><br>
                        <div class="button-group d-flex justify-content-center flex-wrap">
                          <button class="main-btn primary-btn btn-hover w-100 text-center">
                            Iniciar Sesión
                          </button>
                        </div>
                      </div>
                    </div>
                    <!-- end row -->
                  </form>
                </div>
                
              </div>
              
<div class="col-md-6 d-flex justify-content-center align-items-center">
  <p class="text-sm mb-0">
    © <?php echo e(date('Y')); ?> OptenAdvance · v<?php echo e(config('app.version')); ?>

  </p>
</div>
            </div>
            <!-- end col -->
             
          </div>
          
          <!-- end row -->
        </div>
    </section>

   <!-- <div class="login-wrap">
        <form method="POST" action="<?php echo e(route('login.submit')); ?>">
            <?php echo csrf_field(); ?>
            <div class="field">
                <label for="username">Usuario</label>
                <input id="username" name="username" type="text" value="<?php echo e(old('username')); ?>" required autofocus>
                <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="field">
                <label for="password">Contraseña</label>
                <input id="password" name="password" type="password" required>
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <button type="submit">Entrar</button>
        </form>
    </div> -->

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
  </body>
</html><?php /**PATH C:\Users\User\Documents\optenadvance\laragon\www\optenadvance\resources\views/auth/login.blade.php ENDPATH**/ ?>