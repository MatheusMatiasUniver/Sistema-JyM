<?php

namespace Database\Seeders;

use App\Models\AjusteSistema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AjustesSistemaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ajustes_sistema')->insert([
            'idAcademia' => 1,
            'diaVencimentoSalarios' => 5,
            'clienteOpcionalVenda' => false,
            'formasPagamentoAceitas' => json_encode(AjusteSistema::FORMAS_PAGAMENTO_PADRAO),
            'permitirEdicaoManualEstoque' => false,
        ]);

        DB::table('ajustes_sistema')->insert([
            'idAcademia' => 2,
            'diaVencimentoSalarios' => 10,
            'clienteOpcionalVenda' => false,
            'formasPagamentoAceitas' => json_encode(AjusteSistema::FORMAS_PAGAMENTO_PADRAO),
            'permitirEdicaoManualEstoque' => false,
        ]);
    }
}
