@extends('layouts.app')

@section('title', 'Capturar Rosto - {{ $cliente->nome }} (ID: {{ $cliente->idCliente }})')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Capturar Rosto do Cliente</h1>

    <div class="cliente-info mb-4 text-lg text-gray-700">
        Cliente: <strong>{{ $cliente->nome }}</strong> (ID: {{ $cliente->idCliente }})
    </div>

    <input type="hidden" id="clientIdInput" value="{{ $cliente->idCliente }}">


    <div class="relative w-full max-w-[640px] aspect-video mx-auto mb-6 border-4 border-gray-600 rounded-lg overflow-hidden bg-black shadow-xl">
        <video id="videoElement" class="absolute inset-0 w-full h-full object-cover transform scale-x-[-1] z-10"></video>
        <canvas id="overlayCanvas" class="absolute inset-0 w-full h-full transform scale-x-[-1] z-20 pointer-events-none" style="background-color: transparent;"></canvas>
    </div>

    <div class="controls flex flex-col sm:flex-row justify-center items-center gap-4 mb-6">
        <button id="startCameraButton" class="btn-quick-action-blue disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto" style="display: none;">
            Iniciar Câmera
        </button>
        <button id="registerFaceButton" disabled
                class="bg-accent-blue-btn hover:bg-accent-blue-btn-hover text-text-white disabled:opacity-50 disabled:cursor-not-allowed
                       font-bold py-2 px-4 rounded-lg shadow-md transition-all duration-300 w-full sm:w-auto">
            Cadastrar Rosto
        </button>
    </div>

    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-blue-800 text-sm">
            <strong>Importante:</strong> Durante o cadastro de rosto, o sistema de reconhecimento do kiosk será pausado automaticamente para evitar interferências.
        </p>
    </div>

    <div id="result" class="mt-4 p-4 rounded-lg hidden"></div>

    <div class="mt-6">
        <a href="{{ route('clientes.edit', $cliente->idCliente) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Voltar para Edição do Cliente
        </a>
    </div>
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold mb-2">Captura de Rosto</h1>
        <p class="text-lg opacity-90">Cliente: {{ $cliente->nome }}</p>
    </div>

    <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-6 w-full max-w-md">
        <video id="video" autoplay muted class="w-full h-64 bg-black rounded-lg mb-4"></video>
        
        <div class="text-center">
            <button id="capture-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200 mr-3">
                Capturar Rosto
            </button>
            <a href="{{ route('clientes.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                Cancelar
            </a>
        </div>
    </div>
@endsection

@push('body_scripts')
    @vite(['resources/js/app.js'])
@endpush