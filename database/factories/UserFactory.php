<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Academia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $nome = fake('pt_BR')->name();
        $usuario = strtolower(str_replace(' ', '.', fake('pt_BR')->unique()->firstName() . '.' . fake('pt_BR')->lastName()));

        return [
            'nome' => $nome,
            'usuario' => $usuario,
            'email' => fake('pt_BR')->unique()->safeEmail(),
            'senha' => Hash::make('password'),
            'nivelAcesso' => 'Funcionário',
            'idAcademia' => Academia::factory(),
            'salarioMensal' => fake()->randomFloat(2, 1800, 5000),
        ];
    }

    public function administrador(): static
    {
        return $this->state(fn(array $attributes) => [
            'nivelAcesso' => 'Administrador',
            'idAcademia' => null,
            'salarioMensal' => fake()->randomFloat(2, 4000, 10000),
        ]);
    }

    public function funcionario(): static
    {
        return $this->state(fn(array $attributes) => [
            'nivelAcesso' => 'Funcionário',
        ]);
    }
}
