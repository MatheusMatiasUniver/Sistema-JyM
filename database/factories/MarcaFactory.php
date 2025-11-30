<?php

namespace Database\Factories;

use App\Models\Marca;
use App\Models\Academia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Marca>
 */
class MarcaFactory extends Factory
{
    protected $model = Marca::class;

    public function definition(): array
    {
        $marcas = [
            ['nome' => 'Growth Supplements', 'paisOrigem' => 'Brasil', 'site' => 'https://www.growthsupplements.com.br'],
            ['nome' => 'Max Titanium', 'paisOrigem' => 'Brasil', 'site' => 'https://www.maxtitanium.com.br'],
            ['nome' => 'IntegralMedica', 'paisOrigem' => 'Brasil', 'site' => 'https://www.integralmedica.com.br'],
            ['nome' => 'Probiótica', 'paisOrigem' => 'Brasil', 'site' => 'https://www.probiotica.com.br'],
            ['nome' => 'Optimum Nutrition', 'paisOrigem' => 'Estados Unidos', 'site' => 'https://www.optimumnutrition.com'],
            ['nome' => 'Everlast', 'paisOrigem' => 'Estados Unidos', 'site' => 'https://www.everlast.com'],
            ['nome' => 'Nike', 'paisOrigem' => 'Estados Unidos', 'site' => 'https://www.nike.com.br'],
            ['nome' => 'Adidas', 'paisOrigem' => 'Alemanha', 'site' => 'https://www.adidas.com.br'],
        ];

        $marca = fake()->randomElement($marcas);

        return [
            'nome' => $marca['nome'],
            'paisOrigem' => $marca['paisOrigem'],
            'site' => $marca['site'],
            'ativo' => true,
            'idAcademia' => Academia::factory(),
        ];
    }

    public function inativa(): static
    {
        return $this->state(fn(array $attributes) => [
            'ativo' => false,
        ]);
    }
}
