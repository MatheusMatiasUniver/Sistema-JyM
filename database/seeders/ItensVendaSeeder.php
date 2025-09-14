<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ItemVenda;
use App\Models\VendaProduto;
use App\Models\Produto;
use Faker\Factory as Faker;

class ItensVendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $vendas = VendaProduto::all();
        $produtos = Produto::all();

        if ($vendas->isEmpty()) {
            $this->call(VendaProdutoSeeder::class); // Garante que Vendas existam
            $vendas = VendaProduto::all();
        }
        if ($produtos->isEmpty()) {
            $this->call(ProdutoSeeder::class); // Garante que Produtos existam
            $produtos = Produto::all();
        }

        if ($produtos->isEmpty()) {
            $this->command->info('Nenhum produto encontrado. Pulando ItensVendaSeeder.');
            return;
        }

        foreach ($vendas as $venda) {
            $numItens = $faker->numberBetween(1, 3);
            for ($i = 0; $i < $numItens; $i++) {
                $produto = $produtos->random();
                $quantidade = $faker->numberBetween(1, 5);

                ItemVenda::create([
                    'idVenda' => $venda->idVenda,
                    'idProduto' => $produto->idProduto,
                    'quantidade' => $quantidade,
                    'precoUnitario' => $produto->preco, // Usa o preço do produto no momento da venda
                ]);
            }
        }
    }
}