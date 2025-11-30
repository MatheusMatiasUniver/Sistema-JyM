<?php

namespace Database\Factories;

use App\Models\ContaPagar;
use App\Models\Academia;
use App\Models\Fornecedor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContaPagar>
 */
class ContaPagarFactory extends Factory
{
    protected $model = ContaPagar::class;

    public function definition(): array
    {
        $paga = fake()->boolean(60);
        $dataVencimento = fake()->dateTimeBetween('-30 days', '+60 days');

        return [
            'idAcademia' => Academia::factory(),
            'idFornecedor' => Fornecedor::factory(),
            'documentoRef' => fake()->optional(0.7)->numberBetween(1000, 9999),
            'descricao' => fake()->randomElement([
                'Compra de suplementos',
                'Manutenção de equipamentos',
                'Material de limpeza',
                'Energia elétrica',
                'Aluguel',
                'Água',
                'Internet',
            ]),
            'valorTotal' => fake()->randomFloat(2, 100, 5000),
            'status' => $paga ? 'paga' : 'aberta',
            'dataVencimento' => $dataVencimento->format('Y-m-d'),
            'dataPagamento' => $paga ? Carbon::parse($dataVencimento)->subDays(fake()->numberBetween(0, 5))->format('Y-m-d') : null,
            'formaPagamento' => $paga ? fake()->randomElement(['Dinheiro', 'PIX', 'Boleto', 'Transferência']) : null,
        ];
    }

    public function paga(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'paga',
                'dataPagamento' => Carbon::parse($attributes['dataVencimento'])->subDays(fake()->numberBetween(0, 5))->format('Y-m-d'),
                'formaPagamento' => fake()->randomElement(['Dinheiro', 'PIX', 'Boleto', 'Transferência']),
            ];
        });
    }

    public function aberta(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'aberta',
            'dataPagamento' => null,
            'formaPagamento' => null,
        ]);
    }
}
