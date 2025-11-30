function initCNPJMask() {
    const cnpjInputs = document.querySelectorAll('input[name="CNPJ"]');
    
    cnpjInputs.forEach(input => {
        if (input.dataset.boundMask === 'true') {
            return;
        }
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length > 14) {
                value = value.slice(0, 14);
            }
            
            if (value.length > 0) {
                value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            }
            
            e.target.value = value;
        });

        input.addEventListener('blur', function(e) {
            const value = e.target.value;
            const cnpjRegex = /^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/;
            
            if (value && !cnpjRegex.test(value)) {
                e.target.classList.add('border-red-500');
                
                const existingError = e.target.parentNode.querySelector('.cnpj-error');
                if (existingError) {
                    existingError.remove();
                }
                
                const errorMsg = document.createElement('p');
                errorMsg.className = 'text-red-500 text-xs italic cnpj-error';
                errorMsg.textContent = 'CNPJ deve estar no formato XX.XXX.XXX/YYYY-ZZ';
                e.target.parentNode.appendChild(errorMsg);
            } else {
                e.target.classList.remove('border-red-500');
                
                const existingError = e.target.parentNode.querySelector('.cnpj-error');
                if (existingError) {
                    existingError.remove();
                }
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initCNPJMask);

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initCNPJMask };
}