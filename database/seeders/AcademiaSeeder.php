<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademiaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('academias')->insert([
            [
                'idAcademia' => 1,
                'nome' => 'JyM Fitness Centro',
                'cnpj' => '12.345.678/0001-90',
                'telefone' => '(44) 3025-8900',
                'email' => 'contato@jymcentro.com.br',
                'endereco' => 'Av. Brasil, 1500 - Centro, Maringá - PR',
                'responsavel' => 'João Marcos Silva',
            ],
            [
                'idAcademia' => 2,
                'nome' => 'JyM Fitness Zona Sul',
                'cnpj' => '12.345.678/0002-71',
                'telefone' => '(44) 3026-7800',
                'email' => 'contato@jymzonasul.com.br',
                'endereco' => 'Rua das Flores, 850 - Zona Sul, Maringá - PR',
                'responsavel' => 'Maria Fernanda Costa',
            ],
        ]);
    }
}