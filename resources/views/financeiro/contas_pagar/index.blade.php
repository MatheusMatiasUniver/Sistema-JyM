@extends('layouts.app')

@section('title', 'Contas a Pagar - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Contas a Pagar</h1>

    <x-search-filter-dropdown 
        placeholder="Pesquisar contas a pagar..."
        :filters="[
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['aberta' => 'Aberta', 'paga' => 'Paga', 'cancelada' => 'Cancelada']],
            ['name' => 'data_inicial', 'label' => 'Vencimento Inicial', 'type' => 'date'],
            ['name' => 'data_final', 'label' => 'Vencimento Final', 'type' => 'date'],
        ]"
        :sort-options="[]"
    />

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fornecedor</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Descrição</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Valor</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vencimento</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contas as $c)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ optional($c->fornecedor)->razaoSocial ?? '—' }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $c->descricao }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($c->valorTotal,2,',','.') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $c->dataVencimento->format('d/m/Y') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ ucfirst($c->status) }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            @if($c->status === 'aberta')
                                <form action="{{ route('financeiro.contas_pagar.pagar', $c->idContaPagar) }}" method="POST" class="flex items-center space-x-2">
                                    @csrf
                                    <select name="formaPagamento" class="select">
                                        <option value="Dinheiro">Dinheiro</option>
                                        <option value="Cartão de Crédito">Cartão de Crédito</option>
                                        <option value="Cartão de Débito">Cartão de Débito</option>
                                        <option value="PIX">PIX</option>
                                        <option value="Boleto">Boleto</option>
                                    </select>
                                    <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline">Faturar</button>
                                </form>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Nenhuma conta encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($contas->hasPages())
        <div class="mt-6">{{ $contas->links() }}</div>
    @endif
@endsection
