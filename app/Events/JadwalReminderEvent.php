<?php

// namespace App\Events;

// use Illuminate\Broadcasting\Channel;
// use Illuminate\Broadcasting\PrivateChannel;
// use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Foundation\Events\Dispatchable;
// use Illuminate\Queue\SerializesModels;

// class JadwalReminderEvent implements ShouldBroadcast
// {
//     use Dispatchable, InteractsWithSockets, SerializesModels;

//     public function __construct(public $jadwal, public $rtId) {}

//     public function broadcastOn()
//     {
//         // Kirim ke channel per-RT biar cuma warga RT terkait yang nerima
//         return new PrivateChannel('rt.' . $this->rtId);
//     }

//     public function broadcastAs()
//     {
//         return 'jadwal.reminder';
//     }

//     public function broadcastWith()
//     {
//         return [
//             'keterangan' => 'Ada jadwal pelaksanaan setoran sampah hari ini: ',
//             'tanggal' => $this->jadwal->tanggal_setoran->format('Y-m-d'),
//             'jadwal_id' => $this->jadwal->id,
//         ];
//     }
// }
