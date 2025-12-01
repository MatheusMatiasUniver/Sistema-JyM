<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    public function index()
    {
        $materiais = Material::orderBy('descricao')->paginate(20);
        return view('materiais.index', compact('materiais'));
    }

    public function create()
    {
        return view('materiais.create');
    }

    public function store(Request $request)
    {
        try {
            $dados = $request->validate([
                'descricao' => 'required|string|max:255',
                'estoque' => 'required|integer|min:0',
                'unidadeMedida' => 'nullable|string|max:10',
                'estoqueMinimo' => 'nullable|integer|min:0',
            ], [
                'descricao.required' => 'A descrição do material é obrigatória.',
                'estoque.required' => 'O estoque é obrigatório.',
                'estoque.integer' => 'O estoque deve ser um número inteiro.',
                'estoque.min' => 'O estoque não pode ser negativo.',
                'estoqueMinimo.integer' => 'O estoque mínimo deve ser um número inteiro.',
                'estoqueMinimo.min' => 'O estoque mínimo não pode ser negativo.',
            ]);
            $dados['idAcademia'] = Auth::user()->idAcademia ?? config('app.academia_atual');
            Material::create($dados);
            return redirect()->route('materiais.index')->with('success', 'Material cadastrado');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Dados inválidos'], 400);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao cadastrar material')->withInput();
        }
    }

    public function edit(Material $material)
    {
        return view('materiais.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        try {
            $dados = $request->validate([
                'descricao' => 'required|string|max:255',
                'estoque' => 'required|integer|min:0',
                'unidadeMedida' => 'nullable|string|max:10',
                'estoqueMinimo' => 'nullable|integer|min:0',
            ], [
                'descricao.required' => 'A descrição do material é obrigatória.',
                'estoque.required' => 'O estoque é obrigatório.',
                'estoque.integer' => 'O estoque deve ser um número inteiro.',
                'estoque.min' => 'O estoque não pode ser negativo.',
                'estoqueMinimo.integer' => 'O estoque mínimo deve ser um número inteiro.',
                'estoqueMinimo.min' => 'O estoque mínimo não pode ser negativo.',
            ]);
            $material->update($dados);
            return redirect()->route('materiais.index')->with('success', 'Material atualizado');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Dados inválidos'], 400);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar material')->withInput();
        }
    }

    public function destroy(Material $material)
    {
        try {
            $material->delete();
            return redirect()->route('materiais.index')->with('success', 'Material excluído');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir material');
        }
    }
}

