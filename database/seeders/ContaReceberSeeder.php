<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContaReceberSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([1, 2] as $idAcademia) {
            $clientes = DB::table('clientes')->where('idAcademia', $idAcademia)->pluck('idCliente')->all();
            if (empty($clientes)) {
                continue;
            }

            $qtd = 20;
            for ($i = 0; $i < $qtd; $i++) {
                $idCliente = $clientes[array_rand($clientes)];
                $valor = [49.90, 59.90, 69.90, 79.90, 99.90][array_rand([0,1,2,3,4])];
                $descricao = 'Serviço avulso Cliente #'.$idCliente;
                $dias = random_int(5, 45);
                $venc = Carbon::today()->addDays($dias)->toDateString();
                DB::table('contas_receber')->insert([
                    'idAcademia' => $idAcademia,
                    'idCliente' => $idCliente,
                    'documentoRef' => null,
                    'descricao' => $descricao,
                    'valorTotal' => $valor,
                    'status' => 'aberta',
                    'dataVencimento' => $venc,
                    'dataRecebimento' => null,
                    'formaRecebimento' => null,
                ]);
            }
        }
    }
}

