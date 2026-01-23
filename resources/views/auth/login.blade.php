<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .login-wrap { max-width: 360px; margin: 8vh auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .field { margin-bottom: 14px; }
        label { display: block; font-size: 14px; margin-bottom: 6px; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        button { width: 100%; padding: 10px; border: 0; border-radius: 6px; background: #111827; color: #fff; font-weight: 600; }
        .error { color: #b91c1c; font-size: 12px; margin-top: 6px; }
    </style>
</head>
<body>
    <div class="login-wrap">
        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="field">
                <label for="username">Usuario</label>
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
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
