<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ajustes_sistema', function (Blueprint $table) {
            $table->dropForeign(['idFornecedorFolha']);
            $table->dropColumn('idFornecedorFolha');
        });
    }

    public function down(): void
    {
        Schema::table('ajustes_sistema', function (Blueprint $table) {
            $table->unsignedInteger('idFornecedorFolha')->nullable()->index();
            $table->foreign('idFornecedorFolha')->references('idFornecedor')->on('fornecedores')->onDelete('set null');
        });
    }
};