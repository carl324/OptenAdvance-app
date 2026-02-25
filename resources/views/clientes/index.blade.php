@extends('layouts.app')

@section('title', 'Clientes')

@section('content')

<section class="table-components">
  <div class="container-fluid">

    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="title">
            <h2>Clientes</h2>
          </div>
        </div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ventas.create') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Clientes</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <div class="tables-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <div class="card-style mb-30">

            {{-- Barra superior --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
              <div class="input-style-1" style="max-width:320px; margin-bottom:0;">
                <input
                  id="buscador"
                  type="text"
                  placeholder="Buscar por nombre, NIT o teléfono..."
                  value="{{ request('search') }}"
                  style="padding-right: 36px;"
                />
                <span class="icon"><i class="lni lni-search-alt"></i></span>
              </div>
              <button class="main-btn primary-btn btn-hover" onclick="abrirModalCrear()">
                <i class="lni lni-plus me-2"></i> Nuevo cliente
              </button>
            </div>

            {{-- Tabla --}}
            <div class="table-wrapper table-responsive">
              <table class="table" id="tabla-clientes">
                <thead>
                  <tr>
                    <th><h6>Nombre</h6></th>
                    <th><h6>Teléfono</h6></th>
                    <th><h6>NIT / CI</h6></th>
                    <th><h6>Cupo crédito</h6></th>
                    <th><h6>Saldo pendiente</h6></th>
                    <th><h6>Acción</h6></th>
                  </tr>
                </thead>
                <tbody id="tbody-clientes">
                  @forelse($clientes as $cliente)
                  <tr data-id="{{ $cliente->id }}">
                    <td class="min-width">
                      <div class="d-flex align-items-center gap-2">
                        <div style="width:34px;height:34px;background:#eff6ff;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                          <i class="lni lni-user" style="color:#3b82f6;font-size:16px;"></i>
                        </div>
                        <p class="text-sm fw-semibold mb-0">{{ $cliente->nombre }}</p>
                      </div>
                    </td>
                    <td class="min-width"><p class="text-sm mb-0">{{ $cliente->telefono ?? '—' }}</p></td>
                    <td class="min-width"><p class="text-sm mb-0">{{ $cliente->nit ?? '—' }}</p></td>
                    <td class="min-width">
                      <p class="text-sm mb-0">
                        {{ $cliente->cupo_credito > 0 ? '$'.number_format($cliente->cupo_credito) : 'Sin límite' }}
                      </p>
                    </td>
                    <td class="min-width">
                      @if($cliente->saldo_pendiente > 0)
                        <span class="badge" style="background:#fee2e2;color:#b91c1c;padding:5px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                          ${{ number_format($cliente->saldo_pendiente) }}
                        </span>
                      @else
                        <span class="badge" style="background:#dcfce7;color:#166534;padding:5px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                          Al día
                        </span>
                      @endif
                    </td>
                    <td>
                      <div class="action justify-content-end">
                        <button class="more-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="lni lni-more-alt"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                          <li class="dropdown-item">
                            <a href="{{ route('clientes.show', $cliente->id) }}" class="text-gray">
                              <i class="lni lni-eye me-2"></i> Ver detalle
                            </a>
                          </li>
                          <li class="dropdown-item">
                            <button class="text-gray border-0 bg-transparent p-0 w-100 text-start" onclick="abrirModalEditar({{ $cliente->id }}, '{{ addslashes($cliente->nombre) }}', '{{ $cliente->telefono }}', '{{ $cliente->nit }}', '{{ $cliente->email }}', '{{ addslashes($cliente->direccion) }}', {{ $cliente->cupo_credito }})">
                              <i class="lni lni-pencil me-2"></i> Editar
                            </button>
                          </li>
                          <li class="dropdown-item">
                            <button class="text-danger border-0 bg-transparent p-0 w-100 text-start" onclick="confirmarEliminar({{ $cliente->id }}, '{{ addslashes($cliente->nombre) }}')">
                              <i class="lni lni-trash-can me-2"></i> Eliminar
                            </button>
                          </li>
                        </ul>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="text-center py-4 text-gray">
                      <i class="lni lni-users" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                      No hay clientes registrados
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            {{-- Paginación --}}
            @if($clientes->hasPages())
            <div class="d-flex justify-content-end mt-20">
              {{ $clientes->appends(request()->query())->links() }}
            </div>
            @endif

          </div>
        </div>
      </div>
    </div>

  </div>
</section>

{{-- ===== MODAL CREAR ===== --}}
<div class="modal fade" id="modal-crear" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
    <div class="modal-content">
      <div class="modal-header px-4 py-3">
        <h5 class="modal-title fw-semibold">Nuevo cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3">
        <div class="row g-3">
          <div class="col-12">
            <div class="input-style-1">
              <label>Nombre <span class="text-danger">*</span></label>
              <input type="text" id="crear-nombre" placeholder="Nombre completo" />
              <span class="icon"><i class="lni lni-user"></i></span>
            </div>
            <div class="text-danger text-xs mt-1" id="error-crear-nombre"></div>
          </div>
          <div class="col-md-6">
            <div class="input-style-1">
              <label>Teléfono</label>
              <input type="text" id="crear-telefono" placeholder="300 000 0000" />
              <span class="icon"><i class="lni lni-phone"></i></span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="input-style-1">
              <label>NIT / CI</label>
              <input type="text" id="crear-nit" placeholder="000.000.000" />
              <span class="icon"><i class="mdi mdi-card-account-details-outline"></i></span>
            </div>
          </div>
          <div class="col-12">
            <div class="input-style-1">
              <label>Email</label>
              <input type="email" id="crear-email" placeholder="correo@email.com" />
              <span class="icon"><i class="lni lni-envelope"></i></span>
            </div>
          </div>
          <div class="col-12">
            <div class="input-style-1">
              <label>Dirección</label>
              <input type="text" id="crear-direccion" placeholder="Dirección" />
              <span class="icon"><i class="lni lni-map-marker"></i></span>
            </div>
          </div>
          <div class="col-12">
            <div class="input-style-1">
              <label>Cupo de crédito <span class="text-xs text-gray">(0 = sin límite)</span></label>
              <input type="number" id="crear-cupo" placeholder="0" min="0" value="0" />
              <span class="icon"><i class="mdi mdi-currency-usd"></i></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer px-4 py-3">
        <button type="button" class="main-btn light-btn btn-hover" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="main-btn primary-btn btn-hover" id="btn-crear" onclick="guardarCliente()">
          <span id="btn-crear-text">Guardar</span>
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ===== MODAL EDITAR ===== --}}
<div class="modal fade" id="modal-editar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
    <div class="modal-content">
      <div class="modal-header px-4 py-3">
        <h5 class="modal-title fw-semibold">Editar cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3">
        <input type="hidden" id="editar-id" />
        <div class="row g-3">
          <div class="col-12">
            <div class="input-style-1">
              <label>Nombre <span class="text-danger">*</span></label>
              <input type="text" id="editar-nombre" placeholder="Nombre completo" />
              <span class="icon"><i class="lni lni-user"></i></span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="input-style-1">
              <label>Teléfono</label>
              <input type="text" id="editar-telefono" placeholder="300 000 0000" />
              <span class="icon"><i class="lni lni-phone"></i></span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="input-style-1">
              <label>NIT / CI</label>
              <input type="text" id="editar-nit" placeholder="000.000.000" />
              <span class="icon"><i class="mdi mdi-card-account-details-outline"></i></span>
            </div>
          </div>
          <div class="col-12">
            <div class="input-style-1">
              <label>Email</label>
              <input type="email" id="editar-email" placeholder="correo@email.com" />
              <span class="icon"><i class="lni lni-envelope"></i></span>
            </div>
          </div>
          <div class="col-12">
            <div class="input-style-1">
              <label>Dirección</label>
              <input type="text" id="editar-direccion" placeholder="Dirección" />
              <span class="icon"><i class="lni lni-map-marker"></i></span>
            </div>
          </div>
          <div class="col-12">
            <div class="input-style-1">
              <label>Cupo de crédito <span class="text-xs text-gray">(0 = sin límite)</span></label>
              <input type="number" id="editar-cupo" placeholder="0" min="0" />
              <span class="icon"><i class="mdi mdi-currency-usd"></i></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer px-4 py-3">
        <button type="button" class="main-btn light-btn btn-hover" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="main-btn primary-btn btn-hover" onclick="actualizarCliente()">
          <span id="btn-editar-text">Actualizar</span>
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ===== MODAL CONFIRMAR ELIMINAR ===== --}}
<div class="modal fade" id="modal-eliminar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
    <div class="modal-content">
      <div class="modal-body px-4 py-4 text-center">
        <div style="width:64px;height:64px;background:#fee2e2;border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;">
          <i class="lni lni-trash-can" style="font-size:28px;color:#dc2626;"></i>
        </div>
        <h5 class="fw-semibold mb-2">¿Eliminar cliente?</h5>
        <p class="text-sm text-gray mb-0">Se eliminará a <strong id="eliminar-nombre"></strong>. Esta acción no borra su historial de ventas.</p>
      </div>
      <div class="modal-footer px-4 py-3 justify-content-center gap-3">
        <button type="button" class="main-btn light-btn btn-hover" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="main-btn danger-btn btn-hover" onclick="eliminarCliente()">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let modalCrear, modalEditar, modalEliminar;
let clienteEliminarId = null;

document.addEventListener('DOMContentLoaded', function () {
  modalCrear   = new bootstrap.Modal(document.getElementById('modal-crear'));
  modalEditar  = new bootstrap.Modal(document.getElementById('modal-editar'));
  modalEliminar = new bootstrap.Modal(document.getElementById('modal-eliminar'));

  // Buscador con debounce
  let timer;
  document.getElementById('buscador').addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(() => {
      const q = this.value.trim();
      window.location.href = '{{ route("clientes.index") }}?search=' + encodeURIComponent(q);
    }, 400);
  });
});

function abrirModalCrear() {
  document.getElementById('crear-nombre').value    = '';
  document.getElementById('crear-telefono').value  = '';
  document.getElementById('crear-nit').value       = '';
  document.getElementById('crear-email').value     = '';
  document.getElementById('crear-direccion').value = '';
  document.getElementById('crear-cupo').value      = '0';
  document.getElementById('error-crear-nombre').textContent = '';
  modalCrear.show();
}

function abrirModalEditar(id, nombre, telefono, nit, email, direccion, cupo) {
  document.getElementById('editar-id').value       = id;
  document.getElementById('editar-nombre').value   = nombre;
  document.getElementById('editar-telefono').value = telefono || '';
  document.getElementById('editar-nit').value      = nit || '';
  document.getElementById('editar-email').value    = email || '';
  document.getElementById('editar-direccion').value= direccion || '';
  document.getElementById('editar-cupo').value     = cupo || 0;
  modalEditar.show();
}

function confirmarEliminar(id, nombre) {
  clienteEliminarId = id;
  document.getElementById('eliminar-nombre').textContent = nombre;
  modalEliminar.show();
}

async function guardarCliente() {
  const nombre = document.getElementById('crear-nombre').value.trim();
  document.getElementById('error-crear-nombre').textContent = '';

  if (!nombre) {
    document.getElementById('error-crear-nombre').textContent = 'El nombre es obligatorio.';
    return;
  }

  const btn = document.getElementById('btn-crear');
  btn.disabled = true;
  document.getElementById('btn-crear-text').textContent = 'Guardando...';

  try {
    const res = await fetch('{{ route("clientes.store") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify({
        nombre,
        telefono:  document.getElementById('crear-telefono').value.trim() || null,
        nit:       document.getElementById('crear-nit').value.trim() || null,
        email:     document.getElementById('crear-email').value.trim() || null,
        direccion: document.getElementById('crear-direccion').value.trim() || null,
        cupo_credito: parseInt(document.getElementById('crear-cupo').value) || 0,
      })
    });

    const data = await res.json();
    if (data.success) {
      modalCrear.hide();
      window.location.reload();
    } else {
      if (data.errors?.nombre) {
        document.getElementById('error-crear-nombre').textContent = data.errors.nombre[0];
      }
    }
  } catch (e) {
    console.error(e);
  } finally {
    btn.disabled = false;
    document.getElementById('btn-crear-text').textContent = 'Guardar';
  }
}

async function actualizarCliente() {
  const id = document.getElementById('editar-id').value;
  const btn = document.getElementById('btn-editar-text');
  btn.textContent = 'Actualizando...';

  try {
    const res = await fetch(`/clientes/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify({
        nombre:    document.getElementById('editar-nombre').value.trim(),
        telefono:  document.getElementById('editar-telefono').value.trim() || null,
        nit:       document.getElementById('editar-nit').value.trim() || null,
        email:     document.getElementById('editar-email').value.trim() || null,
        direccion: document.getElementById('editar-direccion').value.trim() || null,
        cupo_credito: parseInt(document.getElementById('editar-cupo').value) || 0,
      })
    });

    const data = await res.json();
    if (data.success) {
      modalEditar.hide();
      window.location.reload();
    }
  } catch (e) {
    console.error(e);
  } finally {
    btn.textContent = 'Actualizar';
  }
}

async function eliminarCliente() {
  if (!clienteEliminarId) return;

  try {
    const res = await fetch(`/clientes/${clienteEliminarId}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    });

    const data = await res.json();
    if (data.success) {
      modalEliminar.hide();
      const fila = document.querySelector(`tr[data-id="${clienteEliminarId}"]`);
      if (fila) fila.remove();
      clienteEliminarId = null;
    }
  } catch (e) {
    console.error(e);
  }
}
</script>

@endsection