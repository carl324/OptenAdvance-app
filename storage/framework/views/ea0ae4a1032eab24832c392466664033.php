

<?php $__env->startSection('title', 'Personal'); ?>

<?php $__env->startSection('content'); ?>
<br><br>
<style>
/* ========== Perfil Colapsable ========== */
.profile-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  border-radius: 8px;
}

.profile-header:hover {
  background: #f8fafc;
}

.profile-preview {
  display: flex;
  align-items: center;
  gap: 16px;
  flex: 1;
}

.profile-image-small {
  position: relative;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
  border: 2px solid #e2e8f0;
}

.profile-image-small img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.status-badge {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 2px solid #ffffff;
}

.status-online {
  background: #10b981;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
}

.status-offline {
  background: #94a3b8;
}

.profile-info-compact h6 {
  font-size: 15px;
  font-weight: 600;
  color: #0f172a;
  margin: 0;
}

.profile-info-compact p {
  font-size: 13px;
  color: #64748b;
  margin: 0;
}

.toggle-btn {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  border: none;
  background: #f1f5f9;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.toggle-btn:hover {
  background: #e2e8f0;
  color: #0f172a;
}

.toggle-btn i {
  font-size: 18px;
  transition: transform 0.3s ease;
}

.toggle-btn.active i {
  transform: rotate(180deg);
}

.profile-content {
  overflow: hidden;
  transition: all 0.3s ease;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.profile-content.show {
  animation: slideDown 0.3s ease;
}

/* ========== Badge empleados ========== */
.badge-empleados {
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 51, 234, 0.1));
  border: 1px solid rgba(59, 130, 246, 0.2);
  border-radius: 100px;
  font-size: 12px;
  font-weight: 600;
  color: #3b82f6;
}
</style>
<section class="section">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      
      <!-- end row -->
    </div>
    <!-- ========== title-wrapper end ========== -->

    <div class="row">
      <!-- Columna izquierda: Mi perfil + Agregar empleado -->
      <div class="col-lg-6">
        <!-- Mi Perfil (Colapsable) -->
        <div class="card-style settings-card-1 mb-30">
          <!-- Header con botón desplegable -->
          <div class="profile-header" onclick="toggleProfile()">
            <div class="profile-preview">
              <div class="profile-image-small">
                <img src="assets/images/profile/admin.png" alt="" />
              </div>
                <div class="profile-info-compact">
                  <h6 class="mb-1"><?php echo e(auth()->user()->name ?? 'Administrador'); ?></h6>
                  <p class="text-sm text-gray mb-0">Administrador</p>
                </div>
            </div>
            <button class="toggle-btn" type="button">
              <i class="lni lni-chevron-down" id="toggle-icon"></i>
            </button>
          </div>

          <!-- Contenido expandible -->
          <div class="profile-content" id="profile-content" style="display: none;">
            <div class="profile-info">
              <div id="profile-alert" class="alert d-none" role="alert"></div>

              <form id="form-perfil-admin" data-endpoint="<?php echo e(route('perfil.admin.update')); ?>" novalidate>
                <?php echo csrf_field(); ?>
                <div class="input-style-1">
                  <label>Nombre</label>
                  <input type="text" name="name" value="<?php echo e(auth()->user()->name ?? ''); ?>" maxlength="100" aria-invalid="false" />
                  <div class="invalid-feedback d-none" id="profile-error-name"></div>
                </div>
                
                <div class="input-style-1">
                  <label>Email</label>
                  <input type="text" name="email" value="<?php echo e(auth()->user()->email ?? ''); ?>" maxlength="150" aria-invalid="false" />
                  <div class="invalid-feedback d-none" id="profile-error-email"></div>
                </div>
                <div class="input-style-1">
                  <label>Contraseña (actualizar)</label>
                  <input type="text" name="password" placeholder="Dejar vacío para no cambiar" maxlength="60" aria-invalid="false" />
                  <div class="invalid-feedback d-none" id="profile-error-password"></div>
                </div>

                <div class="mt-3">
                  <button type="button" id="btn-guardar-perfil" class="main-btn primary-btn btn-hover w-100">
                    <i class="lni lni-save"></i> Guardar cambios
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- end card -->

        <!-- Botón para agregar nuevo empleado -->
        <button class="main-btn light-btn btn-hover w-100 mb-30" onclick="toggleNuevoEmpleado()">
          <i class="lni lni-plus"></i> Agregar nuevo empleado
        </button>

        <!-- Formulario nuevo empleado (colapsado) -->
        <div class="card-style settings-card-2 mb-30" id="form-nuevo-empleado" style="display: none;">
          <div class="title mb-30 d-flex justify-content-between align-items-center">
            <h6>Nuevo Empleado</h6>
            <button class="border-0 bg-transparent" onclick="toggleNuevoEmpleado()" type="button">
              <i class="lni lni-close"></i>
            </button>
          </div>
          <div id="empleado-alert" class="alert d-none" role="alert"></div>
          <form id="form-empleado" data-endpoint="<?php echo e(route('personal.store')); ?>" novalidate>
            <?php echo csrf_field(); ?>
            <div class="row">
              <div class="col-12">
                <div class="input-style-1">
                  <label>Nombre</label>
                  <input type="text" name="name" placeholder="Nombre de Usuario" maxlength="100" />
                  <div class="invalid-feedback d-none" id="error-name"></div>
                </div>
              </div>
              <div class="col-12">
                <div class="input-style-1">
                  <label>Email</label>
                  <input type="text" name="email" placeholder="email@ejemplo.com" maxlength="150" />
                  <div class="invalid-feedback d-none" id="error-email"></div>
                </div>
              </div>
              <div class="col-12">
                <div class="input-style-1">
                  <label>Teléfono</label>
                  <input type="text" name="phone" placeholder="+57 300 000 0000" maxlength="20" />
                  <div class="invalid-feedback d-none" id="error-phone"></div>
                </div>
              </div>
              <div class="col-12">
                <div class="input-style-1">
                  <label>Contraseña inicial</label>
                  <input type="text" name="password" placeholder="Mínimo 5 caracteres" maxlength="60" />
                  <div class="invalid-feedback d-none" id="error-password"></div>
                </div>
              </div>
              <div class="col-12">
                <div class="d-flex gap-2">
                  <button type="button" class="main-btn light-btn btn-hover flex-fill" onclick="toggleNuevoEmpleado()">
                    Cancelar
                  </button>
                  <button type="button" class="main-btn primary-btn btn-hover flex-fill" id="btn-crear-empleado">
                    <i class="lni lni-checkmark"></i> Crear empleado
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
        <!-- end card -->
      </div>
      <!-- end col -->

      <!-- Columna derecha: Lista de empleados -->
      <div class="col-lg-6">
        
        <?php $__empty_1 = true; $__currentLoopData = $empleados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empleado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="card-style settings-card-1 mb-30">
            <div class="profile-header" onclick="toggleEmpleado(<?php echo e($empleado->id); ?>)">
              <div class="profile-preview">
                
                <div class="profile-image-small">
                  
                  <img src="assets/images/profile/empleado.png" alt="" />
                  
                </div>
                <div class="profile-info-compact">
                  <h6 class="mb-1"><?php echo e($empleado->name ?? $empleado->username); ?></h6>
                  <p class="text-sm text-gray mb-0">Empleado</p>
                </div>
              </div>
              <button class="toggle-btn" type="button">
                <i class="lni lni-chevron-down" id="toggle-icon-<?php echo e($empleado->id); ?>"></i>
              </button>
            </div>

            <div class="profile-content" id="empleado-content-<?php echo e($empleado->id); ?>" style="display: none;">
              <div class="profile-info">
                <div id="empleado-alert-<?php echo e($empleado->id); ?>" class="alert d-none" role="alert"></div>

                <div class="input-style-1">
                  <label>Nombre</label>
                  <input type="text" id="empleado-name-<?php echo e($empleado->id); ?>" value="<?php echo e($empleado->name ?? ''); ?>" maxlength="100" aria-invalid="false" />
                  <div class="invalid-feedback d-none" id="empleado-error-name-<?php echo e($empleado->id); ?>"></div>
                </div>
                <div class="input-style-1">
                  <label>Email</label>
                  <input type="text" id="empleado-email-<?php echo e($empleado->id); ?>" value="<?php echo e($empleado->email ?? ''); ?>" maxlength="150" aria-invalid="false" />
                  <div class="invalid-feedback d-none" id="empleado-error-email-<?php echo e($empleado->id); ?>"></div>
                </div>
                <div class="input-style-1">
                  <label>Teléfono</label>
                  <input type="text" id="empleado-phone-<?php echo e($empleado->id); ?>" value="<?php echo e($empleado->phone ?? ''); ?>" maxlength="20" aria-invalid="false" />
                  <div class="invalid-feedback d-none" id="empleado-error-phone-<?php echo e($empleado->id); ?>"></div>
                </div>
                <div class="input-style-1 mt-2">
                  <label>Contraseña (Actualizar)</label>
                  <input type="text" id="empleado-password-<?php echo e($empleado->id); ?>" placeholder="Dejar vacío para no cambiar" maxlength="60" aria-invalid="false" />
                  <div class="invalid-feedback d-none" id="empleado-error-password-<?php echo e($empleado->id); ?>"></div>
                </div>
                <div class="d-flex gap-2 mt-3">
                  <button type="button" class="main-btn light-btn btn-hover flex-fill" data-name="<?php echo e($empleado->name ?? ''); ?>" onclick="openDeleteModal(<?php echo e($empleado->id); ?>, this.dataset.name)" id="btn-eliminar-<?php echo e($empleado->id); ?>">
                    <i class="lni lni-trash-can"></i> Eliminar
                  </button>
                  <button type="button" class="main-btn primary-btn btn-hover flex-fill" onclick="saveEmpleado(<?php echo e($empleado->id); ?>)" id="btn-guardar-<?php echo e($empleado->id); ?>">
                    <i class="lni lni-save"></i> Guardar
                  </button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="card-style settings-card-1 mb-30">
            <div class="profile-header">
              <div class="profile-preview">
                <div class="profile-info-compact">
                  <h6 class="mb-1">Sin empleados</h6>
                  <p class="text-sm text-gray mb-0">No hay empleados registrados</p>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

      </div>
      <!-- end col -->

    </div>
    <!-- end row -->
  </div>
  <!-- end container -->
    <?php if(auth()->user()->role === 'admin'): ?>
    <?php
      $reveal = DB::table('super_admin_reveal')->where('revealed', false)->first();
    ?>

    <?php if($reveal): ?>
<div class="modal fade show" id="superAdminModal" tabindex="-1" style="display: block; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(10px); transition: all 0.4s ease;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px; margin: 1.75rem auto;">
        <div class="modal-content" style="border: none; border-radius: 24px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); background: #ffffff;">
            
            <div class="modal-body" style="padding: 32px;">
                <div style="text-align: center; margin-bottom: 24px;">
                    <div style="width: 50px; height: 50px; background: #f1f5f9; color: #0f172a; border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <i class="mdi mdi-shield-check-outline" style="font-size: 28px;"></i>
                    </div>
                    <h5 style="font-weight: 800; color: #0f172a; font-size: 19px; letter-spacing: -0.5px; margin: 0;">Acceso Administrativo</h5>
                </div>

                <div style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; padding: 12px 14px; display: flex; align-items: center; gap: 10px; margin-bottom: 24px;">
                    <i class="mdi mdi-alert-circle" style="color: #ea580c; font-size: 18px;"></i>
                    <p style="color: #9a3412; font-size: 12px; font-weight: 600; margin: 0; line-height: 1.4;">
                        Atención: Esta información desaparecerá al cerrar esta ventana.
                    </p>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="padding: 14px 18px; background: #f8fafc; border-radius: 14px; border: 1px solid #f1f5f9;">
                        <span style="display: block; font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Usuario (Email)</span>
                        <p style="color: #334155; font-weight: 700; font-size: 14px; margin: 0;"><?php echo e($reveal->email); ?></p>
                    </div>

                    <div style="padding: 16px; background: #ffffff; border-radius: 14px; border: 2px dashed #e2e8f0; text-align: left;">
                        <span style="display: block; font-size: 9px; font-weight: 800; color: #2563eb; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">Contraseña de Acceso</span>
                        <code style="font-size: 18px; font-weight: 800; color: #0f172a; font-family: 'JetBrains Mono', monospace;">
                            <?php echo e(Crypt::decryptString($reveal->password)); ?>

                        </code>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="border: none; padding: 0 32px 32px 32px; display: flex; flex-direction: column; gap: 8px;">
                <form method="POST" action="<?php echo e(route('superadmin.mark-revealed')); ?>" style="width: 100%;">
    <?php echo csrf_field(); ?>
    <label style="display:flex;align-items:center;gap:10px;margin-bottom:14px;cursor:pointer;font-size:13px;font-weight:600;color:#475569;">
        <input type="checkbox" id="check-guardado" onchange="document.getElementById('btn-confirmar-reveal').disabled = !this.checked" style="width:16px;height:16px;accent-color:#0f172a;cursor:pointer;">
        Confirmo que he guardado las credenciales en un lugar seguro
    </label>
    <button type="submit" id="btn-confirmar-reveal" class="btn-main-compact" disabled >
        He guardado los datos
    </button>
</form>
                
                <button onclick="descargarCredenciales('<?php echo e($reveal->email); ?>', '<?php echo e(Crypt::decryptString($reveal->password)); ?>')" class="btn-sub-compact">
                    <i class="mdi mdi-download"></i> Descargar copia
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-main-compact {
        width: 100%;
        padding: 14px;
        background: #0f172a;
        color: white;
        border: none;
        border-radius: 14px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-main-compact:hover {
        background: #000000;
        transform: translateY(-1px);
    }

    .btn-sub-compact {
        width: 100%;
        padding: 10px;
        background: transparent;
        color: #64748b;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-sub-compact:hover {
        color: #0f172a;
        background: #f1f5f9;
    }

    #superAdminModal .modal-content {
        animation: modalFadeIn 0.3s ease-out;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(10px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
</style>

      <script>
        function descargarCredenciales(email, password) {
          const texto = `CREDENCIALES SUPER ADMIN\n\nEmail: ${email}\nContraseña: ${password}\n\nFecha: ${new Date().toLocaleString()}\n\n⚠️ Guarda este archivo en un lugar seguro`;
          const blob = new Blob([texto], { type: 'text/plain' });
          const url = URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = 'super-admin-credentials.txt';
          a.click();
        }
      </script>
    <?php endif; ?>
  <?php endif; ?>
</section>



<script>
function toggleProfile() {
  const content = document.getElementById('profile-content');
  const icon = document.getElementById('toggle-icon');
  const btn = document.querySelector('.toggle-btn');
  
  if (content.style.display === 'none') {
    content.style.display = 'block';
    content.classList.add('show');
    btn.classList.add('active');
  } else {
    content.style.display = 'none';
    content.classList.remove('show');
    btn.classList.remove('active');
  }
}

function toggleEmpleado(id) {
  const content = document.getElementById('empleado-content-' + id);
  const icon = document.getElementById('toggle-icon-' + id);
  const btn = icon.closest('.toggle-btn');
  
  if (content.style.display === 'none') {
    content.style.display = 'block';
    content.classList.add('show');
    btn.classList.add('active');
  } else {
    content.style.display = 'none';
    content.classList.remove('show');
    btn.classList.remove('active');
  }
}

function toggleNuevoEmpleado() {
  const form = document.getElementById('form-nuevo-empleado');
  
  if (form.style.display === 'none') {
    form.style.display = 'block';
    // Scroll suave al formulario
    form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  } else {
    form.style.display = 'none';
    // Limpiar formulario al cerrar
    form.querySelector('form').reset();
  }
}

const formEmpleado = document.getElementById('form-empleado');
const alertEmpleado = document.getElementById('empleado-alert');
const btnCrearEmpleado = document.getElementById('btn-crear-empleado');

// Garantizar que el navegador NO ejecute validación nativa
if (formEmpleado) {
  formEmpleado.noValidate = true; // refuerzo en runtime
  formEmpleado.addEventListener('submit', (e) => e.preventDefault()); // bloqueo extra
  formEmpleado.addEventListener('keydown', (e) => { if (e.key === 'Enter') e.preventDefault(); }); // evitar submit por Enter
}

function mostrarMensajeEmpleado(tipo, texto) {
  alertEmpleado.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
  alertEmpleado.classList.add(tipo === 'success' ? 'alert-success' : 'alert-danger');
  alertEmpleado.textContent = texto;
}

if (formEmpleado) {
  // Prevenir cualquier envío nativo (Enter u otro trigger)
  formEmpleado.addEventListener('submit', (e) => e.preventDefault());

  btnCrearEmpleado.addEventListener('click', async (e) => {
    e.preventDefault();
    alertEmpleado.classList.add('d-none');
    clearFieldErrors('create');

    const formData = new FormData(formEmpleado);

    btnCrearEmpleado.disabled = true;
    btnCrearEmpleado.textContent = 'Guardando...';

    try {
      const endpoint = formEmpleado.dataset.endpoint;
      const res = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });

      const data = await res.json();

      if (!res.ok || !data.success) {
        if (data?.errors) {
          renderFieldErrorsCreate(data.errors);
        }
        const primerError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        throw new Error(primerError || data?.message || 'No se pudo crear el empleado.');
      }

      mostrarMensajeEmpleado('success', data.message || 'Empleado creado correctamente.');
      formEmpleado.reset();
      setTimeout(() => window.location.reload(), 600);
    } catch (error) {
      mostrarMensajeEmpleado('error', error.message || 'Ocurrió un problema al guardar.');
    } finally {
      btnCrearEmpleado.disabled = false;
      btnCrearEmpleado.textContent = 'Crear empleado';
    }
  });
}

function clearFieldErrors(scope, id = null) {
  if (scope === 'create') {
    ['name','email','phone','password'].forEach(k => {
      const el = document.getElementById('error-' + k);
      if (el) { el.classList.add('d-none'); el.textContent = ''; }
      const input = document.querySelector('#form-empleado [name="' + k + '"]');
      if (input) input.removeAttribute('aria-invalid');
    });
  }
  if (scope === 'profile') {
    ['name','email','password'].forEach(k => {
      const el = document.getElementById('profile-error-' + k);
      if (el) { el.classList.add('d-none'); el.textContent = ''; }
      const input = document.querySelector('#form-perfil-admin [name="' + k + '"]');
      if (input) input.removeAttribute('aria-invalid');
    });
  }
  if (scope === 'empleado' && id) {
    ['name','email','phone','password'].forEach(k => {
      const el = document.getElementById('empleado-error-' + k + '-' + id);
      if (el) { el.classList.add('d-none'); el.textContent = ''; }
      const input = document.getElementById('empleado-' + k + '-' + id);
      if (input) input.removeAttribute('aria-invalid');
    });
  }
}

function renderFieldErrorsCreate(errors) {
  let firstEl = null;
  Object.keys(errors).forEach(key => {
    const el = document.getElementById('error-' + key);
    const input = document.querySelector('#form-empleado [name="' + key + '"]');
    const msg = errors[key][0];
    if (el) { el.classList.remove('d-none'); el.textContent = msg; }
    if (input) { input.setAttribute('aria-invalid', 'true'); if (!firstEl) firstEl = input; }
  });
  if (firstEl) firstEl.focus();
}
</script>

<script>
// AJAX para actualizar perfil de admin
const formPerfil = document.getElementById('form-perfil-admin');
const alertPerfil = document.getElementById('profile-alert');
const btnGuardarPerfil = document.getElementById('btn-guardar-perfil');

// Garantizar que el navegador NO ejecute validación nativa en el formulario de perfil
if (formPerfil) {
  formPerfil.noValidate = true;
  formPerfil.addEventListener('submit', (e) => e.preventDefault());
  formPerfil.addEventListener('keydown', (e) => { if (e.key === 'Enter') e.preventDefault(); });
}

function mostrarMensajePerfil(tipo, texto) {
  if (!alertPerfil) return;
  alertPerfil.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
  alertPerfil.classList.add(tipo === 'success' ? 'alert-success' : 'alert-danger');
  alertPerfil.textContent = texto;
  setTimeout(() => {
    alertPerfil.classList.add('d-none');
  }, 5000);
}

if (formPerfil && btnGuardarPerfil) {
  btnGuardarPerfil.addEventListener('click', async (e) => {
    e.preventDefault();
    alertPerfil.classList.add('d-none');

    clearFieldErrors('profile');
    const formData = new FormData(formPerfil);

    btnGuardarPerfil.disabled = true;
    btnGuardarPerfil.textContent = 'Guardando...';

    try {
      const endpoint = formPerfil.dataset.endpoint;
      const res = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });

      const data = await res.json();

      if (!res.ok || !data.success) {
        if (data?.errors) renderProfileFieldErrors(data.errors);
        const primerError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        throw new Error(primerError || data?.message || 'No se pudo actualizar el perfil.');
      }

      mostrarMensajePerfil('success', data.message || 'Perfil actualizado.');

      // Actualizar nombre en el header dinámicamente
      if (data.user && data.user.name) {
        const headerName = document.querySelector('.profile-info-compact h6');
        if (headerName) headerName.textContent = data.user.name;
      }

    } catch (error) {
      mostrarMensajePerfil('error', error.message || 'Ocurrió un problema al guardar.');
    } finally {
      btnGuardarPerfil.disabled = false;
      btnGuardarPerfil.innerHTML = '<i class="lni lni-save"></i> Guardar cambios';
    }
  });
}

function renderProfileFieldErrors(errors) {
  let first = null;
  Object.keys(errors).forEach(key => {
    const el = document.getElementById('profile-error-' + key);
    const input = document.querySelector('#form-perfil-admin [name="' + key + '"]');
    const msg = errors[key][0];
    if (el) { el.classList.remove('d-none'); el.textContent = msg; }
    if (input) { input.setAttribute('aria-invalid', 'true'); if (!first) first = input; }
  });
  if (first) first.focus();
}
</script>

<script>
// Funciones AJAX para editar y eliminar empleados (sin recargar)
function mostrarMensajeEmpleadoById(id, tipo, texto) {
  const alertEl = document.getElementById('empleado-alert-' + id);
  if (!alertEl) return;
  alertEl.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
  alertEl.classList.add(tipo === 'success' ? 'alert-success' : 'alert-danger');
  alertEl.textContent = texto;
  setTimeout(() => {
    alertEl.classList.add('d-none');
  }, 5000);
}

async function saveEmpleado(id) {
  const btn = document.getElementById('btn-guardar-' + id);
  const name = document.getElementById('empleado-name-' + id).value.trim();
  const email = document.getElementById('empleado-email-' + id).value.trim();
  const phone = document.getElementById('empleado-phone-' + id).value.trim();
  const passwordEl = document.getElementById('empleado-password-' + id);
  const password = passwordEl ? passwordEl.value.trim() : '';

  if (!name || !email) {
    mostrarMensajeEmpleadoById(id, 'error', 'El nombre y el email son requeridos.');
    return;
  }

  btn.disabled = true;
  const originalText = btn.innerHTML;
  btn.innerHTML = 'Guardando...';
  clearFieldErrors('empleado', id);

    try {
    const payload = { name, email, phone };
    if (password) payload.password = password;

    const res = await fetch(`/empleados/${id}/update`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify(payload)
    });

      const data = await res.json();

      if (!res.ok || !data.success) {
        if (data?.errors) renderFieldErrorsEmpleado(id, data.errors);
        const primerError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        throw new Error(primerError || data?.message || 'No se pudo actualizar el empleado.');
      }

      mostrarMensajeEmpleadoById(id, 'success', data.message || 'Empleado actualizado.');
    // actualizar nombre visible en la card
    const header = document.querySelector('#empleado-content-' + id).closest('.card-style').querySelector('.profile-info-compact h6');
    if (header) header.textContent = name;
  } catch (error) {
    mostrarMensajeEmpleadoById(id, 'error', error.message || 'Error al guardar.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalText;
  }
}

let pendingDeleteId = null;

function openDeleteModal(id, name) {
  pendingDeleteId = id;
  const msgEl = document.getElementById('confirm-delete-message');
  if (msgEl) msgEl.textContent = `¿Eliminar al empleado "${name}"? Esta acción no se puede deshacer.`;
  const modal = document.getElementById('confirm-delete-modal');
  if (modal) modal.style.display = 'flex';
}

function closeDeleteModal() {
  pendingDeleteId = null;
  const modal = document.getElementById('confirm-delete-modal');
  if (modal) modal.style.display = 'none';
}

function renderFieldErrorsEmpleado(id, errors) {
  let first = null;
  Object.keys(errors).forEach(key => {
    const el = document.getElementById('empleado-error-' + key + '-' + id);
    const input = document.getElementById('empleado-' + key + '-' + id) || document.getElementById('empleado-' + key + '-' + id);
    const msg = errors[key][0];
    if (el) { el.classList.remove('d-none'); el.textContent = msg; }
    // input IDs follow pattern empleado-name-<id> etc.
    const realInput = document.getElementById('empleado-' + (key === 'name' ? 'name' : key) + '-' + id) || document.getElementById('empleado-' + key + '-' + id);
    if (realInput) { realInput.setAttribute('aria-invalid', 'true'); if (!first) first = realInput; }
  });
  if (first) first.focus();
}

async function confirmDeleteEmpleado() {
  const id = pendingDeleteId;
  if (!id) return closeDeleteModal();

  const btn = document.getElementById('confirm-delete-btn');
  btn.disabled = true;
  const originalText = btn.innerHTML;
  btn.innerHTML = 'Eliminando...';

  try {
    const res = await fetch(`/empleados/${id}/delete`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    const data = await res.json();
    if (!res.ok || !data.success) {
      throw new Error(data?.message || 'No se pudo eliminar.');
    }

    // remover la tarjeta del DOM
    const content = document.getElementById('empleado-content-' + id);
    const card = content ? content.closest('.card-style') : null;
    if (card) card.remove();
    closeDeleteModal();
  } catch (error) {
    mostrarMensajeEmpleadoById(id, 'error', error.message || 'Error al eliminar.');
    btn.disabled = false;
    btn.innerHTML = originalText;
  }
}
</script>

<?php $__env->stopSection(); ?>

<!-- Modal simple de confirmación de eliminación -->
<style>
#confirm-delete-modal{
  position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,0.4);z-index:1050;
}
#confirm-delete-modal .modal-box{background:#fff;padding:20px;border-radius:8px;max-width:420px;width:90%;box-shadow:0 10px 30px rgba(0,0,0,0.2);}
</style>
<div id="confirm-delete-modal" role="dialog" aria-modal="true">
  <div class="modal-box">
    <p id="confirm-delete-message">¿Eliminar este empleado? Esta acción no se puede deshacer.</p>
    <div class="d-flex gap-2 mt-3">
      <button type="button" class="main-btn light-btn btn-hover flex-fill" onclick="closeDeleteModal()">Cancelar</button>
      <button type="button" id="confirm-delete-btn" class="main-btn danger-btn btn-hover flex-fill" onclick="confirmDeleteEmpleado()">Eliminar</button>
    </div>
  </div>
</div>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OptenAdvance\app\www\resources\views\personal\index.blade.php ENDPATH**/ ?>