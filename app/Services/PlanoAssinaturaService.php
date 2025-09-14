<?php

namespace App\Services;

use App\Models\PlanoAssinatura;
use Illuminate\Support\Facades\Log;
use \Exception;

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
}