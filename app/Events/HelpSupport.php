<?php

namespace App\Events;

use App\Models\Channel as ModelsChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HelpSupport implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public ModelsChannel $channel;
   
    public function __construct(ModelsChannel $channel)
    {
        $this->channel = $channel;
    }

    public function broadcastAs()
    {
        return 'new-request';
    }
   
    public function broadcastOn()
    {
        return new PrivateChannel('channel-support');
    }

    public function broadcastWith()
    {
        preg_match('/\.(.*)/', $this->channel->hash, $matches);
        $channel = trim($matches[1]);
        return [
            'id' => $this->channel->id,
            'hash' => $channel,
            'owner_id' => $this->channel->owner_id
        ];
    }
}
