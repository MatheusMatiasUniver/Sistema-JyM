<?php

namespace Database\Factories;

use App\Models\Produto;
use App\Models\Academia;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Fornecedor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produto>
 */
class ProdutoFactory extends Factory
{
    protected $model = Produto::class;

    public function definition(): array
    {
        $produtos = [
            ['nome' => 'Whey Protein 1kg', 'preco' => 89.90, 'precoCompra' => 65.90],
            ['nome' => 'Creatina 300g', 'preco' => 59.90, 'precoCompra' => 42.90],
            ['nome' => 'BCAA 120 caps', 'preco' => 45.90, 'precoCompra' => 32.90],
            ['nome' => 'Pré-Treino 300g', 'preco' => 79.90, 'precoCompra' => 55.90],
            ['nome' => 'Glutamina 300g', 'preco' => 69.90, 'precoCompra' => 48.90],
            ['nome' => 'Barra de Proteína', 'preco' => 8.50, 'precoCompra' => 5.00],
            ['nome' => 'Squeeze 500ml', 'preco' => 15.90, 'precoCompra' => 10.90],
            ['nome' => 'Luva de Treino', 'preco' => 35.00, 'precoCompra' => 24.90],
            ['nome' => 'Camiseta Dry Fit', 'preco' => 49.90, 'precoCompra' => 32.00],
            ['nome' => 'Toalha Esportiva', 'preco' => 25.00, 'precoCompra' => 16.90],
            ['nome' => 'Isotônico 500ml', 'preco' => 6.50, 'precoCompra' => 4.50],
            ['nome' => 'Água Mineral 500ml', 'preco' => 3.50, 'precoCompra' => 1.80],
        ];

        $produto = fake()->randomElement($produtos);
        $estoque = fake()->numberBetween(10, 100);

        return [
            'nome' => $produto['nome'],
            'preco' => $produto['preco'],
            'precoCompra' => $produto['precoCompra'],
            'custoMedio' => $produto['precoCompra'],
            'estoque' => $estoque,
            'estoqueMinimo' => fake()->numberBetween(5, 20),
            'descricao' => 'Produto de alta qualidade para atletas',
            'idAcademia' => Academia::factory(),
            'idCategoria' => Categoria::factory(),
            'idMarca' => Marca::factory(),
            'idFornecedor' => Fornecedor::factory(),
        ];
    }

    public function semEstoque(): static
    {
        return $this->state(fn(array $attributes) => [
            'estoque' => 0,
        ]);
    }
}
