<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\PlanoAssinatura;
use App\Services\PlanoAssinaturaService;
use App\Http\Requests\UpdateClienteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    /**
     * Show face capture page for the client
     */
    public function showFaceCapture(Cliente $cliente)
    {
        return view('clientes.capturar-rosto', compact('cliente'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academiaId = session('academia_selecionada');
        
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }

        $allPlanos = PlanoAssinatura::where('idAcademia', $academiaId)->get();

        $query = Cliente::where('idAcademia', $academiaId)->with('plano');

        if (request()->filled('search')) {
            $search = request('search');
            $cpfSearch = preg_replace('/[^0-9]/', '', $search);
            $query->where(function ($q) use ($search, $cpfSearch) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$cpfSearch}%");
            });
        }

        if (request()->filled('status_filter') && request('status_filter') !== '') {
            $query->where('status', request('status_filter'));
        }

        if (request()->filled('plano_id') && request('plano_id') !== '') {
            $query->where('idPlano', request('plano_id'));
        }

        $clientes = $query->orderBy('nome')->paginate(15)->appends(request()->query());

        return view('clientes.index', compact('clientes', 'allPlanos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $academiaId = session('academia_selecionada');
        
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }

        $planos = PlanoAssinatura::where('idAcademia', $academiaId)->get();

        return view('clientes.create', compact('planos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $cpfLimpo = preg_replace('/[^0-9]/', '', $request->cpf);
            $request->merge(['cpf' => $cpfLimpo]);

            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'cpf' => 'required|string|size:11|unique:clientes,cpf|regex:/^[0-9]{11}$/',
                'dataNascimento' => 'required|date',
                'telefone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'status' => 'required|string',
                'idPlano' => 'required|exists:plano_assinaturas,idPlano',
                'codigo_acesso' => 'nullable|string|max:20',
            ], [
                'cpf.unique' => 'Este CPF já está cadastrado para outro cliente.',
                'cpf.regex' => 'O CPF deve conter apenas números.',
                'cpf.size' => 'O CPF deve ter exatamente 11 dígitos.',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withInput()
                ->withErrors($e->errors());
        }

        try {
            $academiaId = session('academia_selecionada');

            if (!$academiaId) {
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'Nenhuma academia selecionada.']);
            }

            $clienteData = $validated;

            $clienteData['idAcademia'] = $academiaId;
            $clienteData['idUsuario'] = Auth::id();

            if (!empty($validated['codigo_acesso'])) {
                $clienteData['codigo_acesso'] = Hash::make($validated['codigo_acesso']);
            }

            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('clientes/fotos', 'public');
                $clienteData['foto'] = $fotoPath;
            }

            $cliente = Cliente::create($clienteData);

            return redirect()
                ->route('clientes.capturarRosto', ['cliente' => $cliente->idCliente])
                ->with('success', 'Cliente cadastrado com sucesso! Por favor, registre o reconhecimento facial.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        $academiaId = session('academia_selecionada') ?? $cliente->idAcademia;
        
        if (!\Illuminate\Support\Facades\Auth::user()->isAdministrador()) {
            if (!$academiaId) {
                return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
            }
        }

        $planos = PlanoAssinatura::where('idAcademia', $academiaId)->get();

        return view('clientes.edit', compact('cliente', 'planos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        try {
            $validated = $request->validated();

            if (!empty($validated['codigo_acesso'])) {
                if (empty($validated['codigo_acesso_antigo'])) {
                    return back()
                        ->withInput()
                        ->withErrors(['codigo_acesso_antigo' => 'Digite o código de acesso atual para alterá-lo.']);
                }

                if (!Hash::check($validated['codigo_acesso_antigo'], $cliente->codigo_acesso)) {
                    return back()
                        ->withInput()
                        ->withErrors(['codigo_acesso_antigo' => 'Código de acesso atual incorreto.']);
                }
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withInput()
                ->withErrors($e->errors());
        }

        DB::beginTransaction();

        try {
            $cliente->nome = $validated['nome'];
            $cliente->cpf = $validated['cpf'];
            $cliente->dataNascimento = $validated['dataNascimento'];
            $cliente->telefone = $validated['telefone'] ?? null;
            $cliente->email = $validated['email'] ?? null;
            $cliente->status = $validated['status'];
            $cliente->idPlano = $validated['idPlano'];

            if (!empty($validated['codigo_acesso'])) {
                $cliente->codigo_acesso = Hash::make($validated['codigo_acesso']);
            }

            if ($request->hasFile('foto')) {
                if ($cliente->foto && Storage::disk('public')->exists($cliente->foto)) {
                    Storage::disk('public')->delete($cliente->foto);
                }
                $cliente->foto = $request->file('foto')->store('clientes/fotos', 'public');
            }

            $cliente->save();

            DB::commit();

            return redirect()
                ->route('clientes.index')
                ->with('success', 'Cliente atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar cliente: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        if (!$cliente->podeDeletar()) {
            return back()->with('error', 'Não é possível excluir este cliente pois existem registros associados (mensalidades, entradas ou vendas).');
        }

        try {
            if ($cliente->foto && Storage::disk('public')->exists($cliente->foto)) {
                Storage::disk('public')->delete($cliente->foto);
            }

            $cliente->delete();

            return redirect()
                ->route('clientes.index')
                ->with('success', 'Cliente excluído com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao excluir cliente: ' . $e->getMessage()]);
        }
    }

    /**
     * Renova o plano de assinatura do cliente.
     */
    public function renewPlan(Cliente $cliente, PlanoAssinaturaService $planoService)
    {
        try {
            $academiaId = session('academia_selecionada');
            if (!$academiaId || $cliente->idAcademia != $academiaId) {
                return back()->with('error', 'Cliente não encontrado ou não pertence à academia selecionada.');
            }

            if (!$cliente->plano) {
                return back()->with('error', 'Cliente não possui plano de assinatura associado.');
            }

            $novaMensalidade = $planoService->renewClientPlan($cliente, $cliente->plano);

            return redirect()
                ->route('clientes.index')
                ->with('success', "Plano '{$cliente->plano->nome}' renovado com sucesso para {$cliente->nome}! Nova mensalidade criada com vencimento em " . $novaMensalidade->dataVencimento->format('d/m/Y') . ".");

        } catch (\Exception $e) {
            Log::error("Erro ao renovar plano do cliente ID {$cliente->idCliente}: " . $e->getMessage(), [
                'cliente_id' => $cliente->idCliente,
                'plano_id' => $cliente->idPlano,
                'erro_detalhes' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', 'Erro ao renovar plano: ' . $e->getMessage());
        }
    }
}
