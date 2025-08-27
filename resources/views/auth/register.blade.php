<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário - Sistema JyM</title>
    <link rel="stylesheet" href="{{ asset('css/style-login.css') }}">
</head>
<body>
    <div class="login-container">
        <img src="{{ asset('img/logo.png') }}" alt="Logo JyM" class="logo">
        <h2>Cadastro de Usuário</h2>
        <form id="cadastroForm" method="POST" action="{{ route('register') }}">
            @csrf

            <input type="text" id="nome" name="nome" placeholder="Nome" value="{{ old('nome') }}" required>
            @error('nome') <span class="error-message">{{ $message }}</span> @enderror

            <input type="text" id="usuario" name="usuario" placeholder="Usuário" value="{{ old('usuario') }}" required>
            @error('usuario') <span class="error-message">{{ $message }}</span> @enderror

            {{-- Email agora é opcional --}}
            <input type="email" id="email" name="email" placeholder="Email (Opcional)" value="{{ old('email') }}">
            @error('email') <span class="error-message">{{ $message }}</span> @enderror

            <input type="password" id="senha" name="senha" placeholder="Senha" required>
            @error('senha') <span class="error-message">{{ $message }}</span> @enderror

            <select id="nivelAcesso" name="nivelAcesso" required>
                <option value="">Selecione o nível de acesso</option>
                <option value="Funcionario" {{ old('nivelAcesso') == 'Funcionario' ? 'selected' : '' }}>Funcionário</option>
                <option value="Administrador" {{ old('nivelAcesso') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
            </select>
            @error('nivelAcesso') <span class="error-message">{{ $message }}</span> @enderror

            <button type="submit">Cadastrar</button>
        </form>
        <p><a href="{{ route('login') }}">Voltar ao Login</a></p>
    </div>
</body>
</html>