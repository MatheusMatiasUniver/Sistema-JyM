<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Academia;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academias = Academia::all();

        $categoriasPadrao = [
            [
                'nome' => 'Suplementos',
                'descricao' => 'Suplementos alimentares e proteínas para treino',
                'status' => 'Ativo'
            ],
            [
                'nome' => 'Roupas Esportivas',
                'descricao' => 'Roupas e acessórios para exercícios físicos',
                'status' => 'Ativo'
            ],
            [
                'nome' => 'Equipamentos',
                'descricao' => 'Equipamentos e acessórios para treino',
                'status' => 'Ativo'
            ],
            [
                'nome' => 'Bebidas',
                'descricao' => 'Bebidas energéticas, isotônicos e água',
                'status' => 'Ativo'
            ],
            [
                'nome' => 'Acessórios',
                'descricao' => 'Acessórios diversos para academia',
                'status' => 'Ativo'
            ],
            [
                'nome' => 'Lanches',
                'descricao' => 'Lanches saudáveis e barras de proteína',
                'status' => 'Ativo'
            ]
        ];

        foreach ($academias as $academia) {
            foreach ($categoriasPadrao as $categoria) {
                Categoria::create([
                    'nome' => $categoria['nome'],
                    'descricao' => $categoria['descricao'],
                    'status' => $categoria['status'],
                    'idAcademia' => $academia->idAcademia
                ]);
            }
        }
    }
}
