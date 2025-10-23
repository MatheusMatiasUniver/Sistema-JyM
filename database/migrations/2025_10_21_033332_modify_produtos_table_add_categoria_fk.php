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
            $table->unsignedInteger('idCategoria')->nullable()->after('nome');
            
            $table->foreign('idCategoria')
                  ->references('idCategoria')
                  ->on('categorias')
                  ->onDelete('set null');
            
            $table->index('idCategoria');
        });
        
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->string('categoria', 50)->nullable()->after('nome');
        });
        
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropForeign(['idCategoria']);
            $table->dropIndex(['idCategoria']);
            $table->dropColumn('idCategoria');
        });
    }
};
