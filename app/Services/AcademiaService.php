<?php

namespace App\Services;

use App\Models\Academia;
use Illuminate\Support\Facades\Log;
use \Exception;

class AcademiaService
{
    /**
     * Retorna todas as academias.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllAcademias()
    {
        return Academia::all();
    }

    /**
     * Cria uma nova academia.
     *
     * @param array $data
     * @return Academia
     * @throws \Exception
     */
    public function createAcademia(array $data): Academia
    {
        try {
            $academia = Academia::create($data);
            return $academia;
        } catch (\Exception $e) {
            Log::error("Erro ao criar academia: " . $e->getMessage(), ['data' => $data]);
            throw new \Exception("Falha ao criar academia: " . $e->getMessage());
        }
    }

    /**
     * Atualiza uma academia existente.
     *
     * @param Academia $academia
     * @param array $data
     * @return Academia
     * @throws \Exception
     */
    public function updateAcademia(Academia $academia, array $data): Academia
    {
        try {
            $academia->update($data);
            return $academia;
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar academia ID {$academia->idAcademia}: " . $e->getMessage(), ['data' => $data]);
            throw new \Exception("Falha ao atualizar academia: " . $e->getMessage());
        }
    }

    /**
     * Exclui uma academia.
     *
     * @param Academia $academia
     * @return bool
     * @throws \Exception
     */
    public function deleteAcademia(Academia $academia): bool
    {
        try {
            return $academia->delete();
        } catch (\Exception $e) {
            Log::error("Erro ao excluir academia ID {$academia->idAcademia}: " . $e->getMessage());
            throw new \Exception("Falha ao excluir academia: " . $e->getMessage());
        }
    }
}