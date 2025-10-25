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
        // Adicionar onDelete e onUpdate nas foreign keys que estão faltando
        
        // Tabela itens_vendas - adicionar onDelete cascade
        if (Schema::hasTable('itens_vendas')) {
            Schema::table('itens_vendas', function (Blueprint $table) {
                // Remover foreign keys existentes
                $table->dropForeign(['idVenda']);
                $table->dropForeign(['idProduto']);
                
                // Recriar com onDelete cascade
                $table->foreign('idVenda')
                      ->references('idVenda')
                      ->on('venda_produtos')
                      ->onDelete('cascade');
                      
                $table->foreign('idProduto')
                      ->references('idProduto')
                      ->on('produtos')
                      ->onDelete('cascade');
            });
        }
        
        // Tabela venda_produtos - adicionar onDelete set null
        if (Schema::hasTable('venda_produtos')) {
            Schema::table('venda_produtos', function (Blueprint $table) {
                // Remover foreign keys existentes
                $table->dropForeign(['idCliente']);
                $table->dropForeign(['idUsuario']);
                
                // Recriar com onDelete set null
                $table->foreign('idCliente')
                      ->references('idCliente')
                      ->on('clientes')
                      ->onDelete('set null');
                      
                $table->foreign('idUsuario')
                      ->references('idUsuario')
                      ->on('users')
                      ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter as alterações
        if (Schema::hasTable('itens_vendas')) {
            Schema::table('itens_vendas', function (Blueprint $table) {
                $table->dropForeign(['idVenda']);
                $table->dropForeign(['idProduto']);
                
                $table->foreign('idVenda')
                      ->references('idVenda')
                      ->on('venda_produtos');
                      
                $table->foreign('idProduto')
                      ->references('idProduto')
                      ->on('produtos');
            });
        }
        
        if (Schema::hasTable('venda_produtos')) {
            Schema::table('venda_produtos', function (Blueprint $table) {
                $table->dropForeign(['idCliente']);
                $table->dropForeign(['idUsuario']);
                
                $table->foreign('idCliente')
                      ->references('idCliente')
                      ->on('clientes');
                      
                $table->foreign('idUsuario')
                      ->references('idUsuario')
                      ->on('users');
            });
        }
    }
};
