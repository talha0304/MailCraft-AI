<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GroqApiService
{
    private string $baseUrl = "https://api.groq.com/openai/v1/chat/completions";
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY'); 
    }

    public function generateEmail(string $prompt)
    {
        $messages = [
            [
                'role' => 'user',
                'content' => $prompt,
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl, [
                    'messages' => $messages,
                    'model' => 'llama-3.3-70b-specdec',
                    'temperature' => 0.7,
                    'max_completion_tokens' => 512,
                    'top_p' => 1,
                    'stream' => false,
                ]);

       

        if ($response->successful()) {
            $jsonResponse = $response->json();

            if ($jsonResponse === null) {
                logger()->error('Failed to decode JSON response', ['response' => $response->body()]);
                return [
                    'error' => 'Invalid JSON response',
                    'message' => $response->body(),
                ];
            }

            return $jsonResponse['choices'][0]['message']['content'] ?? 'No content available';
        }

        // Handle API error responses
        return [
            'error' => $response->status(),
            'message' => $response->body(),
        ];
    }



    
}

