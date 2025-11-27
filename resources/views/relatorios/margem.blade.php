@extends('layouts.app')

@section('title', 'Margem por Produto - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Margem por Produto</h1>

    <x-search-filter-dropdown 
        placeholder="Filtrar período de vendas..."
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
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produto</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qtde</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Receita</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Custo</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Margem</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dados as $d)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $d->nome }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $d->quantidadeTotal }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($d->receitaTotal,2,',','.') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($d->custoTotal,2,',','.') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($d->receitaTotal - $d->custoTotal,2,',','.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Sem dados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($dados->hasPages())
        <div class="mt-6">{{ $dados->links() }}</div>
    @endif
@endsection

