@extends('layouts.app') {{-- Usa o layout principal do sistema --}}

@section('title', 'Capturar Rosto - {{ $cliente->nome }} (ID: {{ $cliente->idCliente }})')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Capturar Rosto do Cliente</h1>

    <div class="cliente-info mb-4 text-lg text-gray-700">
        Cliente: <strong>{{ $cliente->nome }}</strong> (ID: {{ $cliente->idCliente }})
    </div>

    <input type="hidden" id="clientIdInput" value="{{ $cliente->idCliente }}">

    {{-- Contêiner do vídeo, pode ter um tamanho mais moderado para a tela de funcionário --}}
    <div class="relative w-full max-w-[640px] aspect-video mx-auto mb-6 border-4 border-gray-600 rounded-lg overflow-hidden bg-black shadow-xl">
        <video id="videoElement" class="absolute inset-0 w-full h-full object-cover transform scale-x-[-1] z-10"></video>
        <canvas id="overlayCanvas" class="absolute inset-0 w-full h-full transform scale-x-[-1] z-20 pointer-events-none" style="background-color: transparent;"></canvas>
    </div>

    <div class="controls flex flex-col sm:flex-row justify-center items-center gap-4 mb-6">
        <button id="startCameraButton" class="btn-quick-action-blue disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto">
            Iniciar Câmera
        </button>
        <button id="registerFaceButton" disabled
                class="bg-accent-blue-btn hover:bg-accent-blue-btn-hover text-text-white disabled:opacity-50 disabled:cursor-not-allowed
                       font-bold py-2 px-4 rounded-lg shadow-md transition-all duration-300 w-full sm:w-auto">
            Cadastrar Rosto
        </button>
    </div>

    {{-- Div de resultados --}}
    <div id="results" class="alert-info face-capture-alert px-4 py-3 rounded relative min-h-[80px] flex items-center justify-center text-center shadow-md">
        <p class="main-message">Aguardando modelos e câmera...</p>
    </div>

    <div class="mt-6">
        <a href="{{ route('clientes.edit', $cliente->idCliente) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Voltar para Edição do Cliente
        </a>
    </div>
@endsection

@push('body_scripts')
    @vite(['resources/js/app.js'])
@endpush