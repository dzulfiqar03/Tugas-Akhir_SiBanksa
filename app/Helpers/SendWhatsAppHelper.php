<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SendWhatsAppHelper
{
    /**
     * Fungsi kirim pesan WhatsApp via Fonnte
     */
    public static function send($target, $message)
    {
        $token = env('FONNTE_TOKEN'); // Simpan token di .env
        $target = preg_replace('/^0/', '62', $target); // Normalisasi nomor

        $response = Http::withHeaders([
            'Authorization' => $token
        ])->post('https://api.fonnte.com/send', [
            'target' => $target,
            'message' => $message,
            'delay' => '2-5', // Rekomendasi: beri jeda otomatis agar tidak kena ban
            'countryCode' => '62',
        ]);

        return $response->json();
    }
}
