@extends('layouts.app')

@section('title', 'Relatório de Faturamento e Lucratividade - Sistema JyM')

@push('head_styles')
<style>
@media print { .sidebar-layout, .user-info-section { display: none !important; } .main-content-area { margin-left: 0 !important; } }
</style>
@endpush

@section('content')
    <x-relatorio-header 
        titulo="Relatório de Faturamento e Lucratividade" 
        modulo="Financeiro" 
        pdf-route="relatorios.faturamento.pdf" 
    />

    <x-search-filter-dropdown 
        placeholder="Filtrar período..."
        :filters="[
            ['name' => 'dataInicial', 'label' => 'Data Inicial', 'type' => 'date'],
            ['name' => 'dataFinal', 'label' => 'Data Final', 'type' => 'date'],
        ]"
        :sort-options="[]"
    />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card p-4"><div class="text-sm text-gray-500">Receita Total</div><div class="text-2xl font-semibold">R$ {{ number_format($receitaTotal,2,',','.') }}</div></div>
        <div class="card p-4"><div class="text-sm text-gray-500">COGS</div><div class="text-2xl font-semibold">R$ {{ number_format($custoTotal,2,',','.') }}</div></div>
        <div class="card p-4"><div class="text-sm text-gray-500">Despesas Pagas</div><div class="text-2xl font-semibold">R$ {{ number_format($despesasPagas,2,',','.') }}</div></div>
        <div class="card p-4"><div class="text-sm text-gray-500">Lucro Operacional</div><div class="text-2xl font-semibold">R$ {{ number_format($lucroOperacional,2,',','.') }}</div></div>
        <div class="card p-4"><div class="text-sm text-gray-500">Ticket Médio</div><div class="text-2xl font-semibold">R$ {{ number_format($ticketMedio,2,',','.') }}</div></div>
        <div class="card p-4"><div class="text-sm text-gray-500">Margem %</div><div class="text-2xl font-semibold">{{ number_format($margemPercentual,2,',','.') }}%</div></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card p-4"><div class="text-sm text-gray-500">Receita Vendas</div><div class="text-xl font-semibold">R$ {{ number_format($receitaVendas,2,',','.') }}</div></div>
        <div class="card p-4"><div class="text-sm text-gray-500">Receita Mensalidades</div><div class="text-xl font-semibold">R$ {{ number_format($receitaMensalidades,2,',','.') }}</div></div>
        <div class="card p-4"><div class="text-sm text-gray-500">Contas Recebidas</div><div class="text-xl font-semibold">R$ {{ number_format($receitaReceber,2,',','.') }}</div></div>
    </div>
@endsection