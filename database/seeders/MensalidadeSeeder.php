<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mensalidade;
use App\Models\Cliente;
use Faker\Factory as Faker;
use Carbon\Carbon; // Importe a classe Carbon para manipulação de datas

class MensalidadeSeeder extends Seeder
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
            
            Mensalidade::create([
                'idCliente' => $cliente->idCliente,
                'dataVencimento' => $faker->dateTimeBetween('-3 months', '-2 months')->format('Y-m-d'),
                'valor' => 89.90,
                'status' => 'Paga',
                'dataPagamento' => $faker->dateTimeBetween('-2 months', '-1 month')->format('Y-m-d'),
            ]);
            
            $status = $faker->randomElement(['Pendente', 'Paga']);
            $dataVencimento = $faker->dateTimeBetween('-15 days', '+15 days');

            $dataPagamento = null;
            if ($status === 'Paga') {
                try {
                    $maxPaymentDate = Carbon::now();

                    $startPaymentRange = Carbon::instance($dataVencimento)->subDays($faker->numberBetween(0, 5));
                    $startPaymentRange = $startPaymentRange->lessThan(Carbon::now()->subMonths(3)) ? Carbon::now()->subMonths(3) : $startPaymentRange;

                    $dataPagamento = $faker->dateTimeBetween(
                        $startPaymentRange,
                        $maxPaymentDate
                    )->format('Y-m-d');

                } catch (\InvalidArgumentException $e) {
                    $dataPagamento = Carbon::instance($dataVencimento)->format('Y-m-d');
                    if (Carbon::parse($dataPagamento)->isFuture()) {
                        $dataPagamento = Carbon::now()->format('Y-m-d');
                    }
                }
            }

            Mensalidade::create([
                'idCliente' => $cliente->idCliente,
                'dataVencimento' => Carbon::instance($dataVencimento)->format('Y-m-d'), // Formata o objeto DateTime para string
                'valor' => 89.90,
                'status' => $status,
                'dataPagamento' => $dataPagamento,
            ]);

            
            for ($i = 0; $i < $faker->numberBetween(0, 2); $i++) {
                $statusRandom = $faker->randomElement(['Pendente', 'Paga']);
                $dataVencimentoRandom = $faker->dateTimeBetween('-2 months', '+3 months');
                $dataPagamentoRandom = null;

                if ($statusRandom === 'Paga') {
                    try {
                        $startPaymentRangeRandom = Carbon::instance($dataVencimentoRandom);
                        if ($startPaymentRangeRandom->isFuture()) {
                             $startPaymentRangeRandom = Carbon::now();
                        }
                        $dataPagamentoRandom = $faker->dateTimeBetween(
                            $startPaymentRangeRandom->subDays($faker->numberBetween(0, 10)),
                            Carbon::now()
                        )->format('Y-m-d');
                    } catch (\InvalidArgumentException $e) {
                        $dataPagamentoRandom = Carbon::now()->format('Y-m-d');
                    }
                }

                Mensalidade::create([
                    'idCliente' => $cliente->idCliente,
                    'dataVencimento' => Carbon::instance($dataVencimentoRandom)->format('Y-m-d'),
                    'valor' => $faker->randomFloat(2, 70, 150),
                    'status' => $statusRandom,
                    'dataPagamento' => $dataPagamentoRandom,
                ]);
            }
        }
    }
}