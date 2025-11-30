<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipamento;
use App\Models\Fornecedor;
use App\Models\ManutencaoEquipamento;

class ManutencaoEquipamentoSeeder extends Seeder
{
    public function run(): void
    {
        $equipamentos = Equipamento::all();

        foreach ($equipamentos as $eq) {
            $fornecedorNome = match ($eq->fabricante) {
                'Technogym' => 'Technogym Brasil',
                'Life Fitness' => 'Life Fitness Brasil',
                'Precor' => 'Precor Brasil',
                'Hammer Strength' => 'Life Fitness Brasil',
                default => null,
            };

            $fornecedor = $fornecedorNome
                ? Fornecedor::where('idAcademia', $eq->idAcademia)->where('razaoSocial', $fornecedorNome)->first()
                : null;

            // Manutenção preventiva programada (pendente)
            $preventiva = [
                'idEquipamento' => $eq->idEquipamento,
                'tipo' => 'preventiva',
                'dataSolicitacao' => now()->subDays(5)->toDateString(),
                'dataProgramada' => now()->addMonths(1)->toDateString(),
                'dataExecucao' => null,
                'descricao' => 'Revisão preventiva programada: inspeção geral, limpeza e lubrificação de componentes.',
                'servicoRealizado' => null,
                'custo' => null,
                'fornecedorId' => $fornecedor?->idFornecedor,
                'responsavel' => null,
                'status' => 'Pendente',
            ];

            // Manutenção corretiva já concluída
            $corretiva = [
                'idEquipamento' => $eq->idEquipamento,
                'tipo' => 'corretiva',
                'dataSolicitacao' => now()->subWeeks(3)->toDateString(),
                'dataProgramada' => null,
                'dataExecucao' => now()->subWeeks(2)->toDateString(),
                'descricao' => 'Ruído anormal durante uso e vibração excessiva.',
                'servicoRealizado' => 'Substituição de rolamentos e ajuste de correia de transmissão.',
                'custo' => 850.00,
                'fornecedorId' => $fornecedor?->idFornecedor,
                'responsavel' => 'Carlos Técnico',
                'status' => 'Concluída',
            ];

            ManutencaoEquipamento::create($preventiva);
            ManutencaoEquipamento::create($corretiva);
        }
    }
}