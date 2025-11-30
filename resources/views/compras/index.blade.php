@extends('layouts.app')

@section('title', 'Compras - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Compras</h1>

    <div class="mb-4 flex space-x-2">
        <a href="{{ route('compras.create') }}" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Nova Compra</a>
    </div>

    <x-search-filter-dropdown 
        placeholder="Pesquisar compras..."
        :filters="[
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['aberta' => 'Aberta', 'recebida' => 'Recebida', 'cancelada' => 'Cancelada']],
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
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($compras as $compra)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $compra->idCompra }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $compra->fornecedor->razaoSocial }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $compra->dataEmissao->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ ucfirst($compra->status) }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($compra->valorTotal,2,',','.') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            <div class="flex space-x-2">
                                <a href="{{ route('compras.show', $compra->idCompra) }}" class="text-blue-600">Ver</a>
                                @if($compra->status==='aberta')
                                    <form action="{{ route('compras.receber', $compra->idCompra) }}" method="POST" 
                                          data-confirm="Confirmar recebimento desta compra?"
                                          data-confirm-title="Receber Compra"
                                          data-confirm-icon="info"
                                          data-confirm-text="Confirmar"
                                          data-cancel-text="Cancelar">
                                        @csrf
                                        <button class="text-green-600">Receber</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Nenhuma compra encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($compras->hasPages())
        <div class="mt-6">{{ $compras->links() }}</div>
    @endif
@endsection

