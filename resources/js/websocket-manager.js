import Pusher from 'pusher-js';

class WebSocketManager {
    constructor() {
        this.pusher = null;
        this.kioskStatusChannel = null;
        this.clientRegistrationChannel = null;
        this.dashboardChannel = null;
        this.isConnected = false;
        this.callbacks = {
            onKioskStatusChanged: null,
            onClientRegistrationStarted: null,
            onClientRegistrationCompleted: null,
            onConnectionStateChange: null,
            onDashboardUpdated: null
        };
    }

    init() {
        try {
            this.pusher = new Pusher(window.pusherConfig.key, {
                cluster: window.pusherConfig.cluster,
                wsHost: window.pusherConfig.host,
                wsPort: window.pusherConfig.port,
                wssPort: window.pusherConfig.port,
                forceTLS: false,
                enabledTransports: ['ws', 'wss'],
                disableStats: true,
            });


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


            this.subscribeToChannels();

        } catch (error) {
            console.error('Erro ao inicializar WebSocket:', error);
        }
    }

    subscribeToChannels() {
        this.kioskStatusChannel = this.pusher.subscribe('kiosk-status');
        
        this.kioskStatusChannel.bind('status.changed', (data) => {
            console.log('Status do kiosk alterado:', data);
            if (this.callbacks.onKioskStatusChanged) {
                this.callbacks.onKioskStatusChanged(data);
            }
        });


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

        this.dashboardChannel = this.pusher.subscribe('dashboard');
        this.dashboardChannel.bind('dashboard.updated', (data) => {
            console.log('Dashboard atualizado:', data);
            if (this.callbacks.onDashboardUpdated) {
                this.callbacks.onDashboardUpdated(data);
            }
        });
    }


    onKioskStatusChanged(callback) {
        this.callbacks.onKioskStatusChanged = callback;
    }


    onClientRegistrationStarted(callback) {
        this.callbacks.onClientRegistrationStarted = callback;
    }


    onClientRegistrationCompleted(callback) {
        this.callbacks.onClientRegistrationCompleted = callback;
    }


    onConnectionStateChange(callback) {
        this.callbacks.onConnectionStateChange = callback;
    }

    onDashboardUpdated(callback) {
        this.callbacks.onDashboardUpdated = callback;
    }


    isWebSocketConnected() {
        return this.isConnected;
    }


    disconnect() {
        if (this.pusher) {
            this.pusher.disconnect();
            this.isConnected = false;
        }
    }


    reconnect() {
        if (this.pusher) {
            this.pusher.connect();
        }
    }
}


export default new WebSocketManager();