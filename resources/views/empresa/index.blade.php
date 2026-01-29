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

                            <div class="card-style mb-30">
                                <h6 class="mb-20">Configuración de impuestos</h6>
                                <div class="form-check form-switch toggle-switch d-flex align-items-center gap-3">
                                    <input id="switch-cobra-iva" name="cobra_iva" class="form-check-input" type="checkbox" {{ old('cobra_iva', $empresa->cobra_iva ?? 0) ? 'checked' : '' }} />
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
                            Gestión de respaldos
                        </h6>
                        <p class="text-xs text-gray mb-0">Configura y administra los respaldos de tu base de datos</p>
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

                                    <form method="POST" action="{{ route('backup.store') }}" id="backup-form">
                                        @csrf
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
                                    <div style="margin-bottom: 24px;">
                                        <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 12px;">
                                            Seleccionar archivo de respaldo
                                        </label>
                                        <div style="position: relative;">
                                            <input type="file" id="file-restore" accept=".sql,.db,.sqlite" style="display: none;">
                                            <button type="button" onclick="document.getElementById('file-restore').click()" style="width: 100%; padding: 20px; border: 2px dashed #cbd5e1; border-radius: 12px; background: #f8fafc; cursor: pointer; transition: all 0.3s;">
                                                <div style="text-align: center;">
                                                    <i class="lni lni-cloud-upload" style="font-size: 48px; color: #94a3b8; display: block; margin-bottom: 12px;"></i>
                                                    <div style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 4px;">Haz clic para seleccionar archivo</div>
                                                    <div style="font-size: 13px; color: #64748b;">Formatos soportados: .sql, .db, .sqlite</div>
                                                </div>
                                            </button>
                                        </div>
                                        <div id="selected-file-info" style="margin-top: 12px; display: none;">
                                            <div style="background: #ecfdf5; border: 1px solid #10b981; padding: 12px; border-radius: 8px; display: flex; align-items: center; gap: 12px;">
                                                <i class="lni lni-checkmark-circle" style="font-size: 24px; color: #10b981;"></i>
                                                <div style="flex: 1;">
                                                    <div style="font-size: 13px; font-weight: 600; color: #047857;" id="file-name">archivo.sql</div>
                                                    <div style="font-size: 12px; color: #059669;" id="file-size">2.5 MB</div>
                                                </div>
                                                <button type="button" onclick="clearFileSelection()" style="background: none; border: none; color: #dc2626; cursor: pointer;">
                                                    <i class="lni lni-close" style="font-size: 20px;"></i>
                                                </button>
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
                                            <input type="checkbox" id="confirm-restore" style="margin-top: 3px; width: 18px; height: 18px; cursor: pointer;">
                                            <span style="flex: 1;">
                                                <strong>Confirmo que entiendo que esta acción sobrescribirá todos mis datos actuales</strong> y que se creará un respaldo automático antes de restaurar.
                                            </span>
                                        </label>
                                    </div>

                                    <div style="display: flex; gap: 12px;">
                                        <button class="main-btn light-btn btn-hover" style="flex: 1; padding: 12px; font-weight: 600;">
                                            <i class="lni lni-close" style="margin-right: 6px;"></i>
                                            Cancelar
                                        </button>
                                        <button class="main-btn btn-hover" style="flex: 1; padding: 12px; font-weight: 600; background: #10b981; color: white; border: none;" disabled id="btn-restore">
                                            <i class="lni lni-reload" style="margin-right: 6px;"></i>
                                            Iniciar restauración
                                        </button>
                                    </div>
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
                                    <a href="{{ route('soporte.index') }}" class="main-btn light-btn btn-hover w-100" style="padding: 10px; font-size: 13px;">
                                        
                                        Contactar a soporte
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
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

    @endsection
