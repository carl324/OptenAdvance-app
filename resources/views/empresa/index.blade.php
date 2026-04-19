@extends('layouts.app')

@section('title','Empresa')

@section('content')



    @php
        // Flag: SOLO si existe al menos un producto ACTIVO con iva > 0
        // Nota: se calcula en la vista usando la colección $productos.
        // Si $productos no viene inyectado en esta vista, se usa un fallback local
        // (sin modificar backend) para mantener el comportamiento.
        if (!isset($productos)) {
            $productos = \App\Models\Producto::select('activo', 'iva')->get();
        }

        $existenProductosConIVA = false;
        foreach ($productos as $p) {
            $activo = (bool)($p->activo ?? false);
            $ivaVal = is_numeric($p->iva ?? null) ? (float)$p->iva : 0;
            if ($activo && $ivaVal > 0) {
                $existenProductosConIVA = true;
                break;
            }
        }
    @endphp

    <div class="wrap">
        <section class="tab-components">
            <div class="container-fluid">
                <!-- ========== title-wrapper start ========== -->
                <div class="title-wrapper pt-30">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            
                        </div>
                        <div class="col-md-6">
                            <div class="breadcrumb-wrapper"></div>
                        </div>
                    </div>
                </div>
                <!-- ========== title-wrapper end ========== -->

                <!-- ========== form-elements-wrapper start ========== -->
                <div class="form-elements-wrapper">
    <div class="row">
                        <!-- COLUMNA IZQUIERDA -->
                        <div class="col-lg-6">
                            <div class="card-style mb-30">
                            <div class="mb-30">
    <label class="text-dark mb-2 d-block">Logo de la empresa</label>
    
    <div style="display:flex;align-items:center;gap:20px;">
        
        <div id="logo-preview-wrapper" onclick="document.getElementById('logo-input').click()"
             style="width:100px;height:100px;border-radius:12px;border:1.5px dashed #cbd5e1;display:flex;align-items:center;justify-content:center;overflow:hidden;cursor:pointer;background:#fff;flex-shrink:0;">
            @if($empresa && $empresa->logo)
                <img id="logo-preview" src="{{ asset($empresa->logo) }}" style="width:100%;height:100%;object-fit:contain;">
            @else
                <div id="logo-placeholder" style="text-align:center;color:#94a3b8;">
                    <i class="lni lni-image" style="font-size:26px;display:block;margin-bottom:4px;"></i>
                    <span style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Logo</span>
                </div>
            @endif
        </div>

        <div>
            <p class="text-xs text-gray mb-2">Recomendado: 200×200px · Fondo blanco · JPG, PNG o WEBP · Máx 2MB</p>
            <button type="button" onclick="document.getElementById('logo-input').click()"
                    style="font-size:12px;font-weight:600;color:#3b82f6;background:none;border:1px solid #bfdbfe;border-radius:6px;padding:5px 14px;cursor:pointer;">
                <i class="lni lni-upload" style="font-size:11px;"></i> Subir imagen
            </button>
            <div id="logo-error" class="text-danger small mt-1" style="display:none;"></div>
            <div id="logo-success" class="text-success small mt-1" style="display:none;"></div>
        </div>

    </div>

    <input type="file" id="logo-input" accept="image/jpeg,image/png,image/webp" style="display:none;">
</div>
<br>
                                <div class="input-style-2">
                                    <label for="nombre" class="text-dark mb-2 d-block">Nombre de la empresa</label>
                                    <input id="nombre" name="nombre" type="text" placeholder="Nombre de la empresa" value="{{ old('nombre', $empresa->nombre ?? '') }}" />
                                    <span class="icon"><i class="lni lni-briefcase"></i></span>
                                </div>

                                <div class="input-style-2">
                                    <label for="nit" class="text-dark mb-2 d-block">NIT</label>
                                    <input id="nit" name="nit" type="text" placeholder="NIT de la empresa" value="{{ old('nit', $empresa->nit ?? '') }}" />
                                    <span class="icon"><i class="mdi mdi-card-account-details-outline"></i></span>
                                </div>




                                
                            </div>
                        </div>

                        <!-- COLUMNA DERECHA -->
                        <div class="col-lg-6">
                            <div class="card-style mb-30">
                                <div class="input-style-2">
                                    <label for="telefono" class="text-dark mb-2 d-block">Teléfono</label>
                                    <input id="telefono" name="telefono" type="text" placeholder="Número de teléfono" value="{{ old('telefono', $empresa->telefono ?? '') }}" />
                                    <span class="icon"><i class="lni lni-phone"></i></span>
                                </div>                            
                                <div class="input-style-2">
                                    <label for="email" class="text-dark mb-2 d-block">Email de contacto</label>
                                    <input id="email" name="email" type="text" placeholder="Email de contacto" value="{{ old('email', $empresa->email ?? '') }}" />
                                    <span class="icon"><i class="lni lni-envelope"></i></span>
                                </div>                                
                                <div class="input-style-2">
                                    <label for="direccion" class="text-dark mb-2 d-block">Dirección</label>
                                    <input id="direccion" name="direccion" type="text" placeholder="Dirección" value="{{ old('direccion', $empresa->direccion ?? '') }}" />
                                    <span class="icon"><i class="lni lni-map-marker"></i></span>
                                </div>
                                <div class="input-style-2">
                                    <label for="moneda" class="text-dark mb-2 d-block">Moneda</label>
                                    <input id="moneda" name="moneda" type="text" placeholder="Moneda (ej: COP, USD)" value="{{ old('moneda', $empresa->moneda ?? '') }}" />
                                    <span class="icon"><i class="mdi mdi-currency-usd"></i></span>
                                </div>
                            </div>

                            <!--<div class="card-style mb-30">
                                <h6 class="mb-20">Configuración de impuestos</h6>
                                <div class="form-check form-switch toggle-switch d-flex align-items-center gap-3">
                                    <input id="switch-cobra-iva" name="cobra_iva" class="form-check-input" type="checkbox" {{ old('cobra_iva', $empresa->cobra_iva ?? 0) ? 'checked' : '' }} />
                                    <label class="form-check-label text-sm" for="switch-cobra-iva">
                                        Cobrar IVA
                                    </label>
                                </div>
                            </div> -->

                            
                        </div>
                    </div>
    <!-- NUEVA SECCIÓN DE RESPALDOS - ANCHO COMPLETO -->

    <!-- poner aquie l tema de los respaldos y licencia -->









<!-- Modal de resultado 
<div class="modal fade" id="modalMensaje" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-body" style="padding: 30px; text-align: center;">
                <i id="modal-icon" class="lni lni-checkmark-circle" style="font-size: 64px; color: #10b981; display: block; margin-bottom: 16px;"></i>
                <h5 id="modal-title" style="font-weight: 600; color: #111827; margin-bottom: 8px;">Éxito</h5>
                <p id="modal-message" style="color: #6b7280; margin-bottom: 20px;">Operación completada</p>
                <button type="button" class="main-btn primary-btn btn-hover" data-bs-dismiss="modal" style="padding: 10px 24px;">
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</div>-->

<!-- Modal de resultado -->
<div id="modal-result" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 90%; text-align: center;">
        <div id="modal-icon"></div>
        <h3 id="modal-title" style="font-size: 20px; font-weight: 600; margin: 16px 0 8px;"></h3>
        <p id="modal-message" style="font-size: 14px; color: #64748b; margin-bottom: 24px;"></p>
        <button id="modal-close-btn" class="main-btn btn-hover" style="width: 100%; padding: 12px; font-weight: 600;">Aceptar</button>
    </div>
</div>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>


                </div>
            </div>
        </div>
    </div>



<style>
/* Estilos para los tabs */
.nav-tabs .nav-link {
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    color: #3b82f6 !important;
}

.nav-tabs .nav-link.active {
    color: #3b82f6 !important;
    border-bottom: 3px solid #3b82f6 !important;
    background: transparent !important;
}

/* Animación de pulse */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* Hover effects para las cards de frecuencia */
input[type="radio"]:checked + div {
    border-color: #3b82f6 !important;
    background: #eff6ff !important;
}

label:has(input[type="radio"]) div:hover {
    border-color: #93c5fd !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}
</style>
                <!-- ========== form-elements-wrapper end ========== -->
            </div>
            <!-- end container -->
        </section>
    </div>

    <!-- Modal de advertencia para desactivar IVA -->
    <div class="modal fade" id="modal-iva" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
            <div class="modal-content">
                <div class="modal-header px-4 py-3 border-bottom">
                    <div class="d-flex align-items-center gap-3 w-100">
                        <div class="flex-grow-1">
                            <h5 class="modal-title mb-0 fw-semibold" style="color:#111827;">¿Desactivar IVA?</h5>
                            <div class="text-sm" style="color:#6b7280;">Requiere confirmación</div>
                        </div>
                        <span class="d-inline-flex align-items-center justify-content-center" style="width:40px;height:40px;border-radius:12px;background:#fee2e2;color:#b91c1c;">
                            <i class="lni lni-warning" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="mb-0 text-sm" style="line-height:1.55;color:#111827;">
                        Este cambio se aplicará solo a las facturas futuras.
Los productos que actualmente tienen IVA dejarán de cobrarlo a partir de este momento..<br>
                        <span class="d-inline-block mt-2" style="color:#6b7280;">Las facturas ya emitidas no se verán afectadas ni se modificarán.</span>
                    </p>
                </div>
                <div class="modal-footer px-4 py-3 border-top">
                    <button type="button" class="main-btn light-btn btn-hover " data-bs-dismiss="modal" id="btn-iva-cancelar">Cancelar</button>
                    <button type="button" class="main-btn primary-btn btn-hover" id="btn-iva-confirmar">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function handleBackupClick(e) {
        e.preventDefault();

        const checkbox = document.getElementById('confirm_backup_checkbox');
        const btn = document.getElementById('btn-backup');
        const btnText = document.getElementById('btn-backup-text');
        const form = document.getElementById('backup-form');

        if (!checkbox) return;

        const label = checkbox.closest('label');

        if (!checkbox.checked) {
            if (label) label.classList.add('text-danger');
            return;
        }

        if (label) label.classList.remove('text-danger');

        // Deshabilitar botón y dejar que el formulario se envíe normalmente
        btn.disabled = true;
        btnText.textContent = 'Creando respaldo...';

        // Enviar formulario de la manera tradicional para conservar el nombre del archivo
        form.submit();

        // En caso de fallo rápido, reactivar el botón después de unos segundos
        setTimeout(() => {
            btn.disabled = false;
            btnText.textContent = 'Crear copia de seguridad';
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', function(){
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var csrf = (csrfMeta && csrfMeta.getAttribute('content')) ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';
        var empresaId = {{ $empresa->id ?? 'null' }};
        var existenProductosConIVA = {{ $existenProductosConIVA ? 'true' : 'false' }};

        // Switch cobrador IVA
        // NOTA: Element 'switch-cobra-iva' está comentado en HTML (línea 127-135)
        // Si se descomenta, descomentar también aquí
        /*
var switchInput     = document.getElementById('switch-cobra-iva');
var modalAbierto    = false;
var cierreModalPendiente = null;
var estadoCobraIVA  = switchInput ? switchInput.checked : false;
var modal           = document.getElementById('modal-iva');
var modalInstance   = modal ? new bootstrap.Modal(modal) : null;
var btnConfirmar    = document.getElementById('btn-iva-confirmar');
var btnCancelar     = document.getElementById('btn-iva-cancelar');
        */

        // Estado único de cobra_iva
       
        function mostrarMensaje(msg, ok) {
            var div = document.createElement('div');
            div.className = 'msg ' + (ok ? 'success' : 'error');
            div.textContent = msg;
            // Insertar al inicio del wrap
            var wrap = document.querySelector('.wrap');
            if (wrap) {
                wrap.insertBefore(div, wrap.firstChild);
            }
            setTimeout(() => div.remove(), 2500);
        }

        // Estado por campo (.input-style-2)
        function getFieldUI(fieldName) {
            var input = document.getElementById(fieldName);
            if (!input) return null;

            var container = input.closest('.input-style-2');
            if (!container) return null;

            var iconSpan = container.querySelector('.icon');
            if (!iconSpan) return null;

            if (!iconSpan.dataset.defaultIconHtml) {
                iconSpan.dataset.defaultIconHtml = iconSpan.innerHTML;
            }

            var errorEl = container.querySelector('.field-error');
            if (!errorEl) {
                errorEl = document.createElement('div');
                errorEl.className = 'field-error text-xs mt-1';
                errorEl.style.display = 'none';
                errorEl.style.color = '#dc3545';
                container.appendChild(errorEl);
            }

            return {
                input: input,
                container: container,
                iconSpan: iconSpan,
                errorEl: errorEl
            };
        }

        function setFieldSaving(fieldName) {
            var ui = getFieldUI(fieldName);
            if (!ui) return;
            ui.errorEl.style.display = 'none';
            ui.errorEl.textContent = '';
            ui.iconSpan.innerHTML = '<span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>';
        }

        function setFieldSuccess(fieldName) {
            var ui = getFieldUI(fieldName);
            if (!ui) return;
            ui.errorEl.style.display = 'none';
            ui.errorEl.textContent = '';
            ui.iconSpan.innerHTML = '<i class="lni lni-checkmark-circle" style="color:#198754"></i>';
        }

        function setFieldError(fieldName, message) {
            var ui = getFieldUI(fieldName);
            if (!ui) return;
            ui.iconSpan.innerHTML = '<i class="lni lni-close" style="color:#dc3545"></i>';
            ui.errorEl.textContent = message || 'No se pudo guardar.';
            ui.errorEl.style.display = 'block';
        }

        function abrirModal(onConfirm) {
            cierreModalPendiente = onConfirm;
            modalAbierto = true;
            modalInstance.show();
            btnConfirmar.focus();
        }

        function cerrarModal() {
            modalInstance.hide();
            cierreModalPendiente = null;
            modalAbierto = false;
        }

        // Guardar campo vía AJAX (enviar solo campo+valor)
        function guardarEmpresaAJAX(campo, valor) {
            // No tocar feedback del switch IVA ni backup
            if (campo !== 'cobra_iva') {
                setFieldSaving(campo);
            }

            var payload = {
                _token: csrf,
                campo: campo,
                valor: valor
            };

            fetch("{{ route('empresa.update') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(resp => {
                if (resp.status === 419) {
                    if (campo !== 'cobra_iva') {
                        setFieldError(campo, 'No se pudo guardar. Recarga la página e intenta de nuevo.');
                    }
                    throw new Error('session_expired');
                }
                if (resp.status === 422) {
                    return resp.json().then(errData => {
                        var msg = errData.message || 'Error de validación';
                        // Mostrar solo el error del campo validado
                        if (errData.errors && errData.errors[campo] && errData.errors[campo].length) {
                            msg = errData.errors[campo][0];
                        }
                        if (campo !== 'cobra_iva') {
                            setFieldError(campo, msg);
                        }
                        throw new Error('validation_error');
                    });
                }

                if (resp.status >= 500) {
                    if (campo !== 'cobra_iva') {
                        setFieldError(campo, 'No se pudo guardar. Intenta de nuevo.');
                    }
                    throw new Error('server_error');
                }

                return resp.json();
            })
            .then(data => {
                if (campo !== 'cobra_iva') {
                    if (data && (data.success || data.message)) {
                        setFieldSuccess(campo);
                    } else {
                        setFieldError(campo, 'No se pudo guardar.');
                    }
                }
            })
            .catch((err) => {
                if (err.message === 'session_expired' || err.message === 'validation_error') {
                    return;
                }
                if (campo !== 'cobra_iva') {
                    setFieldError(campo, 'No se pudo guardar. Intenta de nuevo.');
                }
            });
        }

        // Inputs texto/email
        ['nombre','nit','moneda','telefono','email'].forEach(function(name){
            var input = document.getElementById(name);
            if (input) {
                input.addEventListener('change', function(){
                    guardarEmpresaAJAX(input.name, input.value);
                });
            }
        });

        // Input dirección (text)
        var inputDireccion = document.getElementById('direccion');
        if (inputDireccion) {
            inputDireccion.addEventListener('change', function(){
                guardarEmpresaAJAX('direccion', inputDireccion.value);
            });
        }

        // Switch cobra_iva
        // NOTA: Comentado junto con switch-cobra-iva (línea 307)
        /*
        function toggleSwitch() {
            if (modalAbierto) return; // bloquear interacciones mientras el modal está activo

            var nuevoValor = !estadoCobraIVA;

            // Si se intenta apagar y hay productos con IVA, mostrar advertencia
            if (estadoCobraIVA && !nuevoValor && existenProductosConIVA) {
                abrirModal(function confirmarApagado(){
                    estadoCobraIVA = false;
                    switchInput.checked = false;
                    guardarEmpresaAJAX('cobra_iva', 0);
                    cerrarModal();
                });
                return;
            }

            // Cambio directo sin fricción
            estadoCobraIVA = nuevoValor;
            switchInput.checked = estadoCobraIVA;
            guardarEmpresaAJAX('cobra_iva', estadoCobraIVA ? 1 : 0);
        }
        if (switchInput) switchInput.addEventListener('change', toggleSwitch);
        */

        // Acciones modal
        // NOTA: Comentado junto con switch-cobra-iva (línea 307)
        /*
        if (btnConfirmar) btnConfirmar.addEventListener('click', function(){
            if (typeof cierreModalPendiente === 'function') {
                cierreModalPendiente();
            } else {
                cerrarModal();
            }
        });

        if (btnCancelar) btnCancelar.addEventListener('click', function(){
            estadoCobraIVA = true;
            if (switchInput) switchInput.checked = true;
            cerrarModal();
        });
        */
    });
    document.getElementById('logo-input').addEventListener('change', async function() {
    const file = this.files[0];
    if (!file) return;

    const errEl = document.getElementById('logo-error');
    const sucEl = document.getElementById('logo-success');
    errEl.style.display = 'none';
    sucEl.style.display = 'none';

    // Preview instantáneo
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('logo-preview-wrapper').innerHTML =
            `<img id="logo-preview" src="${e.target.result}" style="width:100%;height:100%;object-fit:contain;">`;
    };
    reader.readAsDataURL(file);

    // Subir
    const fd = new FormData();
    fd.append('logo', file);
    fd.append('_token', '{{ csrf_token() }}');

    try {
        const res = await fetch('{{ route("empresa.logo") }}', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            sucEl.textContent = 'Logo actualizado correctamente';
            sucEl.style.display = 'block';
            setTimeout(() => sucEl.style.display = 'none', 3000);
        } else {
            errEl.textContent = data.message || 'Error al subir el logo';
            errEl.style.display = 'block';
        }
    } catch(e) {
        errEl.textContent = 'Error de conexión';
        errEl.style.display = 'block';
    }
});
    </script>


    @endsection