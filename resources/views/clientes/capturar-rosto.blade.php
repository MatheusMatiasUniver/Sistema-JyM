<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capturar Rosto - {{ $cliente->nome }} (ID: {{ $cliente->idCliente }})</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
            background-color: #e0f7fa;
            color: #263238;
            margin: 0;
            min-height: 100vh;
        }
        h1 {
            color: #00796b;
            margin-bottom: 20px;
        }
        .cliente-info {
            font-size: 1.4em;
            margin-bottom: 25px;
            color: #004d40;
            background-color: #b2dfdb;
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .container-face-recognition {
            position: relative;
            width: 640px;
            height: 480px;
            margin-bottom: 20px;
            border: 5px solid #00796b;
            border-radius: 10px;
            overflow: hidden;
            background-color: #000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .container-face-recognition video,
        .container-face-recognition canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transform: scaleX(-1);
        }
        .controls {
            margin-top: 15px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        .controls button {
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .controls button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(0,0,0,0.15);
        }
        .controls button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            opacity: 0.7;
            box-shadow: none;
        }
        #startCameraButton {
            background-color: #4CAF50;
            color: white;
        }
        #startCameraButton:hover:not(:disabled) {
            background-color: #45a049;
        }
        #registerFaceButton {
            background-color: #2196F3;
            color: white;
        }
        #registerFaceButton:hover:not(:disabled) {
            background-color: #1e88e5;
        }
        #results {
            margin-top: 30px;
            padding: 20px;
            border-radius: 10px;
            background-color: #ffffff;
            color: #3f51b5;
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
            width: 100%;
            max-width: 640px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
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