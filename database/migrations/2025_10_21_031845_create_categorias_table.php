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
        Schema::create('categorias', function (Blueprint $table) {
            $table->increments('idCategoria');
            $table->string('nome', 100)->nullable(false);
            $table->text('descricao')->nullable();
            $table->enum('status', ['Ativo', 'Inativo'])->default('Ativo');
            $table->unsignedInteger('idAcademia')->nullable(false);
            $table->timestamps();

            $table->foreign('idAcademia')
                  ->references('idAcademia')
                  ->on('academias')
                  ->onDelete('cascade');

            $table->index('nome');
            $table->index('status');
            $table->index('idAcademia');
            
            $table->unique(['nome', 'idAcademia']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
