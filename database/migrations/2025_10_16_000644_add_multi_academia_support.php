<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddMultiAcademiaSupport extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'idAcademia')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('idAcademia')
                      ->nullable()
                      ->after('nivelAcesso')
                      ->comment('ID da academia à qual o funcionário está vinculado.');
                
                $table->foreign('idAcademia')
                      ->references('idAcademia')
                      ->on('academias')
                      ->onDelete('set null');
                
                $table->index('idAcademia');
            });
        }

        $tabelas = [
            'clientes' => 'idCliente',
            'plano_assinaturas' => 'idPlano',
            'produtos' => 'idProduto',
            'entradas' => 'idEntrada',
            'venda_produtos' => 'idVenda'
        ];

        foreach ($tabelas as $tabela => $primaryKey) {
            if (Schema::hasTable($tabela) && !Schema::hasColumn($tabela, 'idAcademia')) {
                Schema::table($tabela, function (Blueprint $table) use ($primaryKey) {
                    $table->unsignedInteger('idAcademia')
                          ->nullable()
                          ->after($primaryKey);
                    
                    $table->foreign('idAcademia')
                          ->references('idAcademia')
                          ->on('academias')
                          ->onDelete('cascade');
                    
                    $table->index('idAcademia');
                });
            }
        }

        if (!Schema::hasTable('usuario_academia')) {
            Schema::create('usuario_academia', function (Blueprint $table) {
                $table->unsignedInteger('idUsuario');
                $table->unsignedInteger('idAcademia');
                
                $table->foreign('idUsuario')
                      ->references('idUsuario')
                      ->on('users')
                      ->onDelete('cascade');
                      
                $table->foreign('idAcademia')
                      ->references('idAcademia')
                      ->on('academias')
                      ->onDelete('cascade');
                      
                $table->primary(['idUsuario', 'idAcademia']);
                $table->timestamps();
            });
        }

        $primeiraAcademia = DB::table('academias')->first();
        
        if ($primeiraAcademia) {
            DB::table('users')
              ->where('nivelAcesso', 'Funcionário')
              ->whereNull('idAcademia')
              ->update(['idAcademia' => $primeiraAcademia->idAcademia]);
            
            DB::table('clientes')
              ->whereNull('idAcademia')
              ->update(['idAcademia' => $primeiraAcademia->idAcademia]);
            
            // Atribui produtos
            if (Schema::hasTable('produtos')) {
                DB::table('produtos')
                  ->whereNull('idAcademia')
                  ->update(['idAcademia' => $primeiraAcademia->idAcademia]);
            }
            
            if (Schema::hasTable('venda_produtos')) {
                DB::table('venda_produtos')
                  ->whereNull('idAcademia')
                  ->update(['idAcademia' => $primeiraAcademia->idAcademia]);
            }
            
            if (Schema::hasTable('entradas')) {
                DB::table('entradas')
                  ->whereNull('idAcademia')
                  ->update(['idAcademia' => $primeiraAcademia->idAcademia]);
            }
        }
    }

    public function down(): void
    {        Schema::dropIfExists('usuario_academia');

        $tabelas = ['users', 'clientes', 'plano_assinaturas', 'produtos', 'entradas', 'venda_produtos'];
        
        foreach ($tabelas as $tabela) {
            if (Schema::hasTable($tabela) && Schema::hasColumn($tabela, 'idAcademia')) {
                Schema::table($tabela, function (Blueprint $table) {
                    $table->dropForeign(['idAcademia']);
                    $table->dropColumn('idAcademia');
                });
            }
        }

        echo "✅ Reversão concluída!\n";
    }
}