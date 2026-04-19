@extends('layouts.app')

@section('title', 'Configuración de devoluciones')

@section('content')
<section class="section">
    <div class="container-fluid">
        <div class="title-wrapper pt-30">
            
        </div>

        <div class="row mt-20">

            {{-- LEFT: Días de devolución --}}
            <div class="col-lg-5">
                <div class="card-style mb-30">
                    <div class="d-flex align-items-center gap-2 mb-20">
                        <i class="lni lni-calendar" style="color:#3b82f6;font-size:20px;"></i>
                        <h6 class="mb-0">Plazo de devolución</h6>
                    </div>
                    <p class="text-sm text-gray mb-20">Número de días que tiene el cliente para realizar una devolución después de la compra.</p>

                    <div class="d-flex align-items-center gap-3">
                        <input type="number" id="dias_devolucion" class="form-control" 
                            value="{{ $diasDevolucion }}" min="1" max="365"
                            style="max-width:100px;font-size:18px;font-weight:700;text-align:center;">
                        <span class="text-sm text-gray">días</span>
                        <button onclick="guardarDias()" class="main-btn primary-btn btn-hover btn-sm">
                            Guardar
                        </button>
                    </div>
                    <div id="msg-dias" class="mt-2 text-sm d-none"></div>
                </div>
            </div>

            {{-- RIGHT: Motivos --}}
            <div class="col-lg-7">
                <div class="card-style mb-30">
                    <div class="d-flex align-items-center justify-content-between mb-20">
                        <div class="d-flex align-items-center gap-2">
                            <i class="lni lni-list" style="color:#f59e0b;font-size:20px;"></i>
                            <h6 class="mb-0">Motivos de devolución</h6>
                        </div>
                        <button onclick="abrirNuevoMotivo()" class="main-btn primary-btn btn-hover btn-sm">
                            <i class="lni lni-plus me-1"></i> Nuevo
                        </button>
                    </div>

                    {{-- Form nuevo motivo --}}
                    <div id="form-nuevo-motivo" class="d-none mb-20" style="background:#f8fafc;border-radius:10px;padding:14px;">
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" id="nuevo-motivo-nombre" class="form-control form-control-sm" 
                                placeholder="Nombre del motivo..." maxlength="100">
                            <button onclick="guardarNuevoMotivo()" class="main-btn primary-btn btn-hover btn-sm" style="white-space:nowrap;">
                                <span id="btn-nuevo-texto">Guardar</span>
                            </button>
                            <button onclick="cerrarNuevoMotivo()" class="btn btn-light btn-sm">
                                <i class="lni lni-close"></i>
                            </button>
                        </div>
                        <div id="error-nuevo-motivo" class="text-danger small mt-1 d-none"></div>
                    </div>

                    {{-- Lista de motivos --}}
                    <div id="lista-motivos">
                        @forelse($motivos as $motivo)
                        <div class="motivo-item d-flex align-items-center justify-content-between gap-2 py-2" 
                             style="border-bottom:1px solid #f1f5f9;" id="motivo-{{ $motivo->id }}">
                            
                            {{-- Vista normal --}}
                            <div class="d-flex align-items-center gap-2 flex-fill vista-normal-{{ $motivo->id }}">
                                <span class="text-sm {{ !$motivo->activo ? 'text-gray' : '' }}" 
                                      style="{{ !$motivo->activo ? 'text-decoration:line-through;' : '' }}">
                                    {{ $motivo->nombre }}
                                </span>
                                @if(!$motivo->activo)
                                    <span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:20px;font-size:11px;">Inactivo</span>
                                @endif
                            </div>

                            {{-- Vista edición --}}
                            <div class="d-none flex-fill vista-edicion-{{ $motivo->id }}">
                                <input type="text" class="form-control form-control-sm" 
                                    id="editar-nombre-{{ $motivo->id }}" value="{{ $motivo->nombre }}" maxlength="100">
                                <div id="error-editar-{{ $motivo->id }}" class="text-danger small mt-1 d-none"></div>
                            </div>

                            {{-- Botones --}}
                            <div class="d-flex gap-1 botones-normal-{{ $motivo->id }}">
                                <button onclick="activarEdicion({{ $motivo->id }})" 
                                    class="btn btn-light btn-sm" title="Editar">
                                    <i class="lni lni-pencil"></i>
                                </button>
                                <button onclick="toggleMotivo({{ $motivo->id }}, {{ $motivo->activo ? 'true' : 'false' }})" 
                                    class="btn btn-light btn-sm" title="{{ $motivo->activo ? 'Desactivar' : 'Activar' }}">
                                    <i class="lni {{ $motivo->activo ? 'lni-eye' : 'lni-eye' }}"></i>
                                </button>
                                <button onclick="eliminarMotivo({{ $motivo->id }})" 
                                    class="btn btn-light btn-sm text-danger" title="Eliminar">
                                    <i class="lni lni-trash-can"></i>
                                </button>
                            </div>

                            <div class="d-none gap-1 botones-edicion-{{ $motivo->id }}">
                                <button onclick="guardarEdicion({{ $motivo->id }})" 
                                    class="btn btn-sm main-btn primary-btn btn-hover">
                                    <i class="lni lni-checkmark"></i>
                                </button>
                                <button onclick="cancelarEdicion({{ $motivo->id }})" 
                                    class="btn btn-light btn-sm">
                                    <i class="lni lni-close"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray text-center py-3" id="sin-motivos">No hay motivos registrados</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- Modal eliminar motivo --}}
<div class="modal fade" id="modal-eliminar-motivo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
    <div class="modal-content">
      <div class="modal-body px-4 py-4 text-center">
        <div style="width:64px;height:64px;background:#fee2e2;border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;">
          <i class="lni lni-trash-can" style="font-size:28px;color:#dc2626;"></i>
        </div>
        <h5 class="fw-semibold mb-2">¿Eliminar motivo?</h5>
        <p class="text-sm text-gray mb-0">Esta acción no se puede deshacer.</p>
      </div>
      <div class="modal-footer px-4 py-3 justify-content-center gap-3">
        <button type="button" class="main-btn light-btn btn-hover" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" id="btn-confirmar-eliminar" class="main-btn danger-btn btn-hover">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ===== DÍAS =====
async function guardarDias() {
    const dias = document.getElementById('dias_devolucion').value;
    const msg = document.getElementById('msg-dias');

    try {
        const res = await fetch('{{ route("configuracion.dias") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ dias_devolucion: parseInt(dias) })
        });
        const data = await res.json();
        msg.className = 'mt-2 text-sm ' + (data.success ? 'text-success' : 'text-danger');
        msg.textContent = data.success ? '✓ Guardado' : (data.message || 'Error');
        msg.classList.remove('d-none');
        setTimeout(() => msg.classList.add('d-none'), 2000);
    } catch(e) {
        msg.className = 'mt-2 text-sm text-danger';
        msg.textContent = 'Error de conexión';
        msg.classList.remove('d-none');
    }
}

// ===== NUEVO MOTIVO =====
function abrirNuevoMotivo() {
    document.getElementById('form-nuevo-motivo').classList.remove('d-none');
    document.getElementById('nuevo-motivo-nombre').focus();
}
function cerrarNuevoMotivo() {
    document.getElementById('form-nuevo-motivo').classList.add('d-none');
    document.getElementById('nuevo-motivo-nombre').value = '';
    document.getElementById('error-nuevo-motivo').classList.add('d-none');
}

async function guardarNuevoMotivo() {
    const nombre = document.getElementById('nuevo-motivo-nombre').value.trim();
    const error = document.getElementById('error-nuevo-motivo');
    const btn = document.getElementById('btn-nuevo-texto');

    if (!nombre) { error.textContent = 'El nombre es obligatorio'; error.classList.remove('d-none'); return; }

    btn.textContent = 'Guardando...';
    try {
        const res = await fetch('{{ route("motivos-devolucion.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ nombre })
        });
        const data = await res.json();
        if (data.success) {
            agregarMotivoDOM(data.motivo);
            cerrarNuevoMotivo();
            document.getElementById('sin-motivos')?.remove();
        } else {
            error.textContent = data.errors?.nombre?.[0] || 'Error al guardar';
            error.classList.remove('d-none');
        }
    } catch(e) {
        error.textContent = 'Error de conexión';
        error.classList.remove('d-none');
    } finally {
        btn.textContent = 'Guardar';
    }
}

function agregarMotivoDOM(motivo) {
    const lista = document.getElementById('lista-motivos');
    const div = document.createElement('div');
    div.className = 'motivo-item d-flex align-items-center justify-content-between gap-2 py-2';
    div.style.borderBottom = '1px solid #f1f5f9';
    div.id = 'motivo-' + motivo.id;
    div.innerHTML = `
        <div class="d-flex align-items-center gap-2 flex-fill vista-normal-${motivo.id}">
            <span class="text-sm">${motivo.nombre}</span>
        </div>
        <div class="d-none flex-fill vista-edicion-${motivo.id}">
            <input type="text" class="form-control form-control-sm" id="editar-nombre-${motivo.id}" value="${motivo.nombre}" maxlength="100">
            <div id="error-editar-${motivo.id}" class="text-danger small mt-1 d-none"></div>
        </div>
        <div class="d-flex gap-1 botones-normal-${motivo.id}">
            <button onclick="activarEdicion(${motivo.id})" class="btn btn-light btn-sm"><i class="lni lni-pencil"></i></button>
            <button onclick="toggleMotivo(${motivo.id}, true)" class="btn btn-light btn-sm"><i class="lni lni-eye"></i></button>
            <button onclick="eliminarMotivo(${motivo.id})" class="btn btn-light btn-sm text-danger"><i class="lni lni-trash-can"></i></button>
        </div>
        <div class="d-none gap-1 botones-edicion-${motivo.id}">
            <button onclick="guardarEdicion(${motivo.id})" class="btn btn-sm main-btn primary-btn btn-hover"><i class="lni lni-checkmark"></i></button>
            <button onclick="cancelarEdicion(${motivo.id})" class="btn btn-light btn-sm"><i class="lni lni-close"></i></button>
        </div>
    `;
    lista.appendChild(div);
}

// ===== EDICIÓN =====
function activarEdicion(id) {
    document.querySelector(`.vista-normal-${id}`).classList.add('d-none');
    document.querySelector(`.vista-edicion-${id}`).classList.remove('d-none');
    document.querySelector(`.botones-normal-${id}`).classList.add('d-none');
    document.querySelector(`.botones-edicion-${id}`).classList.remove('d-none');
    document.getElementById(`editar-nombre-${id}`).focus();
}
function cancelarEdicion(id) {
    document.querySelector(`.vista-normal-${id}`).classList.remove('d-none');
    document.querySelector(`.vista-edicion-${id}`).classList.add('d-none');
    document.querySelector(`.botones-normal-${id}`).classList.remove('d-none');
    document.querySelector(`.botones-edicion-${id}`).classList.add('d-none');
    document.getElementById(`error-editar-${id}`).classList.add('d-none');
}

async function guardarEdicion(id) {
    const nombre = document.getElementById(`editar-nombre-${id}`).value.trim();
    const error = document.getElementById(`error-editar-${id}`);

    if (!nombre) { error.textContent = 'El nombre es obligatorio'; error.classList.remove('d-none'); return; }

    try {
        const res = await fetch(`/ajustes/motivos-devolucion/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ nombre })
        });
        const data = await res.json();
        if (data.success) {
            document.querySelector(`.vista-normal-${id} span.text-sm`).textContent = nombre;
            cancelarEdicion(id);
        } else {
            error.textContent = data.errors?.nombre?.[0] || 'Error al guardar';
            error.classList.remove('d-none');
        }
    } catch(e) {
        error.textContent = 'Error de conexión';
        error.classList.remove('d-none');
    }
}

// ===== TOGGLE =====
async function toggleMotivo(id, activo) {
    try {
        const res = await fetch(`/ajustes/motivos-devolucion/${id}/toggle`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            const span = document.querySelector(`#motivo-${id} .vista-normal-${id} span.text-sm`);
            const badge = document.querySelector(`#motivo-${id} .vista-normal-${id} span[style*="background"]`);
            if (data.activo) {
                span.style.textDecoration = '';
                span.classList.remove('text-gray');
                badge?.remove();
            } else {
                span.style.textDecoration = 'line-through';
                span.classList.add('text-gray');
                if (!badge) {
                    span.insertAdjacentHTML('afterend', '<span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:20px;font-size:11px;">Inactivo</span>');
                }
            }
        }
    } catch(e) {}
}

// ===== ELIMINAR =====
let _eliminarId = null;

function eliminarMotivo(id) {
    _eliminarId = id;
    const modal = new bootstrap.Modal(document.getElementById('modal-eliminar-motivo'));
    modal.show();
}

document.getElementById('btn-confirmar-eliminar').addEventListener('click', async function () {
    const id = _eliminarId;
    if (!id) return;

    const modal = bootstrap.Modal.getInstance(document.getElementById('modal-eliminar-motivo'));
    modal.hide();

    try {
        const res = await fetch(`/ajustes/motivos-devolucion/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById(`motivo-${id}`).remove();
            if (!document.querySelector('.motivo-item')) {
                document.getElementById('lista-motivos').innerHTML = '<p class="text-sm text-gray text-center py-3" id="sin-motivos">No hay motivos registrados</p>';
            }
        }
    } catch(e) {}

    _eliminarId = null;
});
</script>
@endsection