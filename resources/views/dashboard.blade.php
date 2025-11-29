@extends('layouts.app')

@section('title', 'Dashboard - Sistema JyM')

@section('content')
    @php
        $formasPagamentoAtivas = $formasPagamentoAtivas ?? \App\Models\AjusteSistema::FORMAS_PAGAMENTO_PADRAO;
    @endphp

    <h1 class="text-3xl font-bold mb-6 text-grip-6">Dashboard</h1>
    

    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <input type="date" id="startDate" class="border rounded px-2 py-1 text-black" />
            <span class="text-gray-600">até</span>
            <input type="date" id="endDate" class="border rounded px-2 py-1 text-black" />
            <button id="btnAtualizarPeriodo" class="ml-2 px-3 py-1 rounded bg-gray-800 text-white hover:bg-gray-700">Atualizar</button>
        </div>
        
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('clientes.index') }}" class="bg-white shadow-md rounded-lg p-6 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Total de Alunos</p>
                <p id="totalClientesCount" class="text-3xl font-bold text-gray-800">{{ $totalClientes }}</p>
            </div>
            <div class="text-indigo-500 text-4xl">
                <i class="fas fa-user-graduate"></i>
            </div>
        </a>

        <a href="{{ route('clientes.index') }}" class="bg-white shadow-md rounded-lg p-6 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Alunos Ativos</p>
                <p id="clientesAtivosCount" class="text-3xl font-bold text-gray-800">{{ $clientesAtivos }}</p>
            </div>
            <div class="text-green-500 text-4xl">
                <i class="fas fa-users"></i>
            </div>
        </a>

        <a href="{{ route('reconhecimento') }}" class="bg-white shadow-md rounded-lg p-6 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Acessos Hoje</p>
                <p id="acessosCount" class="text-3xl font-bold text-gray-800">{{ $acessosHoje }}</p>
            </div>
            <div class="text-blue-500 text-4xl">
                <i class="fas fa-door-open"></i>
            </div>
        </a>

        <a href="{{ route('vendas.index') }}" class="bg-white shadow-md rounded-lg p-6 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Vendas Hoje</p>
                <p id="vendasHojeValue" class="text-3xl font-bold text-gray-800">R\$ {{ number_format($vendasHoje, 2, ',', '.') }}</p>
            </div>
            <div class="text-yellow-500 text-4xl">
                <i class="fas fa-bolt"></i>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-red-600 mb-4">Mensalidades Vencidas ({{ $mensalidadesAtrasadas->count() }})</h2>
            @if($mensalidadesAtrasadas->isEmpty())
                <p class="text-gray-600">Nenhuma mensalidade atrasada. Bom trabalho!</p>
            @else
                <ul class="list-disc list-inside text-gray-700">
                    @foreach($mensalidadesAtrasadas as $mensalidade)
                        <li class="flex items-center justify-between py-1">
                            <div class="flex-1 min-w-0">
                                {{ $mensalidade->cliente && !$mensalidade->cliente->deleted_at ? $mensalidade->cliente->nome : 'Cliente Deletado' }} - Vencimento: {{ $mensalidade->dataVencimento->format('d/m/Y') }}
                                <span class="text-red-500 font-semibold whitespace-nowrap">(R$ {{ number_format($mensalidade->valor, 2, ',', '.') }})</span>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0 whitespace-nowrap">
                                @if($mensalidade->cliente)
                                    <a href="{{ route('clientes.edit', $mensalidade->cliente) }}" class="px-2 py-1 text-sm rounded bg-blue-600 text-white hover:bg-blue-700 whitespace-nowrap">Detalhes do Cliente</a>
                                @endif
                                <button type="button" data-action="{{ route('mensalidades.pagar', $mensalidade) }}" class="px-2 py-1 text-sm rounded bg-green-600 text-white hover:bg-green-700 whitespace-nowrap btn-renovar-mensalidade">Renovar Mensalidade</button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Resumo de Contas a Pagar</h2>
            @if(isset($contasPagarLista) && $contasPagarLista->isNotEmpty())
                <ul class="divide-y divide-gray-200">
                    @foreach($contasPagarLista as $conta)
                        <li class="py-2 flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-800">{{ $conta->descricao ?? 'Conta' }} • {{ optional($conta->fornecedor)->nome ?? 'Fornecedor' }}</p>
                                <p class="text-xs text-gray-600">Vence em: {{ optional($conta->dataVencimento)->format('d/m/Y') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-red-600 font-bold whitespace-nowrap">R$ {{ number_format($conta->valorTotal, 2, ',', '.') }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-3 text-right">
                    <a href="{{ route('financeiro.contas_pagar.index') }}" class="text-indigo-600 hover:underline text-sm">Ver todas</a>
                </div>
            @else
                <p class="text-gray-600">Nenhuma conta a pagar em aberto.</p>
            @endif
        </div>
    </div>

    <div id="modalRenovarMensalidade" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <h3 class="text-lg font-bold mb-4 text-black">Selecione a forma de pagamento</h3>
            <form id="formRenovarMensalidade" method="POST" action="#" class="space-y-4">
                @csrf
                <div>
                    <label for="formaPagamentoModal" class="block text-sm text-gray-700 mb-1">Forma de pagamento</label>
                    <select id="formaPagamentoModal" name="formaPagamento" class="select" required>
                        <option value="">Selecione</option>
                        @foreach($formasPagamentoAtivas as $forma)
                            <option value="{{ $forma }}">{{ $forma }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="btnCancelarModalMensalidade" class="px-3 py-1 rounded bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</button>
                    <button type="submit" class="px-3 py-1 rounded bg-green-600 text-white hover:bg-green-700">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

    @php
        $produtosBaixoEstoqueLista = $produtosBaixoEstoque->take(3);
        $produtosBaixoEstoqueRestantes = max($produtosBaixoEstoque->count() - 3, 0);
    @endphp

    <div id="modalAlertaEstoqueMinimo" data-alert="{{ $produtosBaixoEstoque->isNotEmpty() ? '1' : '0' }}" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
            <h3 class="text-lg font-bold mb-4 text-black">Estoque mínimo atingido</h3>
            <p class="text-sm text-gray-600 mb-4">Os produtos abaixo estão com estoque igual ou abaixo do mínimo configurado:</p>
            <ul class="list-disc list-inside text-sm text-gray-800 mb-4">
                @forelse($produtosBaixoEstoqueLista as $produtoBaixo)
                    <li>{{ $produtoBaixo->nome }} ({{ $produtoBaixo->estoque }} unidades)</li>
                @empty
                    <li>Nenhum produto listado.</li>
                @endforelse
            </ul>
            @if($produtosBaixoEstoqueRestantes > 0)
                <p class="text-xs text-gray-500 mb-4">e mais {{ $produtosBaixoEstoqueRestantes }} produto(s)...</p>
            @endif
            <div class="flex justify-end gap-2">
                <button type="button" id="btnFecharModalEstoque" class="px-3 py-1 rounded bg-gray-200 text-gray-800 hover:bg-gray-300">Agora não</button>
                <a href="{{ route('compras.create') }}" class="px-3 py-1 rounded bg-indigo-600 text-white hover:bg-indigo-700">Fazer lançamento de compra</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Últimas 5 Vendas do Dia</h2>
            @if($ultimasVendas->isEmpty())
                <p class="text-gray-600">Nenhuma venda recente.</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach($ultimasVendas as $venda)
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <p class="text-gray-800 font-semibold">Venda #{{ $venda->idVenda }} - {{ $venda->cliente_nome_exibicao }}</p>
                                <p class="text-gray-600 text-sm">{{ $venda->dataVenda->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-green-600 font-bold whitespace-nowrap">R\$ {{ number_format($venda->valorTotal, 2, ',', '.') }}</span>
                                <a href="{{ route('vendas.show', $venda) }}" class="px-2 py-1 text-sm rounded bg-gray-200 text-gray-800 hover:bg-gray-300 whitespace-nowrap">Ver detalhes</a>
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
                            <div class="min-w-0">
                                {{ $produto->nome }} - Estoque: <span class="text-orange-500 font-semibold">{{ $produto->estoque }}</span> / Mín.: {{ $produto->estoqueMinimo }}
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0 whitespace-nowrap">
                                <a href="{{ route('produtos.edit', $produto) }}" class="px-2 py-1 text-sm rounded bg-orange-600 text-white hover:bg-orange-700 whitespace-nowrap">Editar</a>
                                <a href="{{ route('compras.create') }}" class="px-2 py-1 text-sm rounded bg-indigo-600 text-white hover:bg-indigo-700 whitespace-nowrap">Realizar Nova Compra</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 mt-8">
        <div class="bg-white p-6 rounded-lg shadow-md overflow-hidden">
            <h3 class="text-lg font-bold text-black mb-3">Gráfico de Faturamento/Lucro no Último Mês</h3>
            <canvas id="graficoLinhaFaturamento" class="block w-full h-64 max-w-full"></canvas>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md overflow-hidden">
            <h3 class="text-lg font-bold text-black mb-3">Acessos por Hora (Hoje)</h3>
            <canvas id="graficoBarrasAcessosHora" class="block w-full h-64 max-w-full"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg text-black font-bold mb-3">Resumo Contas a Pagar</h3>
            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="p-3 rounded bg-gray-50">
                    <p class="text-xs text-gray-500">Abertas</p>
                    <p id="contasPagarAbertasQtd" class="text-xl font-bold">{{ $contasPagarResumo['abertas']['quantidade'] }}</p>
                    <p id="contasPagarAbertasTotal" class="text-sm text-gray-700 whitespace-nowrap">R$ {{ number_format($contasPagarResumo['abertas']['total'], 2, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded bg-gray-50">
                    <p class="text-xs text-gray-500">Vencidas</p>
                    <p id="contasPagarVencidasQtd" class="text-xl font-bold">{{ $contasPagarResumo['vencidas']['quantidade'] }}</p>
                    <p id="contasPagarVencidasTotal" class="text-sm text-gray-700 whitespace-nowrap">R$ {{ number_format($contasPagarResumo['vencidas']['total'], 2, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded bg-gray-50">
                    <p class="text-xs text-gray-500">Pagas Hoje</p>
                    <p id="contasPagarPagasHojeQtd" class="text-xl font-bold">{{ $contasPagarResumo['pagasHoje']['quantidade'] }}</p>
                    <p id="contasPagarPagasHojeTotal" class="text-sm text-gray-700 whitespace-nowrap">R$ {{ number_format($contasPagarResumo['pagasHoje']['total'], 2, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg text-black font-bold mb-3">Resumo Contas a Receber</h3>
            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="p-3 rounded bg-gray-50">
                    <p class="text-xs text-gray-500">Abertas</p>
                    <p id="contasReceberAbertasQtd" class="text-xl font-bold">{{ $contasReceberResumo['abertas']['quantidade'] }}</p>
                    <p id="contasReceberAbertasTotal" class="text-sm text-gray-700 whitespace-nowrap">R$ {{ number_format($contasReceberResumo['abertas']['total'], 2, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded bg-gray-50">
                    <p class="text-xs text-gray-500">Vencidas</p>
                    <p id="contasReceberVencidasQtd" class="text-xl font-bold">{{ $contasReceberResumo['vencidas']['quantidade'] }}</p>
                    <p id="contasReceberVencidasTotal" class="text-sm text-gray-700 whitespace-nowrap">R$ {{ number_format($contasReceberResumo['vencidas']['total'], 2, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded bg-gray-50">
                    <p class="text-xs text-gray-500">Recebidas Hoje</p>
                    <p id="contasReceberRecebidasHojeQtd" class="text-xl font-bold">{{ $contasReceberResumo['recebidasHoje']['quantidade'] }}</p>
                    <p id="contasReceberRecebidasHojeTotal" class="text-sm text-gray-700 whitespace-nowrap">R$ {{ number_format($contasReceberResumo['recebidasHoje']['total'], 2, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg text-black font-bold mb-3">Compras Abertas (não recebidas)</h3>
            @if($comprasAbertas->isEmpty())
                <p class="text-gray-600">Nenhuma compra aberta.</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach($comprasAbertas as $compra)
                        <li class="py-2 flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-sm text-gray-800">Compra #{{ $compra->idCompra }} • Itens: {{ $compra->itens_count }}</p>
                                <p class="text-xs text-gray-600">{{ optional($compra->dataEmissao)->format('d/m/Y H:i') }}</p>
                            </div>
                            <a href="{{ route('compras.show', $compra) }}" class="px-2 py-1 text-sm rounded bg-indigo-600 text-white hover:bg-indigo-700 whitespace-nowrap">Ver</a>
                        </li>
                    @endforeach
                </ul>
            @endif
            <div class="mt-3 text-right">
                <a href="{{ route('compras.index', ['status' => 'aberta']) }}" class="text-indigo-600 hover:underline text-sm">Ver todas</a>
            </div>
        </div>
    </div>
@endsection


@push('body_scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const isDashboardPage = window.location.pathname === '/dashboard';
        if (!isDashboardPage) return;

    const estoqueModal = document.getElementById('modalAlertaEstoqueMinimo');
    const closeEstoqueBtn = document.getElementById('btnFecharModalEstoque');
    const openEstoqueModal = () => {
        if (!estoqueModal) return;
        estoqueModal.classList.remove('hidden');
        estoqueModal.classList.add('flex');
    };
    const closeEstoqueModal = () => {
        if (!estoqueModal) return;
        estoqueModal.classList.add('hidden');
        estoqueModal.classList.remove('flex');
    };

    if (estoqueModal && estoqueModal.dataset.alert === '1') {
        setTimeout(() => {
            if (estoqueModal.dataset.alert === '1') {
                openEstoqueModal();
            }
        }, 400);
    }

    closeEstoqueBtn && closeEstoqueBtn.addEventListener('click', closeEstoqueModal);
    estoqueModal && estoqueModal.addEventListener('click', (event) => {
        if (event.target === estoqueModal) {
            closeEstoqueModal();
        }
    });

    const btnAtualizarPeriodo = document.getElementById('btnAtualizarPeriodo');
    const startInput = document.getElementById('startDate');
    const endInput = document.getElementById('endDate');
    const acessosEl = document.getElementById('acessosCount');
    const vendasHojeEl = document.getElementById('vendasHojeValue');

    btnAtualizarPeriodo && btnAtualizarPeriodo.addEventListener('click', async () => {
        const start = startInput && startInput.value;
        const end = endInput && endInput.value;
        if (!start || !end) {
            if (window.showNotification) window.showNotification('Selecione início e fim do período.', 'error');
            return;
        }
        try {
            const res = await fetch(`{{ route('dashboard.metrics') }}` + `?start=${encodeURIComponent(start)}&end=${encodeURIComponent(end)}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            if (typeof data === 'object') {
                acessosEl && (acessosEl.textContent = data.acessos ?? acessosEl.textContent);
                vendasHojeEl && (vendasHojeEl.textContent = formatCurrencyBRL(data.vendasTotal ?? data.vendasHoje));
                if (window.showNotification) window.showNotification('Métricas atualizadas com sucesso.', 'success');
            } else {
                if (window.showNotification) window.showNotification('Não foi possível atualizar métricas para o período selecionado.', 'error');
            }
        } catch (_) {
            if (window.showNotification) window.showNotification('Não foi possível atualizar métricas para o período selecionado.', 'error');
        }
    });

    const manager = window.webSocketManager || null;
    if (manager && typeof manager.init === 'function') {
        manager.onDashboardUpdated(async () => {
            try {
                const res = await fetch('{{ route('dashboard.cards') }}', { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                updateDashboardCards(data);
            } catch (_) {}
        });
        manager.init();
    }

    async function fetchInitial() {
        try {
            const res = await fetch('{{ route('dashboard.cards') }}', { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            updateDashboardCards(data);
        } catch (_) {}
    }

    function updateText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    }

    function formatCurrencyBRL(v) {
        try {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v||0));
        } catch (_) {
            return `R$ ${(Number(v||0)).toFixed(2)}`.replace('.', ',');
        }
    }

    function updateDashboardCards(d) {
        updateText('acessosCount', d.acessosHoje);
        updateText('clientesAtivosCount', d.clientesAtivos);
        updateText('totalClientesCount', d.totalClientes);
        updateText('faturamentoValue', formatCurrencyBRL(d.faturamentoMes));
        updateText('vendasHojeValue', formatCurrencyBRL(d.vendasHoje));

        updateText('contasPagarAbertasQtd', d.contasPagarResumo.abertas.quantidade);
        updateText('contasPagarAbertasTotal', formatCurrencyBRL(d.contasPagarResumo.abertas.total));
        updateText('contasPagarVencidasQtd', d.contasPagarResumo.vencidas.quantidade);
        updateText('contasPagarVencidasTotal', formatCurrencyBRL(d.contasPagarResumo.vencidas.total));
        updateText('contasPagarPagasHojeQtd', d.contasPagarResumo.pagasHoje.quantidade);
        updateText('contasPagarPagasHojeTotal', formatCurrencyBRL(d.contasPagarResumo.pagasHoje.total));

        updateText('contasReceberAbertasQtd', d.contasReceberResumo.abertas.quantidade);
        updateText('contasReceberAbertasTotal', formatCurrencyBRL(d.contasReceberResumo.abertas.total));
        updateText('contasReceberVencidasQtd', d.contasReceberResumo.vencidas.quantidade);
        updateText('contasReceberVencidasTotal', formatCurrencyBRL(d.contasReceberResumo.vencidas.total));
        updateText('contasReceberRecebidasHojeQtd', d.contasReceberResumo.recebidasHoje.quantidade);
        updateText('contasReceberRecebidasHojeTotal', formatCurrencyBRL(d.contasReceberResumo.recebidasHoje.total));

        const agora = new Date();
        const diaAtual = agora.getDate();
        const horaAtual = agora.getHours();
        const faturamentoArr = d.faturamentoPorDia || [];
        const acessosArr = d.acessosPorHora || [];
        lastFaturamento = faturamentoArr.slice(0, Math.min(diaAtual, faturamentoArr.length));
        lastAcessos = acessosArr.slice(0, Math.min(horaAtual + 1, acessosArr.length));
        drawLineChart('graficoLinhaFaturamento', lastFaturamento);
        drawBarChart('graficoBarrasAcessosHora', lastAcessos);
    }

    function drawMicroChart(canvasId, values) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const w = canvas.width, h = canvas.height;
        ctx.clearRect(0,0,w,h);
        const max = Math.max(...values, 1);
        const barWidth = Math.floor(w / values.length) - 4;
        values.forEach((v, i) => {
            const x = i * (barWidth + 4) + 2;
            const barHeight = Math.floor((v / max) * (h - 4));
            ctx.fillStyle = '#10b981';
            ctx.fillRect(x, h - barHeight - 2, barWidth, barHeight);
        });
    }

    let lastFaturamento = [];
    let lastAcessos = [];

    function setCanvasSize(canvas, desiredHeight) {
        const dpr = window.devicePixelRatio || 1;
        const widthCss = canvas.parentElement ? canvas.parentElement.clientWidth : 800;
        const heightCss = desiredHeight || (canvas.clientHeight || 256);
        canvas.width = Math.floor(widthCss * dpr);
        canvas.height = Math.floor(heightCss * dpr);
        canvas.style.width = widthCss + 'px';
        canvas.style.height = heightCss + 'px';
        return { ctx: canvas.getContext('2d'), w: canvas.width, h: canvas.height };
    }

    function drawAxes(ctx, w, h, padding, yTicks, yLabels, xTicks) {
        ctx.clearRect(0,0,w,h);
        ctx.strokeStyle = '#6b7280';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(padding.left, h - padding.bottom);
        ctx.lineTo(w - padding.right, h - padding.bottom);
        ctx.moveTo(padding.left, h - padding.bottom);
        ctx.lineTo(padding.left, padding.top);
        ctx.stroke();
        ctx.fillStyle = '#374151';
        ctx.font = '12px Arial';
        yTicks.forEach((yt, i) => {
            const y = padding.top + (1 - yt) * (h - padding.top - padding.bottom);
            ctx.beginPath();
            ctx.moveTo(padding.left - 4, y);
            ctx.lineTo(padding.left, y);
            ctx.stroke();
            const label = yLabels[i];
            const lw = ctx.measureText(label).width;
            ctx.fillText(label, padding.left - 6 - lw, y + 4);
        });
        xTicks.forEach((xt) => {
            const x = padding.left + xt.pos * (w - padding.left - padding.right);
            ctx.beginPath();
            ctx.moveTo(x, h - padding.bottom);
            ctx.lineTo(x, h - padding.bottom + 4);
            ctx.stroke();
            ctx.fillText(xt.label, x - 6, h - padding.bottom + 16);
        });
    }

    function drawLineChart(canvasId, values) {
        const canvas = document.getElementById(canvasId);
        if (!canvas || !values || values.length === 0) return;
        const s = setCanvasSize(canvas, 256);
        const ctx = s.ctx, w = s.w, h = s.h;
        const max = Math.max(...values, 1);
        const yTicks = [0, 0.25, 0.5, 0.75, 1];
        ctx.font = '12px Arial';
        const yLabels = yTicks.map(t => formatCurrencyBRL(t * max));
        const maxLabelWidth = Math.max(...yLabels.map(lbl => ctx.measureText(lbl).width));
        const padding = { left: Math.max(56, Math.ceil(maxLabelWidth) + 18), right: 16, top: 24, bottom: 48 };
        const n = values.length;
        const step = Math.max(1, Math.ceil(n / 8));
        const xTicks = [];
        for (let i = 0; i < n; i += step) {
            const pos = (n === 1 ? 0 : i / (n - 1));
            xTicks.push({ pos, label: String(i + 1) });
        }
        drawAxes(ctx, w, h, padding, yTicks, yLabels, xTicks);
        ctx.strokeStyle = '#10b981';
        ctx.lineWidth = 2;
        ctx.beginPath();
        for (let i = 0; i < n; i++) {
            const x = padding.left + (n === 1 ? 0 : (i / (n - 1)) * (w - padding.left - padding.right));
            const y = padding.top + (1 - (values[i] / max)) * (h - padding.top - padding.bottom);
            if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
        }
        ctx.stroke();
        ctx.fillStyle = '#111827';
        ctx.font = '12px Arial';
        ctx.fillText('Dias do mês', w - padding.right - 80, h - 8);
        ctx.save();
        ctx.translate(14, padding.top + 60);
        ctx.rotate(-Math.PI / 2);
        ctx.fillText('Faturamento (R$)', -100, 0);
        ctx.restore();
    }

    function drawBarChart(canvasId, values) {
        const canvas = document.getElementById(canvasId);
        if (!canvas || !values || values.length === 0) return;
        const s = setCanvasSize(canvas, 256);
        const ctx = s.ctx, w = s.w, h = s.h;
        const max = Math.max(...values, 1);
        const yTicks = [0, 0.25, 0.5, 0.75, 1];
        ctx.font = '12px Arial';
        const yLabels = yTicks.map(t => String(Math.round(t * max)));
        const maxLabelWidth = Math.max(...yLabels.map(lbl => ctx.measureText(lbl).width));
        const padding = { left: Math.max(56, Math.ceil(maxLabelWidth) + 18), right: 16, top: 24, bottom: 48 };
        const n = values.length;
        const step = Math.max(1, Math.ceil(n / 8));
        const xTicks = [];
        for (let i = 0; i < n; i += step) {
            const pos = (n === 1 ? 0 : i / (n - 1));
            xTicks.push({ pos, label: String(i) });
        }
        drawAxes(ctx, w, h, padding, yTicks, yLabels, xTicks);
        const chartW = w - padding.left - padding.right;
        const chartH = h - padding.top - padding.bottom;
        const barW = Math.max(2, Math.floor(chartW / n) - 2);
        ctx.fillStyle = '#6366f1';
        for (let i = 0; i < n; i++) {
            const x = padding.left + i * (chartW / n) + 1;
            const bh = Math.floor((values[i] / max) * chartH);
            ctx.fillRect(x, h - padding.bottom - bh, barW, bh);
        }
        ctx.fillStyle = '#111827';
        ctx.font = '12px Arial';
        ctx.fillText('Horas do dia', w - padding.right - 70, h - 8);
        ctx.save();
        ctx.translate(14, padding.top + 40);
        ctx.rotate(-Math.PI / 2);
        ctx.fillText('Acessos', 0, 0);
        ctx.restore();
    }

    window.addEventListener('resize', () => {
        drawLineChart('graficoLinhaFaturamento', lastFaturamento);
        drawBarChart('graficoBarrasAcessosHora', lastAcessos);
    });

    const modalMensalidade = document.getElementById('modalRenovarMensalidade');
    const formMensalidade = document.getElementById('formRenovarMensalidade');
    const cancelMensalidade = document.getElementById('btnCancelarModalMensalidade');
    const openBtns = document.querySelectorAll('.btn-renovar-mensalidade');
    openBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const action = btn.getAttribute('data-action');
            if (formMensalidade) formMensalidade.setAttribute('action', action);
            if (modalMensalidade) modalMensalidade.classList.remove('hidden');
            if (modalMensalidade) modalMensalidade.classList.add('flex');
        });
    });
    cancelMensalidade && cancelMensalidade.addEventListener('click', () => {
        modalMensalidade.classList.add('hidden');
        modalMensalidade.classList.remove('flex');
    });
    modalMensalidade && modalMensalidade.addEventListener('click', (e) => {
        if (e.target === modalMensalidade) {
            modalMensalidade.classList.add('hidden');
            modalMensalidade.classList.remove('flex');
        }
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalMensalidade && !modalMensalidade.classList.contains('hidden')) {
            modalMensalidade.classList.add('hidden');
            modalMensalidade.classList.remove('flex');
        }
    });

    fetchInitial();
});
</script>
@endpush
