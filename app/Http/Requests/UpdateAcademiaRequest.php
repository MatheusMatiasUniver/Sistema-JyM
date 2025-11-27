<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateAcademiaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && $user->isAdministrador();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $academiaId = $this->route('academia')->idAcademia;

        return [
            'nome' => ['required', 'string', 'max:100'],
            'CNPJ' => ['required', 'string', 'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/', Rule::unique('academias', 'CNPJ')->ignore($academiaId, 'idAcademia')],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'responsavel' => ['required', 'string', 'max:100'],
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
            'nome.required' => 'O nome da academia é obrigatório.',
            'CNPJ.required' => 'O CNPJ é obrigatório.',
            'CNPJ.regex' => 'O CNPJ deve estar no formato XX.XXX.XXX/YYYY-ZZ.',
            'CNPJ.unique' => 'Já existe outra academia cadastrada com este CNPJ.',
            'email.email' => 'O e-mail deve ser um endereço de e-mail válido.',
            'responsavel.required' => 'O nome do responsável é obrigatório.',
        ];
    }
}