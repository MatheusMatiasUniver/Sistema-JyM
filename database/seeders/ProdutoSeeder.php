<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto;
use App\Models\Categoria;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $academia1Categorias = Categoria::where('idAcademia', 1)->get()->keyBy('nome');
        $academia2Categorias = Categoria::where('idAcademia', 2)->get()->keyBy('nome');

        if ($academia1Categorias->isNotEmpty()) {
            $produtos1 = [
                [
                    'nome' => 'Whey Protein 1kg',
                    'idCategoria' => $academia1Categorias->get('Suplementos')?->idCategoria ?? $academia1Categorias->first()->idCategoria,
                    'preco' => 89.90,
                    'estoque' => 50,
                    'descricao' => 'Whey Protein concentrado sabor chocolate',
                    'idAcademia' => 1,
                ],
                [
                    'nome' => 'Creatina 300g',
                    'idCategoria' => $academia1Categorias->get('Suplementos')?->idCategoria ?? $academia1Categorias->first()->idCategoria,
                    'preco' => 59.90,
                    'estoque' => 30,
                    'descricao' => 'Creatina monohidratada pura',
                    'idAcademia' => 1,
                ],
                [
                    'nome' => 'Barra de Proteína',
                    'idCategoria' => $academia1Categorias->get('Lanches')?->idCategoria ?? $academia1Categorias->first()->idCategoria,
                    'preco' => 8.50,
                    'estoque' => 100,
                    'descricao' => 'Barra de proteína sabor amendoim',
                    'idAcademia' => 1,
                ],
                [
                    'nome' => 'Squeeze 500ml',
                    'idCategoria' => $academia1Categorias->get('Acessórios')?->idCategoria ?? $academia1Categorias->first()->idCategoria,
                    'preco' => 15.90,
                    'estoque' => 25,
                    'descricao' => 'Squeeze para água com logo JyM',
                    'idAcademia' => 1,
                ],
                [
                    'nome' => 'Luva de Treino',
                    'idCategoria' => $academia1Categorias->get('Acessórios')?->idCategoria ?? $academia1Categorias->first()->idCategoria,
                    'preco' => 35.00,
                    'estoque' => 15,
                    'descricao' => 'Luva de treino tamanho M',
                    'idAcademia' => 1,
                ],
            ];

            foreach ($produtos1 as $produto) {
                Produto::create($produto);
            }
        }

        if ($academia2Categorias->isNotEmpty()) {
            $produtos2 = [
                [
                    'nome' => 'Whey Protein 1kg',
                    'idCategoria' => $academia2Categorias->get('Suplementos')?->idCategoria ?? $academia2Categorias->first()->idCategoria,
                    'preco' => 85.90,
                    'estoque' => 40,
                    'descricao' => 'Whey Protein concentrado sabor baunilha',
                    'idAcademia' => 2,
                ],
                [
                    'nome' => 'BCAA 120 caps',
                    'idCategoria' => $academia2Categorias->get('Suplementos')?->idCategoria ?? $academia2Categorias->first()->idCategoria,
                    'preco' => 45.90,
                    'estoque' => 35,
                    'descricao' => 'BCAA em cápsulas',
                    'idAcademia' => 2,
                ],
                [
                    'nome' => 'Isotônico 500ml',
                    'idCategoria' => $academia2Categorias->get('Bebidas')?->idCategoria ?? $academia2Categorias->first()->idCategoria,
                    'preco' => 6.50,
                    'estoque' => 80,
                    'descricao' => 'Isotônico sabor laranja',
                    'idAcademia' => 2,
                ],
                [
                    'nome' => 'Toalha Esportiva',
                    'idCategoria' => $academia2Categorias->get('Acessórios')?->idCategoria ?? $academia2Categorias->first()->idCategoria,
                    'preco' => 25.00,
                    'estoque' => 20,
                    'descricao' => 'Toalha de microfibra',
                    'idAcademia' => 2,
                ],
            ];

            foreach ($produtos2 as $produto) {
                Produto::create($produto);
            }
        }
    }
}