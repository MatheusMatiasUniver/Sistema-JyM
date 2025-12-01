@props([
    'titulo',
    'modulo',
    'pdfRoute' => null
])

@php
    $academiaId = config('app.academia_atual');
    $academia = $academiaId ? \App\Models\Academia::find($academiaId) : null;
@endphp

<div class="mb-6 print:hidden">
    <h1 class="text-3xl font-bold text-grip-6 mb-4">{{ $titulo }}</h1>
    
    <div class="flex flex-wrap gap-3">
        @if($pdfRoute)
            <a href="{{ route($pdfRoute, request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                <i class="fas fa-file-pdf mr-2"></i> Exportar PDF
            </a>
        @endif
        <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
            <i class="fas fa-print mr-2"></i> Imprimir
        </button>
    </div>
</div>

<div class="hidden print:block print:mb-6">
    <div class="border-b-2 border-gray-800 pb-4 mb-4">
        <h1 class="text-2xl font-bold text-gray-900">{{ $academia->nome ?? 'Academia' }}</h1>
        <h2 class="text-xl font-semibold text-gray-700 mt-1">{{ $titulo }}</h2>
    </div>
    <div class="flex justify-between text-sm text-gray-600 mb-4">
        <div>
            <p><span class="font-medium">Módulo:</span> {{ $modulo }}</p>
            <p><span class="font-medium">Emitido por:</span> {{ Auth::user()->nome ?? Auth::user()->name ?? 'N/A' }}</p>
        </div>
        <div class="text-right">
            <p><span class="font-medium">Data de Emissão:</span> {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</div>
