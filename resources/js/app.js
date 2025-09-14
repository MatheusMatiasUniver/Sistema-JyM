import './bootstrap';
import * as faceapi from 'face-api.js';

const video = document.getElementById('videoElement');
const canvas = document.getElementById('overlayCanvas');
const resultsDiv = document.getElementById('results');
const startCameraButton = document.getElementById('startCameraButton');
const authenticateButton = document.getElementById('authenticateButton');
const registerFaceButton = document.getElementById('registerFaceButton');
const clientIdInput = document.getElementById('clientIdInput');

const context = canvas ? canvas.getContext('2d') : null;

let lastDetectedDescriptor = null;
let intervalId = null;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (csrfToken) {
    console.log('CSRF Token obtido:', csrfToken);
} else {
    console.error('CSRF token not found. Please ensure it is present in a meta tag.');
}

const MODEL_URL = '/models';
let modelsLoaded = false;

async function loadModels() {
    if (modelsLoaded) return;
    if (resultsDiv) resultsDiv.innerText = 'Carregando modelos de IA...';
    try {
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
        ]);
        console.log('Face-API models loaded successfully!');
        modelsLoaded = true;
        if (resultsDiv) resultsDiv.innerText = 'Modelos carregados. Pronto para iniciar a câmera.';
        if (startCameraButton) startCameraButton.disabled = false;
    } catch (err) {
        console.error('Erro ao carregar modelos do Face-API:', err);
        if (resultsDiv) resultsDiv.innerText = `Erro ao carregar modelos: ${err.message}`;
    }
}

async function startVideo() {
    if (!video) {
        console.error('Elemento de vídeo não encontrado na página.');
        if (resultsDiv) resultsDiv.innerText = 'Erro: Elemento de vídeo não encontrado.';
        return;
    }
    if (!modelsLoaded) {
        if (resultsDiv) resultsDiv.innerText = 'Modelos do Face-API ainda não carregados. Por favor, aguarde.';
        return;
    }

    if (resultsDiv) resultsDiv.innerText = 'Iniciando webcam...';
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        video.onloadedmetadata = () => {
            video.play();
            console.log('Webcam iniciada.');
            if (resultsDiv) resultsDiv.innerText = 'Webcam iniciada. Detecção de rosto ativa.';
            if (authenticateButton) authenticateButton.disabled = false;
            if (registerFaceButton) registerFaceButton.disabled = false;
            startDetectionLoop();
        };
    } catch (err) {
        console.error('Erro ao acessar a webcam:', err);
        if (resultsDiv) resultsDiv.innerText = `Erro ao iniciar webcam: ${err.name} - ${err.message}. Verifique permissões e se a câmera não está em uso por outro aplicativo.`;
    }
}

function startDetectionLoop() {
    if (intervalId) clearInterval(intervalId);

    if (!video || !canvas || !context) {
        console.warn('Elementos de vídeo/canvas não encontrados. Detecção não pode ser iniciada.');
        return;
    }

    const displaySize = { width: video.width, height: video.height };
    faceapi.matchDimensions(canvas, displaySize);

    intervalId = setInterval(async () => {
        if (modelsLoaded && !video.paused && !video.ended) {
            const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptor();

            context.clearRect(0, 0, canvas.width, canvas.height);

            if (detections) {
                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                faceapi.draw.drawDetections(canvas, resizedDetections);
                faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);

                lastDetectedDescriptor = detections.descriptor;
            } else {
                lastDetectedDescriptor = null;
            }
        }
    }, 100);
}

async function authenticateFace(descriptor) {
    if (!descriptor) {
        if (resultsDiv) resultsDiv.innerHTML = '<span style="color: #dc3545;">Nenhum rosto detectado para autenticar.</span>';
        return;
    }
    if (resultsDiv) resultsDiv.innerText = 'Autenticando rosto...';
    try {
        const response = await fetch('/face/authenticate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                descriptor: Array.from(descriptor)
            })
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `Erro do servidor: ${response.status} ${response.statusText}`);
        }

        const data = await response.json();
        console.log('Resposta da autenticação:', data);

        if (data.authenticated) {
            if (resultsDiv) {
                resultsDiv.innerHTML = `<span style="color:#28a745;">&#10003; Autenticado com sucesso!</span><br>
                                       Cliente: ${data.user_name} (ID: ${data.client_id})<br>
                                       Status: ${data.status}`;
                if (data.status === 'Ativo') {
                    resultsDiv.innerHTML += '<br><span style="color:#28a745; font-size: 1.2em;">ACESSO LIBERADO!</span>';
                } else {
                    resultsDiv.innerHTML += '<br><span style="color:#dc3545; font-size: 1.2em;">ACESSO NEGADO! Status: ' + data.status + '</span>';
                }
            }
        } else {
            if (resultsDiv) resultsDiv.innerHTML = '<span style="color: #dc3545;">Rosto não reconhecido ou não encontrado.</span>';
        }
    } catch (error) {
        console.error('Erro durante a autenticação:', error);
        if (resultsDiv) resultsDiv.innerHTML = `<span style="color: #dc3545;">Erro na autenticação: ${error.message}</span>`;
    }
}

async function registerFace(descriptor, clientId) {
    if (!descriptor) {
        if (resultsDiv) resultsDiv.innerHTML = '<span style="color: #dc3545;">Nenhum rosto detectado para registrar.</span>';
        return;
    }
    if (!clientId || String(clientId).trim() === '') {
        if (resultsDiv) resultsDiv.innerHTML = '<span style="color: #dc3545;">Por favor, insira o ID do Cliente para registro.</span>';
        if (clientIdInput && clientIdInput.type === 'number') clientIdInput.focus();
        return;
    }

    if (resultsDiv) resultsDiv.innerText = `Registrando rosto para Cliente ID ${clientId}...`;

    try {
        const response = await fetch('/face/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                cliente_id: clientId,
                descriptor: Array.from(descriptor)
            })
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `Erro do servidor: ${response.status} ${response.statusText}`);
        }

        const data = await response.json();
        console.log('Resposta do registro:', data);

        if (data.success) {
            if (resultsDiv) resultsDiv.innerHTML = `<span style="color:#28a745;">&#10003; ${data.message}</span>`;
            if (clientIdInput && clientIdInput.type === 'number') clientIdInput.value = '';
            if (document.querySelector('input[type="hidden"][id="clientIdInput"]')) {
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 3000);
            }
        } else {
            if (resultsDiv) resultsDiv.innerHTML = `<span style="color: #dc3545;">${data.message || 'Falha no registro do rosto.'}</span>`;
        }
    } catch (error) {
        console.error('Erro durante o registro:', error);
        if (resultsDiv) resultsDiv.innerHTML = `<span style="color: #dc3545;">Erro no registro: ${error.message}</span>`;
    }
}

document.addEventListener('DOMContentLoaded', loadModels);

if (startCameraButton) {
    startCameraButton.addEventListener('click', startVideo);
}

if (authenticateButton) {
    authenticateButton.addEventListener('click', () => {
        authenticateFace(lastDetectedDescriptor);
    });
}

if (registerFaceButton) {
    registerFaceButton.addEventListener('click', () => {
        const clientIdToRegister = clientIdInput ? clientIdInput.value : null;
        registerFace(lastDetectedDescriptor, clientIdToRegister);
    });
}