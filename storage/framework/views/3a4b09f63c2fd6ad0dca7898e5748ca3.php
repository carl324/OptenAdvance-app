

<?php $__env->startSection('title', 'Clientes'); ?>

<?php $__env->startSection('content'); ?>
<style>
.producto-dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-trigger {
    background: none;
    border: none;
    cursor: pointer;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 18px;
    transition: all 0.2s;
}

.dropdown-trigger:hover {
    background: #f1f5f9;
    color: #475569;
}

.dropdown-menu-custom {
    display: none;
    position: absolute;
    right: 0;
    top: 36px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    z-index: 999;
    min-width: 140px;
    overflow: hidden;
    animation: dropdownFadeIn 0.15s ease-out;
}

.dropdown-menu-custom.open {
    display: block;
}

@keyframes dropdownFadeIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.dropdown-menu-custom button {
    width: 100%;
    padding: 10px 16px;
    background: none;
    border: none;
    text-align: left;
    font-size: 14px;
    font-weight: 500;
    color: #334155;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.15s;
    border-radius: 0;
}

.dropdown-menu-custom button:hover {
    background: #f8fafc;
}

.dropdown-menu-custom button.danger {
    color: #dc2626;
}

.dropdown-menu-custom button.danger:hover {
    background: #fef2f2;
}
.dropdown-menu-custom {
    min-width: 140px;
    max-width: 140px;
}
.producto-dropdown {
    position: relative;
    display: inline-block;
}
.table-wrapper {
    overflow: visible !important;
}

.table-responsive {
    overflow: visible !important;
}
.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    padding: 20px;
    margin-top: 20px;
}
.pagination button {
    padding: 8px 12px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.pagination button:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}
.pagination button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
<section class="table-components">
  <div class="container-fluid">

    <div class="title-wrapper pt-30">
      
    </div>

    <div class="tables-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <div class="card-style mb-30">

            
            <div class="title d-flex align-items-center justify-content-between">

  
  <div>
    <h6 style="margin-bottom: 10px;">Tabla de Clientes</h6>
    <div class="input-group input-group-sm search-pos" style="width:240px;">
      <span class="input-group-text bg-light border-0">
        <i class="lni lni-search-alt"></i>
      </span>
      <input
        type="text"
        id="buscador"
        class="form-control bg-light border-0"
        placeholder="Buscar Cliente..."
        value="<?php echo e(request('search')); ?>"
        autocomplete="off"
      />
    </div>
  </div>

  
  <div class="ms-auto d-flex align-items-center gap-2">
    <button class="btn btn-primary btn-sm d-flex align-items-center gap-1" onclick="abrirModalCrear()">
      <i class="lni lni-plus"></i> Nuevo cliente
    </button>
  </div>

</div>
<br>

            
            <div class="table-wrapper table-responsive">
              <table class="table" id="tabla-clientes">
                <thead>
                  <tr>
                    <th><h6>Nombre</h6></th>
                    <th><h6>Teléfono</h6></th>
                    <th><h6>NIT / CC</h6></th>
                    <th><h6>Cupo crédito</h6></th>
                    <th><h6>Saldo pendiente</h6></th>
                    <th><h6>Acción</h6></th>
                  </tr>
                </thead>
                <br>
                <tbody id="tbody-clientes">
                  <?php echo $__env->make('clientes._table', ['clientes' => $clientes], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </tbody>
              </table>
            </div>

            
            <div class="pagination">
    <button id="btn-prev-clientes" onclick="cambiarPaginaClientes('prev')" disabled>
        <i class="lni lni-chevron-left"></i>
    </button>
    <span class="text-sm">Página <strong id="current-page-clientes">1</strong> de <strong id="last-page-clientes">1</strong></span>
    <button id="btn-next-clientes" onclick="cambiarPaginaClientes('next')" disabled>
        <i class="lni lni-chevron-right"></i>
    </button>
</div>

          </div>
        </div>
      </div>
    </div>

  </div>
</section>


<div id="modal-crear" class="modal-overlay" style="display:none; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(8px); align-items: center; justify-content: center; position: fixed; inset: 0; z-index: 1000; padding: 24px;">

  <div style="background: #ffffff; width: 100%; max-width: 820px; border-radius: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.10); border: 1px solid #f1f5f9; overflow: hidden; position: relative;">

    <button type="button" onclick="cerrarModalCrear()" style="position:absolute;top:16px;right:16px;background:#f8fafc;border:1px solid #e2e8f0;cursor:pointer;width:36px;height:36px;border-radius:10px;color:#94a3b8;display:flex;align-items:center;justify-content:center;z-index:10;">
      <i class="lni lni-close" style="font-size:12px;"></i>
    </button>

    <div style="display:flex; min-height:400px;">

      
      <div style="flex:1.2; padding:32px 40px; border-right:1px solid #f1f5f9;">
        <div style="margin-bottom:24px;">
          <span style="background:#eff6ff;color:#3b82f6;padding:4px 10px;border-radius:8px;font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;">Nuevo</span>
          <h3 style="margin:10px 0 4px;font-size:1.6rem;font-weight:800;color:#0f172a;letter-spacing:-0.03em;">Nuevo Cliente</h3>
          <p style="font-size:0.875rem;color:#94a3b8;margin:0;">Completa los datos del cliente.</p>
          <div id="alertContainerCrear" style="margin-top:12px;"></div>
        </div>

        <div style="display:flex;flex-direction:column;gap:14px;">

          <div>
            <label style="font-size:0.7rem;font-weight:700;color:#0f172a;text-transform:uppercase;display:block;margin-bottom:8px;">Nombre <span style="color:#ef4444;">*</span></label>
            <input type="text" id="crear-nombre" placeholder="Nombre completo"
                   style="width:100%;padding:10px 0;border:none;border-bottom:2px solid #f1f5f9;font-size:1.1rem;color:#0f172a;font-weight:500;outline:none;transition:0.3s;"
                   onfocus="this.style.borderColor='#2478ff'" onblur="this.style.borderColor='#f1f5f9'">
            <span id="error-crear-nombre" style="font-size:12px;color:#ef4444;min-height:16px;display:block;"></span>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div>
              <label style="font-size:0.7rem;font-weight:700;color:#0f172a;text-transform:uppercase;display:block;margin-bottom:8px;">Teléfono</label>
              <input type="text" id="crear-telefono" placeholder="300 000 0000"
                     style="width:100%;padding:10px 0;border:none;border-bottom:2px solid #f1f5f9;font-size:1rem;color:#0f172a;outline:none;transition:0.3s;"
                     onfocus="this.style.borderColor='#2478ff'" onblur="this.style.borderColor='#f1f5f9'">
                     <span id="error-crear-telefono" style="font-size:12px;color:#ef4444;min-height:16px;display:block;"></span>
            </div>
            <div>
              <label style="font-size:0.7rem;font-weight:700;color:#0f172a;text-transform:uppercase;display:block;margin-bottom:8px;">NIT / CC</label>
              <input type="text" id="crear-nit" placeholder="000.000.000"
                     style="width:100%;padding:10px 0;border:none;border-bottom:2px solid #f1f5f9;font-size:1rem;color:#0f172a;outline:none;transition:0.3s;"
                     onfocus="this.style.borderColor='#2478ff'" onblur="this.style.borderColor='#f1f5f9'">
                     <span id="error-crear-nit" style="font-size:12px;color:#ef4444;min-height:16px;display:block;"></span>
            </div>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:8px;">
  <div>
    <label style="font-size:0.7rem;font-weight:700;color:#0f172a;text-transform:uppercase;display:block;margin-bottom:8px;">Email</label>
    <input type="email" id="crear-email" placeholder="correo@email.com"
           style="width:100%;padding:10px 0;border:none;border-bottom:2px solid #f1f5f9;font-size:1rem;color:#0f172a;outline:none;transition:0.3s;"
           onfocus="this.style.borderColor='#2478ff'" onblur="this.style.borderColor='#f1f5f9'">
  </div>
  <div>
    <label style="font-size:0.7rem;font-weight:700;color:#0f172a;text-transform:uppercase;display:block;margin-bottom:8px;">Dirección</label>
    <input type="text" id="crear-direccion" placeholder="Dirección"
           style="width:100%;padding:10px 0;border:none;border-bottom:2px solid #f1f5f9;font-size:1rem;color:#0f172a;outline:none;transition:0.3s;"
           onfocus="this.style.borderColor='#2478ff'" onblur="this.style.borderColor='#f1f5f9'">
  </div>
</div>

        </div>
      </div>
<br>
      
      <div style="flex:1;background:#f8fafc;padding:32px 36px;display:flex;flex-direction:column;justify-content:space-between;">

        <div style="display:flex;flex-direction:column;gap:20px;">

          
          <div style="background:#ffffff;padding:16px;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,0.04);display:flex;align-items:center;justify-content:space-between;margin-top:30px;">
            <div>
              <span style="display:block;font-size:0.7rem;font-weight:800;color:#334155;text-transform:uppercase;margin-bottom:2px;">Crédito</span>
              <span style="font-size:0.8rem;color:#94a3b8;">Permitir compras a crédito</span>
            </div>
            <label style="position:relative;display:inline-block;width:44px;height:24px;cursor:pointer;">
              <input type="checkbox" id="crear-credito-toggle" onchange="toggleCupo('crear')" style="opacity:0;width:0;height:0;">
              <span id="crear-credito-slider" style="position:absolute;inset:0;background:#e2e8f0;border-radius:24px;transition:0.3s;"></span>
              <span id="crear-credito-dot" style="position:absolute;left:3px;top:3px;width:18px;height:18px;background:white;border-radius:50%;transition:0.3s;box-shadow:0 1px 3px rgba(0,0,0,0.2);"></span>
            </label>
          </div>

          
          <div id="crear-cupo-wrapper" style="display:none;flex-direction:column;gap:16px;">
            <label style="font-size:0.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;">Cupo máximo</label>
            <div style="display:flex;gap:8px;">
              <button type="button" id="crear-cupo-ilimitado" onclick="setCupoTipo('crear','ilimitado')"
                      style="flex:1;padding:8px;border-radius:20px;border:2px solid #2478ff;background:#2478ff;color:white;font-size:0.8rem;font-weight:600;cursor:pointer;transition:0.2s;">
                Sin límite
              </button>
              <button type="button" id="crear-cupo-limitado" onclick="setCupoTipo('crear','limitado')"
                      style="flex:1;padding:8px;border-radius:20px;border:2px solid #e2e8f0;background:white;color:#64748b;font-size:0.8rem;font-weight:600;cursor:pointer;transition:0.2s;">
                Con límite
              </button>
            </div>
            <div id="crear-cupo-monto" style="display:none;align-items:center;background:white;padding:12px 16px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
              <span style="color:#94a3b8;font-weight:600;font-size:1.2rem;margin-right:6px;">$</span>
              <input type="text" id="crear-cupo" placeholder="Ej: 10.000" min="0"
                   oninput="this.value=this.value.replace(/\D/g,'').replace(/\B(?=(\d{3})+(?!\d))/g,'.')"
                   style="width:100%;border:none;font-size:1.2rem;font-weight:700;color:#0f172a;outline:none;background:transparent;">
            </div>
          </div>

        </div>

        
        <div style="display:flex;flex-direction:column;gap:10px;margin-top:20px;">
          <button type="button" id="btn-crear" onclick="guardarCliente()"
                  style="width:100%;height:52px;border-radius:14px;border:none;background:#2478ff;color:#ffffff;font-size:1rem;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(36,120,255,0.2);">
            <span id="btn-crear-text">Guardar Cliente</span>
          </button>
          <button type="button" onclick="cerrarModalCrear()"
                  style="background:none;border:none;color:#94a3b8;font-weight:600;font-size:0.875rem;cursor:pointer;padding:6px;">
            Cancelar
          </button>
        </div>

      </div>
    </div>
  </div>
</div>



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
        <button type="button" class="main-btn light-btn btn-hover" data-bs-dismiss="modal" onclick="document.querySelector('#modal-eliminar .danger-btn').style.display=''">
         Cancelar</button>
        <button type="button" class="main-btn danger-btn btn-hover" onclick="eliminarCliente()">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let modalEliminar;
let clienteEliminarId = null;

function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

async function cargarClientes(page = 1) {
    const termino = document.getElementById('buscador').value.trim();
    const tbody   = document.querySelector('#tbody-clientes');
    const url     = new URL('<?php echo e(route("clientes.index")); ?>', window.location.origin);
    url.searchParams.append('page', page);
    if (termino) url.searchParams.append('search', termino);

    const prevHtml = tbody ? tbody.innerHTML : '';
    if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-gray">Buscando...</td></tr>';

    try {
        const res    = await fetch(url.toString(), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }});
        const result = await res.json();
        if (!result.success) throw new Error();
        if (tbody) tbody.innerHTML = result.html;
        const { current_page, last_page } = result.pagination;
        document.getElementById('current-page-clientes').textContent = current_page;
        document.getElementById('last-page-clientes').textContent    = last_page;
        document.getElementById('btn-prev-clientes').disabled = current_page === 1;
        document.getElementById('btn-next-clientes').disabled = current_page === last_page;
    } catch (e) {
        if (tbody) tbody.innerHTML = prevHtml;
    }
}

function cambiarPaginaClientes(direccion) {
    const current = parseInt(document.getElementById('current-page-clientes').textContent);
    const last    = parseInt(document.getElementById('last-page-clientes').textContent);
    const nueva   = direccion === 'prev' ? current - 1 : current + 1;
    if (nueva < 1 || nueva > last) return;
    cargarClientes(nueva);
}

document.addEventListener('DOMContentLoaded', function () {
    modalEliminar = new bootstrap.Modal(document.getElementById('modal-eliminar'));

    cargarClientes(1);

    const busquedaDebounced = debounce(() => cargarClientes(1), 300);
    document.getElementById('buscador').addEventListener('input', busquedaDebounced);
    document.getElementById('buscador').addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { this.value = ''; cargarClientes(1); this.blur(); }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-trigger')) {
            document.querySelectorAll('.dropdown-menu-custom.open').forEach(m => m.classList.remove('open'));
        }
    });
});

function abrirModalCrear() {
    document.getElementById('crear-credito-toggle').checked = false;
    toggleCupo('crear');
    setCupoTipo('crear', 'ilimitado');
    document.getElementById('crear-nombre').value    = '';
    document.getElementById('crear-telefono').value  = '';
    document.getElementById('crear-nit').value       = '';
    document.getElementById('crear-email').value     = '';
    document.getElementById('crear-direccion').value = '';
    document.getElementById('crear-cupo').value      = '0';
document.getElementById('error-crear-nombre').textContent   = '';
document.getElementById('error-crear-telefono').textContent = '';
document.getElementById('error-crear-nit').textContent      = '';
document.getElementById('alertContainerCrear').innerHTML    = '';
    document.getElementById('modal-crear').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarModalCrear() {
    document.getElementById('modal-crear').style.display = 'none';
    document.body.style.overflow = '';
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
        const res = await fetch('<?php echo e(route("clientes.store")); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                nombre,
                telefono:  document.getElementById('crear-telefono').value.trim() || null,
                nit:       document.getElementById('crear-nit').value.trim() || null,
                email:     document.getElementById('crear-email').value.trim() || null,
                direccion: document.getElementById('crear-direccion').value.trim() || null,
                cupo_credito: (() => {
                    if (!document.getElementById('crear-credito-toggle').checked) return null;
                    const ilimitado = document.getElementById('crear-cupo-ilimitado');
                    if (ilimitado.style.background === 'rgb(36, 120, 255)') return -1;
                    return parseInt(document.getElementById('crear-cupo').value.replace(/\./g, '')) || 0;
                })(),
            })
        });
        const data = await res.json();
if (data.success) {
    cerrarModalCrear();
    cargarClientes(1);
} else {
    document.getElementById('error-crear-nombre').textContent    = data.errors?.nombre?.[0]    || '';
    document.getElementById('error-crear-telefono').textContent  = data.errors?.telefono?.[0]  || '';
    document.getElementById('error-crear-nit').textContent       = data.errors?.nit?.[0]       || '';
}
    } catch (e) {
        console.error(e);
    } finally {
        btn.disabled = false;
        document.getElementById('btn-crear-text').textContent = 'Guardar';
    }
}

function toggleCupo(prefix) {
    const toggle  = document.getElementById(`${prefix}-credito-toggle`);
    const slider  = document.getElementById(`${prefix}-credito-slider`);
    const dot     = document.getElementById(`${prefix}-credito-dot`);
    const wrapper = document.getElementById(`${prefix}-cupo-wrapper`);
    if (toggle.checked) {
        slider.style.background = '#2478ff';
        dot.style.left = '23px';
        wrapper.style.display = 'flex';
    } else {
        slider.style.background = '#e2e8f0';
        dot.style.left = '3px';
        wrapper.style.display = 'none';
        document.getElementById(`${prefix}-cupo`).value = '';
    }
}

function setCupoTipo(prefix, tipo) {
    const btnIlimitado = document.getElementById(`${prefix}-cupo-ilimitado`);
    const btnLimitado  = document.getElementById(`${prefix}-cupo-limitado`);
    const montoDiv     = document.getElementById(`${prefix}-cupo-monto`);
    if (tipo === 'ilimitado') {
        btnIlimitado.style.background = '#2478ff';
        btnIlimitado.style.color = 'white';
        btnIlimitado.style.borderColor = '#2478ff';
        btnLimitado.style.background = 'white';
        btnLimitado.style.color = '#64748b';
        btnLimitado.style.borderColor = '#e2e8f0';
        montoDiv.style.display = 'none';
        document.getElementById(`${prefix}-cupo`).value = '';
    } else {
        btnLimitado.style.background = '#2478ff';
        btnLimitado.style.color = 'white';
        btnLimitado.style.borderColor = '#2478ff';
        btnIlimitado.style.background = 'white';
        btnIlimitado.style.color = '#64748b';
        btnIlimitado.style.borderColor = '#e2e8f0';
        montoDiv.style.display = 'flex';
        setTimeout(() => document.getElementById(`${prefix}-cupo`).focus(), 50);
    }
}

function toggleDropdown(btn) {
    const menu = btn.nextElementSibling;
    const isOpen = menu.classList.contains('open');
    document.querySelectorAll('.dropdown-menu-custom.open').forEach(m => m.classList.remove('open'));
    if (!isOpen) menu.classList.add('open');
}
async function eliminarCliente() {
    if (!clienteEliminarId) return;
    try {
        const res  = await fetch(`/clientes/${clienteEliminarId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            modalEliminar.hide();
            cargarClientes(1);
        } else {
            document.getElementById('eliminar-nombre').innerHTML =
                `<span style="color:#dc2626;">${data.message}</span>`;
            document.querySelector('#modal-eliminar .danger-btn').style.display = 'none';
        }
    } catch (e) {
        console.error(e);
    }
}
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\optenadvance\app\www\resources\views/clientes/index.blade.php ENDPATH**/ ?>