<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Academia;

class AcademiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Academia::firstOrCreate(
            ['nome' => 'Academia Teste JyM'],
            [
                'CNPJ' => '12.345.678/0001-90',
                'telefone' => '(11)99999-1234',
                'email' => 'contato@academiatestejym.com',
                'endereco' => 'Rua Exemplo, 123, Cidade',
                'responsavel' => 'Responsável Teste',
            ]
        );

        Academia::firstOrCreate(
            ['nome' => 'Academia Fitness Pro'],
            [
                'CNPJ' => '98.765.432/0001-21',
                'telefone' => '(21)98888-5678',
                'email' => 'fitnesspro@exemplo.com',
                'endereco' => 'Avenida Principal, 456, Outra Cidade',
                'responsavel' => 'Novo Responsável',
            ]
        );
    }
}