<aside class="sidebar-layout">
    <img src="{{ asset('img/logo.png') }}" alt="Logo JyM" class="sidebar-logo">
        
    <nav>
        @include('partials.academia-selector')
        <a href="{{ route('dashboard') }}" class="sidebar-nav-link">Visão Geral</a>
        <a href="{{ route('clientes.index') }}" class="sidebar-nav-link">Clientes</a>
        <a href="{{ route('produtos.index') }}" class="sidebar-nav-link">Produtos</a>
        <a href="{{ route('categorias.index') }}" class="sidebar-nav-link">Categorias</a>
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

    @auth
        <div class="user-info-section">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-details">
                <div class="user-name">{{ Auth::user()->nome }}</div>
                <div class="user-role">
                    @if(Auth::user()->isAdministrador())
                        <span class="role-badge admin">Administrador</span>
                    @elseif(Auth::user()->isFuncionario())
                        <span class="role-badge funcionario">Funcionário</span>
                    @endif
                </div>
                @if(Auth::user()->isFuncionario() && Auth::user()->usuario_academia)
                    <div class="user-academia">{{ Auth::user()->usuario_academia->nome }}</div>
                @endif
            </div>
        </div>
    @endauth

</aside>