<?php

namespace App\Services;

use App\Models\VendaProduto;
use App\Models\Produto;
use App\Models\ItemVenda;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Exception;

class VendaService
{
    /**
     * Registra uma nova venda e atualiza o estoque dos produtos.
     *
     * @param array $data Dados da venda (idCliente, formaPagamento, produtos)
     *        'produtos' é um array de objetos/arrays: [['idProduto' => 1, 'quantidade' => 2, 'precoUnitario' => 10.50], ...]
     * @return VendaProduto
     * @throws \Exception
     */
    public function registrarVenda(array $data): VendaProduto
    {
        DB::beginTransaction();
        try {
            $totalVenda = 0;
            $itensVenda = [];

            foreach ($data['produtos'] as $produtoData) {
                $produto = Produto::find($produtoData['idProduto']);

                if (!$produto) {
                    throw new \Exception("Produto com ID {$produtoData['idProduto']} não encontrado.");
                }

                if ($produto->estoque < $produtoData['quantidade']) {
                    throw new \Exception("Estoque insuficiente para o produto {$produto->nome}. Disponível: {$produto->estoque}, Solicitado: {$produtoData['quantidade']}.");
                }

                $precoUnitario = $produto->preco;
                $subtotal = $precoUnitario * $produtoData['quantidade'];
                $totalVenda += $subtotal;

                $itensVenda[] = [
                    'idProduto' => $produto->idProduto,
                    'quantidade' => $produtoData['quantidade'],
                    'precoUnitario' => $precoUnitario,
                ];
            }

            $venda = VendaProduto::create([
                'idCliente' => $data['idCliente'],
                'dataVenda' => now(),
                'valorTotal' => $totalVenda,
                'formaPagamento' => $data['formaPagamento'],
            ]);

            foreach ($itensVenda as $itemData) {
                $venda->itensVenda()->create($itemData);

                Produto::where('idProduto', $itemData['idProduto'])
                       ->decrement('estoque', $itemData['quantidade']);
            }

            DB::commit();
            return $venda;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao registrar venda: " . $e->getMessage(), ['venda_data' => $data]);
            throw new \Exception("Falha ao registrar venda: " . $e->getMessage());
        }
    }
}