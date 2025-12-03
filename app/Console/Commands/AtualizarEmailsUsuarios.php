<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class AtualizarEmailsUsuarios extends Command
{
    protected $signature = 'users:atualizar-emails';

    protected $description = 'Atualiza emails vazios de usuários para permitir migration de email obrigatório';

    public function handle()
    {
        $users = User::whereNull('email')->orWhere('email', '')->get();
        
        if ($users->isEmpty()) {
            $this->info('✓ Todos os usuários já possuem email.');
            return 0;
        }

        $this->info("Encontrados {$users->count()} usuários sem email.");
        
        foreach ($users as $user) {
            $emailGerado = $user->usuario . '@sistemajym.local';
            $user->email = $emailGerado;
            $user->save();
            
            $this->line("✓ {$user->nome} ({$user->usuario}) -> {$emailGerado}");
        }

        $this->info("\n✓ {$users->count()} emails atualizados com sucesso!");
        return 0;
    }
}
