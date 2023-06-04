<?php

namespace App\Events;

use App\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $name;
    public $link;
    public $type;
    public $created;

    private $user_id;

    /**
     * Create a new event instance.
     *
     * @param $user_id
     * @param Notification $notification
     */
    public function __construct($user_id, Notification $notification)
    {
        $this->name = $notification->name;
        $this->link = $notification->link;
        $this->type = $notification->type;
        $this->created = $notification->created_at->diffForHumans();
        $this->user_id = $user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifications.'.$this->user_id);
    }
}
