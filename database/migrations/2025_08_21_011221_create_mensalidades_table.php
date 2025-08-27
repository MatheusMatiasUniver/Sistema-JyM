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
        Schema::create('mensalidades', function (Blueprint $table) {
            $table->increments('idMensalidade');
            $table->unsignedInteger('idCliente')->nullable(false);
            $table->date('dataVencimento')->nullable(false);
            $table->decimal('valor', 10, 2)->nullable(false);
            $table->enum('status', ['Paga', 'Pendente'])->nullable(false);
            $table->date('dataPagamento')->nullable();

            $table->foreign('idCliente')
                  ->references('idCliente')
                  ->on('clientes')
                  ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensalidades');
    }
};