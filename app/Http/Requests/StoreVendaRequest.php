<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Produto;

class StoreVendaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
        $rules = [
            'idCliente' => ['required', 'exists:clientes,idCliente'],
            'tipoPagamento' => ['required', 'string', 'in:Dinheiro,Cartão de Crédito,Cartão de Débito,Pix'],
            'produtos' => ['required', 'array', 'min:1'],
            'produtos.*.idProduto' => ['required', 'exists:produtos,idProduto'],
            'produtos.*.quantidade' => ['required', 'integer', 'min:1'],
        ];

        foreach ($this->input('produtos', []) as $key => $produtoData) {
            if (isset($produtoData['idProduto']) && isset($produtoData['quantidade'])) {
                $produto = Produto::find($produtoData['idProduto']);
                if ($produto) {
                    $rules["produtos.{$key}.quantidade"][] = "max:{$produto->estoque}";
                }
            }
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'idCliente.required' => 'O cliente é obrigatório.',
            'idCliente.exists' => 'O cliente selecionado não existe.',
            'tipoPagamento.required' => 'O tipo de pagamento é obrigatório.',
            'tipoPagamento.in' => 'O tipo de pagamento selecionado é inválido.',
            'produtos.required' => 'É necessário adicionar pelo menos um produto à venda.',
            'produtos.array' => 'Os produtos devem ser enviados em formato de lista.',
            'produtos.min' => 'É necessário adicionar pelo menos um produto à venda.',
            'produtos.*.idProduto.required' => 'O ID do produto é obrigatório.',
            'produtos.*.idProduto.exists' => 'Um dos produtos selecionados não existe.',
            'produtos.*.quantidade.required' => 'A quantidade do produto é obrigatória.',
            'produtos.*.quantidade.integer' => 'A quantidade do produto deve ser um número inteiro.',
            'produtos.*.quantidade.min' => 'A quantidade do produto deve ser de pelo menos 1.',
            'produtos.*.quantidade.max' => 'Estoque insuficiente para um dos produtos.',
        ];
    }
}