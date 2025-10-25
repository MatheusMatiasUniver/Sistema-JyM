<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreProdutoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
            'nome' => [
                'required',
                'string',
                'max:100',
                Rule::unique('produtos')->where(function ($query) {
                    return $query->where('categoria', $this->categoria);
                })
            ],
            'categoria' => [
                'required',
                'string',
                'max:50',
                Rule::unique('produtos')->where(function ($query) {
                    return $query->where('nome', $this->nome);
                })
            ],
            'preco' => 'required|numeric|min:0.01',
            'estoque' => 'required|integer|min:0',
            'descricao' => 'nullable|string',
            'imagem' => 'nullable|image|max:2048',
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
            'nome.required' => 'O nome do produto é obrigatório.',
            'nome.unique' => 'Já existe um produto com este nome nesta categoria.',
            'nome.max' => 'O nome do produto não pode ter mais de 100 caracteres.',

            'categoria.required' => 'A categoria do produto é obrigatória.',
            'categoria.unique' => 'Já existe um produto com esta categoria e nome.',
            'categoria.max' => 'A categoria do produto não pode ter mais de 50 caracteres.',

            'preco.required' => 'O preço do produto é obrigatório.',
            'preco.numeric' => 'O preço do produto deve ser um número.',
            'preco.min' => 'O preço do produto deve ser maior que zero.',

            'estoque.required' => 'O estoque do produto é obrigatório.',
            'estoque.integer' => 'O estoque do produto deve ser um número inteiro.',
            'estoque.min' => 'O estoque do produto não pode ser negativo.',

            'descricao.string' => 'A descrição deve ser um texto.',

            'imagem.image' => 'O arquivo deve ser uma imagem.',
            'imagem.max' => 'A imagem não pode ter mais de 2 MB (2048 KB).',
        ];
    }
}
