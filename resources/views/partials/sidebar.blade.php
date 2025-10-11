<aside class="sidebar-layout">
    <img src="{{ asset('img/logo.png') }}" alt="Logo JyM" class="sidebar-logo">
    <nav>
        <a href="{{ route('dashboard') }}" class="sidebar-nav-link">Visão Geral</a>
        <a href="{{ route('clientes.index') }}" class="sidebar-nav-link">Clientes</a>
        <a href="{{ route('produtos.index') }}" class="sidebar-nav-link">Produtos</a>
        <a href="{{ route('vendas.index') }}" class="sidebar-nav-link">Vendas</a>
        <a href="{{ route('reconhecimento') }}" class="sidebar-nav-link" target="_blank" rel="noopener noreferrer">Acesso Clientes</a>

        @auth
            @if(Auth::user()->isAdministrador())
                <a href="{{ route('academias.index') }}" class="sidebar-nav-link">Academias</a>
                <a href="{{ route('planos.index') }}" class="sidebar-nav-link">Planos de Assinatura</a>
                <a href="{{ route('users.index') }}" class="sidebar-nav-link">Gerenciar Usuários</a>
                <a href="{{ route('register') }}" class="sidebar-nav-link">Cadastrar Usuário</a>
            @endif
        @endauth
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sidebar-nav-link">Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </nav>
</aside>