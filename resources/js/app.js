import './bootstrap';
import * as faceapi from 'face-api.js';
import { initVendasCreate } from './vendas-create';

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

const COOLDOWN_DURATION = 5000;
const MESSAGE_DURATION = 3000;
const REGISTER_COOLDOWN_DURATION = 10000;
const KIOSK_POLLING_INTERVAL = 3000;
const MAX_FAILED_FACE_ATTEMPTS = 3;
const CODIGO_ACESSO_LENGTH = 6;

const isKioskPage = window.location.pathname.includes('/reconhecimento');
const isCaptureFacePage = window.location.pathname.includes('/clientes/') && window.location.pathname.includes('/capturar-rosto');

if (document.querySelector('#produtos-data')) {
    initVendasCreate();
}

function getCsrfToken() {
    const tokenElement = document.querySelector('meta[name="csrf-token"]');
    if (tokenElement) {
        return tokenElement.getAttribute('content');
    }
    console.error('CSRF token meta tag not found.');
    return null;
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
        const csrfToken = getCsrfToken();
        if (!csrfToken) {
            console.error('CSRF token is null or undefined when attempting to pollKioskStatus.');
            return;
        }

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

                if (codeInputArea) codeInputArea.style.display = 'none';
                showMessage('info', kioskRegistrationMessage || 'Registro de rosto em andamento...');
                console.log('Kiosk: Registro em andamento detectado, pausando autenticação.');
            } else if (!detectionStarted && modelsLoaded && video && video.srcObject) {
                console.log('Kiosk: Registro finalizado, reiniciando autenticação.');
                showMessage('info', 'Aguardando detecção de rosto.');
                hideCodeInput();
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

    if (codeInputArea) codeInputArea.style.display = 'none';
    if (video) video.style.display = 'block';
    if (canvas) overlayCanvas.style.display = 'block';

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
        const currentCsrfToken = getCsrfToken();
        if (!currentCsrfToken) {
            showMessage('error', 'Erro de segurança!', 'Token CSRF ausente. Recarregue a página.');
            console.error('CSRF token is null or undefined when attempting to authenticateFace.');

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
        console.log('Resposta da autenticação:', data);

        if (response.ok && data.authenticated) {
            lastAuthenticatedClientId = data.client_id;

            failedFaceAttempts = 0;sucesso
            if (data.status === 'Ativo') {
                showMessage('success', 'ACESSO LIBERADO!', `Bom treino, ${data.user_name}!`);
            } else {
                showMessage('error', 'ACESSO NEGADO!', `Status: ${data.status} para ${data.user_name}.`);

                failedFaceAttempts++;
                if (failedFaceAttempts >= MAX_FAILED_FACE_ATTEMPTS) {
                    showCodeInput();
                    failedFaceAttempts = 0;
                } else {
                    showMessage('error', 'ROSTO NÃO AUTORIZADO!', `Tentativas: ${failedFaceAttempts}/${MAX_FAILED_FACE_ATTEMPTS}. Por favor, tente novamente.`);
                }
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
        }
    } catch (error) {
        console.error('Erro durante a autenticação (fetch/JSON):', error);
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

    showMessage('info', `Registrando rosto para Cliente ID ${clientId}...`);

    try {
        const currentCsrfToken = getCsrfToken();
        if (!currentCsrfToken) {
            showMessage('error', 'Erro de segurança!', 'Token CSRF ausente. Recarregue a página.');
            console.error('CSRF token is null or undefined when attempting to registerFace.');
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
        console.log('Resposta do registro:', data);

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
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': currentCsrfToken },
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
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': currentCsrfToken },
            body: JSON.stringify({ is_registering: false })
        }).then(res => res.json()).then(res => console.log('Kiosk status cleared (exception):', res));
        setTimeout(() => {
            showMessage('info', 'Aguardando detecção de rosto.');
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

    if (!code || code.length !== CODIGO_ACESSO_LENGTH || isNaN(code)) { // Validação básica
        showMessage('error', 'Código inválido!', `Por favor, digite um código de ${CODIGO_ACESSO_LENGTH} dígitos numéricos.`);
        accessCodeInput.focus();
        return;
    }

    showMessage('info', 'Verificando CPF e código...');

    try {
        const currentCsrfToken = getCsrfToken();atualizado
        if (!currentCsrfToken) {
            showMessage('error', 'Erro de segurança!', 'Token CSRF ausente. Recarregue a página.');
            console.error('CSRF token is null or undefined when attempting to submitAccessCode.');
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
    } catch (error) {
        console.error('Erro durante autenticação por código:', error);
        showMessage('error', 'Erro!', `Falha na autenticação: ${error.message}`);
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


window.addEventListener('beforeunload', () => {
    if (detectionIntervalId) clearInterval(detectionIntervalId);
    if (kioskStatusPollingId) clearInterval(kioskStatusPollingId);
    if (isCaptureFacePage && detectionStarted) {
         fetch('/face/set-kiosk-registering', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
            body: JSON.stringify({ is_registering: false })
        }).then(res => res.json()).then(res => console.log('Kiosk status cleared on unload:', res));
    }
});