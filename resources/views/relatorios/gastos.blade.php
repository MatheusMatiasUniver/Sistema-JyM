@extends('layouts.app')

@section('title', 'Relatório de Gastos - Sistema JyM')

@push('head_styles')
<style>
@media print { .sidebar-layout, .user-info-section { display: none !important; } .main-content-area { margin-left: 0 !important; } }
</style>
@endpush

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Gastos</h1>

    <div class="mb-4 flex gap-2">
        <a href="{{ route('relatorios.gastos.pdf', request()->query()) }}" class="btn btn-primary">Exportar PDF</a>
        <button type="button" class="btn btn-secondary" onclick="window.print()">Imprimir</button>
    </div>

    <x-search-filter-dropdown 
        placeholder="Filtrar gastos..."
        :filters="[
            ['name' => 'dataInicial', 'label' => 'Data Inicial', 'type' => 'date'],
            ['name' => 'dataFinal', 'label' => 'Data Final', 'type' => 'date'],
            ['name' => 'idFornecedor', 'label' => 'Fornecedor', 'type' => 'number'],
            ['name' => 'idFuncionario', 'label' => 'Funcionário', 'type' => 'number'],
            ['name' => 'idCategoria', 'label' => 'Categoria', 'type' => 'number'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['Aberta','Paga','Cancelada']],
        ]"
        :sort-options="[]"
    />

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Categoria</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($totaisPorCategoria as $t)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $t->categoria ?? 'Sem Categoria' }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($t->total,2,',','.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Sem dados.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td class="px-5 py-5 border-b bg-white text-sm font-semibold">Total Geral</td>
                    <td class="px-5 py-5 border-b bg-white text-sm font-semibold">R$ {{ number_format($totalGeral,2,',','.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Descrição</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Categoria</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fornecedor</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Funcionário</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vencimento</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Valor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contas as $c)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $c->descricao }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $c->categoriaNome ?? '—' }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $c->razaoSocial ?? '—' }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $c->funcionarioNome ?? '—' }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $c->status }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ \Carbon\Carbon::parse($c->dataVencimento)->format('d/m/Y') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($c->valorTotal,2,',','.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Sem dados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($contas->hasPages())
        <div class="mt-6">{{ $contas->links() }}</div>
    @endif
@endsection