<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EntradaSeeder extends Seeder
{
    public function run(): void
    {
        $hoje = Carbon::today();
        
        DB::table('entradas')->insert([
            [
                'idCliente' => 1,
                'dataHora' => $hoje->copy()->setTime(6, 30),
                'metodo' => 'Reconhecimento Facial',
                'idAcademia' => 1,
            ],
            [
                'idCliente' => 2,
                'dataHora' => $hoje->copy()->setTime(7, 15),
                'metodo' => 'CPF/Senha',
                'idAcademia' => 1,
            ],
            [
                'idCliente' => 1,
                'dataHora' => $hoje->copy()->setTime(18, 45),
                'metodo' => 'Reconhecimento Facial',
                'idAcademia' => 1,
            ],
            [
                'idCliente' => 5,
                'dataHora' => $hoje->copy()->setTime(8, 0),
                'metodo' => 'CPF/Senha',
                'idAcademia' => 2,
            ],
            [
                'idCliente' => 6,
                'dataHora' => $hoje->copy()->setTime(19, 30),
                'metodo' => 'Reconhecimento Facial',
                'idAcademia' => 2,
            ],
            [
                'idCliente' => 5,
                'dataHora' => $hoje->copy()->subDay()->setTime(7, 30),
                'metodo' => 'Manual',
                'idAcademia' => 2,
            ],
            [
                'idCliente' => 6,
                'dataHora' => $hoje->copy()->subDay()->setTime(18, 0),
                'metodo' => 'CPF/Senha',
                'idAcademia' => 2,
            ],
        ]);
    }
}