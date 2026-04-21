

<?php $__env->startSection('title','base-datos'); ?>

<?php $__env->startSection('content'); ?>



<style>
.nav-tabs .nav-link.active {
    color: #3b82f6 !important;
    border-bottom: 3px solid #3b82f6 !important;
    background: transparent !important;
}
</style>
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
                            Gestión de respaldos
                        <p class="text-xs text-gray mb-0">Configura y administra los respaldos de tu base de datos.</p>
                    </div>
                    
                </div>

                <!-- Tabs de navegación -->
                <ul class="nav nav-tabs mb-25" role="tablist" style="border-bottom: 2px solid #e2e8f0;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab" style="font-weight: 600; padding: 12px 24px; border: none; background: none; color: #64748b;">
                            <i class="lni lni-save" style="margin-right: 6px;"></i>
                            Respaldo manual
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cloud-tab" data-bs-toggle="tab" data-bs-target="#cloud" type="button" role="tab" style="font-weight: 600; padding: 12px 24px; border: none; background: none; color: #64748b;">
                            <i class="lni lni-cloud-sync" style="margin-right: 6px;"></i>
                            Respaldo automático
                        </button>
                    </li> 
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="auto-tab" data-bs-toggle="tab" data-bs-target="#auto" type="button" role="tab" style="font-weight: 600; padding: 12px 24px; border: none; background: none; color: #64748b;">
                            <i class="lni lni-cloud-download" style="margin-right: 6px;"></i>
                            Restaurar datos
                        </button>
                    </li>
                    
                </ul>

                <div class="tab-content">
                    <!-- TAB 1: RESPALDO MANUAL -->
                    <div class="tab-pane fade show active" id="manual" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-6">
                                <div style="background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); padding: 30px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                                        <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
                                            <i class="lni lni-download" style="font-size: 28px; color: white;"></i>
                                        </div>
                                        <div>
                                            <h6 style="margin: 0; font-size: 16px; font-weight: 600; color: #1e293b;">Descarga local</h6>
                                            <p style="margin: 0; font-size: 13px; color: #64748b;">Guardar en tu computadora</p>
                                        </div>
                                    </div>
                                    
                                    <p class="text-sm text-gray mb-20">
                                        Crea un respaldo completo de tu base de datos. El archivo se guardará en tu carpeta de <strong>Descargas</strong> y podrás restaurarlo cuando lo necesites.
                                    </p>

                                    <form method="POST" action="<?php echo e(route('backup.store')); ?>" id="backup-form">
                                        <?php echo csrf_field(); ?>
                                        <label style="font-size:13px; color:#475569; display:flex; align-items:flex-start; gap:10px; margin-bottom:20px; cursor: pointer; user-select: none;">
                                            <input type="checkbox" name="confirm_backup" id="confirm_backup_checkbox" style="margin-top: 3px; width: 18px; height: 18px; cursor: pointer;">
                                            <span>He leído y acepto que se generará un archivo de respaldo en mi carpeta de Descargas</span>
                                        </label>
                                        
                                        <button type="button" id="btn-backup" class="main-btn primary-btn btn-hover w-100" onclick="handleBackupClick(event)" style="padding: 14px; font-weight: 600; font-size: 14px;">
                                            <i class="lni lni-download" style="margin-right: 8px;"></i>
                                            <span id="btn-backup-text">Crear copia de seguridad</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div style="background: #f8fafc; padding: 24px; border-radius: 12px; height: 100%;">
                                    <h6 style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 16px;">
                                        <i class="lni lni-information" style="color: #3b82f6; margin-right: 6px;"></i>
                                        Información importante
                                    </h6>
                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                        <li style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; display: flex; align-items: start; gap: 10px;">
                                            <i class="lni lni-checkmark-circle" style="color: #10b981; font-size: 18px; margin-top: 2px;"></i>
                                            <span style="font-size: 13px; color: #475569;">El respaldo incluye todos los productos, ventas y configuraciones</span>
                                        </li>
                                        <li style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; display: flex; align-items: start; gap: 10px;">
                                            <i class="lni lni-checkmark-circle" style="color: #10b981; font-size: 18px; margin-top: 2px;"></i>
                                            <span style="font-size: 13px; color: #475569;">El archivo se descarga en formato SQL compatible</span>
                                        </li>
                                        <li style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; display: flex; align-items: start; gap: 10px;">
                                            <i class="lni lni-checkmark-circle" style="color: #10b981; font-size: 18px; margin-top: 2px;"></i>
                                            <span style="font-size: 13px; color: #475569;">Recomendamos hacer respaldos semanales</span>
                                        </li>
                                        <li style="padding: 12px 0; display: flex; align-items: start; gap: 10px;">
                                            <i class="lni lni-checkmark-circle" style="color: #10b981; font-size: 18px; margin-top: 2px;"></i>
                                            <span style="font-size: 13px; color: #475569;">Guarda los respaldos en un lugar seguro</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: RESPALDO EN LA NUBE -->
<div class="tab-pane fade" id="cloud" role="tabpanel">
    <div class="row">
        <div class="col-lg-12">
            <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 32px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #bae6fd;">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h6 style="font-size: 18px; font-weight: 700; color: #0c4a6e; margin-bottom: 8px;">
                            Respaldo automático
                        </h6>
                        <p style="font-size: 14px; color: #075985; margin-bottom: 0;">
                            Configura respaldos automáticos de tu base de datos en una carpeta del servidor
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div style="background: white; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; height: 100%;">
                        <h6 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 20px;">
                            <i class="lni lni-cog" style="color: #3b82f6; margin-right: 8px;"></i>
                            Configuración de carpeta
                        </h6>

<div style="margin-bottom: 20px;">
    <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 8px;">
        Carpeta de destino
    </label>
    <div style="position: relative;">
        <input 
            type="text" 
            name="carpeta_destino" 
            id="carpeta_destino_display" 
            placeholder="Ejemplo: C:/respaldos o D:/backups o /home/usuario/backups" 
            style="width: 100%; padding: 12px 40px 12px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;"
        >
        <span style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%);">
            <i class="lni lni-folder" style="color: #3b82f6;"></i>
        </span>
    </div>
    <small style="font-size: 12px; color: #64748b; display: block; margin-top: 6px;">
        <i class="lni lni-information" style="color: #3b82f6;"></i>
        Ingresa la ruta completa donde se guardarán los respaldos automáticos
    </small>
</div>                       


                        <div style="margin-bottom: 20px;">
                            <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 8px;">
                                Nombre del archivo
                            </label>
                            <input type="text" name="prefijo_nombre" value="backup_opten" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                            <small style="font-size: 12px; color: #64748b; display: block; margin-top: 6px;">
                                Se agregará automáticamente la fecha y hora
                            </small>
                        </div>

                        <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border-left: 3px solid #3b82f6;">
                            <div style="display: flex; align-items: start; gap: 12px;">
                                <i class="lni lni-information" style="font-size: 20px; color: #3b82f6; margin-top: 2px;"></i>
                                <div>
                                    <div style="font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 4px;">Información</div>
                                    <div style="font-size: 12px; color: #64748b;">Los respaldos se guardarán automáticamente según la configuración establecida</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div style="background: white; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; height: 100%;">
                        <h6 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 20px;">
                            <i class="lni lni-alarm-clock" style="color: #8b5cf6; margin-right: 8px;"></i>
                            Programación automática
                        </h6>

                        <div style="margin-bottom: 20px;">
                            <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 12px;">
                                Frecuencia de respaldo
                            </label>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                                <label style="cursor: pointer;">
                                    <input type="radio" name="backup_frequency_cloud" value="diario" style="display: none;">
                                    <div class="frecuencia-card" style="padding: 16px; border: 2px solid #e2e8f0; border-radius: 8px; text-align: center; transition: all 0.2s;">
                                        <i class="lni lni-calendar" style="font-size: 24px; color: #3b82f6; display: block; margin-bottom: 6px;"></i>
                                        <div style="font-size: 13px; font-weight: 600; color: #1e293b;">Diario</div>
                                    </div>
                                </label>
                                <label style="cursor: pointer;">
                                    <input type="radio" name="backup_frequency_cloud" value="semanal" style="display: none;" checked>
                                    <div class="frecuencia-card" style="padding: 16px; border: 2px solid #3b82f6; border-radius: 8px; text-align: center; background: #eff6ff; transition: all 0.2s;">
                                        <i class="lni lni-calendar" style="font-size: 24px; color: #3b82f6; display: block; margin-bottom: 6px;"></i>
                                        <div style="font-size: 13px; font-weight: 600; color: #1e293b;">Semanal</div>
                                    </div>
                                </label>
                                <label style="cursor: pointer;">
                                    <input type="radio" name="backup_frequency_cloud" value="mensual" style="display: none;">
                                    <div class="frecuencia-card" style="padding: 16px; border: 2px solid #e2e8f0; border-radius: 8px; text-align: center; transition: all 0.2s;">
                                        <i class="lni lni-calendar" style="font-size: 24px; color: #3b82f6; display: block; margin-bottom: 6px;"></i>
                                        <div style="font-size: 13px; font-weight: 600; color: #1e293b;">Mensual</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div style="margin-bottom: 20px;">
                            
                            <div style="margin-bottom: 20px;">
    <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 8px;">
        Hora del respaldo
    </label>
  
    <input 
        type="time" 
        name="hora_backup" 
        value="02:00" 
        step="60"
        style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; color: #475569;"
    >
    <small style="font-size: 12px; color: #64748b; display: block; margin-top: 6px;">
        <i class="lni lni-moon" style="color: #8b5cf6; margin-right: 4px;"></i>
        Recomendamos horarios nocturnos
    </small>
</div>

                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 8px;">
                                Retención de respaldos
                            </label>
                            <select name="retencion" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; color: #475569;">
                                <option value="3">Mantener últimos 3 respaldos</option>
                                <option value="15" selected>Mantener últimos 15 respaldos</option>
                                <option value="30">Mantener últimos 30 respaldos</option>
                                <option value="365">Mantener todos los respaldos (1 año)</option>
                            </select>
                            <small style="font-size: 12px; color: #64748b; display: block; margin-top: 6px;">
                                Los respaldos antiguos se eliminarán automáticamente
                            </small>
                        </div>

                        <button type="button" data-guardar-config class="main-btn primary-btn btn-hover w-100" style="padding: 12px; font-weight: 600;">
                            <i class="lni lni-save" style="margin-right: 6px;"></i>
                            <span>Guardar configuración</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



                    <!-- TAB 3: RESTAURAR DATOS -->
<div class="tab-pane fade" id="auto" role="tabpanel">
    <div class="row">
        <div class="col-lg-8">
            <div style="background: white; padding: 28px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
                <h6 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 20px;">
                    <i class="lni lni-reload" style="color: #10b981; margin-right: 8px;"></i>
                    Restaurar base de datos
                </h6>

                <form id="form-restore" enctype="multipart/form-data" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <div style="margin-bottom: 24px;">
                        <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 12px;">
                            Seleccionar archivo de respaldo
                        </label>
                        <div style="position: relative;">
                            <input type="file" id="file-restore" name="backup_file" accept=".sql" style="display: none;" required>
                            
                            <!-- Botón de selección de archivo -->
                            <button type="button" id="upload-btn" onclick="document.getElementById('file-restore').click()" style="width: 100%; padding: 20px; border: 2px dashed #cbd5e1; border-radius: 12px; background: #f8fafc; cursor: pointer; transition: all 0.3s;">
                                <!-- Placeholder inicial (se oculta cuando se selecciona archivo) -->
                                <div id="upload-placeholder" style="text-align: center;">
                                    <i class="lni lni-cloud-upload" style="font-size: 48px; color: #94a3b8; display: block; margin-bottom: 12px;"></i>
                                    <div style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 4px;">Haz clic para seleccionar archivo</div>
                                    <div style="font-size: 13px; color: #64748b;">Formato soportado: .sql (MySQL)</div>
                                </div>
                                
                                <!-- Información del archivo seleccionado (oculto inicialmente) -->
                                <div id="file-selected" style="display: none;">
                                    <div style="display: flex; align-items: center; gap: 16px; justify-content: center;">
                                        <i class="lni lni-database" style="font-size: 48px; color: #10b981;"></i>
                                        <div style="text-align: left;">
                                            <div style="font-size: 14px; font-weight: 600; color: #047857;" id="file-name-display"></div>
                                            <div style="font-size: 12px; color: #059669;" id="file-size-display"></div>
                                        </div>
                                    </div>
                                </div>
                            </button>
                            
                            <!-- Spinner de carga (oculto inicialmente) -->
                            <div id="upload-spinner" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(248, 250, 252, 0.95); border-radius: 12px; align-items: center; justify-content: center;">
                                <div style="text-align: center;">
                                    <div class="spinner" style="width: 50px; height: 50px; border: 4px solid #e2e8f0; border-top-color: #10b981; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 12px;"></div>
                                    <div style="font-size: 14px; font-weight: 600; color: #1e293b;">Restaurando...</div>
                                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Espera un momento</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="background: #f0fdfa; border-left: 3px solid #10b981; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
                        <h6 style="font-size: 14px; font-weight: 600; color: #047857; margin-bottom: 12px;">
                            <i class="lni lni-list" style="margin-right: 6px;"></i>
                            Proceso de restauración
                        </h6>
                        <ol style="margin: 0; padding-left: 20px; color: #059669; font-size: 13px; line-height: 1.8;">
                            <li>Se verificará la integridad del archivo</li>
                            <li>Se creará un respaldo automático de tus datos actuales</li>
                            <li>Se restaurarán los datos del archivo seleccionado</li>
                            <li>Se reiniciarán las conexiones de la base de datos</li>
                        </ol>
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label style="font-size: 13px; color: #475569; display: flex; align-items: flex-start; gap: 10px; cursor: pointer; user-select: none;">
                            <input type="checkbox" id="confirm-restore" name="confirm_restore" value="1" style="margin-top: 3px; width: 18px; height: 18px; cursor: pointer;">
                            <span style="flex: 1;">
                                <strong>Confirmo que entiendo que esta acción sobrescribirá todos mis datos actuales</strong> y que se creará un respaldo automático antes de restaurar.
                            </span>
                        </label>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button type="button" class="main-btn light-btn btn-hover" onclick="resetForm()" style="flex: 1; padding: 12px; font-weight: 600;">
                            <i class="lni lni-close" style="margin-right: 6px;"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="main-btn btn-hover" style="flex: 1; padding: 12px; font-weight: 600; background: #10b981; color: white; border: none;" disabled id="btn-restore">
                            <i class="lni lni-reload" style="margin-right: 6px;"></i>
                            Iniciar restauración
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div style="background: #fef3c7; border-left: 3px solid #f59e0b; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
                <div style="display: flex; align-items: start; gap: 12px;">
                    <i class="lni lni-warning" style="font-size: 20px; color: #d97706; margin-top: 2px;"></i>
                    <div>
                        <div style="font-size: 13px; font-weight: 600; color: #92400e; margin-bottom: 4px;">¡Importante! Esta acción sobrescribirá tus datos actuales</div>
                        <div style="font-size: 12px; color: #78350f;">Asegúrate de tener un respaldo reciente antes de restaurar. Todos los datos actuales serán reemplazados por los del archivo de respaldo.</div>
                    </div>
                </div>
            </div>

            <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                <h6 style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 16px;">
                    <i class="lni lni-question-circle" style="color: #3b82f6; margin-right: 6px;"></i>
                    ¿Necesitas ayuda?
                </h6>
                <p style="font-size: 13px; color: #64748b; margin-bottom: 16px; line-height: 1.6;">
                    Si tienes dudas sobre el proceso de restauración, consulta nuestra documentación o contacta a soporte.
                </p>
                <a href="<?php echo e(route('soporte.index')); ?>" class="main-btn light-btn btn-hover w-100" style="padding: 10px; font-size: 13px;">
                    Contactar a soporte
                </a>
            </div>
        </div>
    </div>
</div>




        </div>
    </div>
</div>
<!-- Modal de mensajes -->










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

    btn.disabled = true;
    btnText.textContent = 'Creando respaldo...';

    form.submit();

    setTimeout(() => {
        btn.disabled = false;
        btnText.textContent = 'Crear copia de seguridad';
    }, 5000);
}
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
            if (!file.name.toLowerCase().endsWith('.sql')) {
                showModal('error', 'Archivo no válido', 'Solo se permiten archivos .sql');
                clearFileSelection();
                return;
            }

            const maxSize = 500 * 1024 * 1024;
            if (file.size > maxSize) {
                showModal('error', 'Archivo muy grande', 'El archivo excede 500MB');
                clearFileSelection();
                return;
            }

            document.getElementById('file-name-display').textContent = file.name;
            document.getElementById('file-size-display').textContent = formatFileSize(file.size);
            document.getElementById('upload-placeholder').style.display = 'none';
            document.getElementById('file-selected').style.display = 'block';
            
            validateForm();
        }
    });

    confirmCheckbox.addEventListener('change', validateForm);

    function validateForm() {
        const hasFile = fileInput.files.length > 0;
        const isConfirmed = confirmCheckbox.checked;
        btnRestore.disabled = !(hasFile && isConfirmed);
    }

    formRestore.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(formRestore);
        
        uploadBtn.style.pointerEvents = 'none';
        uploadSpinner.style.display = 'flex';
        btnRestore.disabled = true;

        fetch('<?php echo e(route("database.restore")); ?>', {
    method: 'POST',
    body: formData,
    headers: {
        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
    }
})
.then(response => {
    if (response.status === 429) {
        uploadSpinner.style.display = 'none';
        uploadBtn.style.pointerEvents = 'auto';
        showModal('error', 'Restauración en proceso', 'Ya hay una restauración en curso. Espera unos minutos e intenta de nuevo.');
        btnRestore.disabled = false;
        return Promise.reject('rate_limit');
    }
    return response.json();
})
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
    if (error === 'rate_limit') return;
    uploadSpinner.style.display = 'none';
    uploadBtn.style.pointerEvents = 'auto';
    showModal('error', 'Error de conexión', 'No se pudo conectar con el servidor.');
    btnRestore.disabled = false;
});
    });

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
        
        if (type === 'success') {
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        }
    }

    window.clearFileSelection = function() {
        fileInput.value = '';
        document.getElementById('upload-placeholder').style.display = 'block';
        document.getElementById('file-selected').style.display = 'none';
        validateForm();
    };

    window.resetForm = function() {
        clearFileSelection();
        confirmCheckbox.checked = false;
        validateForm();
    };

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputCarpetaDisplay = document.getElementById('carpeta_destino_display');
    const btnGuardar = document.querySelector('[data-guardar-config]');
    const selectHora = document.querySelector('[name="hora_backup"]');
    const selectRetencion = document.querySelector('[name="retencion"]');
    const inputPrefijo = document.querySelector('[name="prefijo_nombre"]');
    
    let rutaCarpeta = null;

    cargarConfiguracion();

    btnGuardar.addEventListener('click', guardarConfiguracion);

    document.querySelectorAll('input[name="backup_frequency_cloud"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.frecuencia-card').forEach(card => {
                card.style.borderColor = '#e2e8f0';
                card.style.background = 'white';
            });
            
            const card = this.nextElementSibling;
            card.style.borderColor = '#3b82f6';
            card.style.background = '#eff6ff';
        });
    });

    async function cargarConfiguracion() {
        try {
            const response = await fetch('/backup-config/obtener');
            const data = await response.json();

            if (data.success && data.config) {
                const config = data.config;

                if (config.carpeta_destino) {
                    inputCarpetaDisplay.value = config.carpeta_destino;
                    rutaCarpeta = config.carpeta_destino;
                }

                inputPrefijo.value = config.prefijo_nombre_archivo || 'backup_opten';
                selectHora.value = (config.hora_backup || '02:00').substring(0, 5);
                selectRetencion.value = config.retencion || 30;

                if (config.frecuencia) {
                    const radio = document.querySelector(`input[name="backup_frequency_cloud"][value="${config.frecuencia}"]`);
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    }
                }
            }
        } catch (error) {
            console.error('Error cargando configuración:', error);
        }
    }

    async function guardarConfiguracion() {

        const frecuenciaRadio = document.querySelector('input[name="backup_frequency_cloud"]:checked');
        const rutaInput = inputCarpetaDisplay.value.trim();
        const horaValue = selectHora.value;
if (!horaValue) {
    mostrarMensaje('Debe seleccionar una hora', 'error');
    return;
}

        if (!rutaInput) {
            mostrarMensaje('Debe ingresar la ruta de destino', 'error');
            return;
        }

        if (!frecuenciaRadio) {
            mostrarMensaje('Debe seleccionar una frecuencia', 'error');
            return;
        }

        const formData = {
            carpeta_destino: rutaInput,
            prefijo_nombre_archivo: inputPrefijo.value || 'backup_opten',
            frecuencia: frecuenciaRadio.value,
            hora_backup: selectHora.value,
            retencion: parseInt(selectRetencion.value)
        };

        const btnSpan = btnGuardar.querySelector('span');
        const originalText = btnSpan.textContent;
        btnGuardar.disabled = true;
        btnSpan.textContent = 'Guardando...';

        try {
            const response = await fetch('/backup-config/guardar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                mostrarMensaje(data.message, 'success');
                rutaCarpeta = data.config.carpeta_destino;
            } else {
                mostrarMensaje(data.message, 'error');
            }
        } catch (error) {
            mostrarMensaje('Error al guardar configuración', 'error');
            console.error('Error:', error);
        } finally {
            btnGuardar.disabled = false;
            btnSpan.textContent = originalText;
        }
    }

    function mostrarMensaje(mensaje, tipo) {
        const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
        const icon = tipo === 'success' ? 'lni-checkmark-circle' : 'lni-close-circle';

        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `
            <i class="lni ${icon} me-2"></i>
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);

        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
});
</script>



    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\optenadvance\app\www\resources\views/db/index.blade.php ENDPATH**/ ?>