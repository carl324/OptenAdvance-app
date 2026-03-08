@extends('layouts.app')

@section('title','licencia')

@section('content')




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
                
    <!-- NUEVA SECCIÓN DE RESPALDOS - ANCHO COMPLETO -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card-style mb-30">
                <div class="d-flex align-items-center justify-content-between mb-25">
                    <div>
                        <h6 class="mb-2" style="font-size: 18px; font-weight: 600;">
                            <i class="lni lni-cloud-upload" style="color: #3b82f6; margin-right: 8px;"></i>
                            Gestión de licencia
                        </h6>
                        <p class="text-xs text-gray mb-0">Configura y administra la licencia de tu software</p>
                    </div>
                    
                </div>

                

                <div class="tab-content">



<div class="tab-pane fade show active" >
    <div class="row">
        <div class="col-lg-12">
            
            

            <div class="row">
                <!-- Panel izquierdo: Estado actual -->
                <div class="col-lg-6">
                    <div style="background: white; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; height: 100%;">
                        <h6 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 20px;">
                            <i class="lni lni-certificate" style="color: #0b8cf5; margin-right: 8px;"></i>
                            Estado de la Licencia
                        </h6>

                        <!-- Estado actual con badge dinámico -->
                        <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                            <div style="font-size: 13px; color: #64748b; margin-bottom: 8px;">Estado actual</div>
                            <div id="license-status-badge" style="display: inline-block; padding: 8px 20px; border-radius: 20px; font-weight: 600; font-size: 14px; background: #dbeafe; color: #1e40af;">
                                <i class="lni lni-checkmark-circle" style="margin-right: 6px;"></i>
                                <span>Activa</span>
                            </div>
                        </div>

                        <!-- Información de fechas -->
                        <div style="margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 6px; margin-bottom: 8px;">
                                <span style="font-size: 13px; color: #64748b;">Fecha de inicio:</span>
                                <span id="license-start-date" style="font-size: 13px; font-weight: 600; color: #1e293b;">--</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 6px; margin-bottom: 8px;">
                                <span style="font-size: 13px; color: #64748b;">Fecha de vencimiento:</span>
                                <span id="license-end-date" style="font-size: 13px; font-weight: 600; color: #1e293b;">--</span>
                            </div>
                        </div>

                        <!-- Botón limpiar caché -->
                        <button type="button" id="btn-refresh-license" class="main-btn primary-btn btn-hover w-100" style="padding: 12px; font-weight: 600; margin-bottom: 12px;">
                            <i class="lni lni-reload" style="margin-right: 6px;"></i>
                            <span>Actualizar Estado</span>
                        </button>

                        <!-- Info box -->
                        <div style="background: #eff6ff; padding: 16px; border-radius: 8px; border-left: 3px solid #3b82f6;">
                            <div style="display: flex; align-items: start; gap: 12px;">
                                <i class="lni lni-information" style="font-size: 20px; color: #3b82f6; margin-top: 2px;"></i>
                                <div>
                                    <div style="font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 4px;">Actualización de estado</div>
                                    <div style="font-size: 12px; color: #64748b;">Usa "Actualizar Estado" después de cargar un nuevo archivo de licencia para ver los cambios inmediatamente</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel derecho: Cargar licencia -->
                <div class="col-lg-6">
                    <div style="background: white; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; height: 100%;">
                        <h6 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 20px;">
                            <i class="lni lni-upload" style="color: #8b5cf6; margin-right: 8px;"></i>
                            Cargar Nueva Licencia
                        </h6>

                        <div style="margin-bottom: 20px;">
                            <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 8px;">
                                Archivo de licencia (.lic)
                            </label>
                            
                            <input type="file" id="license-file-input" accept=".lic" style="display: none;">
                            
                            <button type="button" onclick="document.getElementById('license-file-input').click()" style="width: 100%; padding: 20px; border: 2px dashed #cbd5e1; border-radius: 12px; background: #f8fafc; cursor: pointer; transition: all 0.3s;">
                                <!-- Placeholder inicial -->
                                <div id="license-upload-placeholder" style="text-align: center;">
                                    <i class="lni lni-cloud-upload" style="font-size: 48px; color: #94a3b8; display: block; margin-bottom: 12px;"></i>
                                    <div style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 4px;">Haz clic para seleccionar archivo</div>
                                    <div style="font-size: 13px; color: #64748b;">Formato soportado: .lic</div>
                                </div>
                                
                                <!-- Información del archivo seleccionado -->
                                <div id="license-file-selected" style="display: none;">
                                    <div style="display: flex; align-items: center; gap: 16px; justify-content: center;">
                                        <i class="lni lni-certificate" style="font-size: 48px; color: #f59e0b;"></i>
                                        <div style="text-align: left;">
                                            <div style="font-size: 14px; font-weight: 600; color: #92400e;" id="license-file-name-display"></div>
                                            <div style="font-size: 12px; color: #d97706;" id="license-file-size-display"></div>
                                        </div>
                                    </div>
                                </div>
                            </button>
                            
                            <small style="font-size: 12px; color: #64748b; display: block; margin-top: 6px;">
                                <i class="lni lni-files" style="color: #8b5cf6;"></i>
                                Selecciona el archivo .lic proporcionado por soporte
                            </small>
                        </div>

                        <button type="button" id="btn-upload-license" class="main-btn success-btn btn-hover w-100" style="padding: 12px; font-weight: 600; margin-bottom: 16px;">
                            <i class="lni lni-checkmark-circle" style="margin-right: 6px;"></i>
                            <span>Cargar Licencia</span>
                        </button>

                        <!-- Warning box -->
                        <div style="background: #fef3c7; padding: 16px; border-radius: 8px; border-left: 3px solid #f59e0b;">
                            <div style="display: flex; align-items: start; gap: 12px;">
                                <i class="lni lni-warning" style="font-size: 20px; color: #d97706; margin-top: 2px;"></i>
                                <div>
                                    <div style="font-size: 13px; font-weight: 600; color: #78350f; margin-bottom: 4px;">Importante</div>
                                    <div style="font-size: 12px; color: #92400e;">Solo carga archivos .lic válidos proporcionados por soporte. Archivos incorrectos pueden causar problemas de acceso.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br><br>
<div class="row mb-4">
    <div class="col-lg-12">
        <div style="
            background:#ffffff;
            padding:22px;
            border-radius:12px;
            border:1px solid #e5e7eb;
        ">
            <div style="
                display:flex;
                justify-content:space-between;
                align-items:flex-start;
                gap:20px;
                flex-wrap:wrap;
            ">
                
                <div style="flex:1; min-width:260px;">
                    <div style="
                        display:flex;
                        align-items:center;
                        gap:8px;
                        margin-bottom:6px;
                    ">
                        <i class="lni lni-code" style="font-size:15px; color:#6b7280;"></i>
                        <span style="
                            font-size:13px;
                            font-weight:600;
                            color:#111827;
                        ">
                            Identificador del equipo
                        </span>
                    </div>

                    <div style="
                        font-size:12px;
                        color:#6b7280;
                        margin-bottom:12px;
                    ">
                        Necesario para emitir la licencia de uso
                    </div>

                    <div style="
                        background:#f9fafb;
                        border:1px solid #e5e7eb;
                        border-radius:8px;
                        padding:12px;
                        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
                        font-size:13px;
                        color:#111827;
                        word-break:break-all;
                    ">
                        <span id="machine-hash-display">Cargando…</span>
                    </div>
                </div>

                <div style="
                    display:flex;
                    flex-direction:column;
                    gap:10px;
                ">
                    <button
                        type="button"
                        id="btn-copy-machine-hash"
                        class="btn btn-sm"
                        style="
                            background:rgb(37, 97, 233);
                            color:#ffffff;
                            border-radius:8px;
                            padding:8px 14px;
                            font-size:13px;
                            font-weight:500;
                            display:flex;
                            align-items:center;
                            gap:6px;
                        ">
                        <i class="lni lni-clipboard"></i>
                        Copiar
                    </button>

                    <button
                        type="button"
                        id="btn-refresh-machine-hash"
                        class="btn btn-sm"
                        style="
                            background:transparent;
                            color:#374151;
                            border:1px solid #d1d5db;
                            border-radius:8px;
                            padding:8px 14px;
                            font-size:13px;
                            font-weight:500;
                            display:flex;
                            align-items:center;
                            gap:6px;
                        ">
                        <i class="lni lni-reload"></i>
                        Regenerar
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>

        </div>
    </div>
</div>
<!-- Modal de mensajes -->








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
<script>
// Helper para mostrar mensajes bonitos
// Helper para mostrar mensajes bonitos
function showMessage(title, message, isSuccess = true) {
    const modalResult = document.getElementById('modal-result');
    const icon = document.getElementById('modal-icon');
    const titleEl = document.getElementById('modal-title');
    const messageEl = document.getElementById('modal-message');
    const modalCloseBtn = document.getElementById('modal-close-btn');
    
    // Configurar icono y colores según el tipo
    if (isSuccess) {
        icon.innerHTML = '<div style="width: 80px; height: 80px; background: #ecfdf5; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;"><i class="lni lni-checkmark" style="font-size: 48px; color: #10b981;"></i></div>';
        titleEl.style.color = '#047857';
        modalCloseBtn.style.background = '#10b981';
    } else {
        icon.innerHTML = '<div style="width: 80px; height: 80px; background: #fee2e2; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;"><i class="lni lni-close" style="font-size: 48px; color: #dc2626;"></i></div>';
        titleEl.style.color = '#991b1b';
        modalCloseBtn.style.background = '#dc2626';
    }
    
    modalCloseBtn.style.color = 'white';
    modalCloseBtn.style.border = 'none';
    
    titleEl.textContent = title;
    messageEl.innerHTML = message;
    
    // Mostrar modal
    modalResult.style.display = 'flex';
    
    // Cerrar modal al hacer click en el botón
    modalCloseBtn.onclick = function() {
        modalResult.style.display = 'none';
    };
}

// Cargar hash y datos de licencia al iniciar
document.addEventListener('DOMContentLoaded', () => {
    loadHash();
    loadLicenseData();
});

// ========== MACHINE HASH ==========

// Cargar machine hash
function loadHash() {
    fetch('/license/machine-hash')
        .then(res => res.json())
        .then(data => {
            document.getElementById('machine-hash-display').textContent = 
                data.success ? data.machine_hash : 'Error';
        })
        .catch(() => {
            document.getElementById('machine-hash-display').textContent = 'Error de conexión';
        });
}

// Botón copiar
document.getElementById('btn-copy-machine-hash').addEventListener('click', function() {
    const hash = document.getElementById('machine-hash-display').textContent;
    
    if (hash === 'Cargando…' || hash === 'Error') return;
    
    navigator.clipboard.writeText(hash).then(() => {
        const originalHTML = this.innerHTML;
        this.innerHTML = '<i class="lni lni-checkmark"></i> Copiado';
        
        setTimeout(() => {
            this.innerHTML = originalHTML;
        }, 2000);
    });
});

// Botón regenerar hash
document.getElementById('btn-refresh-machine-hash').addEventListener('click', function() {
    this.disabled = true;
    this.innerHTML = '<i class="lni lni-spinner lni-is-spinning"></i> Regenerando...';
    
    fetch('/license/machine-hash/refresh', { 
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
        .then(res => res.json())
        .then(data => {
            document.getElementById('machine-hash-display').textContent = 
                data.success ? data.machine_hash : 'Error';
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="lni lni-reload"></i> Regenerar';
        });
});

// ========== LICENCIA ==========

// Cargar datos de la licencia
function loadLicenseData() {
    fetch('/license/data')
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                updateLicenseUI(response.data);
            }
        })
        .catch(err => console.error('Error cargando licencia:', err));
}

// Actualizar UI con datos de licencia
function updateLicenseUI(data) {
    const statusBadge = document.getElementById('license-status-badge');
    const startDate = document.getElementById('license-start-date');
    const endDate = document.getElementById('license-end-date');

    // Mapeo de estados
    const statusConfig = {
        'active': {
            html: '<i class="lni lni-checkmark-circle"></i> Activa',
            style: 'background: #dcfce7; color: #166534;'
        },
        'trial_active': {
            html: '<i class="lni lni-checkmark-circle"></i> Trial Activa',
            style: 'background: #dbeafe; color: #1e40af;'
        },'trial_first': {
    html: '<i class="lni lni-warning"></i> modo de prueba',
    style: 'background: #fef3c7; color: #92400e;'
},
        'expired': {
            html: '<i class="lni lni-close-circle"></i> Expirada',
            style: 'background: #fee2e2; color: #991b1b;'
        },
    };

    const config = statusConfig[data.status] || statusConfig['expired'];
    statusBadge.innerHTML = config.html;
    statusBadge.style = config.style + ' display: inline-block; padding: 8px 20px; border-radius: 20px; font-weight: 600; font-size: 14px;';

    startDate.textContent = data.start_at || '--';
    endDate.textContent = data.end_at || '--';
}

// Mostrar archivo seleccionado
document.getElementById('license-file-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    
    if (file) {
        const fileName = file.name;
        const fileSize = (file.size / 1024).toFixed(2) + ' KB';
        
        document.getElementById('license-file-name-display').textContent = fileName;
        document.getElementById('license-file-size-display').textContent = fileSize;
        
        document.getElementById('license-upload-placeholder').style.display = 'none';
        document.getElementById('license-file-selected').style.display = 'block';
    }
});

// Botón subir licencia
document.getElementById('btn-upload-license').addEventListener('click', function() {
    const fileInput = document.getElementById('license-file-input');
    const file = fileInput.files[0];
    
    if (!file) {
        showMessage('Error', 'Por favor selecciona un archivo .lic', false);
        return;
    }

    const formData = new FormData();
    formData.append('license_file', file);

    this.disabled = true;
    this.innerHTML = '<i class="lni lni-spinner lni-is-spinning"></i> Cargando...';

    fetch('/license/upload', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(res => {
        // Verificar si la respuesta es JSON
        const contentType = res.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('El servidor devolvió un error inesperado');
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            showMessage('Licencia instalada', data.message);
            
            // Recargar datos de licencia
            loadLicenseData();
            
            // Resetear input
            fileInput.value = '';
            document.getElementById('license-upload-placeholder').style.display = 'block';
            document.getElementById('license-file-selected').style.display = 'none';
        } else {
            showMessage('Error', data.message, false);
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showMessage('Error', err.message || 'Error al subir la licencia', false);
    })
    .finally(() => {
        this.disabled = false;
        this.innerHTML = '<i class="lni lni-checkmark-circle"></i> Cargar Licencia';
    });
});

// Botón actualizar estado de licencia
document.getElementById('btn-refresh-license').addEventListener('click', function() {
    this.disabled = true;
    this.innerHTML = '<i class="lni lni-spinner lni-is-spinning"></i> Actualizando...';
    
    fetch('/license/refresh', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            updateLicenseUI(response.data);
            showMessage('Actualizado', 'Estado de licencia actualizado');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showMessage('Error', 'Error al actualizar estado', false);
    })
    .finally(() => {
        this.disabled = false;
        this.innerHTML = '<i class="lni lni-reload"></i> Actualizar Estado';
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file-restore');
    const confirmCheckbox = document.getElementById('confirm-restore');
    const btnRestore = document.getElementById('btn-restore');
    const formRestore = document.getElementById('form-restore');
    const uploadSpinner = document.getElementById('upload-spinner');
    const uploadBtn = document.getElementById('upload-btn');
    const modalResult = document.getElementById('modal-result');
    const modalIcon = document.getElementById('modal-icon');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    const modalCloseBtn = document.getElementById('modal-close-btn');

    // Manejar selección de archivo
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Validar extensión
            if (!file.name.toLowerCase().endsWith('.sql')) {
                showModal('error', 'Archivo no válido', 'Solo se permiten archivos .sql');
                clearFileSelection();
                return;
            }

            // Validar tamaño (500MB)
            const maxSize = 500 * 1024 * 1024;
            if (file.size > maxSize) {
                showModal('error', 'Archivo muy grande', 'El archivo excede 500MB');
                clearFileSelection();
                return;
            }

            // Mostrar archivo en el botón
            document.getElementById('file-name-display').textContent = file.name;
            document.getElementById('file-size-display').textContent = formatFileSize(file.size);
            document.getElementById('upload-placeholder').style.display = 'none';
            document.getElementById('file-selected').style.display = 'block';
            
            validateForm();
        }
    });

    // Validar checkbox de confirmación
    confirmCheckbox.addEventListener('change', validateForm);

    // Validar formulario completo
    function validateForm() {
        const hasFile = fileInput.files.length > 0;
        const isConfirmed = confirmCheckbox.checked;
        btnRestore.disabled = !(hasFile && isConfirmed);
    }

    // Enviar formulario
    formRestore.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(formRestore);
        
        // Mostrar spinner DENTRO del contenedor
        uploadBtn.style.pointerEvents = 'none';
        uploadSpinner.style.display = 'flex';
        btnRestore.disabled = true;

        fetch('{{ route("database.restore") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            uploadSpinner.style.display = 'none';
            uploadBtn.style.pointerEvents = 'auto';
            
            if (data.success) {
                showModal(
                    'success', 
                    'Restauración exitosa', 
                    'La base de datos se restauró correctamente.<br><small>Backup: ' + data.backup_created + '</small>'
                );
            } else {
                showModal('error', 'Error en la restauración', data.error);
                btnRestore.disabled = false;
            }
        })
        .catch(error => {
            uploadSpinner.style.display = 'none';
            uploadBtn.style.pointerEvents = 'auto';
            showModal('error', 'Error de conexión', 'No se pudo conectar con el servidor.');
            btnRestore.disabled = false;
        });
    });

    // Mostrar modal
    function showModal(type, title, message) {
        const icons = {
            success: '<div style="width: 80px; height: 80px; background: #ecfdf5; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;"><i class="lni lni-checkmark" style="font-size: 48px; color: #10b981;"></i></div>',
            error: '<div style="width: 80px; height: 80px; background: #fee2e2; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;"><i class="lni lni-close" style="font-size: 48px; color: #dc2626;"></i></div>'
        };
        
        const colors = {
            success: { bg: '#10b981', text: '#047857' },
            error: { bg: '#dc2626', text: '#991b1b' }
        };

        modalIcon.innerHTML = icons[type];
        modalTitle.textContent = title;
        modalTitle.style.color = colors[type].text;
        modalMessage.innerHTML = message;
        modalCloseBtn.style.background = colors[type].bg;
        modalCloseBtn.style.color = 'white';
        modalCloseBtn.style.border = 'none';
        modalResult.style.display = 'flex';
        
        modalCloseBtn.onclick = function() {
            if (type === 'success') {
                window.location.reload();
            } else {
                modalResult.style.display = 'none';
            }
        };
        
        // Si es éxito, recargar automáticamente después de 3 segundos
        if (type === 'success') {
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        }
    }

    // Limpiar selección de archivo
    window.clearFileSelection = function() {
        fileInput.value = '';
        document.getElementById('upload-placeholder').style.display = 'block';
        document.getElementById('file-selected').style.display = 'none';
        validateForm();
    };

    // Resetear formulario
    window.resetForm = function() {
        clearFileSelection();
        confirmCheckbox.checked = false;
        validateForm();
    };

    // Formatear tamaño de archivo
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
});
</script>
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