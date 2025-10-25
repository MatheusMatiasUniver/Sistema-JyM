// CNPJ Mask functionality
function initCNPJMask() {
    const cnpjInputs = document.querySelectorAll('input[name="CNPJ"]');
    
    cnpjInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            // Limit to 14 digits
            if (value.length > 14) {
                value = value.slice(0, 14);
            }
            
            // Apply CNPJ mask: XX.XXX.XXX/YYYY-ZZ
            if (value.length > 0) {
                value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            }
            
            e.target.value = value;
        });

        // Validate CNPJ format on blur
        input.addEventListener('blur', function(e) {
            const value = e.target.value;
            const cnpjRegex = /^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/;
            
            if (value && !cnpjRegex.test(value)) {
                e.target.classList.add('border-red-500');
                
                // Remove existing error message
                const existingError = e.target.parentNode.querySelector('.cnpj-error');
                if (existingError) {
                    existingError.remove();
                }
                
                // Add error message
                const errorMsg = document.createElement('p');
                errorMsg.className = 'text-red-500 text-xs italic cnpj-error';
                errorMsg.textContent = 'CNPJ deve estar no formato XX.XXX.XXX/YYYY-ZZ';
                e.target.parentNode.appendChild(errorMsg);
            } else {
                e.target.classList.remove('border-red-500');
                
                // Remove error message
                const existingError = e.target.parentNode.querySelector('.cnpj-error');
                if (existingError) {
                    existingError.remove();
                }
            }
        });
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initCNPJMask);

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initCNPJMask };
}