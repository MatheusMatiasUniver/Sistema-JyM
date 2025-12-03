<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->idUsuario;

        return [
            'nome' => ['required', 'string', 'max:100'],
            'usuario' => ['required', 'string', 'max:50', Rule::unique('users', 'usuario')->ignore($userId, 'idUsuario')],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users', 'email')->ignore($userId, 'idUsuario')],
            'senha' => ['nullable', 'string', 'min:6'],
            'nivelAcesso' => ['required', 'in:Administrador,Funcionario'],
            'idAcademia' => ['required', 'exists:academias,idAcademia'],
            'salarioMensal' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'usuario.required' => 'O usuário é obrigatório.',
            'usuario.unique' => 'Este usuário já está em uso.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'senha.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'nivelAcesso.required' => 'O nível de acesso é obrigatório.',
            'nivelAcesso.in' => 'Nível de acesso inválido.',
            'idAcademia.required' => 'A academia é obrigatória.',
            'idAcademia.exists' => 'Academia não encontrada.',
            'salarioMensal.numeric' => 'O salário deve ser um número.',
            'salarioMensal.min' => 'O salário não pode ser negativo.',
        ];
    }
}
