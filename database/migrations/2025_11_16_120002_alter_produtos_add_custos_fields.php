<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->decimal('precoCompra', 10, 2)->nullable();
            $table->decimal('custoMedio', 10, 2)->nullable();
            $table->integer('estoqueMinimo')->default(0);
            $table->string('unidadeMedida', 10)->nullable();
            $table->string('codigoBarras', 40)->nullable();
            $table->boolean('vendavel')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn(['precoCompra', 'custoMedio', 'estoqueMinimo', 'unidadeMedida', 'codigoBarras', 'vendavel']);
        });
    }
};

