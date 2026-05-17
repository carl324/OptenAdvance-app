<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Super Admin - Acceso Restringido</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/assets/css/lineicons.css" />
    <link rel="stylesheet" href="/assets/css/materialdesignicons.min.css" />
    <link rel="stylesheet" href="/assets/css/fullcalendar.css" />
    <link rel="stylesheet" href="/assets/css/main.css" />
</head>
<body class="login-body">
    
    <div class="login-container">
        
        <!-- Logo/Header -->
        <div class="text-center mb-4">
            <div class="logo-shield">
                <i class="mdi mdi-shield-lock text-white"></i>
            </div>
        </div>

        <!-- Login Card -->
        <div class="login-card">
            
            <div class="card-header-login">
                <h2 class="card-title-login">Autenticación de Seguridad</h2>
                <p class="card-subtitle-login">Ingrese sus credenciales de super administrador</p>
            </div>

            <form method="POST" action="<?php echo e(route('superadmin.login.post')); ?>" id="loginForm">
                <?php echo csrf_field(); ?>

                <div class="form-content-login">
                    
                    <div class="mb-4">
                        <label class="form-label-login">Correo Electrónico</label>
                        <div class="position-relative">
                            <div class="input-icon-login">
                                <i class="mdi mdi-email"></i>
                            </div>
                            <input 
                                type="email" 
                                name="email" 
                                id="email"
                                class="form-input-login"
                                value="<?php echo e(old('email')); ?>"
                                required 
                                autofocus
                                placeholder="superadmin@sistema.local"
                            >
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-login">Contraseña</label>
                        <div class="position-relative">
                            <div class="input-icon-login">
                                <i class="mdi mdi-lock"></i>
                            </div>
                            <input 
                                type="password" 
                                name="password" 
                                id="password"
                                class="form-input-login"
                                required
                                placeholder="••••••••"
                            >
                            <button type="button" onclick="togglePassword()" class="password-toggle-login">
                                <i class="mdi mdi-eye" id="icon_password"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login-submit">
                        <i class="mdi mdi-login"></i>
                        <span class="ms-2">Iniciar Sesión</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center mt-4">
            <a href="<?php echo e(url('/')); ?>" class="back-link-login">
                <i class="mdi mdi-arrow-left"></i>
                <span class="ms-1">Volver al sistema</span>
            </a>
        </div>
    </div>

    <!-- Modal Success -->
    <div id="successModal" class="modal-overlay-login">
        <div class="modal-container-login">
            <div class="modal-header-success-login">
                <div class="d-flex align-items-center">
                    <div class="modal-icon-box-login">
                        <i class="mdi mdi-check-circle modal-icon-success"></i>
                    </div>
                    <h3 class="modal-title-login ms-3">¡Éxito!</h3>
                </div>
            </div>
            <div class="modal-body-login">
                <p class="modal-text-login" id="successMessage"></p>
            </div>
            <div class="modal-footer-login">
                <button onclick="closeSuccessModal()" class="btn-modal-confirm-login w-100">
                    Entendido
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Error -->
    <div id="errorModal" class="modal-overlay-login">
        <div class="modal-container-login">
            <div class="modal-header-error-login">
                <div class="d-flex align-items-center">
                    <div class="modal-icon-box-login">
                        <i class="mdi mdi-alert-circle modal-icon-error"></i>
                    </div>
                    <h3 class="modal-title-login ms-3">Error de Acceso</h3>
                </div>
            </div>
            <div class="modal-body-login">
                <p class="modal-text-login" id="errorMessage"></p>
            </div>
            <div class="modal-footer-login">
                <button onclick="closeErrorModal()" class="btn-modal-confirm-login w-100">
                    Entendido
                </button>
            </div>
        </div>
    </div>

    <style>
        /* Base */
        body.login-body {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
        }

        /* Logo Shield */
        .logo-shield {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.3);
            margin: 0 auto 24px;
            font-size: 40px;
        }

        /* Login Card */
        .login-card {
            background: white;
            border-radius: 24px;
            border: 1px solid rgba(226, 232, 240, 0.6);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header-login {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            padding: 32px;
            border-bottom: 1px solid #e2e8f0;
        }

        .card-title-login {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
            margin: 0;
        }

        .card-subtitle-login {
            font-size: 13px;
            color: #94a3b8;
            margin: 4px 0 0 0;
        }

        .form-content-login {
            padding: 32px;
        }

        /* Form Elements */
        .form-label-login {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input-login {
            width: 100%;
            padding: 14px 18px;
            padding-left: 50px;
            padding-right: 50px;
            background: #f1f5f9;
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            color: #0f172a;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-input-login:focus {
            outline: none;
            background: white;
            border-color: #0f172a;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
        }

        .input-icon-login {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            transition: color 0.2s;
            z-index: 1;
        }

        .form-input-login:focus ~ .input-icon-login {
            color: #0f172a;
        }

        /* Password Toggle */
        .password-toggle-login {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s;
            z-index: 2;
        }

        .password-toggle-login:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        /* Login Button */
        .btn-login-submit {
            width: 100%;
            padding: 16px;
            background: #0f172a;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 8px;
        }

        .btn-login-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.2);
            background: #1e293b;
        }

        .btn-login-submit:active {
            transform: translateY(0);
        }

        /* Back Link */
        .back-link-login {
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: color 0.2s;
        }

        .back-link-login:hover {
            color: #0f172a;
        }

        /* Modales */
        .modal-overlay-login {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 16px;
            animation: fadeIn 0.2s ease-out;
        }

        .modal-overlay-login.active {
            display: flex;
        }

        .modal-container-login {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.3s ease-out;
        }

        .modal-header-success-login {
            background: #d1fae5;
            padding: 24px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .modal-header-error-login {
            background: #fee2e2;
            padding: 24px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .modal-icon-box-login {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-icon-success {
            color: #059669;
            font-size: 24px;
        }

        .modal-icon-error {
            color: #dc2626;
            font-size: 24px;
        }

        .modal-title-login {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .modal-body-login {
            padding: 24px;
        }

        .modal-text-login {
            color: #64748b;
            font-size: 15px;
            margin: 0;
        }

        .modal-footer-login {
            display: flex;
            gap: 12px;
            padding: 24px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .btn-modal-confirm-login {
            flex: 1;
            padding: 12px 16px;
            background: #0f172a;
            color: white;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-modal-confirm-login:hover {
            background: #1e293b;
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.2);
        }

        /* Animations */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 576px) {
            .logo-shield {
                width: 64px;
                height: 64px;
                font-size: 32px;
            }

            .card-header-login,
            .form-content-login {
                padding: 24px;
            }

            .modal-container-login {
                margin: 16px;
            }
        }
    </style>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/opten-helpers.js"></script>
    <script src="/assets/js/dynamic-pie-chart.js"></script>
    <script src="/assets/js/moment.min.js"></script>
    <script src="/assets/js/main.js"></script>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const field = document.getElementById('password');
            const icon = document.getElementById('icon_password');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('mdi-eye');
                icon.classList.add('mdi-eye-off');
            } else {
                field.type = 'password';
                icon.classList.remove('mdi-eye-off');
                icon.classList.add('mdi-eye');
            }
        }

        // Check for success/error messages on page load - FIX #1: XSS Protection
        <?php if($errors->any()): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showErrorModal(<?php echo json_encode($errors->first()); ?>);
            });
        <?php endif; ?>

        <?php if(session('success')): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showSuccessModal(<?php echo json_encode(session('success')); ?>);
            });
        <?php endif; ?>

        // Success Modal
        function showSuccessModal(message) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.add('active');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.remove('active');
        }

        // Error Modal
        function showErrorModal(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').classList.add('active');
        }

        function closeErrorModal() {
            document.getElementById('errorModal').classList.remove('active');
        }

        // Cerrar modales con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSuccessModal();
                closeErrorModal();
            }
        });

        // Cerrar modales al hacer click fuera
        document.getElementById('successModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSuccessModal();
            }
        });

        document.getElementById('errorModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeErrorModal();
            }
        });
    </script>

</body>
</html><?php /**PATH C:\OptenAdvance\app\www\resources\views\superadmin\login.blade.php ENDPATH**/ ?>