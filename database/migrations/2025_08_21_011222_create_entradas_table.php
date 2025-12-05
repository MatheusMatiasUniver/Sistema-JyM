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
        Schema::create('entradas', function (Blueprint $table) {
            $table->increments('idEntrada');
            $table->unsignedInteger('idCliente')->nullable(false);
            $table->dateTime('dataHora')->nullable(false);
            $table->enum('metodo', ['Reconhecimento Facial', 'Codigo de Acesso'])->nullable(false);

            $table->foreign('idCliente')
                  ->references('idCliente')
                  ->on('clientes')
                  ->onDelete('cascade'); 
                  
            $table->index('dataHora');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entradas');
    }
};