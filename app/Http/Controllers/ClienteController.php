<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\PlanoAssinatura;
use App\Services\PlanoAssinaturaService;
use App\Http\Requests\UpdateClienteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
 
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
            $search = trim((string) request('search'));
            if ($search !== '') {
                $searchLower = mb_strtolower($search, 'UTF-8');
                $cpfSearch = preg_replace('/[^0-9]/', '', $search);
                $query->where(function ($q) use ($searchLower, $cpfSearch) {
                    $q->whereRaw('LOWER(nome) LIKE ?', ["%{$searchLower}%"]);
                    if ($cpfSearch !== '') {
                        $q->orWhere('cpf', 'like', "%{$cpfSearch}%");
                    }
                });
            }
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
    public function store(\App\Http\Requests\StoreClienteRequest $request)
    {
        $validated = $request->validated();

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

            

            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('clientes/fotos', 'public');
                $clienteData['foto'] = $fotoPath;
            }

            $cliente = Cliente::create($clienteData);

            return redirect()
                ->route('clientes.capturarRosto', ['cliente' => $cliente->idCliente])
                ->with('success', 'Cliente cadastrado com sucesso! Por favor, registre o reconhecimento facial.')
                ->with('access_code', $cliente->codigo_acesso);

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

    public function deletedIndex()
    {
        $academiaId = session('academia_selecionada');
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }
        $clientes = Cliente::onlyTrashed()->where('idAcademia', $academiaId)->orderBy('nome')->paginate(15);
        return view('clientes.excluidos', compact('clientes'));
    }

    public function restore(int $id)
    {
        try {
            $cliente = Cliente::withTrashed()->findOrFail($id);
            $cliente->restore();
            return redirect()->route('clientes.excluidos.index')->with('success', 'Cliente restaurado com sucesso.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao restaurar cliente: ' . $e->getMessage()]);
        }
    }

    public function confirmForceDelete(int $id)
    {
        $cliente = Cliente::withTrashed()->findOrFail($id);
        $mensalidadesCount = \App\Models\Mensalidade::where('idCliente', $cliente->idCliente)->count();
        $entradasCount = \App\Models\Entrada::where('idCliente', $cliente->idCliente)->count();
        $vendasCount = \App\Models\VendaProduto::where('idCliente', $cliente->idCliente)->count();
        return view('clientes.confirm-force-delete', compact('cliente', 'mensalidadesCount', 'entradasCount', 'vendasCount'));
    }

    public function forceDelete(int $id)
    {
        try {
            $cliente = Cliente::withTrashed()->findOrFail($id);

            $mensalidadesCount = \App\Models\Mensalidade::where('idCliente', $cliente->idCliente)->count();
            $entradasCount = \App\Models\Entrada::where('idCliente', $cliente->idCliente)->count();
            $vendasCount = \App\Models\VendaProduto::where('idCliente', $cliente->idCliente)->count();

            if (!request()->has('confirm') || request('confirm') !== 'yes') {
                return redirect()->route('clientes.confirmForceDelete', ['id' => $id])
                    ->with('warning', 'Excluir definitivamente pode tornar históricos inconsistentes. Confirme para prosseguir.');
            }

            \App\Models\Mensalidade::where('idCliente', $cliente->idCliente)->update(['idCliente' => null]);
            \App\Models\Entrada::where('idCliente', $cliente->idCliente)->update(['idCliente' => null]);
            \App\Models\VendaProduto::where('idCliente', $cliente->idCliente)->update(['idCliente' => null]);

            if ($cliente->foto && Storage::disk('public')->exists($cliente->foto)) {
                Storage::disk('public')->delete($cliente->foto);
            }

            $cliente->forceDelete();

            return redirect()->route('clientes.excluidos.index')->with('success', 'Cliente excluído definitivamente. Histórico preservado como "Cliente Deletado".');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao excluir definitivamente: ' . $e->getMessage()]);
        }
    }

    /**
     * Renova o plano de assinatura do cliente.
     */
    public function renewPlan(Request $request, Cliente $cliente, PlanoAssinaturaService $planoService)
    {
        try {
            $academiaId = session('academia_selecionada');
            if (!$academiaId || $cliente->idAcademia != $academiaId) {
                return back()->with('error', 'Cliente não encontrado ou não pertence à academia selecionada.');
            }

            if (!$cliente->plano) {
                return back()->with('error', 'Cliente não possui plano de assinatura associado.');
            }

            $formaPagamento = $request->input('formaPagamento');
            if ($formaPagamento !== null && $formaPagamento !== '') {
                $request->validate([
                    'formaPagamento' => 'required|in:Dinheiro,Cartão de Crédito,Cartão de Débito,PIX,Boleto',
                ]);
            }

            $novaMensalidade = $planoService->renewClientPlan($cliente, $cliente->plano);

            if ($formaPagamento) {
                DB::beginTransaction();
                try {
                    $novaMensalidade->status = 'Paga';
                    $novaMensalidade->dataPagamento = now();
                    $novaMensalidade->formaPagamento = $formaPagamento;
                    $novaMensalidade->save();

                    $recebimentoData = $novaMensalidade->dataPagamento ?? now();
                    $updated = DB::table('contas_receber')
                        ->where('idAcademia', $novaMensalidade->idAcademia)
                        ->where('documentoRef', $novaMensalidade->idMensalidade)
                        ->where('status', 'aberta')
                        ->update([
                            'status' => 'recebida',
                            'dataRecebimento' => $recebimentoData,
                            'formaRecebimento' => $formaPagamento,
                        ]);

                    if ($updated === 0) {
                        DB::table('contas_receber')->insert([
                            'idAcademia' => $novaMensalidade->idAcademia,
                            'idCliente' => $novaMensalidade->idCliente,
                            'documentoRef' => $novaMensalidade->idMensalidade,
                            'descricao' => 'Mensalidade Cliente #'.$novaMensalidade->idCliente,
                            'valorTotal' => $novaMensalidade->valor,
                            'status' => 'recebida',
                            'dataVencimento' => $novaMensalidade->dataVencimento,
                            'dataRecebimento' => $recebimentoData,
                            'formaRecebimento' => $formaPagamento,
                        ]);
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'Erro ao registrar pagamento da renovação: '.$e->getMessage());
                }
            }

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
