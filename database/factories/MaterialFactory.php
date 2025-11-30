<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\Academia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    protected $model = Material::class;

    public function definition(): array
    {
        $materiais = [
            ['descricao' => 'Álcool 70%', 'unidadeMedida' => 'L', 'estoqueMinimo' => 5],
            ['descricao' => 'Toalhas de Papel', 'unidadeMedida' => 'un', 'estoqueMinimo' => 20],
            ['descricao' => 'Desinfetante', 'unidadeMedida' => 'L', 'estoqueMinimo' => 6],
            ['descricao' => 'Detergente Neutro', 'unidadeMedida' => 'L', 'estoqueMinimo' => 4],
            ['descricao' => 'Lubrificante de Esteira', 'unidadeMedida' => 'L', 'estoqueMinimo' => 3],
            ['descricao' => 'WD-40', 'unidadeMedida' => 'L', 'estoqueMinimo' => 3],
            ['descricao' => 'Graxa Branca', 'unidadeMedida' => 'kg', 'estoqueMinimo' => 2],
            ['descricao' => 'Papel Higiênico', 'unidadeMedida' => 'un', 'estoqueMinimo' => 50],
            ['descricao' => 'Sabonete Líquido', 'unidadeMedida' => 'L', 'estoqueMinimo' => 10],
            ['descricao' => 'Saco de Lixo 100L', 'unidadeMedida' => 'un', 'estoqueMinimo' => 100],
        ];

        $material = fake()->randomElement($materiais);

        return [
            'descricao' => $material['descricao'],
            'estoque' => fake()->numberBetween($material['estoqueMinimo'], $material['estoqueMinimo'] * 3),
            'unidadeMedida' => $material['unidadeMedida'],
            'estoqueMinimo' => $material['estoqueMinimo'],
            'idAcademia' => Academia::factory(),
        ];
    }
}
