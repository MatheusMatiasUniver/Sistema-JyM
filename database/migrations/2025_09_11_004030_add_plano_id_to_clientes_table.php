<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Adiciona a coluna idPlano, permite nulos (nullable) e define depois de 'idUsuario'
            $table->unsignedInteger('idPlano')->nullable()->after('idUsuario');
            // Adiciona a chave estrangeira referenciando a tabela 'plano_assinaturas'
            // 'onDelete('set null')' significa que se um plano for excluído, o idPlano nos clientes se tornará nulo
            $table->foreign('idPlano')
                  ->references('idPlano')
                  ->on('plano_assinaturas')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Remove a chave estrangeira antes de remover a coluna
            $table->dropForeign(['idPlano']);
            // Remove a coluna idPlano
            $table->dropColumn('idPlano');
        });
    }
};