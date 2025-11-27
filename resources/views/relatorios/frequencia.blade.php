@extends('layouts.app')

@section('title', 'Relatório de Frequência - Sistema JyM')

@push('head_styles')
<style>
@media print { .sidebar-layout, .user-info-section { display: none !important; } .main-content-area { margin-left: 0 !important; } }
</style>
@endpush

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Frequência</h1>

    <div class="mb-4 flex gap-2">
        <a href="{{ route('relatorios.frequencia.pdf', request()->query()) }}" class="btn btn-primary">Exportar PDF</a>
        <button type="button" class="btn btn-secondary" onclick="window.print()">Imprimir</button>
    </div>

    <x-search-filter-dropdown 
        placeholder="Filtrar entradas..."
        :filters="[
            ['name' => 'dataInicial', 'label' => 'Data Inicial', 'type' => 'date'],
            ['name' => 'dataFinal', 'label' => 'Data Final', 'type' => 'date'],
            ['name' => 'metodo', 'label' => 'Método', 'type' => 'select', 'options' => ['Reconhecimento Facial','CPF/Senha','Manual']],
        ]"
        :sort-options="[]"
    />

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dia</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Entradas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($porDia as $d)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ \Carbon\Carbon::parse($d->dia)->format('d/m/Y') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $d->quantidade }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Sem dados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cliente</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Data/Hora</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Método</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entradas as $e)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $e->nome }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ \Carbon\Carbon::parse($e->dataHora)->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $e->metodo }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Sem dados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($entradas->hasPages())
        <div class="mt-6">{{ $entradas->links() }}</div>
    @endif
@endsection