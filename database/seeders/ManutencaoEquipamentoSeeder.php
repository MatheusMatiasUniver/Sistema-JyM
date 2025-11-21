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

            $preventiva = [
                'idEquipamento' => $eq->idEquipamento,
                'tipo' => 'preventiva',
                'dataProgramada' => now()->addMonths(1)->toDateString(),
                'dataExecucao' => null,
                'custo' => null,
                'fornecedorId' => $fornecedor?->idFornecedor,
                'observacoes' => 'Revisão preventiva programada: inspeção, limpeza e lubrificação.',
            ];

            $corretiva = [
                'idEquipamento' => $eq->idEquipamento,
                'tipo' => 'corretiva',
                'dataProgramada' => null,
                'dataExecucao' => now()->subWeeks(2)->toDateString(),
                'custo' => 850.00,
                'fornecedorId' => $fornecedor?->idFornecedor,
                'observacoes' => 'Substituição de rolamentos e ajuste de correia.',
            ];

            ManutencaoEquipamento::create($preventiva);
            ManutencaoEquipamento::create($corretiva);
        }
    }
}