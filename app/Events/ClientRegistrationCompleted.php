<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientRegistrationCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $clientId;
    public $clientName;
    public $success;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($clientId, $clientName = null, $success = true, $message = null)
    {
        $this->clientId = $clientId;
        $this->clientName = $clientName;
        $this->success = $success;
        $this->message = $message ?: ($success 
            ? "Registro de rosto para {$clientName} concluído com sucesso!" 
            : "Falha no registro de rosto para cliente {$clientId}.");
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('client-registration'),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'client_id' => $this->clientId,
            'client_name' => $this->clientName,
            'success' => $this->success,
            'message' => $this->message,
            'status' => 'completed',
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'registration.completed';
    }
}
