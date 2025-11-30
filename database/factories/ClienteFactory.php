<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Academia;
use App\Models\PlanoAssinatura;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'nome' => fake('pt_BR')->name(),
            'cpf' => sprintf(
                '%03d.%03d.%03d-%02d',
                fake()->numberBetween(100, 999),
                fake()->numberBetween(100, 999),
                fake()->numberBetween(100, 999),
                fake()->numberBetween(10, 99)
            ),
            'dataNascimento' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'telefone' => sprintf('(%02d) 9%04d-%04d', fake()->numberBetween(11, 99), fake()->numberBetween(1000, 9999), fake()->numberBetween(1000, 9999)),
            'email' => fake('pt_BR')->unique()->safeEmail(),
            'codigo_acesso' => str_pad((string) fake()->unique()->numberBetween(0, 999999), 6, '0', STR_PAD_LEFT),
            'status' => fake()->randomElement(['Ativo', 'Inativo', 'Inadimplente']),
            'idAcademia' => Academia::factory(),
            'idPlano' => PlanoAssinatura::factory(),
            'idUsuario' => User::factory(),
        ];
    }

    public function ativo(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Ativo',
        ]);
    }

    public function inadimplente(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Inadimplente',
        ]);
    }

    public function inativo(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Inativo',
        ]);
    }
}
