<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('movimentacoes_estoque', function (Blueprint $table) {
            $table->increments('idMovimentacao');
            $table->unsignedInteger('idAcademia')->index();
            $table->unsignedInteger('idProduto')->index();
            $table->enum('tipo', ['entrada', 'saida', 'ajuste', 'devolucao', 'transferencia']);
            $table->integer('quantidade');
            $table->decimal('custoUnitario', 10, 2)->nullable();
            $table->decimal('custoTotal', 10, 2)->nullable();
            $table->string('origem', 30)->nullable();
            $table->unsignedInteger('referenciaId')->nullable();
            $table->string('motivo')->nullable();
            $table->dateTime('dataMovimentacao');
            $table->unsignedInteger('usuarioId')->nullable();
        });

        Schema::table('movimentacoes_estoque', function (Blueprint $table) {
            $table->foreign('idAcademia')->references('idAcademia')->on('academias')->onDelete('cascade');
            $table->foreign('idProduto')->references('idProduto')->on('produtos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('movimentacoes_estoque', function (Blueprint $table) {
            $table->dropForeign(['idAcademia']);
            $table->dropForeign(['idProduto']);
        });
        Schema::dropIfExists('movimentacoes_estoque');
    }
};

