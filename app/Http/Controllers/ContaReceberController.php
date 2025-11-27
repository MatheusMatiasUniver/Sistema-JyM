<?php

namespace App\Http\Controllers;

use App\Models\ContaReceber;
use Illuminate\Http\Request;

class ContaReceberController extends Controller
{
    public function index(Request $request)
    {
        $query = ContaReceber::with('cliente');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('data_inicial')) {
            $query->whereDate('dataVencimento', '>=', $request->data_inicial);
        }
        if ($request->filled('data_final')) {
            $query->whereDate('dataVencimento', '<=', $request->data_final);
        }
        $contas = $query->orderByDesc('idContaReceber')->paginate(20);
        return view('financeiro.contas_receber.index', compact('contas'));
    }
}