<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipamentos', function (Blueprint $table) {
            $table->increments('idEquipamento');
            $table->unsignedInteger('idAcademia')->index();
            $table->string('descricao');
            $table->string('fabricante')->nullable();
            $table->string('modelo')->nullable();
            $table->string('numeroSerie')->nullable();
            $table->date('dataAquisicao')->nullable();
            $table->decimal('valorAquisicao', 10, 2)->nullable();
            $table->date('garantiaFim')->nullable();
            $table->string('centroCusto')->nullable();
            $table->enum('status', ['Ativo', 'Em Manutenção', 'Desativado'])->default('Ativo');
        });

        Schema::table('equipamentos', function (Blueprint $table) {
            $table->foreign('idAcademia')->references('idAcademia')->on('academias')->onDelete('cascade');
        });

        Schema::create('manutencoes_equipamento', function (Blueprint $table) {
            $table->increments('idManutencao');
            $table->unsignedInteger('idEquipamento')->index();
            $table->enum('tipo', ['preventiva','corretiva']);
            $table->date('dataProgramada')->nullable();
            $table->date('dataExecucao')->nullable();
            $table->decimal('custo', 10, 2)->nullable();
            $table->unsignedInteger('fornecedorId')->nullable();
            $table->text('observacoes')->nullable();
        });

        Schema::table('manutencoes_equipamento', function (Blueprint $table) {
            $table->foreign('idEquipamento')->references('idEquipamento')->on('equipamentos')->onDelete('cascade');
            $table->foreign('fornecedorId')->references('idFornecedor')->on('fornecedores')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('manutencoes_equipamento', function (Blueprint $table) {
            $table->dropForeign(['idEquipamento']);
            $table->dropForeign(['fornecedorId']);
        });
        Schema::dropIfExists('manutencoes_equipamento');

        Schema::table('equipamentos', function (Blueprint $table) {
            $table->dropForeign(['idAcademia']);
        });
        Schema::dropIfExists('equipamentos');
    }
};

