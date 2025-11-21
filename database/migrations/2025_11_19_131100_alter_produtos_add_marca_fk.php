<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->unsignedInteger('idMarca')->nullable()->index();
        });

        Schema::table('produtos', function (Blueprint $table) {
            $table->foreign('idMarca')->references('idMarca')->on('marcas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropForeign(['idMarca']);
            $table->dropColumn('idMarca');
        });
    }
};