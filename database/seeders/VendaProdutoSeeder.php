<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendaProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $venda1 = DB::table('venda_produtos')->insertGetId([
            'idCliente' => 1,
            'idUsuario' => 2,
            'dataVenda' => Carbon::now()->subDays(5),
            'valorTotal' => 98.40,
            'formaPagamento' => 'Dinheiro',
            'idAcademia' => 1,
        ]);

        DB::table('itens_vendas')->insert([
            [
                'idVenda' => $venda1,
                'idProduto' => 1,
                'quantidade' => 1,
                'precoUnitario' => 89.90,
            ],
            [
                'idVenda' => $venda1,
                'idProduto' => 3,
                'quantidade' => 1,
                'precoUnitario' => 8.50,
            ],
        ]);

        $venda2 = DB::table('venda_produtos')->insertGetId([
            'idCliente' => 2,
            'idUsuario' => 2,
            'dataVenda' => Carbon::now()->subDays(3),
            'valorTotal' => 59.90,
            'formaPagamento' => 'PIX',
            'idAcademia' => 1,
        ]);

        DB::table('itens_vendas')->insert([
            [
                'idVenda' => $venda2,
                'idProduto' => 2,
                'quantidade' => 1,
                'precoUnitario' => 59.90,
            ],
        ]);

        $venda3 = DB::table('venda_produtos')->insertGetId([
            'idCliente' => 5,
            'idUsuario' => 3,
            'dataVenda' => Carbon::now()->subDays(2),
            'valorTotal' => 91.80,
            'formaPagamento' => 'Cartão de Débito',
            'idAcademia' => 2,
        ]);

        DB::table('itens_vendas')->insert([
            [
                'idVenda' => $venda3,
                'idProduto' => 6,
                'quantidade' => 1,
                'precoUnitario' => 85.90,
            ],
            [
                'idVenda' => $venda3,
                'idProduto' => 9,
                'quantidade' => 1,
                'precoUnitario' => 25.00,
            ],
        ]);

        $venda4 = DB::table('venda_produtos')->insertGetId([
            'idCliente' => 6,
            'idUsuario' => 3,
            'dataVenda' => Carbon::now()->subDays(1),
            'valorTotal' => 52.40,
            'formaPagamento' => 'PIX',
            'idAcademia' => 2,
        ]);

        DB::table('itens_vendas')->insert([
            [
                'idVenda' => $venda4,
                'idProduto' => 7,
                'quantidade' => 1,
                'precoUnitario' => 45.90,
            ],
            [
                'idVenda' => $venda4,
                'idProduto' => 8,
                'quantidade' => 1,
                'precoUnitario' => 6.50,
            ],
        ]);
    }
}