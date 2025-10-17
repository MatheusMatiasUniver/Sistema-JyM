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
            $table->string('email', 150)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->enum('status', ['Ativo', 'Inativo', 'Inadimplente', 'Suspenso', 'Pendente'])->nullable(false);
            $table->string('foto', 255)->nullable();
            $table->unsignedInteger('idUsuario')->nullable();

            $table->foreign('idUsuario')
                  ->references('idUsuario')
                  ->on('users')
                  ->onDelete('set null');
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