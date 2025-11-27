<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ajustes_sistema', function (Blueprint $table) {
            $table->increments('idAjuste');
            $table->unsignedInteger('idAcademia')->index();
            $table->unsignedInteger('idFornecedorFolha')->nullable()->index();
            $table->unsignedTinyInteger('diaVencimentoSalarios')->default(5);
        });

        Schema::table('ajustes_sistema', function (Blueprint $table) {
            $table->foreign('idAcademia')->references('idAcademia')->on('academias')->onDelete('cascade');
            $table->foreign('idFornecedorFolha')->references('idFornecedor')->on('fornecedores')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('ajustes_sistema', function (Blueprint $table) {
            $table->dropForeign(['idAcademia']);
            $table->dropForeign(['idFornecedorFolha']);
        });
        Schema::dropIfExists('ajustes_sistema');
    }
};