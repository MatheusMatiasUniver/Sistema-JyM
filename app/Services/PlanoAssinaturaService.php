<?php

namespace App\Services;

use App\Models\Cliente; 
use App\Models\Mensalidade;
use App\Models\PlanoAssinatura;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;
use \Exception;
use Carbon\Carbon;

class PlanoAssinaturaService
{
    /**
     * Retorna todos os planos de assinatura, com suas academias.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPlanos()
    {
        return PlanoAssinatura::with('academia')->get();
    }

    /**
     * Cria um novo plano de assinatura.
     *
     * @param array $data
     * @return PlanoAssinatura
     * @throws \Exception
     */
    public function createPlano(array $data): PlanoAssinatura
    {
        try {
            $plano = PlanoAssinatura::create($data);
            return $plano;
        } catch (\Exception $e) {
            Log::error("Erro ao criar plano de assinatura: " . $e->getMessage(), ['data' => $data]);
            throw new \Exception("Falha ao criar plano de assinatura: " . $e->getMessage());
        }
    }

    /**
     * Atualiza um plano de assinatura existente.
     *
     * @param PlanoAssinatura $plano
     * @param array $data
     * @return PlanoAssinatura
     * @throws \Exception
     */
    public function updatePlano(PlanoAssinatura $plano, array $data): PlanoAssinatura
    {
        try {
            $plano->update($data);
            return $plano;
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar plano ID {$plano->idPlano}: " . $e->getMessage(), ['data' => $data]);
            throw new \Exception("Falha ao atualizar plano: " . $e->getMessage());
        }
    }

    /**
     * Exclui um plano de assinatura.
     *
     * @param PlanoAssinatura $plano
     * @return bool
     * @throws \Exception
     */
    public function deletePlano(PlanoAssinatura $plano): bool
    {
        try {
            return $plano->delete();
        } catch (\Exception $e) {
            Log::error("Erro ao excluir plano ID {$plano->idPlano}: " . $e->getMessage());
            throw new \Exception("Falha ao excluir plano: " . $e->getMessage());
        }
    }

    /**
     * Renova o plano de assinatura de um cliente.
     *
     * @param Cliente $cliente
     * @param PlanoAssinatura $plano
     * @return Mensalidade
     * @throws \Exception
     */
    public function renewClientPlan(Cliente $cliente, PlanoAssinatura $plano): Mensalidade
    {
        DB::beginTransaction();
        try {
            $ultimaMensalidade = $cliente->mensalidades()->latest('dataVencimento')->first();

            $dataInicioNovoPeriodo = Carbon::now();

            if ($ultimaMensalidade) {
                if ($ultimaMensalidade->dataVencimento->isFuture()) {
                    $dataInicioNovoPeriodo = $ultimaMensalidade->dataVencimento->addDay(); 
                } else {
                    $dataInicioNovoPeriodo = Carbon::now();
                }
            } else {
                $dataInicioNovoPeriodo = Carbon::now();
            }

            $novaDataVencimento = $dataInicioNovoPeriodo->addDays($plano->duracaoDias);

            $novaMensalidade = Mensalidade::create([
                'idCliente' => $cliente->idCliente,
                'dataVencimento' => $novaDataVencimento,
                'valor' => $plano->valor,
                'status' => 'Paga',
                'dataPagamento' => Carbon::now(),
            ]);

            if ($cliente->status !== 'Ativo') {
                $cliente->status = 'Ativo';
                $cliente->save();
            }

            DB::commit();
            return $novaMensalidade;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao renovar plano para cliente ID {$cliente->idCliente}: " . $e->getMessage(), [
                'cliente_id' => $cliente->idCliente,
                'plano_id' => $plano->idPlano,
                'erro_detalhes' => $e->getTraceAsString(),
            ]);
            throw new \Exception("Falha ao renovar plano: " . $e->getMessage());
        }
    }
}