export function initVendasCreate() {
    document.addEventListener('DOMContentLoaded', function () {
        const produtosDataElement = document.getElementById('produtos-data');
        if (!produtosDataElement) return;

        const produtosData = JSON.parse(produtosDataElement.dataset.produtos);
        const produtosContainer = document.getElementById('produtos-container');
        const addProductBtn = document.getElementById('add-product-btn');
        const totalVendaSpan = document.getElementById('total-venda');
        let productIndex = parseInt(produtosDataElement.dataset.productIndex) || 0;

        function createProductItem() {
            const div = document.createElement('div');
            div.classList.add('flex', 'flex-col', 'sm:flex-row', 'items-start', 'sm:items-center', 'space-y-2', 'sm:space-y-0', 'sm:space-x-2', 'mb-4', 'p-3', 'border', 'rounded-lg', 'bg-gray-50', 'product-item');

            // Product select container
            const selectContainer = document.createElement('div');
            selectContainer.classList.add('w-full', 'sm:flex-grow');

            const selectLabel = document.createElement('label');
            selectLabel.classList.add('block', 'text-sm', 'font-medium', 'text-gray-700', 'mb-1', 'sm:hidden');
            selectLabel.textContent = 'Produto:';

            const select = document.createElement('select');
            select.name = `produtos[${productIndex}][idProduto]`;
            select.classList.add('shadow', 'border', 'rounded', 'py-2', 'px-3', 'text-gray-700', 'leading-tight', 'focus:outline-none', 'focus:shadow-outline', 'w-full', 'product-select');
            
            let defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Selecione um produto';
            select.appendChild(defaultOption);

            for (const id in produtosData) {
                if (produtosData.hasOwnProperty(id)) {
                    const prod = produtosData[id];
                    const option = document.createElement('option');
                    option.value = prod.idProduto;
                    option.textContent = `${prod.nome} (Estoque: ${prod.estoque})`;
                    option.dataset.preco = prod.preco;
                    option.dataset.estoque = prod.estoque;
                    select.appendChild(option);
                }
            }

            // Quantity container
            const quantityContainer = document.createElement('div');
            quantityContainer.classList.add('w-full', 'sm:w-24');

            const quantityLabel = document.createElement('label');
            quantityLabel.classList.add('block', 'text-sm', 'font-medium', 'text-gray-700', 'mb-1', 'sm:hidden');
            quantityLabel.textContent = 'Quantidade:';

            const quantityInput = document.createElement('input');
            quantityInput.type = 'number';
            quantityInput.name = `produtos[${productIndex}][quantidade]`;
            quantityInput.value = '1';
            quantityInput.min = '1';
            quantityInput.placeholder = 'Qtd';
            quantityInput.classList.add('shadow', 'border', 'rounded', 'py-2', 'px-3', 'text-gray-700', 'leading-tight', 'focus:outline-none', 'focus:shadow-outline', 'w-full', 'product-quantity');

            // Price container
            const priceContainer = document.createElement('div');
            priceContainer.classList.add('w-full', 'sm:w-28');

            const priceLabel = document.createElement('label');
            priceLabel.classList.add('block', 'text-sm', 'font-medium', 'text-gray-700', 'mb-1', 'sm:hidden');
            priceLabel.textContent = 'Preço:';

            const priceSpan = document.createElement('span');
            priceSpan.classList.add('product-price', 'font-semibold', 'text-lg', 'text-green-600', 'block');
            priceSpan.textContent = 'R$ 0,00';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.classList.add('bg-red-500', 'hover:bg-red-700', 'text-white', 'font-bold', 'py-2', 'px-3', 'rounded', 'remove-product-btn', 'w-full', 'sm:w-auto');
            
            const removeBtnTextMobile = document.createElement('span');
            removeBtnTextMobile.classList.add('sm:hidden');
            removeBtnTextMobile.textContent = 'Remover Produto';
            
            const removeBtnTextDesktop = document.createElement('span');
            removeBtnTextDesktop.classList.add('hidden', 'sm:inline');
            removeBtnTextDesktop.textContent = '-';
            
            removeBtn.appendChild(removeBtnTextMobile);
            removeBtn.appendChild(removeBtnTextDesktop);
            removeBtn.addEventListener('click', function () {
                div.remove();
                updateTotal();
            });

            select.addEventListener('change', function() {
                const selectedOption = select.options[select.selectedIndex];
                const preco = parseFloat(selectedOption.dataset.preco || 0);
                const estoque = parseInt(selectedOption.dataset.estoque || 0);
                
                priceSpan.textContent = 'R$ ' + preco.toFixed(2).replace('.', ',');
                quantityInput.max = estoque;
                if (parseInt(quantityInput.value) > estoque) {
                    quantityInput.value = estoque;
                }
                calculateTotal();
            });

            quantityInput.addEventListener('input', calculateTotal);
            quantityInput.addEventListener('change', function() {
                const val = parseInt(quantityInput.value);
                const min = parseInt(quantityInput.min);
                const max = parseInt(quantityInput.max);
                if (val < min || isNaN(val)) {
                    quantityInput.value = min;
                } else if (val > max) {
                    quantityInput.value = max;
                }
                calculateTotal();
            });

            // Assemble containers
            selectContainer.appendChild(selectLabel);
            selectContainer.appendChild(select);

            quantityContainer.appendChild(quantityLabel);
            quantityContainer.appendChild(quantityInput);

            priceContainer.appendChild(priceLabel);
            priceContainer.appendChild(priceSpan);

            div.appendChild(selectContainer);
            div.appendChild(quantityContainer);
            div.appendChild(priceContainer);
            div.appendChild(removeBtn);

            productIndex++;
            return div;
        }

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.product-item').forEach(item => {
                const select = item.querySelector('.product-select');
                const quantityInput = item.querySelector('.product-quantity');
                const selectedOption = select.options[select.selectedIndex];
                
                const preco = parseFloat(selectedOption.dataset.preco || 0);
                const quantidade = parseInt(quantityInput.value || 0);
                
                total += preco * quantidade;
            });
            totalVendaSpan.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
        }

        if (addProductBtn) {
            addProductBtn.addEventListener('click', function () {
                produtosContainer.appendChild(createProductItem());
                calculateTotal();
            });
        }

        document.querySelectorAll('.remove-product-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                btn.closest('.product-item').remove();
                calculateTotal();
            });
        });

        document.querySelectorAll('.product-select').forEach(select => {
            select.addEventListener('change', function() {
                const selectedOption = select.options[select.selectedIndex];
                const preco = parseFloat(selectedOption.dataset.preco || 0);
                const estoque = parseInt(selectedOption.dataset.estoque || 0);
                
                const item = select.closest('.product-item');
                const priceElement = item.querySelector('.product-price');
                const quantityElement = item.querySelector('.product-quantity');
                
                if (priceElement) {
                    priceElement.textContent = 'R$ ' + preco.toFixed(2).replace('.', ',');
                }
                if (quantityElement) {
                    quantityElement.max = estoque;
                }
                calculateTotal();
            });
        });

        document.querySelectorAll('.product-quantity').forEach(input => {
            input.addEventListener('input', calculateTotal);
            input.addEventListener('change', function() {
                const val = parseInt(input.value);
                const min = parseInt(input.min);
                const max = parseInt(input.max);
                if (val < min || isNaN(val)) {
                    input.value = min;
                } else if (val > max) {
                    input.value = max;
                }
                calculateTotal();
            });
        });

        calculateTotal();
    });
}