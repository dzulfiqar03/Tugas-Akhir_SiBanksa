<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $id;
    public $sender_id;
    public $sender_name;
    public $message;
    public $time;
    public $receiver_user_id;

    public function __construct($chat, string $senderName, $receiverUserId)
    {
        $this->id = $chat->id;
        $this->sender_id = (string) $chat->sender_id;
        $this->sender_name = $senderName;
        $this->message = $chat->message;
        $this->time = $chat->time;
        $this->receiver_user_id = $receiverUserId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->receiver_user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'NewChatMessage';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'sender_name' => $this->sender_name,
            'message' => $this->message,
            'time' => $this->time,
        ];
    }
}
