<?php

namespace App\Http\Controllers;

use App\Models\Academia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreAcademiaRequest;
use App\Http\Requests\UpdateAcademiaRequest;

class AcademiaController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdministrador()) {
            $academias = Auth::user()->academias;
        } else {
            $academias = Academia::where('idAcademia', Auth::user()->idAcademia)->get();
        }
        
        return view('academias.index', compact('academias'));
    }

    public function create()
    {
        if (!Auth::user()->isAdministrador()) {
            abort(403, 'Apenas administradores podem criar academias.');
        }

        return view('academias.create');
    }

    public function store(StoreAcademiaRequest $request)
    {
        if (!Auth::user()->isAdministrador()) {
            abort(403, 'Apenas administradores podem criar academias.');
        }

        $validated = $request->validated();

        $academia = Academia::create($validated);

        Auth::user()->academias()->attach($academia->idAcademia);

        return redirect()->route('academias.index')
                         ->with('success', 'Academia cadastrada com sucesso!');
    }

    public function show(Academia $academia)
    {
        if (!Auth::user()->temAcessoAcademia($academia->idAcademia)) {
            abort(403, 'Você não tem acesso a esta academia.');
        }

        return view('academias.show', compact('academia'));
    }

    public function edit(Academia $academia)
    {
        if (!Auth::user()->temAcessoAcademia($academia->idAcademia)) {
            abort(403, 'Você não tem permissão para editar esta academia.');
        }

        return view('academias.edit', compact('academia'));
    }

    public function update(UpdateAcademiaRequest $request, Academia $academia)
    {
        if (!Auth::user()->temAcessoAcademia($academia->idAcademia)) {
            abort(403, 'Você não tem permissão para editar esta academia.');
        }

        $validated = $request->validated();

        $academia->update($validated);

        return redirect()->route('academias.index')
                        ->with('success', 'Academia atualizada com sucesso!');
    }

    public function trocar(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdministrador()) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }
        
        $request->validate([
            'idAcademia' => 'required|exists:academias,idAcademia'
        ]);
        
        $academiaId = $request->idAcademia;
        
        if (!$user->temAcessoAcademia($academiaId)) {
            return response()->json(['error' => 'Você não tem acesso a esta academia'], 403);
        }
        
        session(['academia_selecionada' => $academiaId]);
        
        return response()->json([
            'success' => true,
            'message' => 'Academia alterada com sucesso',
            'academia' => Academia::find($academiaId)
        ]);
    }

    public function destroy(Academia $academia)
    {
        if (!Auth::user()->isAdministrador()) {
            abort(403, 'Apenas administradores podem excluir academias.');
        }

        if (!$academia->podeDeletar()) {
            return redirect()->route('academias.index')
                           ->with('error', 'Não é possível excluir esta academia pois existem registros associados (clientes, produtos, vendas, entradas, planos ou funcionários).');
        }

        try {
            $academia->delete();
            return redirect()->route('academias.index')
                           ->with('success', 'Academia excluída com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('academias.index')
                           ->with('error', 'Erro ao excluir academia: ' . $e->getMessage());
        }
    }
}