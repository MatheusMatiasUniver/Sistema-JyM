<?php

namespace App\Services;

use App\Models\Entrada;
use Illuminate\Support\Facades\Log;
use Exception;

class EntradaService
{
    /**
     * Registra um novo acesso para o cliente.
     *
     * @param int $idCliente
     * @param string $metodo
     * @return Entrada
     * @throws Exception
     */
    public function registrarEntrada(int $idCliente, string $metodo, int $idAcademia = null): Entrada
    {
        try {
            // Se não foi fornecido idAcademia, tenta obter da sessão ou do usuário
            if (!$idAcademia) {
                $idAcademia = session('academia_selecionada') ?? auth()->user()->idAcademia ?? null;
            }

            if (!$idAcademia) {
                throw new Exception("ID da academia é obrigatório para registrar entrada.");
            }

            $entrada = Entrada::create([
                'idCliente' => $idCliente,
                'dataHora' => now(),
                'metodo' => $metodo,
                'idAcademia' => $idAcademia,
            ]);
            return $entrada;
        } catch (Exception $e) {
            Log::error("Erro ao registrar entrada para cliente {$idCliente}: " . $e->getMessage());
            throw new Exception("Falha ao registrar entrada: " . $e->getMessage());
        }
    }
}