<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlanoAssinatura;
use App\Models\Academia;
use App\Services\PlanoAssinaturaService;
use App\Http\Requests\StorePlanoAssinaturaRequest;
use App\Http\Requests\UpdatePlanoAssinaturaRequest;
use Illuminate\Support\Facades\Log;

class PlanoAssinaturaController extends Controller
{
    protected $planoAssinaturaService;

    public function __construct(PlanoAssinaturaService $planoAssinaturaService)
    {
        $this->planoAssinaturaService = $planoAssinaturaService;
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PlanoAssinatura::with('academia');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('idCategoria', $request->categoria_id);
        }

        if ($request->filled('valor_min')) {
            $query->where('valor', '>=', $request->valor_min);
        }

        if ($request->filled('valor_max')) {
            $query->where('valor', '<=', $request->valor_max);
        }

        if ($request->filled('duracao_min')) {
            $query->where('duracaoMeses', '>=', $request->duracao_min);
        }

        if ($request->filled('duracao_max')) {
            $query->where('duracaoMeses', '<=', $request->duracao_max);
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
                case 'valor_asc':
                    $sortField = 'valor';
                    $sortDirection = 'asc';
                    break;
                case 'valor_desc':
                    $sortField = 'valor';
                    $sortDirection = 'desc';
                    break;
                case 'duracao_asc':
                    $sortField = 'duracaoDias';
                    $sortDirection = 'asc';
                    break;
                case 'duracao_desc':
                    $sortField = 'duracaoDias';
                    $sortDirection = 'desc';
                    break;
                default:
                    $sortField = 'nome';
                    $sortDirection = 'asc';
            }
        }

        $planos = $query->orderBy($sortField, $sortDirection)->get();
        
        $academias = \App\Models\Academia::all();
        
        return view('planos.index', compact('planos', 'academias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $academias = Academia::all();
        return view('planos.create', compact('academias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlanoAssinaturaRequest $request)
    {
        try {
            $plano = $this->planoAssinaturaService->createPlano($request->validated());
            return redirect()->route('planos.index')->with('success', 'Plano "' . $plano->nome . '" cadastrado com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro em PlanoAssinaturaController@store: " . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao cadastrar plano: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PlanoAssinatura $plano)
    {
        return redirect()->route('planos.edit', $plano->idPlano);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlanoAssinatura $plano)
    {
        $academias = Academia::all();
        return view('planos.edit', compact('plano', 'academias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlanoAssinaturaRequest $request, PlanoAssinatura $plano)
    {
        try {
            $updatedPlano = $this->planoAssinaturaService->updatePlano($plano, $request->validated());
            return redirect()->route('planos.index')->with('success', 'Plano "' . $updatedPlano->nome . '" atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro em PlanoAssinaturaController@update: " . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao atualizar plano: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlanoAssinatura $plano)
    {
        try {
            $this->planoAssinaturaService->deletePlano($plano);
            return redirect()->route('planos.index')->with('success', 'Plano "' . $plano->nome . '" excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro em PlanoAssinaturaController@destroy: " . $e->getMessage());
            return back()->with('error', 'Erro ao excluir plano: ' . $e->getMessage());
        }
    }
}