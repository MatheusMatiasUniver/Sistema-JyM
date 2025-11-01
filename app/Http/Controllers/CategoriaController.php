<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $academiaId = $this->getAcademiaId();
        
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }

        $query = Categoria::with('academia')
            ->porAcademia($academiaId)
            ->withCount('produtos');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nome', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->get('sort_by', 'nome');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        // Define default values for sorting
        $sortField = 'nome';
        $sortDirection = 'asc';
        
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'nome_desc':
                    $sortField = 'nome';
                    $sortDirection = 'desc';
                    break;
                case 'produtos_asc':
                    $sortField = 'produtos_count';
                    $sortDirection = 'asc';
                    break;
                case 'produtos_desc':
                    $sortField = 'produtos_count';
                    $sortDirection = 'desc';
                    break;
                case 'status_asc':
                    $sortField = 'status';
                    $sortDirection = 'asc';
                    break;
                case 'status_desc':
                    $sortField = 'status';
                    $sortDirection = 'desc';
                    break;
                default:
                    $sortField = 'nome';
                    $sortDirection = 'asc';
            }
        }

        $categorias = $query->orderBy($sortField, $sortDirection)->paginate(15);

        return view('categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $academiaId = $this->getAcademiaId();
        
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }

        return view('categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $academiaId = $this->getAcademiaId();
        
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }

        $validated = $request->validate([
            'nome' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categorias')->where(function ($query) use ($academiaId) {
                    return $query->where('idAcademia', $academiaId);
                })
            ],
            'descricao' => 'nullable|string',
            'status' => 'required|in:Ativo,Inativo',
        ], [
            'nome.required' => 'O nome da categoria é obrigatório.',
            'nome.unique' => 'Já existe uma categoria com este nome nesta academia.',
            'nome.max' => 'O nome da categoria não pode ter mais de 100 caracteres.',
            'status.required' => 'O status da categoria é obrigatório.',
            'status.in' => 'O status deve ser Ativo ou Inativo.',
        ]);

        try {
            DB::beginTransaction();

            $validated['idAcademia'] = $academiaId;
            
            Categoria::create($validated);

            DB::commit();

            return redirect()->route('categorias.index')
                           ->with('success', 'Categoria cadastrada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao cadastrar categoria: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        $this->verificarAcademiaCategoria($categoria);
        
        $categoria->load(['produtos' => function($query) {
            $query->orderBy('nome');
        }]);

        return view('categorias.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        $this->verificarAcademiaCategoria($categoria);

        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        $this->verificarAcademiaCategoria($categoria);

        $validated = $request->validate([
            'nome' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categorias')->where(function ($query) use ($categoria) {
                    return $query->where('idAcademia', $categoria->idAcademia);
                })->ignore($categoria->idCategoria, 'idCategoria')
            ],
            'descricao' => 'nullable|string',
            'status' => 'required|in:Ativo,Inativo',
        ], [
            'nome.required' => 'O nome da categoria é obrigatório.',
            'nome.unique' => 'Já existe uma categoria com este nome nesta academia.',
            'nome.max' => 'O nome da categoria não pode ter mais de 100 caracteres.',
            'status.required' => 'O status da categoria é obrigatório.',
            'status.in' => 'O status deve ser Ativo ou Inativo.',
        ]);

        try {
            DB::beginTransaction();

            $categoria->update($validated);

            DB::commit();

            return redirect()->route('categorias.index')
                           ->with('success', 'Categoria atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar categoria: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        $this->verificarAcademiaCategoria($categoria);

        if (!$categoria->podeDeletar()) {
            return back()->with('error', 'Não é possível excluir esta categoria pois existem produtos associados a ela.');
        }

        try {
            DB::beginTransaction();

            $categoria->delete();

            DB::commit();

            return redirect()->route('categorias.index')
                           ->with('success', 'Categoria excluída com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Erro ao excluir categoria: ' . $e->getMessage());
        }
    }

    /**
     * Obter ID da academia atual
     */
    private function getAcademiaId(): ?int
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user && $user->isFuncionario() && isset($user->idAcademia)) {
                return $user->idAcademia;
            } elseif ($user && $user->isAdministrador()) {
                return session('academia_selecionada');
            }
        }
        
        return null;
    }

    /**
     * Verificar se a categoria pertence à academia atual
     */
    private function verificarAcademiaCategoria(Categoria $categoria): void
    {
        $academiaId = $this->getAcademiaId();
        
        if (!$academiaId || $categoria->idAcademia !== $academiaId) {
            abort(403, 'Você não tem permissão para acessar esta categoria.');
        }
    }
}
