<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE contas_pagar MODIFY idFornecedor INT UNSIGNED NULL');

        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->unsignedInteger('idFuncionario')->nullable()->after('idFornecedor');
            $table->foreign('idFuncionario')->references('idUsuario')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->dropForeign(['idFuncionario']);
            $table->dropColumn('idFuncionario');
        });
        DB::statement('ALTER TABLE contas_pagar MODIFY idFornecedor INT UNSIGNED NOT NULL');
    }
};