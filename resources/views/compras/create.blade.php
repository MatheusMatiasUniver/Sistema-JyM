@extends('layouts.app')

@section('title', 'Nova Compra - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Nova Compra</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-3xl mx-auto">
        <form action="{{ route('compras.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="idFornecedor" class="block text-gray-700 text-sm font-bold mb-2">Fornecedor:</label>
                <select id="idFornecedor" name="idFornecedor" required class="select">
                    <option value="">Selecione</option>
                    @foreach($fornecedores as $fornecedor)
                        <option value="{{ $fornecedor->idFornecedor }}">{{ $fornecedor->razaoSocial }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Itens da Compra:</label>
                <div id="itens-container">
                    <div class="grid grid-cols-3 gap-3 mb-2">
                        <select name="itens[0][idProduto]" class="select">
                            @foreach($produtos as $p)
                                <option value="{{ $p->idProduto }}">{{ $p->nome }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="itens[0][quantidade]" min="1" placeholder="Qtd" class="border rounded px-2 py-1 text-black" />
                        <input type="number" step="0.01" name="itens[0][precoUnitario]" placeholder="Preço" class="border rounded px-2 py-1 text-black" />
                    </div>
                </div>
                <button type="button" onclick="addItem()" class="bg-gray-200 px-3 py-1 rounded text-black">Adicionar Item</button>
            </div>

            <div class="grid grid-cols-3 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Frete:</label>
                    <input type="number" step="0.01" name="valorFrete" class="border rounded px-2 py-1 text-black" />
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Desconto:</label>
                    <input type="number" step="0.01" name="valorDesconto" class="border rounded px-2 py-1 text-black" />
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Impostos:</label>
                    <input type="number" step="0.01" name="valorImpostos" class="border rounded px-2 py-1 text-black" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Vencimento (Contas a Pagar):</label>
                    <input type="date" name="dataVencimento" class="border rounded px-2 py-1 text-black" />
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Observações:</label>
                    <input type="text" name="observacoes" class="border rounded px-2 py-1 text-black" />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Salvar</button>
                <a href="{{ route('compras.index') }}" class="text-blue-600">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        let itemIndex = 1;
        function addItem() {
            const container = document.getElementById('itens-container');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-3 gap-3 mb-2';
            row.innerHTML = `
                <select name="itens[${itemIndex}][idProduto]" class="select">
                    ${`@foreach($produtos as $p)<option value="{{ $p->idProduto }}">{{ $p->nome }}</option>@endforeach`}
                </select>
                <input type="number" name="itens[${itemIndex}][quantidade]" min="1" placeholder="Qtd" class="border rounded px-2 py-1 text-black" />
                <input type="number" step="0.01" name="itens[${itemIndex}][precoUnitario]" placeholder="Preço" class="border rounded px-2 py-1 text-black" />
            `;
            container.appendChild(row);
            itemIndex++;
        }
    </script>
@endsection
