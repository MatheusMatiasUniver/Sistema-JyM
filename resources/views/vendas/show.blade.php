@extends('layouts.app')

@section('title', 'Detalhes da Venda #' . $venda->idVenda . ' - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Detalhes da Venda #{{ $venda->idVenda }}</h1>

    @if(session('success'))
        <div class="alert-success" role="alert">
            <strong class="font-bold">Sucesso!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert-error" role="alert">
            <strong class="font-bold">Erro!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6 max-w-4xl mx-auto">
        <div class="mb-4">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Informações da Venda</h2>
            <p class="text-black"><strong>Cliente:</strong> {{ $venda->cliente ? $venda->cliente->nome : 'Cliente Removido' }} (CPF: {{ $venda->cliente ? $venda->cliente->cpfFormatado : 'N/A' }})</p>
            <p class="text-black"><strong>Data da Venda:</strong> {{ $venda->dataVenda->format('d/m/Y H:i:s') }}</p>
            <p class="text-black"><strong>Valor Total:</strong> R$ {{ number_format($venda->valorTotal, 2, ',', '.') }}</p>
            <p class="text-black"><strong>Tipo de Pagamento:</strong> {{ $venda->tipoPagamento }}</p>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Produtos Comprados</h2>
            @if($venda->itensVenda->isEmpty())
                <p>Nenhum produto associado a esta venda.</p>
            @else
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Produto
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Quantidade
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Preço Unitário
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Subtotal
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venda->itensVenda as $item)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    {{ $item->produto ? $item->produto->nome : 'Produto Removido' }}
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    {{ $item->quantidade }}
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    R$ {{ number_format($item->precoUnitario, 2, ',', '.') }}
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    R$ {{ number_format($item->quantidade * $item->precoUnitario, 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="flex justify-end">
            <a href="{{ route('vendas.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Voltar ao Histórico de Vendas
            </a>
            <form action="{{ route('vendas.destroy', $venda->idVenda) }}" method="POST" class="ml-3" onsubmit="return confirm('Tem certeza que deseja estornar esta venda? Esta ação irá retornar os produtos ao estoque.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Estornar Venda</button>
            </form>
        </div>
    </div>
@endsection