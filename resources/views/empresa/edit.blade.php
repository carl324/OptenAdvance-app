<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Editar Empresa</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
  <h2>Editar datos de la empresa</h2>

  @if(session('success'))
    <div style="color:green">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div style="color:red">{{ session('error') }}</div>
  @endif

  @if($errors->any())
    <div style="color:red">
      <ul>
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('empresa.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div>
      <label>Nombre *</label><br>
      <input type="text" name="nombre" value="{{ old('nombre', $empresa->nombre ?? '') }}" required style="width:100%">
    </div>

    <div>
      <label>NIT *</label><br>
      <input type="text" name="nit" value="{{ old('nit', $empresa->nit ?? '') }}" required style="width:100%">
    </div>

    <div>
      <label>Dirección</label><br>
      <input type="text" name="direccion" value="{{ old('direccion', $empresa->direccion ?? '') }}" style="width:100%">
    </div>

    <div>
      <label>Teléfono</label><br>
      <input type="text" name="telefono" value="{{ old('telefono', $empresa->telefono ?? '') }}" style="width:100%">
    </div>

    <div>
      <label>Email</label><br>
      <input type="email" name="email" value="{{ old('email', $empresa->email ?? '') }}" style="width:100%">
    </div>

    <div>
      <label>Moneda *</label><br>
      <input type="text" name="moneda" value="{{ old('moneda', $empresa->moneda ?? '') }}" required style="width:100%">
    </div>

    <div style="margin-top:12px">
      <button type="submit">Guardar cambios</button>
    </div>
  </form>

</body>
</html>
