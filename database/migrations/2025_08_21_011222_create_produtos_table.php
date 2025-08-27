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
        Schema::create('produtos', function (Blueprint $table) {
            $table->increments('idProduto');
            $table->string('nome', 100)->nullable();
            $table->string('categoria', 50)->nullable();
            $table->decimal('preco', 10, 2)->nullable();
            $table->integer('estoque')->nullable();
            $table->text('descricao')->nullable();
            $table->string('imagem', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};