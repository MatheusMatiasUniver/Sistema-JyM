<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Cliente;
use App\Models\Mensalidade;
use Carbon\Carbon;

class VerificarMensalidadesVencidas implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Iniciando verificação de mensalidades vencidas');
            
            // Buscar clientes ativos que podem ter mensalidades vencidas
            $clientesAtivos = Cliente::where('status', 'Ativo')->get();
            
            $clientesAtualizados = 0;
            
            foreach ($clientesAtivos as $cliente) {
                $mensalidadeVencida = Mensalidade::where('idCliente', $cliente->idCliente)
                    ->where('status', 'Pendente')
                    ->where('dataVencimento', '<', Carbon::today())
                    ->exists();
                
                if ($mensalidadeVencida) {
                    // Atualizar status do cliente para Inadimplente
                    $cliente->update(['status' => 'Inadimplente']);
                    $clientesAtualizados++;
                    
                    Log::info("Cliente ID {$cliente->idCliente} ({$cliente->nome}) marcado como Inadimplente devido a mensalidade vencida");
                }
            }
            
            Log::info("Verificação de mensalidades concluída. {$clientesAtualizados} clientes atualizados para Inadimplente");
            
        } catch (\Exception $e) {
            Log::error('Erro ao verificar mensalidades vencidas: ' . $e->getMessage(), [
                'erro_detalhes' => $e->getTraceAsString(),
            ]);
        }
    }
}
