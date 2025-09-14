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

        // Garante que existam clientes antes de tentar criar mensalidades para eles
        if ($clientes->isEmpty()) {
            $this->call(ClienteSeeder::class); // Chama o ClienteSeeder se não houver clientes
            $clientes = Cliente::all(); // Recarrega os clientes após o seeder
        }

        foreach ($clientes as $cliente) {
            // --- Mensalidade Paga Antiga (para garantir histórico) ---
            Mensalidade::create([
                'idCliente' => $cliente->idCliente, // Usar idCliente conforme o Model
                'dataVencimento' => $faker->dateTimeBetween('-3 months', '-2 months')->format('Y-m-d'),
                'valor' => 89.90,
                'status' => 'Paga',
                'dataPagamento' => $faker->dateTimeBetween('-2 months', '-1 month')->format('Y-m-d'),
            ]);

            // --- Mensalidade do Mês Atual (Pendente ou Paga) ---
            $status = $faker->randomElement(['Pendente', 'Paga']);
            // A data de vencimento pode ser no passado (vencida) ou no futuro
            $dataVencimento = $faker->dateTimeBetween('-15 days', '+15 days'); // Retorna um objeto DateTime

            $dataPagamento = null;
            if ($status === 'Paga') {
                // A data de pagamento deve ser entre a data de vencimento ou a data atual (o que for maior)
                // e a data atual, ou um pouco depois da data de vencimento se for um pagamento adiantado.

                try {
                    // Garante que o pagamento não seja depois de hoje (para 'Paga')
                    $maxPaymentDate = Carbon::now();

                    // Se a data de vencimento for no futuro, a data de pagamento pode ser entre agora e a data de vencimento
                    // Se a data de vencimento for no passado, a data de pagamento deve ser entre a data de vencimento e agora
                    $startPaymentRange = Carbon::instance($dataVencimento)->subDays($faker->numberBetween(0, 5)); // Começa alguns dias antes do vencimento
                    $startPaymentRange = $startPaymentRange->lessThan(Carbon::now()->subMonths(3)) ? Carbon::now()->subMonths(3) : $startPaymentRange; // Limita o início muito antigo

                    $dataPagamento = $faker->dateTimeBetween(
                        $startPaymentRange, // Início do período de pagamento
                        $maxPaymentDate      // Fim do período de pagamento (hoje)
                    )->format('Y-m-d');

                } catch (\InvalidArgumentException $e) {
                    // Fallback se o intervalo for inválido (pode acontecer com datas muito passadas ou futuras extremas)
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

            // --- Mais algumas mensalidades aleatórias (Pendente ou Paga, futuras/passadas) ---
            for ($i = 0; $i < $faker->numberBetween(0, 2); $i++) {
                $statusRandom = $faker->randomElement(['Pendente', 'Paga']);
                $dataVencimentoRandom = $faker->dateTimeBetween('-2 months', '+3 months'); // Retorna um objeto DateTime
                $dataPagamentoRandom = null;

                if ($statusRandom === 'Paga') {
                    try {
                        // Garante que a data de pagamento não seja no futuro para status 'Paga'
                        $startPaymentRangeRandom = Carbon::instance($dataVencimentoRandom);
                        if ($startPaymentRangeRandom->isFuture()) {
                             $startPaymentRangeRandom = Carbon::now(); // Se o vencimento é futuro, pague a partir de hoje
                        }
                        // Garante que o pagamento seja no passado ou hoje
                        $dataPagamentoRandom = $faker->dateTimeBetween(
                            $startPaymentRangeRandom->subDays($faker->numberBetween(0, 10)), // Início (até 10 dias antes do vencimento)
                            Carbon::now() // Fim (até a data atual)
                        )->format('Y-m-d');
                    } catch (\InvalidArgumentException $e) {
                        // Fallback
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