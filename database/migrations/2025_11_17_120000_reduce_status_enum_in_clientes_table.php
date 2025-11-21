<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('clientes')->whereIn('status', ['Suspenso', 'Pendente'])->update(['status' => 'Inativo']);
        DB::statement("ALTER TABLE `clientes` MODIFY COLUMN `status` ENUM('Ativo', 'Inativo', 'Inadimplente') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `clientes` MODIFY COLUMN `status` ENUM('Ativo', 'Inativo', 'Inadimplente', 'Suspenso', 'Pendente') NOT NULL");
    }
};
