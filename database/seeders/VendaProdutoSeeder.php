<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Produto;

class VendaProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $produtosAcademia1 = Produto::where('idAcademia', 1)->get();
        $produtosAcademia2 = Produto::where('idAcademia', 2)->get();

        if ($produtosAcademia1->isEmpty() || $produtosAcademia2->isEmpty()) {
            $this->command->info('Produtos insuficientes para criar vendas. Execute o ProdutoSeeder primeiro.');
            return;
        }

        $clientesAtivos1 = DB::table('clientes')->where('idAcademia', 1)->whereIn('status', ['Ativo','Inadimplente'])->pluck('idCliente')->all();
        $clientesAtivos2 = DB::table('clientes')->where('idAcademia', 2)->whereIn('status', ['Ativo','Inadimplente'])->pluck('idCliente')->all();

        $usuarios1 = DB::table('users')->where('idAcademia', 1)->pluck('idUsuario')->all();
        $usuarios2 = DB::table('users')->where('idAcademia', 2)->pluck('idUsuario')->all();
        $adminId = DB::table('users')->where('nivelAcesso', 'Administrador')->value('idUsuario');
        if (empty($usuarios1)) { $usuarios1 = $adminId ? [$adminId] : [null]; }
        if (empty($usuarios2)) { $usuarios2 = $adminId ? [$adminId] : [null]; }

        $formas = ['Dinheiro','PIX','Cartão de Débito','Cartão de Crédito'];
        $inicio = Carbon::today()->subDays(90);
        $fim = Carbon::today();

        $dia = $inicio->copy();
        while ($dia->lte($fim)) {
            foreach ([1, 2] as $idAcademia) {
                $produtos = $idAcademia === 1 ? $produtosAcademia1 : $produtosAcademia2;
                $clientes = $idAcademia === 1 ? $clientesAtivos1 : $clientesAtivos2;
                $usuarios = $idAcademia === 1 ? $usuarios1 : $usuarios2;
                if (empty($clientes)) { continue; }

                $qtdVendas = random_int(2, 10);
                for ($v = 0; $v < $qtdVendas; $v++) {
                    $idCliente = $clientes[array_rand($clientes)];
                    $idUsuario = $usuarios[array_rand($usuarios)] ?? null;
                    $hora = random_int(9, 21);
                    $min = [0,10,20,30,40,50][array_rand([0,1,2,3,4,5])];
                    $dataVenda = $dia->copy()->setTime($hora, $min);
                    $itensCount = random_int(1, 4);
                    $itens = [];
                    $valorTotal = 0;
                    for ($i = 0; $i < $itensCount; $i++) {
                        $produto = $produtos->random();
                        $qtde = random_int(1, 3);
                        $preco = $produto->preco;
                        $valorTotal += $qtde * $preco;
                        $itens[] = [
                            'idProduto' => $produto->idProduto,
                            'quantidade' => $qtde,
                            'precoUnitario' => $preco,
                        ];
                    }

                    $idVenda = DB::table('venda_produtos')->insertGetId([
                        'idCliente' => $idCliente,
                        'idUsuario' => $idUsuario,
                        'dataVenda' => $dataVenda,
                        'valorTotal' => round($valorTotal, 2),
                        'formaPagamento' => $formas[array_rand($formas)],
                        'idAcademia' => $idAcademia,
                    ]);

                    foreach ($itens as $item) {
                        DB::table('itens_vendas')->insert([
                            'idVenda' => $idVenda,
                            'idProduto' => $item['idProduto'],
                            'quantidade' => $item['quantidade'],
                            'precoUnitario' => $item['precoUnitario'],
                        ]);
                    }
                }
            }
            $dia->addDay();
        }
    }
}
