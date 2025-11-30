@extends('layouts.app')

@section('title', 'Compra - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Compra #{{ $compra->idCompra }}</h1>

    <div class="mb-6 bg-white p-4 rounded-lg shadow-md text-black">
        <p><strong>Fornecedor:</strong> {{ $compra->fornecedor->razaoSocial }}</p>
        <p><strong>Data:</strong> {{ $compra->dataEmissao->format('d/m/Y H:i') }}</p>
        <p><strong>Status:</strong> {{ ucfirst($compra->status) }}</p>
        <p><strong>Total:</strong> R$ {{ number_format($compra->valorTotal,2,',','.') }}</p>
        @if($compra->status==='aberta')
            <form action="{{ route('compras.receber', $compra->idCompra) }}" method="POST" class="mt-3" 
                  data-confirm="Confirmar recebimento desta compra?"
                  data-confirm-title="Receber Compra"
                  data-confirm-icon="info"
                  data-confirm-text="Confirmar"
                  data-cancel-text="Cancelar">
                @csrf
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Receber</button>
            </form>
        @endif
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produto</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qtd</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Preço Unit.</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($compra->itens as $item)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $item->produto->nome }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $item->quantidade }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($item->precoUnitario,2,',','.') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">R$ {{ number_format($item->precoUnitario * $item->quantidade,2,',','.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

