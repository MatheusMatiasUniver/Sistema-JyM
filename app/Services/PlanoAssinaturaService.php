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
        if (!$plano->podeDeletar()) {
            throw new \Exception("Não é possível excluir este plano pois existem clientes ou mensalidades associadas.");
        }

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
            $dataVencimento = Carbon::now()->addDays($plano->duracaoDias);

            $mensalidade = Mensalidade::create([
                'idCliente' => $cliente->idCliente,
                'idPlano' => $plano->idPlano,
                'valor' => $plano->valor,
                'dataVencimento' => $dataVencimento,
                'status' => 'Pendente',
                'idAcademia' => $cliente->idAcademia,
            ]);

            DB::commit();

            return $mensalidade;

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Erro ao renovar plano: ' . $e->getMessage());
        }
    }

    /**
     * Verifica se um cliente pode ter seu status alterado para Ativo
     * @param Cliente $cliente
     * @return bool
     */
    public function podeAtivarCliente(Cliente $cliente): bool
    {
        $mensalidadeVencida = Mensalidade::where('idCliente', $cliente->idCliente)
            ->where('status', 'Pendente')
            ->where('dataVencimento', '<', Carbon::today())
            ->exists();
        
        return !$mensalidadeVencida;
    }

    /**
     * Obtém informações sobre mensalidades vencidas de um cliente
     * @param Cliente $cliente
     * @return array
     */
    public function getMensalidadesVencidas(Cliente $cliente): array
    {
        $mensalidadesVencidas = Mensalidade::where('idCliente', $cliente->idCliente)
            ->where('status', 'Pendente')
            ->where('dataVencimento', '<', Carbon::today())
            ->orderBy('dataVencimento', 'asc')
            ->get();

        return [
            'total' => $mensalidadesVencidas->count(),
            'valor_total' => $mensalidadesVencidas->sum('valor'),
            'mensalidades' => $mensalidadesVencidas
        ];
    }
}