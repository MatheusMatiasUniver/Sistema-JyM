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
    public function registrarEntrada(int $idCliente, string $metodo): Entrada
    {
        try {
            $entrada = Entrada::create([
                'idCliente' => $idCliente,
                'dataHora' => now(),
                'metodo' => $metodo,
            ]);
            return $entrada;
        } catch (Exception $e) {
            Log::error("Erro ao registrar entrada para cliente {$idCliente}: " . $e->getMessage());
            throw new Exception("Falha ao registrar entrada: " . $e->getMessage());
        }
    }
}