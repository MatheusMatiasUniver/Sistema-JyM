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
        Schema::create('venda_produtos', function (Blueprint $table) {
            $table->increments('idVenda');
            $table->unsignedInteger('idCliente')->nullable();
            
            // ✅ CAMPO ADICIONADO: Para rastrear o funcionário que fez a venda
            $table->unsignedInteger('idUsuario')->nullable(); 

            $table->dateTime('dataVenda')->nullable();
            $table->decimal('valorTotal', 10, 2)->nullable();
            $table->string('formaPagamento', 50)->nullable();

            // Chaves Estrangeiras
            $table->foreign('idCliente')
                  ->references('idCliente')
                  ->on('clientes'); 

            // ✅ CHAVE ESTRANGEIRA ADICIONADA
            $table->foreign('idUsuario')
                  ->references('idUsuario')
                  ->on('users');

            // ✅ ÍNDICE ADICIONADO
            $table->index('idUsuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venda_produtos');
    }
};