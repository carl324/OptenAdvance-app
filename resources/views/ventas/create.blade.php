@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('content')
<style>
/* =========================
   SECCIÓN: SUMMARY / TOTALES
   ========================= */
.summary {
    text-align: center;
    padding-bottom: 30px;
}

.summary .label {
    font-size: 12px;
    color: var(--text-muted);
    letter-spacing: 0.5px;
    font-weight: 600;
}

.summary .total {
    font-size: 44px;
    font-weight: 700;
    color: var(--text-main);
    margin: 4px 0;
}

.summary .pending {
    font-size: 14px;
    font-weight: 600;
}

/* =====================================
   MODAL FORMULARIO (ÚNICO CON GRID)
   ===================================== */
#estado-formulario .modal-body {
    display: grid;
    grid-template-columns: 1fr 1px 1fr;
    gap: 45px;
    margin-top: 20px;
}

/* Divider vertical */
.divider-v {
    background-color: var(--border-light);
    height: 100%;
}

/* =========================
   MODALES DE ESTADO (FIX)
   ========================= */
#estado-exito .modal-body,
#estado-error .modal-body,
#estado-loading .modal-body {
    display: flex;
    flex-direction: column;
    align-items: center;

    /* 🔥 RESETS CRÍTICOS */
    gap: 0;
    margin-top: 0;
    padding-top: 24px;
    padding-bottom: 24px;
}

/* Control fino icono → texto */
#estado-exito .modal-body > div:first-child,
#estado-error .modal-body > div:first-child {
    margin-bottom: 8px;
}

#estado-exito h5,
#estado-error h5 {
    margin-bottom: 0;
}

/* =========================
   MODAL TAMAÑOS
   ========================= */
#modalPago .modal-dialog {
    max-width: none;
    width: auto;
}

#modalPago .modal-content {
    width: fit-content;
    min-width: 360px;
    margin: 0 auto;
}

/* Estado grande */
#estado-formulario {
    width: 800px;
}

/* Estados pequeños */
#estado-loading,
#estado-exito,
#estado-error {
    width: 420px;
}

/* =========================
   INPUTS
   ========================= */
.modal-input {
    width: 100%;
    padding: 12px 15px !important;
    border-radius: 12px !important;
    font-size: 14px;
    background-color: #fff;
    box-sizing: border-box !important;
    margin-bottom: 22px;
}

textarea {
    height: 110px;
    resize: none;
}

/* =========================
   BOTONES RÁPIDOS
   ========================= */
.quick-btns {
    display: flex;
    gap: 15px;
    margin-top: 15px;
}

.q-btn {
    flex: 1;
    padding: 12px 5px;
    border: 1px solid var(--border-light);
    background: white;
    border-radius: 10px;
    font-weight: 600;
    color: var(--text-main);
    cursor: pointer;
    font-size: 14px;
}

/* Header opciones rápidas */
.quick-pay-header {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 20px 0 12px;
    color: var(--text-muted);
    font-size: 13px;
    font-weight: 600;
}

.quick-pay-header::before,
.quick-pay-header::after {
    content: "";
    flex: 1;
    border-bottom: 1px solid var(--border-light);
}

.quick-pay-header span {
    padding: 0 10px;
}

/* =========================
   ESTADOS
   ========================= */
.estado-modal {
    display: flex;
    flex-direction: column;
}

/* Error */
.error-msg {
    color: var(--error-red);
    font-size: 11px;
    margin-top: 6px;
}

/* =========================
   TOTAL BOX
   ========================= */
.total-box-square {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 12px;
    background: #f4f6fb;
    border-radius: 6px;
    font-size: 14px;
}

/* =========================
   SPINNER
   ========================= */
#estado-loading .spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f0f0f0;
    border-top-color: #365cf5;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* =========================
   CAJA – HERO (SCOPED)
   Solo afecta a .caja-scope
   ========================= */

.caja-scope .caja-hero {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 48px;
  padding: 48px 56px;
  background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
  position: relative;
  overflow: hidden;
}

.caja-scope .caja-hero::before {
  content: "";
  position: absolute;
  top: -100px;
  right: -100px;
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(59,130,246,0.08), transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

/* Contenido */
.caja-scope .caja-content {
  flex: 1;
  max-width: 520px;
  position: relative;
  z-index: 1;
}

/* Badge estado */
.caja-scope .caja-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  background: linear-gradient(
    135deg,
    rgba(59,130,246,0.08),
    rgba(147,51,234,0.08)
  );
  border: 1px solid rgba(59,130,246,0.2);
  border-radius: 100px;
  margin-bottom: 16px;
}

.caja-scope .caja-badge svg {
  color: #3b82f6;
}

.caja-scope .caja-badge span {
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Título y texto */
.caja-scope .caja-title {
  font-size: 32px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 12px;
  letter-spacing: -0.02em;
  line-height: 1.2;
}

.caja-scope .caja-subtitle {
  font-size: 15px;
  color: #64748b;
  margin-bottom: 28px;
  line-height: 1.5;
}

/* Acciones */
.caja-scope .caja-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

/* Visual */
.caja-scope .caja-visual {
  flex-shrink: 0;
  position: relative;
  z-index: 1;
}

.caja-scope .caja-visual img {
  width: 320px;
  max-width: 100%;
  height: auto;
  filter: drop-shadow(0 20px 40px rgba(15,23,42,0.12));
}

/* =========================
   RESPONSIVE
   ========================= */

@media (max-width: 992px) {
  .caja-scope .caja-hero {
    flex-direction: column;
    text-align: center;
    padding: 40px 32px;
    gap: 32px;
  }

  .caja-scope .caja-content {
    max-width: 100%;
  }

  .caja-scope .caja-actions {
    justify-content: center;
  }

  .caja-scope .caja-visual img {
    width: 280px;
  }
}

@media (max-width: 576px) {
  .caja-scope .caja-hero {
    padding: 32px 24px;
  }

  .caja-scope .caja-title {
    font-size: 26px;
  }

  .caja-scope .caja-subtitle {
    font-size: 14px;
  }

  .caja-scope .caja-actions {
    flex-direction: column;
    width: 100%;
  }

  .caja-scope .caja-actions button {
    width: 100%;
  }

  .caja-scope .caja-visual img {
    width: 240px;
  }
}

@keyframes flashAzul {
    0%   { background-color: rgba(59,130,246,0.25); }
    100% { background-color: transparent; }
}
.flash-nuevo {
    animation: flashAzul 0.8s ease-out;
}
#carrito-contenido td {
    vertical-align: middle;
}
#btn-confirmar-pago:disabled {
    cursor: not-allowed !important;
    pointer-events: all !important;
}
</style>


@if(!$cajaAbierta)
<section class="section pt-30 caja-scope">
  <div class="container-fluid">
    <div class="card-style mb-30 p-0 overflow-hidden">
      <div class="caja-hero">
        <div class="caja-content">
          <div class="caja-badge">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
              <circle cx="6" cy="6" r="6" fill="currentColor" opacity="0.2"/>
              <circle cx="6" cy="6" r="3" fill="currentColor"/>
            </svg>
            <span>Caja Cerrada</span>
          </div>
          
          <h2 class="caja-title">Caja registradora</h2>
          <p class="caja-subtitle">Gestiona tus ventas de forma rápida y eficiente</p>
          
          <div class="caja-actions">
                        <button class="main-btn primary-btn btn-hover" type="button" data-bs-toggle="modal" data-bs-target="#modalAbrirCaja">
              <i class="lni lni-unlock me-2"></i>
              Abrir caja
            </button>
            <a href="{{ route('ventas.index') }}" style="color: inherit; text-decoration: none;"><button class="main-btn light-btn btn-hover {{ activeRoute(['ventas.index', 'ventas.show', 'ventas.devolucion', 'ventas.factura*']) }}">
              <i class="lni lni-revenue"></i>
               Ver ventas 
            </button> </a>
          </div>
        </div>
        
        <div class="caja-visual">
          <img src="/assets/images/cards/caja.png" alt="Caja registradora" />
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="modalAbrirCaja" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content" style="border: none; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden;">
            
            <div class="modal-header border-0" style="padding: 30px 30px 10px 30px;">
                <h6 style="margin: 0; font-weight: 800; color: #1e293b; font-size: 18px;">Apertura de caja</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('caja.abrir') }}">
                @csrf
                <div class="modal-body" style="padding: 10px 30px 20px 30px;">
                    
                    <div style="margin-bottom: 20px;">
                        <label for="monto_apertura" style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px;">Monto inicial</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); font-weight: 700; color: #64748b;">$</span>
                            <input
  type="text"
  id="monto_apertura"
  name="monto_apertura"
  inputmode="numeric"
  placeholder="0"
  value="0"
  autocomplete="off"
  style="width: 100%; padding: 12px 15px 12px 35px; border-radius: 12px; border: 1.5px solid #e2e8f0; font-size: 18px; font-weight: 700; color: #1e293b; outline: none;"
>
<script>
const inputMonto = document.getElementById('monto_apertura');

inputMonto.addEventListener('input', () => {
  // Quita todo lo que no sea número
  let raw = inputMonto.value.replace(/\D/g, '');

  // Evita valores vacíos
  if (!raw) {
    inputMonto.value = '';
    return;
  }

  // Formato tipo Nequi: 1.234.567
  inputMonto.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
});

// Limpia antes de enviar el form
inputMonto.form?.addEventListener('submit', () => {
  inputMonto.value = inputMonto.value.replace(/\./g, '');
});
</script>

                        </div>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <label for="nota_apertura" style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px;">Nota de apertura</label>
                        <textarea id="nota_apertura" name="nota_apertura" rows="3" maxlength="255"
                            placeholder="Escribe aquí alguna observación sobre el inicio del turno..."
                            style="width: 100%; padding: 15px; border-radius: 12px; border: 1.5px solid #e2e8f0; font-size: 14px; color: #475569; outline: none; resize: none; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#3b82f6';"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0" style="padding: 10px 30px 30px 30px;">
                    <div class="d-flex gap-3 w-100">
                        <button class="main-btn light-btn btn-hover flex-fill" type="button" data-bs-dismiss="modal" style="padding: 12px; border-radius: 10px;">
                            Cancelar
                        </button>
                        <button class="main-btn primary-btn btn-hover flex-fill" type="submit" style="padding: 12px; border-radius: 10px;">
                            Abrir caja
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($cajaAbierta)
<section class="section">
    <div class="container-fluid">
        <div class="title-wrapper pt-30"></div>
        
        <div id="mensaje" class="alert alert-info alert-dismissible fade d-none mb-3" role="alert" style="margin: 15px 0;">
            <span id="texto-mensaje"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <div class="row">
            <!-- LEFT: Tabla de Productos -->
            <div class="col-lg-7">
                <div class="card-style mb-30">
                    <div class="title d-flex flex-wrap align-items-center justify-content-between">
                        <div class="right">
                            <div class="select-style-1">
                                <div class="input-group input-group-sm search-pos">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="lni lni-search-alt"></i>
                                    </span>
                                    <input type="text" id="buscar-producto" class="form-control bg-light border-0" placeholder="Buscar producto..." autocomplete="off" autofocus />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table top-selling-table">
                            <thead>
                                <tr>
                                    <th><h6 class="text-sm text-medium">Producto</h6></th>
                                    <th class="min-width"><h6 class="text-sm text-medium">Precio</h6></th>
                                    @if($empresa && $empresa->cobra_iva)
                                    <th class="min-width"><h6 class="text-sm text-medium">IVA</h6></th>
                                    @endif
                                    <th class="min-width"><h6 class="text-sm text-medium">Stock</h6></th>
                                </tr>
                            </thead>
                            <tbody id="tabla-productos">
                                <tr><td colspan="{{ ($empresa && $empresa->cobra_iva) ? 4 : 3 }}" style="text-align: center; padding: 20px;">Cargando productos...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Carrito actual -->
            <div class="col-lg-5">
                <div class="card-style mb-30">
                    <div class="title mb-20">
                        <h6 class="text-medium">Venta actual</h6>
                        <p class="text-xs text-gray">Productos listos para vender</p>
                    </div>

                    <div class="table-responsive">
                        <div id="alerta-carrito" class="alert alert-danger fade d-none mb-3" role="alert">
                            <span id="texto-alerta-carrito"></span>
                        </div>
                        <table class="table">
                            <thead id="carrito-header">
                                <tr>
                                    <td class="text-sm">Sin productos</td>
                                </tr>
                            </thead>
                            <tbody id="carrito-contenido">
                                <tr>
                                    <td class="text-sm" colspan="3" style="text-align: center; padding: 20px; color: #999;">El carrito está vacío</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-20">
                        <span class="text-sm">Total</span>
                        <strong class="text-medium" id="total">$0</strong>
                    </div>

                    <div class="d-flex gap-2 mt-20">
                        <button class="main-btn light-btn btn-hover w-100" type="button" onclick="limpiarVenta()">
                            Cancelar
                        </button>
                        <button class="main-btn primary-btn btn-hover w-100" id="btn-finalizar" type="button" onclick="confirmarVenta()" disabled>
                            Vender
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de pago - Basado en estados -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content alegra-modal-square px-2">
            
            <!-- Estado: Formulario -->
            <div id="estado-formulario" class="estado-modal">
                <div class="summary"><br>
                    <div class="total" id="modal-total">$0</div>
                    <div class="pending">Por cobrar: <span id="modal-por-cobrar" style="color: #fb6a6a;">$0</span></div>
                </div>

                <div class="modal-body">
                    <div class="col">
                        <label>Total Pagado</label>
                        <input type="text" id="total_pagado" maxlength="40" required class="modal-input form-control form-control-sm mb-2" placeholder="ingresa un valor" oninput="formatTotalPagadoInput(); calcularPorCobrar()" />
                        <small class="text-muted" id="contador-totalpagado" style="font-size: 11px; display: none;"></small>
                        <div id="error-totalpagado" class="error-msg" style="display: none;">Ingresa el total pagado por el cliente</div>
                        
                        
                        
                        <div class="quick-pay-header" style="margin-top: 20px;"><span>Método de pago</span></div>
                        <div class="quick-btns" style="display: flex; gap: 10px; margin-top: 10px;">
                            <button type="button" class="q-btn" style="flex: 1; padding: 10px; border: 2px solid #e0e0e0; background: white; border-radius: 8px; cursor: pointer;" onclick="seleccionarPago('efectivo')" id="pago-efectivo">
                                <i class="lni lni-money-location" style="font-size: 20px; display: block; margin-bottom: 5px;"></i>
                                <span style="font-size: 12px;">Efectivo</span>
                            </button>
                            <button type="button" class="q-btn" style="flex: 1; padding: 10px; border: 2px solid #e0e0e0; background: white; border-radius: 8px; cursor: pointer;" onclick="seleccionarPago('tarjeta')" id="pago-tarjeta">
                                <i class="lni lni-credit-cards" style="font-size: 20px; display: block; margin-bottom: 5px;"></i>
                                <span style="font-size: 12px;">Tarjeta</span>
                            </button>
                            <button type="button" class="q-btn" style="flex: 1; padding: 10px; border: 2px solid #e0e0e0; background: white; border-radius: 8px; cursor: pointer;" onclick="seleccionarPago('transferencia')" id="pago-transferencia">
                                <i class="lni lni-apartment" style="font-size: 20px; display: block; margin-bottom: 5px;"></i>
                                <span style="font-size: 12px;">Transferencia</span>
                            </button>
                            <button type="button" class="q-btn" style="flex: 1; padding: 10px; border: 2px solid #e0e0e0; background: white; border-radius: 8px; cursor: pointer;" onclick="seleccionarPago('credito')" id="pago-credito">
    <i class="lni lni-handshake" style="font-size: 20px; display: block; margin-bottom: 5px;"></i>
    <span style="font-size: 12px;">Crédito</span>
</button>
                        </div>
                        <input type="hidden" id="forma_pago" value="efectivo" />
                    </div>

                    <div class="divider-v" style="width: 1px; background: #e0e0e0; margin: 0 20px;"></div>

<div class="col">
    <label style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;">Cliente</label>
    <div style="display:flex;gap:8px;align-items:center;margin-top:6px;">
        <div style="position:relative;flex:1;">
            <input type="text" id="buscar-cliente-input" class="modal-input form-control form-control-sm" placeholder="Buscar por nombre o teléfono..." autocomplete="off" oninput="buscarClientePOS(this.value)" style="margin-bottom:0;" />
            <div id="lista-clientes-sugeridos" style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;max-height:160px;overflow-y:auto;display:none;position:absolute;z-index:9999;width:100%;box-shadow:0 4px 12px rgba(0,0,0,0.08);top:100%;left:0;"></div>
        </div>
        <button type="button" title="Registrar cliente rápido" onclick="toggleRegistroRapido()" id="btn-registro-rapido" style="flex-shrink:0;width:38px;height:38px;border:1.5px solid #e0e0e0;border-radius:10px;background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;">
            <i class="lni lni-plus" style="font-size:16px;color:#3b82f6;"></i>
        </button>
    </div>

    {{-- Cliente seleccionado chip --}}
    <div id="cliente-seleccionado" style="display:none;margin-top:8px;padding:8px 12px;background:#eff6ff;border-radius:8px;font-size:13px;color:#1e40af;justify-content:space-between;align-items:center;">
        <span id="cliente-seleccionado-nombre"></span>
        <button type="button" onclick="limpiarClienteSeleccionado()" style="background:none;border:none;cursor:pointer;color:#64748b;font-size:18px;padding:0;line-height:1;">×</button>
    </div>

    <div class="text-danger text-xs mt-1" id="error-cliente-credito"></div>

    {{-- Registro rápido inline --}}
    <div id="panel-registro-rapido" style="display:none;margin-top:12px;border-top:1px solid #f1f5f9;padding-top:12px;">
        <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:14px;">Nuevo cliente</p>
        
        <div style="margin-bottom:16px;">
            <input type="text" id="rr-nombre" placeholder="Nombre *" autocomplete="off"
                style="width:100%;border:none;border-bottom:1.5px solid #e2e8f0;padding:6px 0;font-size:14px;outline:none;background:transparent;transition:border-color 0.2s;"
                onfocus="this.style.borderBottomColor='#3b82f6'"
                onblur="this.style.borderBottomColor='#e2e8f0'" />
        </div>
        <div style="margin-bottom:16px;">
            <input type="text" id="rr-telefono" placeholder="Teléfono" autocomplete="off"
                style="width:100%;border:none;border-bottom:1.5px solid #e2e8f0;padding:6px 0;font-size:14px;outline:none;background:transparent;transition:border-color 0.2s;"
                onfocus="this.style.borderBottomColor='#3b82f6'"
                onblur="this.style.borderBottomColor='#e2e8f0'" />
        </div>

        <div class="text-danger text-xs mb-2" id="error-rr"></div>

        <div style="display:flex;gap:8px;">
            <button type="button" onclick="toggleRegistroRapido()" style="flex:1;padding:8px;border:1.5px solid #e0e0e0;border-radius:8px;background:white;font-size:13px;cursor:pointer;color:#64748b;">
                Cancelar
            </button>
            <button type="button" onclick="guardarRegistroRapido()" style="flex:1;padding:8px;border:none;border-radius:8px;background:#3b82f6;color:white;font-size:13px;font-weight:600;cursor:pointer;">
                <span id="rr-btn-text">Guardar</span>
            </button>
        </div>
    </div>

    {{-- Aviso crédito --}}
    <div id="bloque-credito-aviso" style="display:none;margin-top:12px;padding:10px 12px;background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;font-size:12px;color:#c2410c;">
        <i class="lni lni-warning me-1"></i> Para crédito debes seleccionar un cliente.
    </div>

    {{-- Campos ocultos --}}
    <input type="hidden" id="cliente" value="" />
    <input type="hidden" id="cliente_nit" value="" />
    <input type="hidden" id="cliente_id" value="" />
</div>
                    

                </div>
                   
                <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 20px; display: flex; gap: 10px; justify-content: flex-end;">
                   <br> <div class="d-flex gap-3 w-100">
                        <button class="main-btn light-btn btn-hover flex-fill" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button class="main-btn primary-btn btn-hover flex-fill" id="btn-confirmar-pago" type="button" onclick="finalizarVenta()">
                            Finalizar venta
                        </button>
                    </div>
                </div>
            </div>
            <!-- Estado: Loading -->
            <div id="estado-loading" class="estado-modal d-none">
                <div class="modal-body py-5 text-center">
                    <div class="spinner mb-3" style="margin: 0 auto;"></div>
                    <p class="text-sm text-gray">Procesando venta...</p>
                </div>
            </div>

            <!-- Estado: Éxito -->
            <div id="estado-exito" class="estado-modal d-none">
                <div class="modal-body py-5 text-center">
                    <div style="font-size: 64px; color: #4CAF50; margin-bottom: 16px;">
                        <i class="lni lni-checkmark-circle"></i>
                    </div>
                    <h5 class="text-medium" style="font-size: 18px;">¡Venta registrada correctamente!</h5>
                </div>

                <div class="modal-footer border-0 flex-column gap-3">
                    <div class="total-box-square w-100">
                        <span>Total</span>
                        <strong id="resultado-total">$0</strong>
                    </div>
                    <div class="d-flex gap-2 w-100">
                        <button class="main-btn light-btn btn-hover flex-fill" onclick="irAFactura()">
                            <i class="lni lni-eye me-2"></i> Ver factura
                        </button>
                        <button class="main-btn primary-btn btn-hover flex-fill" onclick="nuevaVenta()">
                            <i class="lni lni-plus me-2"></i> Nueva venta
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estado: Error -->
            <div id="estado-error" class="estado-modal d-none">
                <div class="modal-body py-5 text-center">
                    <div style="font-size: 48px; color: #d9534f; margin-bottom: 20px;">
                        <i class="lni lni-cross-circle"></i>
                    </div>
                    <h6 class="text-medium mb-2">Algo salió mal</h6>
                    <p class="text-sm text-gray" id="resultado-error"></p>
                </div>

                <div class="modal-footer border-0 pt-3">
                    <div class="d-flex gap-3 w-100">
                        <button class="main-btn light-btn btn-hover flex-fill" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button class="main-btn primary-btn btn-hover flex-fill" onclick="modalEstado('formulario')">
                            <i class="lni lni-reload me-2"></i> Intentar de nuevo
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
  
let clienteSeleccionadoId = null;
let clienteCupoCredito = null;
let clienteSaldoPendiente = 0;
let buscarClienteTimer = null;
let rrDuplicadoConfirmado = false;
function buscarClientePOS(q) {
    clearTimeout(buscarClienteTimer);
    const lista = document.getElementById('lista-clientes-sugeridos');
    if (!lista) return;
    if (q.length < 2) { lista.style.display = 'none'; return; }

    buscarClienteTimer = setTimeout(async () => {
        const res = await fetch(`/api/clientes/buscar?q=${encodeURIComponent(q)}`, {
            headers: { 'Accept': 'application/json' }
        });
        const clientes = await res.json();
        lista.innerHTML = '';
        if (!clientes.length) {
            lista.innerHTML = '<div style="padding:10px;font-size:13px;color:#64748b;">Sin resultados — usa el botón + para registrar un cliente</div>';
            lista.style.display = 'block';
            return;
        }
        clientes.forEach(c => {
            const item = document.createElement('div');
            item.style.cssText = 'padding:10px 12px;cursor:pointer;font-size:13px;border-bottom:1px solid #f1f5f9;';
            item.innerHTML = `<strong>${c.nombre}</strong><br><span style="color:#64748b;font-size:11px;">${c.telefono || ''} ${c.nit ? '· '+c.nit : ''}</span>`;
            item.onmouseover = () => item.style.background = '#f8fafc';
            item.onmouseout  = () => item.style.background = 'white';
            item.onclick = () => seleccionarCliente(c);
            lista.appendChild(item);
        });
        lista.style.display = 'block';
    }, 300);
}

function seleccionarCliente(c) {
    clienteSeleccionadoId = c.id;          
    clienteCupoCredito = c.cupo_credito ?? null;
    clienteSaldoPendiente = c.saldo_pendiente ?? 0;
    document.getElementById('cliente_id').value = c.id;
    document.getElementById('cliente').value = c.nombre;
    document.getElementById('cliente_nit').value = c.nit || '';
    document.getElementById('buscar-cliente-input').value = c.nombre;
    document.getElementById('cliente-seleccionado-nombre').textContent = '✓ ' + c.nombre;
    document.getElementById('cliente-seleccionado').style.display = 'flex';
    document.getElementById('lista-clientes-sugeridos').style.display = 'none';
    const aviso = document.getElementById('bloque-credito-aviso');
    if (aviso) aviso.style.display = 'none';
    validarCupoCredito();
    localStorage.setItem('pos_cliente', JSON.stringify(c));
}

function limpiarClienteSeleccionado() {
    clienteSeleccionadoId = null;
    document.getElementById('cliente_id').value = '';
    document.getElementById('cliente').value = '';
    document.getElementById('cliente_nit').value = '';
    document.getElementById('buscar-cliente-input').value = '';
    document.getElementById('cliente-seleccionado').style.display = 'none';
    clienteCupoCredito = null;
    clienteSaldoPendiente = 0;
}
function validarCupoCredito() {
    const err = document.getElementById('error-cliente-credito');
    const btnFinalizar = document.getElementById('btn-confirmar-pago');
    if (!err) return;

    err.textContent = '';
    err.innerHTML = '';

    const formaPago = document.getElementById('forma_pago').value;

    if (formaPago !== 'credito' || !clienteSeleccionadoId) {
        if (btnFinalizar) { btnFinalizar.disabled = false; btnFinalizar.style.cursor = ''; }
        return;
    }

    if (clienteCupoCredito === null) {
        err.textContent = 'Este cliente no tiene crédito habilitado.';
    } else if (clienteCupoCredito !== -1) {
    const total = window.totalVentaNumeric || 0;
    const cupoDisponible = clienteCupoCredito - clienteSaldoPendiente;

    if (cupoDisponible <= 0) {
        err.innerHTML = `Este cliente ya superó su cupo de $${clienteCupoCredito.toLocaleString('es-CO')}. Saldo pendiente: $${clienteSaldoPendiente.toLocaleString('es-CO')}. No puede comprar a crédito hasta abonar. <a href="/clientes/${clienteSeleccionadoId}">Ver cliente</a>`;
    } else if (total > cupoDisponible) {
        const cupoMostrar = cupoDisponible < 0 ? 0 : cupoDisponible;
        err.innerHTML = `Esta venta con el valor de $${total.toLocaleString('es-CO')} supera el cupo disponible de $${cupoMostrar.toLocaleString('es-CO')} para este cliente. <a href="/clientes/${clienteSeleccionadoId}">Aumentar cupo</a>`;
    }
}

    const hayError = err.textContent !== '' || err.innerHTML !== '';
    if (btnFinalizar) {
        btnFinalizar.disabled = hayError;
        btnFinalizar.style.cursor = hayError ? 'not-allowed' : '';
    }
}
function toggleRegistroRapido() {
    const panel = document.getElementById('panel-registro-rapido');
    const btn = document.getElementById('btn-registro-rapido');
    const abierto = panel.style.display !== 'none';

    if (abierto) {
        panel.style.display = 'none';
        btn.style.borderColor = '#e0e0e0';
        btn.querySelector('i').style.transform = 'rotate(0deg)';
        document.getElementById('rr-btn-text').textContent = 'Guardar';
    } else {
        panel.style.display = 'block';
        btn.style.borderColor = '#3b82f6';
        btn.querySelector('i').style.transform = 'rotate(45deg)';
        document.getElementById('rr-nombre').value = document.getElementById('buscar-cliente-input').value.trim();
        document.getElementById('rr-telefono').value = '';
        document.getElementById('error-rr').textContent = '';

        document.getElementById('rr-telefono').onblur = async function() {
            const tel = this.value.trim();
            if (!tel) return;
            const res = await fetch(`/api/clientes/buscar?q=${encodeURIComponent(tel)}`, { headers: { 'Accept': 'application/json' } });
            const clientes = await res.json();
            const duplicado = clientes.find(c => c.telefono === tel);
            const err = document.getElementById('error-rr');
            if (duplicado) {
                rrDuplicadoConfirmado = false;
                document.getElementById('rr-btn-text').textContent = 'Continuar de todas formas';
                err.innerHTML = `⚠️ Este número ya está registrado como <strong>${duplicado.nombre}</strong>. 
                    <a href="#" onclick="seleccionarCliente(${JSON.stringify(duplicado).replace(/"/g, '&quot;')});toggleRegistroRapido();return false;">Usar este cliente</a>`;
                err.style.color = '#b45309';
            } else {
                rrDuplicadoConfirmado = false;
                document.getElementById('rr-btn-text').textContent = 'Guardar';
                err.textContent = '';
            }
        };

        setTimeout(() => document.getElementById('rr-nombre').focus(), 50);
    }
}

async function guardarRegistroRapido() {
    const nombre = document.getElementById('rr-nombre').value.trim();
    const telefono = document.getElementById('rr-telefono').value.trim();
    const errEl = document.getElementById('error-rr');

    if (!nombre) {
        errEl.textContent = 'El nombre es obligatorio.';
        return;
    }

    // Verificar duplicado en tiempo real antes de guardar
    if (telefono && !rrDuplicadoConfirmado) {
        const res = await fetch(`/api/clientes/buscar?q=${encodeURIComponent(telefono)}`, { headers: { 'Accept': 'application/json' } });
        const clientes = await res.json();
        const duplicado = clientes.find(c => c.telefono === telefono);
        if (duplicado) {
            rrDuplicadoConfirmado = true;
            document.getElementById('rr-btn-text').textContent = 'Guardar';
            errEl.innerHTML = `⚠️ Este número ya está registrado como <strong>${duplicado.nombre}</strong>. 
                <a href="#" onclick="seleccionarCliente(${JSON.stringify(duplicado).replace(/"/g, '&quot;')});toggleRegistroRapido();return false;">Usar este cliente</a>`;
            errEl.style.color = '#b45309';
            return;
        }
    }

    errEl.textContent = '';
    const btn = document.getElementById('rr-btn-text');
    btn.textContent = 'Guardando...';

    try {
        const res = await fetch('/clientes', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfTokenGlobal, 'Accept': 'application/json' },
            body: JSON.stringify({ nombre, telefono: telefono || null })
        });
        const data = await res.json();
        if (data.success) {
            rrDuplicadoConfirmado = false;
            toggleRegistroRapido();
            seleccionarCliente(data.cliente);
        } else {
            errEl.textContent = data.errors?.nombre?.[0] || 'Error al guardar.';
        }
    } catch(e) {
        errEl.textContent = 'Error de conexión.';
    } finally {
        btn.textContent = 'Guardar';
    }
}
// ========== VALIDACIÓN DE ELEMENTOS DOM AL INICIAR ==========
function validarElementosDOM() {
    const elementos = {
        'buscar-producto': 'input de búsqueda',
        'tabla-productos': 'tabla de productos',
        'carrito-contenido': 'contenido del carrito',
        'total': 'elemento total',
        'btn-finalizar': 'botón finalizar',
        'mensaje': 'contenedor de mensajes',
        'modalPago': 'modal de pago',
        'cliente': 'input cliente',
        'total_pagado': 'input total pagado',
        'cliente_nit': 'input NIT',
        'forma_pago': 'input forma de pago'
    };

    for (const [id, descripcion] of Object.entries(elementos)) {
        if (!document.getElementById(id)) {
            return false;
        }
    }
    return true;
}

// Validar CSRF token
function validarCSRFToken() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken || !csrfToken.content) {
        return null;
    }
    return csrfToken.content;
}

// ========== INICIALIZACIÓN CON VALIDACIONES ==========
let carrito = [];
let productoSeleccionado = null;
let todosProductos = [];
let busquedaTimeout = null;
let ventaEnProceso = false; // Flag para prevenir race condition
let csrfTokenGlobal = null; // Almacenar token de forma segura
// Flag desde servidor: si la empresa cobra IVA
const UNIDAD_ABREV = {
  'Unidad':'und','Par':'par','Docena':'doc','Caja':'caja','Paquete':'paq',
  'Sobre':'sob','Frasco':'fco','Botella':'bot','Lata':'lata','Tubo':'tubo',
  'Gramo':'g','Kilogramo':'kg','Libra':'lb','Tonelada':'t','Onza':'oz',
  'Mililitro':'ml','Litro':'L','Galón':'gal','Metro cúbico':'m³',
  'Milímetro':'mm','Centímetro':'cm','Metro':'m','Metro lineal':'m lineal',
  'Kilómetro':'km','Pulgada':'in','Pie':'ft','Metro cuadrado':'m²',
  'Centímetro cuadrado':'cm²','Hectárea':'ha'
};
const COBRA_IVA = @json((bool)($empresa && $empresa->cobra_iva));

// Obtener elementos DOM de forma segura
const inputBuscar = document.getElementById('buscar-producto');
const tablaProductos = document.getElementById('tabla-productos');
const carritoDiv = document.getElementById('carrito-contenido');
const totalSpan = document.getElementById('total');
const btnFinalizar = document.getElementById('btn-finalizar');
const mensajeDiv = document.getElementById('mensaje');
const totalPagadoInput = document.getElementById('total_pagado');

// Validar caracteres en tiempo real
function validarCaracteres(input, limite, contadorId) {
    const contador = document.getElementById(contadorId);
    const actual = input.value.length;
    
    if (contador) {
        // Si el input está vacío, nunca mostrar el mensaje
        if (actual === 0) {
            contador.style.display = 'none';
            input.classList.remove('border-danger');
            return;
        }
        
        // Si llegó al 90% del límite, mostrar advertencia
        if (actual >= limite * 0.9) {
            if (actual >= limite) {
                contador.textContent = '⚠️ Límite de caracteres alcanzado';
                contador.style.color = '#dc2626'; // Rojo
            } else {
                contador.textContent = `⚠️ Casi al límite (${actual}/${limite})`;
                contador.style.color = '#f59e0b'; // Naranja
            }
            contador.style.display = 'block'; // Mostrar
            input.classList.add('border-danger');
        } else {
            // Ocultar si está dentro del límite
            contador.style.display = 'none';
            input.classList.remove('border-danger');
        }
    }
}

// Limpiar mensajes de validación de caracteres
function limpiarMensajesCaracteres() {
    const contador_cliente = document.getElementById('contador-cliente');
    const contador_nit = document.getElementById('contador-nit');
    
    if (contador_cliente) contador_cliente.style.display = 'none';
    if (contador_nit) contador_nit.style.display = 'none';
    
    const cliente = document.getElementById('cliente');
    const cliente_nit = document.getElementById('cliente_nit');
    
    if (cliente) cliente.classList.remove('border-danger');
    if (cliente_nit) cliente_nit.classList.remove('border-danger');
}

// Escapar HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Formato colombiano
function formatoPrecio(precio) {
    return '$' + Math.round(precio).toLocaleString('es-CO');
}

// ========== FORMATEO TIPO NEQUI PARA INPUTS (mientras se escribe) ==========
function formatoNequiNumber(value) {
    if (!value) return '';
    // Mantener solo dígitos
    const digits = String(value).replace(/\D/g, '');
    if (digits === '') return '';
    return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function formatTotalPagadoInput() {
    const input = document.getElementById('total_pagado');
    if (!input) return;

    // Guardar posición (se sitúa al final tras formato para simplicidad)
    const raw = input.value || '';

    // Si el usuario borra todo, permitir vacío
    if (raw.trim() === '') {
        input.value = '';
        return;
    }

    // Remover puntos existentes y cualquier no-dígito
    const unformatted = raw.replace(/\./g, '').replace(/,/g, '.').replace(/[^0-9]/g, '');

    input.value = formatoNequiNumber(unformatted);
    // Poner el cursor al final para evitar saltos inesperados
    // Mostrar contador de caracteres (mensaje) usando la misma función, pero
    // evitar marcar borde rojo mientras se escribe: eliminar clase `border-danger`.
    try { validarCaracteres(input, 40, 'contador-totalpagado'); input.classList.remove('border-danger'); } catch (e) {}
    // Si ya tenía is-invalid (mensaje de intento previo), quitarlo al escribir
    try { if (input.value.trim() !== '') input.classList.remove('is-invalid'); } catch (e) {}
    // Ocultar el mensaje inline específico del campo cuando el usuario escribe
    try { const err = document.getElementById('error-totalpagado'); if (err) err.style.display = 'none'; } catch (e) {}
    try { input.setSelectionRange(input.value.length, input.value.length); } catch (e) {}
}

function parseInputNumber(str) {
    if (str === null || str === undefined) return 0;
    const s = String(str).trim();
    if (s === '') return 0;
    // El formato Nequi usa '.' como separador de miles. Quitarlos.
    const only = s.replace(/\./g, '').replace(/,/g, '.').replace(/[^0-9.\-]/g, '');
    const n = parseFloat(only);
    return isNaN(n) ? 0 : n;
}

// Cargar todos los productos al iniciar
async function cargarProductos() {
    try {
        const res = await fetch('/api/productos/buscar?q=__top__');
        const data = await res.json();
        todosProductos = [];
        actualizarTablaProductos(data);
    } catch (error) {
        tablaProductos.innerHTML = `<tr><td colspan="${COBRA_IVA ? 4 : 3}" style="text-align: center; color: #d9534f; padding: 20px;">Error al cargar productos</td></tr>`;
    }
const carritoGuardado = localStorage.getItem('pos_carrito');
if (carritoGuardado) {
    const parsed = JSON.parse(carritoGuardado);
    const horas = (Date.now() - parsed.ts) / (1000 * 60 * 60);
    if (horas < 1) {
        const ids = parsed.data.map(i => i.id).join(',');
        fetch(`/api/productos/buscar?ids=${ids}`)
            .then(r => r.json())
            .then(frescos => {
                carrito = parsed.data.map(item => {
                    const fresco = frescos.find(p => p.id === item.id);
                    if (!fresco) return null; // producto eliminado
                    const precioBase = parseFloat(fresco.precio);
                    const ivaValor = fresco.iva > 0 ? precioBase * fresco.iva / 100 : 0;
                    return {
                        ...item,
                        nombre: fresco.nombre,
                        precio: precioBase,
                        stock: fresco.stock,
                        iva: fresco.iva || 0,
                        subtotalConIva: precioBase + ivaValor,
                    };
                }).filter(Boolean); // elimina productos borrados
                actualizarCarrito();
            });
    } else {
        localStorage.removeItem('pos_carrito');
        localStorage.removeItem('pos_cliente');
    }
}

// Restaurar cliente
const clienteGuardado = localStorage.getItem('pos_cliente');
if (clienteGuardado) {
    seleccionarCliente(JSON.parse(clienteGuardado));
}
}

// Actualizar tabla de productos
function actualizarTablaProductos(filtrados = null) {
    const productos = filtrados || [];

    if (productos.length === 0) {
        tablaProductos.innerHTML = `<tr><td colspan="${COBRA_IVA ? 4 : 3}" style="text-align: center; padding: 20px; color: #999;">No hay productos</td></tr>`;
        return;
    }

    const html = productos.map(p => {
        let statusClass = '';

        return `
            <tr>
                <td>
                    <div class="product">
                        <p class="text-sm truncate truncate-medium" style="cursor:pointer; max-width: 300px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${p.nombre}" style="cursor: pointer;" onclick="agregarAlCarrito({id: ${p.id}, nombre: '${escapeHtml(p.nombre)}', precio: ${p.precio}, stock: ${p.stock}, iva: ${COBRA_IVA ? (p.iva || 0) : 0}, unidad: '${escapeHtml(p.unidad || 'Unidad')}'})">
                            ${p.nombre}
                        </p>
                    </div>
                </td>
                <td><p class="text-sm">${formatoPrecio(p.precio)}</p></td>
                ${COBRA_IVA ? `<td><p class="text-sm">${p.iva || 0}%</p></td>` : ''}
                <td><span class="" style="color: #2563EB;">${p.stock}</span> <span style="color:#94a3b8;font-size:0.75rem;">${UNIDAD_ABREV[p.unidad] || p.unidad || 'und'}</span></td>
            </tr>
        `;
    }).join('');

    tablaProductos.innerHTML = html;
    if (window.initTooltips) window.initTooltips(tablaProductos);
}

// Búsqueda de productos
inputBuscar.addEventListener('input', function() {
    clearTimeout(busquedaTimeout);
    const query = this.value.trim();

    if (query.length === 0) {
        cargarProductos();
        return;
    }

    if (query.length < 2) return;

    busquedaTimeout = setTimeout(async () => {
        try {
            const res = await fetch(`/api/productos/buscar?q=${encodeURIComponent(query)}`);
            const data = await res.json();
            actualizarTablaProductos(data.length ? data : null);
        } catch (e) {
            mostrarAlertaCarrito('Error al buscar productos');
        }
    }, 300);
});

inputBuscar.addEventListener('input', function() {
    clearTimeout(busquedaTimeout);
    const query = this.value.trim();

    if (query.length === 0) {
        cargarProductos();
        return;
    }

    if (query.length < 2) return;

    busquedaTimeout = setTimeout(async () => {
        try {
            const res = await fetch(`/api/productos/buscar?q=${encodeURIComponent(query)}`);
            const data = await res.json();
            actualizarTablaProductos(data.length ? data : null);
        } catch (e) {
            mostrarAlertaCarrito('Error al buscar productos');
        }
    }, 300);
});

document.addEventListener('keydown', async function(e) {
    if (e.key !== 'Enter') return;

    // Solo actuar si el foco NO está en un input de cantidad del carrito
    const activo = document.activeElement;
    const esCantidad = activo && activo.classList.contains('cantidad-display');
    const esModalAbierto = document.getElementById('modalPago')?.classList.contains('show');
    if (esCantidad || esModalAbierto) return;

    e.preventDefault();

    const query = inputBuscar.value.trim();
    if (!query) return;

    try {
        const res = await fetch(`/api/productos/buscar?q=${encodeURIComponent(query)}`);
        const data = await res.json();

        if (data.length === 1) {
            const p = data[0];
            agregarAlCarrito({
                id: p.id,
                nombre: p.nombre,
                precio: p.precio,
                stock: p.stock,
                iva: COBRA_IVA ? (p.iva || 0) : 0,
                unidad: p.unidad || 'Unidad'
            });
            inputBuscar.value = '';
            cargarProductos();
        } else if (data.length > 1) {
            actualizarTablaProductos(data);
        } else {
            mostrarAlertaCarrito('Producto no encontrado');
            inputBuscar.value = '';
        }
    } catch (e) {
        mostrarAlertaCarrito('Error al buscar producto');
    } finally {
        setTimeout(() => inputBuscar.focus(), 50);
    }
});

// Agregar al carrito
function agregarAlCarrito(producto) {
    if (producto.stock === 0) {
        mostrarAlertaCarrito(`${producto.nombre} no tiene stock disponible`);
        return;
    }

    const existe = carrito.find(item => item.id === producto.id);
    
    if (existe) {
        if (existe.cantidad < producto.stock) {
            existe.cantidad++;
        } else {
            mostrarAlertaCarrito(`Stock máximo de ${producto.nombre} alcanzado`);
            return;
        }
    } else {
        const precioBase = parseFloat(producto.precio);
        const ivaRate = producto.iva || 0;
        const ivaValor = ivaRate > 0 ? precioBase * ivaRate / 100 : 0;
        const subtotalConIva = precioBase + ivaValor;
        
        const UNIDADES_ENTERAS = ['Unidad','Par','Docena','Caja','Paquete','Sobre','Frasco','Botella','Lata','Tubo'];
carrito.push({
    id: producto.id,
    nombre: producto.nombre,
    precio: precioBase,
    cantidad: 1,
    stock: producto.stock,
    iva: ivaRate,
    subtotalConIva: subtotalConIva,
    unidad: producto.unidad || 'Unidad',
    esEntero: UNIDADES_ENTERAS.includes(producto.unidad || 'Unidad')
});
    }

    actualizarCarrito();
    if (!existe) {
            const fila = document.querySelector(`#carrito-contenido tr[data-item-id="${producto.id}"]`);
            if (fila) {
                fila.classList.add('flash-nuevo');
                fila.addEventListener('animationend', () => fila.classList.remove('flash-nuevo'), { once: true });
            }
        }
}

// Actualizar carrito
function actualizarCarrito() {
    if (carrito.length === 0) {
        document.getElementById('carrito-header').innerHTML = '<tr><td class="text-sm">Sin productos</td></tr>';
        carritoDiv.innerHTML = '<tr><td class="text-sm" colspan="3" style="text-align: center; padding: 20px; color: #999;">El carrito está vacío</td></tr>';
        btnFinalizar.disabled = true;
        totalSpan.textContent = '$0';
        return;
    }

    const headerHtml = `
        <tr>
            <td class="text-sm">Producto</td>
            <td class="text-sm text-center" style="width: 100px;">Cantidad</td>
            <td class="text-sm text-end" style="width: 150px;"></td>
        </tr>
    `;

    const bodyHtml = carrito.map((item, index) => {
        const totalConIva = item.cantidad * item.subtotalConIva;
        return `
            <tr data-item-id="${item.id}">
                <td class="text-sm"><span class="truncate truncate-medium" data-bs-toggle="tooltip" data-bs-title="${item.nombre}">${item.nombre}</span></td>
                <td class="text-sm text-center">
    <div class="d-inline-flex align-items-center gap-1">
        <button  style="padding: 4px 10px;" class="btn btn-light btn-sm px-2" onclick="cambiarCantidad(${index}, ${item.cantidad - (item.esEntero ? 1 : 0.5)})" ${item.cantidad <= (item.esEntero ? 1 : 0.01) ? 'disabled' : ''}>−</button>
        <input type="text" class="cantidad-display" value="${item.cantidad}" 
              style="min-width:2ch;max-width:6ch;width:${String(item.cantidad).length + 1}ch;border:none;border-radius:0;font-size:0.9rem;outline:none;background:transparent;text-align:right;overflow:hidden;"
               oninput="this.style.width=Math.min(this.value.length+1,6)+'ch'" onchange="cambiarCantidadDirecta(${index}, this.value)"
               inputmode="${item.esEntero ? 'numeric' : 'decimal'}">
               <span style="color:#94a3b8;font-size:0.75rem;font-weight:600;">${UNIDAD_ABREV[item.unidad] || item.unidad}</span>
        <button style="padding: 4px 10px;" class="btn btn-light btn-sm px-2" onclick="cambiarCantidad(${index}, ${item.cantidad + (item.esEntero ? 1 : 0.5)})">+</button>
    </div>
   
</td>
                <td class="text-sm text-end" style="vertical-align:middle;">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <span>${formatoPrecio(totalConIva)}</span>
                    <button style="padding: 4px 10px; " class="btn btn-light btn-sm" onclick="confirmarEliminar(${index})" title="Eliminar">
                        <i class="lni lni-trash-can"></i>
                    </button>
                </div>
                </td>
            </tr>
        `;
    }).join('');

    document.getElementById('carrito-header').innerHTML = headerHtml;
    carritoDiv.innerHTML = bodyHtml;
    if (window.initTooltips) window.initTooltips(carritoDiv);
    btnFinalizar.disabled = false;
    actualizarTotal();
    localStorage.setItem('pos_carrito', JSON.stringify({
    data: carrito,
    ts: Date.now()
    }));
}

// Cambiar cantidad
function cambiarCantidad(index, nuevaCantidad) {
    if (index < 0 || index >= carrito.length) {
        mostrarAlertaCarrito('Error al modificar cantidad.');
        return;
    }

    const item = carrito[index];
    nuevaCantidad = Math.round(parseFloat(nuevaCantidad) * 100) / 100;

    if (isNaN(nuevaCantidad) || nuevaCantidad <= 0) return;

    if (nuevaCantidad > item.stock) {
        mostrarAlertaCarrito(`${item.nombre} no tiene más stock disponible`);
        return;
    }

    carrito[index].cantidad = nuevaCantidad;
    actualizarCarrito();
}
function cambiarCantidadDirecta(index, valor) {
    if (index < 0 || index >= carrito.length) return;
    const item = carrito[index];
    
    // Convertir coma a punto
    const parsed = parseFloat(String(valor).replace(',', '.'));
    
    if (isNaN(parsed) || parsed <= 0) {
        mostrarAlertaCarrito('Cantidad inválida');
        actualizarCarrito();
        return;
    }
    
    // Si es unidad entera, redondear
    const nuevaCantidad = item.esEntero ? Math.round(parsed) : Math.round(parsed * 100) / 100;
    
    if (nuevaCantidad > item.stock) {
        mostrarAlertaCarrito(`${item.nombre} no tiene suficiente stock`);
        actualizarCarrito();
        return;
    }
    
    carrito[index].cantidad = nuevaCantidad;
    actualizarCarrito();
}
// Confirmar eliminar
function confirmarEliminar(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
    setTimeout(() => inputBuscar.focus(), 50);
}

// Actualizar total
function actualizarTotal() {
    const total = carrito.reduce((sum, item) => {
        return sum + (item.cantidad * item.subtotalConIva);
    }, 0);
    totalSpan.textContent = formatoPrecio(total);
    document.getElementById('modal-total').textContent = formatoPrecio(total);
    // Guardar valor numérico para cálculos posteriores
    window.totalVentaNumeric = total;
    calcularPorCobrar();
    validarCupoCredito();
}

// Calcula y muestra el por cobrar / cambio en el modal
function calcularPorCobrar() {
    const modalPorCobrar = document.getElementById('modal-por-cobrar');
    if (!modalPorCobrar) return;

    const totalVenta = window.totalVentaNumeric || 0;
    let totalPagado = 0;
    const input = document.getElementById('total_pagado');
    if (input && input.value !== '') {
        totalPagado = parseInputNumber(input.value);
    }

    const diferencia = totalPagado - totalVenta;
    const absFormatted = formatoPrecio(Math.abs(diferencia));

    // Determinar etiqueta y color
    let label = 'Por cobrar';
    let color = '#333';
    if (diferencia > 0) {
        label = 'Cambio';
        color = '#1f7a1f';
    } else if (diferencia < 0) {
        label = 'Falta';
        color = '#fb6a6a';
    }

    // Actualizar contenido: etiqueta + valor
    const parent = modalPorCobrar.parentElement;
    if (parent) {
        parent.innerHTML = `${label}: <span id="modal-por-cobrar" style="color: ${color};">${absFormatted}</span>`;
    } else {
        modalPorCobrar.textContent = absFormatted;
        modalPorCobrar.style.color = color;
    }

    // No bloquear el botón por diferencia de montos. El único bloqueo visual
    // será cuando se intente finalizar y el campo esté vacío.
}

// Seleccionar método de pago
function seleccionarPago(metodo) {
    document.getElementById('forma_pago').value = metodo;
    ['efectivo','tarjeta','transferencia','credito'].forEach(m => {
        const btn = document.getElementById('pago-' + m);
        if (btn) btn.style.borderColor = metodo === m ? '#4CAF50' : 'transparent';
    });

    const aviso = document.getElementById('bloque-credito-aviso');
    const clienteId = document.getElementById('cliente_id').value;

    if (metodo === 'credito') {
        // Solo mostrar aviso si no hay cliente seleccionado
        if (aviso) aviso.style.display = clienteId ? 'none' : 'block';
        document.getElementById('total_pagado').value = '0';
        document.getElementById('total_pagado').disabled = true;
    } else {
        if (aviso) aviso.style.display = 'none';
        document.getElementById('total_pagado').disabled = false;
        // No limpiar cliente ni total al cambiar de método
    }
    validarCupoCredito();
}

// Confirmar venta
async function confirmarVenta() {
    if (carrito.length === 0) {
        mostrarMensaje('El carrito está vacío', 'error');
        return;
    }
 if (clienteSeleccionadoId) {
        try {
            const res = await fetch(`/api/clientes/buscar?q=${encodeURIComponent(document.getElementById('buscar-cliente-input').value)}`, {
                headers: { 'Accept': 'application/json' }
            });
            const clientes = await res.json();
            const fresco = clientes.find(c => c.id === clienteSeleccionadoId);
            if (fresco) {
                clienteCupoCredito = fresco.cupo_credito ?? null;
                clienteSaldoPendiente = fresco.saldo_pendiente ?? 0;
            }
        } catch(e) {}
    }

    const total = carrito.reduce((sum, item) => sum + (item.cantidad * item.subtotalConIva), 0);
    document.getElementById('modal-total').textContent = formatoPrecio(total);
    validarCupoCredito();

    const modal = new bootstrap.Modal(document.getElementById('modalPago'));
    modal.show();
}
   

// Finalizar venta
async function finalizarVenta() {
    // ========== VALIDACIÓN 1: PREVENIR RACE CONDITION ==========
    if (ventaEnProceso) {
        mostrarAlertaCarrito('La venta ya está siendo procesada. Por favor, espera...');
        return;
    }

    // ========== VALIDACIÓN 2: VERIFICAR CSRF TOKEN ==========
    if (!csrfTokenGlobal) {
        mostrarMensaje('Error de seguridad. Por favor, recarga la página.', 'error');
        return;
    }

    const cliente = document.getElementById('cliente').value.trim() || null;
    const cliente_nit = document.getElementById('cliente_nit').value.trim() || null;
    const forma_pago = document.getElementById('forma_pago').value;
    const totalPagadoInput = document.getElementById('total_pagado');

    if (forma_pago === 'credito' && !clienteSeleccionadoId) {
        const err = document.getElementById('error-cliente-credito');
        if (err) err.textContent = 'Debes seleccionar un cliente para ventas a crédito.';
        return;
    }

if (forma_pago === 'credito' && clienteCupoCredito !== null) {
    if (clienteCupoCredito === -1) {
    } else {
        const cupoDisponible = clienteCupoCredito - clienteSaldoPendiente;
        if ((window.totalVentaNumeric || 0) > cupoDisponible) {
            return;
        }
    }
}


    // Validación visual: si está vacío al presionar Finalizar, mostrar sólo el mensaje inline del campo
    if (!totalPagadoInput || totalPagadoInput.value.trim() === '') {
        if (totalPagadoInput) {
            totalPagadoInput.classList.add('is-invalid');
            const err = document.getElementById('error-totalpagado');
            if (err) { err.textContent = 'Ingresa el total pagado por el cliente'; err.style.display = 'block'; }
        }
        return;
    }

    const total_pagado = parseInputNumber(totalPagadoInput.value);
    if (totalPagadoInput) totalPagadoInput.classList.remove('is-invalid');
    // Asegurar que el mensaje inline esté oculto si el campo es válido
    try { const err = document.getElementById('error-totalpagado'); if (err) err.style.display = 'none'; } catch (e) {}


    // ========== VALIDACIÓN 4: VALIDAR LONGITUD DE CARACTERES ==========
    if (cliente && cliente.length > 40) {
        mostrarMensaje('El nombre del cliente no puede exceder 40 caracteres', 'error');
        return;
    }
    
    if (cliente_nit && cliente_nit.length > 40) {
        mostrarMensaje('El NIT/Documento no puede exceder 40 caracteres', 'error');
        return;
    }

    // ========== VALIDACIÓN 3: VALIDAR CARRITO NO ESTÉ VACÍO ==========
    if (!carrito || carrito.length === 0) {
        mostrarMensaje('El carrito está vacío', 'error');
        return;
    }

    // Nota: NO se valida que total_pagado cubra el total de la venta. Se permite guardar incluso si es menor.

    const data = {
        cliente: cliente,
        cliente_nit: cliente_nit,
        cliente_id: clienteSeleccionadoId || null,
        forma_pago: forma_pago,
        total_pagado: total_pagado,
        productos: carrito.map(item => ({
            id: item.id,
            cantidad: item.cantidad,
            precio: item.precio,
            iva: item.iva || 0
        }))
    };

    // ========== MARCAR VENTA COMO EN PROCESO ==========
    ventaEnProceso = true;
    const btnConfirmar = document.getElementById('btn-confirmar-pago');
    if (btnConfirmar) {
        btnConfirmar.disabled = true;
    }

    // Timeout para mostrar loading (solo si tarda más de 300ms)
    let loadingTimeout = setTimeout(() => {
        modalEstado('loading');
    }, 300);

    try {
        const res = await fetch('/ventas', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfTokenGlobal,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        // Cancelar timeout si aún no se mostró el loading
        clearTimeout(loadingTimeout);

        // ========== MANEJO DE RESPUESTA CON VALIDACIÓN ==========
        let result;
        try {
            result = await res.json();
        } catch (e) {
            throw new Error('Respuesta inválida del servidor');
        }

        if (!res.ok) throw result;

        // Mostrar pantalla de éxito
        const resultadoTotal = document.getElementById('resultado-total');
        if (resultadoTotal) {
            resultadoTotal.textContent = formatoPrecio(result.total);
        }
        
        // Guardar ID de venta para ver factura
        window.ultimaVentaId = result.venta_id;
        localStorage.removeItem('pos_carrito');
        localStorage.removeItem('pos_cliente');
        modalEstado('exito');
        
    } catch (error) {
        // Cancelar timeout
        clearTimeout(loadingTimeout);
        
        const resultadoError = document.getElementById('resultado-error');
        if (resultadoError) {
            resultadoError.textContent = 'No se pudo procesar la venta. Por favor, intenta de nuevo.';
        }
        modalEstado('error');
        
    } finally {
        // ========== LIMPIAR FLAG Y BOTONES ==========
        ventaEnProceso = false;
        if (btnConfirmar) {
            btnConfirmar.disabled = false;
        }
        
        // Limpiar mensajes de caracteres
        limpiarMensajesCaracteres();
    }
}

// Cambiar estado del modal
function modalEstado(estado) {
    const estados = ['formulario', 'loading', 'exito', 'error'];
    
    // Ocultar todos
    estados.forEach(e => {
        document.getElementById(`estado-${e}`).classList.add('d-none');
    });
    
    // Mostrar el seleccionado
    document.getElementById(`estado-${estado}`).classList.remove('d-none');
}

// Ver factura
function irAFactura() {
    if (window.ultimaVentaId) {
        window.location.href = `/ventas/${window.ultimaVentaId}/factura`;
    }
}


// Nueva venta
function nuevaVenta() {
    // 1. Ocultar visualmente el modal de inmediato
    const modalElement = document.getElementById('modalPago');
    if (modalElement) {
        modalElement.style.opacity = '0';
        modalElement.style.transition = 'none'; // Sin transición para que sea instantáneo
    }
    
    // 2. Cambiar al estado formulario
    modalEstado('formulario');
    
    // 3. Cerrar el modal
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) {
        modalInstance.hide();
    }

    localStorage.removeItem('pos_carrito');
    localStorage.removeItem('pos_cliente');
    // 4. Recargar la página después de que el modal se cierre
    setTimeout(() => {
        location.reload();
    }, 100); // Reducido porque el modal ya está visualmente oculto
}

// Limpiar venta
function limpiarVenta() {
    carrito = [];
    limpiarClienteSeleccionado(); 
    document.getElementById('forma_pago').value = 'efectivo';
    inputBuscar.value = '';
    actualizarCarrito();
    cargarProductos();
    seleccionarPago('efectivo');
    localStorage.removeItem('pos_carrito');
localStorage.removeItem('pos_cliente');
}

// Mostrar mensaje
function mostrarMensaje(texto, tipo) {
    const mensajeDiv = document.getElementById('mensaje');
    const textoMensaje = document.getElementById('texto-mensaje');
    
    // Mapear tipo de mensaje a clase de Bootstrap
    const claseAlerta = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'info': 'alert-info',
        'warning': 'alert-warning'
    };
    
    textoMensaje.textContent = texto;
    mensajeDiv.className = `alert alert-dismissible fade show mb-3 ${claseAlerta[tipo] || 'alert-info'}`;
    mensajeDiv.style.margin = '15px 0';
    
    // Auto-cerrar después de 4 segundos
    setTimeout(() => {
        mensajeDiv.classList.remove('show');
        mensajeDiv.classList.add('d-none');
    }, 4000);
}

// Mostrar alerta en el carrito
function mostrarAlertaCarrito(texto) {
    const alertaDiv = document.getElementById('alerta-carrito');
    const textoAlerta = document.getElementById('texto-alerta-carrito');
    
    textoAlerta.textContent = texto;
    alertaDiv.classList.remove('d-none');
    alertaDiv.classList.add('show');
    
    // Auto-cerrar después de 2.5 segundos
    setTimeout(() => {
        alertaDiv.classList.add('d-none');
        alertaDiv.classList.remove('show');
    }, 2500);
}

// Limpiar carrito cuando se cierra el modal
document.getElementById('modalPago').addEventListener('hidden.bs.modal', function() {
    // IMPORTANTE: NO limpiamos el carrito aquí
    // Si el estado de éxito está visible, recargar la página y no ejecutar
    // ninguna otra lógica (permite limpiar todo y evitar estados inconsistentes).
    var estadoExito = document.getElementById('estado-exito');
    if (estadoExito && !estadoExito.classList.contains('d-none')) {
        location.reload();
        return;
    }

    // En cualquier otro caso, conservar comportamiento previo
    modalEstado('formulario');
    // Recargar productos para actualizar stock
    cargarProductos();
});

// ========== INICIALIZACIÓN CON VALIDACIONES DE SEGURIDAD ==========
document.addEventListener('DOMContentLoaded', function() {
    // PASO 1: Validar todos los elementos del DOM
    if (!validarElementosDOM()) {
        mostrarMensaje('Error en la inicialización de la página. Por favor, recarga.', 'error');
        return;
    }

    // PASO 2: Validar y guardar CSRF token
    csrfTokenGlobal = validarCSRFToken();
    if (!csrfTokenGlobal) {
        mostrarMensaje('Error de seguridad. Por favor, recarga la página.', 'error');
        return;
    }

    // PASO 3: Inicializar funcionalidad normal
    cargarProductos();
    seleccionarPago('efectivo');

    
});
</script>

@endif

@endsection