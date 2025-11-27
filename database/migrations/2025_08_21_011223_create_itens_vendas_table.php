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
        Schema::create('itens_vendas', function (Blueprint $table) {
            $table->increments('idItem');
            $table->unsignedInteger('idVenda')->nullable();
            $table->unsignedInteger('idProduto')->nullable();
            $table->integer('quantidade')->nullable();
            $table->decimal('precoUnitario', 10, 2)->nullable();

            $table->foreign('idVenda')
                  ->references('idVenda')
                  ->on('venda_produtos');
            $table->foreign('idProduto')
                  ->references('idProduto')
                  ->on('produtos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itens_vendas');
    }
};