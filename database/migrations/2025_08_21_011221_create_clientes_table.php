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
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('idCliente');
            $table->string('nome', 100)->nullable(false);
            $table->string('cpf', 14)->unique()->nullable(false);
            $table->date('dataNascimento')->nullable(false);
            $table->enum('status', ['Ativo', 'Inativo'])->nullable(false);
            $table->binary('foto')->nullable(); 

            $table->unsignedInteger('idUsuario')->nullable();
            $table->foreign('idUsuario')
                  ->references('idUsuario')
                  ->on('users') 
                  ->onDelete('set null'); 
            $table->index('cpf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};