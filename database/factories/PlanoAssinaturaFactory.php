<?php

namespace Database\Factories;

use App\Models\PlanoAssinatura;
use App\Models\Academia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlanoAssinatura>
 */
class PlanoAssinaturaFactory extends Factory
{
    protected $model = PlanoAssinatura::class;

    public function definition(): array
    {
        $planos = [
            ['nome' => 'Mensal Básico', 'duracaoDias' => 30, 'valor' => 89.90],
            ['nome' => 'Mensal Premium', 'duracaoDias' => 30, 'valor' => 129.90],
            ['nome' => 'Trimestral', 'duracaoDias' => 90, 'valor' => 249.90],
            ['nome' => 'Semestral', 'duracaoDias' => 180, 'valor' => 449.90],
            ['nome' => 'Anual', 'duracaoDias' => 365, 'valor' => 799.90],
            ['nome' => 'Anual Premium', 'duracaoDias' => 365, 'valor' => 999.90],
        ];

        $plano = fake()->randomElement($planos);

        return [
            'nome' => $plano['nome'],
            'descricao' => 'Plano ' . $plano['nome'] . ' com acesso completo à academia',
            'valor' => $plano['valor'],
            'duracaoDias' => $plano['duracaoDias'],
            'idAcademia' => Academia::factory(),
        ];
    }

    public function mensal(): static
    {
        return $this->state(fn(array $attributes) => [
            'nome' => 'Mensal',
            'duracaoDias' => 30,
            'valor' => 89.90,
        ]);
    }

    public function anual(): static
    {
        return $this->state(fn(array $attributes) => [
            'nome' => 'Anual',
            'duracaoDias' => 365,
            'valor' => 799.90,
        ]);
    }
}
