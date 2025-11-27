@extends('layouts.app')

@section('title', 'Contas a Receber - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Contas a Receber</h1>

    <x-search-filter-dropdown 
        placeholder="Pesquisar contas a receber..."
        :filters="[
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['aberta' => 'Aberta', 'recebida' => 'Recebida', 'cancelada' => 'Cancelada']],
            ['name' => 'data_inicial', 'label' => 'Vencimento Inicial', 'type' => 'date'],
            ['name' => 'data_final', 'label' => 'Vencimento Final', 'type' => 'date'],
        ]"
        :sort-options="[]"
    />

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cliente</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Descrição</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Valor</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vencimento</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Recebimento</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contas as $c)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $c->idContaReceber }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ optional($c->cliente)->nome }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $c->descricao }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($c->valorTotal, 2, ',', '.') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ ucfirst($c->status) }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ optional($c->dataVencimento)->format('d/m/Y') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ optional($c->dataRecebimento)->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Nenhuma conta encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($contas->hasPages())
        <div class="mt-6">{{ $contas->links() }}</div>
    @endif
@endsection