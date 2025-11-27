<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropForeign(['idCliente']);
        });
        DB::statement('ALTER TABLE entradas MODIFY idCliente INT UNSIGNED NULL');
        Schema::table('entradas', function (Blueprint $table) {
            $table->foreign('idCliente')->references('idCliente')->on('clientes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropForeign(['idCliente']);
        });
        DB::statement('ALTER TABLE entradas MODIFY idCliente INT UNSIGNED NOT NULL');
        Schema::table('entradas', function (Blueprint $table) {
            $table->foreign('idCliente')->references('idCliente')->on('clientes')->onDelete('cascade');
        });
    }
};