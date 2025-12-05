<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\AjusteSistema;
use Carbon\Carbon;

class GerarSalariosMensais implements ShouldQueue
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
     * Gera contas a pagar referentes aos salários mensais dos funcionários
     * com base no dia de vencimento configurado em cada academia.
     */
    public function handle(): void
    {
        try {
            Log::info('Iniciando geração automática de salários mensais');
            
            $hoje = Carbon::today();
            $diaHoje = (int) $hoje->day;
            $anoMesAtual = $hoje->format('Y-m');
            
            $academias = DB::table('academias')->get();
            $salariosCriados = 0;
            
            foreach ($academias as $academia) {
                $ajuste = AjusteSistema::obterOuCriarParaAcademia($academia->idAcademia);
                $diaVencimento = (int) $ajuste->diaVencimentoSalarios;
                
                if ($diaHoje !== $diaVencimento) {
                    continue;
                }
                
                $funcionarios = User::where('idAcademia', $academia->idAcademia)
                    ->where('nivelAcesso', 'Funcionário')
                    ->where('salarioMensal', '>', 0)
                    ->get();
                
                foreach ($funcionarios as $funcionario) {
                    $jaExiste = DB::table('contas_pagar')
                        ->where('idAcademia', $academia->idAcademia)
                        ->where('idFuncionario', $funcionario->idUsuario)
                        ->where('origem', 'salario')
                        ->whereRaw("DATE_FORMAT(dataVencimento, '%Y-%m') = ?", [$anoMesAtual])
                        ->exists();
                    
                    if ($jaExiste) {
                        Log::info("Salário do mês {$anoMesAtual} já existe para funcionário {$funcionario->idUsuario}");
                        continue;
                    }
                    
                    $dataVencimento = Carbon::createFromFormat('Y-m-d', $hoje->format('Y-m') . '-' . str_pad($diaVencimento, 2, '0', STR_PAD_LEFT));
                    
                    DB::table('contas_pagar')->insert([
                        'idAcademia' => $academia->idAcademia,
                        'idFuncionario' => $funcionario->idUsuario,
                        'idFornecedor' => null,
                        'origem' => 'salario',
                        'documentoRef' => null,
                        'descricao' => "Salário {$funcionario->nome} - " . $hoje->locale('pt_BR')->translatedFormat('F/Y'),
                        'valorTotal' => $funcionario->salarioMensal,
                        'status' => 'aberta',
                        'dataVencimento' => $dataVencimento,
                        'dataPagamento' => null,
                        'formaPagamento' => null,
                    ]);
                    
                    $salariosCriados++;
                    Log::info("Salário gerado: Funcionário {$funcionario->nome} (ID: {$funcionario->idUsuario}), Valor: R$ {$funcionario->salarioMensal}, Vencimento: {$dataVencimento->format('d/m/Y')}");
                }
            }
            
            Log::info("Geração de salários concluída. {$salariosCriados} salário(s) criado(s).");
            
        } catch (\Exception $e) {
            Log::error('Erro ao gerar salários mensais: ' . $e->getMessage(), [
                'erro_detalhes' => $e->getTraceAsString(),
            ]);
        }
    }
}
