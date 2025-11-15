@extends('layouts.app')

@section('title', 'Histórico de Vendas - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Histórico de Vendas</h1>
   
    @if(session('info'))
    <div class="bg-grip-4 border border-border-light text-grip-3 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Info!</strong>
            <span class="block sm:inline">{{ session('info') }}</span>
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('vendas.create') }}" class="bg-grip-1 hover:bg-grip-2 hover:text-white text-white font-bold py-2 px-4 rounded">
            Registrar Nova Venda
        </a>
    </div>

    <!-- Filtros -->
    <x-search-filter-dropdown 
        placeholder="ID da venda ou nome do cliente..."
        :filters="[
            [
                'name' => 'forma_pagamento',
                'label' => 'Forma de Pagamento',
                'type' => 'select',
                'options' => [
                    'Dinheiro' => 'Dinheiro',
                    'Cartão de Crédito' => 'Cartão de Crédito',
                    'Cartão de Débito' => 'Cartão de Débito',
                    'PIX' => 'PIX',
                    'Boleto' => 'Boleto'
                ]
            ],
            [
                'name' => 'data_inicial',
                'label' => 'Data Inicial',
                'type' => 'date'
            ],
            [
                'name' => 'data_final',
                'label' => 'Data Final',
                'type' => 'date'
            ],
            [
                'name' => 'valor_minimo',
                'label' => 'Valor Mínimo',
                'type' => 'number',
                'placeholder' => '0.00',
                'step' => '0.01',
                'min' => '0'
            ],
            [
                'name' => 'valor_maximo',
                'label' => 'Valor Máximo',
                'type' => 'number',
                'placeholder' => '999.99',
                'step' => '0.01',
                'min' => '0'
            ]
        ]"
        :sort-options="[
            'data_desc' => 'Data (Recente)',
            'data_asc' => 'Data (Antiga)',
            'valor_desc' => 'Valor (Maior)',
            'valor_asc' => 'Valor (Menor)',
            'id_desc' => 'ID (Maior)',
            'id_asc' => 'ID (Menor)'
        ]"
    />

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        ID Venda
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Cliente
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Data
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Valor Total
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Tipo Pagamento
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vendas as $venda)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $venda->idVenda }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $venda->cliente ? $venda->cliente->nome : 'Cliente Removido' }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $venda->dataVenda->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            R$ {{ number_format($venda->valorTotal, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $venda->formaPagamento }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('vendas.show', $venda->idVenda) }}" class="text-grip-1 hover:text-grip-red-light">Ver Detalhes</a>
                                <form action="{{ route('vendas.destroy', $venda->idVenda) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja estornar esta venda? Esta ação irá retornar os produtos ao estoque.');">
                                    @csrf
                                    @method('DELETE')
                                <button type="submit" class="text-grip-2 hover:text-grip-red-dark">Estornar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            Nenhuma venda registrada ainda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
            {{ $vendas->links() }}
        </div>
    </div>
@endsection
