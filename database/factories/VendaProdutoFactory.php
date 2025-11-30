<?php

namespace Database\Factories;

use App\Models\VendaProduto;
use App\Models\Cliente;
use App\Models\Academia;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VendaProduto>
 */
class VendaProdutoFactory extends Factory
{
    protected $model = VendaProduto::class;

    public function definition(): array
    {
        return [
            'idCliente' => Cliente::factory(),
            'idAcademia' => Academia::factory(),
            'idUsuario' => User::factory(),
            'dataHora' => fake()->dateTimeBetween('-90 days', 'now'),
            'valorTotal' => fake()->randomFloat(2, 10, 500),
            'formaPagamento' => fake()->randomElement(['Dinheiro', 'PIX', 'Cartão de Débito', 'Cartão de Crédito']),
            'status' => fake()->randomElement(['Concluída', 'Concluída', 'Concluída', 'Cancelada']),
        ];
    }

    public function concluida(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Concluída',
        ]);
    }

    public function cancelada(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Cancelada',
        ]);
    }
}
