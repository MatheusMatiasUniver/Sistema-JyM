<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProdutoController extends Controller
{
    public function index()
    {
        $produtos = Produto::with('categoria')->get();
        return view('produtos.index', compact('produtos'));
    }

    public function create()
    {
        $academiaId = $this->getAcademiaId();
        
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }

        $categorias = Categoria::ativas()
            ->porAcademia($academiaId)
            ->orderBy('nome')
            ->get();

        return view('produtos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $academiaId = $this->getAcademiaId();
        
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'idCategoria' => 'required|exists:categorias,idCategoria',
            'preco' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
            'descricao' => 'nullable|string',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nome.required' => 'O nome do produto é obrigatório.',
            'idCategoria.required' => 'A categoria é obrigatória.',
            'idCategoria.exists' => 'A categoria selecionada não existe.',
            'preco.required' => 'O preço é obrigatório.',
            'preco.numeric' => 'O preço deve ser um número.',
            'preco.min' => 'O preço deve ser maior ou igual a zero.',
            'estoque.required' => 'O estoque é obrigatório.',
            'estoque.integer' => 'O estoque deve ser um número inteiro.',
            'estoque.min' => 'O estoque deve ser maior ou igual a zero.',
            'imagem.image' => 'O arquivo deve ser uma imagem.',
            'imagem.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif.',
            'imagem.max' => 'A imagem não pode ser maior que 2MB.',
        ]);

        $categoria = Categoria::where('idCategoria', $validated['idCategoria'])
            ->where('idAcademia', $academiaId)
            ->first();

        if (!$categoria) {
            return back()->withInput()->withErrors(['idCategoria' => 'A categoria selecionada não pertence a esta academia.']);
        }

        $validated['idAcademia'] = $academiaId;

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
        $academiaId = $this->getAcademiaId();
        
        if (!$academiaId || $produto->idAcademia !== $academiaId) {
            abort(403, 'Você não tem permissão para editar este produto.');
        }

        $categorias = Categoria::ativas()
            ->porAcademia($academiaId)
            ->orderBy('nome')
            ->get();

        return view('produtos.edit', compact('produto', 'categorias'));
    }

    public function update(Request $request, Produto $produto)
    {
        $academiaId = $this->getAcademiaId();
        
        if (!$academiaId || $produto->idAcademia !== $academiaId) {
            abort(403, 'Você não tem permissão para editar este produto.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'idCategoria' => 'required|exists:categorias,idCategoria',
            'preco' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
            'descricao' => 'nullable|string',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nome.required' => 'O nome do produto é obrigatório.',
            'idCategoria.required' => 'A categoria é obrigatória.',
            'idCategoria.exists' => 'A categoria selecionada não existe.',
            'preco.required' => 'O preço é obrigatório.',
            'preco.numeric' => 'O preço deve ser um número.',
            'preco.min' => 'O preço deve ser maior ou igual a zero.',
            'estoque.required' => 'O estoque é obrigatório.',
            'estoque.integer' => 'O estoque deve ser um número inteiro.',
            'estoque.min' => 'O estoque deve ser maior ou igual a zero.',
            'imagem.image' => 'O arquivo deve ser uma imagem.',
            'imagem.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif.',
            'imagem.max' => 'A imagem não pode ser maior que 2MB.',
        ]);

        $categoria = Categoria::where('idCategoria', $validated['idCategoria'])
            ->where('idAcademia', $academiaId)
            ->first();

        if (!$categoria) {
            return back()->withInput()->withErrors(['idCategoria' => 'A categoria selecionada não pertence a esta academia.']);
        }

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

        if (!$produto->podeDeletar()) {
            return back()->with('error', 'Não é possível excluir este produto pois existem vendas associadas a ele.');
        }

        try {
            if ($produto->imagem) {
                Storage::disk('public')->delete($produto->imagem);
            }

            $produto->delete();

            return redirect()->route('produtos.index')
                             ->with('success', 'Produto excluído com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir produto: ' . $e->getMessage());
        }
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

    private function getAcademiaId()
    {
        return Auth::user()->idAcademia ?? config('app.academia_atual');
    }
}