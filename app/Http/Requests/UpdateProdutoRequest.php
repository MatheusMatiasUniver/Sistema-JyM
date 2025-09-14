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
            'nome' => ['required', 'string', 'max:100'],
            'categoria' => ['required', 'string', 'max:50'],
            'preco' => ['required', 'numeric', 'min:0'],
            'estoque' => ['required', 'integer', 'min:0'],
            'descricao' => ['nullable', 'string', 'max:500'],
            'imagem' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'remover_imagem' => ['nullable', 'boolean'],
            Rule::unique('produtos')->where(function ($query) {
                return $query->where('nome', $this->nome)
                             ->where('categoria', $this->categoria);
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
            'nome.required' => 'O campo Nome é obrigatório.',
            'categoria.required' => 'O campo Categoria é obrigatório.',
            'preco.required' => 'O campo Preço é obrigatório.',
            'preco.numeric' => 'O Preço deve ser um número.',
            'preco.min' => 'O Preço não pode ser negativo.',
            'estoque.required' => 'O campo Estoque é obrigatório.',
            'estoque.integer' => 'O Estoque deve ser um número inteiro.',
            'estoque.min' => 'O Estoque não pode ser negativo.',
            'imagem.image' => 'O arquivo da Imagem deve ser uma imagem.',
            'imagem.mimes' => 'A Imagem deve ser um arquivo dos tipos: jpeg, png, jpg, gif, svg.',
            'imagem.max' => 'A Imagem não pode ter mais de 2MB.',
            'unique' => 'Já existe um produto com o mesmo Nome e Categoria.',
        ];
    }
}