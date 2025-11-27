<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('materiais', function (Blueprint $table) {
            $table->increments('idMaterial');
            $table->unsignedInteger('idAcademia')->index();
            $table->string('descricao');
            $table->integer('estoque')->default(0);
            $table->string('unidadeMedida', 10)->nullable();
            $table->integer('estoqueMinimo')->default(0);
        });

        Schema::table('materiais', function (Blueprint $table) {
            $table->foreign('idAcademia')->references('idAcademia')->on('academias')->onDelete('cascade');
        });

        Schema::create('requisicoes_materiais', function (Blueprint $table) {
            $table->increments('idRequisicao');
            $table->unsignedInteger('idAcademia')->index();
            $table->unsignedInteger('idMaterial')->index();
            $table->integer('quantidade');
            $table->string('centroCusto');
            $table->dateTime('data');
            $table->unsignedInteger('usuarioId')->nullable();
            $table->string('motivo')->nullable();
        });

        Schema::table('requisicoes_materiais', function (Blueprint $table) {
            $table->foreign('idAcademia')->references('idAcademia')->on('academias')->onDelete('cascade');
            $table->foreign('idMaterial')->references('idMaterial')->on('materiais')->onDelete('cascade');
        });

        Schema::create('movimentacoes_materiais', function (Blueprint $table) {
            $table->increments('idMovMaterial');
            $table->unsignedInteger('idAcademia')->index();
            $table->unsignedInteger('idMaterial')->index();
            $table->enum('tipo', ['entrada','saida','ajuste']);
            $table->integer('quantidade');
            $table->string('origem', 30)->nullable();
            $table->unsignedInteger('referenciaId')->nullable();
            $table->string('motivo')->nullable();
            $table->dateTime('dataMovimentacao');
            $table->unsignedInteger('usuarioId')->nullable();
        });

        Schema::table('movimentacoes_materiais', function (Blueprint $table) {
            $table->foreign('idAcademia')->references('idAcademia')->on('academias')->onDelete('cascade');
            $table->foreign('idMaterial')->references('idMaterial')->on('materiais')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('movimentacoes_materiais', function (Blueprint $table) {
            $table->dropForeign(['idAcademia']);
            $table->dropForeign(['idMaterial']);
        });
        Schema::dropIfExists('movimentacoes_materiais');

        Schema::table('requisicoes_materiais', function (Blueprint $table) {
            $table->dropForeign(['idAcademia']);
            $table->dropForeign(['idMaterial']);
        });
        Schema::dropIfExists('requisicoes_materiais');

        Schema::table('materiais', function (Blueprint $table) {
            $table->dropForeign(['idAcademia']);
        });
        Schema::dropIfExists('materiais');
    }
};

