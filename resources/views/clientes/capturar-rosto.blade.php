<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capturar Rosto - {{ $cliente->nome }} (ID: {{ $cliente->idCliente }})</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])    
</head>
<body>
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <h1>Capturar Rosto do Cliente</h1>

    <div class="cliente-info">
        Cliente: <strong>{{ $cliente->nome }}</strong> (ID: {{ $cliente->idCliente }})
    </div>

    <input type="hidden" id="clientIdInput" value="{{ $cliente->idCliente }}">

    <div class="container-face-recognition">
        <video id="videoElement" width="640" height="480" autoplay muted></video>
        <canvas id="overlayCanvas"></canvas>
    </div>

    <div class="controls">
        <button id="startCameraButton" disabled>Iniciar Câmera</button>
        <button id="registerFaceButton" disabled>Registrar Rosto</button>
    </div>

    <div id="results">Aguardando...</div>
</body>
</html>