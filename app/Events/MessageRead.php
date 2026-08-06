<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $reader_id;           // ID User yang membaca pesan (penerima)
    public $original_sender_id;  // ID User pengirim pesan asli (target broadcast)

    /**
     * @param  int|string  $readerId          ID User yang baru saja membaca pesan
     * @param  int|string  $originalSenderId  ID User pengirim pesan asli (akan menerima update centang biru)
     */
    public function __construct($readerId, $originalSenderId)
    {
        $this->reader_id = (string) $readerId;
        $this->original_sender_id = $originalSenderId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->original_sender_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageRead';
    }

    public function broadcastWith(): array
    {
        return [
            'reader_id' => $this->reader_id,
        ];
    }
}
