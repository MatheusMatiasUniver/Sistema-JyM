import './bootstrap';
import * as faceapi from 'face-api.js';
import { initVendasCreate } from './vendas-create';
import * as formatters from './formatters';
import './cnpj-mask';
import webSocketManager from './websocket-manager';

window.applyFormatting = formatters.applyFormatting;

const video = document.getElementById('videoElement');
const canvas = document.getElementById('overlayCanvas');
const resultsDiv = document.getElementById('results');
const startCameraButton = document.getElementById('startCameraButton');
const registerFaceButton = document.getElementById('registerFaceButton');

const context = canvas ? canvas.getContext('2d') : null;

const codeInputArea = document.getElementById('code-input-area');
const cpfInput = document.getElementById('cpfInput');
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
        resultsDiv.classList.remove('alert-info', 'alert-success', 'alert-error');
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
    const newIsRegistering = data.isRegistering || false;
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
        } else {
            console.log('WebSocket desconectado - tentando reconectar...');
            setTimeout(() => {
                if (!webSocketManager.isWebSocketConnected()) {
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
    
    kioskStatusPollingId = setInterval(async () => {
        if (webSocketManager.isWebSocketConnected()) {
            clearInterval(kioskStatusPollingId);
            kioskStatusPollingId = null;
            return;
        }
        
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
        showMessage('error', 'Erro!', `Ao iniciar webcam: ${err.name} - ${err.message}. Verifique permissões e se a câmera não está em uso por outro aplicativo.`);

        if (startCameraButton) startCameraButton.disabled = true;
        
        if (isCaptureFacePage) {
            const currentCsrfToken = getCsrfToken();
            if (currentCsrfToken) {
                fetch('/face/set-kiosk-registering', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
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
    if (canvas) overlayCanvas.style.display = 'block';

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
                    'X-CSRF-TOKEN': currentCsrfToken
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
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': currentCsrfToken },
                    body: JSON.stringify({ is_registering: false })
                });

                if (isCaptureFacePage) {
                    window.location.href = '/clientes';
                } else if (isKioskPage) {
                    showMessage('info', 'Aguardando detecção de rosto.');
                    // Reinicia o loop de detecção se estivermos no kiosk
                    if (!detectionIntervalId && modelsLoaded && video && !video.paused && !video.ended) {
                        startDetectionLoop();
                    }
                }
            }, REGISTER_COOLDOWN_DURATION);
        } else {
            showMessage('error', 'Erro no Registro!', `${data.message || 'Falha ao registrar o rosto.'}`);
            fetch('/face/set-kiosk-registering', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': currentCsrfToken },
                body: JSON.stringify({ is_registering: false })
            });

            setTimeout(() => {
                showMessage('info', 'Aguardando detecção de rosto.');
                // Reinicia o loop de detecção após erro
                if (isCaptureFacePage && !detectionIntervalId && modelsLoaded && video && !video.paused && !video.ended) {
                    startDetectionLoop();
                }
            }, MESSAGE_DURATION);
        }
    } catch (error) {
        showMessage('error', 'Erro!', `Falha no registro: ${error.message}`);
        fetch('/face/set-kiosk-registering', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': currentCsrfToken },
            body: JSON.stringify({ is_registering: false })
        });
        setTimeout(() => {
            showMessage('info', 'Aguardando detecção de rosto.');
            // Reinicia o loop de detecção após exceção
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
    if (cpfInput) {
        cpfInput.value = '';
        cpfInput.focus();
    }
    if (accessCodeInput) {
        accessCodeInput.value = '';
    }
    showMessage('info', 'Rosto não reconhecido!', 'Por favor, digite seu CPF e código de acesso.');
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
    let cpf = cpfInput.value;
    const code = accessCodeInput.value;

    cpf = cpf.replace(/[^0-9]/g, '');

    if (!cpf || cpf.length !== 11 || isNaN(cpf)) {
        showMessage('error', 'CPF inválido!', 'Por favor, digite um CPF válido (11 dígitos numéricos).');
        cpfInput.focus();
        return;
    }

    if (!code || code.length !== CODIGO_ACESSO_LENGTH || isNaN(code)) {
        showMessage('error', 'Código inválido!', `Por favor, digite um código de ${CODIGO_ACESSO_LENGTH} dígitos numéricos.`);
        accessCodeInput.focus();
        return;
    }

    showMessage('info', 'Verificando CPF e código...');

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
                cpf: cpf,
                code: code
            })
        });

        const data = await response.json();

        if (response.ok && data.authenticated) {
            showMessage('success', data.message);
            setTimeout(hideCodeInput, MESSAGE_DURATION);
        } else {
            showMessage('error', data.message || 'Falha ao autenticar com CPF e código.');
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

if (cpfInput) {
    cpfInput.addEventListener('keyup', function(event) {
        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11);
        if (event.key === 'Enter') {
            accessCodeInput.focus();
        }
    });
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
    // Support both signatures:
    // 1) showNotification(message, type = 'info', duration = 5000)
    // 2) showNotification(type, title, message, duration = 5000)
    let type = 'info';
    let title = null;
    let message = '';
    let duration = 5000;

    const knownTypes = ['success', 'error', 'warning', 'info'];

    if (typeof args[0] === 'string' && knownTypes.includes(args[0])) {
        // type-first signature
        type = args[0];
        title = typeof args[1] === 'string' ? args[1] : null;
        message = typeof args[2] === 'string' ? args[2] : '';
        duration = typeof args[3] === 'number' ? args[3] : 5000;
    } else {
        // message-first signature
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

// Expose notification helpers globally for views that call them directly
window.showNotification = showNotification;
window.showSuccess = showSuccess;
window.showError = showError;
window.showWarning = showWarning;
window.showInfo = showInfo;

document.addEventListener('DOMContentLoaded', function() {
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

// Use existing flag instead of undefined currentPath
if (isCaptureFacePage) {
    document.body.classList.add('face-capture-page');
}