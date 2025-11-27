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
        Schema::create('academias', function (Blueprint $table) {
            $table->increments('idAcademia');
            $table->string('nome', 100)->nullable();
            $table->string('CNPJ', 18)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('endereco')->nullable();
            $table->string('responsavel', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academias');
    }
};