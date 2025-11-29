<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ajustes_sistema', function (Blueprint $table) {
            $table->boolean('clienteOpcionalVenda')->default(false)->after('diaVencimentoSalarios');
            $table->json('formasPagamentoAceitas')->nullable()->after('clienteOpcionalVenda');
            $table->boolean('permitirEdicaoManualEstoque')->default(false)->after('formasPagamentoAceitas');
        });

        $defaultOptions = json_encode([
            'Dinheiro',
            'Cartão de Crédito',
            'Cartão de Débito',
            'PIX',
            'Boleto',
        ]);

        DB::table('ajustes_sistema')
            ->whereNull('formasPagamentoAceitas')
            ->update(['formasPagamentoAceitas' => $defaultOptions]);
    }

    public function down(): void
    {
        Schema::table('ajustes_sistema', function (Blueprint $table) {
            $table->dropColumn(['clienteOpcionalVenda', 'formasPagamentoAceitas', 'permitirEdicaoManualEstoque']);
        });
    }
};
