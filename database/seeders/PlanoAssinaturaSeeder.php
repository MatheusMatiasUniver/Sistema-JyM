<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanoAssinaturaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('plano_assinaturas')->insert([
            [
                'nome' => 'Mensal Básico',
                'descricao' => 'Acesso completo à academia',
                'valor' => 89.90,
                'duracaoDias' => 30,
                'idAcademia' => 1,
            ],
            [
                'nome' => 'Trimestral',
                'descricao' => 'Acesso completo por 3 meses',
                'valor' => 249.90,
                'duracaoDias' => 90,
                'idAcademia' => 1,
            ],
            [
                'nome' => 'Anual Premium',
                'descricao' => 'Acesso completo + Personal Trainer',
                'valor' => 999.90,
                'duracaoDias' => 365,
                'idAcademia' => 1,
            ],
            [
                'nome' => 'Mensal Básico',
                'descricao' => 'Acesso completo à academia',
                'valor' => 79.90,
                'duracaoDias' => 30,
                'idAcademia' => 2,
            ],
            [
                'nome' => 'Semestral',
                'descricao' => 'Acesso completo por 6 meses',
                'valor' => 449.90,
                'duracaoDias' => 180,
                'idAcademia' => 2,
            ],
        ]);
    }
}