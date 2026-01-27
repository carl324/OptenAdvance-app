<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Bienvenido</title>
    <style>
        /* Estilos mínimos y auto-contenidos */
        :root{--bg:#f6f7fb;--card:#fff;--accent:#1f5fbf;--muted:#6b7280}
        html,body{height:100%;margin:0;font-family:Inter,Segoe UI,Arial,Helvetica,sans-serif;background:var(--bg);color:#111}
        .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        .card{width:100%;max-width:780px;background:var(--card);box-shadow:0 6px 18px rgba(16,24,40,.06);border-radius:10px;padding:28px}
        h1{margin:0 0 6px;font-size:20px}
        p.lead{margin:0 0 18px;color:var(--muted)}
        .actions{display:flex;gap:10px;flex-wrap:wrap}
        button.primary{background:var(--accent);color:#fff;border:none;padding:10px 16px;border-radius:8px;font-weight:600;cursor:pointer;transition:opacity 0.2s}
        button.primary:disabled{opacity:0.6;cursor:not-allowed}
        button.secondary{background:transparent;border:1px solid #e6e9ef;padding:10px 14px;border-radius:8px;color:#374151;cursor:pointer}
        .error-banner{background:#fef2f2;border:1px solid #fca5a5;border-radius:6px;padding:12px;margin-bottom:16px;color:#991b1b;font-size:13px}
        .error-field{border-color:#dc2626 !important}
        .field-error{color:#dc2626;font-size:12px;margin-top:4px;display:none}
        .field-error.show{display:block}
        .field-group{display:flex;flex-direction:column}
        .checkbox-label{display:flex;align-items:center;gap:8px;cursor:pointer;padding:6px;margin:-6px;user-select:none}
        form{margin-top:14px;display:grid;grid-template-columns:1fr 1fr;gap:12px}
        label{font-size:13px;font-weight:600;margin-bottom:6px;display:block}
        input[type="text"],input[type="email"],select,textarea{width:100%;padding:10px;border:1px solid #e6e9ef;border-radius:8px}
        .full{grid-column:1/-1}
        .note{font-size:13px;color:var(--muted)}
        @media (max-width:640px){form{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Bienvenido, antes de comenzar a vender</h1>
            <p class="lead">Configura los datos de tu empresa para emitir facturas correctamente. Puedes hacerlo ahora o saltar y empezar a usar el sistema.</p>

            <div class="actions" style="margin-bottom:12px">
                <button id="btn-show" class="primary">Configurar ahora</button>
                <a href="{{ route('ventas.index') }}"><button class="secondary" type="button">Saltar por ahora</button></a>
            </div>

            <!-- Formulario inline (oculto por defecto). Envía al controlador existente EmpresaController::update -->
            <div id="form-wrap" style="display:none">
                <form id="onboard-form" data-endpoint="{{ route('empresa.update') }}" novalidate>
                    @csrf
                    
                    @if($errors->any())
                    <div class="error-banner">
                        <strong>Por favor, revisa los errores abajo:</strong>
                        <ul style="margin:6px 0 0;padding-left:20px">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="full field-group">
                        <label for="nombre">Nombre <span style="color:#dc2626">*</span></label>
                        <input id="nombre" name="nombre" type="text" class="{{ $errors->has('nombre') ? 'error-field' : '' }}" autocomplete="organization" placeholder="Ej: Mi Empresa S.A.S.">
                        @if($errors->has('nombre'))
                        <div class="field-error show">{{ $errors->first('nombre') }}</div>
                        @endif
                    </div>

                    <div class="field-group">
                        <label for="nit">NIT</label>
                        <input id="nit" name="nit" type="text" class="{{ $errors->has('nit') ? 'error-field' : '' }}" placeholder="Ej: 123456789">
                        @if($errors->has('nit'))
                        <div class="field-error show">{{ $errors->first('nit') }}</div>
                        @endif
                    </div>

                    <div class="field-group">
                        <label for="moneda">Moneda</label>
                        <input id="moneda" name="moneda" type="text" class="{{ $errors->has('moneda') ? 'error-field' : '' }}" value="COP" placeholder="COP">
                        @if($errors->has('moneda'))
                        <div class="field-error show">{{ $errors->first('moneda') }}</div>
                        @endif
                    </div>

                    <div class="full field-group">
                        <label for="direccion">Dirección</label>
                        <input id="direccion" name="direccion" type="text" class="{{ $errors->has('direccion') ? 'error-field' : '' }}" placeholder="Ej: Calle 123 #45-67, Apartado 1">
                        @if($errors->has('direccion'))
                        <div class="field-error show">{{ $errors->first('direccion') }}</div>
                        @endif
                    </div>

                    <div class="field-group">
                        <label for="telefono">Teléfono</label>
                        <input id="telefono" name="telefono" type="text" class="{{ $errors->has('telefono') ? 'error-field' : '' }}" placeholder="Ej: 3001234567">
                        @if($errors->has('telefono'))
                        <div class="field-error show">{{ $errors->first('telefono') }}</div>
                        @endif
                    </div>

                    <div class="field-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="text" inputmode="email" autocomplete="email" class="{{ $errors->has('email') ? 'error-field' : '' }}" placeholder="contacto@miempresa.com">
                        @if($errors->has('email'))
                        <div class="field-error show">{{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    <div class="full field-group">
                        <label style="font-weight:600">¿La empresa cobra IVA?</label>
                        <label for="cobra_iva" class="checkbox-label">
                            <input id="cobra_iva" type="checkbox" name="cobra_iva" value="1">
                            <span>Sí, esta empresa cobra IVA</span>
                        </label>
                    </div>

                    <div style="display:flex;justify-content:flex-end;grid-column:1/-1;margin-top:8px">
                        <button id="btn-submit" type="button" class="primary">Guardar y comenzar</button>
                    </div>
                </form>
            </div>

            <p class="note" style="margin-top:12px">Puedes cambiar estos datos más adelante desde el panel de configuración de la empresa.</p>
        </div>
    </div>

    <script>
        // JS vanilla: onboarding con AJAX (fetch) + sin recarga de página
        (function(){
            var btn = document.getElementById('btn-show');
            var wrap = document.getElementById('form-wrap');
            var form = document.getElementById('onboard-form');
            var submitBtn = document.getElementById('btn-submit');
            
            if (!btn || !wrap || !form || !submitBtn) return;

            // Toggle formulario: mostrar/ocultar
            btn.addEventListener('click', function(e){
                e.preventDefault();
                if (wrap.style.display === 'none' || wrap.style.display === '') {
                    wrap.style.display = 'block';
                    btn.textContent = 'Ocultar formulario';
                } else {
                    wrap.style.display = 'none';
                    btn.textContent = 'Configurar ahora';
                }
            });

            // Estado de envío
            var isSubmitting = false;

            // Forzar que el navegador NO haga validación nativa y bloquear envíos nativos
            form.noValidate = true;
            form.addEventListener('keydown', function(e){ if (e.key === 'Enter') { e.preventDefault(); e.stopImmediatePropagation(); } });
            form.addEventListener('submit', function(e){
                e.preventDefault();
                e.stopImmediatePropagation();

                var nombre = document.getElementById('nombre').value.trim();
                
                // Validación cliente mínima
                if (!nombre) {
                    var nombreField = document.getElementById('nombre');
                    nombreField.classList.add('error-field');
                    var existingError = nombreField.nextElementSibling;
                    if (existingError && existingError.classList.contains('field-error')) {
                        existingError.classList.add('show');
                        existingError.textContent = 'El nombre de la empresa es obligatorio.';
                    } else {
                        var errorDiv = document.createElement('div');
                        errorDiv.className = 'field-error show';
                        errorDiv.textContent = 'El nombre de la empresa es obligatorio.';
                        nombreField.parentNode.insertBefore(errorDiv, nombreField.nextSibling);
                    }
                    return false;
                }

                // Prevenir doble submit
                if (isSubmitting) {
                    return false;
                }

                isSubmitting = true;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Guardando…';

                // Limpiar errores previos
                clearFieldErrors();

                // Preparar FormData con los datos del formulario
                var formData = new FormData(form);

                // Enviar con fetch al endpoint controlado (sin action/method nativos)
                var endpoint = form.dataset.endpoint;
                fetch(endpoint, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function(response){
                    // Manejar respuesta 200 (éxito, posible redirección)
                    if (response.ok) {
                        // El servidor puede redirigir internamente o devolver una respuesta
                        // Si hay Location header, el navegador sigue automáticamente
                        if (response.redirected) {
                            window.location.href = response.url;
                        } else {
                            // Si es 200 sin redirección, ir a ventas.index
                            window.location.href = '{{ route("ventas.index") }}';
                        }
                        return null;
                    }

                    // Manejar respuesta 422 (validación fallida)
                    if (response.status === 422) {
                        return response.json().then(function(data){
                            showValidationErrors(data.errors);
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Guardar y comenzar';
                            isSubmitting = false;
                        });
                    }

                    // Manejar respuesta 419 (CSRF token expirado)
                    if (response.status === 419) {
                        showError('La sesión expiró. Recarga la página e inténtalo de nuevo.');
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Guardar y comenzar';
                        isSubmitting = false;
                        return null;
                    }

                    // Manejar otros errores (500, etc.)
                    showError('Ocurrió un error inesperado. Intenta nuevamente.');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Guardar y comenzar';
                    isSubmitting = false;
                    return null;
                })
                .catch(function(error){
                    // Error de red o CORS
                    console.error('Error:', error);
                    showError('Ocurrió un error inesperado. Intenta nuevamente.');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Guardar y comenzar';
                    isSubmitting = false;
                });
            });

            // Limpiar errores previos de la vista
            function clearFieldErrors() {
                // Remover banner de errores
                var errorBanner = document.querySelector('.error-banner');
                if (errorBanner) {
                    errorBanner.remove();
                }

                // Remover estilos de error de todos los campos
                var inputs = form.querySelectorAll('input');
                inputs.forEach(function(input){
                    input.classList.remove('error-field');
                });

                // Remover mensajes de error de los campos
                var fieldErrors = form.querySelectorAll('.field-error');
                fieldErrors.forEach(function(error){
                    error.classList.remove('show');
                    error.textContent = '';
                });
            }

            // Mostrar errores de validación (respuesta 422)
            function showValidationErrors(errors) {
                // Crear banner de errores general
                var errorBanner = document.createElement('div');
                errorBanner.className = 'error-banner';
                errorBanner.innerHTML = '<strong>Por favor, revisa los errores abajo:</strong><ul style="margin:6px 0 0;padding-left:20px"></ul>';
                var ul = errorBanner.querySelector('ul');

                // Iterar sobre cada campo con error
                for (var field in errors) {
                    if (errors.hasOwnProperty(field)) {
                        var messages = errors[field];
                        var fieldInput = document.getElementById(field);

                        // Resaltar el campo
                        if (fieldInput) {
                            fieldInput.classList.add('error-field');

                            // Mostrar error específico debajo del campo
                            var errorDiv = fieldInput.nextElementSibling;
                            if (errorDiv && errorDiv.classList.contains('field-error')) {
                                errorDiv.classList.add('show');
                                errorDiv.textContent = messages[0] || 'Este campo es inválido.';
                            }
                        }

                        // Agregar al banner general
                        if (Array.isArray(messages)) {
                            messages.forEach(function(msg){
                                var li = document.createElement('li');
                                li.textContent = msg;
                                ul.appendChild(li);
                            });
                        }
                    }
                }

                // Insertar banner al inicio del formulario
                form.insertBefore(errorBanner, form.firstChild);
            }

            // Mostrar error genérico
            function showError(message) {
                var errorBanner = document.createElement('div');
                errorBanner.className = 'error-banner';
                errorBanner.textContent = message;
                form.insertBefore(errorBanner, form.firstChild);
            }
        })();
    </script>
</body>
</html>
