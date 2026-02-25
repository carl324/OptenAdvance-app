@extends('layouts.app')

@section('title', 'Detalle Cliente')

@section('content')

<style>
/* Inputs edición tipo línea */
.campo-editar {
    display: none;
    width: 100%;
    border: none;
    border-bottom: 1.5px solid #3b82f6;
    padding: 2px 0;
    font-size: 13px;
    font-weight: 600;
    background: transparent;
    outline: none;
    text-align: right;
    color: #1e293b;
}
.modo-edicion .campo-ver  { display: none !important; }
.modo-edicion .campo-editar { display: inline-block !important; }

/* Dropdown tres puntos */
.dropdown-tres-puntos { position: relative; display: inline-block; }
.dropdown-tres-puntos .menu {
    display: none;
    position: absolute;
    right: 0; top: 100%;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    min-width: 160px;
    z-index: 999;
    padding: 4px 0;
}
.dropdown-tres-puntos .menu.abierto { display: block; }
.dropdown-tres-puntos .menu a,
.dropdown-tres-puntos .menu button {
    display: flex; align-items: center; gap: 8px;
    width: 100%; padding: 9px 16px;
    font-size: 13px; color: #334155;
    background: none; border: none; cursor: pointer;
    text-decoration: none; transition: background .15s;
}
.dropdown-tres-puntos .menu a:hover,
.dropdown-tres-puntos .menu button:hover { background: #f8fafc; }

@media print {
    .no-print { display: none !important; }
    .col-lg-4, .card-style:first-child { display: none !important; }
    .card-style { box-shadow: none !important; border: 1px solid #e0e0e0; }
}
</style>

<section class="table-components">
  <div class="container-fluid">

    <div class="title-wrapper pt-30 no-print">
      <div class="row align-items-center">
        
      </div>
    </div>

    <div class="row">

      {{-- ===== Tarjeta info cliente ===== --}}
      <div class="col-lg-4 no-print">
        <div class="card-style mb-30">

          {{-- Header: avatar + nombre + botones editar --}}
          <div class="d-flex align-items-center justify-content-between mb-25">
            <div class="d-flex align-items-center gap-3">
              <div style="width:56px;height:56px;background:#eff6ff;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="lni lni-user" style="font-size:26px;color:#3b82f6;"></i>
              </div>
              <div>
                <h6 class="mb-1 fw-semibold" id="display-nombre">{{ $cliente->nombre }}</h6>
                <p class="text-xs text-gray mb-0">Cliente desde {{ $cliente->created_at->format('d/m/Y') }}</p>
              </div>
            </div>
            <div class="d-flex gap-1">
              <button class="border-0 bg-transparent" id="btn-editar" onclick="activarEdicion()" title="Editar">
                <i class="lni lni-pencil-alt" style="font-size:18px;color:#64748b;"></i>
              </button>
              <button class="border-0 bg-transparent d-none" id="btn-guardar" onclick="guardarEdicion()" title="Guardar cambios">
                <i class="lni lni-checkmark" style="font-size:18px;color:#3b82f6;"></i>
              </button>
              <button class="border-0 bg-transparent d-none" id="btn-cancelar-edicion" onclick="cancelarEdicion()" title="Cancelar">
                <i class="lni lni-close" style="font-size:18px;color:#94a3b8;"></i>
              </button>
            </div>
          </div>

          {{-- Campos --}}
          <div id="campos-cliente" style="border-top:1px solid #f1f5f9;padding-top:20px;">

            @foreach([
              ['label'=>'Nombre',       'id'=>'nombre',    'valor'=>$cliente->nombre,    'placeholder'=>'Nombre'],
              ['label'=>'Teléfono',     'id'=>'telefono',  'valor'=>$cliente->telefono,  'placeholder'=>'Teléfono'],
              ['label'=>'NIT / CI',     'id'=>'nit',       'valor'=>$cliente->nit,       'placeholder'=>'NIT'],
              ['label'=>'Email',        'id'=>'email',     'valor'=>$cliente->email,     'placeholder'=>'Email'],
              ['label'=>'Dirección',    'id'=>'direccion', 'valor'=>$cliente->direccion, 'placeholder'=>'Dirección'],
            ] as $campo)
            <div class="d-flex justify-content-between align-items-center mb-15">
              <span class="text-sm text-gray">{{ $campo['label'] }}</span>
              <span class="text-sm fw-semibold campo-ver" id="ver-{{ $campo['id'] }}">{{ $campo['valor'] ?? '—' }}</span>
              <input class="campo-editar" id="edit-{{ $campo['id'] }}"
                     value="{{ $campo['valor'] ?? '' }}"
                     placeholder="{{ $campo['placeholder'] }}" />
            </div>
            @endforeach

            <div class="d-flex justify-content-between align-items-center mb-15">
              <span class="text-sm text-gray">Cupo crédito</span>
              <span class="text-sm fw-semibold campo-ver" id="ver-cupo">
                {{ $cliente->cupo_credito > 0 ? '$'.number_format($cliente->cupo_credito, 0, ',', '.') : 'Sin límite' }}
              </span>
              <input class="campo-editar" id="edit-cupo"
                     value="{{ $cliente->cupo_credito > 0 ? number_format($cliente->cupo_credito, 0, ',', '.') : '' }}"
                     placeholder="0"
                     oninput="formatCOP(this)" />
            </div>

            <div class="text-danger text-xs mt-1 d-none" id="error-edicion"></div>
          </div>

          {{-- Resumen financiero --}}
          <div style="background:#f8fafc;border-radius:12px;padding:16px;margin-top:10px;">
            <p class="text-xs text-gray mb-15 fw-semibold" style="text-transform:uppercase;letter-spacing:1px;">Resumen financiero</p>
            <div class="d-flex justify-content-between mb-10">
              <span class="text-sm text-gray">Total comprado</span>
              <span class="text-sm fw-semibold">${{ number_format($totalComprado, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-10">
              <span class="text-sm text-gray">Total abonado</span>
              <span class="text-sm fw-semibold" style="color:#16a34a;">${{ number_format($totalAbonado, 0, ',', '.') }}</span>
            </div>
            <div style="border-top:1px solid #e2e8f0;padding-top:10px;" class="d-flex justify-content-between">
              <span class="text-sm fw-semibold">Saldo pendiente</span>
              <span class="fw-semibold" style="color:{{ $cliente->saldo_pendiente > 0 ? '#dc2626' : '#16a34a' }};font-size:15px;">
                ${{ number_format($cliente->saldo_pendiente, 0, ',', '.') }}
              </span>
            </div>
          </div>
        </div>
      </div>

      {{-- ===== Columna derecha ===== --}}
      <div class="col-lg-8">

        {{-- Ventas a crédito --}}
        <div class="card-style mb-30 no-print">
          <h6 class="mb-20 fw-semibold">Ventas a crédito</h6>

          @php
            $ventasCredito = $cliente->ventas()
              ->whereIn('estado', ['credito', 'parcial'])
              ->orderByDesc('fecha')
              ->get();
          @endphp

          @if($ventasCredito->isEmpty())
            <div class="text-center py-4 text-gray">
              <i class="lni lni-checkmark-circle" style="font-size:32px;display:block;margin-bottom:8px;color:#16a34a;"></i>
              Este cliente no tiene deudas pendientes
            </div>
          @else
          <div class="table-wrapper table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th><h6>Fecha</h6></th>
                  <th><h6>Total venta</h6></th>
                  <th><h6>Saldo pendiente</h6></th>
                  <th><h6>Estado</h6></th>
                  <th><h6></h6></th>
                </tr>
              </thead>
              <tbody>
                @foreach($ventasCredito as $venta)
                <tr>
                  <td class="min-width">
                    <p class="text-sm mb-0">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</p>
                  </td>
                  <td class="min-width">
                    <p class="text-sm mb-0">${{ number_format($venta->total, 0, ',', '.') }}</p>
                  </td>
                  <td class="min-width">
                    <span style="color:#dc2626;font-weight:600;">${{ number_format($venta->saldo_pendiente, 0, ',', '.') }}</span>
                  </td>
                  <td class="min-width">
                    @if($venta->estado === 'credito')
                      <span class="badge" style="background:#fee2e2;color:#b91c1c;padding:4px 10px;border-radius:20px;font-size:11px;">Crédito</span>
                    @else
                      <span class="badge" style="background:#fef3c7;color:#92400e;padding:4px 10px;border-radius:20px;font-size:11px;">Parcial</span>
                    @endif
                  </td>
                  <td>
                    <div class="dropdown-tres-puntos">
                      <button class="border-0 bg-transparent px-2" onclick="toggleMenu(this)" style="font-size:20px;color:#94a3b8;cursor:pointer;line-height:1;">
                        <i class="lni lni-more-alt"></i>
                      </button>
                      <div class="menu">
                        <button onclick="cerrarMenus(); abrirModalAbono({{ $venta->id }}, {{ $venta->saldo_pendiente }})">
                          <i class="lni lni-plus" style="color:#3b82f6;"></i> Abonar
                        </button>
                        <a href="{{ route('ventas.detalle', $venta->id) }}" onclick="cerrarMenus()">
                          <i class="lni lni-eye" style="color:#64748b;"></i> Ver venta
                        </a>
                      </div>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @endif
        </div>

        {{-- Historial de abonos --}}
        <div class="card-style mb-30">
          <div class="d-flex justify-content-between align-items-center mb-20">
            <div>
              <h6 class="fw-semibold mb-0">Historial de abonos</h6>
              {{-- Solo visible al imprimir --}}
              <p class="text-xs text-gray mb-0" style="display:none;" id="print-subtitle">{{ $cliente->nombre }} · Impreso el {{ now()->format('d/m/Y') }}</p>
            </div>
            <button class="border-0 bg-transparent no-print" onclick="imprimirAbonos()" title="Imprimir abonos" style="color:#64748b;">
              <i class="lni lni-printer" style="font-size:20px;"></i>
            </button>
          </div>

          @php
            $abonos = $cliente->abonos()->orderByDesc('created_at')->take(50)->get();
          @endphp

          @if($abonos->isEmpty())
            <div class="text-center py-4 text-gray">
              <i class="lni lni-empty-file" style="font-size:32px;display:block;margin-bottom:8px;"></i>
              Sin abonos registrados
            </div>
          @else
          <div class="table-wrapper table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th><h6>Fecha</h6></th>
                  <th><h6>Monto</h6></th>
                  <th><h6>Forma de pago</h6></th>
                  <th><h6>Observación</h6></th>
                </tr>
              </thead>
              <tbody>
                @foreach($abonos as $abono)
                <tr>
                  <td class="min-width">
                    <p class="text-sm mb-0">{{ $abono->created_at->format('d/m/Y H:i') }}</p>
                  </td>
                  <td class="min-width">
                    <span style="color:#16a34a;font-weight:600;">${{ number_format($abono->monto, 0, ',', '.') }}</span>
                  </td>
                  <td class="min-width">
                    <p class="text-sm mb-0 text-capitalize">{{ $abono->forma_pago }}</p>
                  </td>
                  <td>
                    <p class="text-sm mb-0 text-gray">{{ $abono->observacion ?? '—' }}</p>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @endif
        </div>

      </div>
    </div>
  </div>
</section>

{{-- ===== MODAL ABONAR ===== --}}
<div class="modal fade" id="modal-abono" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
    <div class="modal-content">
      <div class="modal-header px-4 py-3">
        <h5 class="modal-title fw-semibold">Registrar abono</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3">
        <input type="hidden" id="abono-venta-id" />
        <p class="text-sm text-gray mb-20">Saldo pendiente: <strong id="abono-saldo-display" style="color:#dc2626;"></strong></p>
        <div class="row g-3">
          <div class="col-12">
            <div class="input-style-1">
              <label>Monto a abonar <span class="text-danger">*</span></label>
              <input type="text" id="abono-monto" placeholder="Ej: 50.000" oninput="formatCOP(this)" />
            </div>
            <div class="text-danger text-xs mt-1" id="error-abono-monto"></div>
          </div>
          <div class="col-12">
            <label class="text-dark mb-2 d-block text-sm">Forma de pago <span class="text-danger">*</span></label>
            <div class="select-style-1">
              <select id="abono-forma-pago">
                <option value="efectivo">Efectivo</option>
                <option value="transferencia">Transferencia</option>
                <option value="tarjeta">Tarjeta</option>
              </select>
            </div>
          </div>
          <div class="col-12">
            <div class="input-style-1">
              <label>Observación</label>
              <input type="text" id="abono-observacion" placeholder="Opcional" />
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer px-4 py-3">
        <button type="button" class="main-btn light-btn btn-hover" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="main-btn primary-btn btn-hover" onclick="guardarAbono()">
          <span id="btn-abono-text">Registrar abono</span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const clienteId = {{ $cliente->id }};
let modalAbono;

document.addEventListener('DOMContentLoaded', function () {
    modalAbono = new bootstrap.Modal(document.getElementById('modal-abono'));
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-tres-puntos')) cerrarMenus();
    });
});

// ===== FORMATO PESO COLOMBIANO =====
function formatCOP(input) {
    const raw = input.value.replace(/\./g, '').replace(/[^0-9]/g, '');
    if (raw === '') { input.value = ''; return; }
    input.value = parseInt(raw).toLocaleString('es-CO').replace(/,/g, '.');
}

function parseCOP(str) {
    return parseInt(String(str).replace(/\./g, '').replace(/[^0-9]/g, '') || '0');
}

// ===== DROPDOWN TRES PUNTOS =====
function toggleMenu(btn) {
    const menu = btn.nextElementSibling;
    const estaAbierto = menu.classList.contains('abierto');
    cerrarMenus();
    if (!estaAbierto) menu.classList.add('abierto');
}

function cerrarMenus() {
    document.querySelectorAll('.dropdown-tres-puntos .menu').forEach(m => m.classList.remove('abierto'));
}

// ===== EDICIÓN INLINE =====
function activarEdicion() {
    document.getElementById('campos-cliente').classList.add('modo-edicion');
    document.getElementById('btn-editar').classList.add('d-none');
    document.getElementById('btn-guardar').classList.remove('d-none');
    document.getElementById('btn-cancelar-edicion').classList.remove('d-none');
    document.getElementById('edit-nombre').focus();
}

function cancelarEdicion() {
    document.getElementById('campos-cliente').classList.remove('modo-edicion');
    document.getElementById('btn-editar').classList.remove('d-none');
    document.getElementById('btn-guardar').classList.add('d-none');
    document.getElementById('btn-cancelar-edicion').classList.add('d-none');
    document.getElementById('error-edicion').classList.add('d-none');
}

async function guardarEdicion() {
    const nombre = document.getElementById('edit-nombre').value.trim();
    if (!nombre) {
        const err = document.getElementById('error-edicion');
        err.textContent = 'El nombre es obligatorio.';
        err.classList.remove('d-none');
        return;
    }

    const cupoRaw = parseCOP(document.getElementById('edit-cupo').value);

    const payload = {
        nombre,
        telefono:     document.getElementById('edit-telefono').value.trim() || null,
        nit:          document.getElementById('edit-nit').value.trim() || null,
        email:        document.getElementById('edit-email').value.trim() || null,
        direccion:    document.getElementById('edit-direccion').value.trim() || null,
        cupo_credito: cupoRaw,
    };

    try {
        const res = await fetch(`/clientes/${clienteId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('display-nombre').textContent     = nombre;
            document.getElementById('ver-nombre').textContent         = nombre;
            document.getElementById('ver-telefono').textContent       = payload.telefono || '—';
            document.getElementById('ver-nit').textContent            = payload.nit || '—';
            document.getElementById('ver-email').textContent          = payload.email || '—';
            document.getElementById('ver-direccion').textContent      = payload.direccion || '—';
            document.getElementById('ver-cupo').textContent           = cupoRaw > 0
                ? '$' + cupoRaw.toLocaleString('es-CO').replace(/,/g, '.')
                : 'Sin límite';
            cancelarEdicion();
        } else {
            const err = document.getElementById('error-edicion');
            err.textContent = data.errors?.nombre?.[0] || 'Error al guardar.';
            err.classList.remove('d-none');
        }
    } catch(e) {
        const err = document.getElementById('error-edicion');
        err.textContent = 'Error de conexión.';
        err.classList.remove('d-none');
    }
}

// ===== MODAL ABONO =====
function abrirModalAbono(ventaId, saldoPendiente) {
    document.getElementById('abono-venta-id').value = ventaId;
    document.getElementById('abono-saldo-display').textContent =
        '$' + parseInt(saldoPendiente).toLocaleString('es-CO').replace(/,/g, '.');
    document.getElementById('abono-monto').value = '';
    document.getElementById('abono-observacion').value = '';
    document.getElementById('error-abono-monto').textContent = '';
    document.getElementById('abono-forma-pago').value = 'efectivo';
    modalAbono.show();
}

async function guardarAbono() {
    const monto = parseCOP(document.getElementById('abono-monto').value);
    document.getElementById('error-abono-monto').textContent = '';

    if (!monto || monto <= 0) {
        document.getElementById('error-abono-monto').textContent = 'Ingresa un monto válido.';
        return;
    }

    const btn = document.getElementById('btn-abono-text');
    btn.textContent = 'Guardando...';

    try {
        const res = await fetch(`/clientes/${clienteId}/abonar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                venta_id:    document.getElementById('abono-venta-id').value,
                monto,
                forma_pago:  document.getElementById('abono-forma-pago').value,
                observacion: document.getElementById('abono-observacion').value || null,
            })
        });
        const data = await res.json();
        if (data.success) {
            modalAbono.hide();
            window.location.reload();
        } else {
            document.getElementById('error-abono-monto').textContent = data.message || 'Error.';
        }
    } catch(e) {
        document.getElementById('error-abono-monto').textContent = 'Error de conexión.';
    } finally {
        document.getElementById('btn-abono-text').textContent = 'Registrar abono';
    }
}

// ===== IMPRIMIR ABONOS =====
function imprimirAbonos() {
    document.getElementById('print-subtitle').style.display = 'block';
    window.print();
    setTimeout(() => {
        document.getElementById('print-subtitle').style.display = 'none';
    }, 1000);
}
</script>

@endsection