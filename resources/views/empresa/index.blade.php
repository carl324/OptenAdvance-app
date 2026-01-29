@extends('layouts.app')

@section('title','Empresa — Configuración')

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
                            </div>
                        </div>

                        <!-- COLUMNA DERECHA -->
                        <div class="col-lg-6">
                            <div class="card-style mb-30">
                                <div class="input-style-2">
                                    <label for="moneda" class="text-dark mb-2 d-block">Moneda</label>
                                    <input id="moneda" name="moneda" type="text" placeholder="Moneda (ej: COP, USD)" value="{{ old('moneda', $empresa->moneda ?? '') }}" />
                                    <span class="icon"><i class="mdi mdi-currency-usd"></i></span>
                                </div>

                                <div class="input-style-2">
                                    <label for="direccion" class="text-dark mb-2 d-block">Dirección</label>
                                    <input id="direccion" name="direccion" type="text" placeholder="Dirección" value="{{ old('direccion', $empresa->direccion ?? '') }}" />
                                    <span class="icon"><i class="lni lni-map-marker"></i></span>
                                </div>
                            </div>

                            <div class="card-style mb-30">
                                <h6 class="mb-20">Configuración de impuestos</h6>
                                <div class="form-check form-switch toggle-switch d-flex align-items-center gap-3">
                                    <input id="switch-cobra-iva" name="cobra_iva" class="form-check-input" type="checkbox" {{ old('cobra_iva', $empresa->cobra_iva ?? 0) ? 'checked' : '' }} />
                                    <label class="form-check-label text-sm" for="switch-cobra-iva">
                                        Cobrar IVA
                                    </label>
                                </div>
                            </div>

                            <!-- Sección de copia de seguridad manual (no intrusiva) -->
                            <div class="card-style mb-30">
                                <h6 class="mb-20">Respaldo de datos</h6>
                                <p class="text-xs text-gray mb-15">Crear un respaldo local del archivo de base de datos. Se guardará en tu carpeta <strong>Descargas</strong></p>

                                <form method="POST" action="{{ route('backup.store') }}" id="backup-form">
                                    @csrf
                                    <label style="font-size:13px;color:#6b7280;display:flex;align-items:center;gap:8px;margin-bottom:10px">
                                        <input type="checkbox" name="confirm_backup" id="confirm_backup_checkbox">
                                        He leído y acepto que se generará un archivo en mi carpeta de Descargas.
                                    </label>
                                    <button type="button" id="btn-backup" class="main-btn primary-btn btn-hover w-100" onclick="handleBackupClick(event)">
                                        <span id="btn-backup-text">Crear copia de seguridad</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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

        function setBackupConfirmInvalid(isInvalid) {
            if (!checkbox) return;
            const label = checkbox.closest('label');

            if (isInvalid) {
                if (label) {
                    label.classList.add('text-danger');
                }
            } else {
                if (label) {
                    label.classList.remove('text-danger');
                }
            }
        }

        // Si el usuario marca el checkbox, limpiar el estado de error
        if (checkbox && !checkbox.dataset.boundBackupValidation) {
            checkbox.addEventListener('change', function () {
                if (checkbox.checked) setBackupConfirmInvalid(false);
            });
            checkbox.dataset.boundBackupValidation = '1';
        }
        
        if (!checkbox.checked) {
            setBackupConfirmInvalid(true);
            return;
        }

        setBackupConfirmInvalid(false);
        
        btn.disabled = true;
        btnText.textContent = 'Creando respaldo...';
        
        const formData = new FormData(form);
        const csrf = formData.get('_token');
        
        // Crear iframe oculto para descarga
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
        
        // Solicitar permisos de notificación
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
        
        // Detectar cuando la descarga inicia
        let downloadStarted = false;
        const downloadDetectionTimer = setTimeout(() => {
            if (!downloadStarted) {
                downloadStarted = true;
            }
        }, 1000);
        
        fetch("{{ route('backup.store') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrf
            }
        })
        .then(response => {
            clearTimeout(downloadDetectionTimer);
            
            if (response.ok) {
                // Obtener nombre del archivo del header Content-Disposition
                const contentDisposition = response.headers.get('content-disposition');
                let fileName = 'opten_backup.sql';
                if (contentDisposition) {
                    const matches = contentDisposition.match(/filename="(.+?)"/);
                    if (matches) fileName = matches[1];
                }
                
                // Convertir a blob y crear descarga
                return response.blob().then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = fileName;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                    
                    // Mostrar notificación
                    if ('Notification' in window && Notification.permission === 'granted') {
                        new Notification('Respaldo completado', {
                            body: 'Archivo: ' + fileName,
                            icon: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%231f5fbf"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>'
                        });
                    }
                    
                    downloadStarted = true;
                });
            } else if (response.status === 400 || response.status === 429 || response.status === 500) {
                return response.json().then(data => {
                    throw new Error(data.error || 'Error al crear respaldo');
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + (error.message || 'No se pudo crear el respaldo'));
        })
        .finally(() => {
            clearTimeout(downloadDetectionTimer);
            setTimeout(() => {
                btn.disabled = false;
                btnText.textContent = 'Crear copia de seguridad';
            }, 3000);
            document.body.removeChild(iframe);
        });
    }

    document.addEventListener('DOMContentLoaded', function(){
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var csrf = (csrfMeta && csrfMeta.getAttribute('content')) ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';
        var empresaId = {{ $empresa->id ?? 'null' }};
        var existenProductosConIVA = {{ $existenProductosConIVA ? 'true' : 'false' }};

        // Switch cobrador IVA
        var switchInput = document.getElementById('switch-cobra-iva');
        var modal = document.getElementById('modal-iva');
        var modalInstance = new bootstrap.Modal(modal);
        var btnConfirmar = document.getElementById('btn-iva-confirmar');
        var btnCancelar = document.getElementById('btn-iva-cancelar');
        var cierreModalPendiente = null;
        var modalAbierto = false;

        // Estado único de cobra_iva
        var estadoCobraIVA = switchInput.checked;

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
        switchInput.addEventListener('change', toggleSwitch);

        // Acciones modal
        btnConfirmar.addEventListener('click', function(){
            if (typeof cierreModalPendiente === 'function') {
                cierreModalPendiente();
            } else {
                cerrarModal();
            }
        });

        btnCancelar.addEventListener('click', function(){
            // Revertir al estado actual (antes del intento de apagado) y no guardar
            estadoCobraIVA = true;
            switchInput.checked = true;
            cerrarModal();
        });
    });
    </script>

    @endsection
