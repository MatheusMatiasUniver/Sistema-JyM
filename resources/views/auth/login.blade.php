<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema JyM</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-grip-7 text-white flex justify-center items-center min-h-screen">
    <div class="bg-grip-5 p-8 rounded-lg shadow-2xl w-[350px] text-center">
        <img src="{{ asset('img/logo.png') }}" alt="Logo JyM" class="w-[120px] mb-5 mx-auto">

        <h2 class="mb-5 text-grip-6 text-2xl font-bold">Login</h2>
        
        <form id="loginForm" method="POST" action="{{ route('login.submit') }}" autocomplete="off">
            @csrf

            <input type="text" id="usuario" name="usuario" placeholder="Usuário" value="{{ old('usuario') }}" required autofocus class="w-[95%] p-3 my-2.5 border-none rounded box-border text-black">
            @error('usuario')
                <span class="text-error-text text-sm">{{ $message }}</span>
            @enderror

            <input type="password" id="senha" name="senha" placeholder="Senha" required class="w-[95%] p-3 my-2.5 border-none rounded box-border text-black">
            @error('senha')
                <span class="text-error-text text-sm">{{ $message }}</span>
            @enderror

            <button type="submit" class="w-[95%] p-3 my-2.5 bg-grip-1 hover:bg-grip-2 text-white cursor-pointer transition-all duration-300 rounded font-bold">Entrar</button>
        </form>
        
        @if(session('success'))
            <p class="text-success-text mt-2 text-sm">{{ session('success') }}</p>
        @endif
        @if(session('error'))
            <p class="text-error-text mt-2 text-sm">{{ session('error') }}</p>
        @endif
    </div>
</body>
</html>
