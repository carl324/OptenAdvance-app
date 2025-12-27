<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'POS')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS global -->

</head>
<body>

<nav>
    <a href="/ventas">Ventas</a>
    <a href="/productos">Productos</a>
</nav>

<main class="container">
    @yield('content')
</main>

<!-- JS global -->


</body>
</html>
