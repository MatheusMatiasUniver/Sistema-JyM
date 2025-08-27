<aside class="sidebar">
    <img src="{{ asset('img/logo.png') }}" alt="Logo JyM" class="logo">
    <nav>
        <a href="{{ route('dashboard') }}">Visão Geral</a>
        <a href="{{ route('clientes.index') }}">Clientes</a>

        @auth {{-- Verifica se o usuário está logado --}}
            {{-- Mostra o link "Cadastrar Usuário" APENAS se o usuário logado for Administrador --}}
            @if(Auth::user()->nivelAcesso === 'Administrador')
                <a href="{{ route('register') }}">Cadastrar Usuário</a>
                <a href="{{ route('users.index') }}">Gerenciar Usuários</a> 
            @endif
        @endauth
        <a href="{{ route('logout') }}">Logout</a>
    </nav>
</aside>