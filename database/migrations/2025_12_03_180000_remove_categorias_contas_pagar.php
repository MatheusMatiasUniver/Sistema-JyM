<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->dropForeign(['idCategoriaContaPagar']);
            $table->dropColumn('idCategoriaContaPagar');
        });

        Schema::dropIfExists('categorias_contas_pagar');
    }

    public function down(): void
    {
        Schema::create('categorias_contas_pagar', function (Blueprint $table) {
            $table->increments('idCategoriaContaPagar');
            $table->unsignedInteger('idAcademia')->nullable()->index();
            $table->string('nome', 100);
            $table->boolean('ativa')->default(true);

            $table->foreign('idAcademia')
                  ->references('idAcademia')
                  ->on('academias')
                  ->onDelete('cascade');
        });

        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->unsignedInteger('idCategoriaContaPagar')->nullable()->after('idFuncionario');
            $table->foreign('idCategoriaContaPagar')
                  ->references('idCategoriaContaPagar')
                  ->on('categorias_contas_pagar')
                  ->onDelete('set null');
        });
    }
};
