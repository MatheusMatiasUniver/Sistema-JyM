<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VendaProduto;
use App\Models\Cliente;
use Faker\Factory as Faker;

class VendaProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $clientes = Cliente::all();

        if ($clientes->isEmpty()) {
            $this->call(ClienteSeeder::class);
            $clientes = Cliente::all();
        }

        foreach ($clientes as $cliente) {
            for ($i = 0; $i < $faker->numberBetween(0, 3); $i++) {
                VendaProduto::create([
                    'idCliente' => $cliente->id,
                    'dataVenda' => $faker->dateTimeBetween('-6 months', 'now'),
                    'valorTotal' => $faker->randomFloat(2, 20, 500),
                    'tipoPagamento' => $faker->randomElement(['Cartão de Crédito', 'Dinheiro', 'Pix', 'Débito']),
                ]);
            }
        }
    }
}