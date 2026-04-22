<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Bienvenido</title>
    <link rel="icon" type="image/png" href="/assets/images/logo/icon.png" />
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
<link rel="stylesheet" href="/assets/css/lineicons.css" />
<link rel="stylesheet" href="/assets/css/main.css" />

<style>
    /* Usamos una fuente más limpia y moderna */
    body { background: #f8fafc; font-family: 'Inter', 'Plus Jakarta Sans', sans-serif; }
    
    .onboarding-card {
        border: none;
        border-radius: 32px; /* Más redondeado para suavizar */
        box-shadow: 0 20px 50px rgba(0, 76, 255, 0.04);
        overflow: hidden;
        background: #fff;
        max-width: 850px; /* Un pelín más ancho para que los campos dobles respiren */
        margin: 80px auto;
    }

    .onboarding-header {
        padding: 60px 60px 30px 60px; /* Mucho más aire arriba y a los lados */
        background: #fff;
    }

    .onboarding-header h1 {
        font-weight: 850;
        color: #000000;
        font-size: 32px;
        letter-spacing: -1px;
        margin-bottom: 15px;
    }

    .onboarding-header p.lead {
        color: #64748b;
        font-size: 16px;
        line-height: 1.8;
        max-width: 600px; /* Evitamos que el texto se estire demasiado */
    }

    /* Sección del Formulario con más respiro */
    .form-section {
        padding: 20px 60px 60px 60px;
    }

    /* Separación entre bloques de campos */
    .form-group-row {
        margin-bottom: 25px; /* Espacio extra entre filas */
    }

    .form-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #94a3b8;
        margin-bottom: 12px; /* Más espacio entre etiqueta e input */
        display: block;
    }

    .form-control-custom {
        padding: 16px 20px; /* Input más alto y cómodo */
        border-radius: 16px;
        border: 2px solid #f1f5f9;
        background: #f8fafc;
        font-size: 15px;
        color: #1e293b;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .form-control-custom:focus {
        border-color: #2e6cff;
        background: #fff;
        box-shadow: 0 10px 20px rgba(31, 98, 255, 0.08);
        outline: none;
    }

    /* Botones más grandes y separados */
    .btn-main {
        padding: 18px 35px; /* Botones más robustos */
        border-radius: 18px;
        font-weight: 700;
        font-size: 16px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-primary-custom {
        background: #2563eb; /* Color más sobrio y elegante */
        border: none;
        color: #fff;
    }

    .btn-primary-custom:hover {
        background: #1352ff;
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0, 68, 255, 0.1);
    }

    .btn-skip {
        background: transparent;
        border: 2px solid #f1f5f9;
        color: #94a3b8;
    }

    .btn-skip:hover {
        background: #f8fafc;
        color: #1e293b;
        border-color: #e2e8f0;
    }

    /* Card de IVA con más aire interno */
    .iva-card {
        background: #f8fafc;
        padding: 25px 30px;
        border-radius: 20px;
        border: 2px solid #f1f5f9;
        margin-top: 10px;
    }

    .field-error { color: #f43f5e; font-size: 12px; margin-top: 8px; font-weight: 600; }
    
    /* Utility para espaciado */
    .spacer-y { margin-top: 40px; }
</style>

<div class="container">
    <div class="onboarding-card">
        <div class="onboarding-header text-center text-sm-start">
            <div class="d-flex align-items-center gap-3 mb-3 justify-content-center justify-content-sm-start">
                <div style="width: 45px; height: 45px; background: #e0e7ff; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="lni lni-sprout text-primary" style="font-size: 24px;"></i>
                </div>
                <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill" style="font-size: 11px; font-weight: 700;">CONFIGURACIÓN INICIAL</span>
            </div>
            <h1>¡Bienvenido! Vamos a preparar tu negocio</h1>
            <p class="lead">Completa los datos básicos de tu empresa. Esto aparecerá en tus facturas y reportes.</p>
            
            <div class="mt-4 d-flex gap-2 justify-content-center justify-content-sm-start">
                <button id="btn-show" class="btn btn-main btn-primary-custom">
                    <i class="lni lni-cog m-1"></i> Configurar ahora
                </button>
                <a href="<?php echo e(route('ventas.index')); ?>" class="btn btn-main btn-skip">
                    Saltar por ahora
                </a>
            </div>
        </div>

        <div id="form-wrap" class="form-section" style="display:none; margin-top: 20px;">
            <hr style="border-top: 2px solid #f1f5f9; margin-bottom: 35px;">
            
            <form id="onboard-form" class="row g-4" data-endpoint="<?php echo e(route('empresa.update')); ?>" novalidate>
                <?php echo csrf_field(); ?>
                <input type="hidden" name="from_onboarding" value="1">
                
                <div class="col-12">
                    <label class="form-label">Nombre de la Empresa <span class="text-danger">*</span></label>
                    <input id="nombre" name="nombre" type="text" class="form-control-custom w-100" placeholder="Ej: Mi Tienda Digital S.A.S.">
                    <div class="field-error" id="error-nombre"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">NIT / RUT</label>
                    <input id="nit" name="nit" type="text" class="form-control-custom w-100" placeholder="900.123.456-1">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Moneda Base</label>
                    <input id="moneda" name="moneda" type="text" class="form-control-custom w-100" placeholder="Moneda (ej: COP, USD)">
                </div>

                <div class="col-12">
                    <label class="form-label">Dirección Fiscal</label>
                    <input id="direccion" name="direccion" type="text" class="form-control-custom w-100" placeholder="Calle 100 #15-20, Edificio Pro, Oficina 101">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Teléfono de Contacto</label>
                    <input id="telefono" name="telefono" type="text" class="form-control-custom w-100" placeholder="+57 300 000 0000">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Correo Electrónico</label>
                    <input id="email" name="email" type="email" class="form-control-custom w-100" placeholder="admin@empresa.com">
                </div>

                <!--<div class="col-12">
                    <div class="iva-card">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="cobra_iva" name="cobra_iva" value="1" style="width: 40px; height: 20px; cursor: pointer;">
                            <label class="form-check-label ms-2" for="cobra_iva" style="font-weight: 700; color: #334155; cursor: pointer;">
                                ¿Esta empresa es responsable de IVA?
                            </label>
                        </div>
                        <p class="text-muted mb-0 mt-2" style="font-size: 12px; margin-left: 50px;">
                            Si activas esta opción, podrás configurar las tarifas por producto.
                        </p>
                    </div>
                </div> -->

                <div class="col-12 mt-5 d-flex justify-content-end gap-3">
    <a href="<?php echo e(route('productos.index')); ?>" class="btn btn-main btn-skip">
        Saltar por ahora
    </a>

    <button id="btn-submit" type="submit" class="btn btn-main btn-primary-custom px-5">
        Guardar y Comenzar <i class="lni lni-arrow-right ms-2"></i>
    </button>
</div>

            </form>
        </div>

        <div class="p-4 text-center" style="background: #f8fafc; border-top: 1px solid #f1f5f9;">
            <p class="mb-0" style="font-size: 13px; color: #94a3b8;">
                <i class="lni lni-lock-alt me-1"></i> Tus datos están seguros y puedes editarlos en cualquier momento.
            </p>
        </div>
    </div>
</div>

<script src="/assets/js/bootstrap.bundle.min.js"></script>

<script>
(function(){
    var btn = document.getElementById('btn-show');
    var wrap = document.getElementById('form-wrap');
    var form = document.getElementById('onboard-form');
    var submitBtn = document.getElementById('btn-submit');
    var headerCard = document.querySelector('.onboarding-header'); // CUADRO SUPERIOR

    if (!btn || !wrap || !form || !submitBtn || !headerCard) return;

    // Toggle formulario: mostrar/ocultar y ocultar el cuadro superior
    btn.addEventListener('click', function(e){
        e.preventDefault();
        if (wrap.style.display === 'none' || wrap.style.display === '') {
            wrap.style.display = 'block';
            headerCard.style.display = 'none';
        } else {
            wrap.style.display = 'none';
            headerCard.style.display = 'flex';
        }
    });

    // Estado de envío
    var isSubmitting = false;

    // Bloquear submit nativo y Enter
    form.noValidate = true;
    form.addEventListener('keydown', function(e){ if(e.key === 'Enter') e.preventDefault(); });

    form.addEventListener('submit', function(e){
        e.preventDefault();

        var nombre = document.getElementById('nombre').value.trim();

        // Validación mínima
        if (!nombre) {
            showFieldError('nombre', 'El nombre de la empresa es obligatorio.');
            return false;
        }

        if (isSubmitting) return false;
        isSubmitting = true;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Guardando…';

        clearFieldErrors();

        var formData = new FormData(form);
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
            if (response.ok) {
                window.location.href = response.redirected ? response.url : '<?php echo e(route("productos.index")); ?>';
                return null;
            }
            if (response.status === 422) {
                return response.json().then(function(data){
                    showValidationErrors(data.errors);
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Guardar Configuración y Empezar';
                    isSubmitting = false;
                });
            }
            if (response.status === 419) {
                showError('La sesión expiró. Recarga la página e inténtalo de nuevo.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Guardar Configuración y Empezar';
                isSubmitting = false;
                return null;
            }
            showError('Ocurrió un error inesperado. Intenta nuevamente.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Guardar Configuración y Empezar';
            isSubmitting = false;
            return null;
        })
        .catch(function(error){
            console.error('Error:', error);
            showError('Ocurrió un error inesperado. Intenta nuevamente.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Guardar Configuración y Empezar';
            isSubmitting = false;
        });
    });

    // Funciones auxiliares
    function clearFieldErrors() {
        var errorBanner = document.querySelector('.error-banner');
        if (errorBanner) errorBanner.remove();

        var inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => input.classList.remove('error-field'));

        var fieldErrors = form.querySelectorAll('.field-error');
        fieldErrors.forEach(error => { error.classList.remove('show'); error.textContent = ''; });
    }

    function showFieldError(fieldId, message) {
        var fieldInput = document.getElementById(fieldId);
        if (fieldInput) {
            fieldInput.classList.add('error-field');
            var errorDiv = fieldInput.nextElementSibling;
            if (errorDiv && errorDiv.classList.contains('field-error')) {
                errorDiv.classList.add('show');
                errorDiv.textContent = message;
            }
        }
    }

    function showValidationErrors(errors) {
        var errorBanner = document.createElement('div');
        errorBanner.className = 'error-banner';
        errorBanner.innerHTML = '<strong>Por favor, revisa los errores abajo:</strong><ul style="margin:6px 0 0;padding-left:20px"></ul>';
        var ul = errorBanner.querySelector('ul');

        for (var field in errors) {
            if (errors.hasOwnProperty(field)) {
                var messages = errors[field];
                var fieldInput = document.getElementById(field);
                if (fieldInput) fieldInput.classList.add('error-field');

                messages.forEach(msg => {
                    // Mostrar error específico debajo del campo
                    if (fieldInput) {
                        var errorDiv = fieldInput.nextElementSibling;
                        if (errorDiv && errorDiv.classList.contains('field-error')) {
                            errorDiv.classList.add('show');
                            errorDiv.textContent = msg;
                        }
                    }
                    // Agregar al banner general
                    var li = document.createElement('li');
                    li.textContent = msg;
                    ul.appendChild(li);
                });
            }
        }

        form.insertBefore(errorBanner, form.firstChild);
    }

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
<?php /**PATH C:\optenadvance\app\www\resources\views/onboarding.blade.php ENDPATH**/ ?>