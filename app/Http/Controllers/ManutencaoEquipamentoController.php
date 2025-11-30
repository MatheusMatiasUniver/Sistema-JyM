<?php

namespace App\Http\Controllers;

use App\Models\ManutencaoEquipamento;
use App\Models\Equipamento;
use App\Models\Fornecedor;
use App\Models\StatusEquipamento;
use App\Models\StatusManutencao;
use App\Http\Requests\StoreManutencaoRequest;
use App\Http\Requests\UpdateManutencaoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManutencaoEquipamentoController extends Controller
{
    /**
     * Listar todas as manutenções
     */
    public function index(Request $request)
    {
        $query = ManutencaoEquipamento::with(['equipamento', 'fornecedor'])
            ->orderBy('dataSolicitacao', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('idEquipamento')) {
            $query->where('idEquipamento', $request->idEquipamento);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $manutencoes = $query->paginate(20)->withQueryString();
        $equipamentos = Equipamento::orderBy('descricao')->get();

        return view('manutencoes.index', compact('manutencoes', 'equipamentos'));
    }

    /**
     * Formulário de criação de manutenção
     */
    public function create(Request $request)
    {
        $equipamentos = Equipamento::where('status', 'Ativo')
            ->orderBy('descricao')
            ->get();

        $fornecedores = Fornecedor::orderBy('razaoSocial')->get();

        $equipamentoSelecionado = null;
        if ($request->filled('idEquipamento')) {
            $equipamentoSelecionado = Equipamento::find($request->idEquipamento);
        }

        return view('manutencoes.create', compact('equipamentos', 'fornecedores', 'equipamentoSelecionado'));
    }

    public function store(StoreManutencaoRequest $request)
    {
        try {
            $dados = $request->validated();
            $dados['dataSolicitacao'] = now()->toDateString();
            $dados['status'] = 'Pendente';

            $equipamento = Equipamento::findOrFail($dados['idEquipamento']);

            if ($equipamento->status === StatusEquipamento::EM_MANUTENCAO) {
                return back()
                    ->with('error', 'Este equipamento já possui uma manutenção em andamento.')
                    ->withInput();
            }

            $manutencao = ManutencaoEquipamento::create($dados);

            $equipamento->update(['status' => StatusEquipamento::EM_MANUTENCAO->value]);

            \App\Models\ActivityLog::create([
                'usuarioId' => Auth::id(),
                'modulo' => 'Equipamentos',
                'acao' => 'registrar_manutencao',
                'entidade' => 'ManutencaoEquipamento',
                'entidadeId' => $manutencao->idManutencao,
                'dados' => [
                    'idEquipamento' => $equipamento->idEquipamento,
                    'equipamento' => $equipamento->descricao,
                    'tipo' => $dados['tipo'],
                    'descricao' => $dados['descricao'],
                ],
            ]);

            return redirect()
                ->route('manutencoes.index')
                ->with('success', 'Manutenção registrada com sucesso! Status do equipamento alterado para "Em Manutenção".');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao registrar manutenção: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(ManutencaoEquipamento $manutencao)
    {
        $manutencao->load(['equipamento', 'fornecedor']);
        return view('manutencoes.show', compact('manutencao'));
    }

    public function edit(ManutencaoEquipamento $manutencao)
    {
        $manutencao->load(['equipamento', 'fornecedor']);
        $fornecedores = Fornecedor::orderBy('razaoSocial')->get();

        return view('manutencoes.edit', compact('manutencao', 'fornecedores'));
    }

    public function update(UpdateManutencaoRequest $request, ManutencaoEquipamento $manutencao)
    {
        try {
            if ($manutencao->status === StatusManutencao::CONCLUIDA) {
                return back()->with('error', 'Esta manutenção já foi finalizada.');
            }

            $dados = $request->validated();

            $manutencao->finalizarManutencao($dados);

            \App\Models\ActivityLog::create([
                'usuarioId' => Auth::id(),
                'modulo' => 'Equipamentos',
                'acao' => 'finalizar_manutencao',
                'entidade' => 'ManutencaoEquipamento',
                'entidadeId' => $manutencao->idManutencao,
                'dados' => [
                    'idEquipamento' => $manutencao->idEquipamento,
                    'equipamento' => $manutencao->equipamento?->descricao ?? 'N/A',
                    'servicoRealizado' => $dados['servicoRealizado'],
                    'custo' => $dados['custo'] ?? null,
                    'responsavel' => $dados['responsavel'],
                ],
            ]);

            return redirect()
                ->route('manutencoes.index')
                ->with('success', 'Manutenção finalizada com sucesso! Status do equipamento alterado para "Ativo".');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao finalizar manutenção: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function finalizar(Request $request, ManutencaoEquipamento $manutencao)
    {
        $request->validate([
            'dataExecucao' => 'required|date',
            'servicoRealizado' => 'required|string|max:2000',
            'custo' => 'nullable|numeric|min:0',
            'responsavel' => 'required|string|max:100',
            'fornecedorId' => 'nullable|integer|exists:fornecedores,idFornecedor',
        ]);

        try {
            if ($manutencao->status === StatusManutencao::CONCLUIDA) {
                return back()->with('error', 'Esta manutenção já foi finalizada.');
            }

            $dados = $request->only(['dataExecucao', 'servicoRealizado', 'custo', 'responsavel', 'fornecedorId']);

            if (isset($dados['fornecedorId'])) {
                $manutencao->fornecedorId = $dados['fornecedorId'];
            }

            $manutencao->finalizarManutencao($dados);

            \App\Models\ActivityLog::create([
                'usuarioId' => Auth::id(),
                'modulo' => 'Equipamentos',
                'acao' => 'finalizar_manutencao',
                'entidade' => 'ManutencaoEquipamento',
                'entidadeId' => $manutencao->idManutencao,
                'dados' => [
                    'idEquipamento' => $manutencao->idEquipamento,
                    'equipamento' => $manutencao->equipamento?->descricao ?? 'N/A',
                    'servicoRealizado' => $dados['servicoRealizado'],
                    'custo' => $dados['custo'] ?? null,
                    'responsavel' => $dados['responsavel'],
                ],
            ]);

            return redirect()
                ->route('manutencoes.index')
                ->with('success', 'Manutenção finalizada com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao finalizar manutenção: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(ManutencaoEquipamento $manutencao)
    {
        try {
            if ($manutencao->status === StatusManutencao::CONCLUIDA) {
                return back()->with('error', 'Não é possível excluir manutenções já finalizadas.');
            }

            $equipamento = $manutencao->equipamento;
            $idEquipamento = $manutencao->idEquipamento;

            $manutencao->delete();

            if ($equipamento) {
                $outrasManutencoes = ManutencaoEquipamento::where('idEquipamento', $idEquipamento)
                    ->where('status', 'Pendente')
                    ->count();

                if ($outrasManutencoes === 0) {
                    $equipamento->update(['status' => StatusEquipamento::ATIVO->value]);
                }
            }

            return redirect()
                ->route('manutencoes.index')
                ->with('success', 'Manutenção excluída com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir manutenção: ' . $e->getMessage());
        }
    }

    public function historico(Equipamento $equipamento)
    {
        $manutencoes = ManutencaoEquipamento::where('idEquipamento', $equipamento->idEquipamento)
            ->with('fornecedor')
            ->orderBy('dataSolicitacao', 'desc')
            ->paginate(20);

        return view('manutencoes.historico', compact('equipamento', 'manutencoes'));
    }
}
