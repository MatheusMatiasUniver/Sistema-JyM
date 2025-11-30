<?php

namespace Database\Factories;

use App\Models\Mensalidade;
use App\Models\Cliente;
use App\Models\PlanoAssinatura;
use App\Models\Academia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mensalidade>
 */
class MensalidadeFactory extends Factory
{
    protected $model = Mensalidade::class;

    public function definition(): array
    {
        $paga = fake()->boolean(70);
        $dataVencimento = fake()->dateTimeBetween('-30 days', '+30 days');

        return [
            'idCliente' => Cliente::factory(),
            'idPlano' => PlanoAssinatura::factory(),
            'idAcademia' => Academia::factory(),
            'dataVencimento' => $dataVencimento->format('Y-m-d'),
            'valor' => fake()->randomFloat(2, 79.90, 999.90),
            'status' => $paga ? 'Paga' : 'Pendente',
            'dataPagamento' => $paga ? Carbon::parse($dataVencimento)->subDays(fake()->numberBetween(1, 10))->format('Y-m-d') : null,
            'formaPagamento' => $paga ? fake()->randomElement(['Dinheiro', 'PIX', 'Cartão de Crédito', 'Cartão de Débito', 'Boleto']) : null,
        ];
    }

    public function paga(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Paga',
            'dataPagamento' => Carbon::parse($attributes['dataVencimento'])->subDays(fake()->numberBetween(1, 10))->format('Y-m-d'),
            'formaPagamento' => fake()->randomElement(['Dinheiro', 'PIX', 'Cartão de Crédito', 'Cartão de Débito', 'Boleto']),
        ]);
    }

    public function pendente(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Pendente',
            'dataPagamento' => null,
            'formaPagamento' => null,
        ]);
    }
}
