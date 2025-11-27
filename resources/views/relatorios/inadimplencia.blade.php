@extends('layouts.app')

@section('title', 'Relatório de Inadimplência - Sistema JyM')

@push('head_styles')
<style>
@media print { .sidebar-layout, .user-info-section { display: none !important; } .main-content-area { margin-left: 0 !important; } }
</style>
@endpush

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Inadimplência</h1>

    <div class="mb-4 flex gap-2">
        <a href="{{ route('relatorios.inadimplencia.pdf', request()->query()) }}" class="btn btn-primary">Exportar PDF</a>
        <button type="button" class="btn btn-secondary" onclick="window.print()">Imprimir</button>
    </div>

    <x-search-filter-dropdown 
        placeholder="Filtrar por vencimento..."
        :filters="[
            ['name' => 'dataInicial', 'label' => 'Venc. Inicial', 'type' => 'date'],
            ['name' => 'dataFinal', 'label' => 'Venc. Final', 'type' => 'date'],
        ]"
        :sort-options="[]"
    />

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="card p-4"><div class="text-sm text-gray-500">0–30 dias</div><div class="text-xl font-semibold">R$ {{ number_format($bucket030,2,',','.') }}</div></div>
        <div class="card p-4"><div class="text-sm text-gray-500">31–60 dias</div><div class="text-xl font-semibold">R$ {{ number_format($bucket3160,2,',','.') }}</div></div>
        <div class="card p-4"><div class="text-sm text-gray-500">61–90 dias</div><div class="text-xl font-semibold">R$ {{ number_format($bucket6190,2,',','.') }}</div></div>
        <div class="card p-4"><div class="text-sm text-gray-500">> 90 dias</div><div class="text-xl font-semibold">R$ {{ number_format($bucket90p,2,',','.') }}</div></div>
        <div class="card p-4"><div class="text-sm text-gray-500">Total em Aberto</div><div class="text-xl font-semibold">R$ {{ number_format($totalAberto,2,',','.') }}</div></div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cliente</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vencimento</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Valor</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mensalidades as $m)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $m->nome }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ \Carbon\Carbon::parse($m->dataVencimento)->format('d/m/Y') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($m->valor,2,',','.') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $m->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Sem dados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($mensalidades->hasPages())
        <div class="mt-6">{{ $mensalidades->links() }}</div>
    @endif
@endsection