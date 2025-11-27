<?php

namespace App\Http\Controllers;

use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FornecedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Fornecedor::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('razaoSocial', 'like', "%{$search}%")
                  ->orWhere('cnpjCpf', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $fornecedores = $query->orderBy('razaoSocial')->paginate(20);

        return view('fornecedores.index', compact('fornecedores'));
    }

    public function create()
    {
        return view('fornecedores.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'razaoSocial' => 'required|string|max:255',
                'cnpjCpf' => 'nullable|string|max:20',
                'inscricaoEstadual' => 'nullable|string|max:30',
                'contato' => 'nullable|string|max:100',
                'telefone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'endereco' => 'nullable|string|max:255',
                'condicaoPagamentoPadrao' => 'nullable|string|max:100',
                'ativo' => 'nullable|boolean',
            ]);

            $validated['idAcademia'] = Auth::user()->idAcademia ?? config('app.academia_atual');
            $validated['ativo'] = $validated['ativo'] ?? true;

            Fornecedor::create($validated);

            return redirect()->route('fornecedores.index')->with('success', 'Fornecedor criado com sucesso');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Dados inválidos'], 400);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao criar fornecedor')->withInput();
        }
    }

    public function edit(Fornecedor $fornecedor)
    {
        return view('fornecedores.edit', compact('fornecedor'));
    }

    public function show(Fornecedor $fornecedor)
    {
        return view('fornecedores.show', compact('fornecedor'));
    }

    public function update(Request $request, Fornecedor $fornecedor)
    {
        try {
            $validated = $request->validate([
                'razaoSocial' => 'required|string|max:255',
                'cnpjCpf' => 'nullable|string|max:20',
                'inscricaoEstadual' => 'nullable|string|max:30',
                'contato' => 'nullable|string|max:100',
                'telefone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'endereco' => 'nullable|string|max:255',
                'condicaoPagamentoPadrao' => 'nullable|string|max:100',
                'ativo' => 'nullable|boolean',
            ]);

            $fornecedor->update($validated);

            return redirect()->route('fornecedores.index')->with('success', 'Fornecedor atualizado com sucesso');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Dados inválidos'], 400);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar fornecedor')->withInput();
        }
    }

    public function destroy(Fornecedor $fornecedor)
    {
        try {
            $fornecedor->delete();
            return redirect()->route('fornecedores.index')->with('success', 'Fornecedor excluído com sucesso');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir fornecedor');
        }
    }
}
