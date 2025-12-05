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
     * 
     * Critério de Inadimplência:
     * - Verifica a MENSALIDADE MAIS RECENTE de cada cliente
     * - Se a mensalidade mais recente está Pendente E vencida → Inadimplente
     * - Se a mensalidade mais recente está Paga OU (Pendente e não vencida) → Ativo
     */
    public function handle(): void
    {
        try {
            Log::info('Iniciando verificação de mensalidades vencidas');
            
            $clientesAtivos = Cliente::where('status', 'Ativo')->get();
            
            $clientesAtualizados = 0;
            
            foreach ($clientesAtivos as $cliente) {
                $mensalidadeMaisRecente = Mensalidade::where('idCliente', $cliente->idCliente)
                    ->orderBy('dataVencimento', 'desc')
                    ->first();
                
                if ($mensalidadeMaisRecente && 
                    $mensalidadeMaisRecente->status === 'Pendente' && 
                    $mensalidadeMaisRecente->dataVencimento < Carbon::today()) {
                    
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
