@extends('layouts.app')

@section('title', 'Empleados')

@section('content')
<br><br>
<section class="section">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          
        </div>
        <!-- end col -->
        <div class="col-md-6">
          <div class="breadcrumb-wrapper">
           
          </div>
        </div>
        <!-- end col -->
      </div>
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
                  <h6 class="mb-1">{{ auth()->user()->name ?? 'Administrador' }}</h6>
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

              <form id="form-perfil-admin" action="{{ route('perfil.admin.update') }}" method="POST">
                @csrf
                <div class="input-style-1">
                  <label>Nombre</label>
                  <input type="text" name="name" value="{{ auth()->user()->name ?? '' }}" />
                </div>
                <div class="input-style-1">
                  <label>Usuario</label>
                  <input type="text" name="username" value="{{ auth()->user()->username ?? '' }}" />
                </div>
                <div class="input-style-1">
                  <label>Email</label>
                  <input type="email" name="email" value="{{ auth()->user()->email ?? '' }}" />
                </div>
                <div class="input-style-1">
                  <label>Contraseña (actualizar)</label>
                  <input type="text" name="password" placeholder="Dejar vacío para no cambiar" />
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
          <form action="{{ route('personal.store') }}" method="POST" id="form-empleado">
            @csrf
            <div class="row">
              <div class="col-12">
                <div class="input-style-1">
                  <label>Nombre de Usuario</label>
                  <input type="text" name="name" placeholder="Nombre de Usuario" />
                </div>
              </div>
              <div class="col-12">
                <div class="input-style-1">
                  <label>Email</label>
                  <input type="email" name="email" placeholder="email@ejemplo.com" />
                </div>
              </div>
              <div class="col-12">
                <div class="input-style-1">
                  <label>Teléfono</label>
                  <input type="text" name="phone" placeholder="+57 300 000 0000" />
                </div>
              </div>
              <div class="col-12">
                <div class="input-style-1">
                  <label>Contraseña inicial</label>
                  <input type="password" name="password" placeholder="Mínimo 8 caracteres" />
                </div>
              </div>
              <div class="col-12">
                <div class="d-flex gap-2">
                  <button type="button" class="main-btn light-btn btn-hover flex-fill" onclick="toggleNuevoEmpleado()">
                    Cancelar
                  </button>
                  <button type="submit" class="main-btn primary-btn btn-hover flex-fill" id="btn-crear-empleado">
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
        
        @forelse($empleados as $empleado)
          <div class="card-style settings-card-1 mb-30">
            <div class="profile-header" onclick="toggleEmpleado({{ $empleado->id }})">
              <div class="profile-preview">
                
                <div class="profile-image-small">
                  
                  <img src="assets/images/profile/empleado.png" alt="" />
                  
                </div>
                <div class="profile-info-compact">
                  <h6 class="mb-1">{{ $empleado->name ?? $empleado->username }}</h6>
                  <p class="text-sm text-gray mb-0">Empleado</p>
                </div>
              </div>
              <button class="toggle-btn" type="button">
                <i class="lni lni-chevron-down" id="toggle-icon-{{ $empleado->id }}"></i>
              </button>
            </div>

            <div class="profile-content" id="empleado-content-{{ $empleado->id }}" style="display: none;">
              <div class="profile-info">
                <div id="empleado-alert-{{ $empleado->id }}" class="alert d-none" role="alert"></div>

                <div class="input-style-1">
                  <label>Usuario</label>
                  <input type="text" id="empleado-name-{{ $empleado->id }}" value="{{ $empleado->name ?? '' }}" />
                </div>
                <div class="input-style-1">
                  <label>Email</label>
                  <input type="email" id="empleado-email-{{ $empleado->id }}" value="{{ $empleado->email ?? '' }}" />
                </div>
                <div class="input-style-1">
                  <label>Teléfono</label>
                  <input type="text" id="empleado-phone-{{ $empleado->id }}" value="{{ $empleado->phone ?? '' }}" />
                </div>
                <div class="d-flex gap-2 mt-3">
                  <button type="button" class="main-btn light-btn btn-hover flex-fill" onclick="saveEmpleado({{ $empleado->id }})" id="btn-guardar-{{ $empleado->id }}">
                    <i class="lni lni-save"></i> Guardar
                  </button>
                  <button type="button" class="main-btn danger-btn btn-hover flex-fill" data-name="{{ $empleado->name ?? '' }}" onclick="openDeleteModal({{ $empleado->id }}, this.dataset.name)" id="btn-eliminar-{{ $empleado->id }}">
                    <i class="lni lni-trash-can"></i> Eliminar
                  </button>
                </div>
              </div>
            </div>
          </div>
        @empty
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
        @endforelse

      </div>
      <!-- end col -->

    </div>
    <!-- end row -->
  </div>
  <!-- end container -->
</section>

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

function mostrarMensajeEmpleado(tipo, texto) {
  alertEmpleado.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
  alertEmpleado.classList.add(tipo === 'success' ? 'alert-success' : 'alert-danger');
  alertEmpleado.textContent = texto;
}

if (formEmpleado) {
  formEmpleado.addEventListener('submit', async (e) => {
    e.preventDefault();
    alertEmpleado.classList.add('d-none');

    const formData = new FormData(formEmpleado);

    btnCrearEmpleado.disabled = true;
    btnCrearEmpleado.textContent = 'Guardando...';

    try {
      const res = await fetch(formEmpleado.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        },
        body: formData
      });

      const data = await res.json();

      if (!res.ok || !data.success) {
        const primerError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        throw new Error(primerError || data?.message || 'Ups, no pude guardar el empleado.');
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
</script>

<script>
// AJAX para actualizar perfil de admin
const formPerfil = document.getElementById('form-perfil-admin');
const alertPerfil = document.getElementById('profile-alert');
const btnGuardarPerfil = document.getElementById('btn-guardar-perfil');

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

    const formData = new FormData(formPerfil);

    btnGuardarPerfil.disabled = true;
    btnGuardarPerfil.textContent = 'Guardando...';

    try {
      const res = await fetch(formPerfil.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        },
        body: formData
      });

      const data = await res.json();

      if (!res.ok || !data.success) {
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

  if (!name || !email) {
    mostrarMensajeEmpleadoById(id, 'error', 'El nombre y el email son requeridos.');
    return;
  }

  btn.disabled = true;
  const originalText = btn.innerHTML;
  btn.innerHTML = 'Guardando...';

  try {
    const res = await fetch(`/empleados/${id}/update`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ name, email, phone })
    });

    const data = await res.json();

    if (!res.ok || !data.success) {
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
        'Accept': 'application/json'
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

@endsection

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
