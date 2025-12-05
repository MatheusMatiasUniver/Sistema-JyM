@extends('layouts.app')

@section('title', 'Nova Compra - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Nova Compra</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form id="compraForm" action="{{ route('compras.store') }}" method="POST" autocomplete="off">
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
                            <div class="item-row grid grid-cols-4 gap-3 mb-2">
                                <select name="itens[0][idProduto]" class="select col-span-1">
                                    @foreach($produtos as $p)
                                        <option value="{{ $p->idProduto }}">{{ $p->nome }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="itens[0][quantidade]" min="1" placeholder="Qtd" class="item-quantidade border rounded px-2 py-1 text-black" />
                                <input type="number" step="0.01" name="itens[0][precoUnitario]" placeholder="Preço Unit." class="item-preco border rounded px-2 py-1 text-black" />
                                <div class="flex items-center">
                                    <span class="item-subtotal text-sm font-medium text-gray-700">R$ 0,00</span>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="btnAddItem" class="bg-gray-200 px-3 py-1 rounded text-black mt-2">Adicionar Item</button>
                    </div>

                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Frete:</label>
                            <input type="number" step="0.01" name="valorFrete" id="valorFrete" value="0" class="border rounded px-2 py-1 text-black w-full" />
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Desconto:</label>
                            <input type="number" step="0.01" name="valorDesconto" id="valorDesconto" value="0" class="border rounded px-2 py-1 text-black w-full" />
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Impostos:</label>
                            <input type="number" step="0.01" name="valorImpostos" id="valorImpostos" value="0" class="border rounded px-2 py-1 text-black w-full" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Vencimento (Contas a Pagar):</label>
                            <input type="date" name="dataVencimento" class="border rounded px-2 py-1 text-black w-full" />
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Observações:</label>
                            <input type="text" name="observacoes" class="border rounded px-2 py-1 text-black w-full" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Salvar</button>
                        <a href="{{ route('compras.index') }}" class="text-blue-600">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white shadow-lg rounded-lg p-6 sticky top-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumo da Compra</h3>
                
                <div id="resumoItens" class="space-y-2 mb-4 max-h-64 overflow-y-auto">
                    <div class="text-sm text-gray-500 text-center py-4">
                        Adicione itens à compra
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-4 space-y-2">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal Itens:</span>
                        <span id="subtotalItens">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Frete:</span>
                        <span id="resumoFrete">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Impostos:</span>
                        <span id="resumoImpostos">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between text-sm text-red-600">
                        <span>Desconto:</span>
                        <span id="resumoDesconto">- R$ 0,00</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2 mt-2">
                        <div class="flex justify-between items-center text-lg font-bold text-black">
                            <span>Total:</span>
                            <span id="totalCompra" class="text-grip-1">R$ 0,00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let itemIndex = 1;
            const container = document.getElementById('itens-container');
            const btnAddItem = document.getElementById('btnAddItem');
            const produtosOptions = `@foreach($produtos as $p)<option value="{{ $p->idProduto }}">{{ $p->nome }}</option>@endforeach`;

            function formatCurrency(value) {
                return 'R$ ' + value.toFixed(2).replace('.', ',');
            }

            function calcularTotais() {
                let subtotalItens = 0;
                const rows = container.querySelectorAll('.item-row');
                
                rows.forEach(row => {
                    const quantidade = parseFloat(row.querySelector('.item-quantidade').value) || 0;
                    const preco = parseFloat(row.querySelector('.item-preco').value) || 0;
                    const subtotal = quantidade * preco;
                    row.querySelector('.item-subtotal').textContent = formatCurrency(subtotal);
                    subtotalItens += subtotal;
                });

                const frete = parseFloat(document.getElementById('valorFrete').value) || 0;
                const desconto = parseFloat(document.getElementById('valorDesconto').value) || 0;
                const impostos = parseFloat(document.getElementById('valorImpostos').value) || 0;

                const total = subtotalItens + frete + impostos - desconto;

                document.getElementById('subtotalItens').textContent = formatCurrency(subtotalItens);
                document.getElementById('resumoFrete').textContent = formatCurrency(frete);
                document.getElementById('resumoImpostos').textContent = formatCurrency(impostos);
                document.getElementById('resumoDesconto').textContent = '- ' + formatCurrency(desconto);
                document.getElementById('totalCompra').textContent = formatCurrency(total);

                atualizarResumoItens();
            }

            function atualizarResumoItens() {
                const rows = container.querySelectorAll('.item-row');
                const resumoItens = document.getElementById('resumoItens');
                
                if (rows.length === 0) {
                    resumoItens.innerHTML = '<div class="text-sm text-gray-500 text-center py-4">Adicione itens à compra</div>';
                    return;
                }

                let html = '';
                rows.forEach((row, index) => {
                    const select = row.querySelector('select');
                    const produto = select.options[select.selectedIndex].text;
                    const quantidade = row.querySelector('.item-quantidade').value || 0;
                    const preco = parseFloat(row.querySelector('.item-preco').value) || 0;
                    const subtotal = quantidade * preco;

                    if (quantidade > 0 && preco > 0) {
                        html += `
                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded text-sm">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900 truncate">${produto}</div>
                                    <div class="text-xs text-gray-500">${quantidade}x ${formatCurrency(preco)}</div>
                                </div>
                                <div class="font-semibold text-green-600 ml-2">
                                    ${formatCurrency(subtotal)}
                                </div>
                            </div>
                        `;
                    }
                });

                resumoItens.innerHTML = html || '<div class="text-sm text-gray-500 text-center py-4">Preencha quantidade e preço</div>';
            }

            function addInputListeners(row) {
                row.querySelector('.item-quantidade').addEventListener('input', calcularTotais);
                row.querySelector('.item-preco').addEventListener('input', calcularTotais);
                row.querySelector('select').addEventListener('change', calcularTotais);
            }

            addInputListeners(container.querySelector('.item-row'));

            btnAddItem.addEventListener('click', function() {
                const row = document.createElement('div');
                row.className = 'item-row grid grid-cols-4 gap-3 mb-2';
                row.innerHTML = `
                    <select name="itens[${itemIndex}][idProduto]" class="select col-span-1">
                        ${produtosOptions}
                    </select>
                    <input type="number" name="itens[${itemIndex}][quantidade]" min="1" placeholder="Qtd" class="item-quantidade border rounded px-2 py-1 text-black" />
                    <input type="number" step="0.01" name="itens[${itemIndex}][precoUnitario]" placeholder="Preço Unit." class="item-preco border rounded px-2 py-1 text-black" />
                    <div class="flex items-center justify-between">
                        <span class="item-subtotal text-sm font-medium text-gray-700">R$ 0,00</span>
                        <button type="button" class="btn-remove-item text-red-500 hover:text-red-700 ml-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                `;
                container.appendChild(row);
                addInputListeners(row);

                row.querySelector('.btn-remove-item').addEventListener('click', function() {
                    row.remove();
                    calcularTotais();
                });

                itemIndex++;
                calcularTotais();
            });

            document.getElementById('valorFrete').addEventListener('input', calcularTotais);
            document.getElementById('valorDesconto').addEventListener('input', calcularTotais);
            document.getElementById('valorImpostos').addEventListener('input', calcularTotais);

            calcularTotais();
        });
    </script>
@endsection
