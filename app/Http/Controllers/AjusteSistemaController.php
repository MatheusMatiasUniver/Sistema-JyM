<?php

namespace App\Http\Controllers;

use App\Models\AjusteSistema;
use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AjusteSistemaController extends Controller
{
    public function index(Request $request)
    {
        $academiaId = session('academia_selecionada') ?? (Auth::user()->idAcademia ?? null);
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }

        $ajuste = AjusteSistema::where('idAcademia', $academiaId)->first();
        if (!$ajuste) {
            $ajuste = AjusteSistema::create([
                'idAcademia' => $academiaId,
                'diaVencimentoSalarios' => 5,
            ]);
        }

        return view('ajustes.index', compact('ajuste'));
    }

    public function update(Request $request)
    {
        $academiaId = session('academia_selecionada') ?? (Auth::user()->idAcademia ?? null);
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }

        $dados = $request->validate([
            'diaVencimentoSalarios' => 'required|integer|min:1|max:31',
        ]);

        $ajuste = AjusteSistema::where('idAcademia', $academiaId)->first();
        if (!$ajuste) {
            $ajuste = new AjusteSistema();
            $ajuste->idAcademia = $academiaId;
        }

        $ajuste->diaVencimentoSalarios = $dados['diaVencimentoSalarios'];
        $ajuste->save();

        \App\Models\ActivityLog::create([
            'usuarioId' => \Illuminate\Support\Facades\Auth::id(),
            'modulo' => 'AjustesSistema',
            'acao' => 'update',
            'entidade' => 'AjusteSistema',
            'entidadeId' => null,
            'dados' => [
                'idAcademia' => $academiaId,
                'diaVencimentoSalarios' => $ajuste->diaVencimentoSalarios,
            ],
        ]);

        return redirect()->route('ajustes.index')->with('success', 'Ajustes atualizados');
    }
}
