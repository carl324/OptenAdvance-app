<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'POS')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Meta CSRF centralizado -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS global y navbar simple (offline, sin librerías) -->
    <style>
        :root{--bg:#f6f7fb;--nav:#ffffff;--accent:#1f5fbf;--muted:#6b7280}
        body{font-family:Inter,Segoe UI,Arial,Helvetica,sans-serif;margin:0;background:var(--bg);color:#111}
        .app-nav{background:var(--nav);border-bottom:1px solid #e6e9ef;padding:10px 16px;box-shadow:0 1px 0 rgba(16,24,40,.03)}
        .app-nav .wrap{max-width:1100px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:12px}
        .brand{font-weight:700;color:var(--accent);text-decoration:none}
        .nav-links{display:flex;gap:10px;align-items:center}
        .nav-links a{display:inline-block;padding:8px 10px;border-radius:6px;color:#111;text-decoration:none;font-weight:600}
        .nav-links a.active{background:var(--accent);color:#fff}
        .nav-links a:hover{background:#f0f6ff}
        .container-main{max-width:1100px;margin:22px auto;padding:0 16px}
        @media (max-width:640px){.nav-links{gap:6px;font-size:14px}}
    </style>

</head>
<body>

    <div class="app-nav" role="navigation" aria-label="Navegación principal">
        <div class="wrap">
            <a href="{{ route('ventas.index') }}" class="brand">{{ config('app.name', 'POS') }}</a>
            <div class="nav-links">
                <a href="{{ route('ventas.create') }}">Nueva Venta</a>
                <a href="{{ route('ventas.index') }}">Listar Ventas</a>
                <a href="{{ route('productos.index') }}">Productos</a>
                <a href="{{ route('empresa.index') }}">Empresa</a>
            </div>
        </div>
    </div>

    <main class="container-main">
        @yield('content')
    </main>

<!-- JS global -->


</body>
</html>
