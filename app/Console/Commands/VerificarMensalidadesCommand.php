<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\VerificarMensalidadesVencidas;

class VerificarMensalidadesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mensalidades:verificar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica mensalidades vencidas e atualiza status dos clientes para inadimplente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando verificação de mensalidades vencidas...');
        
        // Executar o job
        VerificarMensalidadesVencidas::dispatch();
        
        $this->info('Verificação de mensalidades iniciada com sucesso!');
        
        return Command::SUCCESS;
    }
}
