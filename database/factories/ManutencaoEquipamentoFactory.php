<?php

namespace Database\Factories;

use App\Models\ManutencaoEquipamento;
use App\Models\Equipamento;
use App\Models\Fornecedor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ManutencaoEquipamento>
 */
class ManutencaoEquipamentoFactory extends Factory
{
    protected $model = ManutencaoEquipamento::class;

    public function definition(): array
    {
        $executada = fake()->boolean(60);
        $dataProgramada = fake()->dateTimeBetween('-60 days', '+30 days');

        return [
            'idEquipamento' => Equipamento::factory(),
            'tipo' => fake()->randomElement(['preventiva', 'corretiva']),
            'dataProgramada' => $dataProgramada->format('Y-m-d'),
            'dataExecucao' => $executada ? $dataProgramada->format('Y-m-d') : null,
            'custo' => $executada ? fake()->randomFloat(2, 100, 2000) : null,
            'fornecedorId' => Fornecedor::factory(),
            'observacoes' => fake()->optional(0.5)->sentence(),
        ];
    }

    public function preventiva(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo' => 'preventiva',
        ]);
    }

    public function corretiva(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo' => 'corretiva',
        ]);
    }

    public function executada(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'dataExecucao' => $attributes['dataProgramada'],
                'custo' => fake()->randomFloat(2, 100, 2000),
            ];
        });
    }
}
