<aside class="sidebar">
    <img src="{{ asset('img/logo.png') }}" alt="Logo JyM" class="logo">
    <nav>
        <a href="{{ route('dashboard') }}">Visão Geral</a>
        <a href="{{ route('clientes.index') }}">Clientes</a>       
        <a href="{{ route('produtos.index') }}">Produtos</a>
        <a href="{{ route('vendas.index') }}">Vendas</a>

        @auth
            @if(Auth::user()->isAdministrador())
                <a href="{{ route('academias.index') }}">Academias</a>
                <a href="{{ route('planos.index') }}">Planos de Assinatura</a>           
                <a href="{{ route('users.index') }}">Gerenciar Usuários</a>
                <a href="{{ route('register') }}">Cadastrar Usuário</a>
            @endif
        @endauth
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </nav>
</aside>