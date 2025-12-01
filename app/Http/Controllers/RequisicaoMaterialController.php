<?php

namespace App\Http\Controllers;

use App\Models\RequisicaoMaterial;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequisicaoMaterialController extends Controller
{
    public function index()
    {
        $requisicoes = RequisicaoMaterial::with('material')->orderByDesc('idRequisicao')->paginate(20);
        return view('materiais.requisicoes.index', compact('requisicoes'));
    }

    public function create()
    {
        $materiais = Material::orderBy('descricao')->get();
        return view('materiais.requisicoes.create', compact('materiais'));
    }

    public function store(Request $request)
    {
        try {
            $dados = $request->validate([
                'idMaterial' => 'required|exists:materiais,idMaterial',
                'quantidade' => 'required|integer|min:1',
                'centroCusto' => 'required|string|max:255',
                'motivo' => 'nullable|string',
            ], [
                'idMaterial.required' => 'O material é obrigatório.',
                'idMaterial.exists' => 'O material selecionado não existe.',
                'quantidade.required' => 'A quantidade é obrigatória.',
                'quantidade.integer' => 'A quantidade deve ser um número inteiro.',
                'quantidade.min' => 'A quantidade deve ser pelo menos 1.',
                'centroCusto.required' => 'O centro de custo é obrigatório.',
            ]);
            $dados['idAcademia'] = Auth::user()->idAcademia ?? config('app.academia_atual');
            $dados['data'] = now();
            $dados['usuarioId'] = Auth::id();

            DB::beginTransaction();

            $material = Material::findOrFail($dados['idMaterial']);
            if ($material->estoque < $dados['quantidade']) {
                return back()->with('error', 'Estoque insuficiente')->withInput();
            }
            $material->estoque -= $dados['quantidade'];
            $material->save();

            $req = RequisicaoMaterial::create($dados);

            DB::table('movimentacoes_materiais')->insert([
                'idAcademia' => $dados['idAcademia'],
                'idMaterial' => $dados['idMaterial'],
                'tipo' => 'saida',
                'quantidade' => $dados['quantidade'],
                'origem' => 'requisicao',
                'referenciaId' => $req->idRequisicao,
                'motivo' => $dados['motivo'] ?? null,
                'dataMovimentacao' => now(),
                'usuarioId' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('materiais.requisicoes.index')->with('success', 'Requisição registrada');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Dados inválidos'], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao registrar requisição')->withInput();
        }
    }
}

