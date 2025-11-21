<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contas_receber', function (Blueprint $table) {
            $table->increments('idContaReceber');
            $table->unsignedInteger('idAcademia')->index();
            $table->unsignedInteger('idCliente')->nullable()->index();
            $table->unsignedInteger('documentoRef')->nullable();
            $table->string('descricao');
            $table->decimal('valorTotal', 10, 2);
            $table->enum('status', ['aberta','recebida','cancelada'])->default('aberta');
            $table->date('dataVencimento')->nullable();
            $table->date('dataRecebimento')->nullable();
            $table->string('formaRecebimento')->nullable();
        });

        Schema::table('contas_receber', function (Blueprint $table) {
            $table->foreign('idAcademia')->references('idAcademia')->on('academias')->onDelete('cascade');
            $table->foreign('idCliente')->references('idCliente')->on('clientes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('contas_receber', function (Blueprint $table) {
            $table->dropForeign(['idAcademia']);
            $table->dropForeign(['idCliente']);
        });
        Schema::dropIfExists('contas_receber');
    }
};