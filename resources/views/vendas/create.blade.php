@extends('layouts.app')

@section('title', 'Registrar Nova Venda - Sistema JyM')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
    <!-- Elemento para passar dados para o JavaScript -->
    <div id="produtos-data" 
         data-produtos="{{ json_encode($produtos->keyBy('idProduto')) }}"
         data-product-index="{{ old('produtos') ? count(old('produtos')) : 0 }}"
         class="hidden">
    </div>

    <h1 class="text-3xl font-bold mb-6 text-gray-800">Registrar Nova Venda</h1>

    @if(session('error'))
        <div class="alert-error" role="alert">
            <strong class="font-bold">Erro!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
            @if($errors->any())
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6 max-w-4xl mx-auto">
        <form id="vendaForm" action="{{ route('vendas.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="idCliente" class="block text-gray-700 text-sm font-bold mb-2">Cliente:</label>
                <select id="idCliente" name="idCliente" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('idCliente') border-red-500 @enderror">
                    <option value="">Selecione um cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->idCliente }}" {{ old('idCliente') == $cliente->idCliente ? 'selected' : '' }}>
                            {{ $cliente->nome }} (CPF: {{ $cliente->cpfFormatado }})
                        </option>
                    @endforeach
                </select>
                @error('idCliente')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="tipoPagamento" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Pagamento:</label>
                <select id="tipoPagamento" name="tipoPagamento" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('tipoPagamento') border-red-500 @enderror">
                    <option value="">Selecione o tipo de pagamento</option>
                    @foreach($tiposPagamento as $tipo)
                        <option value="{{ $tipo }}" {{ old('tipoPagamento') == $tipo ? 'selected' : '' }}>
                            {{ $tipo }}
                        </option>
                    @endforeach
                </select>
                @error('tipoPagamento')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <h2 class="text-xl font-bold mb-4 text-gray-800">Itens da Venda</h2>
            <div id="produtos-container" class="mb-4">                
                @if(old('produtos'))
                    @foreach(old('produtos') as $index => $oldProduct)
                        <div class="flex items-center space-x-2 mb-2 product-item">
                            <select name="produtos[{{ $index }}][idProduto]" class="shadow border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline flex-grow product-select">
                                <option value="">Selecione um produto</option>
                                @foreach($produtos as $prod)
                                    <option value="{{ $prod->idProduto }}" data-preco="{{ $prod->preco }}" data-estoque="{{ $prod->estoque }}" {{ $oldProduct['idProduto'] == $prod->idProduto ? 'selected' : '' }}>
                                        {{ $prod->nome }} (Estoque: {{ $prod->estoque }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="number" name="produtos[{{ $index }}][quantidade]" value="{{ $oldProduct['quantidade'] }}" min="1" placeholder="Qtd" class="shadow border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-20 product-quantity">
                            <span class="product-price font-semibold w-24 text-right">R\$ {{ number_format($oldProduct['precoUnitario'] ?? 0, 2, ',', '.') }}</span>
                            <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded remove-product-btn">-</button>
                        </div>
                    @endforeach
                @endif
            </div>

            <button type="button" id="add-product-btn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-4">
                Adicionar Produto
            </button>

            <div class="text-right text-2xl font-bold text-gray-800 mb-6">
                Total: <span id="total-venda">R\$ 0,00</span>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Registrar Venda
                </button>
                <a href="{{ route('vendas.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection