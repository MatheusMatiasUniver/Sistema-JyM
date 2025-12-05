<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fornecedores', function (Blueprint $table) {
            $table->increments('idFornecedor');
            $table->unsignedInteger('idAcademia')->nullable()->index();
            $table->string('razaoSocial');
            $table->string('cnpjCpf', 20)->nullable();
            $table->string('inscricaoEstadual', 30)->nullable();
            $table->string('contato', 100)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('endereco')->nullable();
            $table->string('condicaoPagamentoPadrao', 100)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::table('fornecedores', function (Blueprint $table) {
            $table->foreign('idAcademia')->references('idAcademia')->on('academias')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('fornecedores', function (Blueprint $table) {
            $table->dropForeign(['idAcademia']);
        });
        Schema::dropIfExists('fornecedores');
    }
};

