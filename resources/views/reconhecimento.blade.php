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

        <div class="controls hidden">
            <button id="startCameraButton">Iniciar Câmera</button>
            <input type="number" id="clientIdInput">
            <button id="registerFaceButton">Cadastrar Rosto</button>
        </div>

        {{-- <div class="mt-6">
            <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Voltar para o Dashboard
            </a>
        </div> --}}
    </div>
@endsection

@push('body_scripts')
    @vite(['resources/js/app.js'])
@endpush