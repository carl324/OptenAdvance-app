<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro inicial</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .wrap { max-width: 420px; margin: 8vh auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .field { margin-bottom: 14px; }
        label { display: block; font-size: 14px; margin-bottom: 6px; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        button { width: 100%; padding: 10px; border: 0; border-radius: 6px; background: #111827; color: #fff; font-weight: 600; }
        .error { color: #b91c1c; font-size: 12px; margin-top: 6px; }
    </style>
</head>
<body>
    <div class="wrap">
        <form method="POST" action="{{ route('setup.store') }}">
            @csrf
            <div class="field">
                <label for="username">Usuario administrador</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus>
                @error('username')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="password">Contraseña</label>
                <input id="password" name="password" type="password" required>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Confirmar contraseña</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>
            </div>
            <button type="submit">Crear administrador</button>
        </form>
    </div>
</body>
</html>
