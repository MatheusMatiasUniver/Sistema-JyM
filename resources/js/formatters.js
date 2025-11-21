let __formattersInitialized = false;

export function formatCPF(value) {
    if (!value) return '';
    
    const cpf = value.replace(/\D/g, '');
    
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
}

export function formatCNPJ(value) {
    if (!value) return '';
    
    const cnpj = value.replace(/\D/g, '');
    
    return cnpj.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
}

export function formatTelefone(value) {
    if (!value) return '';
    
    const telefone = value.replace(/\D/g, '');
    
    if (telefone.length <= 10) {
        return telefone.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    } else {
        return telefone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    }
}

function getDigitIndexAtPosition(text, pos) {
    const slice = text.slice(0, pos);
    const match = slice.match(/\d/g);
    return match ? match.length : 0;
}

function findCaretPositionForDigitIndex(masked, digitIndex) {
    if (digitIndex <= 0) {
        for (let i = 0; i < masked.length; i++) {
            if (/\d/.test(masked.charAt(i))) return i;
        }
        return masked.length;
    }
    let count = 0;
    for (let i = 0; i < masked.length; i++) {
        if (/\d/.test(masked.charAt(i))) {
            count++;
            if (count === digitIndex) return i + 1;
        }
    }
    return masked.length;
}

export function validarCPF(cpf) {
    if (!cpf) return false;
    
    cpf = cpf.replace(/\D/g, '');
    
    if (cpf.length !== 11) return false;
    
    if (/^(\d)\1{10}$/.test(cpf)) return false;
    
    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let resto = 11 - (soma % 11);
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(9))) return false;
    
    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    resto = 11 - (soma % 11);
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(10))) return false;
    
    return true;
}

export function validarCNPJ(cnpj) {
    if (!cnpj) return false;
    
    cnpj = cnpj.replace(/\D/g, '');
    
    if (cnpj.length !== 14) return false;
    
    if (/^(\d)\1{13}$/.test(cnpj)) return false;
    
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
    
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    
    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado !== parseInt(digitos.charAt(1))) return false;
    
    return true;
}

export function initFormatters() {
    // Prevent multiple initializations
    if (__formattersInitialized) return;
    __formattersInitialized = true;

    const bindWithFormatter = (input, formatter) => {
        input.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const digitIndex = getDigitIndexAtPosition(oldValue, cursorPosition);
            const newValue = formatter(e.target.value);
            e.target.value = newValue;
            const caretPos = findCaretPositionForDigitIndex(newValue, digitIndex);
            e.target.setSelectionRange(caretPos, caretPos);
        });
        if (input.value) {
            input.value = formatter(input.value);
        }
        input.dataset.boundMask = 'true';
    };

    const bindCPF = (input) => bindWithFormatter(input, formatCPF);

    const cpfInputs = [
        ...document.querySelectorAll('[data-format="cpf"]'),
        ...document.querySelectorAll('#cpf'),
        ...document.querySelectorAll('input[name="cpf"]')
    ];
    cpfInputs.forEach(bindCPF);

    const cnpjInputs = [
        ...document.querySelectorAll('[data-format="cnpj"]'),
        ...document.querySelectorAll('#CNPJ'),
        ...document.querySelectorAll('input[name="CNPJ"]')
    ];
    cnpjInputs.forEach(input => bindWithFormatter(input, formatCNPJ));

    const bindTelefone = (input) => bindWithFormatter(input, formatTelefone);

    const telefoneInputs = [
        ...document.querySelectorAll('[data-format="telefone"]'),
        ...document.querySelectorAll('#telefone'),
        ...document.querySelectorAll('input[name="telefone"]')
    ];
    telefoneInputs.forEach(bindTelefone);
}

document.addEventListener('DOMContentLoaded', initFormatters);

export function applyFormatting() {
    initFormatters();
}