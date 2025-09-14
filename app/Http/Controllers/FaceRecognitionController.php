<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\FaceDescriptor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FaceRecognitionController extends Controller
{
    /**
     * Salva um descritor facial para um cliente.
     * Renomeado de 'store' para 'register' para corresponder à chamada JS.
     */
    public function register(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,idCliente',
            'descriptor' => 'required|array',
        ]);

        $cliente = Cliente::find($request->cliente_id);

        if ($cliente->faceDescriptors()->exists()) {
            return response()->json(['message' => 'Este cliente já possui um descritor facial registrado.'], 409);
        }

        $descriptor = new FaceDescriptor([
            'descriptor' => json_encode($request->descriptor),
        ]);
        $cliente->faceDescriptors()->save($descriptor);

        return response()->json(['success' => true, 'message' => 'Descritor facial salvo com sucesso para o cliente ' . $cliente->nome . '.'], 200);
    }

    /**
     * Autentica um rosto comparando o descritor de entrada com os armazenados.
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'descriptor' => 'required|array',
        ]);

        $inputDescriptor = $request->descriptor;

        $clientes = Cliente::with('faceDescriptors')->get();

        $matchThreshold = 0.6;

        foreach ($clientes as $cliente) {
            foreach ($cliente->faceDescriptors as $storedFaceDescriptor) {
                $storedDescriptorArray = json_decode($storedFaceDescriptor->descriptor, true);

                if (!is_array($storedDescriptorArray)) {
                    Log::error("Descritor armazenado para cliente {$cliente->nome} (ID: {$cliente->idCliente}) não é um JSON válido ou não decodificou para um array.");
                    continue;
                }
                
                if (count($inputDescriptor) !== count($storedDescriptorArray)) {
                    Log::warning("Tamanho do descritor de entrada ({count($inputDescriptor)}) não corresponde ao armazenado ({count($storedDescriptorArray)}) para cliente {$cliente->nome}.");
                    continue;
                }
                $distance = $this->calculateEuclideanDistance($inputDescriptor, $storedDescriptorArray);
                
                Log::info("Comparando cliente {$cliente->nome} (ID: {$cliente->idCliente}). Distância: {$distance}");

                if ($distance < $matchThreshold) {
                    return response()->json([
                        'authenticated' => true,
                        'message' => 'Autenticação bem-sucedida.',
                        'user_name' => $cliente->nome,
                        'client_id' => $cliente->idCliente,
                        'cpf' => $cliente->cpf,
                        'status' => $cliente->status,
                    ], 200);
                }
            }
        }

        return response()->json(['authenticated' => false, 'message' => 'Rosto não reconhecido. Nenhum match encontrado.'], 200);
    }

    /**
     * Calcula a distância euclidiana entre dois descritores.
     * Assume que ambos os descritores são arrays numéricos de mesmo tamanho.
     */
    private function calculateEuclideanDistance(array $desc1, array $desc2): float
    {
        $sum = 0;
        for ($i = 0; $i < count($desc1); $i++) {
            $sum += pow($desc1[$i] - $desc2[$i], 2);
        }
        return sqrt($sum);
    }
}