<?php $__env->startSection('title','Empresa'); ?>

<?php $__env->startSection('content'); ?>



    <?php
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
    ?>

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
                                    <input id="nombre" name="nombre" type="text" placeholder="Nombre de la empresa" value="<?php echo e(old('nombre', $empresa->nombre ?? '')); ?>" />
                                    <span class="icon"><i class="lni lni-briefcase"></i></span>
                                </div>

                                <div class="input-style-2">
                                    <label for="nit" class="text-dark mb-2 d-block">NIT</label>
                                    <input id="nit" name="nit" type="text" placeholder="NIT de la empresa" value="<?php echo e(old('nit', $empresa->nit ?? '')); ?>" />
                                    <span class="icon"><i class="mdi mdi-card-account-details-outline"></i></span>
                                </div>

                                <div class="input-style-2">
                                    <label for="telefono" class="text-dark mb-2 d-block">Teléfono</label>
                                    <input id="telefono" name="telefono" type="text" placeholder="Número de teléfono" value="<?php echo e(old('telefono', $empresa->telefono ?? '')); ?>" />
                                    <span class="icon"><i class="lni lni-phone"></i></span>
                                </div>
                                <div class="input-style-2">
                                    <label for="email" class="text-dark mb-2 d-block">Email de contacto</label>
                                    <input id="email" name="email" type="text" placeholder="Email de contacto" value="<?php echo e(old('email', $empresa->email ?? '')); ?>" />
                                    <span class="icon"><i class="lni lni-envelope"></i></span>
                                </div>

                                
                            </div>
                        </div>

                        <!-- COLUMNA DERECHA -->
                        <div class="col-lg-6">
                            <div class="card-style mb-30">
                                
                                <div class="input-style-2">
                                    <label for="direccion" class="text-dark mb-2 d-block">Dirección</label>
                                    <input id="direccion" name="direccion" type="text" placeholder="Dirección" value="<?php echo e(old('direccion', $empresa->direccion ?? '')); ?>" />
                                    <span class="icon"><i class="lni lni-map-marker"></i></span>
                                </div>
                                <div class="input-style-2">
                                    <label for="moneda" class="text-dark mb-2 d-block">Moneda</label>
                                    <input id="moneda" name="moneda" type="text" placeholder="Moneda (ej: COP, USD)" value="<?php echo e(old('moneda', $empresa->moneda ?? '')); ?>" />
                                    <span class="icon"><i class="mdi mdi-currency-usd"></i></span>
                                </div>
                            </div>

                            <div class="card-style mb-30">
                                <h6 class="mb-20">Configuración de impuestos</h6>
                                <div class="form-check form-switch toggle-switch d-flex align-items-center gap-3">
                                    <input id="switch-cobra-iva" name="cobra_iva" class="form-check-input" type="checkbox" <?php echo e(old('cobra_iva', $empresa->cobra_iva ?? 0) ? 'checked' : ''); ?> />
                                    <label class="form-check-label text-sm" for="switch-cobra-iva">
                                        Cobrar IVA
                                    </label>
                                </div>
                            </div>

                            
                        </div>
                    </div>
    <!-- NUEVA SECCIÓN DE RESPALDOS - ANCHO COMPLETO -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card-style mb-30">
                <div class="d-flex align-items-center justify-content-between mb-25">
                    <div>
                        <h6 class="mb-2" style="font-size: 18px; font-weight: 600;">
                            <i class="lni lni-cloud-upload" style="color: #3b82f6; margin-right: 8px;"></i>
                            Gestión de respaldos y licencia
                        </h6>
                        <p class="text-xs text-gray mb-0">Configura y administra los respaldos de tu base de datos y la licencia de tu software</p>
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
                   <!-- <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cloud-tab" data-bs-toggle="tab" data-bs-target="#cloud" type="button" role="tab" style="font-weight: 600; padding: 12px 24px; border: none; background: none; color: #64748b;">
                            <i class="lni lni-cloud-sync" style="margin-right: 6px;"></i>
                            Respaldo en la nube
                        </button>
                    </li> -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="auto-tab" data-bs-toggle="tab" data-bs-target="#auto" type="button" role="tab" style="font-weight: 600; padding: 12px 24px; border: none; background: none; color: #64748b;">
                            <i class="lni lni-cloud-download" style="margin-right: 6px;"></i>
                            Restaurar datos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cloud-tab" data-bs-toggle="tab" data-bs-target="#cloud" type="button" role="tab" style="font-weight: 600; padding: 12px 24px; border: none; background: none; color: #64748b;">
                            <i class="mdi mdi-key" style="margin-right: 6px;"></i>
                            Licencia
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

                    <!-- TAB 2: RESPALDO EN LA NUBE 
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
                            <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 8px;">
                                Hora del respaldo
                            </label>
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

                            <small style="font-size: 12px; color: #64748b; display: block; margin-top: 6px;">
                                <i class="lni lni-moon" style="color: #8b5cf6; margin-right: 4px;"></i>
                                Recomendamos horarios nocturnos
                            </small>
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
</div>-->



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


<div class="tab-pane fade" id="cloud" role="tabpanel">
    <div class="row">
        <div class="col-lg-12">
            
            <!-- 🆕 NUEVA SECCIÓN: Código de Equipo -->
            

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
        },
        'trial_first': {
            html: '<i class="lni lni-warning"></i> Sin Licencia',
            style: 'background: #fef3c7; color: #92400e;'
        },
        'expired': {
            html: '<i class="lni lni-close-circle"></i> Expirada',
            style: 'background: #fee2e2; color: #991b1b;'
        },
        'trial_hardware': {
            html: '<i class="lni lni-warning"></i> Hardware Cambiado',
            style: 'background: #fed7aa; color: #9a3412;'
        }
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

        fetch('<?php echo e(route("database.restore")); ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
        var csrf = (csrfMeta && csrfMeta.getAttribute('content')) ? csrfMeta.getAttribute('content') : '<?php echo e(csrf_token()); ?>';
        var empresaId = <?php echo e($empresa->id ?? 'null'); ?>;
        var existenProductosConIVA = <?php echo e($existenProductosConIVA ? 'true' : 'false'); ?>;

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

            fetch("<?php echo e(route('empresa.update')); ?>", {
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
<!-- <script>
document.addEventListener('DOMContentLoaded', function() {
    const inputCarpetaDisplay = document.getElementById('carpeta_destino_display'); // input de texto
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
                selectHora.value = config.hora_backup || '02:00';
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
</script> -->

    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\optenadvance\app\www\resources\views/empresa/index.blade.php ENDPATH**/ ?>