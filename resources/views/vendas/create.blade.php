@extends('layouts.app')

@section('title', 'Registrar Nova Venda - Sistema JyM')

@vite(['resources/css/app.css', 'resources/js/app.js'])


@section('content')
    <div id="produtos-data" 
         data-produtos="{{ json_encode($produtos->keyBy('idProduto')) }}"
         class="hidden">
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-grip-6">Registrar Nova Venda</h1>
            <p class="mt-2 text-sm text-gray-600">Selecione produtos, cliente e forma de pagamento para registrar uma nova venda</p>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Erro!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Sucesso!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Erros de validação:</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="vendaForm" action="{{ route('vendas.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-3">
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Produtos Disponíveis</h2>
                            <p class="text-sm text-gray-600">Selecione os produtos e especifique as quantidades</p>
                        </div>
                        
                        <div class="p-4 bg-gray-50 border-b border-gray-200">
                            <div class="relative">
                                <input type="text" 
                                       id="searchProducts" 
                                       placeholder="Pesquisar produtos por nome..." 
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-grip-1 focus:border-grip-1 text-black">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto overflow-y-auto h-[673px]">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selecionar</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody" class="bg-white divide-y divide-gray-200">
                                    @foreach($produtos as $produto)
                                        <tr class="product-row hover:bg-gray-50" data-product-id="{{ $produto->idProduto }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" 
                                                       class="product-checkbox h-4 w-4 text-grip-1 focus:ring-grip-1 border-gray-300 rounded"
                                                       data-product-id="{{ $produto->idProduto }}"
                                                       data-product-name="{{ $produto->nome }}"
                                                       data-product-price="{{ $produto->preco }}"
                                                       data-product-stock="{{ $produto->estoque }}">
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $produto->nome }}</div>
                                                @if($produto->descricao)
                                                    <div class="text-sm text-gray-500">{{ Str::limit($produto->descricao, 50) }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-grip-4 text-grip-3">
                                                    {{ $produto->categoria->nome ?? 'Sem categoria' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-grip-1">R$ {{ number_format($produto->preco, 2, ',', '.') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $produto->estoque <= 5 ? 'bg-grip-red-light text-white' : 'bg-grip-4 text-grip-3' }}">
                                                    {{ $produto->estoque }} unidades
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number" 
                                                       class="quantity-input w-20 px-3 py-1 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-grip-1 focus:border-grip-1 text-black"
                                                       data-product-id="{{ $produto->idProduto }}"
                                                       min="1" 
                                                       max="{{ $produto->estoque }}" 
                                                       value="1"
                                                       disabled>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="space-y-6">
                        <div class="bg-white shadow-lg rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cliente</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="idCliente" class="block text-sm font-medium text-gray-700 mb-2">Selecionar Cliente</label>
                                    <select id="idCliente" name="idCliente" required
                                            class="select @error('idCliente') border-grip-2 @enderror">
                                        <option value="">Escolha um cliente</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->idCliente }}" 
                                                    data-cpf="{{ $cliente->cpfFormatado }}"
                                                    data-telefone="{{ $cliente->telefone }}"
                                                    {{ old('idCliente') == $cliente->idCliente ? 'selected' : '' }}>
                                                {{ $cliente->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('idCliente')
                                        <p class="text-grip-2 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Informações do Cliente Selecionado -->
                                <div id="clienteInfo" class="hidden p-3 bg-grip-4 rounded-lg border border-border-light">
                                    <h4 class="text-sm font-medium text-grip-3 mb-2">Informações do Cliente</h4>
                                    <div class="text-sm text-grip-3">
                                        <p><span class="font-medium">CPF:</span> <span id="clienteCpf">-</span></p>
                                        <p><span class="font-medium">Telefone:</span> <span id="clienteTelefone">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Forma de Pagamento -->
                        <div class="bg-white shadow-lg rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Forma de Pagamento</h3>
                            <div>
                                <label for="formaPagamento" class="block text-sm font-medium text-gray-700 mb-2">Método de Pagamento</label>
                                <select id="formaPagamento" name="formaPagamento" required
                                        class="select @error('formaPagamento') border-grip-2 @enderror">
                                    <option value="">Selecione o pagamento</option>
                                    @foreach($tiposPagamento as $tipo)
                                        <option value="{{ $tipo }}" {{ old('formaPagamento') == $tipo ? 'selected' : '' }}>
                                            {{ $tipo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('formaPagamento')
                                    <p class="text-grip-2 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Resumo dos Produtos Selecionados -->
                        <div class="bg-white shadow-lg rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumo da Venda</h3>
                            
                            <div id="selectedProductsList" class="space-y-3 mb-4">
                                <div class="text-sm text-gray-500 text-center py-4">
                                    Nenhum produto selecionado
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-center text-lg font-bold">
                                    <span>Total:</span>
                                    <span id="totalVenda" class="text-grip-1">R$ 0,00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="bg-white shadow-lg rounded-lg p-6">
                            <div class="space-y-3">
                                <button type="submit" 
                                        id="btnRegistrarVenda"
                                        disabled
                                        class="w-full bg-gray-400 text-white font-bold py-3 px-4 rounded-lg cursor-not-allowed transition-colors duration-200">
                                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Registrar Venda
                                </button>
                                
                                <a href="{{ route('vendas.index') }}" 
                                   class="w-full bg-grip-1 hover:bg-grip-2 hover:text-white text-white font-bold py-3 px-4 rounded-lg text-center block transition-colors duration-200">
                                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancelar
                                </a>
                            </div>
                            
                            <!-- Indicadores de Validação -->
                            <div class="mt-4 space-y-2 text-xs">
                                <div id="validationProducts" class="flex items-center text-grip-2">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Selecione pelo menos um produto
                                </div>
                                <div id="validationClient" class="flex items-center text-grip-2">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Selecione um cliente
                                </div>
                                <div id="validationPayment" class="flex items-center text-grip-2">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Selecione a forma de pagamento
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campos Hidden para Produtos Selecionados -->
            <div id="hiddenProductInputs"></div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchProducts');
            const productRows = document.querySelectorAll('.product-row');
            const productCheckboxes = document.querySelectorAll('.product-checkbox');
            const quantityInputs = document.querySelectorAll('.quantity-input');
            const clienteSelect = document.getElementById('idCliente');
            const clienteInfo = document.getElementById('clienteInfo');
            const clienteCpf = document.getElementById('clienteCpf');
            const clienteTelefone = document.getElementById('clienteTelefone');
            const formaPagamentoSelect = document.getElementById('formaPagamento');
            const selectedProductsList = document.getElementById('selectedProductsList');
            const totalVendaSpan = document.getElementById('totalVenda');
            const btnRegistrarVenda = document.getElementById('btnRegistrarVenda');
            const hiddenProductInputs = document.getElementById('hiddenProductInputs');
            
            let selectedProducts = new Map();

            // Pesquisa de produtos
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                productRows.forEach(row => {
                    const productName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    if (productName.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Seleção de produtos
            productCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const productId = this.dataset.productId;
                    const quantityInput = document.querySelector(`input.quantity-input[data-product-id="${productId}"]`);
                    
                    if (this.checked) {
                        quantityInput.disabled = false;
                        addProduct(productId, this.dataset.productName, parseFloat(this.dataset.productPrice), parseInt(quantityInput.value));
                    } else {
                        quantityInput.disabled = true;
                        removeProduct(productId);
                    }
                    updateValidation();
                });
            });

            // Mudança de quantidade
            quantityInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const productId = this.dataset.productId;
                    const checkbox = document.querySelector(`input.product-checkbox[data-product-id="${productId}"]`);
                    
                    if (checkbox.checked) {
                        const quantity = parseInt(this.value) || 1;
                        const product = selectedProducts.get(productId);
                        if (product) {
                            product.quantity = quantity;
                            updateSelectedProductsList();
                            updateTotal();
                            updateHiddenInputs();
                        }
                    }
                });
            });

            // Seleção de cliente
            clienteSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    clienteCpf.textContent = selectedOption.dataset.cpf || '-';
                    clienteTelefone.textContent = selectedOption.dataset.telefone || '-';
                    clienteInfo.classList.remove('hidden');
                } else {
                    clienteInfo.classList.add('hidden');
                }
                updateValidation();
            });

            

            // Forma de pagamento
            formaPagamentoSelect.addEventListener('change', updateValidation);

            function addProduct(id, name, price, quantity) {
                selectedProducts.set(id, { id, name, price, quantity });
                updateSelectedProductsList();
                updateTotal();
                updateHiddenInputs();
            }

            function removeProduct(id) {
                selectedProducts.delete(id);
                updateSelectedProductsList();
                updateTotal();
                updateHiddenInputs();
            }

            function updateSelectedProductsList() {
                if (selectedProducts.size === 0) {
                    selectedProductsList.innerHTML = '<div class="text-sm text-gray-500 text-center py-4">Nenhum produto selecionado</div>';
                    return;
                }

                let html = '';
                selectedProducts.forEach(product => {
                    const subtotal = product.price * product.quantity;
                    html += `
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">${product.name}</div>
                                <div class="text-xs text-gray-500">${product.quantity}x R$ ${product.price.toFixed(2).replace('.', ',')}</div>
                            </div>
                            <div class="text-sm font-semibold text-green-600">
                                R$ ${subtotal.toFixed(2).replace('.', ',')}
                            </div>
                        </div>
                    `;
                });
                selectedProductsList.innerHTML = html;
            }

            function updateTotal() {
                let total = 0;
                selectedProducts.forEach(product => {
                    total += product.price * product.quantity;
                });
                totalVendaSpan.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            }

            function updateHiddenInputs() {
                hiddenProductInputs.innerHTML = '';
                let index = 0;
                selectedProducts.forEach(product => {
                    hiddenProductInputs.innerHTML += `
                        <input type="hidden" name="produtos[${index}][idProduto]" value="${product.id}">
                        <input type="hidden" name="produtos[${index}][quantidade]" value="${product.quantity}">
                    `;
                    index++;
                });
            }

            function updateValidation() {
                const hasProducts = selectedProducts.size > 0;
                const hasClient = clienteSelect.value !== '';
                const hasPayment = formaPagamentoSelect.value !== '';

                // Atualizar indicadores visuais
                updateValidationIndicator('validationProducts', hasProducts);
                updateValidationIndicator('validationClient', hasClient);
                updateValidationIndicator('validationPayment', hasPayment);

                // Habilitar/desabilitar botão
                const isValid = hasProducts && hasClient && hasPayment;
                btnRegistrarVenda.disabled = !isValid;
                
                if (isValid) {
                    btnRegistrarVenda.className = 'w-full bg-grip-1 hover:bg-grip-2 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200';
                } else {
                    btnRegistrarVenda.className = 'w-full bg-gray-400 text-white font-bold py-3 px-4 rounded-lg cursor-not-allowed transition-colors duration-200';
                }
            }

            function updateValidationIndicator(elementId, isValid) {
                const element = document.getElementById(elementId);
                if (isValid) {
                    element.className = 'flex items-center text-grip-1';
                    element.querySelector('svg').innerHTML = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>';
                } else {
                    element.className = 'flex items-center text-grip-2';
                    element.querySelector('svg').innerHTML = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>';
                }
            }

            // Validação inicial
            updateValidation();
        });
    </script>
@endsection
