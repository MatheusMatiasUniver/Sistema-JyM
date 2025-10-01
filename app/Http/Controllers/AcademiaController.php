<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Academia;
use App\Services\AcademiaService;
use App\Http\Requests\StoreAcademiaRequest;
use App\Http\Requests\UpdateAcademiaRequest;
use Illuminate\Support\Facades\Log;

class AcademiaController extends Controller
{
    protected $academiaService;

    public function __construct(AcademiaService $academiaService)
    {
        $this->academiaService = $academiaService;
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academias = $this->academiaService->getAllAcademias();
        return view('academias.index', compact('academias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('academias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAcademiaRequest $request)
    {
        try {
            $academia = $this->academiaService->createAcademia($request->validated());
            return redirect()->route('academias.index')->with('success', 'Academia ' . $academia->nome . ' cadastrada com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro em AcademiaController@store: " . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao cadastrar academia: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Academia $academia)
    {
        return redirect()->route('academias.edit', $academia->idAcademia);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Academia $academia)
    {
        return view('academias.edit', compact('academia'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAcademiaRequest $request, Academia $academia)
    {
        try {
            $updatedAcademia = $this->academiaService->updateAcademia($academia, $request->validated());
            return redirect()->route('academias.index')->with('success', 'Academia ' . $updatedAcademia->nome . ' atualizada com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro em AcademiaController@update: " . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao atualizar academia: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Academia $academia)
    {
        try {
            $this->academiaService->deleteAcademia($academia);
            return redirect()->route('academias.index')->with('success', 'Academia ' . $academia->nome . ' excluída com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro em AcademiaController@destroy: " . $e->getMessage());
            return back()->with('error', 'Erro ao excluir academia: ' . $e->getMessage());
        }
    }
}