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
    </style>

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

                <div class="full actions">
                    <span class="note">Revisa los datos antes de guardar.</span>
                    <button type="submit" class="primary">Guardar cambios</button>
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

    <script>
        // JS mínimo para mejorar UX: confirmar envío si campos obligatorios vacíos
        (function(){
            var form = document.querySelector('form');
            form.addEventListener('submit', function(e){
                var nombre = document.getElementById('nombre').value.trim();
                var nit = document.getElementById('nit').value.trim();
                var moneda = document.getElementById('moneda').value.trim();
                if (!nombre || !nit || !moneda) {
                    e.preventDefault();
                    alert('Por favor completa los campos obligatorios: Nombre, NIT y Moneda.');
                }
            });
        })();

    </script>

    @endsection
