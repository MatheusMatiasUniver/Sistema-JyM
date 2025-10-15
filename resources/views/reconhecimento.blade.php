<!-- resources/views/reconhecimento.blade.php -->
@extends('layouts.kiosk')

@section('title', 'Reconhecimento Facial - Acesso à Academia')

@section('content')
    <div class="bg-black/80 shadow-2xl rounded-lg p-8 max-w-full lg:max-w-4xl w-full mx-auto text-center">

        <div class="relative w-full max-w-[1280px] aspect-video mx-auto mb-8 border-4 border-gray-600 rounded-lg overflow-hidden bg-black shadow-xl">
            <video id="videoElement" class="absolute top-0 left-0 w-full h-full object-cover transform scale-x-[-1]"></video>
            <canvas id="overlayCanvas" class="absolute top-0 left-0 w-full h-full transform scale-x-[-1]" style="background-color: transparent;" ></canvas>
        </div>

        <div id="results" class="alert-info px-6 py-8 rounded-lg relative min-h-[150px] flex items-center justify-center text-center shadow-lg">
            <p class="text-3xl md:text-4xl lg:text-5xl font-bold">Aguardando modelos e câmera...</p>
        </div>

        <div id="code-input-area" class="hidden mt-4">
            <input type="text" id="cpfInput" placeholder="Digite seu CPF (apenas números)"
                   class="w-full p-4 text-center text-4xl font-bold bg-gray-800 text-white rounded-lg mb-4"
                   maxlength="11" pattern="\\d*" inputmode="numeric">

            <input type="password" id="accessCodeInput" placeholder="Digite seu código de acesso (6 dígitos)"
                   class="w-full p-4 text-center text-4xl font-bold bg-gray-800 text-white rounded-lg"
                   maxlength="6" pattern="\\d*" inputmode="numeric">
            <button id="submitCodeBtn" class="mt-4 bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-2xl">
                Confirmar
            </button>
            <button id="cancelCodeBtn" class="mt-4 ml-4 bg-red-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-2xl">
                Cancelar
            </button>
        </div>
    </div>
@endsection

@push('body_scripts')
    @vite(['resources/js/app.js'])
@endpush