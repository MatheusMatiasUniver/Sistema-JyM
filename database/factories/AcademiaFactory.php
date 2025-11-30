<?php

namespace Database\Factories;

use App\Models\Academia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Academia>
 */
class AcademiaFactory extends Factory
{
    protected $model = Academia::class;

    public function definition(): array
    {
        $nomes = [
            'Fitness Center', 'Academia Vida', 'Power Gym', 'Iron House',
            'Strong Body', 'Mega Fitness', 'Elite Gym', 'Top Forma',
            'Força Total', 'Corpo em Ação', 'Saúde & Forma', 'Muscle Factory'
        ];

        $cidades = [
            'Maringá - PR', 'Londrina - PR', 'Curitiba - PR', 'Cascavel - PR',
            'Ponta Grossa - PR', 'São Paulo - SP', 'Campinas - SP', 'Ribeirão Preto - SP'
        ];

        return [
            'nome' => fake('pt_BR')->randomElement($nomes) . ' ' . fake('pt_BR')->city(),
            'CNPJ' => sprintf(
                '%02d.%03d.%03d/%04d-%02d',
                fake()->numberBetween(10, 99),
                fake()->numberBetween(100, 999),
                fake()->numberBetween(100, 999),
                fake()->numberBetween(1, 9999),
                fake()->numberBetween(10, 99)
            ),
            'telefone' => sprintf('(%02d) %05d-%04d', fake()->numberBetween(11, 99), fake()->numberBetween(90000, 99999), fake()->numberBetween(1000, 9999)),
            'email' => fake('pt_BR')->unique()->companyEmail(),
            'endereco' => fake('pt_BR')->streetAddress() . ', ' . fake('pt_BR')->buildingNumber() . ' - ' . fake('pt_BR')->randomElement($cidades),
            'responsavel' => fake('pt_BR')->name(),
        ];
    }
}
