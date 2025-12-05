<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\FaceDescriptor;
use App\Models\KioskStatus;
use App\Services\EntradaService; 
use App\Events\KioskStatusChanged;
use App\Events\ClientRegistrationStarted;
use App\Events\ClientRegistrationCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon; 

class FaceRecognitionController extends Controller
{
    protected $entradaService;

    public function __construct(EntradaService $entradaService)
    {
        $this->entradaService = $entradaService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,idCliente',
            'descriptor' => 'required|array',
            'descriptor.*' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $cliente = Cliente::find($request->cliente_id);
            
            KioskStatus::updateOrCreate(
                ['id' => 1],
                ['is_registering' => true, 'message' => 'Rosto sendo registrado...', 'expires_at' => Carbon::now()->addSeconds(60)]
            );

            event(new ClientRegistrationStarted($request->cliente_id, $cliente->nome, 'Rosto sendo registrado...'));
            event(new KioskStatusChanged(true, 'Rosto sendo registrado...'));

            $existingDescriptor = FaceDescriptor::where('cliente_id', $request->cliente_id)->first();

            $descriptorData = [
                'cliente_id' => $request->cliente_id,
                'descriptor' => $request->descriptor,
            ];

            if ($existingDescriptor) {
                $existingDescriptor->update($descriptorData);
                $message = "Descritor facial atualizado com sucesso para o cliente {$cliente->nome}.";
            } else {
                FaceDescriptor::create($descriptorData);
                $message = "Descritor facial registrado com sucesso para o cliente {$cliente->nome}.";
            }

            DB::commit();

            KioskStatus::where('id', 1)->update(['is_registering' => false, 'message' => null, 'expires_at' => null]);

            event(new ClientRegistrationCompleted($request->cliente_id, $cliente->nome, true, $message));
            event(new KioskStatusChanged(false, null));

            return response()->json(['success' => true, 'message' => $message, 'user_name' => $cliente->nome]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao registrar/atualizar descritor facial', ['error' => $e->getMessage(), 'cliente_id' => $request->cliente_id]);
            
            KioskStatus::where('id', 1)->update(['is_registering' => false, 'message' => null, 'expires_at' => null]);
            
            $cliente = Cliente::find($request->cliente_id);
            $errorMessage = 'Falha ao registrar descritor facial.';
            
            event(new ClientRegistrationCompleted($request->cliente_id, $cliente ? $cliente->nome : 'Cliente', false, $errorMessage));
            event(new KioskStatusChanged(false, null));
            
            return response()->json(['success' => false, 'message' => $errorMessage], 500);
        }
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'descriptor' => 'required|array',
            'descriptor.*' => 'required|numeric',
        ]);

        $inputDescriptor = $request->input('descriptor');
        $minDistance = config('faceapi.min_distance', 0.6);
        $allFaceDescriptors = FaceDescriptor::all();
        $bestMatch = null;
        $closestDistance = PHP_FLOAT_MAX;

        foreach ($allFaceDescriptors as $storedDescriptor) {
            $distance = $this->calculateEuclideanDistance($inputDescriptor, $storedDescriptor->descriptor);

            if ($distance < $closestDistance) {
                $closestDistance = $distance;
                $bestMatch = $storedDescriptor;
            }
        }

        if ($bestMatch && $closestDistance < $minDistance) {
            $cliente = Cliente::find($bestMatch->cliente_id);

            if ($cliente) {
                if ($cliente->status === 'Inativo') {
                    Log::warning("Acesso negado por reconhecimento para cliente ID {$cliente->idCliente}: Cadastro Inativo.");
                    return response()->json([
                        'authenticated' => false,
                        'client_id' => $cliente->idCliente,
                        'user_name' => $cliente->nome,
                        'status' => $cliente->status,
                        'message' => 'ACESSO NEGADO! Cadastro Inativo - Procure a recepção.',
                    ], 403);
                }

                if ($cliente->status === 'Inadimplente') {
                    Log::warning("Acesso negado por reconhecimento para cliente ID {$cliente->idCliente}: Mensalidade vencida.");
                    return response()->json([
                        'authenticated' => false,
                        'client_id' => $cliente->idCliente,
                        'user_name' => $cliente->nome,
                        'status' => $cliente->status,
                        'message' => 'ACESSO NEGADO! Sua mensalidade está vencida - Procure a recepção para regularizar.',
                    ], 403);
                }

                try {
                    $this->entradaService->registrarEntrada($cliente, 'Reconhecimento Facial');
                    Log::info("Acesso registrado para cliente {$cliente->idCliente} via reconhecimento facial.");
                } catch (\Exception $e) {
                    Log::error("Falha ao registrar entrada para cliente {$cliente->idCliente}: " . $e->getMessage());
                }

                return response()->json([
                    'authenticated' => true,
                    'client_id' => $cliente->idCliente,
                    'user_name' => $cliente->nome,
                    'status' => $cliente->status,
                    'message' => 'ACESSO LIBERADO! Bom treino, ' . $cliente->nome . '!',
                ]);
            }
        }

        return response()->json([
            'authenticated' => false,
            'message' => 'Rosto não reconhecido ou não encontrado.',
        ], 401);
    }

    public function authenticateByCode(Request $request)
    {
        $request->validate([
            'code' => 'required|integer|digits:6',
        ]);

        $cliente = Cliente::where('codigo_acesso', $request->code)->first();

        if (!$cliente) {
            Log::warning('Tentativa de acesso por código falhou: Código não encontrado.');
            return response()->json(['authenticated' => false, 'message' => 'Código de acesso inválido.'], 401);
        }

        if ($cliente->status === 'Inativo') {
            Log::warning("Acesso negado por código para cliente ID {$cliente->idCliente}: Cadastro Inativo.");
            return response()->json(['authenticated' => false, 'message' => 'ACESSO NEGADO! Cadastro Inativo - Procure a recepção.'], 403);
        }

        if ($cliente->status === 'Inadimplente') {
            Log::warning("Acesso negado por código para cliente ID {$cliente->idCliente}: Mensalidade vencida.");
            return response()->json(['authenticated' => false, 'message' => 'ACESSO NEGADO! Sua mensalidade está vencida - Procure a recepção para regularizar.'], 403);
        }

        try {
            $this->entradaService->registrarEntrada($cliente, 'CodigoAcesso');
            Log::info("Acesso registrado para cliente {$cliente->idCliente} via Código de Acesso.");
        } catch (\Exception $e) {
            Log::error('Falha ao registrar entrada por código', ['error' => $e->getMessage(), 'cliente_id' => $cliente->idCliente]);
        }
    
        return response()->json([
            'authenticated' => true,
            'client_id' => $cliente->idCliente,
            'user_name' => $cliente->nome,
            'status' => $cliente->status,
            'message' => 'ACESSO LIBERADO! Bom treino, ' . $cliente->nome . '!'
        ]);
    }


    public function getKioskStatus()
    {
        $kioskStatus = KioskStatus::find(1);
        
        if ($kioskStatus && $kioskStatus->is_registering && $kioskStatus->expires_at && $kioskStatus->expires_at->isPast()) {
            $kioskStatus->update(['is_registering' => false, 'message' => null, 'expires_at' => null]);
        }

        return response()->json([
            'is_registering' => $kioskStatus ? $kioskStatus->is_registering : false,
            'message' => $kioskStatus ? $kioskStatus->message : null,
        ]);
    }

    public function setKioskRegistering(Request $request)
    {
        try {
            $data = $request->all();

            $validated = validator($data, [
                'is_registering' => 'boolean',
                'message' => 'nullable|string',
                'duration_seconds' => 'nullable|integer|min:1',
            ]);

            if ($validated->fails()) {
                return response()->json(['success' => false, 'errors' => $validated->errors()], 422);
            }

            $isRegistering = (bool)($data['is_registering'] ?? false);
            $durationSeconds = isset($data['duration_seconds']) ? (int)$data['duration_seconds'] : null;
            $message = $data['message'] ?? null;

            if ($isRegistering) {
                $expiresAt = $durationSeconds ? Carbon::now()->addSeconds($durationSeconds) : null;
                $message = $message ?: 'Rosto sendo registrado em outra estação...';

                $kioskStatus = KioskStatus::updateOrCreate(
                    ['id' => 1],
                    [
                        'is_registering' => true,
                        'message' => $message,
                        'expires_at' => $expiresAt,
                    ]
                );

                event(new KioskStatusChanged(true, $message));
                Log::info('Kiosk status set to registering', ['message' => $message, 'expires_at' => $expiresAt]);
            } else {
                $kioskStatus = KioskStatus::updateOrCreate(
                    ['id' => 1],
                    [
                        'is_registering' => false,
                        'message' => null,
                        'expires_at' => null,
                    ]
                );

                event(new KioskStatusChanged(false, null));
                Log::info('Kiosk status set to not registering');
            }

            return response()->json(['success' => true, 'kiosk_status' => $kioskStatus]);
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar status do kiosk', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Falha ao atualizar status do kiosk.'], 500);
        }
    }

    private function calculateEuclideanDistance(array $desc1, array $desc2): float
    {
        $sum = 0;
        for ($i = 0; $i < count($desc1); $i++) {
            $sum += pow($desc1[$i] - $desc2[$i], 2);
        }
        return sqrt($sum);
    }
}
