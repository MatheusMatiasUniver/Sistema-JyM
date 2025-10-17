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
        Schema::create('mensalidades', function (Blueprint $table) {
            $table->increments('idMensalidade');
            $table->unsignedInteger('idCliente')->nullable(false);
            $table->unsignedInteger('idPlano')->nullable(false);
            
            // ✅ COLUNA ADICIONADA
            $table->unsignedInteger('idAcademia')->nullable(false)->comment('Identificador da academia');

            $table->date('dataVencimento')->nullable(false);
            $table->decimal('valor', 10, 2)->nullable(false);
            $table->enum('status', ['Paga', 'Pendente'])->nullable(false);
            $table->date('dataPagamento')->nullable();
            
            $table->enum('formaPagamento', [
                'Dinheiro',
                'PIX',
                'Cartão de Crédito',
                'Cartão de Débito',
                'Transferência Bancária',
                'Boleto'
            ])->nullable()->comment('Forma de pagamento utilizada');

            // Foreign keys
            $table->foreign('idCliente')
                  ->references('idCliente')
                  ->on('clientes')
                  ->onDelete('cascade');
            
            $table->foreign('idPlano')
                  ->references('idPlano')
                  ->on('plano_assinaturas')
                  ->onDelete('restrict');

            // ✅ CHAVE ESTRANGEIRA ADICIONADA
            $table->foreign('idAcademia')
                  ->references('idAcademia')
                  ->on('academias')
                  ->onDelete('cascade');
            
            // Índices para melhorar performance
            $table->index('dataVencimento');
            $table->index('status');
            $table->index('dataPagamento');
            $table->index('idAcademia'); // ✅ ÍNDICE ADICIONADO
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensalidades');
    }
};