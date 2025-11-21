<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\Fornecedor;
use App\Models\Marca;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $academia1Categorias = Categoria::where('idAcademia', 1)->get()->keyBy('nome');
        $academia2Categorias = Categoria::where('idAcademia', 2)->get()->keyBy('nome');
        $fornecedores1 = Fornecedor::where('idAcademia', 1)->get()->keyBy('razaoSocial');
        $fornecedores2 = Fornecedor::where('idAcademia', 2)->get()->keyBy('razaoSocial');
        $marcas1 = Marca::where('idAcademia', 1)->get()->keyBy('nome');
        $marcas2 = Marca::where('idAcademia', 2)->get()->keyBy('nome');

        if ($academia1Categorias->isNotEmpty()) {
            $produtos1 = [
                [
                    'nome' => 'Whey Protein 1kg',
                    'idCategoria' => $academia1Categorias->get('Suplementos')?->idCategoria ?? $academia1Categorias->first()->idCategoria,
                    'preco' => 89.90,
                    'estoque' => 50,
                    'descricao' => 'Whey Protein concentrado sabor chocolate',
                    'idAcademia' => 1,
                    'idMarca' => $marcas1->get('Growth Supplements')?->idMarca ?? null,
                    'idFornecedor' => $fornecedores1->get('Growth Supplements')?->idFornecedor ?? null,
                    'precoCompra' => 65.90,
                    'custoMedio' => 65.90,
                    'estoqueMinimo' => 10,
                    'unidadeMedida' => 'un',
                    'codigoBarras' => '7891000000011',
                    'vendavel' => true,
                ],
                [
                    'nome' => 'Creatina 300g',
                    'idCategoria' => $academia1Categorias->get('Suplementos')?->idCategoria ?? $academia1Categorias->first()->idCategoria,
                    'preco' => 59.90,
                    'estoque' => 30,
                    'descricao' => 'Creatina monohidratada pura',
                    'idAcademia' => 1,
                    'idMarca' => $marcas1->get('Max Titanium')?->idMarca ?? null,
                    'idFornecedor' => $fornecedores1->get('Max Titanium')?->idFornecedor ?? null,
                    'precoCompra' => 42.90,
                    'custoMedio' => 42.90,
                    'estoqueMinimo' => 8,
                    'unidadeMedida' => 'un',
                    'codigoBarras' => '7891000000028',
                    'vendavel' => true,
                ],
                [
                    'nome' => 'Barra de Proteína',
                    'idCategoria' => $academia1Categorias->get('Lanches')?->idCategoria ?? $academia1Categorias->first()->idCategoria,
                    'preco' => 8.50,
                    'estoque' => 100,
                    'descricao' => 'Barra de proteína sabor amendoim',
                    'idAcademia' => 1,
                    'idMarca' => $marcas1->get('IntegralMedica')?->idMarca ?? null,
                    'idFornecedor' => $fornecedores1->get('IntegralMedica')?->idFornecedor ?? null,
                    'precoCompra' => 5.00,
                    'custoMedio' => 5.00,
                    'estoqueMinimo' => 20,
                    'unidadeMedida' => 'un',
                    'codigoBarras' => '7891000000035',
                    'vendavel' => true,
                ],
                [
                    'nome' => 'Squeeze 500ml',
                    'idCategoria' => $academia1Categorias->get('Acessórios')?->idCategoria ?? $academia1Categorias->first()->idCategoria,
                    'preco' => 15.90,
                    'estoque' => 25,
                    'descricao' => 'Squeeze para água com logo JyM',
                    'idAcademia' => 1,
                    'idMarca' => $marcas1->get('Kalenji')?->idMarca ?? null,
                    'idFornecedor' => $fornecedores1->get('Decathlon Brasil')?->idFornecedor ?? null,
                    'precoCompra' => 10.90,
                    'custoMedio' => 10.90,
                    'estoqueMinimo' => 5,
                    'unidadeMedida' => 'un',
                    'codigoBarras' => '7891000000042',
                    'vendavel' => true,
                ],
                [
                    'nome' => 'Luva de Treino',
                    'idCategoria' => $academia1Categorias->get('Acessórios')?->idCategoria ?? $academia1Categorias->first()->idCategoria,
                    'preco' => 35.00,
                    'estoque' => 15,
                    'descricao' => 'Luva de treino tamanho M',
                    'idAcademia' => 1,
                    'idMarca' => $marcas1->get('Everlast')?->idMarca ?? null,
                    'idFornecedor' => $fornecedores1->get('Centauro')?->idFornecedor ?? null,
                    'precoCompra' => 24.90,
                    'custoMedio' => 24.90,
                    'estoqueMinimo' => 5,
                    'unidadeMedida' => 'un',
                    'codigoBarras' => '7891000000059',
                    'vendavel' => true,
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
                    'idMarca' => $marcas2->get('Growth Supplements')?->idMarca ?? null,
                    'idFornecedor' => $fornecedores2->get('Growth Supplements')?->idFornecedor ?? null,
                    'precoCompra' => 62.90,
                    'custoMedio' => 62.90,
                    'estoqueMinimo' => 10,
                    'unidadeMedida' => 'un',
                    'codigoBarras' => '7892000000018',
                    'vendavel' => true,
                ],
                [
                    'nome' => 'BCAA 120 caps',
                    'idCategoria' => $academia2Categorias->get('Suplementos')?->idCategoria ?? $academia2Categorias->first()->idCategoria,
                    'preco' => 45.90,
                    'estoque' => 35,
                    'descricao' => 'BCAA em cápsulas',
                    'idAcademia' => 2,
                    'idMarca' => $marcas2->get('Max Titanium')?->idMarca ?? null,
                    'idFornecedor' => $fornecedores2->get('Max Titanium')?->idFornecedor ?? null,
                    'precoCompra' => 32.90,
                    'custoMedio' => 32.90,
                    'estoqueMinimo' => 8,
                    'unidadeMedida' => 'un',
                    'codigoBarras' => '7892000000025',
                    'vendavel' => true,
                ],
                [
                    'nome' => 'Isotônico 500ml',
                    'idCategoria' => $academia2Categorias->get('Bebidas')?->idCategoria ?? $academia2Categorias->first()->idCategoria,
                    'preco' => 6.50,
                    'estoque' => 80,
                    'descricao' => 'Isotônico sabor laranja',
                    'idAcademia' => 2,
                    'idMarca' => $marcas2->get('Powerade')?->idMarca ?? null,
                    'idFornecedor' => $fornecedores2->get('Decathlon Brasil')?->idFornecedor ?? null,
                    'precoCompra' => 4.50,
                    'custoMedio' => 4.50,
                    'estoqueMinimo' => 20,
                    'unidadeMedida' => 'un',
                    'codigoBarras' => '7892000000032',
                    'vendavel' => true,
                ],
                [
                    'nome' => 'Toalha Esportiva',
                    'idCategoria' => $academia2Categorias->get('Acessórios')?->idCategoria ?? $academia2Categorias->first()->idCategoria,
                    'preco' => 25.00,
                    'estoque' => 20,
                    'descricao' => 'Toalha de microfibra',
                    'idAcademia' => 2,
                    'idMarca' => $marcas2->get('Domyos')?->idMarca ?? null,
                    'idFornecedor' => $fornecedores2->get('Centauro')?->idFornecedor ?? null,
                    'precoCompra' => 16.90,
                    'custoMedio' => 16.90,
                    'estoqueMinimo' => 6,
                    'unidadeMedida' => 'un',
                    'codigoBarras' => '7892000000049',
                    'vendavel' => true,
                ],
            ];

            foreach ($produtos2 as $produto) {
                Produto::create($produto);
            }
        }
    }
}
