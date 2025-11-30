<?php

namespace Database\Factories;

use App\Models\Compra;
use App\Models\Academia;
use App\Models\Fornecedor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Compra>
 */
class CompraFactory extends Factory
{
    protected $model = Compra::class;

    public function definition(): array
    {
        $valorProdutos = fake()->randomFloat(2, 500, 10000);
        $valorFrete = fake()->randomFloat(2, 0, 200);
        $valorDesconto = fake()->randomFloat(2, 0, $valorProdutos * 0.1);
        $valorImpostos = round($valorProdutos * 0.08, 2);
        $valorTotal = $valorProdutos + $valorFrete + $valorImpostos - $valorDesconto;

        return [
            'idAcademia' => Academia::factory(),
            'idFornecedor' => Fornecedor::factory(),
            'dataEmissao' => fake()->dateTimeBetween('-90 days', 'now'),
            'status' => fake()->randomElement(['aberta', 'recebida', 'cancelada']),
            'valorProdutos' => $valorProdutos,
            'valorFrete' => $valorFrete,
            'valorDesconto' => $valorDesconto,
            'valorImpostos' => $valorImpostos,
            'valorTotal' => $valorTotal,
            'observacoes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function aberta(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'aberta',
        ]);
    }

    public function recebida(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'recebida',
        ]);
    }

    public function cancelada(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelada',
        ]);
    }
}
