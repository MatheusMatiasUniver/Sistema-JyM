<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // [LIMPEZA DE DADOS - PRÉ-VALIDAÇÃO]
        // Este método é executado AUTOMATICAMENTE pelo Laravel ANTES das regras de validação (rules()).
        // Aqui removemos a máscara (pontos e traços) para que o banco receba apenas os números.
        
        if ($this->has('cpf')) {
            $this->merge([
                // preg_replace remove tudo que NÃO for número (0-9).
                // Ex: Transforma "123.456.789-00" em "12345678900"
                'cpf' => preg_replace('/[^0-9]/', '', $this->cpf)
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'nome' => ['required', 'string', 'max:100'],
            'cpf' => [
                'required',
                'string',
                'size:11',
                'regex:/^[0-9]{11}$/',
                Rule::unique('clientes', 'cpf'),
            ],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('clientes', 'email')],
            'telefone' => ['nullable', 'string', 'max:15'],
            'dataNascimento' => ['required', 'date', 'before:today'],
            'status' => ['required', 'in:Ativo,Inativo'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'idPlano' => ['nullable', 'integer', Rule::exists('plano_assinaturas', 'idPlano')],
            
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O campo Nome é obrigatório.',
            'nome.max' => 'O Nome não pode ter mais de :max caracteres.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'cpf.regex' => 'O CPF deve conter apenas números.',
            'cpf.size' => 'O CPF deve ter exatamente 11 dígitos.',
            'email.unique' => 'O e-mail informado já está em uso.',
            'dataNascimento.required' => 'A Data de Nascimento é obrigatória.',
            'dataNascimento.date' => 'A Data de Nascimento deve ser uma data válida.',
            'dataNascimento.before' => 'A Data de Nascimento não pode ser futura.',
            'status.required' => 'O campo Status é obrigatório.',
            'status.in' => 'O Status deve ser Ativo ou Inativo.',
            'foto.image' => 'O arquivo da Foto deve ser uma imagem.',
            'foto.mimes' => 'A Foto deve ser um arquivo dos tipos: jpeg, png, jpg, gif, svg.',
            'foto.max' => 'A Foto não pode ter mais de 2MB.',
            
        ];
    }
}