<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->increments('idCompra');
            $table->unsignedInteger('idAcademia')->index();
            $table->unsignedInteger('idFornecedor')->index();
            $table->dateTime('dataEmissao');
            $table->enum('status', ['aberta','recebida','cancelada'])->default('aberta');
            $table->decimal('valorProdutos', 10, 2)->default(0);
            $table->decimal('valorFrete', 10, 2)->default(0);
            $table->decimal('valorDesconto', 10, 2)->default(0);
            $table->decimal('valorImpostos', 10, 2)->default(0);
            $table->decimal('valorTotal', 10, 2)->default(0);
            $table->text('observacoes')->nullable();
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->foreign('idAcademia')->references('idAcademia')->on('academias')->onDelete('cascade');
            $table->foreign('idFornecedor')->references('idFornecedor')->on('fornecedores')->onDelete('restrict');
        });

        Schema::create('itens_compras', function (Blueprint $table) {
            $table->increments('idItemCompra');
            $table->unsignedInteger('idCompra')->index();
            $table->unsignedInteger('idProduto')->index();
            $table->integer('quantidade');
            $table->decimal('precoUnitario', 10, 2);
            $table->decimal('descontoPercent', 5, 2)->nullable();
            $table->decimal('custoRateadoTotal', 10, 2)->nullable();
        });

        Schema::table('itens_compras', function (Blueprint $table) {
            $table->foreign('idCompra')->references('idCompra')->on('compras')->onDelete('cascade');
            $table->foreign('idProduto')->references('idProduto')->on('produtos')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('itens_compras', function (Blueprint $table) {
            $table->dropForeign(['idCompra']);
            $table->dropForeign(['idProduto']);
        });
        Schema::dropIfExists('itens_compras');

        Schema::table('compras', function (Blueprint $table) {
            $table->dropForeign(['idAcademia']);
            $table->dropForeign(['idFornecedor']);
        });
        Schema::dropIfExists('compras');
    }
};