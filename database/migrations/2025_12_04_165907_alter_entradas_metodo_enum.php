<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primeiro converter para VARCHAR para permitir a atualização
        DB::statement("ALTER TABLE entradas MODIFY metodo VARCHAR(50) NOT NULL");
        
        // Atualizar registros existentes
        DB::statement("UPDATE entradas SET metodo = 'Codigo de Acesso' WHERE metodo IN ('CPF/Senha', 'Manual', 'CodigoAcesso')");
        
        // Agora converter para o novo ENUM
        DB::statement("ALTER TABLE entradas MODIFY metodo ENUM('Reconhecimento Facial', 'Codigo de Acesso') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE entradas MODIFY metodo ENUM('Reconhecimento Facial', 'CPF/Senha', 'Manual') NOT NULL");
    }
};
