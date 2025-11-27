<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Academia;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $academias = Academia::all();

        foreach ($academias as $academia) {
            $materiais = [
                [
                    'descricao' => 'Álcool 70%',
                    'estoque' => 10,
                    'unidadeMedida' => 'L',
                    'estoqueMinimo' => 5,
                ],
                [
                    'descricao' => 'Toalhas de Papel',
                    'estoque' => 50,
                    'unidadeMedida' => 'un',
                    'estoqueMinimo' => 20,
                ],
                [
                    'descricao' => 'Desinfetante',
                    'estoque' => 12,
                    'unidadeMedida' => 'L',
                    'estoqueMinimo' => 6,
                ],
                [
                    'descricao' => 'Detergente Neutro',
                    'estoque' => 8,
                    'unidadeMedida' => 'L',
                    'estoqueMinimo' => 4,
                ],
                [
                    'descricao' => 'Lubrificante de Esteira',
                    'estoque' => 6,
                    'unidadeMedida' => 'L',
                    'estoqueMinimo' => 3,
                ],
                [
                    'descricao' => 'WD-40',
                    'estoque' => 6,
                    'unidadeMedida' => 'L',
                    'estoqueMinimo' => 3,
                ],
                [
                    'descricao' => 'Graxa Branca',
                    'estoque' => 5,
                    'unidadeMedida' => 'kg',
                    'estoqueMinimo' => 2,
                ],
            ];

            foreach ($materiais as $m) {
                Material::create(array_merge($m, ['idAcademia' => $academia->idAcademia]));
            }
        }
    }
}