<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categorias_contas_pagar', function (Blueprint $table) {
            $table->increments('idCategoriaContaPagar');
            $table->unsignedInteger('idAcademia')->index();
            $table->string('nome');
            $table->boolean('ativa')->default(true);
        });

        Schema::table('categorias_contas_pagar', function (Blueprint $table) {
            $table->foreign('idAcademia')->references('idAcademia')->on('academias')->onDelete('cascade');
        });

        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->unsignedInteger('idCategoriaContaPagar')->nullable()->after('idFornecedor');
            $table->foreign('idCategoriaContaPagar')->references('idCategoriaContaPagar')->on('categorias_contas_pagar')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->dropForeign(['idCategoriaContaPagar']);
            $table->dropColumn('idCategoriaContaPagar');
        });
        Schema::table('categorias_contas_pagar', function (Blueprint $table) {
            $table->dropForeign(['idAcademia']);
        });
        Schema::dropIfExists('categorias_contas_pagar');
    }
};