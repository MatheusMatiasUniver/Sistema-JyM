<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fornecedor;
use App\Models\Academia;

class FornecedorSeeder extends Seeder
{
    public function run(): void
    {
        $academias = Academia::all();

        foreach ($academias as $academia) {
            $fornecedores = [
                [
                    'razaoSocial' => 'Growth Supplements',
                    'cnpjCpf' => '12.345.678/0001-90',
                    'inscricaoEstadual' => null,
                    'contato' => 'Atendimento Growth',
                    'telefone' => '(11) 3003-1234',
                    'email' => 'contato@growthsupplements.com.br',
                    'endereco' => 'São Paulo - SP',
                    'condicaoPagamentoPadrao' => '30 dias',
                    'ativo' => true,
                ],
                [
                    'razaoSocial' => 'Max Titanium',
                    'cnpjCpf' => '23.456.789/0001-01',
                    'inscricaoEstadual' => null,
                    'contato' => 'Comercial Max Titanium',
                    'telefone' => '(11) 4002-8922',
                    'email' => 'comercial@maxtitanium.com.br',
                    'endereco' => 'São Paulo - SP',
                    'condicaoPagamentoPadrao' => '28 dias',
                    'ativo' => true,
                ],
                [
                    'razaoSocial' => 'IntegralMedica',
                    'cnpjCpf' => '34.567.890/0001-12',
                    'inscricaoEstadual' => null,
                    'contato' => 'Vendas IntegralMedica',
                    'telefone' => '(11) 3500-1234',
                    'email' => 'vendas@integralmedica.com.br',
                    'endereco' => 'São Paulo - SP',
                    'condicaoPagamentoPadrao' => '30 dias',
                    'ativo' => true,
                ],
                [
                    'razaoSocial' => 'Decathlon Brasil',
                    'cnpjCpf' => '45.678.901/0001-23',
                    'inscricaoEstadual' => null,
                    'contato' => 'Atendimento Decathlon',
                    'telefone' => '(11) 3004-7777',
                    'email' => 'contato@decathlon.com.br',
                    'endereco' => 'Barueri - SP',
                    'condicaoPagamentoPadrao' => '21 dias',
                    'ativo' => true,
                ],
                [
                    'razaoSocial' => 'Centauro',
                    'cnpjCpf' => '56.789.012/0001-34',
                    'inscricaoEstadual' => null,
                    'contato' => 'Comercial Centauro',
                    'telefone' => '(11) 3020-1234',
                    'email' => 'comercial@centauro.com.br',
                    'endereco' => 'São Paulo - SP',
                    'condicaoPagamentoPadrao' => '15 dias',
                    'ativo' => true,
                ],
                [
                    'razaoSocial' => 'Technogym Brasil',
                    'cnpjCpf' => '67.890.123/0001-45',
                    'inscricaoEstadual' => null,
                    'contato' => 'Comercial Technogym',
                    'telefone' => '(11) 3100-1234',
                    'email' => 'comercial@technogym.com',
                    'endereco' => 'São Paulo - SP',
                    'condicaoPagamentoPadrao' => '30 dias',
                    'ativo' => true,
                ],
                [
                    'razaoSocial' => 'Life Fitness Brasil',
                    'cnpjCpf' => '78.901.234/0001-56',
                    'inscricaoEstadual' => null,
                    'contato' => 'Comercial Life Fitness',
                    'telefone' => '(11) 3200-1234',
                    'email' => 'sales.br@lifefitness.com',
                    'endereco' => 'São Paulo - SP',
                    'condicaoPagamentoPadrao' => '30 dias',
                    'ativo' => true,
                ],
                [
                    'razaoSocial' => 'Precor Brasil',
                    'cnpjCpf' => '89.012.345/0001-67',
                    'inscricaoEstadual' => null,
                    'contato' => 'Comercial Precor',
                    'telefone' => '(11) 3300-1234',
                    'email' => 'sales.br@precor.com',
                    'endereco' => 'São Paulo - SP',
                    'condicaoPagamentoPadrao' => '30 dias',
                    'ativo' => true,
                ],
            ];

            foreach ($fornecedores as $f) {
                Fornecedor::create(array_merge($f, ['idAcademia' => $academia->idAcademia]));
            }
        }
    }
}