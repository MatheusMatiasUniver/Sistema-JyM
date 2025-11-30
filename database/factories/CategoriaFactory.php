<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Academia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Categoria>
 */
class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        $categorias = [
            ['nome' => 'Suplementos', 'descricao' => 'Suplementos alimentares e proteínas para treino'],
            ['nome' => 'Roupas Esportivas', 'descricao' => 'Roupas e acessórios para exercícios físicos'],
            ['nome' => 'Equipamentos', 'descricao' => 'Equipamentos e acessórios para treino'],
            ['nome' => 'Bebidas', 'descricao' => 'Bebidas energéticas, isotônicos e água'],
            ['nome' => 'Acessórios', 'descricao' => 'Acessórios diversos para academia'],
            ['nome' => 'Lanches', 'descricao' => 'Lanches saudáveis e barras de proteína'],
        ];

        $categoria = fake()->randomElement($categorias);

        return [
            'nome' => $categoria['nome'],
            'descricao' => $categoria['descricao'],
            'status' => 'Ativo',
            'idAcademia' => Academia::factory(),
        ];
    }

    public function ativa(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Ativo',
        ]);
    }

    public function inativa(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Inativo',
        ]);
    }
}
