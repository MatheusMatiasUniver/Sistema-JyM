import './bootstrap';
import * as faceapi from 'face-api.js';
import { initVendasCreate } from './vendas-create';
import * as formatters from './formatters';
import './cnpj-mask';
import webSocketManager from './websocket-manager';

window.applyFormatting = formatters.applyFormatting;
window.webSocketManager = webSocketManager;

const video = document.getElementById('videoElement');
const canvas = document.getElementById('overlayCanvas');
const resultsDiv = document.getElementById('results');
const startCameraButton = document.getElementById('startCameraButton');
const registerFaceButton = document.getElementById('registerFaceButton');

const context = canvas ? canvas.getContext('2d') : null;

const codeInputArea = document.getElementById('code-input-area');
const accessCodeInput = document.getElementById('accessCodeInput');
const submitCodeBtn = document.getElementById('submitCodeBtn');
const cancelCodeBtn = document.getElementById('cancelCodeBtn');


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
let failedFaceAttempts = 0;
let isDetectionPaused = false;

const COOLDOWN_DURATION = 5000;
const MESSAGE_DURATION = 3000;
const REGISTER_COOLDOWN_DURATION = 10000;
const KIOSK_POLLING_INTERVAL = 3000;
const MAX_FAILED_FACE_ATTEMPTS = 3;
const CODIGO_ACESSO_LENGTH = 6;

const isKioskPage = window.location.pathname.includes('/reconhecimento');
const isCaptureFacePage = window.location.pathname.includes('/clientes/') && window.location.pathname.includes('/capturar-rosto');



if (document.body.classList.contains('capture-page')) {
    document.body.classList.add('capture-funcionario');
}

if (document.querySelector('#produtos-data')) {
    initVendasCreate();
}

function getCsrfToken() {
    const tokenElement = document.querySelector('meta[name="csrf-token"]');
    if (tokenElement) {
        return tokenElement.getAttribute('content');
    }
    return null;
}

const MODEL_URL = '/models';
let modelsLoaded = false;

function showMessage(type, mainMessage, subMessage = '') {
    if (resultsDiv) {
        resultsDiv.classList.remove('alert-info', 'alert-success', 'alert-error', 'hidden');
        resultsDiv.style.display = 'block';
        resultsDiv.classList.add(`alert-${type}`);
        
        if (isCaptureFacePage) {
            resultsDiv.classList.add('face-capture-alert');
        }
        
        resultsDiv.innerHTML = `<p class="main-message">${mainMessage}</p>`;
        if (subMessage) {
            resultsDiv.innerHTML += `<p class="sub-message">${subMessage}</p>`;
        }
    }
}

function handleKioskStatusChange(data) {
    const newIsRegistering = (typeof data.isRegistering !== 'undefined' ? data.isRegistering : data.is_registering) || false;
    const newMessage = data.message || '';

    if (newIsRegistering !== isKioskRegistering || newMessage !== kioskRegistrationMessage) {
        isKioskRegistering = newIsRegistering;
        kioskRegistrationMessage = newMessage;

        if (isKioskRegistering) {
            if (detectionIntervalId) {
                clearInterval(detectionIntervalId);
                detectionIntervalId = null;
                detectionStarted = false;
            }

            if (codeInputArea) codeInputArea.style.display = 'none';
            showMessage('info', kioskRegistrationMessage || 'Registro de rosto em andamento...');
        } else if (!detectionStarted && modelsLoaded && video && video.srcObject) {
            showMessage('info', 'Aguardando detecção de rosto.');
            hideCodeInput();
        }
    }
}

function handleClientRegistrationStarted(data) {
    console.log('Registro de cliente iniciado:', data);
    if (data.message) {
        showMessage('info', data.message);
    }
}

function handleClientRegistrationCompleted(data) {
    console.log('Registro de cliente concluído:', data);
    if (data.success) {
        showMessage('success', data.message || 'Cliente registrado com sucesso!');
    } else {
        showMessage('error', data.message || 'Erro ao registrar cliente.');
    }
}

function initializeWebSocket() {
    if (!isKioskPage) return;

    webSocketManager.onKioskStatusChanged(handleKioskStatusChange);
    webSocketManager.onClientRegistrationStarted(handleClientRegistrationStarted);
    webSocketManager.onClientRegistrationCompleted(handleClientRegistrationCompleted);
    
    webSocketManager.onConnectionStateChange((isConnected) => {
        if (isConnected) {
            console.log('WebSocket conectado - polling desabilitado');
            if (kioskStatusPollingId) {
                clearInterval(kioskStatusPollingId);
                kioskStatusPollingId = null;
            }
        } else {
            console.log('WebSocket desconectado - tentando reconectar...');
            setTimeout(() => {
                if (!webSocketManager.isWebSocketConnected() && !kioskStatusPollingId) {
                    console.log('Fallback: iniciando polling HTTP');
                    fallbackToPolling();
                }
            }, 5000);
        }
    });

    webSocketManager.init();
}

function fallbackToPolling() {
    if (kioskStatusPollingId) {
        clearInterval(kioskStatusPollingId);
    }
    
    if (webSocketManager.isWebSocketConnected()) {
        return;
    }

    kioskStatusPollingId = setInterval(async () => {
        try {
            const csrfToken = getCsrfToken();
            if (!csrfToken) return;

            const response = await fetch('/face/kiosk-status', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
            });
            const data = await response.json();
            handleKioskStatusChange({
                isRegistering: data.is_registering,
                message: data.message
            });
        } catch (error) {
            console.error('Erro no polling de fallback:', error);
        }
    }, KIOSK_POLLING_INTERVAL);
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
        modelsLoaded = true;

        showMessage('info', 'Modelos carregados. Pronto para iniciar a câmera.');

        if (isKioskPage) {
            if (startCameraButton) startCameraButton.style.display = 'none';
            startVideo();
            initializeWebSocket();
        } else if (isCaptureFacePage) {
            showMessage('info', 'Iniciando câmera automaticamente...');
            if (startCameraButton) startCameraButton.style.display = 'none';
            if (registerFaceButton) registerFaceButton.disabled = false;
            startVideo();
        }

        if (window.location.pathname === '/captura') {
            startCamera();
        }

    } catch (err) {
        showMessage('error', 'Erro!', `Falha ao carregar modelos: ${err.message}`);
    }
}

async function startVideo() {
    if (video && video.srcObject && !video.paused) {
        return;
    }

    if (!video) {
        showMessage('error', 'Erro!', 'Elemento de vídeo não encontrado.');
        return;
    }
    if (!modelsLoaded) {
        showMessage('error', 'Erro!', 'Modelos do Face-API ainda não carregados. Por favor, aguarde.');
        return;
    }

    showMessage('info', 'Iniciando webcam...');

    try {
        if (isCaptureFacePage) {
            const currentCsrfToken = getCsrfToken();
            if (currentCsrfToken) {
                await fetch('/face/set-kiosk-registering', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': currentCsrfToken
                    },
                    body: JSON.stringify({
                        is_registering: true,
                        message: 'Câmera sendo preparada para cadastro de rosto...',
                        duration_seconds: 300
                    })
                });
            }
        }

        if (window.location.pathname === '/captura') {
            websocketManager.sendMessage('kiosk-status', {
                status: 'registering',
                message: 'Iniciando registro de funcionário'
            });
        }

        const stream = await ensureCameraStream();
        video.srcObject = stream;

        video.removeEventListener('play', setupVideoAndDetection);
        video.addEventListener('play', setupVideoAndDetection, { once: true });

        video.play();

    } catch (err) {
        const friendly = formatCameraError(err);
        showMessage('error', 'Erro!', friendly);

        if (startCameraButton) startCameraButton.disabled = true;
        
        if (isCaptureFacePage) {
            const currentCsrfToken = getCsrfToken();
            if (currentCsrfToken) {
                fetch('/face/set-kiosk-registering', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': currentCsrfToken
                    },
                    body: JSON.stringify({
                        is_registering: false
                    })
                });
            }
        }
    }
}

async function ensureCameraStream() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        throw new Error('Navegador sem suporte a câmera ou em contexto não seguro. Use HTTPS ou localhost.');
    }

    const devices = await navigator.mediaDevices.enumerateDevices();
    const cameras = devices.filter(d => d.kind === 'videoinput');
    if (cameras.length === 0) {
        throw new Error('Nenhuma câmera detectada. Verifique conexões e permissões do sistema.');
    }

    const preferred = cameras[0];
    try {
        return await navigator.mediaDevices.getUserMedia({
            video: {
                deviceId: { ideal: preferred.deviceId },
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        });
    } catch (e) {
        if (e && (e.name === 'OverconstrainedError' || e.name === 'NotFoundError')) {
            return await navigator.mediaDevices.getUserMedia({ video: true });
        }
        throw e;
    }
}

function formatCameraError(err) {
    if (!err || !err.name) return 'Falha ao acessar a câmera. Verifique permissões e feche outros aplicativos que possam estar usando a câmera.';
    if (err.name === 'NotFoundError') return 'Nenhum dispositivo de câmera foi encontrado. Verifique se uma câmera está conectada e reconhecida pelo sistema.';
    if (err.name === 'NotAllowedError') return 'Permissão de câmera negada. Autorize o uso da câmera no navegador.';
    if (err.name === 'OverconstrainedError') return 'As configurações de vídeo solicitadas não são suportadas pela câmera. Ajuste as configurações e tente novamente.';
    if (err.name === 'SecurityError') return 'Acesso à câmera bloqueado por políticas de segurança. Use HTTPS ou localhost.';
    return 'Erro ao iniciar câmera. Verifique permissões e se a câmera não está em uso por outro aplicativo.';
}

function setupVideoAndDetection() {
    if (detectionStarted) {
        return;
    }

    setTimeout(() => {
        displaySize = {
            width: video.videoWidth || video.offsetWidth,
            height: video.videoHeight || video.offsetHeight
        };

        if (displaySize.width === 0 || displaySize.height === 0) {
            displaySize = { width: video.offsetWidth || 1280, height: video.offsetHeight || 720 };
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
        showMessage('error', 'Erro!', 'Problema na inicialização do vídeo/canvas. Recarregue a página.');

        detectionStarted = false;
        if (startCameraButton) startCameraButton.disabled = false;
        return;
    }

    if (isKioskPage && isKioskRegistering) {
        return;
    }

    if (codeInputArea) codeInputArea.style.display = 'none';
    if (video) video.style.display = 'block';
    if (canvas) canvas.style.display = 'block';

    detectionIntervalId = setInterval(async () => {
        if (modelsLoaded && !video.paused && !video.ended && displaySize.width > 0 && displaySize.height > 0) {
            if (isKioskPage && isKioskRegistering) {
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

                if (isKioskPage && !isKioskRegistering) {
                    const currentTime = Date.now();
                    if (!authenticationCooldown && (currentTime - lastAuthTime > COOLDOWN_DURATION)) {
                        authenticationCooldown = true;
                        lastAuthTime = currentTime;
                        authenticateFace(detections.descriptor);

                        setTimeout(() => {
                            authenticationCooldown = false;
                            isDetectionPaused = false;
                            if (!lastDetectedDescriptor || video.paused || video.ended) {
                                if (!isKioskRegistering) {
                                    showMessage('info', 'Aguardando detecção de rosto.');
                                    lastAuthenticatedClientId = null;
                                }
                            }
                        }, COOLDOWN_DURATION);
                    }
                } else if (isKioskPage && isKioskRegistering) {
                    showMessage('info', kioskRegistrationMessage || 'Registro de rosto em andamento...');
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

    if (isKioskRegistering) {
        showMessage('info', kioskRegistrationMessage || 'Registro de rosto em andamento...');
        return;
    }

    showMessage('info', 'Autenticando rosto...');

    try {
        const currentCsrfToken = getCsrfToken();
        if (!currentCsrfToken) {
            showMessage('error', 'Erro de segurança!', 'Token CSRF ausente. Recarregue a página.');

            failedFaceAttempts++;
            if (failedFaceAttempts >= MAX_FAILED_FACE_ATTEMPTS) {
                showCodeInput();
                failedFaceAttempts = 0;
            }
            return;
        }

        const response = await fetch('/face/authenticate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': currentCsrfToken
            },
            body: JSON.stringify({
                descriptor: Array.from(descriptor)
            })
        });

        const data = await response.json();

        if (response.ok && data.authenticated) {
            lastAuthenticatedClientId = data.client_id;

            failedFaceAttempts = 0;
            if (data.status === 'Ativo') {
                showMessage('success', 'ACESSO LIBERADO!', `Bom treino, ${data.user_name}!`);
                isDetectionPaused = false;
            } else {
                showMessage('error', 'ACESSO NEGADO!', `Status: ${data.status} para ${data.user_name}.`);

                failedFaceAttempts++;
                if (failedFaceAttempts >= MAX_FAILED_FACE_ATTEMPTS) {
                    showCodeInput();
                    failedFaceAttempts = 0;
                } else {
                    showMessage('error', 'ROSTO NÃO AUTORIZADO!', `Tentativas: ${failedFaceAttempts}/${MAX_FAILED_FACE_ATTEMPTS}. Por favor, tente novamente.`);
                }
                isDetectionPaused = false;
            }
        } else {
            failedFaceAttempts++;
            if (failedFaceAttempts >= MAX_FAILED_FACE_ATTEMPTS) {
                showCodeInput();
                failedFaceAttempts = 0;
            } else {
                let errorMessage = (data && data.message) ? data.message : 'Falha na autenticação facial.';
                showMessage('error', 'ROSTO NÃO RECONHECIDO!', `${errorMessage} Tentativas: ${failedFaceAttempts}/${MAX_FAILED_FACE_ATTEMPTS}.`);
            }
            isDetectionPaused = false;
        }
    } catch (error) {
        lastAuthenticatedClientId = null;
        showMessage('error', 'Erro!', `Falha na autenticação: ${error.message}`);

        failedFaceAttempts++;
        if (failedFaceAttempts >= MAX_FAILED_FACE_ATTEMPTS) {
            showCodeInput();
            failedFaceAttempts = 0;
        } else {
            showMessage('error', 'ERRO NA COMUNICAÇÃO!', `Tente novamente. Tentativas: ${failedFaceAttempts}/${MAX_FAILED_FACE_ATTEMPTS}.`);
        }
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

    if (detectionIntervalId) {
        clearInterval(detectionIntervalId);
        detectionIntervalId = null;
    }

    showMessage('info', `Registrando rosto para Cliente ID ${clientId}...`);

    try {
        const currentCsrfToken = getCsrfToken();
        if (!currentCsrfToken) {
            showMessage('error', 'Erro de segurança!', 'Token CSRF ausente. Recarregue a página.');
            return;
        }

        await fetch('/face/set-kiosk-registering', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': currentCsrfToken
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
                'X-CSRF-TOKEN': currentCsrfToken
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

        if (data.success) {
            showMessage('success', 'REGISTRO CONCLUÍDO!', `Rosto de ${data.user_name || 'o cliente'} foi registrado.`);

            await fetch('/face/set-kiosk-registering', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': currentCsrfToken
                },
                body: JSON.stringify({
                    is_registering: true,
                    message: 'Cadastro concluído. Reiniciando kiosk em 10s...',
                    duration_seconds: Math.ceil(REGISTER_COOLDOWN_DURATION / 1000)
                })
            });

            setTimeout(async () => {
                try {
                    await fetch('/face/set-kiosk-registering', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': currentCsrfToken
                        },
                        body: JSON.stringify({ is_registering: false })
                    });
                } catch (e) {}
            }, REGISTER_COOLDOWN_DURATION);

            setTimeout(() => {
                if (isCaptureFacePage) {
                    window.location.href = '/clientes';
                } else if (isKioskPage) {
                    showMessage('info', 'Aguardando detecção de rosto.');
                    if (!detectionIntervalId && modelsLoaded && video && !video.paused && !video.ended) {
                        startDetectionLoop();
                    }
                }
            }, 2500);
        } else {
            showMessage('error', 'Erro no Registro!', `${data.message || 'Falha ao registrar o rosto.'}`);
            fetch('/face/set-kiosk-registering', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': currentCsrfToken },
                body: JSON.stringify({ is_registering: false })
            });

            setTimeout(() => {
                showMessage('info', 'Aguardando detecção de rosto.');
                if (isCaptureFacePage && !detectionIntervalId && modelsLoaded && video && !video.paused && !video.ended) {
                    startDetectionLoop();
                }
            }, MESSAGE_DURATION);
        }
    } catch (error) {
        showMessage('error', 'Erro!', `Falha no registro: ${error.message}`);
        const csrfToken = getCsrfToken();
        if (csrfToken) {
            fetch('/face/set-kiosk-registering', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ is_registering: false })
            });
        }
        setTimeout(() => {
            showMessage('info', 'Aguardando detecção de rosto.');
            if (isCaptureFacePage && !detectionIntervalId && modelsLoaded && video && !video.paused && !video.ended) {
                startDetectionLoop();
            }
        }, MESSAGE_DURATION);
    }
}

function showCodeInput() {
    if (video) video.style.display = 'none';
    if (canvas) canvas.style.display = 'none';
    if (codeInputArea) codeInputArea.style.display = 'block';
    if (accessCodeInput) {
        accessCodeInput.value = '';
        accessCodeInput.focus();
    }
    showMessage('info', 'Rosto não reconhecido!', 'Por favor, digite seu código de acesso.');
    if (detectionIntervalId) {
        clearInterval(detectionIntervalId);
        detectionIntervalId = null;
        detectionStarted = false;
    }
}

function hideCodeInput() {
    if (video) video.style.display = 'block';
    if (canvas) canvas.style.display = 'block';
    if (codeInputArea) codeInputArea.style.display = 'none';
    failedFaceAttempts = 0;

    if (!detectionStarted && modelsLoaded && video && video.srcObject && !isKioskRegistering) {
        setupVideoAndDetection();
    }
    showMessage('info', 'Aguardando detecção de rosto.');
}

async function submitAccessCode() {
    const code = accessCodeInput.value;

    if (!code || code.length !== CODIGO_ACESSO_LENGTH || isNaN(code)) {
        showMessage('error', 'Código inválido!', `Por favor, digite um código de ${CODIGO_ACESSO_LENGTH} dígitos numéricos.`);
        accessCodeInput.focus();
        return;
    }

    showMessage('info', 'Verificando código...');

    try {
        const currentCsrfToken = getCsrfToken();
        if (!currentCsrfToken) {
            showMessage('error', 'Erro de segurança!', 'Token CSRF ausente. Recarregue a página.');
            return;
        }

        const response = await fetch('/face/authenticate-code', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': currentCsrfToken
            },
            body: JSON.stringify({
                code: code
            })
        });

        const data = await response.json();

        if (response.ok && data.authenticated) {
            showMessage('success', data.message);
            setTimeout(hideCodeInput, MESSAGE_DURATION);
        } else {
            showMessage('error', data.message || 'Falha ao autenticar com código de acesso.');
            accessCodeInput.value = '';
            accessCodeInput.focus();
        }

        if (window.location.pathname === '/captura') {
            websocketManager.sendMessage('kiosk-status', {
                status: 'idle',
                message: 'Kiosk liberado'
            });
        }
    } catch (error) {
        showMessage('error', 'Erro!', `Falha na autenticação: ${error.message}`);
    }
}


document.addEventListener('DOMContentLoaded', () => {
    loadModels();
    applyFormatting();
    initializeWebSocket();
});

if (startCameraButton && !isKioskPage) {
    startCameraButton.addEventListener('click', startVideo);
}

if (registerFaceButton && isCaptureFacePage) {
    registerFaceButton.addEventListener('click', () => {
        registerFace(lastDetectedDescriptor);
    });
}

if (submitCodeBtn) {
    submitCodeBtn.addEventListener('click', submitAccessCode);
}
if (cancelCodeBtn) {
    cancelCodeBtn.addEventListener('click', hideCodeInput);
}

if (accessCodeInput) {
    accessCodeInput.addEventListener('keyup', function(event) {
        this.value = this.value.replace(/[^0-9]/g, '');
        this.value = this.value.substring(0, CODIGO_ACESSO_LENGTH);
        if (event.key === 'Enter') {
            submitAccessCode();
        }
    });
}


window.addEventListener('beforeunload', function() {
    if (detectionIntervalId) clearInterval(detectionIntervalId);
    if (kioskStatusPollingId) clearInterval(kioskStatusPollingId);
    
    if (webSocketManager) {
        webSocketManager.disconnect();
    }
    
    if (isCaptureFacePage) {
        const currentCsrfToken = getCsrfToken();
        if (currentCsrfToken) {
            fetch('/face/set-kiosk-registering', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json', 
                    'X-CSRF-TOKEN': currentCsrfToken 
                },
                body: JSON.stringify({ is_registering: false })
            });
        }
    }

    if (window.location.pathname === '/captura') {
        websocketManager.sendMessage('kiosk-status', {
            status: 'idle',
            message: 'Kiosk liberado'
        });
    }
});

window.addEventListener('pagehide', () => {
    if (isCaptureFacePage) {
        const currentCsrfToken = getCsrfToken();
        if (currentCsrfToken) {
            navigator.sendBeacon('/face/set-kiosk-registering', JSON.stringify({
                is_registering: false,
                _token: currentCsrfToken
            }));
        }
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const userDropdown = document.querySelector('.user-info-section') || document.getElementById('userDropdown');
    const userDropdownToggle = document.querySelector('.user-info-content') || document.querySelector('.user-dropdown-toggle');
    const userDropdownMenu = document.querySelector('.user-dropdown-menu') || document.getElementById('userDropdownMenu');
    
    if (userDropdown && userDropdownToggle && userDropdownMenu) {
        userDropdownToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdownMenu.classList.toggle('show');
            
            const arrow = userDropdownToggle.querySelector('.dropdown-arrow i');
            if (arrow) {
                arrow.classList.toggle('rotated');
            }
        });
        
        document.addEventListener('click', (e) => {
            if (!userDropdown.contains(e.target)) {
                userDropdownMenu.classList.remove('show');
                const arrow = userDropdownToggle.querySelector('.dropdown-arrow i');
                if (arrow) {
                    arrow.classList.remove('rotated');
                }
            }
        });
        
        userDropdownMenu.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }
});

let notificationContainer = null;

function createNotificationContainer() {
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.className = 'notification-container';
        document.body.appendChild(notificationContainer);
    }
    return notificationContainer;
}

function showNotification(...args) {
    let type = 'info';
    let title = null;
    let message = '';
    let duration = 5000;

    const knownTypes = ['success', 'error', 'warning', 'info'];

    if (typeof args[0] === 'string' && knownTypes.includes(args[0])) {
        type = args[0];
        title = typeof args[1] === 'string' ? args[1] : null;
        message = typeof args[2] === 'string' ? args[2] : '';
        duration = typeof args[3] === 'number' ? args[3] : 5000;
    } else {
        message = typeof args[0] === 'string' ? args[0] : '';
        type = typeof args[1] === 'string' && knownTypes.includes(args[1]) ? args[1] : 'info';
        duration = typeof args[2] === 'number' ? args[2] : 5000;
    }

    const container = createNotificationContainer();

    const notification = document.createElement('div');
    notification.className = `notification-popup ${type}`;

    const iconMap = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };

    const titleText = title || ({
        success: 'Sucesso',
        error: 'Erro',
        warning: 'Aviso',
        info: 'Informação'
    }[type]);

    notification.innerHTML = `
        <div class="notification-header">
            <div class="notification-title ${type}">
                <i class="${iconMap[type]} notification-icon"></i>
                <span>${titleText}</span>
            </div>
            <button class="notification-close" aria-label="Fechar">&times;</button>
        </div>
        <div class="notification-message">${message}</div>
        <div class="notification-progress"></div>
    `;

    const closeBtn = notification.querySelector('.notification-close');
    let removeTimer = null;

    const removeWithAnimation = () => {
        notification.classList.add('removing');
        setTimeout(() => notification.remove(), 300);
    };

    closeBtn.addEventListener('click', () => {
        if (removeTimer) clearTimeout(removeTimer);
        removeWithAnimation();
    });

    container.appendChild(notification);

    removeTimer = setTimeout(() => {
        removeWithAnimation();
    }, duration);

    return notification;
}

function showSuccess(message, duration = 5000) {
    return showNotification(message, 'success', duration);
}

function showError(message, duration = 5000) {
    return showNotification(message, 'error', duration);
}

function showWarning(message, duration = 5000) {
    return showNotification(message, 'warning', duration);
}

function showInfo(message, duration = 5000) {
    return showNotification(message, 'info', duration);
}

window.showNotification = showNotification;
window.showSuccess = showSuccess;
window.showError = showError;
window.showWarning = showWarning;
window.showInfo = showInfo;

function showConfirmDialog(options = {}) {
    const {
        title = 'Confirmação',
        message = 'Tem certeza que deseja realizar esta ação?',
        confirmText = 'Confirmar',
        cancelText = 'Cancelar',
        confirmClass = 'bg-grip-1 hover:bg-grip-2',
        cancelClass = 'bg-gray-200 hover:bg-gray-300 text-gray-800',
        icon = 'warning'
    } = options;

    return new Promise((resolve) => {
        const existingOverlay = document.getElementById('confirmDialogOverlay');
        if (existingOverlay) {
            existingOverlay.remove();
        }

        const overlay = document.createElement('div');
        overlay.id = 'confirmDialogOverlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]';
        overlay.style.cssText = 'backdrop-filter: blur(2px);';

        const iconSvg = getIconSvg(icon);

        overlay.innerHTML = `
            <div class="confirm-dialog bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 transform transition-all duration-200 scale-95 opacity-0" style="animation: confirmDialogIn 0.2s ease-out forwards;">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full ${icon === 'danger' ? 'bg-red-100' : icon === 'warning' ? 'bg-yellow-100' : 'bg-blue-100'} flex items-center justify-center">
                            ${iconSvg}
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">${title}</h3>
                            <p class="text-sm text-gray-600">${message}</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-100">
                    <button type="button" id="confirmDialogCancel" class="px-4 py-2 rounded-lg font-medium transition-colors ${cancelClass}">
                        ${cancelText}
                    </button>
                    <button type="button" id="confirmDialogConfirm" class="${confirmClass} text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        ${confirmText}
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';

        const dialog = overlay.querySelector('.confirm-dialog');
        const confirmBtn = overlay.querySelector('#confirmDialogConfirm');
        const cancelBtn = overlay.querySelector('#confirmDialogCancel');

        function cleanup(result) {
            dialog.style.animation = 'confirmDialogOut 0.15s ease-in forwards';
            setTimeout(() => {
                overlay.remove();
                document.body.style.overflow = '';
                resolve(result);
            }, 150);
        }

        confirmBtn.addEventListener('click', () => cleanup(true));
        cancelBtn.addEventListener('click', () => cleanup(false));

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                cleanup(false);
            }
        });

        document.addEventListener('keydown', function handleEscape(e) {
            if (e.key === 'Escape') {
                document.removeEventListener('keydown', handleEscape);
                cleanup(false);
            }
        });

        setTimeout(() => confirmBtn.focus(), 100);
    });
}

function getIconSvg(icon) {
    switch (icon) {
        case 'danger':
            return `<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>`;
        case 'warning':
            return `<svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>`;
        case 'info':
            return `<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>`;
        case 'success':
            return `<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>`;
        default:
            return `<svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>`;
    }
}

function confirmAction(formElement, options = {}) {
    if (!formElement) return;
    
    formElement.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const confirmed = await showConfirmDialog(options);
        
        if (confirmed) {
            formElement.removeEventListener('submit', arguments.callee);
            formElement.submit();
        }
    });
}

window.showConfirmDialog = showConfirmDialog;
window.confirmAction = confirmAction;

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-confirm]').forEach(form => {
        const message = form.dataset.confirm || 'Tem certeza que deseja realizar esta ação?';
        const title = form.dataset.confirmTitle || 'Confirmação';
        const icon = form.dataset.confirmIcon || 'warning';
        const confirmText = form.dataset.confirmText || 'Confirmar';
        const cancelText = form.dataset.cancelText || 'Cancelar';

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const confirmed = await showConfirmDialog({
                title,
                message,
                icon,
                confirmText,
                cancelText
            });
            
            if (confirmed) {
                const handler = form._confirmHandler;
                if (handler) {
                    form.removeEventListener('submit', handler);
                }
                form.submit();
            }
        });
    });

    const sessionMessages = document.querySelectorAll('[data-session-message]');
    
    sessionMessages.forEach(element => {
        const type = element.dataset.sessionType || 'info';
        const message = element.textContent.trim();
        
        if (message) {
            showNotification(message, type);
            element.style.display = 'none';
        }
    });
});

if (isCaptureFacePage) {
    document.body.classList.add('face-capture-page');
}
