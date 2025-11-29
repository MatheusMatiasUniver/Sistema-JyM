<?php

namespace App\Http\Controllers;

use App\Models\Equipamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipamentoController extends Controller
{
    public function index(Request $request)
    {
        $equipamentos = Equipamento::orderBy('descricao')->paginate(20);
        return view('equipamentos.index', compact('equipamentos'));
    }

    public function create()
    {
        return view('equipamentos.create');
    }

    public function store(Request $request)
    {
        try {
            $dados = $request->validate([
                'descricao' => 'required|string|max:255',
                'fabricante' => 'nullable|string|max:255',
                'modelo' => 'nullable|string|max:255',
                'numeroSerie' => 'nullable|string|max:255',
                'dataAquisicao' => 'nullable|date',
                'valorAquisicao' => 'nullable|numeric|min:0',
                'garantiaFim' => 'nullable|date',
                'centroCusto' => 'nullable|string|max:255',
                'status' => 'nullable|string|max:50',
            ]);
            $dados['idAcademia'] = Auth::user()->idAcademia ?? config('app.academia_atual');
            Equipamento::create($dados);
            return redirect()->route('equipamentos.index')->with('success', 'Equipamento cadastrado');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Dados inválidos'], 400);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao cadastrar equipamento')->withInput();
        }
    }

    public function edit(Equipamento $equipamento)
    {
        return view('equipamentos.edit', compact('equipamento'));
    }

    public function update(Request $request, Equipamento $equipamento)
    {
        try {
            $dados = $request->validate([
                'descricao' => 'required|string|max:255',
                'fabricante' => 'nullable|string|max:255',
                'modelo' => 'nullable|string|max:255',
                'numeroSerie' => 'nullable|string|max:255',
                'dataAquisicao' => 'nullable|date',
                'valorAquisicao' => 'nullable|numeric|min:0',
                'garantiaFim' => 'nullable|date',
                'centroCusto' => 'nullable|string|max:255',
                'status' => 'nullable|string|max:50',
            ]);
            $equipamento->update($dados);
            return redirect()->route('equipamentos.index')->with('success', 'Equipamento atualizado');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Dados inválidos'], 400);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar equipamento')->withInput();
        }
    }

    public function destroy(Equipamento $equipamento)
    {
        try {
            $equipamento->delete();
            return redirect()->route('equipamentos.index')->with('success', 'Equipamento excluído');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir equipamento');
        }
    }

}
