<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reconhecimento Facial - Sistema JyM</title>    

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
            background-color: #f0f2f5;
            color: #333;
        }
        .container-face-recognition {
            position: relative;
            width: 640px;
            height: 480px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            background-color: #000;
        }
        .container-face-recognition video,
        .container-face-recognition canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .controls {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        .controls button, .controls input {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .controls button:hover:not(:disabled) {
            opacity: 0.9;
        }
        .controls button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        #startCameraButton { background-color: #28a745; color: white; }
        #authenticateButton { background-color: #007bff; color: white; }
        #registerFaceButton { background-color: #ffc107; color: #333; }
        #clientIdInput {
            border: 1px solid #ced4da;
            width: 150px;
            text-align: center;
        }
        #results {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            background-color: #e9ecef;
            color: #495057;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            width: 100%;
            max-width: 640px;
        }
    </style>
</head>
<body>
    <h1>Reconhecimento Facial</h1>

    <div class="container-face-recognition">
        <video id="videoElement" width="640" height="480" autoplay muted></video>
        <canvas id="overlayCanvas"></canvas>
    </div>

    <div class="controls">
        <button id="startCameraButton" disabled>Iniciar Câmera</button>
        <button id="authenticateButton" disabled>Autenticar Rosto</button>
        <input type="number" id="clientIdInput" placeholder="ID do Cliente" min="1">
        <button id="registerFaceButton" disabled>Cadastrar Rosto</button>
    </div>

    <div id="results">Aguardando...</div>

</body>
</html>
