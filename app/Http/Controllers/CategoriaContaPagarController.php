<?php

namespace App\Http\Controllers;

use App\Models\ContaPagarCategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriaContaPagarController extends Controller
{
    public function index(Request $request)
    {
        $academiaId = session('academia_selecionada') ?? (Auth::user()->idAcademia ?? null);
        $categorias = ContaPagarCategoria::where('idAcademia', $academiaId)->orderBy('nome')->get();
        return view('financeiro.categorias_contas_pagar.index', compact('categorias'));
    }

    public function create()
    {
        return view('financeiro.categorias_contas_pagar.create');
    }

    public function store(Request $request)
    {
        $academiaId = session('academia_selecionada') ?? (Auth::user()->idAcademia ?? null);
        $dados = $request->validate([
            'nome' => 'required|string|max:100',
        ]);
        ContaPagarCategoria::create([
            'idAcademia' => $academiaId,
            'nome' => $dados['nome'],
            'ativa' => true,
        ]);
        return redirect()->route('financeiro.categorias_contas_pagar.index')->with('success', 'Categoria criada');
    }
}