<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('produtos')->insert([
            [
                'nome' => 'Whey Protein 1kg',
                'categoria' => 'Suplementos',
                'preco' => 89.90,
                'estoque' => 50,
                'descricao' => 'Whey Protein concentrado sabor chocolate',
                'idAcademia' => 1,
            ],
            [
                'nome' => 'Creatina 300g',
                'categoria' => 'Suplementos',
                'preco' => 59.90,
                'estoque' => 30,
                'descricao' => 'Creatina monohidratada pura',
                'idAcademia' => 1,
            ],
            [
                'nome' => 'Barra de Proteína',
                'categoria' => 'Alimentos',
                'preco' => 8.50,
                'estoque' => 100,
                'descricao' => 'Barra de proteína sabor amendoim',
                'idAcademia' => 1,
            ],
            [
                'nome' => 'Squeeze 500ml',
                'categoria' => 'Acessórios',
                'preco' => 15.90,
                'estoque' => 25,
                'descricao' => 'Squeeze para água com logo JyM',
                'idAcademia' => 1,
            ],
            [
                'nome' => 'Luva de Treino',
                'categoria' => 'Acessórios',
                'preco' => 35.00,
                'estoque' => 15,
                'descricao' => 'Luva de treino tamanho M',
                'idAcademia' => 1,
            ],
            [
                'nome' => 'Whey Protein 1kg',
                'categoria' => 'Suplementos',
                'preco' => 85.90,
                'estoque' => 40,
                'descricao' => 'Whey Protein concentrado sabor baunilha',
                'idAcademia' => 2,
            ],
            [
                'nome' => 'BCAA 120 caps',
                'categoria' => 'Suplementos',
                'preco' => 45.90,
                'estoque' => 35,
                'descricao' => 'BCAA em cápsulas',
                'idAcademia' => 2,
            ],
            [
                'nome' => 'Isotônico 500ml',
                'categoria' => 'Bebidas',
                'preco' => 6.50,
                'estoque' => 80,
                'descricao' => 'Isotônico sabor laranja',
                'idAcademia' => 2,
            ],
            [
                'nome' => 'Toalha Esportiva',
                'categoria' => 'Acessórios',
                'preco' => 25.00,
                'estoque' => 20,
                'descricao' => 'Toalha de microfibra',
                'idAcademia' => 2,
            ],
        ]);
    }
}