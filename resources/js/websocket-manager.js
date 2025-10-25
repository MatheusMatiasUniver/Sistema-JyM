import Pusher from 'pusher-js';

class WebSocketManager {
    constructor() {
        this.pusher = null;
        this.kioskStatusChannel = null;
        this.clientRegistrationChannel = null;
        this.isConnected = false;
        this.callbacks = {
            onKioskStatusChanged: null,
            onClientRegistrationStarted: null,
            onClientRegistrationCompleted: null,
            onConnectionStateChange: null
        };
    }

    /**
     * Inicializa a conexão WebSocket
     */
    init() {
        try {
            // Configuração do Pusher para Reverb
            this.pusher = new Pusher(window.pusherConfig.key, {
                cluster: window.pusherConfig.cluster,
                wsHost: window.pusherConfig.host,
                wsPort: window.pusherConfig.port,
                wssPort: window.pusherConfig.port,
                forceTLS: window.pusherConfig.scheme === 'https',
                enabledTransports: ['ws', 'wss'],
                disableStats: true,
            });

            // Eventos de conexão
            this.pusher.connection.bind('connected', () => {
                console.log('WebSocket conectado');
                this.isConnected = true;
                if (this.callbacks.onConnectionStateChange) {
                    this.callbacks.onConnectionStateChange(true);
                }
            });

            this.pusher.connection.bind('disconnected', () => {
                console.log('WebSocket desconectado');
                this.isConnected = false;
                if (this.callbacks.onConnectionStateChange) {
                    this.callbacks.onConnectionStateChange(false);
                }
            });

            this.pusher.connection.bind('error', (error) => {
                console.error('Erro na conexão WebSocket:', error);
                this.isConnected = false;
                if (this.callbacks.onConnectionStateChange) {
                    this.callbacks.onConnectionStateChange(false);
                }
            });

            // Subscrever aos canais
            this.subscribeToChannels();

        } catch (error) {
            console.error('Erro ao inicializar WebSocket:', error);
        }
    }

    /**
     * Subscreve aos canais necessários
     */
    subscribeToChannels() {
        // Canal de status do kiosk
        this.kioskStatusChannel = this.pusher.subscribe('kiosk-status');
        
        this.kioskStatusChannel.bind('status.changed', (data) => {
            console.log('Status do kiosk alterado:', data);
            if (this.callbacks.onKioskStatusChanged) {
                this.callbacks.onKioskStatusChanged(data);
            }
        });

        // Canal de registro de cliente
        this.clientRegistrationChannel = this.pusher.subscribe('client-registration');
        
        this.clientRegistrationChannel.bind('registration.started', (data) => {
            console.log('Registro de cliente iniciado:', data);
            if (this.callbacks.onClientRegistrationStarted) {
                this.callbacks.onClientRegistrationStarted(data);
            }
        });

        this.clientRegistrationChannel.bind('registration.completed', (data) => {
            console.log('Registro de cliente concluído:', data);
            if (this.callbacks.onClientRegistrationCompleted) {
                this.callbacks.onClientRegistrationCompleted(data);
            }
        });
    }

    /**
     * Define callback para mudanças de status do kiosk
     */
    onKioskStatusChanged(callback) {
        this.callbacks.onKioskStatusChanged = callback;
    }

    /**
     * Define callback para início de registro de cliente
     */
    onClientRegistrationStarted(callback) {
        this.callbacks.onClientRegistrationStarted = callback;
    }

    /**
     * Define callback para conclusão de registro de cliente
     */
    onClientRegistrationCompleted(callback) {
        this.callbacks.onClientRegistrationCompleted = callback;
    }

    /**
     * Define callback para mudanças no estado da conexão
     */
    onConnectionStateChange(callback) {
        this.callbacks.onConnectionStateChange = callback;
    }

    /**
     * Verifica se está conectado
     */
    isWebSocketConnected() {
        return this.isConnected;
    }

    /**
     * Desconecta do WebSocket
     */
    disconnect() {
        if (this.pusher) {
            this.pusher.disconnect();
            this.isConnected = false;
        }
    }

    /**
     * Reconecta ao WebSocket
     */
    reconnect() {
        if (this.pusher) {
            this.pusher.connect();
        }
    }
}

// Exporta uma instância singleton
export default new WebSocketManager();