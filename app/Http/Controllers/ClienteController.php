<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\PlanoAssinatura;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Services\ClienteService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{
    protected $clienteService;

    public function __construct(ClienteService $clienteService)
    {
        $this->clienteService = $clienteService;
        $this->middleware(['auth']);
    }

    /**
     * Exibe uma lista de clientes.
     */
    public function index()
    {
        $clientes = $this->clienteService->getAllClientes()->load('plano'); 
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Exibe o formulário para criar um novo cliente.
     */
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

    /**
     * Exibe os detalhes de um cliente específico.
     */
    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Exibe o formulário para editar um cliente existente.
     */
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
            $data = $request->validated();
            
            $updatedCliente = $this->clienteService->updateCliente(
                $cliente,
                $data,
                $request->file('foto')
            );

            return redirect()->route('clientes.index')->with('success', 'Cliente ' . $updatedCliente->nome . ' atualizado com sucesso!');

        } catch (\Exception $e) {
            Log::error("Erro no ClienteController@update: " . $e->getMessage());
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

    /**
     * Exibe a tela de captura de rosto para um cliente específico.
     * Este método é acessado apos o cadastro bem sucedido de um cliente.
     */
    public function showFaceCapture(Cliente $cliente)
    {
        return view('clientes.capturar-rosto', compact('cliente'));
    }
}