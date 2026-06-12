<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GroqServices
{
    public function ask(string $userMessage, string $contextData): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'Kamu adalah AI Banksa, asisten bank sampah yang ramah.
                                  Jawab dalam Bahasa Indonesia. Data konteks: ' . $contextData,
                ],
                [
                    'role'    => 'user',
                    'content' => $userMessage,
                ],
            ],
        ]);



        return $response->json('choices.0.message.content')
            ?? 'Maaf, saya tidak dapat merespons saat ini.';
    }
}
