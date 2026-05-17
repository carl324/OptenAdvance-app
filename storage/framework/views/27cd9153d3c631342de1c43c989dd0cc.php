<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?php echo $__env->yieldContent('title', 'Inicia sesión'); ?></title>
    <link rel="icon" type="image/png" href="/assets/images/logo/icon.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
                                            <input type="text" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="email" value="<?php echo e(old('email')); ?>" placeholder="Ingresa tu correo electrónico" />
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
                                                <input type="password" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" name="password" id="password-input" class="form-control" placeholder="Ingresa tu contraseña" style="border-right: none; padding-right: 12px;" />
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
                                    <div class="col-12 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                            <label class="form-check-label text-sm" for="remember" style="color:#64748b;">
                                                Mantener sesión iniciada
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12"><br><br>
                                        <div class="button-group d-flex justify-content-center flex-wrap">
                                            <button class="main-btn primary-btn btn-hover w-100 text-center">
                                                Iniciar Sesión
                                            </button>
                                        </div>

                                        <div class="text-start mt-3">
                                            <a href="#" class="text-decoration-none fw-semibold small text-muted" data-bs-toggle="modal" data-bs-target="#recoveryInfoModal">
                                                ¿Has olvidado tu contraseña?
                                            </a>

                                        </div>

                                    </div>

                                </div>
                                <!-- end row -->
                            </form>
                        </div>

                    </div>

<div class="container">
  <div class="row">
    <div class="col-md-6 d-flex align-items-center">
      <p class="text-sm mb-0">
        © <?php echo e(date('Y')); ?> OptenAdvance · v<?php echo e(config('app.version')); ?>

      </p>
    </div>

    <div class="col-md-6 d-flex align-items-center justify-content-md-end">
      <p class="text-sm mb-0 ">
        ¿Problemas? <a href="<?php echo e(route('soporte.off')); ?>">Contactar a soporte</a>
      </p>
    </div>
  </div>
</div>



            </div>
            <!-- end col -->
             
          </div>
          
          <!-- end row -->
        </div>
    </section>
<div class="modal fade" id="recoveryInfoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
    
    <div class="modal-content" style="border: none; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); overflow: hidden; font-family: 'Segoe UI', Roboto, sans-serif;">
      
      <div class="modal-header" style="border-bottom: none; padding: 30px 30px 10px 30px;">
        <h5 class="modal-title" style="font-weight: 700; color: #2d3436; font-size: 1.25rem;">Recuperación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.8rem;"></button>
      </div>

      <div class="modal-body" style="padding: 10px 30px 30px 30px;">
        <p style="color: #636e72; line-height: 1.6; font-size: 0.95rem; margin-bottom: 25px;">
         Por razones de seguridad, la recuperación de contraseña solo puede ser gestionada por un <strong>administrador</strong> del sistema. Si la has olvidado, contacta a tu administrador para recibir asistencia.
        </p>

        <div class="form-check" id="checkboxContainer" style="background: #f8f9fa; padding: 12px 12px 12px 35px; border-radius: 12px; transition: all 0.3s ease;">
          <input class="form-check-input" type="checkbox" id="understandCheck" style="cursor: pointer; margin-top: 0.3rem;">
          <label class="form-check-label" for="understandCheck" style="cursor: pointer; color: #2d3436; font-size: 0.85rem; font-weight: 500;">
            Entiendo el procedimiento.
          </label>
        </div>
        
        <p id="errorMessage" style="color: #e74c3c; font-size: 0.85rem; margin-top: 10px; display: none; font-weight: 600;">
          <i class="fas fa-exclamation-circle"></i> Debes confirmar que entiendes el procedimiento
        </p>
      </div>

      <div class="modal-footer" style="border-top: none; padding: 0 30px 30px 30px; justify-content: center;">
        <button type="button" class="btn" id="continueBtn" onclick="validateAndRedirect()" 
                style="background-color: #2d3436; color: white; border-radius: 10px; padding: 10px 40px; font-weight: 600; font-size: 0.9rem; border: none; transition: transform 0.2s ease; width: 100%;">
          Continuar
        </button>
      </div>

    </div>
  </div>
</div>

<style>
  .shake {
    animation: shake 0.5s;
  }
  
  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
  }
  
  .error-border {
    border: 2px solid #e74c3c !important;
    background: #ffe5e5 !important;
  }
</style>

<script>
function validateAndRedirect() {
  const checkbox = document.getElementById('understandCheck');
  const checkboxContainer = document.getElementById('checkboxContainer');
  const errorMessage = document.getElementById('errorMessage');
  const continueBtn = document.getElementById('continueBtn');
  
  if (!checkbox.checked) {
    // Marcar en rojo
    checkboxContainer.classList.add('error-border', 'shake');
    errorMessage.style.display = 'block';
    
    // Shake animation al botón
    continueBtn.classList.add('shake');
    
    // Remover la animación después de que termine
    setTimeout(() => {
      checkboxContainer.classList.remove('shake');
      continueBtn.classList.remove('shake');
    }, 500);
    
    return false;
  }
  
  // Si el checkbox está marcado, redirigir
  window.location.href = '<?php echo e(url("superadmin/login")); ?>';
}

// Remover el error cuando el usuario marque el checkbox
document.getElementById('understandCheck').addEventListener('change', function() {
  if (this.checked) {
    document.getElementById('checkboxContainer').classList.remove('error-border');
    document.getElementById('errorMessage').style.display = 'none';
  }
});

// También permitir presionar Enter para continuar
document.getElementById('recoveryInfoModal').addEventListener('keypress', function(e) {
  if (e.key === 'Enter') {
    validateAndRedirect();
  }
});
</script>
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
</html><?php /**PATH C:\OptenAdvance\app\www\resources\views\auth\login.blade.php ENDPATH**/ ?>