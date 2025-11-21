@extends('layouts.app')

@section('title', 'Relatório por Funcionário - Sistema JyM')

@push('head_styles')
<style>
@media print { .sidebar-layout, .user-info-section { display: none !important; } .main-content-area { margin-left: 0 !important; } }
</style>
@endpush

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Relatório por Funcionário</h1>

    <div class="mb-4 flex gap-2">
        <a href="{{ route('relatorios.porFuncionario.pdf', request()->query()) }}" class="btn btn-primary">Exportar PDF</a>
        <button type="button" class="btn btn-secondary" onclick="window.print()">Imprimir</button>
    </div>

    <x-search-filter-dropdown 
        placeholder="Filtrar por funcionário..."
        :filters="[
            ['name' => 'idFuncionario', 'label' => 'Funcionário', 'type' => 'number'],
            ['name' => 'dataInicial', 'label' => 'Data Inicial', 'type' => 'date'],
            ['name' => 'dataFinal', 'label' => 'Data Final', 'type' => 'date'],
        ]"
        :sort-options="[]"
    />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card p-4"><div class="text-sm text-gray-500">Total de Vendas</div><div class="text-2xl font-semibold">R$ {{ number_format($totalVendas,2,',','.') }}</div></div>
        <div class="card p-4"><div class="text-sm text-gray-500">Total de Despesas</div><div class="text-2xl font-semibold">R$ {{ number_format($totalDespesas,2,',','.') }}</div></div>
        <div class="card p-4"><div class="text-sm text-gray-500">Quantidade de Vendas</div><div class="text-2xl font-semibold">{{ $qtdVendas }}</div></div>
    </div>

    <h2 class="text-xl font-semibold mb-2">Vendas</h2>
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Funcionário</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Data</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendas as $v)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $v->idVenda }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $v->funcionarioNome ?? '—' }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ \Carbon\Carbon::parse($v->dataVenda)->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($v->valorTotal,2,',','.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Sem dados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($vendas->hasPages())
        <div class="mt-6">{{ $vendas->links() }}</div>
    @endif

    <h2 class="text-xl font-semibold mb-2">Despesas</h2>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Descrição</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Funcionário</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pagamento</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Valor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($despesas as $d)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $d->descricao }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $d->funcionarioNome ?? '—' }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $d->dataPagamento ? \Carbon\Carbon::parse($d->dataPagamento)->format('d/m/Y') : '—' }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($d->valorTotal,2,',','.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Sem dados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($despesas->hasPages())
        <div class="mt-6">{{ $despesas->links() }}</div>
    @endif
@endsection