@extends('layouts.app')

@section('title','Empresa — Configuración')

@section('content')

<style>
        /* Estilos simples y auto-contenidos para uso offline */
        :root{--bg:#f6f7fb;--card:#ffffff;--accent:#1f5fbf;--muted:#6b7280}
        html,body{height:100%;margin:0;font-family:Inter,Segoe UI,Arial,Helvetica,sans-serif;background:var(--bg);color:#111}
        .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        .card{width:100%;max-width:820px;background:var(--card);box-shadow:0 6px 18px rgba(16,24,40,.06);border-radius:10px;padding:28px}
        h1{margin:0 0 8px;font-size:20px}
        p.lead{margin:0 0 18px;color:var(--muted)}
        form{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        label{display:block;font-weight:600;margin-bottom:8px;font-size:13px}
        input[type="text"],input[type="email"],select,textarea{width:100%;padding:12px 14px;border:1px solid #e6e9ef;border-radius:8px;font-size:15px}
        textarea{min-height:90px;resize:vertical}
        .full{grid-column:1/ -1}
        .actions{display:flex;justify-content:flex-end;gap:12px;margin-top:8px}
        button.primary{background:var(--accent);color:#fff;border:none;padding:10px 16px;border-radius:8px;font-weight:600;cursor:pointer}
        .note{font-size:13px;color:var(--muted)}
        .msg{padding:10px 12px;border-radius:8px;margin-bottom:12px}
        .msg.success{background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0}
        .msg.error{background:#fff1f2;color:#981b1b;border:1px solid #fecaca}
        .errors{background:#fff7ed;color:#92400e;border:1px solid #ffd8a8;padding:10px;border-radius:8px;margin-bottom:12px}
        @media (max-width:640px){form{grid-template-columns:1fr} .actions{justify-content:stretch}}

        /* Modal simple y reutilizable */
        .modal-mask{position:fixed;inset:0;background:rgba(15,23,42,0.55);display:flex;align-items:center;justify-content:center;z-index:50;padding:16px;visibility:hidden;opacity:0;transition:opacity .2s ease,visibility .2s ease}
        .modal-mask.active{visibility:visible;opacity:1}
        .modal-box{background:#fff;border-radius:12px;max-width:420px;width:100%;box-shadow:0 10px 30px rgba(0,0,0,.18);padding:20px 22px;display:flex;flex-direction:column;gap:12px}
        .modal-title{font-weight:700;font-size:16px;margin:0;color:#0f172a}
        .modal-body{font-size:14px;color:#334155;line-height:1.45;white-space:pre-line}
        .modal-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:6px}
        .btn{border-radius:8px;border:1px solid #e2e8f0;padding:8px 12px;font-weight:600;cursor:pointer;font-size:14px}
        .btn.secondary{background:#fff;color:#0f172a}
        .btn.primary{background:var(--accent);color:#fff;border-color:var(--accent)}
    </style>

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
        <div class="card">
            <h1>Datos de la empresa</h1>
            <p class="lead">Información básica para factura y contabilidad — v1.0.0</p>

            {{-- Mensajes claros para el usuario --}}
            @if(session('success'))
                <div class="msg success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="msg error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="errors">
                    <strong>Hay algunos problemas con la información enviada:</strong>
                    <ul style="margin:8px 0 0;padding-left:18px">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('empresa.update') }}" novalidate>
                @csrf

                <div class="full">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $empresa->nombre ?? '') }}" required>
                </div>

                <div>
                    <label for="nit">NIT</label>
                    <input id="nit" name="nit" type="text" value="{{ old('nit', $empresa->nit ?? '') }}" required>
                </div>

                <div>
                    <label for="moneda">Moneda</label>
                    <input id="moneda" name="moneda" type="text" value="{{ old('moneda', $empresa->moneda ?? '') }}" required>
                </div>

                <div class="full">
                    <label for="direccion">Dirección</label>
                    <textarea id="direccion" name="direccion">{{ old('direccion', $empresa->direccion ?? '') }}</textarea>
                </div>

                <div>
                    <label for="telefono">Teléfono</label>
                    <input id="telefono" name="telefono" type="text" value="{{ old('telefono', $empresa->telefono ?? '') }}">
                </div>

                <div>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $empresa->email ?? '') }}">
                </div>

                <div class="full">
                    <label style="display:flex;align-items:center;gap:12px;font-weight:500;">
                        <span>¿La empresa cobra IVA?</span>
                        <span style="display:inline-flex;align-items:center;gap:6px;">
                            <input type="checkbox" id="switch-cobra-iva" {{ old('cobra_iva', $empresa->cobra_iva ?? 0) ? 'checked' : '' }} style="display:none;">
                            <span id="switch-cobra-iva-ui" tabindex="0" role="switch" aria-checked="{{ old('cobra_iva', $empresa->cobra_iva ?? 0) ? 'true' : 'false' }}" style="width:44px;height:24px;display:inline-block;position:relative;background:#e6e9ef;border-radius:12px;cursor:pointer;transition:background 0.2s;outline:none;">
                                <span style="position:absolute;top:2px;left:2px;width:20px;height:20px;background:#fff;border-radius:50%;box-shadow:0 1px 4px #0001;transition:left 0.2s;" id="switch-cobra-iva-knob"></span>
                            </span>
                            <span id="switch-cobra-iva-label" style="font-size:13px;color:#888;min-width:32px;">{{ old('cobra_iva', $empresa->cobra_iva ?? 0) ? 'Sí' : 'No' }}</span>
                        </span>
                    </label>
                </div>
                <div class="full actions">
                    <span class="note">Los cambios se guardan automáticamente.</span>
                </div>
            </form>
            
            {{-- Sección de copia de seguridad manual (no intrusiva) --}}
            <hr style="margin:18px 0;border:none;border-top:1px solid #eef2ff">
            <div style="display:flex;flex-direction:column;gap:10px">
                <div class="note">Crear un respaldo local del archivo de base de datos. Se guardará en tu carpeta <strong>Descargas</strong> dentro de <em>opten-backups</em>. No se realizan restauraciones automáticas.</div>

                <form method="POST" action="{{ route('backup.store') }}">
                    @csrf
                    <label style="font-size:13px;color:#6b7280;display:flex;align-items:center;gap:8px">
                        <input type="checkbox" name="confirm_backup" required>
                        He leído y acepto que se generará un archivo en mi carpeta de Descargas.
                    </label>
                    <div style="display:flex;justify-content:flex-end;margin-top:6px">
                        <button type="submit" class="primary">Crear copia de seguridad</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de advertencia para desactivar IVA -->
    <div id="modal-iva" class="modal-mask" aria-hidden="true" role="dialog" aria-modal="true">
        <div class="modal-box">
            <h3 class="modal-title">¿Desactivar IVA?</h3>
            <p class="modal-body">Actualmente tienes productos que cobran IVA.
Después de realizar este cambio, los productos que cobran IVA dejarán de cobrarlo.</p>
            <div class="modal-actions">
                <button type="button" class="btn secondary" id="btn-iva-cancelar">Cancelar</button>
                <button type="button" class="btn primary" id="btn-iva-confirmar">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.querySelector('form');
        var csrf = document.querySelector('input[name="_token"]').value;
        var empresaId = {{ $empresa->id ?? 'null' }};
        var existenProductosConIVA = {{ $existenProductosConIVA ? 'true' : 'false' }};

        // Switch visual
        var switchInput = document.getElementById('switch-cobra-iva');
        var switchUI = document.getElementById('switch-cobra-iva-ui');
        var switchKnob = document.getElementById('switch-cobra-iva-knob');
        var switchLabel = document.getElementById('switch-cobra-iva-label');

        // Modal
        var modal = document.getElementById('modal-iva');
        var btnConfirmar = document.getElementById('btn-iva-confirmar');
        var btnCancelar = document.getElementById('btn-iva-cancelar');
        var cierreModalPendiente = null; // función a ejecutar si confirma
        var modalAbierto = false;

        // Estado único de cobra_iva
        var estadoCobraIVA = !!switchInput.checked;

        function updateSwitchUI(checked) {
            switchInput.checked = checked; // mantener input sincronizado pero sin usar como fuente de verdad
            switchUI.setAttribute('aria-checked', checked ? 'true' : 'false');
            switchUI.style.background = checked ? '#1f5fbf' : '#e6e9ef';
            switchKnob.style.left = checked ? '22px' : '2px';
            switchLabel.textContent = checked ? 'Sí' : 'No';
        }
        updateSwitchUI(estadoCobraIVA);

        function mostrarMensaje(msg, ok) {
            var div = document.createElement('div');
            div.className = 'msg ' + (ok ? 'success' : 'error');
            div.textContent = msg;
            document.querySelector('.card').insertBefore(div, document.querySelector('.card').firstChild);
            setTimeout(() => div.remove(), 2500);
        }

        function abrirModal(onConfirm) {
            cierreModalPendiente = onConfirm;
            modalAbierto = true;
            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            btnConfirmar.focus();
        }

        function cerrarModal() {
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            cierreModalPendiente = null;
            modalAbierto = false;
        }

        // Guardar todos los campos vía AJAX
        function guardarEmpresaAJAX(campo, valor) {
            // Recolectar todos los valores actuales
            var data = {
                _token: csrf,
                id: empresaId
            };
            // Campos del formulario
            ['nombre','nit','moneda','direccion','telefono','email'].forEach(function(name){
                var el = form.querySelector('[name="'+name+'"]');
                if (el) data[name] = el.value;
            });
            // Switch cobra_iva
            data['cobra_iva'] = (campo === 'cobra_iva') ? valor : (estadoCobraIVA ? 1 : 0);

            fetch("{{ route('empresa.update') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(resp => resp.json())
            .then(data => {
                if (data.success || data.message) {
                    mostrarMensaje('Cambios guardados', true);
                } else {
                    mostrarMensaje('No se pudo guardar el cambio', false);
                }
            })
            .catch(() => mostrarMensaje('Error de red al guardar', false));
        }

        // Inputs texto/caja
        form.querySelectorAll('input[type="text"], input[type="email"], textarea').forEach(function(input){
            input.addEventListener('change', function(){
                guardarEmpresaAJAX(input.name, input.value);
            });
        });

        // Switch cobra_iva
        function toggleSwitch() {
            if (modalAbierto) return; // bloquear interacciones mientras el modal está activo

            var nuevoValor = !estadoCobraIVA;

            // Si se intenta apagar y hay productos con IVA, mostrar advertencia
            if (estadoCobraIVA && !nuevoValor && existenProductosConIVA) {
                abrirModal(function confirmarApagado(){
                    estadoCobraIVA = false;
                    updateSwitchUI(false);
                    guardarEmpresaAJAX('cobra_iva', 0);
                    cerrarModal();
                });
                return;
            }

            // Cambio directo sin fricción
            estadoCobraIVA = nuevoValor;
            updateSwitchUI(estadoCobraIVA);
            guardarEmpresaAJAX('cobra_iva', estadoCobraIVA ? 1 : 0);
        }
        switchUI.addEventListener('click', toggleSwitch);
        switchUI.addEventListener('keydown', function(e){
            if (e.key === ' ' || e.key === 'Enter') {
                e.preventDefault();
                toggleSwitch();
            }
        });

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
            updateSwitchUI(true);
            cerrarModal();
        });

        modal.addEventListener('click', function(e){
            if (e.target === modal) {
                // Clic fuera del contenido: cancelar y revertir
                estadoCobraIVA = true;
                updateSwitchUI(true);
                cerrarModal();
            }
        });
    });
    </script>

    @endsection
