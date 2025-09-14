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
            $table->increments('idCliente'); // Use increments para chave primária auto-increment
            $table->string('nome', 100)->nullable(false);
            $table->string('cpf', 14)->unique()->nullable(false); // CPF único
            $table->date('dataNascimento')->nullable(false);
            $table->enum('status', ['Ativo', 'Inativo'])->nullable(false);
            $table->string('foto', 255)->nullable();
            $table->unsignedInteger('idUsuario')->nullable(); // Chave estrangeira para User
            
            // NENHUMA REFERÊNCIA A idPlano AQUI!
            // NENHUMA CHAVE ESTRANGEIRA PARA idPlano AQUI!

            $table->foreign('idUsuario')
                  ->references('idUsuario')
                  ->on('users')
                  ->onDelete('set null'); 
                  
            // Se você tiver outras chaves estrangeiras que não sejam para idPlano, mantenha-as.
            // Mas a lógica é: crie a coluna ANTES de criar a FK.
            // Exemplo: $table->unsignedInteger('alguma_coluna_id'); $table->foreign('alguma_coluna_id')->references('id')->on('outra_tabela');
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