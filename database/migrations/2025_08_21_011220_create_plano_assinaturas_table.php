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
        Schema::create('plano_assinaturas', function (Blueprint $table) {
            $table->increments('idPlano');
            $table->string('nome', 100)->nullable();
            $table->text('descricao')->nullable();
            $table->decimal('valor', 10, 2)->nullable();
            $table->integer('duracaoDias')->nullable();
            $table->unsignedInteger('idAcademia')->nullable(); 
            $table->timestamps();

            $table->foreign('idAcademia')
                  ->references('idAcademia')
                  ->on('academias')
                  ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plano_assinaturas');
    }
};