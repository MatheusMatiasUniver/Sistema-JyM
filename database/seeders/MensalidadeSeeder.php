<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MensalidadeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mensalidades')->insert([
            [
                'idCliente' => 1,
                'idPlano' => 1,
                'idAcademia' => 1,
                'dataVencimento' => Carbon::now()->addDays(15),
                'dataPagamento' => Carbon::now()->subDays(5),
                'valor' => 89.90,
                'status' => 'Paga',
                'formaPagamento' => 'PIX',
            ],
            [
                'idCliente' => 2,
                'idPlano' => 2,
                'idAcademia' => 1,
                'dataVencimento' => Carbon::now()->addDays(60),
                'dataPagamento' => Carbon::now()->subDays(10),
                'valor' => 249.90,
                'status' => 'Paga',
                'formaPagamento' => 'Cartão de Crédito',
            ],
            [
                'idCliente' => 3,
                'idPlano' => 1,
                'idAcademia' => 1,
                'dataVencimento' => Carbon::now()->subDays(10),
                'dataPagamento' => null,
                'valor' => 89.90,
                'status' => 'Pendente',
                'formaPagamento' => null,
            ],
            [
                'idCliente' => 4,
                'idPlano' => 3,
                'idAcademia' => 1,
                'dataVencimento' => Carbon::now()->addDays(300),
                'dataPagamento' => Carbon::now()->subDays(30),
                'valor' => 999.90,
                'status' => 'Paga',
                'formaPagamento' => 'Boleto',
            ],
            [
                'idCliente' => 5,
                'idPlano' => 4,
                'idAcademia' => 2,
                'dataVencimento' => Carbon::now()->addDays(20),
                'dataPagamento' => Carbon::now()->subDays(3),
                'valor' => 79.90,
                'status' => 'Paga',
                'formaPagamento' => 'PIX',
            ],
            [
                'idCliente' => 6,
                'idPlano' => 5,
                'idAcademia' => 2,
                'dataVencimento' => Carbon::now()->addDays(150),
                'dataPagamento' => Carbon::now()->subDays(15),
                'valor' => 449.90,
                'status' => 'Paga',
                'formaPagamento' => 'Cartão de Débito',
            ],
            [
                'idCliente' => 7,
                'idPlano' => 4,
                'idAcademia' => 2,
                'dataVencimento' => Carbon::now()->addDays(5),
                'dataPagamento' => null,
                'valor' => 79.90,
                'status' => 'Pendente',
                'formaPagamento' => null,
            ],
        ]);
    }
}