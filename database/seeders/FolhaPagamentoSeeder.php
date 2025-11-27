<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FolhaPagamentoSeeder extends Seeder
{
    public function run(): void
    {
        $academias = DB::table('academias')->pluck('idAcademia');

        foreach ($academias as $idAcademia) {
            $idCategoria = DB::table('categorias_contas_pagar')
                ->where('idAcademia', $idAcademia)
                ->where('nome', 'Salários')
                ->value('idCategoriaContaPagar');

            if (!$idCategoria) {
                $idCategoria = DB::table('categorias_contas_pagar')->insertGetId([
                    'idAcademia' => $idAcademia,
                    'nome' => 'Salários',
                    'ativa' => true,
                ]);
            }

            $diaVencimento = DB::table('ajustes_sistema')
                ->where('idAcademia', $idAcademia)
                ->value('diaVencimentoSalarios') ?? 5;

            $funcionarios = DB::table('users')
                ->where('idAcademia', $idAcademia)
                ->whereNotNull('salarioMensal')
                ->where('salarioMensal', '>', 0)
                ->get();

            if ($funcionarios->isEmpty()) {
                continue;
            }

            for ($i = 0; $i < 4; $i++) {
                $dataReferencia = Carbon::now()->subMonths($i);
                $dataVencimento = $dataReferencia->copy()->addMonth()->day($diaVencimento);
                
                $isPast = $dataVencimento->isPast();
                $status = $isPast ? 'paga' : 'aberta';
                $dataPagamento = $isPast ? $dataVencimento->copy() : null;
                $formaPagamento = $isPast ? 'Transferência Bancária' : null;

                foreach ($funcionarios as $func) {
                    DB::table('contas_pagar')->insert([
                        'idAcademia' => $idAcademia,
                        'idFornecedor' => null,
                        'idFuncionario' => $func->idUsuario,
                        'idCategoriaContaPagar' => $idCategoria,
                        'documentoRef' => null,
                        'descricao' => "Salário {$dataReferencia->format('m/Y')} - {$func->nome}",
                        'valorTotal' => $func->salarioMensal,
                        'status' => $status,
                        'dataVencimento' => $dataVencimento->format('Y-m-d'),
                        'dataPagamento' => $dataPagamento ? $dataPagamento->format('Y-m-d') : null,
                        'formaPagamento' => $formaPagamento,
                    ]);
                }
            }
        }
    }
}
