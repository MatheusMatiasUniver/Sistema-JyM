<?php

namespace Database\Factories;

use App\Models\Equipamento;
use App\Models\Academia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipamento>
 */
class EquipamentoFactory extends Factory
{
    protected $model = Equipamento::class;

    public function definition(): array
    {
        $equipamentos = [
            ['descricao' => 'Esteira Elétrica Profissional', 'fabricante' => 'Technogym', 'modelo' => 'Skillrun', 'valor' => 45000.00],
            ['descricao' => 'Bicicleta Ergométrica Vertical', 'fabricante' => 'Life Fitness', 'modelo' => 'Integrity Upright', 'valor' => 25000.00],
            ['descricao' => 'Elíptico Profissional', 'fabricante' => 'Precor', 'modelo' => 'EFX 885', 'valor' => 38000.00],
            ['descricao' => 'Power Rack Profissional', 'fabricante' => 'Hammer Strength', 'modelo' => 'HD Elite', 'valor' => 18000.00],
            ['descricao' => 'Leg Press 45°', 'fabricante' => 'Technogym', 'modelo' => 'Selection Pro', 'valor' => 22000.00],
            ['descricao' => 'Supino Reto', 'fabricante' => 'Life Fitness', 'modelo' => 'Signature Series', 'valor' => 12000.00],
            ['descricao' => 'Puxador Alto', 'fabricante' => 'Hammer Strength', 'modelo' => 'MTS Pulldown', 'valor' => 15000.00],
            ['descricao' => 'Cross Cable', 'fabricante' => 'Technogym', 'modelo' => 'Element+', 'valor' => 35000.00],
            ['descricao' => 'Hack Squat', 'fabricante' => 'Life Fitness', 'modelo' => 'Hammer Strength', 'valor' => 16000.00],
            ['descricao' => 'Smith Machine', 'fabricante' => 'Precor', 'modelo' => 'Discovery Series', 'valor' => 20000.00],
            ['descricao' => 'Remo Baixo', 'fabricante' => 'Technogym', 'modelo' => 'Selection Pro', 'valor' => 14000.00],
            ['descricao' => 'Cadeira Extensora', 'fabricante' => 'Life Fitness', 'modelo' => 'Optima Series', 'valor' => 11000.00],
            ['descricao' => 'Mesa Flexora', 'fabricante' => 'Hammer Strength', 'modelo' => 'Select', 'valor' => 11500.00],
            ['descricao' => 'Cadeira Adutora/Abdutora', 'fabricante' => 'Technogym', 'modelo' => 'Selection', 'valor' => 10000.00],
            ['descricao' => 'Banco Regulável', 'fabricante' => 'Life Fitness', 'modelo' => 'Signature', 'valor' => 3500.00],
        ];

        $equip = fake()->randomElement($equipamentos);
        $dataAquisicao = fake()->dateTimeBetween('-5 years', '-6 months');
        $garantiaMeses = fake()->numberBetween(12, 36);

        return [
            'descricao' => $equip['descricao'],
            'fabricante' => $equip['fabricante'],
            'modelo' => $equip['modelo'],
            'numeroSerie' => strtoupper(fake()->bothify('??-##-????-###')),
            'dataAquisicao' => $dataAquisicao->format('Y-m-d'),
            'valorAquisicao' => $equip['valor'],
            'garantiaFim' => (clone $dataAquisicao)->modify("+{$garantiaMeses} months")->format('Y-m-d'),
            'centroCusto' => 'Academia',
            'status' => fake()->randomElement(['Ativo', 'Ativo', 'Ativo', 'Em Manutenção', 'Desativado']),
            'idAcademia' => Academia::factory(),
        ];
    }

    public function ativo(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Ativo',
        ]);
    }

    public function emManutencao(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Em Manutenção',
        ]);
    }

    public function desativado(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Desativado',
        ]);
    }
}
