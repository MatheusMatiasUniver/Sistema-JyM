<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdatePlanoAssinaturaRequest extends FormRequest
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
        return [
            'nome' => ['required', 'string', 'max:100'],
            'descricao' => ['nullable', 'string', 'max:500'],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'duracaoDias' => ['required', 'integer', 'min:1'],
            'idAcademia' => ['required', 'integer', Rule::exists('academias', 'idAcademia')],
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
            'nome.required' => 'O nome do plano é obrigatório.',
            'valor.required' => 'O valor do plano é obrigatório.',
            'valor.numeric' => 'O valor do plano deve ser um número.',
            'valor.min' => 'O valor do plano deve ser maior que zero.',
            'duracaoDias.required' => 'A duração do plano é obrigatória.',
            'duracaoDias.integer' => 'A duração do plano deve ser um número inteiro.',
            'duracaoDias.min' => 'A duração do plano deve ser de pelo menos 1 dia.',
            'idAcademia.required' => 'É obrigatório vincular o plano a uma academia.',
            'idAcademia.integer' => 'O ID da academia deve ser um número inteiro.',
            'idAcademia.exists' => 'A academia selecionada não existe.',
        ];
    }
}