<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientRegistrationStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $clientId;
    public $clientName;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($clientId, $clientName = null, $message = null)
    {
        $this->clientId = $clientId;
        $this->clientName = $clientName;
        $this->message = $message ?: "Registro de rosto para cliente {$clientId} iniciado...";
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
            'message' => $this->message,
            'status' => 'started',
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'registration.started';
    }
}
