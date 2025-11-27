<?php

namespace App\Http\Controllers;

use App\Models\ManutencaoEquipamento;
use App\Models\Equipamento;
use App\Models\Fornecedor;
use Illuminate\Http\Request;

class ManutencaoEquipamentoController extends Controller
{
    public function index()
    {
        $manutencoes = ManutencaoEquipamento::with(['equipamento', 'fornecedor'])->orderByDesc('idManutencao')->paginate(20);
        return view('manutencoes.index', compact('manutencoes'));
    }

    public function create()
    {
        $equipamentos = Equipamento::orderBy('descricao')->get();
        $fornecedores = Fornecedor::orderBy('razaoSocial')->get();
        return view('manutencoes.create', compact('equipamentos', 'fornecedores'));
    }

    public function store(Request $request)
    {
        try {
            $dados = $request->validate([
                'idEquipamento' => 'required|exists:equipamentos,idEquipamento',
                'tipo' => 'required|in:preventiva,corretiva',
                'dataProgramada' => 'nullable|date',
                'dataExecucao' => 'nullable|date',
                'custo' => 'nullable|numeric|min:0',
                'fornecedorId' => 'nullable|exists:fornecedores,idFornecedor',
                'observacoes' => 'nullable|string',
            ]);
            ManutencaoEquipamento::create($dados);
            return redirect()->route('manutencoes.index')->with('success', 'Manutenção registrada');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Dados inválidos'], 400);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao registrar manutenção')->withInput();
        }
    }
}

