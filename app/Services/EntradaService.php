<?php

namespace App\Services;

use App\Models\Entrada;
use App\Models\Cliente;
use Illuminate\Support\Facades\Log;
use Exception;

class EntradaService
{
    /**
     * Registra um novo acesso para o cliente.
     *
     * @param Cliente $cliente
     * @param string $metodo
     * @return Entrada
     * @throws Exception
     */
    public function registrarEntrada(Cliente $cliente, string $metodo = 'Manual'): Entrada
    {
        try {
            $entrada = Entrada::create([
                'idCliente' => $cliente->idCliente,
                'dataHora' => now(),
                'metodo' => $metodo,
                'idAcademia' => $cliente->idAcademia,
            ]);

            return $entrada;

        } catch (\Exception $e) {
            Log::error("Erro ao registrar entrada para cliente ID {$cliente->idCliente}: " . $e->getMessage());
            throw new \Exception('Erro ao registrar entrada: ' . $e->getMessage());
        }
    }
}