<?php

namespace Database\Factories;

use App\Models\Fornecedor;
use App\Models\Academia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fornecedor>
 */
class FornecedorFactory extends Factory
{
    protected $model = Fornecedor::class;

    public function definition(): array
    {
        $fornecedores = [
            'Growth Supplements', 'Max Titanium', 'IntegralMedica',
            'Decathlon Brasil', 'Centauro', 'Netshoes',
            'Technogym Brasil', 'Life Fitness Brasil', 'Precor Brasil'
        ];

        return [
            'razaoSocial' => fake()->randomElement($fornecedores) . ' ' . fake('pt_BR')->companySuffix(),
            'cnpjCpf' => sprintf(
                '%02d.%03d.%03d/%04d-%02d',
                fake()->numberBetween(10, 99),
                fake()->numberBetween(100, 999),
                fake()->numberBetween(100, 999),
                fake()->numberBetween(1, 9999),
                fake()->numberBetween(10, 99)
            ),
            'inscricaoEstadual' => fake()->optional(0.7)->numerify('###.###.###.###'),
            'contato' => fake('pt_BR')->name(),
            'telefone' => sprintf('(%02d) %04d-%04d', fake()->numberBetween(11, 99), fake()->numberBetween(3000, 3999), fake()->numberBetween(1000, 9999)),
            'email' => fake('pt_BR')->companyEmail(),
            'endereco' => fake('pt_BR')->city() . ' - ' . fake('pt_BR')->stateAbbr(),
            'condicaoPagamentoPadrao' => fake()->randomElement(['15 dias', '21 dias', '28 dias', '30 dias', '45 dias']),
            'ativo' => true,
            'idAcademia' => Academia::factory(),
        ];
    }

    public function inativo(): static
    {
        return $this->state(fn(array $attributes) => [
            'ativo' => false,
        ]);
    }
}
