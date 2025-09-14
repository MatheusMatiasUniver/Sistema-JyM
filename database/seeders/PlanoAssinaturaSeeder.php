<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PlanoAssinatura; // Importe o Model PlanoAssinatura
use App\Models\Academia; // Importe o Model Academia para pegar o ID

class PlanoAssinaturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Encontre a primeira academia criada pelo AcademiaSeeder
        $academiaJyM = Academia::where('nome', 'Academia Teste JyM')->first();
        $academiaFitnessPro = Academia::where('nome', 'Academia Fitness Pro')->first();

        // Se não encontrar, pode significar que o AcademiaSeeder não rodou ou o nome está diferente
        if (!$academiaJyM) {
            $this->command->warn('Academia "Academia Teste JyM" não encontrada para vincular planos.');
            // Opcional: crie-a aqui se não for um problema de ordem de execução
            $academiaJyM = Academia::factory()->create(['nome' => 'Academia Teste JyM']);
        }
        if (!$academiaFitnessPro) {
            $this->command->warn('Academia "Academia Fitness Pro" não encontrada para vincular planos.');
            $academiaFitnessPro = Academia::factory()->create(['nome' => 'Academia Fitness Pro']);
        }

        // Insere os planos de assinatura
        PlanoAssinatura::firstOrCreate(
            ['nome' => 'Plano Mensal Básico', 'idAcademia' => $academiaJyM->idAcademia ?? null],
            [
                'descricao' => 'Acesso ilimitado à academia por 30 dias.',
                'valor' => 89.90,
                'duracaoDias' => 30,
            ]
        );

        PlanoAssinatura::firstOrCreate(
            ['nome' => 'Plano Trimestral Premium', 'idAcademia' => $academiaJyM->idAcademia ?? null],
            [
                'descricao' => 'Acesso completo + aulas especiais por 90 dias.',
                'valor' => 250.00,
                'duracaoDias' => 90,
            ]
        );

        PlanoAssinatura::firstOrCreate(
            ['nome' => 'Plano Anual Gold', 'idAcademia' => $academiaFitnessPro->idAcademia ?? null],
            [
                'descricao' => 'Acesso completo a todas as unidades por 365 dias.',
                'valor' => 899.00,
                'duracaoDias' => 365,
            ]
        );
    }
}