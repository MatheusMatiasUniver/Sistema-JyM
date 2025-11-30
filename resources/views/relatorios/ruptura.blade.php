@extends('layouts.app')

@section('title', 'Relatório de Ruptura de Estoque - Sistema JyM')

@push('head_styles')
<style>
@media print { .sidebar-layout, .user-info-section { display: none !important; } .main-content-area { margin-left: 0 !important; } }
</style>
@endpush

@section('content')
    <x-relatorio-header 
        titulo="Relatório de Ruptura de Estoque" 
        modulo="Estoque" 
        pdf-route="relatorios.ruptura.pdf" 
    />

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produto</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estoque</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estoque Mínimo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produtos as $p)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $p->nome }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $p->estoque }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $p->estoqueMinimo }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Sem rupturas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($produtos->hasPages())
        <div class="mt-6">{{ $produtos->links() }}</div>
    @endif
@endsection

