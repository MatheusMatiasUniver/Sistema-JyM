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
    public function registrarEntrada(Cliente $cliente, string $tipoEntrada = 'Manual'): Entrada
    {
        try {
            $entrada = Entrada::create([
                'idCliente' => $cliente->idCliente,
                'dataEntrada' => now(),
                'tipoEntrada' => $tipoEntrada,
                'idAcademia' => $cliente->idAcademia,
            ]);

            return $entrada;

        } catch (\Exception $e) {
            Log::error("Erro ao registrar entrada para cliente ID {$cliente->idCliente}: " . $e->getMessage());
            throw new \Exception('Erro ao registrar entrada: ' . $e->getMessage());
        }
    }
}