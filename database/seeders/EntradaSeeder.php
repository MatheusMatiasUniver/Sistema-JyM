<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Entrada;
use App\Models\Cliente;
use Faker\Factory as Faker;

class EntradaSeeder extends Seeder
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
            for ($i = 0; $i < $faker->numberBetween(2, 10); $i++) {
                Entrada::create([
                    'idCliente' => $cliente->idCliente,
                    'dataHora' => $faker->dateTimeBetween('-1 month', 'now'),
                    'metodo' => $faker->randomElement(['Reconhecimento Facial', 'CPF/Senha', 'Manual']),
                ]);
            }
        }
    }
}