@extends('layouts.app')

@section('title', 'Relatório de Compras de Fornecedores - Sistema JyM')

@push('head_styles')
<style>
@media print { .sidebar-layout, .user-info-section { display: none !important; } .main-content-area { margin-left: 0 !important; } }
</style>
@endpush

@section('content')
    <x-relatorio-header 
        titulo="Relatório de Compras de Fornecedores" 
        modulo="Compras" 
        pdf-route="relatorios.compras.pdf" 
    />

    <x-search-filter-dropdown 
        placeholder="Pesquisar compras..."
        :filters="[
            ['name' => 'data_inicial', 'label' => 'Data Inicial', 'type' => 'date'],
            ['name' => 'data_final', 'label' => 'Data Final', 'type' => 'date'],
        ]"
        :sort-options="[]"
    />

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fornecedor</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Data</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($compras as $c)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $c->idCompra }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $c->razaoSocial }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ \Carbon\Carbon::parse($c->dataEmissao)->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ ucfirst($c->status) }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($c->valorTotal,2,',','.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Sem dados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($compras->hasPages())
        <div class="mt-6">{{ $compras->links() }}</div>
    @endif
@endsection

