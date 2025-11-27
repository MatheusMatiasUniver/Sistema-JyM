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
                'max:255',
                Rule::unique('produtos', 'nome')->where(function ($query) {
                    return $query->where('idCategoria', $this->idCategoria);
                }),
            ],
            'idCategoria' => [
                'required',
                Rule::exists('categorias', 'idCategoria'),
            ],
            'idMarca' => [
                'required',
                Rule::exists('marcas', 'idMarca'),
            ],
            'idFornecedor' => [
                'nullable',
                Rule::exists('fornecedores', 'idFornecedor'),
            ],
            'preco' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
            'precoCompra' => 'nullable|numeric|min:0',
            'descricao' => 'nullable|string',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'nome.max' => 'O nome do produto não pode ter mais de 255 caracteres.',

            'idCategoria.required' => 'A categoria do produto é obrigatória.',
            'idCategoria.exists' => 'A categoria selecionada não existe.',

            'idMarca.required' => 'A marca do produto é obrigatória.',
            'idMarca.exists' => 'A marca selecionada não existe.',

            'idFornecedor.exists' => 'O fornecedor selecionado não existe.',

            'preco.required' => 'O preço do produto é obrigatório.',
            'preco.numeric' => 'O preço do produto deve ser um número.',
            'preco.min' => 'O preço do produto deve ser maior ou igual a zero.',

            'estoque.required' => 'O estoque do produto é obrigatório.',
            'estoque.integer' => 'O estoque do produto deve ser um número inteiro.',
            'estoque.min' => 'O estoque do produto não pode ser negativo.',

            'precoCompra.numeric' => 'O preço de compra deve ser um número.',
            'precoCompra.min' => 'O preço de compra deve ser maior ou igual a zero.',

            'descricao.string' => 'A descrição deve ser um texto.',

            'imagem.image' => 'O arquivo deve ser uma imagem.',
            'imagem.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif.',
            'imagem.max' => 'A imagem não pode ter mais de 2 MB (2048 KB).',
        ];
    }
}
