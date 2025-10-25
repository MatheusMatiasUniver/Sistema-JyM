/**
 * Formatadores automáticos para CPF, CNPJ e telefone
 */

// Função para formatar CPF
function formatCPF(value) {
    // Remove tudo que não é dígito
    value = value.replace(/\D/g, '');
    
    // Aplica a formatação
    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }
    
    return value;
}

// Função para formatar CNPJ
function formatCNPJ(value) {
    // Remove tudo que não é dígito
    value = value.replace(/\D/g, '');
    
    // Aplica a formatação
    if (value.length <= 14) {
        value = value.replace(/(\d{2})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1/$2');
        value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    }
    
    return value;
}

// Função para formatar telefone
function formatTelefone(value) {
    // Remove tudo que não é dígito
    value = value.replace(/\D/g, '');
    
    // Aplica a formatação baseada no tamanho
    if (value.length <= 10) {
        // Telefone fixo: (XX) XXXX-XXXX
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4})(\d{1,4})$/, '$1-$2');
    } else if (value.length <= 11) {
        // Celular: (XX) XXXXX-XXXX
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
    }
    
    return value;
}

// Função para validar CPF
function validarCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    
    if (cpf.length !== 11) return false;
    
    // Verifica se todos os dígitos são iguais
    if (/^(\d)\1{10}$/.test(cpf)) return false;
    
    // Validação do primeiro dígito verificador
    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let resto = 11 - (soma % 11);
    let digito1 = resto < 2 ? 0 : resto;
    
    if (parseInt(cpf.charAt(9)) !== digito1) return false;
    
    // Validação do segundo dígito verificador
    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    resto = 11 - (soma % 11);
    let digito2 = resto < 2 ? 0 : resto;
    
    return parseInt(cpf.charAt(10)) === digito2;
}

// Função para validar CNPJ
function validarCNPJ(cnpj) {
    cnpj = cnpj.replace(/\D/g, '');
    
    if (cnpj.length !== 14) return false;
    
    // Verifica se todos os dígitos são iguais
    if (/^(\d)\1{13}$/.test(cnpj)) return false;
    
    // Validação do primeiro dígito verificador
    let tamanho = cnpj.length - 2;
    let numeros = cnpj.substring(0, tamanho);
    let digitos = cnpj.substring(tamanho);
    let soma = 0;
    let pos = tamanho - 7;
    
    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    
    let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado !== parseInt(digitos.charAt(0))) return false;
    
    // Validação do segundo dígito verificador
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    
    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    return resultado === parseInt(digitos.charAt(1));
}

// Função para aplicar formatação automática
function applyFormatting() {
    // Campos de CPF
    const cpfFields = document.querySelectorAll('input[name="cpf"], input[id="cpf"], input[id="cpfInput"]');
    cpfFields.forEach(field => {
        // Formatar valor existente ao carregar a página
        if (field.value) {
            field.value = formatCPF(field.value);
        }
        
        field.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const newValue = formatCPF(e.target.value);
            
            e.target.value = newValue;
            
            // Ajusta a posição do cursor
            if (newValue.length > oldValue.length) {
                e.target.setSelectionRange(cursorPosition + 1, cursorPosition + 1);
            } else {
                e.target.setSelectionRange(cursorPosition, cursorPosition);
            }
        });
        
        field.addEventListener('blur', function(e) {
            const cpf = e.target.value.replace(/\D/g, '');
            if (cpf.length > 0 && !validarCPF(cpf)) {
                e.target.classList.add('border-red-500');
                
                // Remove mensagem de erro anterior se existir
                const existingError = e.target.parentNode.querySelector('.cpf-error');
                if (existingError) {
                    existingError.remove();
                }
                
                // Adiciona mensagem de erro
                const errorMsg = document.createElement('p');
                errorMsg.className = 'text-red-500 text-xs italic cpf-error';
                errorMsg.textContent = 'CPF inválido';
                e.target.parentNode.appendChild(errorMsg);
            } else {
                e.target.classList.remove('border-red-500');
                const existingError = e.target.parentNode.querySelector('.cpf-error');
                if (existingError) {
                    existingError.remove();
                }
            }
        });
    });
    
    // Campos de CNPJ
    const cnpjFields = document.querySelectorAll('input[name="CNPJ"], input[id="CNPJ"], input[name="cnpj"], input[id="cnpj"]');
    cnpjFields.forEach(field => {
        // Formatar valor existente ao carregar a página
        if (field.value) {
            field.value = formatCNPJ(field.value);
        }
        
        field.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const newValue = formatCNPJ(e.target.value);
            
            e.target.value = newValue;
            
            // Ajusta a posição do cursor
            if (newValue.length > oldValue.length) {
                e.target.setSelectionRange(cursorPosition + 1, cursorPosition + 1);
            } else {
                e.target.setSelectionRange(cursorPosition, cursorPosition);
            }
        });
        
        field.addEventListener('blur', function(e) {
            const cnpj = e.target.value.replace(/\D/g, '');
            if (cnpj.length > 0 && !validarCNPJ(cnpj)) {
                e.target.classList.add('border-red-500');
                
                // Remove mensagem de erro anterior se existir
                const existingError = e.target.parentNode.querySelector('.cnpj-error');
                if (existingError) {
                    existingError.remove();
                }
                
                // Adiciona mensagem de erro
                const errorMsg = document.createElement('p');
                errorMsg.className = 'text-red-500 text-xs italic cnpj-error';
                errorMsg.textContent = 'CNPJ inválido';
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
    
    // Campos de telefone
    const telefoneFields = document.querySelectorAll('input[name="telefone"], input[id="telefone"]');
    telefoneFields.forEach(field => {
        // Formatar valor existente ao carregar a página
        if (field.value) {
            field.value = formatTelefone(field.value);
        }
        
        field.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const newValue = formatTelefone(e.target.value);
            
            e.target.value = newValue;
            
            // Ajusta a posição do cursor
            if (newValue.length > oldValue.length) {
                e.target.setSelectionRange(cursorPosition + 1, cursorPosition + 1);
            } else {
                e.target.setSelectionRange(cursorPosition, cursorPosition);
            }
        });
    });
}

// Inicializa a formatação quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', applyFormatting);

// Exporta as funções para uso em outros módulos
export { formatCPF, formatCNPJ, formatTelefone, validarCPF, validarCNPJ, applyFormatting };