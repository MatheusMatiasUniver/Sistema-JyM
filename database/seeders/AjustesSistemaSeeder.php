<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AjustesSistemaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ajustes_sistema')->insert([
            'idAcademia' => 1,
            'diaVencimentoSalarios' => 5,
        ]);

        DB::table('ajustes_sistema')->insert([
            'idAcademia' => 2,
            'diaVencimentoSalarios' => 10,
        ]);
    }
}
