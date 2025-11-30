<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('manutencoes_equipamento', function (Blueprint $table) {
            // Renomear observacoes para descricao (problema/defeito reportado)
            $table->renameColumn('observacoes', 'descricao');
        });

        Schema::table('manutencoes_equipamento', function (Blueprint $table) {
            // Adicionar campos conforme documentação
            $table->date('dataSolicitacao')->nullable()->after('tipo');
            $table->text('servicoRealizado')->nullable()->after('descricao');
            $table->string('responsavel', 100)->nullable()->after('custo');
            $table->enum('status', ['Pendente', 'Concluída'])->default('Pendente')->after('responsavel');
        });

        // Atualizar registros existentes: dataSolicitacao = dataProgramada ou hoje
        DB::statement("
            UPDATE manutencoes_equipamento 
            SET dataSolicitacao = COALESCE(dataProgramada, CURDATE())
        ");

        // Registros com dataExecucao preenchida são concluídos
        DB::statement("
            UPDATE manutencoes_equipamento 
            SET status = 'Concluída' 
            WHERE dataExecucao IS NOT NULL
        ");

        // Tornar dataSolicitacao NOT NULL após preencher
        Schema::table('manutencoes_equipamento', function (Blueprint $table) {
            $table->date('dataSolicitacao')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('manutencoes_equipamento', function (Blueprint $table) {
            $table->dropColumn(['dataSolicitacao', 'servicoRealizado', 'responsavel', 'status']);
        });

        Schema::table('manutencoes_equipamento', function (Blueprint $table) {
            $table->renameColumn('descricao', 'observacoes');
        });
    }
};
