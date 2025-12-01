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
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropUnique('produtos_codigo_barras_unique');
            $table->dropColumn(['unidadeMedida', 'codigoBarras', 'vendavel']);
        });
    }

    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->string('unidadeMedida', 10)->nullable();
            $table->string('codigoBarras', 40)->nullable();
            $table->boolean('vendavel')->default(true);
        });
    }
};
