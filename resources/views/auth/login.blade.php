<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema JyM</title>
    <link rel="stylesheet" href="{{ asset('css/style-login.css') }}">
</head>
<body>
    <div class="login-container">
        <img src="{{ asset('img/logo.png') }}" alt="Logo JyM" class="logo">
        <h2>Login</h2>
        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Campo para 'usuario' --}}
            <input type="text" id="usuario" name="usuario" placeholder="Usuário" value="{{ old('usuario') }}" required autofocus>
            @error('usuario')
                <span class="error-message">{{ $message }}</span>
            @enderror

            <input type="password" id="senha" name="senha" placeholder="Senha" required>
            @error('senha')
                <span class="error-message">{{ $message }}</span>
            @enderror

            <button type="submit">Entrar</button>
        </form>
        
        @if(session('success'))
            <p class="success-message">{{ session('success') }}</p>
        @endif
        @if(session('error'))
            <p class="error-message">{{ session('error') }}</p>
        @endif
    </div>
</body>
</html>