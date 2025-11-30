@extends('layouts.app')

@section('title', 'Relatório de Vendas de Produtos - Sistema JyM')

@push('head_styles')
<style>
@media print { .sidebar-layout, .user-info-section { display: none !important; } .main-content-area { margin-left: 0 !important; } }
</style>
@endpush

@section('content')
    <x-relatorio-header 
        titulo="Relatório de Vendas de Produtos" 
        modulo="Vendas" 
        pdf-route="relatorios.vendas.pdf" 
    />

    <x-search-filter-dropdown 
        placeholder="Filtrar período de vendas..."
        :filters="[
            ['name' => 'dataInicial', 'label' => 'Data Inicial', 'type' => 'date'],
            ['name' => 'dataFinal', 'label' => 'Data Final', 'type' => 'date'],
        ]"
        :sort-options="[]"
    />

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produto</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantidade</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Receita</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProdutos as $p)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $p->nome }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $p->quantidade }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($p->receita,2,',','.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Sem dados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mb-4"><span class="text-sm text-gray-500">Ticket Médio</span> <span class="text-xl font-semibold">R$ {{ number_format($ticketMedio,2,',','.') }}</span></div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Funcionário</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cliente</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Data</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendas as $v)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $v->idVenda }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $v->funcionarioNome ?? '—' }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $v->clienteNome ?? '—' }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ \Carbon\Carbon::parse($v->dataVenda)->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($v->valorTotal,2,',','.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Sem dados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($vendas->hasPages())
        <div class="mt-6">{{ $vendas->links() }}</div>
    @endif
@endsection