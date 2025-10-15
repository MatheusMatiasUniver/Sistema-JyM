<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateClienteRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta solicitação.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Regras de validação para a atualização de um cliente.
     */
    public function rules(): array
    {
        $clienteId = $this->route('cliente')->idCliente;

        return [
            'nome' => ['required', 'string', 'max:100'],
            'cpf' => [
                'required',
                'string',
                'digits:11',
                'regex:/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/',
                Rule::unique('clientes', 'cpf')->ignore($clienteId, 'idCliente'),
            ],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('clientes', 'email')->ignore($clienteId, 'idCliente')],
            'telefone' => ['nullable', 'string', 'max:15'],
            'dataNascimento' => ['required', 'date', 'before:today'],
            'status' => ['required', Rule::in(['Ativo', 'Inativo'])],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'remover_foto' => ['nullable', 'boolean'],
            'idPlano' => ['nullable', 'integer', Rule::exists('plano_assinaturas', 'idPlano')],
            'codigo_acesso' => 'nullable|sometimes|string|min:6|max:255',
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O campo Nome é obrigatório.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.digits' => 'O CPF deve conter exatamente 11 dígitos numéricos.',
            'cpf.unique' => 'Este CPF já está cadastrado para outro cliente.',
            'email.email' => 'O formato do E-mail é inválido.',
             'email.unique' => 'O e-mail informado já está em uso.',
            'telefone.min' => 'O Telefone deve ter no mínimo 8 dígitos.',
            'telefone.max' => 'O Telefone deve ter no máximo 15 dígitos.',
            'dataNascimento.required' => 'A Data de Nascimento é obrigatória.',
            'dataNascimento.date' => 'A Data de Nascimento deve ser uma data válida.',
            'dataNascimento.before' => 'A Data de Nascimento deve ser anterior à data atual.',
            'status.required' => 'O campo Status é obrigatório.',
            'status.in' => 'O Status deve ser Ativo ou Inativo.',
            'foto.image' => 'O arquivo enviado para Foto deve ser uma imagem.',
            'foto.mimes' => 'A Foto deve ser dos tipos: jpeg, png, jpg, gif ou svg.',
            'foto.max' => 'A Foto não pode ser maior que 2MB.',
            'idPlano.integer' => 'O Plano de Assinatura selecionado é inválido.',
            'idPlano.exists' => 'O Plano de Assinatura selecionado não existe.',
            'codigo_acesso.digits' => 'O Código de Acesso deve ter exatamente :digits dígitos.',
            'codigo_acesso.numeric' => 'O Código de Acesso deve conter apenas números.',
        ];
    }
}