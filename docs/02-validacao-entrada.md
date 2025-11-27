# Validação de Entrada

Este documento define as práticas de validação de entrada que devem ser seguidas no desenvolvimento do Sistema JyM.

---

## Princípios Gerais

- **Valide todos os inputs** antes de processar qualquer dado.
- Retorne **erros de validação com status HTTP 400 (Bad Request)**.
- **Documente** todos os campos obrigatórios e seus tipos nas funções, endpoints e métodos.
- Não permita que dados inválidos passem despercebidos para a lógica de negócio.

---

## Implementação em Laravel

### Usando Form Requests

Crie classes de validação específicas para cada operação:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para a requisição.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'telefone' => 'required|string|max:20',
            'cpf' => 'required|string|size:11|unique:clientes,cpf',
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter 11 dígitos.',
        ];
    }
}
```

---

## Validação Manual em Controllers

Quando necessário, utilize validação manual:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'nome' => 'required|string|max:255',
        'email' => 'required|email',
        'valor' => 'required|numeric|min:0',
    ]);

    // Processar dados validados
    $cliente = Cliente::create($validated);

    return response()->json($cliente, 201);
}
```

---

## Tratamento de Erros

### Resposta de Erro Padrão (Status 400)

```json
{
    "message": "Os dados fornecidos são inválidos.",
    "errors": {
        "email": [
            "O campo email é obrigatório."
        ],
        "nome": [
            "O campo nome deve ter no máximo 255 caracteres."
        ]
    }
}
```

---

## Tipos de Validação Comuns

| Campo | Tipo | Regras Recomendadas |
|-------|------|---------------------|
| Nome | string | `required\|string\|max:255` |
| Email | string | `required\|email\|unique:table,column` |
| CPF | string | `required\|string\|size:11` |
| Telefone | string | `required\|string\|max:20` |
| Valor monetário | decimal | `required\|numeric\|min:0` |
| Data | date | `required\|date\|after:today` |
| ID de referência | integer | `required\|exists:table,id` |

---

## Boas Práticas

1. **Sempre valide antes de processar** - Nunca confie em dados do cliente.
2. **Use mensagens de erro claras** - Ajude o usuário a entender o que está errado.
3. **Documente os campos** - Indique quais campos são obrigatórios e seus formatos.
4. **Sanitize os dados** - Além de validar, limpe os dados de caracteres perigosos.
5. **Teste as validações** - Crie testes automatizados para as regras de validação.

---

## Referências

- [Laravel Validation](https://laravel.com/docs/validation)
- [Laravel Form Requests](https://laravel.com/docs/validation#form-request-validation)
