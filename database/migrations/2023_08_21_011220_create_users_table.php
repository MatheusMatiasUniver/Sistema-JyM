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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('idUsuario'); 
            $table->string('nome', 100)->nullable(false); 
            $table->string('usuario', 50)->unique()->nullable(false); 
            $table->string('email', 150)->nullable(); 
            $table->string('senha', 255)->nullable(false); 
            $table->enum('nivelAcesso', ['Administrador', 'Funcionário'])->nullable(false); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};