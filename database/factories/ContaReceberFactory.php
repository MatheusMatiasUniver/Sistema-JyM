<?php

namespace Database\Factories;

use App\Models\ContaReceber;
use App\Models\Academia;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContaReceber>
 */
class ContaReceberFactory extends Factory
{
    protected $model = ContaReceber::class;

    public function definition(): array
    {
        $recebida = fake()->boolean(50);
        $dataVencimento = fake()->dateTimeBetween('-15 days', '+45 days');

        return [
            'idAcademia' => Academia::factory(),
            'idCliente' => Cliente::factory(),
            'documentoRef' => fake()->optional(0.6)->numberBetween(1000, 9999),
            'descricao' => fake()->randomElement([
                'Mensalidade',
                'Serviço avulso',
                'Personal trainer',
                'Avaliação física',
                'Locação de armário',
            ]),
            'valorTotal' => fake()->randomFloat(2, 49.90, 299.90),
            'status' => $recebida ? 'recebida' : 'aberta',
            'dataVencimento' => $dataVencimento->format('Y-m-d'),
            'dataRecebimento' => $recebida ? Carbon::parse($dataVencimento)->subDays(fake()->numberBetween(0, 3))->format('Y-m-d') : null,
            'formaRecebimento' => $recebida ? fake()->randomElement(['Dinheiro', 'PIX', 'Cartão de Crédito', 'Cartão de Débito']) : null,
        ];
    }

    public function recebida(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'recebida',
                'dataRecebimento' => Carbon::parse($attributes['dataVencimento'])->subDays(fake()->numberBetween(0, 3))->format('Y-m-d'),
                'formaRecebimento' => fake()->randomElement(['Dinheiro', 'PIX', 'Cartão de Crédito', 'Cartão de Débito']),
            ];
        });
    }

    public function aberta(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'aberta',
            'dataRecebimento' => null,
            'formaRecebimento' => null,
        ]);
    }
}
