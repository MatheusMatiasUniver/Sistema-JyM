@extends('layouts.app')

@section('title', 'Dashboard - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Dashboard</h1>
    <div class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            <a href="{{ route('clientes.create') }}" class="inline-flex items-center justify-center px-4 py-3 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition">
                <i class="fas fa-user-plus mr-2"></i>
                Novo Cliente
            </a>
            <a href="{{ route('vendas.create') }}" class="inline-flex items-center justify-center px-4 py-3 rounded-md bg-green-600 text-white hover:bg-green-700 transition">
                <i class="fas fa-cart-plus mr-2"></i>
                Nova Venda
            </a>
            <a href="{{ route('reconhecimento') }}" class="inline-flex items-center justify-center px-4 py-3 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 transition">
                <i class="fas fa-camera mr-2"></i>
                Registrar Entrada
            </a>
            <a href="{{ route('produtos.create') }}" class="inline-flex items-center justify-center px-4 py-3 rounded-md bg-yellow-600 text-white hover:bg-yellow-700 transition">
                <i class="fas fa-box-open mr-2"></i>
                Cadastrar Produto
            </a>
            <a href="{{ route('compras.create') }}" class="inline-flex items-center justify-center px-4 py-3 rounded-md bg-orange-600 text-white hover:bg-orange-700 transition">
                <i class="fas fa-truck-loading mr-2"></i>
                Registrar Compra
            </a>
            <a href="{{ route('materiais.requisicoes.create') }}" class="inline-flex items-center justify-center px-4 py-3 rounded-md bg-teal-600 text-white hover:bg-teal-700 transition">
                <i class="fas fa-tools mr-2"></i>
                Requisição de Material
            </a>
        </div>
    </div>

    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <input type="date" id="startDate" class="border rounded px-2 py-1 text-black" />
            <span class="text-gray-600">até</span>
            <input type="date" id="endDate" class="border rounded px-2 py-1 text-black" />
            <button id="btnAtualizarPeriodo" class="ml-2 px-3 py-1 rounded bg-gray-800 text-white hover:bg-gray-700">Atualizar</button>
        </div>
        <div class="text-sm text-gray-500">Dica: Ctrl+K abre a paleta de comandos</div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('reconhecimento') }}" class="bg-white shadow-md rounded-lg p-6 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Acessos Hoje</p>
                <p id="acessosCount" class="text-3xl font-bold text-gray-800">{{ $acessosHoje }}</p>
            </div>
            <div class="text-blue-500 text-4xl">
                <i class="fas fa-door-open"></i>
            </div>
        </a>

        <a href="{{ route('clientes.index') }}" class="bg-white shadow-md rounded-lg p-6 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Clientes Ativos</p>
                <p class="text-3xl font-bold text-gray-800">{{ $clientesAtivos }}</p>
            </div>
            <div class="text-green-500 text-4xl">
                <i class="fas fa-users"></i>
            </div>
        </a>

        <a href="{{ route('vendas.index') }}" class="bg-white shadow-md rounded-lg p-6 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Faturamento Mês</p>
                <p id="faturamentoValue" class="text-3xl font-bold text-gray-800">R\$ {{ number_format($faturamentoMes, 2, ',', '.') }}</p>
            </div>
            <div class="text-purple-500 text-4xl">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-red-600 mb-4">Mensalidades Atrasadas ({{ $mensalidadesAtrasadas->count() }})</h2>
            @if($mensalidadesAtrasadas->isEmpty())
                <p class="text-gray-600">Nenhuma mensalidade atrasada. Bom trabalho!</p>
            @else
                <ul class="list-disc list-inside text-gray-700">
                    @foreach($mensalidadesAtrasadas as $mensalidade)
                        <li class="flex items-center justify-between py-1">
                            <div>
                                {{ $mensalidade->cliente && !$mensalidade->cliente->deleted_at ? $mensalidade->cliente->nome : 'Cliente Deletado' }} - Vencimento: {{ $mensalidade->dataVencimento->format('d/m/Y') }}
                                <span class="text-red-500 font-semibold">(R\$ {{ number_format($mensalidade->valor, 2, ',', '.') }})</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($mensalidade->cliente)
                                    <a href="{{ route('clientes.edit', $mensalidade->cliente) }}" class="px-2 py-1 text-sm rounded bg-blue-600 text-white hover:bg-blue-700">Ver cliente</a>
                                @endif
                                <form method="POST" action="{{ route('mensalidades.pagar', $mensalidade) }}">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 text-sm rounded bg-green-600 text-white hover:bg-green-700">Registrar pagamento</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold text-yellow-600 mb-4">Mensalidades Próximas ({{ $mensalidadesProximas->count() }})</h2>
            @if($mensalidadesProximas->isEmpty())
                <p class="text-gray-600">Nenhuma mensalidade próxima do vencimento nos próximos 7 dias.</p>
            @else
                <ul class="list-disc list-inside text-gray-700">
                    @foreach($mensalidadesProximas as $mensalidade)
                        <li class="flex items-center justify-between py-1">
                            <div>
                                {{ $mensalidade->cliente && !$mensalidade->cliente->deleted_at ? $mensalidade->cliente->nome : 'Cliente Deletado' }} - Vence em: {{ $mensalidade->dataVencimento->format('d/m/Y') }}
                                <span class="text-yellow-500 font-semibold">(R\$ {{ number_format($mensalidade->valor, 2, ',', '.') }})</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($mensalidade->cliente)
                                    <a href="{{ route('clientes.edit', $mensalidade->cliente) }}" class="px-2 py-1 text-sm rounded bg-blue-600 text-white hover:bg-blue-700">Ver cliente</a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Últimas Vendas</h2>
            @if($ultimasVendas->isEmpty())
                <p class="text-gray-600">Nenhuma venda recente.</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach($ultimasVendas as $venda)
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <p class="text-gray-800 font-semibold">Venda #{{ $venda->idVenda }} - {{ $venda->cliente && !$venda->cliente->deleted_at ? $venda->cliente->nome : 'Cliente Deletado' }}</p>
                                <p class="text-gray-600 text-sm">{{ $venda->dataVenda->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-green-600 font-bold">R\$ {{ number_format($venda->valorTotal, 2, ',', '.') }}</span>
                                <a href="{{ route('vendas.show', $venda) }}" class="px-2 py-1 text-sm rounded bg-gray-200 text-gray-800 hover:bg-gray-300">Ver detalhes</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold text-orange-600 mb-4">Produtos com Baixo Estoque ({{ $produtosBaixoEstoque->count() }})</h2>
            @if($produtosBaixoEstoque->isEmpty())
                <p class="text-gray-600">Todos os produtos estão com estoque adequado.</p>
            @else
                <ul class="list-disc list-inside text-gray-700">
                    @foreach($produtosBaixoEstoque as $produto)
                        <li class="flex items-center justify-between py-1">
                            <div>
                                {{ $produto->nome }} - Estoque: <span class="text-orange-500 font-semibold">{{ $produto->estoque }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('produtos.edit', $produto) }}" class="px-2 py-1 text-sm rounded bg-orange-600 text-white hover:bg-orange-700">Editar</a>
                                <a href="{{ route('compras.create') }}" class="px-2 py-1 text-sm rounded bg-indigo-600 text-white hover:bg-indigo-700">Criar compra</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- Additional Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <a href="{{ route('reconhecimento') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition block">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-500 text-white mr-4">
                    <i class="fas fa-door-open"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Entradas Hoje</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $entradasHoje }}</p>
                </div>
            </div>
        </a>

        <a href="{{ route('clientes.index') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition block">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-500 text-white mr-4">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Total de Clientes</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalClientes }}</p>
                </div>
            </div>
        </a>

        <a href="{{ route('vendas.index') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition block">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-500 text-white mr-4">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Vendas Hoje</p>
                    <p id="vendasHojeValue" class="text-2xl font-bold text-gray-800">R$ {{ number_format($vendasHoje, 2, ',', '.') }}</p>
                </div>
            </div>
        </a>
    </div>
@endsection

@push('body_scripts')
<div x-data="{ open:false, query:'', items:[
    {label:'Dashboard', href:'{{ route('dashboard') }}'},
    {label:'Clientes', href:'{{ route('clientes.index') }}'},
    {label:'Novo Cliente', href:'{{ route('clientes.create') }}'},
    {label:'Vendas', href:'{{ route('vendas.index') }}'},
    {label:'Nova Venda', href:'{{ route('vendas.create') }}'},
    {label:'Produtos', href:'{{ route('produtos.index') }}'},
    {label:'Cadastrar Produto', href:'{{ route('produtos.create') }}'},
    {label:'Compras', href:'{{ route('compras.index') }}'},
    {label:'Registrar Compra', href:'{{ route('compras.create') }}'},
    {label:'Fornecedores', href:'{{ route('fornecedores.index') }}'},
    {label:'Contas a pagar', href:'{{ route('financeiro.contas_pagar.index') }}'},
    {label:'Requisição de Material', href:'{{ route('materiais.requisicoes.create') }}'},
    {label:'Reconhecimento', href:'{{ route('reconhecimento') }}'},
] }" 
     x-on:keydown.window="if(($event.ctrlKey||$event.metaKey)&&$event.key==='k'){ open=true; $nextTick(()=>{ $refs.cmdInput.focus() }) }"
     x-on:keydown.window.escape="open=false"
>
    <template x-if="open">
        <div class="fixed inset-0 z-50 flex items-start justify-center pt-24 bg-black/40" x-on:click.self="open=false">
            <div class="w-full max-w-xl bg-white rounded-lg shadow-lg">
                <div class="border-b p-3">
                    <input x-ref="cmdInput" x-model="query" type="text" class="w-full border rounded px-3 py-2 text-black" placeholder="Digite para buscar • Esc para fechar" />
                </div>
                <ul class="max-h-80 overflow-y-auto p-2">
                    <template x-for="item in items.filter(i=> i.label.toLowerCase().includes(query.toLowerCase()))" :key="item.href">
                        <li>
                            <a :href="item.href" class="block px-3 py-2 hover:bg-gray-100" x-on:click="open=false" x-text="item.label"></a>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </template>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function(){
        const btn = document.getElementById('btnAtualizarPeriodo');
        const startInput = document.getElementById('startDate');
        const endInput = document.getElementById('endDate');
        const acessosEl = document.getElementById('acessosCount');
        const faturamentoEl = document.getElementById('faturamentoValue');
        const vendasHojeEl = document.getElementById('vendasHojeValue');

        const formatCurrency = (v) => (new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' })).format(Number(v || 0));

        btn?.addEventListener('click', async () => {
            const start = startInput?.value;
            const end = endInput?.value;
            if (!start || !end) {
                showNotification('Selecione início e fim do período.', 'error');
                return;
            }
            try {
                const { data } = await axios.get(`{{ route('dashboard.metrics') }}`, { params: { start, end } });
                acessosEl && (acessosEl.textContent = data.acessos);
                faturamentoEl && (faturamentoEl.textContent = formatCurrency(data.vendasTotal));
                vendasHojeEl && (vendasHojeEl.textContent = formatCurrency(data.vendasTotal));
                showNotification('Métricas atualizadas com sucesso.', 'success');
            } catch (e) {
                showNotification('Não foi possível atualizar métricas para o período selecionado.', 'error');
            }
        });
        // Hint para o usuário
        if (!localStorage.getItem('cmdPaletteHintShown')) {
            showNotification('Dica: use Ctrl+K para navegação rápida.', 'info');
            localStorage.setItem('cmdPaletteHintShown', '1');
        }
    });
</script>
@endpush
