<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Produto;
use Faker\Factory as Faker;

class ProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        Produto::create([
            'nome' => 'Camiseta Dry Fit',
            'categoria' => 'Vestuário',
            'preco' => 49.90,
            'estoque' => 50,
            'descricao' => 'Camiseta esportiva de secagem rápida.',
            'imagem' => 'camiseta.jpg',
        ]);

        Produto::create([
            'nome' => 'Garrafa Térmica 1L',
            'categoria' => 'Acessórios',
            'preco' => 35.00,
            'estoque' => 30,
            'descricao' => 'Garrafa para manter sua bebida na temperatura ideal.',
            'imagem' => 'garrafa.jpg',
        ]);

        Produto::create([
            'nome' => 'Suplemento Whey Protein',
            'categoria' => 'Suplementos',
            'preco' => 120.00,
            'estoque' => 20,
            'descricao' => 'Proteína para recuperação muscular.',
            'imagem' => 'whey.jpg',
        ]);

        for ($i = 0; $i < 5; $i++) {
            Produto::create([
                'nome' => $faker->word . ' ' . $faker->randomElement(['Top', 'Short', 'Luva', 'Barra']),
                'categoria' => $faker->randomElement(['Vestuário', 'Acessórios', 'Suplementos']),
                'preco' => $faker->randomFloat(2, 10, 200),
                'estoque' => $faker->numberBetween(5, 100),
                'descricao' => $faker->sentence,
                'imagem' => $faker->imageUrl(640, 480, 'sports', true, null, false),
            ]);
        }
    }
}