<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('marcas', function (Blueprint $table) {
            $table->increments('idMarca');
            $table->unsignedInteger('idAcademia')->nullable()->index();
            $table->string('nome');
            $table->string('paisOrigem', 50)->nullable();
            $table->string('site')->nullable();
            $table->boolean('ativo')->default(true);
        });

        Schema::table('marcas', function (Blueprint $table) {
            $table->foreign('idAcademia')->references('idAcademia')->on('academias')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('marcas', function (Blueprint $table) {
            $table->dropForeign(['idAcademia']);
        });
        Schema::dropIfExists('marcas');
    }
};