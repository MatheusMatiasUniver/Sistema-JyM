<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contas_pagar', function (Blueprint $table) {
            $table->increments('idContaPagar');
            $table->unsignedInteger('idAcademia')->index();
            $table->unsignedInteger('idFornecedor')->index();
            $table->unsignedInteger('documentoRef')->nullable();
            $table->string('descricao');
            $table->decimal('valorTotal', 10, 2);
            $table->enum('status', ['aberta','paga','cancelada'])->default('aberta');
            $table->date('dataVencimento');
            $table->date('dataPagamento')->nullable();
            $table->string('formaPagamento')->nullable();
        });

        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->foreign('idAcademia')->references('idAcademia')->on('academias')->onDelete('cascade');
            $table->foreign('idFornecedor')->references('idFornecedor')->on('fornecedores')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->dropForeign(['idAcademia']);
            $table->dropForeign(['idFornecedor']);
        });
        Schema::dropIfExists('contas_pagar');
    }
};