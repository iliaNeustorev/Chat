<?php

namespace App\Events;

use App\Models\Channel as ModelsChannel;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class SendMessage implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public array $data;
    public int $messageId;
    public bool $change;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $data, int $messageId, $change = false)
    {
        $this->data = $data;
        $this->messageId = $messageId;
        $this->change = $change;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('translation.'.$this->data['channel_id']);
    }

    public function broadcastAs()
    {
        if($this->change) {
            return 'change-message';
        } else {
            return 'new-message';
        }
    }
    
    public function broadcastWith()
    {
        return [
            'id' => $this->messageId,
            'message' => $this->data['text'],
            'channel' => $this->data['channel_id'],
            'recipient_name' => $this->data['recipient_name'],
            'sender_name' => $this->data['sender_name'],
            'parent_id' => $this->data['parent_id'] ?? null,
        ];
    }
}
