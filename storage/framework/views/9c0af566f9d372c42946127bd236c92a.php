<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Recuperación de Contraseñas</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/assets/css/lineicons.css" />
    <link rel="stylesheet" href="/assets/css/materialdesignicons.min.css" />
    <link rel="stylesheet" href="/assets/css/fullcalendar.css" />
    <link rel="stylesheet" href="/assets/css/main.css" />    
    <style>
        /* Reset y Base */
        body { 
            background-color: #f8fafc; 
            color: #1e293b;
        }

        /* Utilities - Gaps */
        .gap-3 { gap: 1rem; }
        .gap-4 { gap: 1.5rem; }

        /* Navbar Custom */
        .navbar-custom {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .logo-box {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.15);
        }

        .navbar-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.3px;
            margin: 0;
        }

        .navbar-subtitle {
            font-size: 10px;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .session-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }

        .session-email {
            font-size: 13px;
            color: #0f172a;
            font-weight: 700;
        }

        /* Page Title */
        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -1px;
            margin-bottom: 12px;
        }

        /* Card Principal */
        .card-main {
            background: white;
            padding: 40px;
            border-radius: 24px;
            border: 1px solid rgba(226, 232, 240, 0.6);
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.04);
        }

        .form-sections {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        /* Inputs y Selects */
        .input-group-custom {
            margin-bottom: 0;
        }

        .form-label-custom {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control-custom, .form-select-custom {
            width: 100%;
            padding: 14px 18px;
            padding-right: 45px;
            background: #f1f5f9;
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            color: #0f172a;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-select-custom {
            padding-right: 18px;
            appearance: none;
            background-image: none;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            outline: none;
            background: white;
            border-color: #0f172a;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
        }

        .select-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            pointer-events: none;
        }

        .password-toggle {
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
        }

        .password-toggle:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        /* Botones */
        .btn-primary {
            padding: 16px;
            background: #0f172a;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.2);
            background: #1e293b;
        }

        .btn-secondary {
            padding: 16px;
            background: transparent;
            color: #64748b;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fee2e2;
            color: #991b1b;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-logout:hover {
            background: #fecaca;
        }

        /* Sidebar Usuarios */
        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding: 0 5px;
        }

        .sidebar-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .badge-count {
            font-size: 11px;
            font-weight: 800;
            background: #e2e8f0;
            color: #475569;
            padding: 4px 10px;
            border-radius: 8px;
        }

        .user-list-container {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .user-card {
            background: white;
            padding: 14px;
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.5);
            display: flex;
            align-items: center;
            gap: 14px;
            transition: 0.2s;
        }

        .user-card:hover {
            border-color: #cbd5e1;
            transform: translateX(4px);
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            background: #f1f5f9;
            color: #0f172a;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
        }

        .user-name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .user-email {
            font-size: 12px;
            color: #94a3b8;
            margin: 0;
        }

        .user-role-badge {
            font-size: 9px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .empty-icon {
            font-size: 40px;
            color: #cbd5e1;
        }

        .empty-text {
            color: #94a3b8;
            font-size: 14px;
            margin-top: 10px;
        }

        /* Modales */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 16px;
            animation: fadeIn 0.2s ease-out;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.3s ease-out;
        }

        .modal-header-default {
            background: #dbeafe;
            padding: 24px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .modal-header-success {
            background: #d1fae5;
            padding: 24px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .modal-header-error {
            background: #fee2e2;
            padding: 24px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .modal-header-warning {
            background: #fef0db;
            padding: 24px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .modal-icon-box {
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

        .modal-icon-warning {
            color: #d97706;
            font-size: 24px;
        }

        .modal-icon-blue {
            color: #2563eb;
            font-size: 24px;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .modal-body-custom {
            padding: 24px;
        }

        .modal-text {
            color: #64748b;
            font-size: 15px;
            margin: 0;
        }

        .modal-text-secondary {
            color: #64748b;
            margin: 0;
        }

        .modal-text-small {
            font-size: 13px;
            color: #94a3b8;
            margin: 0;
        }

        .info-box {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 16px;
        }

        .info-label {
            font-size: 12px;
            color: #94a3b8;
        }

        .info-value {
            font-weight: 700;
            color: #0f172a;
        }

        .modal-footer-custom {
            display: flex;
            gap: 12px;
            padding: 24px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .btn-modal-cancel {
            flex: 1;
            padding: 12px 16px;
            background: white;
            border: 2px solid #e2e8f0;
            color: #64748b;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-modal-cancel:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .btn-modal-confirm {
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

        .btn-modal-confirm:hover {
            background: #1e293b;
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.2);
        }

        /* Animaciones */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }
    </style>
</head>
<body style="background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); min-height: 100vh;">

    <!-- Header -->
    <nav class="navbar-custom">
        <div class="container-fluid px-4 py-3">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="d-flex align-items-center">
                    <div class="logo-box">
                        <i class="mdi mdi-shield-check text-white"></i>
                    </div>
                    <div class="ms-3">
                        <h1 class="navbar-title">Panel Central</h1>
                        <span class="navbar-subtitle">Super Admin</span>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center me-4">
                        <div class="text-end">
                            <p class="session-label mb-0">Sesión activa</p>
                            <p class="session-email mb-0"><?php echo e(Auth::user()->email); ?></p>
                        </div>
                    </div>
                    <form method="POST" action="<?php echo e(route('superadmin.logout')); ?>" id="logoutForm">
                        <?php echo csrf_field(); ?>
                        <button type="button" onclick="showLogoutModal()" class="btn-logout">
                            <span>Salir</span>
                            <i class="mdi mdi-logout-variant"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-xl py-5">
        <div class="row g-4">
            
            <!-- Formulario Principal -->
            <div class="col-lg-7">
                <header class="mb-5">
                    <h3 class="page-title">Gestión de Seguridad</h3>
                </header>

                <div class="card-main">
                    <form method="POST" action="<?php echo e(route('superadmin.reset.password')); ?>" id="resetForm">
                        <?php echo csrf_field(); ?>
                        <div class="form-sections">
                            
                            <div class="input-group-custom">
                                <label class="form-label-custom">Usuario a modificar</label>
                                <div class="position-relative">
                                    <select name="user_id" id="user_id" class="form-select-custom" required>
                                        <option value="" disabled selected>Busca un usuario...</option>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($user->id); ?>" data-email="<?php echo e($user->email); ?>" data-name="<?php echo e($user->name); ?>"><?php echo e($user->name); ?> — <?php echo e($user->email); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <i class="mdi mdi-unfold-more-horizontal select-icon"></i>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="input-group-custom">
                                        <label class="form-label-custom">Nueva Contraseña</label>
                                        <div class="position-relative">
                                            <input type="password" name="new_password" id="new_password" class="form-control-custom" placeholder="••••••••" required>
                                            <button type="button" onclick="togglePassword('new_password')" class="password-toggle">
                                                <i class="mdi mdi-eye" id="icon_new_password"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-custom">
                                        <label class="form-label-custom">Confirmar</label>
                                        <div class="position-relative">
                                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control-custom" placeholder="••••••••" required>
                                            <button type="button" onclick="togglePassword('new_password_confirmation')" class="password-toggle">
                                                <i class="mdi mdi-eye" id="icon_new_password_confirmation"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 pt-3">
                                <button type="button" onclick="document.getElementById('resetForm').reset()" class="btn-secondary flex-fill">
                                    Cancelar
                                </button>
                                <button type="button" onclick="showResetModal()" class="btn-primary flex-grow-1" style="flex: 2;">
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar Usuarios -->
            <div class="col-lg-5">
                <div class="sidebar-header">
                    <h3 class="sidebar-title">Directorio</h3>
                    <span class="badge-count"><?php echo e(count($users)); ?> Usuarios</span>
                </div>
                
                <div class="user-list-container">
                    <div class="d-flex flex-column gap-3">
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="user-card">
                                <div class="user-avatar">
                                    <?php echo e(substr($user->name, 0, 1)); ?>

                                </div>
                                <div class="flex-grow-1">
                                    <p class="user-name"><?php echo e($user->name); ?></p>
                                    <p class="user-email"><?php echo e($user->email); ?></p>
                                </div>
                                <span class="user-role-badge"><?php echo e($user->role); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-5">
                                <i class="mdi mdi-account-off-outline empty-icon"></i>
                                <p class="empty-text">No se encontraron registros.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Success -->
    <div id="successModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header-success">
                <div class="d-flex align-items-center">
                    <div class="modal-icon-box">
                        <i class="mdi mdi-check-circle modal-icon-success"></i>
                    </div>
                    <h3 class="modal-title ms-3">¡Éxito!</h3>
                </div>
            </div>
            <div class="modal-body-custom">
                <p class="modal-text" id="successMessage"></p>
            </div>
            <div class="modal-footer-custom">
                <button onclick="closeSuccessModal()" class="btn-modal-confirm w-100">
                    Entendido
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Error -->
    <div id="errorModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header-error">
                <div class="d-flex align-items-center">
                    <div class="modal-icon-box">
                        <i class="mdi mdi-alert-circle modal-icon-error"></i>
                    </div>
                    <h3 class="modal-title ms-3">Error</h3>
                </div>
            </div>
            <div class="modal-body-custom">
                <p class="modal-text" id="errorMessage"></p>
            </div>
            <div class="modal-footer-custom">
                <button onclick="closeErrorModal()" class="btn-modal-confirm w-100">
                    Entendido
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Reset -->
    <div id="resetModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header-warning">
                <div class="d-flex align-items-center">
                    <div class="modal-icon-box">
                        <i class="mdi mdi-alert-circle modal-icon-warning"></i>
                    </div>
                    <h3 class="modal-title ms-3">Confirmar operación</h3>
                </div>
            </div>
            <div class="modal-body-custom">
                <p class="modal-text-secondary mb-2">Está a punto de resetear la contraseña de:</p>
                <div class="info-box mb-4">
                    <p class="info-label mb-1">Usuario</p>
                    <p class="info-value mb-0" id="resetUserEmail">-</p>
                </div>
                <p class="modal-text-small">Esta acción quedará registrada en el sistema.</p>
            </div>
            <div class="modal-footer-custom">
                <button onclick="closeResetModal()" class="btn-modal-cancel">
                    Cancelar
                </button>
                <button onclick="confirmReset()" class="btn-modal-confirm">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Logout -->
    <div id="logoutModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header-default">
                <div class="d-flex align-items-center">
                    <div class="modal-icon-box">
                        <i class="mdi mdi-logout modal-icon-blue"></i>
                    </div>
                    <h3 class="modal-title ms-3">Finalizar sesión</h3>
                </div>
            </div>
            <div class="modal-body-custom">
                <p class="modal-text-secondary mb-2">¿Desea cerrar la sesión de Super Admin?</p>
                <p class="modal-text-small">Deberá ingresar las credenciales nuevamente para volver a acceder.</p>
            </div>
            <div class="modal-footer-custom">
                <button onclick="closeLogoutModal()" class="btn-modal-cancel">
                    Cancelar
                </button>
                <button onclick="confirmLogout()" class="btn-modal-confirm">
                    Cerrar sesión
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Warning Exit -->
    <div id="warningModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header-warning">
                <div class="d-flex align-items-center">
                    <div class="modal-icon-box">
                        <i class="mdi mdi-shield-alert modal-icon-blue"></i>
                    </div>
                    <h3 class="modal-title ms-3">Alerta de Seguridad</h3>
                </div>
            </div>
            <div class="modal-body-custom">
                <p class="modal-text-secondary mb-3">Por razones de seguridad, debe cerrar su sesión antes de salir de esta página.</p>
                <p class="modal-text-small">Las sesiones activas sin cerrar representan un riesgo de seguridad.</p>
            </div>
            <div class="modal-footer-custom">
                <button onclick="closeWarningModal()" class="btn-modal-cancel">
                    Continuar aquí
                </button>
                <button onclick="logoutAndExit()" class="btn-modal-confirm">
                    Cerrar sesión y salir
                </button>
            </div>
        </div>
    </div>

    

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/opten-helpers.js"></script>
    <script src="/assets/js/dynamic-pie-chart.js"></script>
    <script src="/assets/js/moment.min.js"></script>
    <script src="/assets/js/main.js"></script>

    <script>
        let allowExit = false;
        let sessionLoggedOut = false;

        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById('icon_' + fieldId);
            
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

        // Check for success/error messages on page load
        <?php if(session('success')): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showSuccessModal(<?php echo json_encode(session('success')); ?>);
            });
        <?php endif; ?>

        <?php if(session('error')): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showErrorModal(<?php echo json_encode(session('error')); ?>);
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

        // Modal Reset
        function showResetModal() {
            const userSelect = document.getElementById('user_id');
            const password = document.getElementById('new_password').value;
            const passwordConfirm = document.getElementById('new_password_confirmation').value;
            
            if (!userSelect.value) {
                showErrorModal('Por favor seleccione un usuario');
                return;
            }
            if (!password) {
                showErrorModal('Por favor ingrese una contraseña');
                return;
            }
            if (password !== passwordConfirm) {
                showErrorModal('Las contraseñas no coinciden');
                return;
            }
            
            const selectedOption = userSelect.options[userSelect.selectedIndex];
            document.getElementById('resetUserEmail').textContent = selectedOption.getAttribute('data-email');
            document.getElementById('resetModal').classList.add('active');
        }

        function closeResetModal() {
            document.getElementById('resetModal').classList.remove('active');
        }

        function confirmReset() {
            allowExit = true;
            document.getElementById('resetForm').submit();
        }

        // Modal Logout
        function showLogoutModal() {
            document.getElementById('logoutModal').classList.add('active');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.remove('active');
        }

        function confirmLogout() {
            allowExit = true;
            sessionLoggedOut = true;
            document.getElementById('logoutForm').submit();
        }

        // Warning Modal
        function showWarningModal() {
            document.getElementById('warningModal').classList.add('active');
        }

        function closeWarningModal() {
            document.getElementById('warningModal').classList.remove('active');
        }

        function logoutAndExit() {
            allowExit = true;
            sessionLoggedOut = true;
            document.getElementById('logoutForm').submit();
        }

        // Prevent closing browser/tab without logout
        window.addEventListener('beforeunload', function(e) {
            if (!allowExit && !sessionLoggedOut) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            }
        });

        // Prevent navigation without logout
        window.addEventListener('popstate', function(e) {
            if (!sessionLoggedOut) {
                e.preventDefault();
                showWarningModal();
                history.pushState(null, '', location.href);
            }
        });

        // Initialize history state
        history.pushState(null, '', location.href);

        // Cerrar modales con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeResetModal();
                closeLogoutModal();
                closeSuccessModal();
                closeErrorModal();
                closeWarningModal();
            }
        });

        // Cerrar modales al hacer click fuera
        document.getElementById('resetModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeResetModal();
            }
        });

        document.getElementById('logoutModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLogoutModal();
            }
        });

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

        document.getElementById('warningModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeWarningModal();
            }
        });
    </script>

</body>
</html><?php /**PATH C:\OptenAdvance\app\www\resources\views\superadmin\recovery.blade.php ENDPATH**/ ?>