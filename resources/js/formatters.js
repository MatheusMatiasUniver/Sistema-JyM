let __formattersInitialized = false;

// [FUNÇÃO DE FORMATAÇÃO DE CPF]
// Chamada por: bindWithFormatter (dentro do evento 'input')
// Objetivo: Receber uma string suja (ex: "12345678901") e retornar formatada (ex: "123.456.789-01")
export function formatCPF(value) {
    // Se não houver valor, retorna vazio para não quebrar a regex
    if (!value) return '';
    
    // 1. Remove tudo que não é dígito (0-9)
    // \D significa "não-dígito". A flag 'g' significa "global" (todos os caracteres)
    const cpf = value.replace(/\D/g, '');
    
    // 2. Aplica a máscara usando Grupos de Captura ($1, $2, etc)
    // (\d{3}) -> Captura os primeiros 3 dígitos ($1)
    // (\d{3}) -> Captura os próximos 3 dígitos ($2)
    // (\d{3}) -> Captura os próximos 3 dígitos ($3)
    // (\d{2}) -> Captura os últimos 2 dígitos ($4)
    // Retorno: $1.$2.$3-$4 (ex: 123.456.789-01)
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

export function initFormatters() {
    // [PASSO 1 - INICIALIZAÇÃO]
    // Verifica se os formatadores já foram iniciados para evitar duplicação de eventos.
    // Se __formattersInitialized for true, a função para aqui.
    if (__formattersInitialized) return;
    __formattersInitialized = true;

    // [PASSO 2 - DEFINIÇÃO DO VINCULADOR]
    // Esta função interna é responsável por conectar um input HTML a uma função de formatação específica (ex: formatCPF).
    const bindWithFormatter = (input, formatter) => {
        
        // [PASSO 3 - ESCUTA DE EVENTOS]
        // Adiciona um 'ouvinte' para o evento 'input'.
        // Esse evento é disparado IMEDIATAMENTE após o usuário digitar, colar ou apagar qualquer caractere.
        input.addEventListener('input', function(e) {
            
            // [PASSO 4 - CAPTURA DO ESTADO ATUAL]
            // cursorPosition: Onde o cursor está AGORA, antes da formatação alterar o texto.
            const cursorPosition = e.target.selectionStart;
            
            // oldValue: O valor bruto que acabou de ser digitado (ex: "123.456.789-01a").
            const oldValue = e.target.value;
            
            // [PASSO 5 - CÁLCULO DA POSIÇÃO LÓGICA]
            // Calcula quantos DÍGITOS numéricos existem antes do cursor.
            // Isso é necessário porque a formatação vai inserir/remover pontos e traços, mudando as posições absolutas.
            const digitIndex = getDigitIndexAtPosition(oldValue, cursorPosition);
            
            // [PASSO 6 - APLICAÇÃO DA MÁSCARA]
            // Chama a função formatadora (ex: formatCPF) que limpa caracteres inválidos e aplica a pontuação.
            // Ex: transforma "12345678901" em "123.456.789-01"
            const newValue = formatter(e.target.value);
            
            // [PASSO 7 - ATUALIZAÇÃO DO DOM]
            // Substitui o valor do input pelo valor formatado. O usuário vê a mudança instantaneamente.
            e.target.value = newValue;
            
            // [PASSO 8 - REPOSICIONAMENTO DO CURSOR]
            // Calcula onde o cursor deve ficar no novo texto formatado, baseando-se na contagem de dígitos (digitIndex).
            const caretPos = findCaretPositionForDigitIndex(newValue, digitIndex);
            
            // Move o cursor para a posição correta, garantindo que a digitação continue fluida.
            e.target.setSelectionRange(caretPos, caretPos);
        });

        // [PASSO 9 - FORMATAÇÃO INICIAL]
        // Se o input já tiver um valor (ex: vindo do banco de dados na edição), aplica a máscara imediatamente.
        if (input.value) {
            input.value = formatter(input.value);
        }
        
        // Marca o input para indicar que ele já possui uma máscara ativa.
        input.dataset.boundMask = 'true';
    };

    // [PASSO 10 - SELEÇÃO DE ELEMENTOS]
    // Busca todos os inputs que devem receber a máscara de CPF.
    // Procura por atributo data-format, ID ou name.
    const cpfInputs = [
        ...document.querySelectorAll('[data-format="cpf"]'),
        ...document.querySelectorAll('#cpf'),
        ...document.querySelectorAll('input[name="cpf"]')
    ];
    // Aplica o vinculador para cada input de CPF encontrado.
    cpfInputs.forEach(input => bindWithFormatter(input, formatCPF));

    const cnpjInputs = [
        ...document.querySelectorAll('[data-format="cnpj"]'),
        ...document.querySelectorAll('#CNPJ'),
        ...document.querySelectorAll('input[name="CNPJ"]')
    ];
    cnpjInputs.forEach(input => bindWithFormatter(input, formatCNPJ));

    const telefoneInputs = [
        ...document.querySelectorAll('[data-format="telefone"]'),
        ...document.querySelectorAll('#telefone'),
        ...document.querySelectorAll('input[name="telefone"]')
    ];
    telefoneInputs.forEach(input => bindWithFormatter(input, formatTelefone));
}

export function applyFormatting() {
    initFormatters();
}

document.addEventListener('DOMContentLoaded', initFormatters);