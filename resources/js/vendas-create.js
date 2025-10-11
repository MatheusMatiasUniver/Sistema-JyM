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
            div.classList.add('flex', 'items-center', 'space-x-2', 'mb-2', 'product-item');

            const select = document.createElement('select');
            select.name = `produtos[${productIndex}][idProduto]`;
            select.classList.add('shadow', 'border', 'rounded', 'py-2', 'px-3', 'text-gray-700', 'leading-tight', 'focus:outline-none', 'focus:shadow-outline', 'flex-grow', 'product-select');
            
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

            const quantityInput = document.createElement('input');
            quantityInput.type = 'number';
            quantityInput.name = `produtos[${productIndex}][quantidade]`;
            quantityInput.value = '1';
            quantityInput.min = '1';
            quantityInput.placeholder = 'Qtd';
            quantityInput.classList.add('shadow', 'border', 'rounded', 'py-2', 'px-3', 'text-gray-700', 'leading-tight', 'focus:outline-none', 'focus:shadow-outline', 'w-20', 'product-quantity');

            const priceSpan = document.createElement('span');
            priceSpan.classList.add('product-price', 'font-semibold', 'w-24', 'text-right');
            priceSpan.textContent = 'R$ 0,00';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.classList.add('bg-red-500', 'hover:bg-red-700', 'text-white', 'font-bold', 'py-1', 'px-2', 'rounded', 'remove-product-btn');
            removeBtn.textContent = '-';
            removeBtn.addEventListener('click', function () {
                div.remove();
                calculateTotal();
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

            div.appendChild(select);
            div.appendChild(quantityInput);
            div.appendChild(priceSpan);
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