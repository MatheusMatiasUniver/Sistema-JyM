<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Academia;

return new class extends Migration
{
    public function up(): void
    {
        echo "Iniciando a migração para suporte multi-academia...\n";

        // ✅ 1. SEMPRE cria a coluna idAcademia na tabela users
        if (!Schema::hasColumn('users', 'idAcademia')) {
            echo "Adicionando 'idAcademia' à tabela 'users'...\n";
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
            echo "✅ Coluna 'idAcademia' adicionada à tabela 'users'.\n";
        }

        // ✅ 2. Adiciona idAcademia às outras tabelas
        $tabelas = [
            'clientes' => 'idCliente',
            'plano_assinaturas' => 'idPlano',
            'produtos' => 'idProduto',
            'entradas' => 'idEntrada',
            'venda_produtos' => 'idVenda'
        ];

        foreach ($tabelas as $tabela => $primaryKey) {
            if (Schema::hasTable($tabela) && !Schema::hasColumn($tabela, 'idAcademia')) {
                echo "Adicionando 'idAcademia' à tabela '{$tabela}'...\n";
                
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
                
                echo "✅ Coluna 'idAcademia' adicionada à tabela '{$tabela}'.\n";
            }
        }

        // ✅ 3. Cria a tabela pivot usuario_academia
        if (!Schema::hasTable('usuario_academia')) {
            echo "Criando tabela pivot 'usuario_academia'...\n";
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
            echo "✅ Tabela 'usuario_academia' criada.\n";
        }

        // ✅ 4. SOMENTE AGORA atribui registros existentes à primeira academia (se existir)
        $primeiraAcademia = Academia::first();
        
        if ($primeiraAcademia) {
            echo "Migrando dados existentes para academia '{$primeiraAcademia->nome}'...\n";
            
            // Atribui usuários funcionários
            DB::table('users')
              ->where('nivelAcesso', 'Funcionário')
              ->whereNull('idAcademia')
              ->update(['idAcademia' => $primeiraAcademia->idAcademia]);
            
            // Atribui clientes
            DB::table('clientes')
              ->whereNull('idAcademia')
              ->update(['idAcademia' => $primeiraAcademia->idAcademia]);
            
            // Atribui produtos
            if (Schema::hasTable('produtos')) {
                DB::table('produtos')
                  ->whereNull('idAcademia')
                  ->update(['idAcademia' => $primeiraAcademia->idAcademia]);
            }
            
            // Atribui vendas
            if (Schema::hasTable('venda_produtos')) {
                DB::table('venda_produtos')
                  ->whereNull('idAcademia')
                  ->update(['idAcademia' => $primeiraAcademia->idAcademia]);
            }
            
            // Atribui entradas
            if (Schema::hasTable('entradas')) {
                DB::table('entradas')
                  ->whereNull('idAcademia')
                  ->update(['idAcademia' => $primeiraAcademia->idAcademia]);
            }
            
            echo "✅ Dados migrados com sucesso!\n";
        } else {
            echo "⚠️ Nenhuma academia encontrada. Os dados serão migrados quando academias forem criadas.\n";
        }

        echo "✅ Suporte multi-academia adicionado com sucesso!\n";
    }

    public function down(): void
    {
        echo "Revertendo suporte multi-academia...\n";

        // Remove tabela pivot
        Schema::dropIfExists('usuario_academia');

        // Remove colunas das tabelas
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
};