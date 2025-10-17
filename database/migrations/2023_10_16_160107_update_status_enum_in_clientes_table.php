<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Modifica o ENUM para incluir todos os status necessários
        DB::statement("ALTER TABLE `clientes` MODIFY COLUMN `status` ENUM('Ativo', 'Inativo', 'Inadimplente', 'Suspenso', 'Pendente') NOT NULL");
    }

    public function down(): void
    {
        // Reverte para o ENUM original
        DB::statement("ALTER TABLE `clientes` MODIFY COLUMN `status` ENUM('Ativo', 'Inativo') NOT NULL");
    }
};