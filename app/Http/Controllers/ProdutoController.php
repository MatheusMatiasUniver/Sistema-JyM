<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    public function index()
    {
        $produtos = Produto::with('academia')->paginate(15);
        return view('produtos.index', compact('produtos'));
    }

    public function create()
    {
        return view('produtos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'descricao' => 'nullable|string',
        ]);

        $validated['idAcademia'] = config('app.academia_atual');

        if ($request->hasFile('imagem')) {
            $validated['imagem'] = $request->file('imagem')->store('produtos', 'public');
        }

        Produto::create($validated);

        return redirect()->route('produtos.index')
                         ->with('success', 'Produto cadastrado com sucesso!');
    }

    public function show(Produto $produto)
    {
        if ($produto->idAcademia !== config('app.academia_atual')) {
            abort(403, 'Você não tem permissão para visualizar este produto.');
        }

        return view('produtos.show', compact('produto'));
    }

    public function edit(Produto $produto)
    {
        if ($produto->idAcademia !== config('app.academia_atual')) {
            abort(403, 'Você não tem permissão para editar este produto.');
        }

        return view('produtos.edit', compact('produto'));
    }

    public function update(Request $request, Produto $produto)
    {
        if ($produto->idAcademia !== config('app.academia_atual')) {
            abort(403, 'Você não tem permissão para editar este produto.');
        }

        $validated = $request->validate([
            'descricao' => 'nullable|string',
        ]);

        if ($request->hasFile('imagem')) {
            if ($produto->imagem) {
                Storage::disk('public')->delete($produto->imagem);
            }
            $validated['imagem'] = $request->file('imagem')->store('produtos', 'public');
        }

        $produto->update($validated);

        return redirect()->route('produtos.index')
                         ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Produto $produto)
    {
        if ($produto->idAcademia !== config('app.academia_atual')) {
            abort(403, 'Você não tem permissão para excluir este produto.');
        }

        if ($produto->imagem) {
            Storage::disk('public')->delete($produto->imagem);
        }

        $produto->delete();

        return redirect()->route('produtos.index')
                         ->with('success', 'Produto excluído com sucesso!');
    }

    public function ajustarEstoque(Request $request, Produto $produto)
    {
        if ($produto->idAcademia !== config('app.academia_atual')) {
            abort(403, 'Você não tem permissão para ajustar o estoque deste produto.');
        }

        $validated = $request->validate([
            'tipo' => 'required|in:adicionar,remover',
        ]);

        if ($validated['tipo'] === 'adicionar') {
            $produto->adicionarEstoque($validated['quantidade']);
            $mensagem = 'Estoque adicionado com sucesso!';
        } else {
            if (!$produto->baixarEstoque($validated['quantidade'])) {
                return back()->with('error', 'Estoque insuficiente!');
            }
            $mensagem = 'Estoque removido com sucesso!';
        }

        return back()->with('success', $mensagem);
    }
}