<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('face_descriptors', function (Blueprint $table) {
            $table->id(); // ID primário para esta tabela

            // Altere esta linha:
            $table->unsignedInteger('cliente_id'); // Garante que o tipo seja UNSIGNED INT, como idCliente na tabela clientes

            $table->json('descriptor'); // Armazena o array de descritores como JSON
            $table->timestamps();

            // Adicione esta linha para a chave estrangeira, referenciando o idCliente
            $table->foreign('cliente_id')
                  ->references('idCliente')
                  ->on('clientes')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('face_descriptors');
    }
};