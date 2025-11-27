<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipamento;
use App\Models\Academia;

class EquipamentoSeeder extends Seeder
{
    public function run(): void
    {
        $academias = Academia::all();

        foreach ($academias as $academia) {
            $equipamentos = [
                [
                    'descricao' => 'Esteira Technogym Skillrun',
                    'fabricante' => 'Technogym',
                    'modelo' => 'Skillrun',
                    'numeroSerie' => 'TG-SR-' . $academia->idAcademia . '-001',
                    'dataAquisicao' => now()->subYears(2)->toDateString(),
                    'valorAquisicao' => 45000.00,
                    'garantiaFim' => now()->subYears(1)->toDateString(),
                    'centroCusto' => 'Academia',
                    'status' => 'Ativo',
                ],
                [
                    'descricao' => 'Bicicleta Life Fitness Integrity Upright',
                    'fabricante' => 'Life Fitness',
                    'modelo' => 'Integrity Upright',
                    'numeroSerie' => 'LF-IN-' . $academia->idAcademia . '-002',
                    'dataAquisicao' => now()->subYears(3)->toDateString(),
                    'valorAquisicao' => 25000.00,
                    'garantiaFim' => now()->subYears(1)->toDateString(),
                    'centroCusto' => 'Academia',
                    'status' => 'Ativo',
                ],
                [
                    'descricao' => 'Elíptico Precor EFX 885',
                    'fabricante' => 'Precor',
                    'modelo' => 'EFX 885',
                    'numeroSerie' => 'PC-EF-' . $academia->idAcademia . '-003',
                    'dataAquisicao' => now()->subYears(1)->toDateString(),
                    'valorAquisicao' => 38000.00,
                    'garantiaFim' => now()->addMonths(6)->toDateString(),
                    'centroCusto' => 'Academia',
                    'status' => 'Ativo',
                ],
                [
                    'descricao' => 'Power Rack Hammer Strength',
                    'fabricante' => 'Hammer Strength',
                    'modelo' => 'HD Elite',
                    'numeroSerie' => 'HS-HD-' . $academia->idAcademia . '-004',
                    'dataAquisicao' => now()->subYears(4)->toDateString(),
                    'valorAquisicao' => 18000.00,
                    'garantiaFim' => now()->subYears(2)->toDateString(),
                    'centroCusto' => 'Academia',
                    'status' => 'Ativo',
                ],
            ];

            foreach ($equipamentos as $e) {
                Equipamento::create(array_merge($e, ['idAcademia' => $academia->idAcademia]));
            }
        }
    }
}