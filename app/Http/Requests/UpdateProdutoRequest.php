<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateProdutoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && ($user->isAdministrador() || $user->isFuncionario());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $produtoId = $this->route('produto')->idProduto;

        return [
            'nome' => ['required', 'string', 'max:255'],
            'idCategoria' => ['required', Rule::exists('categorias', 'idCategoria')],
            'idMarca' => ['required', Rule::exists('marcas', 'idMarca')],
            'idFornecedor' => ['nullable', Rule::exists('fornecedores', 'idFornecedor')],
            'preco' => ['required', 'numeric', 'min:0'],
            'estoque' => ['required', 'integer', 'min:0'],
            'precoCompra' => ['nullable', 'numeric', 'min:0'],
            'descricao' => ['nullable', 'string'],
            'imagem' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'remover_imagem' => ['nullable', 'boolean'],
            Rule::unique('produtos', 'nome')->where(function ($query) {
                return $query->where('idCategoria', $this->idCategoria);
            })->ignore($produtoId, 'idProduto'),
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
            'idCategoria.required' => 'A categoria é obrigatória.',
            'idCategoria.exists' => 'A categoria selecionada não existe.',
            'idMarca.required' => 'A marca é obrigatória.',
            'idMarca.exists' => 'A marca selecionada não existe.',
            'idFornecedor.exists' => 'O fornecedor selecionado não existe.',
            'preco.required' => 'O preço é obrigatório.',
            'preco.numeric' => 'O preço deve ser um número.',
            'preco.min' => 'O preço deve ser maior ou igual a zero.',
            'estoque.required' => 'O estoque é obrigatório.',
            'estoque.integer' => 'O estoque deve ser um número inteiro.',
            'estoque.min' => 'O estoque deve ser maior ou igual a zero.',
            'precoCompra.numeric' => 'O preço de compra deve ser um número.',
            'precoCompra.min' => 'O preço de compra deve ser maior ou igual a zero.',
            'imagem.image' => 'O arquivo deve ser uma imagem.',
            'imagem.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif.',
            'imagem.max' => 'A imagem não pode ser maior que 2MB.',
            'unique' => 'Já existe um produto com este nome nesta categoria.',
        ];
    }
}