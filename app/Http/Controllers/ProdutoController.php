<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Services\ProdutoService;
use App\Http\Requests\StoreProdutoRequest;
use App\Http\Requests\UpdateProdutoRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    protected $produtoService;

    public function __construct(ProdutoService $produtoService)
    {
        $this->produtoService = $produtoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produtos = $this->produtoService->getAllProdutos();
        return view('produtos.index', compact('produtos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('produtos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProdutoRequest $request)
    {
        try {
            $produto = $this->produtoService->createProduto(
                $request->validated(),
                $request->file('imagem')
            );
            return redirect()->route('produtos.index')->with('success', 'Produto ' . $produto->nome . ' cadastrado com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro em ProdutoController@store: " . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao cadastrar produto: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        return redirect()->route('produtos.edit', $produto->idProduto);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produto $produto)
    {
        return view('produtos.edit', compact('produto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProdutoRequest $request, Produto $produto)
    {
        try {
            $updatedProduto = $this->produtoService->updateProduto(
                $produto,
                $request->validated(),
                $request->file('imagem')
            );
            return redirect()->route('produtos.index')->with('success', 'Produto ' . $updatedProduto->nome . ' atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro em ProdutoController@update: " . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao atualizar produto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        try {
            $this->produtoService->deleteProduto($produto);
            return redirect()->route('produtos.index')->with('success', 'Produto ' . $produto->nome . ' excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro em ProdutoController@destroy: " . $e->getMessage());
            return back()->with('error', 'Erro ao excluir produto: ' . $e->getMessage());
        }
    }
}