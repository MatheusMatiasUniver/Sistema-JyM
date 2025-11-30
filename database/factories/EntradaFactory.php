<?php

namespace Database\Factories;

use App\Models\Entrada;
use App\Models\Cliente;
use App\Models\Academia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entrada>
 */
class EntradaFactory extends Factory
{
    protected $model = Entrada::class;

    public function definition(): array
    {
        return [
            'idCliente' => Cliente::factory(),
            'idAcademia' => Academia::factory(),
            'dataHora' => fake()->dateTimeBetween('-90 days', 'now'),
            'metodo' => fake()->randomElement(['Reconhecimento Facial', 'CPF/Senha', 'Manual']),
        ];
    }

    public function reconhecimentoFacial(): static
    {
        return $this->state(fn(array $attributes) => [
            'metodo' => 'Reconhecimento Facial',
        ]);
    }

    public function manual(): static
    {
        return $this->state(fn(array $attributes) => [
            'metodo' => 'Manual',
        ]);
    }
}
