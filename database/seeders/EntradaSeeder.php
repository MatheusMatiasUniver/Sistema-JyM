<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EntradaSeeder extends Seeder
{
    public function run(): void
    {
        $inicio = Carbon::today()->subDays(90);
        $fim = Carbon::today();

        $clientesPorAcademia = [];
        foreach ([1, 2] as $idAcademia) {
            $clientesPorAcademia[$idAcademia] = DB::table('clientes')
                ->where('idAcademia', $idAcademia)
                ->whereIn('status', ['Ativo','Inadimplente'])
                ->pluck('idCliente')
                ->all();
        }

        $metodos = ['Reconhecimento Facial','CPF/Senha','Manual'];
        $entradas = [];

        $dia = $inicio->copy();
        while ($dia->lte($fim)) {
            foreach ([1, 2] as $idAcademia) {
                $clientes = $clientesPorAcademia[$idAcademia];
                if (empty($clientes)) {
                    continue;
                }
                $qtdHoje = random_int(15, 50);
                $selecionados = [];
                for ($i = 0; $i < $qtdHoje; $i++) {
                    $selecionados[] = $clientes[array_rand($clientes)];
                }
                foreach ($selecionados as $idCliente) {
                    $vezes = random_int(1, 2);
                    for ($v = 0; $v < $vezes; $v++) {
                        $hora = random_int(6, 21);
                        $min = [0, 15, 30, 45][array_rand([0,1,2,3])];
                        $metodo = $metodos[array_rand($metodos)];
                        $entradas[] = [
                            'idCliente' => $idCliente,
                            'dataHora' => $dia->copy()->setTime($hora, $min),
                            'metodo' => $metodo,
                            'idAcademia' => $idAcademia,
                        ];
                    }
                }
            }
            $dia->addDay();
        }

        $chunks = array_chunk($entradas, 1000);
        foreach ($chunks as $chunk) {
            DB::table('entradas')->insert($chunk);
        }
    }
}
