import './bootstrap';
import * as faceapi from 'face-api.js';

const video = document.getElementById('videoElement');
const canvas = document.getElementById('overlayCanvas');
const resultsDiv = document.getElementById('results');
const startCameraButton = document.getElementById('startCameraButton');
const authenticateButton = document.getElementById('authenticateButton');
const registerFaceButton = document.getElementById('registerFaceButton');
const clientIdInput = document.getElementById('clientIdInput');

const context = canvas.getContext('2d');

let lastDetectedDescriptor = null;

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
console.log('CSRF Token obtido:', csrfToken);

const MODEL_URL = '/models';
let modelsLoaded = false;

Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
]).then(() => {
    console.log('Face-API models loaded successfully!');
    modelsLoaded = true;
    resultsDiv.innerText = 'Modelos carregados. Pronto para iniciar a câmera.';

    if (startCameraButton) startCameraButton.disabled = false;
}).catch(err => {
    console.error('Erro ao carregar modelos do Face-API:', err);
    resultsDiv.innerText = `Erro ao carregar modelos: ${err.message}`;
});

async function startVideo() {
    if (!modelsLoaded) {
        resultsDiv.innerText = 'Modelos do Face-API ainda não carregados. Por favor, aguarde.';
        return;
    }
    resultsDiv.innerText = 'Iniciando webcam...';
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        video.onloadedmetadata = () => {
            video.play();
            console.log('Webcam iniciada.');
            resultsDiv.innerText = 'Webcam iniciada. Detecção de rosto ativa.';
            if (authenticateButton) authenticateButton.disabled = false;
            if (registerFaceButton) registerFaceButton.disabled = false;
        };
    } catch (err) {
        console.error('Erro ao acessar a webcam:', err);
        resultsDiv.innerText = `Erro ao iniciar webcam: ${err.name} - ${err.message}. Verifique permissões e se a câmera não está em uso por outro aplicativo.`;
    }
}

video.addEventListener('play', () => {
    const displaySize = { width: video.width, height: video.height };
    faceapi.matchDimensions(canvas, displaySize);

    setInterval(async () => {
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
});

async function authenticateFace(descriptor) {
    if (!descriptor) {
        resultsDiv.innerText = 'Nenhum rosto detectado para autenticar.';
        return;
    }
    resultsDiv.innerText = 'Autenticando rosto...';
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
            resultsDiv.innerText = `Autenticado com sucesso como: ${data.user_name} (ID: ${data.client_id})`;
        } else {
            resultsDiv.innerText = 'Rosto não reconhecido ou não encontrado.';
        }
    } catch (error) {
        console.error('Erro durante a autenticação:', error);
        resultsDiv.innerText = `Erro na autenticação: ${error.message}`;
    }
}

async function registerFace(descriptor, clientId) {
    if (!descriptor) {
        resultsDiv.innerText = 'Nenhum rosto detectado para registrar.';
        return;
    }
    if (!clientId || clientId.trim() === '') {
        resultsDiv.innerText = 'Por favor, insira o ID do Cliente para registro.';
        clientIdInput.focus();
        return;
    }

    resultsDiv.innerText = `Registrando rosto para Cliente ID ${clientId}...`;

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
            resultsDiv.innerText = `Rosto registrado com sucesso para Cliente ID ${clientId}!`;
            clientIdInput.value = '';
        } else {
            resultsDiv.innerText = 'Falha no registro do rosto.';
        }
    } catch (error) {
        console.error('Erro durante o registro:', error);
        resultsDiv.innerText = `Erro no registro: ${error.message}`;
    }
}


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
        const clientId = clientIdInput ? clientIdInput.value : null;
        registerFace(lastDetectedDescriptor, clientId);
    });
}
