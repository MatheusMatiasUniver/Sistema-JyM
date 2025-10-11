import './bootstrap';
import * as faceapi from 'face-api.js';
import { initVendasCreate } from './vendas-create';

const video = document.getElementById('videoElement');
const canvas = document.getElementById('overlayCanvas');
const resultsDiv = document.getElementById('results');
const startCameraButton = document.getElementById('startCameraButton');
const registerFaceButton = document.getElementById('registerFaceButton'); 
const clientIdInput = document.getElementById('clientIdInput');
const context = canvas ? canvas.getContext('2d') : null;

let lastDetectedDescriptor = null;
let detectionIntervalId = null;
let kioskStatusPollingId = null;
let displaySize = { width: 0, height: 0 }; 
let authenticationCooldown = false; 
let lastAuthenticatedClientId = null; 
let lastAuthTime = 0;
let detectionStarted = false; 
let isKioskRegistering = false;
let kioskRegistrationMessage = '';

const COOLDOWN_DURATION = 5000;
const MESSAGE_DURATION = 3000;
const REGISTER_COOLDOWN_DURATION = 10000;
const KIOSK_POLLING_INTERVAL = 3000;

const isKioskPage = window.location.pathname.includes('/reconhecimento');
const isCaptureFacePage = window.location.pathname.includes('/clientes/') && window.location.pathname.includes('/capturar-rosto');

if (document.querySelector('#produtos-data')) {
    initVendasCreate();
}

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (csrfToken) {
    console.log('CSRF Token obtido:', csrfToken);
} else {
    console.error('CSRF token not found. Please ensure it is present in a meta tag.');
}

const MODEL_URL = '/models';
let modelsLoaded = false;

function showMessage(type, mainMessage, subMessage = '') {
    if (resultsDiv) {
        resultsDiv.classList.remove('alert-info', 'alert-success', 'alert-error');
        resultsDiv.classList.add(`alert-${type}`);
        resultsDiv.innerHTML = `<p class="main-message">${mainMessage}</p>`;
        if (subMessage) {
            resultsDiv.innerHTML += `<p class="sub-message">${subMessage}</p>`;
        }
    }
}

async function pollKioskStatus() {
    try {
        const response = await fetch('/face/kiosk-status', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
        });
        const data = await response.json();
        if (data.is_registering !== isKioskRegistering || data.message !== kioskRegistrationMessage) {
            isKioskRegistering = data.is_registering;
            kioskRegistrationMessage = data.message;

            if (isKioskRegistering) {
                if (detectionIntervalId) {
                    clearInterval(detectionIntervalId);
                    detectionIntervalId = null;
                    detectionStarted = false;
                }
                showMessage('info', kioskRegistrationMessage || 'Registro de rosto em andamento...');
                console.log('Kiosk: Registro em andamento detectado, pausando autenticação.');
            } else if (!detectionStarted && modelsLoaded && video && video.srcObject) {
                console.log('Kiosk: Registro finalizado, reiniciando autenticação.');
                showMessage('info', 'Aguardando detecção de rosto.');
                setupVideoAndDetection();
            }
        }
    } catch (error) {
        console.error('Erro ao verificar status do quiosque:', error);
    }
}

async function loadModels() {
    if (modelsLoaded) return;

    displaySize = { width: 0, height: 0 }; 

    showMessage('info', 'Carregando modelos de IA...');

    try {
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
        ]);
        console.log('Face-API models loaded successfully!');
        modelsLoaded = true;
        
        showMessage('info', 'Modelos carregados. Pronto para iniciar a câmera.');
        
        if (isKioskPage) {
            if (startCameraButton) startCameraButton.style.display = 'none'; 
            startVideo(); 
            kioskStatusPollingId = setInterval(pollKioskStatus, KIOSK_POLLING_INTERVAL);
        } else if (isCaptureFacePage) {
            if (startCameraButton) startCameraButton.disabled = false;
            if (registerFaceButton) registerFaceButton.disabled = false;
        }

    } catch (err) {
        console.error('Erro ao carregar modelos do Face-API:', err);
        showMessage('error', 'Erro!', `Falha ao carregar modelos: ${err.message}`);
    }
}

async function startVideo() {
    if (video && video.srcObject && !video.paused) {
        console.log('Webcam já está rodando.');
        return;
    }

    if (!video) {
        console.error('Elemento de vídeo não encontrado na página.');
        showMessage('error', 'Erro!', 'Elemento de vídeo não encontrado.');
        return;
    }
    if (!modelsLoaded) {
        showMessage('error', 'Erro!', 'Modelos do Face-API ainda não carregados. Por favor, aguarde.');
        return;
    }

    showMessage('info', 'Iniciando webcam...');

    try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: { ideal: 1280 }, 
                height: { ideal: 720 }  
            } 
        });
        video.srcObject = stream;
        
        video.removeEventListener('play', setupVideoAndDetection); 
        video.addEventListener('play', setupVideoAndDetection, { once: true });
        
        video.play(); 

    } catch (err) {
        console.error('Erro ao acessar a webcam:', err);

        showMessage('error', 'Erro!', `Ao iniciar webcam: ${err.name} - ${err.message}. Verifique permissões e se a câmera não está em uso por outro aplicativo.`);

        if (startCameraButton) startCameraButton.disabled = true;
    }
}

function setupVideoAndDetection() {
    if (detectionStarted) {
        console.log('Detecção já iniciada, ignorando setup.');
        return;
    }

    setTimeout(() => {
        displaySize = { 
            width: video.videoWidth || video.offsetWidth, 
            height: video.videoHeight || video.offsetHeight 
        };
        
        if (displaySize.width === 0 || displaySize.height === 0) {
            displaySize = { width: video.offsetWidth || 1280, height: video.offsetHeight || 720 }; 
            console.warn('Dimensões do vídeo ainda são 0 após metadados/play, usando dimensões do elemento ou fallback.');
        }

        if (canvas) {
            faceapi.matchDimensions(canvas, displaySize);
            canvas.width = displaySize.width;
            canvas.height = displaySize.height;
        }
        
        if (isKioskPage && isKioskRegistering) {
            showMessage('info', kioskRegistrationMessage || 'Registro de rosto em andamento...');
        } else {
        showMessage('info', 'Webcam iniciada. Aguardando detecção de rosto.');
        }
        
        if (isCaptureFacePage) {
            if (registerFaceButton) registerFaceButton.disabled = false;
            if (startCameraButton) startCameraButton.style.display = 'none';
        } else if (isKioskPage) {
            if (startCameraButton) startCameraButton.style.display = 'none'; 
        }

        startDetectionLoop(); 
        detectionStarted = true; 
    }, 500); 
}

function startDetectionLoop() {
    if (detectionIntervalId) clearInterval(detectionIntervalId); 

    if (!video || !canvas || !context || displaySize.width === 0 || displaySize.height === 0) {
        console.warn('Elementos de vídeo/canvas não encontrados ou dimensões inválidas. Detecção não pode ser iniciada.');

        showMessage('error', 'Erro!', 'Problema na inicialização do vídeo/canvas. Recarregue a página.');
        
        detectionStarted = false; 
        if (startCameraButton) startCameraButton.disabled = false;
        return;
    }

    if (isKioskPage && isKioskRegistering) {
        console.log('Kiosk: Detecção suspensa devido a registro em andamento.');
        return; 
    }

    detectionIntervalId = setInterval(async () => {
        if (modelsLoaded && !video.paused && !video.ended && displaySize.width > 0 && displaySize.height > 0) {
            if (isKioskPage && isKioskRegistering) {
                console.log('Kiosk: Detecção suspensa (loop inter.).');
                clearInterval(detectionIntervalId);
                detectionIntervalId = null;
                detectionStarted = false;
                showMessage('info', kioskRegistrationMessage || 'Registro de rosto em andamento...');
                return;
            }

            const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptor();

            context.clearRect(0, 0, canvas.width, canvas.height);

            if (detections) {
                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                faceapi.draw.drawDetections(canvas, resizedDetections);
                faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);

                lastDetectedDescriptor = detections.descriptor;

                if (isKioskPage) {
                    const currentTime = Date.now();
                    if (!authenticationCooldown && (currentTime - lastAuthTime > COOLDOWN_DURATION)) {
                        authenticationCooldown = true;
                        lastAuthTime = currentTime; 
                        authenticateFace(detections.descriptor);
                        
                        setTimeout(() => {
                            authenticationCooldown = false;
                            if (!lastDetectedDescriptor || video.paused || video.ended) {
                                if (!isKioskRegistering) {
                                showMessage('info', 'Aguardando detecção de rosto.');
                                lastAuthenticatedClientId = null;
                                } else {
                                    showMessage('info', kioskRegistrationMessage || 'Registro de rosto em andamento...');
                                }
                            }
                        }, COOLDOWN_DURATION); 
                    }
                } 
                else if (isCaptureFacePage) {
                    showMessage('info', 'Rosto detectado!', 'Pronto para cadastrar.');
                }

            } else {
                lastDetectedDescriptor = null;
                context.clearRect(0, 0, canvas.width, canvas.height);

                if (!authenticationCooldown && !isKioskRegistering) { 
                    showMessage('info', 'Aguardando detecção de rosto.');
                    lastAuthenticatedClientId = null;
                } else if (isKioskRegistering) {
                    showMessage('info', kioskRegistrationMessage || 'Registro de rosto em andamento...');
                }
            }
        } else if (video.paused || video.ended) {
            console.log('Vídeo pausado ou finalizado, parando loop de detecção.');
            clearInterval(detectionIntervalId);
            detectionIntervalId = null;
            detectionStarted = false; 
            if (startCameraButton) startCameraButton.disabled = false;
            showMessage('info', 'Webcam parada.');
        }
    }, 200); 
}

async function authenticateFace(descriptor) {
    if (!descriptor) {
        showMessage('error', 'Erro!', 'Nenhum rosto detectado para autenticar.');
        return;
    }

    if (lastAuthenticatedClientId && (Date.now() - lastAuthTime < COOLDOWN_DURATION) ) {
        console.log(`Cliente ${lastAuthenticatedClientId} já autenticado recentemente. Ignorando.`);
        return;
    }

    showMessage('info', 'Autenticando rosto...');
    
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
            lastAuthenticatedClientId = data.client_id;
            if (data.status === 'Ativo') {
                showMessage('success', 'ACESSO LIBERADO!', `Bom treino, ${data.user_name}!`);
            } else {
                showMessage('error', 'ACESSO NEGADO!', `Status: ${data.status} para ${data.user_name}.`);
            }
        } else {
            lastAuthenticatedClientId = null; 
            showMessage('error', 'ROSTO NÃO RECONHECIDO!', 'Por favor, tente novamente.');
        }
    } catch (error) {
        console.error('Erro durante a autenticação:', error);
        lastAuthenticatedClientId = null; 
        showMessage('error', 'Erro!', `Falha na autenticação: ${error.message}`);
    }
}

async function registerFace(descriptor) {
    showMessage('info', 'Processando registro...');

    if (!descriptor) {
        showMessage('error', 'Erro!', 'Nenhum rosto detectado para registrar.');
        return;
    }

    const clientId = clientIdInput ? clientIdInput.value : null;

    if (!clientId || String(clientId).trim() === '') {
        showMessage('error', 'Erro!', 'ID do Cliente não fornecido para registro.');
        return;
    }

    showMessage('info', `Registrando rosto para Cliente ID ${clientId}...`);

    try {
        await fetch('/face/set-kiosk-registering', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                is_registering: true,
                message: `Registro de rosto para cliente ${clientId} em andamento...`,
                duration_seconds: Math.ceil(REGISTER_COOLDOWN_DURATION / 1000) + 10
            })
        });

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
            showMessage('success', 'REGISTRO CONCLUÍDO!', `Rosto de ${data.user_name || 'o cliente'} foi registrado.`);
            
            await fetch('/face/set-kiosk-registering', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    is_registering: true,
                    message: `REGISTRO CONCLUÍDO! Rosto de ${data.user_name || 'o cliente'} foi registrado.`,
                    duration_seconds: Math.ceil(REGISTER_COOLDOWN_DURATION / 1000)
                })
            });

            setTimeout(() => {
                fetch('/face/set-kiosk-registering', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ is_registering: false })
                }).then(res => res.json()).then(res => console.log('Kiosk status cleared:', res));

                if (isCaptureFacePage) { 
                    window.location.href = '/clientes'; 
                } else if (isKioskPage) { 
                    showMessage('info', 'Aguardando detecção de rosto.');
                }
            }, REGISTER_COOLDOWN_DURATION);
        } else {
            showMessage('error', 'Erro no Registro!', `${data.message || 'Falha ao registrar o rosto.'}`);
            fetch('/face/set-kiosk-registering', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ is_registering: false })
            }).then(res => res.json()).then(res => console.log('Kiosk status cleared (error):', res));

            setTimeout(() => {
                showMessage('info', 'Aguardando detecção de rosto.');
            }, MESSAGE_DURATION);
        }
    } catch (error) {
        console.error('Erro durante o registro:', error);
        showMessage('error', 'Erro!', `Falha no registro: ${error.message}`);
        fetch('/face/set-kiosk-registering', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ is_registering: false })
        }).then(res => res.json()).then(res => console.log('Kiosk status cleared (exception):', res));

        setTimeout(() => {
            showMessage('info', 'Aguardando detecção de rosto.');
        }, MESSAGE_DURATION);
    }
}

document.addEventListener('DOMContentLoaded', loadModels);

if (startCameraButton && !isKioskPage) { 
    startCameraButton.addEventListener('click', startVideo);
}

if (registerFaceButton && isCaptureFacePage) {
    registerFaceButton.addEventListener('click', () => {
        registerFace(lastDetectedDescriptor); 
    });
}

window.addEventListener('beforeunload', () => {
    if (detectionIntervalId) clearInterval(detectionIntervalId);
    if (kioskStatusPollingId) clearInterval(kioskStatusPollingId);    
    if (isCaptureFacePage && detectionStarted) { 
         fetch('/face/set-kiosk-registering', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ is_registering: false })
        }).then(res => res.json()).then(res => console.log('Kiosk status cleared on unload:', res));
    }
});