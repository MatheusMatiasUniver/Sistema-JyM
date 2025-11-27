<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarcaController extends Controller
{
    public function index(Request $request)
    {
        $academiaId = $this->getAcademiaId();
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }

        $query = Marca::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('paisOrigem', 'like', "%{$search}%")
                  ->orWhere('site', 'like', "%{$search}%");
            });
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === '1');
        }

        $marcas = $query->orderBy('nome')->paginate(15)->appends($request->query());

        return view('marcas.index', compact('marcas'));
    }

    public function create()
    {
        $academiaId = $this->getAcademiaId();
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }
        return view('marcas.create');
    }

    public function store(Request $request)
    {
        $academiaId = $this->getAcademiaId();
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'paisOrigem' => 'nullable|string|max:50',
            'site' => 'nullable|url|max:255',
            'ativo' => 'nullable|boolean',
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 100 caracteres.',
            'paisOrigem.max' => 'O país de origem não pode ter mais de 50 caracteres.',
            'site.url' => 'O site deve ser uma URL válida.',
        ]);

        $validated['idAcademia'] = $academiaId;
        $validated['ativo'] = $validated['ativo'] ?? true;

        Marca::create($validated);

        return redirect()->route('marcas.index')->with('success', 'Marca cadastrada com sucesso!');
    }

    public function edit(Marca $marca)
    {
        $academiaId = $this->getAcademiaId();
        if (!$academiaId || ($marca->idAcademia !== null && $marca->idAcademia !== $academiaId)) {
            abort(403, 'Você não tem permissão para editar esta marca.');
        }
        return view('marcas.edit', compact('marca'));
    }

    public function update(Request $request, Marca $marca)
    {
        $academiaId = $this->getAcademiaId();
        if (!$academiaId || ($marca->idAcademia !== null && $marca->idAcademia !== $academiaId)) {
            abort(403, 'Você não tem permissão para editar esta marca.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'paisOrigem' => 'nullable|string|max:50',
            'site' => 'nullable|url|max:255',
            'ativo' => 'nullable|boolean',
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 100 caracteres.',
            'paisOrigem.max' => 'O país de origem não pode ter mais de 50 caracteres.',
            'site.url' => 'O site deve ser uma URL válida.',
        ]);

        $validated['ativo'] = $validated['ativo'] ?? $marca->ativo;

        $marca->update($validated);

        return redirect()->route('marcas.index')->with('success', 'Marca atualizada com sucesso!');
    }

    public function destroy(Marca $marca)
    {
        $academiaId = $this->getAcademiaId();
        if (!$academiaId || ($marca->idAcademia !== null && $marca->idAcademia !== $academiaId)) {
            abort(403, 'Você não tem permissão para excluir esta marca.');
        }

        if (Produto::where('idMarca', $marca->idMarca)->exists()) {
            return back()->with('error', 'Não é possível excluir a marca pois existem produtos associados.');
        }

        $marca->delete();

        return redirect()->route('marcas.index')->with('success', 'Marca excluída com sucesso!');
    }

    private function getAcademiaId()
    {
        return Auth::user()->idAcademia ?? session('academia_selecionada') ?? config('app.academia_atual');
    }
}