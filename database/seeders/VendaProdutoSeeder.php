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

        $produto1_1 = $produtosAcademia1->first();
        $produto1_2 = $produtosAcademia1->skip(1)->first() ?? $produto1_1;
        
        $venda1 = DB::table('venda_produtos')->insertGetId([
            'idCliente' => 1,
            'idUsuario' => 2,
            'dataVenda' => Carbon::now()->subDays(5),
            'valorTotal' => $produto1_1->preco + $produto1_2->preco,
            'formaPagamento' => 'Dinheiro',
            'idAcademia' => 1,
        ]);

        DB::table('itens_vendas')->insert([
            [
                'idVenda' => $venda1,
                'idProduto' => $produto1_1->idProduto,
                'quantidade' => 1,
                'precoUnitario' => $produto1_1->preco,
            ],
            [
                'idVenda' => $venda1,
                'idProduto' => $produto1_2->idProduto,
                'quantidade' => 1,
                'precoUnitario' => $produto1_2->preco,
            ],
        ]);

        // Venda 2 - Academia 1
        $produto2_1 = $produtosAcademia1->skip(2)->first() ?? $produto1_1;
        
        $venda2 = DB::table('venda_produtos')->insertGetId([
            'idCliente' => 2,
            'idUsuario' => 2,
            'dataVenda' => Carbon::now()->subDays(3),
            'valorTotal' => $produto2_1->preco,
            'formaPagamento' => 'PIX',
            'idAcademia' => 1,
        ]);

        DB::table('itens_vendas')->insert([
            [
                'idVenda' => $venda2,
                'idProduto' => $produto2_1->idProduto,
                'quantidade' => 1,
                'precoUnitario' => $produto2_1->preco,
            ],
        ]);

        // Venda 3 - Academia 2
        $produto3_1 = $produtosAcademia2->first();
        $produto3_2 = $produtosAcademia2->skip(1)->first() ?? $produto3_1;
        
        $venda3 = DB::table('venda_produtos')->insertGetId([
            'idCliente' => 5,
            'idUsuario' => 3,
            'dataVenda' => Carbon::now()->subDays(2),
            'valorTotal' => $produto3_1->preco + $produto3_2->preco,
            'formaPagamento' => 'Cartão de Débito',
            'idAcademia' => 2,
        ]);

        DB::table('itens_vendas')->insert([
            [
                'idVenda' => $venda3,
                'idProduto' => $produto3_1->idProduto,
                'quantidade' => 1,
                'precoUnitario' => $produto3_1->preco,
            ],
            [
                'idVenda' => $venda3,
                'idProduto' => $produto3_2->idProduto,
                'quantidade' => 1,
                'precoUnitario' => $produto3_2->preco,
            ],
        ]);

        $produto4_1 = $produtosAcademia2->skip(2)->first() ?? $produto3_1;
        $produto4_2 = $produtosAcademia2->skip(3)->first() ?? $produto3_1;
        
        $venda4 = DB::table('venda_produtos')->insertGetId([
            'idCliente' => 6,
            'idUsuario' => 3,
            'dataVenda' => Carbon::now()->subDays(1),
            'valorTotal' => $produto4_1->preco + $produto4_2->preco,
            'formaPagamento' => 'PIX',
            'idAcademia' => 2,
        ]);

        DB::table('itens_vendas')->insert([
            [
                'idVenda' => $venda4,
                'idProduto' => $produto4_1->idProduto,
                'quantidade' => 1,
                'precoUnitario' => $produto4_1->preco,
            ],
            [
                'idVenda' => $venda4,
                'idProduto' => $produto4_2->idProduto,
                'quantidade' => 1,
                'precoUnitario' => $produto4_2->preco,
            ],
        ]);
    }
}