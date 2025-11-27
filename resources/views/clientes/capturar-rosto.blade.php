@extends('layouts.app')

@section('title', 'Capturar Rosto - {{ $cliente->nome }} (ID: {{ $cliente->idCliente }})')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Capturar Rosto do Cliente</h1>

    <div class="cliente-info mb-4 text-lg text-gray-700">
        Cliente: <strong>{{ $cliente->nome }}</strong> (ID: {{ $cliente->idCliente }})
    </div>

    @if(session('access_code'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
            <strong>Código de acesso gerado:</strong>
            <span class="ml-2 text-black">{{ session('access_code') }}</span>
        </div>
    @endif

    <input type="hidden" id="clientIdInput" value="{{ $cliente->idCliente }}" class="text-black">


    <div class="relative w-full max-w-[640px] aspect-video mx-auto mb-6 border-4 border-gray-600 rounded-lg overflow-hidden bg-black shadow-xl">
        <video id="videoElement" class="absolute inset-0 w-full h-full object-cover transform scale-x-[-1] z-10"></video>
        <canvas id="overlayCanvas" class="absolute inset-0 w-full h-full transform scale-x-[-1] z-20 pointer-events-none" style="background-color: transparent;"></canvas>
    </div>

    <div class="controls flex flex-col sm:flex-row justify-center items-center gap-4 mb-6">
        <button id="startCameraButton" class="bg-grip-1 hover:bg-grip-2 text-white disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto" style="display: none;">
            Iniciar Câmera
        </button>
        <button id="registerFaceButton" disabled
                class="bg-grip-1 hover:bg-grip-2 text-white disabled:opacity-50 disabled:cursor-not-allowed
                       font-bold py-2 px-4 rounded-lg shadow-md transition-all duration-300 w-full sm:w-auto">
            Cadastrar Rosto
        </button>
    </div>

<div class="mb-4 p-4 bg-grip-4 border border-border-light rounded-lg">
    <p class="text-grip-3 text-sm">
            <strong>Importante:</strong> Durante o cadastro de rosto, o sistema de reconhecimento do kiosk será pausado automaticamente para evitar interferências.
        </p>
    </div>

    <div id="results" class="mt-4 p-4 rounded-lg hidden"></div>

    <div class="mt-6">
        <a href="{{ route('clientes.edit', $cliente->idCliente) }}" class="bg-grip-1 hover:bg-grip-2 hover:text-white text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Voltar para Edição do Cliente
        </a>
    </div>
    
@endsection

@push('body_scripts')
    @vite(['resources/js/app.js'])
@endpush
