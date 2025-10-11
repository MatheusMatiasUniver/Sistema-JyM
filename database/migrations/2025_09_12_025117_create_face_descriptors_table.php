<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('face_descriptors', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('cliente_id');

            $table->json('descriptor');
            $table->timestamps();

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