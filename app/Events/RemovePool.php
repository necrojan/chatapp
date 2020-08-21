<?php

namespace App\Events;

use App\Client;
use App\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\AuthManager;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RemovePool implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Client
     */
    public $client;

    /**
     * @var int
     */
    public $acceptedBy;

    /**
     * Create a new event instance.
     *
     * @param Client $client
     * @param int $acceptedBy
     */
    public function __construct(Client $client, int $acceptedBy)
    {
        $this->client = $client;
        $this->acceptedBy = $acceptedBy;

        $this->dontBroadcastToCurrentUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('remove.pool');
    }
}
