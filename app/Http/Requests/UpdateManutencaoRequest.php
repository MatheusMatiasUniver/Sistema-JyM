<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateManutencaoRequest extends FormRequest
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
            'dataExecucao' => [
                'required',
                'date',
            ],
            'servicoRealizado' => [
                'required',
                'string',
                'max:2000',
            ],
            'custo' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'responsavel' => [
                'required',
                'string',
                'max:100',
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
            'dataExecucao.required' => 'A data de execução é obrigatória.',
            'dataExecucao.date' => 'Data de execução inválida.',
            'servicoRealizado.required' => 'A descrição do serviço realizado é obrigatória.',
            'servicoRealizado.max' => 'O serviço realizado deve ter no máximo 2000 caracteres.',
            'custo.numeric' => 'O custo deve ser um valor numérico.',
            'custo.min' => 'O custo não pode ser negativo.',
            'responsavel.required' => 'O responsável técnico é obrigatório.',
            'responsavel.max' => 'O responsável deve ter no máximo 100 caracteres.',
            'fornecedorId.exists' => 'Fornecedor não encontrado.',
        ];
    }
}
