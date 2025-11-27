<aside class="sidebar-layout" x-data="{ activeCategory: null, hoveringFlyout: false, flyoutTop: 0, setFlyout(category, el) { this.activeCategory = category; if(!category) return; this.$nextTick(() => { const rect = el.getBoundingClientRect(); const flyout = this.$refs.flyout; if (!flyout) return; const flyoutHeight = flyout.scrollHeight || flyout.offsetHeight || 0; const viewportHeight = window.innerHeight; const margin = 10; const spaceBelow = viewportHeight - rect.bottom - margin; let top; if (flyoutHeight > spaceBelow) { top = Math.max(margin, rect.bottom - flyoutHeight); } else if (rect.top + flyoutHeight <= viewportHeight - margin) { top = rect.top; } else { top = Math.max(margin, viewportHeight - margin - flyoutHeight); } this.flyoutTop = top; }); } }" @mouseleave="if(!hoveringFlyout) activeCategory = null">
    <img src="{{ asset('img/logo.png') }}" alt="Logo JyM" class="sidebar-logo">
        
    <nav class="sidebar-nav">
        @include('partials.academia-selector')
        <a href="{{ route('dashboard') }}" class="sidebar-nav-link"><i class="fas fa-chart-line mr-2"></i>Visão Geral</a>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Acesso Rápido</div>
            <a href="{{ route('vendas.create') }}" class="sidebar-nav-link"><i class="fas fa-cash-register mr-2"></i>Registrar Nova Venda</a>
            <a href="{{ route('reconhecimento') }}" class="sidebar-nav-link" target="_blank" rel="noopener noreferrer"><i class="fas fa-user-check mr-2"></i>Acesso Clientes</a>
        </div>

        <div class="sidebar-section">
            <button type="button" class="sidebar-section-toggle" @click="setFlyout(activeCategory === 'clientes' ? null : 'clientes', $el)">
                <span>Clientes</span>
                <div class="dropdown-arrow"><i class="fas fa-chevron-right" :class="activeCategory === 'clientes' ? 'rotated' : ''"></i></div>
            </button>
        </div>

        <div class="sidebar-section">
            <button type="button" class="sidebar-section-toggle" @click="setFlyout(activeCategory === 'lancamentos' ? null : 'lancamentos', $el)">
                <span>Lançamentos</span>
                <div class="dropdown-arrow"><i class="fas fa-chevron-right" :class="activeCategory === 'lancamentos' ? 'rotated' : ''"></i></div>
            </button>
        </div>

        <div class="sidebar-section">
            <button type="button" class="sidebar-section-toggle" @click="setFlyout(activeCategory === 'financeiro' ? null : 'financeiro', $el)">
                <span>Financeiro</span>
                <div class="dropdown-arrow"><i class="fas fa-chevron-right" :class="activeCategory === 'financeiro' ? 'rotated' : ''"></i></div>
            </button>
        </div>
        
        <div class="sidebar-section">
            <button type="button" class="sidebar-section-toggle" @click="setFlyout(activeCategory === 'produtos' ? null : 'produtos', $el)">
                <span>Produtos</span>
                <div class="dropdown-arrow"><i class="fas fa-chevron-right" :class="activeCategory === 'produtos' ? 'rotated' : ''"></i></div>
            </button>
        </div>
        
        <div class="sidebar-section">
            <button type="button" class="sidebar-section-toggle" @click="setFlyout(activeCategory === 'relatorios' ? null : 'relatorios', $el)">
                <span>Relatórios</span>
                <div class="dropdown-arrow"><i class="fas fa-chevron-right" :class="activeCategory === 'relatorios' ? 'rotated' : ''"></i></div>
            </button>
        </div>
                
        @auth
            @if(Auth::user()->isAdministrador())
                <div class="sidebar-section">
                    <button type="button" class="sidebar-section-toggle" @click="setFlyout(activeCategory === 'administracao' ? null : 'administracao', $el)">
                        <span>Administração</span>
                        <div class="dropdown-arrow"><i class="fas fa-chevron-right" :class="activeCategory === 'administracao' ? 'rotated' : ''"></i></div>
                    </button>
                </div>
            @endif
        @endauth
    </nav>

    @auth
        <div class="user-info-section" id="userDropdown">
            <div class="user-info-content">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-details">
                    <div class="user-name">{{ Auth::user()->nome }}</div>
                    <div class="user-role">
                        @if(Auth::user()->isAdministrador())
                            <span class="role-badge admin">Admin</span>
                        @elseif(Auth::user()->isFuncionario())
                            <span class="role-badge funcionario">Funcionário</span>
                        @endif
                    </div>
                </div>
                <div class="dropdown-arrow">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
            
            <div class="user-dropdown-menu" id="userDropdownMenu">
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    @endauth

    <div class="sidebar-flyout" x-ref="flyout" x-show="activeCategory" :style="{ top: flyoutTop + 'px' }">
        <div x-show="activeCategory === 'clientes'">
            <a href="{{ route('clientes.index') }}" class="sidebar-nav-link"><i class="fas fa-users mr-2"></i>Clientes</a>
            @auth
                @if(Auth::user()->isAdministrador())
                    <a href="{{ route('clientes.excluidos.index') }}" class="sidebar-nav-link"><i class="fas fa-user-slash mr-2"></i>Clientes Excluídos</a>
                @endif
            @endauth
        </div>

        <div x-show="activeCategory === 'lancamentos'">
            <a href="{{ route('compras.index') }}" class="sidebar-nav-link"><i class="fas fa-shopping-cart mr-2"></i>Compras</a>
            <a href="{{ route('vendas.index') }}" class="sidebar-nav-link"><i class="fas fa-receipt mr-2"></i>Vendas</a>
        </div>

        <div x-show="activeCategory === 'financeiro'">
            <a href="{{ route('financeiro.contas_pagar.index') }}" class="sidebar-nav-link"><i class="fas fa-money-check-alt mr-2"></i>Contas a Pagar</a>
            <a href="{{ route('financeiro.contas_receber.index') }}" class="sidebar-nav-link"><i class="fas fa-hand-holding-usd mr-2"></i>Contas a Receber</a>
        </div>

        <div x-show="activeCategory === 'produtos'">
            <a href="{{ route('produtos.index') }}" class="sidebar-nav-link"><i class="fas fa-boxes mr-2"></i>Produtos</a>
            <a href="{{ route('categorias.index') }}" class="sidebar-nav-link"><i class="fas fa-tags mr-2"></i>Categorias</a>
            <a href="{{ route('fornecedores.index') }}" class="sidebar-nav-link"><i class="fas fa-truck mr-2"></i>Fornecedores</a>            
            <a href="{{ route('equipamentos.index') }}" class="sidebar-nav-link"><i class="fas fa-tools mr-2"></i>Equipamentos</a>
            <a href="{{ route('materiais.index') }}" class="sidebar-nav-link"><i class="fas fa-cubes mr-2"></i>Materiais</a>        
    </div>

    <div x-show="activeCategory === 'relatorios'">
            <a href="{{ route('relatorios.compras') }}" class="sidebar-nav-link"><i class="fas fa-file-alt mr-2"></i>Relatórios de Compras</a>
            <a href="{{ route('relatorios.margem') }}" class="sidebar-nav-link"><i class="fas fa-percentage mr-2"></i>Margem por Produto</a>
            <a href="{{ route('relatorios.ruptura') }}" class="sidebar-nav-link"><i class="fas fa-exclamation-triangle mr-2"></i>Ruptura de Estoque</a>
            <a href="{{ route('relatorios.faturamento') }}" class="sidebar-nav-link"><i class="fas fa-file-invoice-dollar mr-2"></i>Faturamento e Lucro</a>
            <a href="{{ route('relatorios.gastos') }}" class="sidebar-nav-link"><i class="fas fa-money-bill-wave mr-2"></i>Gastos</a>
            <a href="{{ route('relatorios.inadimplencia') }}" class="sidebar-nav-link"><i class="fas fa-user-times mr-2"></i>Inadimplência</a>
            <a href="{{ route('relatorios.frequencia') }}" class="sidebar-nav-link"><i class="fas fa-user-clock mr-2"></i>Frequência</a>
            <a href="{{ route('relatorios.vendas') }}" class="sidebar-nav-link"><i class="fas fa-shopping-bag mr-2"></i>Vendas</a>
            <a href="{{ route('relatorios.porFuncionario') }}" class="sidebar-nav-link"><i class="fas fa-id-badge mr-2"></i>Por Funcionário</a>
        </div>

    @auth
        @if(Auth::user()->isAdministrador())
            <div x-show="activeCategory === 'administracao'">
                <a href="{{ route('academias.index') }}" class="sidebar-nav-link"><i class="fas fa-dumbbell mr-2"></i>Academias</a>
                <a href="{{ route('planos.index') }}" class="sidebar-nav-link"><i class="fas fa-list-alt mr-2"></i>Planos de Assinatura</a>
                <a href="{{ route('users.index') }}" class="sidebar-nav-link"><i class="fas fa-user-cog mr-2"></i>Gerenciar Usuários</a>
                <a href="{{ route('register') }}" class="sidebar-nav-link"><i class="fas fa-user-plus mr-2"></i>Cadastrar Usuário</a>
                <a href="{{ route('ajustes.index') }}" class="sidebar-nav-link"><i class="fas fa-sliders-h mr-2"></i>Ajustes do Sistema</a>
            </div>
        @endif
        @endauth
    </div>

</aside>
