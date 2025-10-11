<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\PlanoAssinatura;
use App\Services\PlanoAssinaturaService;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Services\ClienteService;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ClienteController extends Controller
{
    protected $clienteService;
    protected $planoAssinaturaService;

    public function __construct(ClienteService $clienteService, PlanoAssinaturaService $planoAssinaturaService)
    {
        $this->clienteService = $clienteService;
        $this->planoAssinaturaService = $planoAssinaturaService;
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $query = Cliente::with(['plano', 'mensalidades', 'entradas']);

        if ($search = $request->input('search')) {
            $query->where('nome', 'like', '%'.$search.'%')
                  ->orWhere('cpf', 'like', '%'.$search.'%')
                  ->orWhere('email', 'like', '%'.$search.'%');
        }

        if ($statusFilter = $request->input('status_filter')) {
            if (in_array($statusFilter, ['Ativo', 'Inativo'])) {
                $query->where('status', $statusFilter);
            }
        }

        if ($planoId = $request->input('plano_id')) {
            $query->where('idPlano', $planoId);
        }

        $clientes = $query->paginate(10);
        $allPlanos = PlanoAssinatura::all();

        return view('clientes.index', compact('clientes', 'allPlanos'));
    }

    public function create()
    {
        $planos = PlanoAssinatura::all(); 
        return view('clientes.create', compact('planos'));
    }

    /**
     * Armazena um novo cliente no banco de dados e redireciona para a tela de captura de rosto.
     * @param StoreClienteRequest $request
     */
    public function store(StoreClienteRequest $request)
    {
        try {
            $data = $request->validated();
            
            $cliente = $this->clienteService->createCliente($data, Auth::id(), $request->file('foto'));
            
            return redirect()->route('clientes.capturarRosto', ['cliente' => $cliente->idCliente])
                             ->with('success', 'Cliente ' . $cliente->nome . ' cadastrado com sucesso! Agora, capture o rosto do cliente.');

        } catch (\Exception $e) {
            Log::error("Erro no ClienteController@store: " . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao cadastrar cliente: ' . $e->getMessage());
        }
    }

    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        $planos = PlanoAssinatura::all(); 
        return view('clientes.edit', compact('cliente', 'planos'));
    }

    /**
     * Atualiza um cliente existente no banco de dados.
     * @param UpdateClienteRequest $request  
     * @param Cliente $cliente
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        try {
            $this->clienteService->updateCliente(
                $cliente,
                $request->validated(),
                $request->file('foto')
            );

            return redirect()->route('clientes.index')->with('success', 'Cliente ' . $cliente->nome . ' atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro no ClienteController@update para cliente ID {$cliente->idCliente}: " . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao atualizar cliente: ' . $e->getMessage());
        }
    }

    /**
     * Remove um cliente do banco de dados.
     * @param Cliente $cliente
     */
    public function destroy(Cliente $cliente)
    {
        try {
            $this->clienteService->deleteCliente($cliente);
            return redirect()->route('clientes.index')->with('success', 'Cliente ' . $cliente->nome . ' excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro no ClienteController@destroy: " . $e->getMessage());
            return back()->with('error', 'Erro ao excluir cliente: ' . $e->getMessage());
        }
    }

    public function showFaceCapture(Cliente $cliente)
    {
        return view('clientes.capturar-rosto', compact('cliente'));
    }

    public function renewPlan(Cliente $cliente)
    {
        try {
            if (!$cliente->idPlano) {
                return redirect()->back()->with('error', 'O cliente não possui um plano de assinatura associado para renovar.');
            }

            $plano = PlanoAssinatura::find($cliente->idPlano);

            if (!$plano) {
                return redirect()->back()->with('error', 'O plano de assinatura associado ao cliente não foi encontrado.');
            }

            $this->planoAssinaturaService->renewClientPlan($cliente, $plano);

            return redirect()->back()->with('success', 'Plano de assinatura do cliente ' . $cliente->nome . ' renovado com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro ao renovar plano via controller para cliente ID {$cliente->idCliente}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao renovar plano de assinatura: ' . $e->getMessage());
        }
    }
}