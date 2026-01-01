<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Bienvenido</title>
    <style>
        /* Estilos mínimos y auto-contenidos */
        :root{--bg:#f6f7fb;--card:#fff;--accent:#1f5fbf;--muted:#6b7280}
        html,body{height:100%;margin:0;font-family:Inter,Segoe UI,Arial,Helvetica,sans-serif;background:var(--bg);color:#111}
        .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        .card{width:100%;max-width:780px;background:var(--card);box-shadow:0 6px 18px rgba(16,24,40,.06);border-radius:10px;padding:28px}
        h1{margin:0 0 6px;font-size:20px}
        p.lead{margin:0 0 18px;color:var(--muted)}
        .actions{display:flex;gap:10px;flex-wrap:wrap}
        button.primary{background:var(--accent);color:#fff;border:none;padding:10px 16px;border-radius:8px;font-weight:600;cursor:pointer}
        button.secondary{background:transparent;border:1px solid #e6e9ef;padding:10px 14px;border-radius:8px;color:#374151;cursor:pointer}
        form{margin-top:14px;display:grid;grid-template-columns:1fr 1fr;gap:12px}
        label{font-size:13px;font-weight:600;margin-bottom:6px;display:block}
        input[type="text"],input[type="email"],select,textarea{width:100%;padding:10px;border:1px solid #e6e9ef;border-radius:8px}
        .full{grid-column:1/-1}
        .note{font-size:13px;color:var(--muted)}
        @media (max-width:640px){form{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Bienvenido, antes de comenzar a vender</h1>
            <p class="lead">Configura los datos de tu empresa para emitir facturas correctamente. Puedes hacerlo ahora o saltar y empezar a usar el sistema.</p>

            <div class="actions" style="margin-bottom:12px">
                <button id="btn-show" class="primary">Configurar ahora</button>
                <a href="{{ route('ventas.index') }}"><button class="secondary" type="button">Saltar por ahora</button></a>
            </div>

            <!-- Formulario inline (oculto por defecto). Envía al controlador existente EmpresaController::update -->
            <div id="form-wrap" style="display:none">
                <form id="onboard-form" method="POST" action="{{ route('empresa.update') }}">
                    @csrf
                    <div class="full">
                        <label for="nombre">Nombre</label>
                        <input id="nombre" name="nombre" type="text" required>
                    </div>

                    <div>
                        <label for="nit">NIT</label>
                        <input id="nit" name="nit" type="text">
                    </div>

                    <div>
                        <label for="moneda">Moneda</label>
                        <input id="moneda" name="moneda" type="text" value="COP">
                    </div>

                    <div class="full">
                        <label for="direccion">Dirección</label>
                        <input id="direccion" name="direccion" type="text">
                    </div>

                    <div>
                        <label for="telefono">Teléfono</label>
                        <input id="telefono" name="telefono" type="text">
                    </div>

                    <div>
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email">
                    </div>

                    <div class="full" style="display:flex;align-items:center;gap:12px">
                        <label style="font-weight:600">¿La empresa cobra IVA?</label>
                        <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="cobra_iva" value="1"> Sí</label>
                    </div>

                    <div style="display:flex;justify-content:flex-end;grid-column:1/-1;margin-top:8px">
                        <button type="submit" class="primary">Guardar y comenzar</button>
                    </div>
                </form>
            </div>

            <p class="note" style="margin-top:12px">Puedes cambiar estos datos más adelante desde el panel de configuración de la empresa.</p>
        </div>
    </div>

    <script>
        // JS vanilla: muestra/oculta el formulario en la misma vista
        (function(){
            var btn = document.getElementById('btn-show');
            var wrap = document.getElementById('form-wrap');
            if (!btn || !wrap) return;

            btn.addEventListener('click', function(e){
                e.preventDefault();
                if (wrap.style.display === 'none' || wrap.style.display === '') {
                    wrap.style.display = 'block';
                    btn.textContent = 'Ocultar formulario';
                } else {
                    wrap.style.display = 'none';
                    btn.textContent = 'Configurar ahora';
                }
            });

            // Validación mínima antes de submit
            var form = document.getElementById('onboard-form');
            if (form) {
                form.addEventListener('submit', function(e){
                    var nombre = document.getElementById('nombre').value.trim();
                    if (!nombre) {
                        e.preventDefault();
                        alert('El nombre de la empresa es obligatorio.');
                    }
                });
            }
        })();
    </script>
</body>
</html>
