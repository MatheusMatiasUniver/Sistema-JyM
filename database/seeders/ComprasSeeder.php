<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Produto;
use App\Models\Fornecedor;

class ComprasSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([1, 2] as $idAcademia) {
            $fornecedores = Fornecedor::where('idAcademia', $idAcademia)->get();
            $produtos = Produto::where('idAcademia', $idAcademia)->get();
            if ($fornecedores->isEmpty() || $produtos->isEmpty()) {
                continue;
            }

            $qtdCompras = random_int(8, 16);
            for ($c = 0; $c < $qtdCompras; $c++) {
                $fornecedor = $fornecedores->random();
                $dataEmissao = Carbon::today()->subDays(random_int(5, 90))->setTime(random_int(9, 18), [0, 30][array_rand([0,1])]);
                $itensCount = random_int(1, 5);
                $valorProdutos = 0;
                $itens = [];
                for ($i = 0; $i < $itensCount; $i++) {
                    $produto = $produtos->random();
                    $qtde = random_int(5, 40);
                    $precoUnit = $produto->precoCompra ?? $produto->custoMedio ?? ($produto->preco * 0.7);
                    $valorProdutos += $qtde * $precoUnit;
                    $itens[] = [
                        'idProduto' => $produto->idProduto,
                        'quantidade' => $qtde,
                        'precoUnitario' => $precoUnit,
                        'descontoPercent' => 0,
                        'custoRateadoTotal' => null,
                    ];
                }

                $valorFrete = round($valorProdutos * 0.03, 2);
                $valorDesconto = round($valorProdutos * 0.02, 2);
                $valorImpostos = round($valorProdutos * 0.08, 2);
                $valorTotal = round($valorProdutos + $valorFrete + $valorImpostos - $valorDesconto, 2);

                $statusCompra = ['aberta','recebida'][array_rand([0,1])];
                $idCompra = DB::table('compras')->insertGetId([
                    'idAcademia' => $idAcademia,
                    'idFornecedor' => $fornecedor->idFornecedor,
                    'dataEmissao' => $dataEmissao,
                    'status' => $statusCompra,
                    'valorProdutos' => round($valorProdutos, 2),
                    'valorFrete' => $valorFrete,
                    'valorDesconto' => $valorDesconto,
                    'valorImpostos' => $valorImpostos,
                    'valorTotal' => $valorTotal,
                    'observacoes' => null,
                ]);

                foreach ($itens as $item) {
                    DB::table('itens_compras')->insert([
                        'idCompra' => $idCompra,
                        'idProduto' => $item['idProduto'],
                        'quantidade' => $item['quantidade'],
                        'precoUnitario' => $item['precoUnitario'],
                        'descontoPercent' => $item['descontoPercent'],
                        'custoRateadoTotal' => $item['custoRateadoTotal'],
                    ]);
                    DB::table('produtos')->where('idProduto', $item['idProduto'])->increment('estoque', $item['quantidade']);
                }

                $statusConta = ['aberta','paga'][array_rand([0,1])];
                $dataVencimento = $dataEmissao->copy()->addDays(random_int(10, 30))->toDateString();
                $dataPagamento = $statusConta === 'paga' ? $dataEmissao->copy()->addDays(random_int(5, 20))->toDateString() : null;
                $formaPagamento = $statusConta === 'paga' ? (['PIX','Boleto','Transferência Bancária'][array_rand([0,1,2])]) : null;

                $idCategoria = DB::table('categorias_contas_pagar')
                    ->where('idAcademia', $idAcademia)
                    ->where('nome', 'Compra de produtos')
                    ->value('idCategoriaContaPagar');

                if (!$idCategoria) {
                    $idCategoria = DB::table('categorias_contas_pagar')->insertGetId([
                        'idAcademia' => $idAcademia,
                        'nome' => 'Compra de produtos',
                        'ativa' => true,
                    ]);
                }

                DB::table('contas_pagar')->insert([
                    'idAcademia' => $idAcademia,
                    'idFornecedor' => $fornecedor->idFornecedor,
                    'idCategoriaContaPagar' => $idCategoria,
                    'documentoRef' => $idCompra,
                    'descricao' => 'Compra #'.$idCompra,
                    'valorTotal' => $valorTotal,
                    'status' => $statusConta,
                    'dataVencimento' => $dataVencimento,
                    'dataPagamento' => $dataPagamento,
                    'formaPagamento' => $formaPagamento,
                ]);
            }
        }
    }
}

