<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeclarationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // public $name;
    public $id_dec;
    public $id_med;
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    // public function __construct($name)
    // {
    //     $this->name = $name;
    // }
    public function __construct($id_dec, $id_med, $message )
    {
        $this->id_dec = $id_dec;
        $this->id_med = $id_med;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('notification-declaration');
    }

    public function broadcastAs()
    {
        return 'declaration-notif';
    }
}
