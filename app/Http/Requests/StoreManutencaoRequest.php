<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreManutencaoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (Auth::check()) {
            $user = Auth::user();
            return $user->nivelAcesso === 'Administrador' || $user->nivelAcesso === 'Funcionário';
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'idEquipamento' => [
                'required',
                'integer',
                Rule::exists('equipamentos', 'idEquipamento'),
            ],
            'tipo' => [
                'required',
                'string',
                Rule::in(['preventiva', 'corretiva']),
            ],
            'descricao' => [
                'required',
                'string',
                'max:2000',
            ],
            'dataProgramada' => [
                'nullable',
                'date',
            ],
            'fornecedorId' => [
                'nullable',
                'integer',
                Rule::exists('fornecedores', 'idFornecedor'),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'idEquipamento.required' => 'O equipamento é obrigatório.',
            'idEquipamento.exists' => 'Equipamento não encontrado.',
            'tipo.required' => 'O tipo de manutenção é obrigatório.',
            'tipo.in' => 'O tipo deve ser preventiva ou corretiva.',
            'descricao.required' => 'A descrição do problema é obrigatória.',
            'descricao.max' => 'A descrição deve ter no máximo 2000 caracteres.',
            'dataProgramada.date' => 'Data programada inválida.',
            'fornecedorId.exists' => 'Fornecedor não encontrado.',
        ];
    }
}
