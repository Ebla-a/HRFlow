<?php

namespace CodingPartners\TranslaGenius\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AutoTranslationService
{
    protected $apiKey;
    protected $apiUrl;
    protected $model;

    public function __construct()
    {
        $this->apiKey = config('translaGenius.api.key');
        $this->apiUrl = rtrim(config('translaGenius.api.url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $this->model = config('translaGenius.api.model', 'gemini-3.6-flash');
    }

    public function translate($text, $sourceLanguage, $targetLanguage)
    {
        if (empty($text)) return $text;

        try {
            $baseUrl = str_replace('/models', '', $this->apiUrl);
            $endpoint = "{$baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

            
            $prompt = "Translate this text into {$targetLanguage}. "
                    . "Output ONLY the translation. Do not summarize.\n\n"
                    . "Text: " . $text;

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying() 
                ->timeout(60)
                ->post($endpoint, [
                    'contents' => [
                        [
                            'parts' => [['text' => $prompt]]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.0,
                        'maxOutputTokens' => 2048 
                    ]
                ]);

            if ($response->failed()) {
                Log::error('Gemini API Error', ['body' => $response->body()]);
                return $text;
            }

            $data = $response->json();
            $raw = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            
            Log::info('RAW RESPONSE FROM GEMINI:', ['raw' => $raw]);

            if (empty($raw)) return $text;

            
            $clean = str_replace(['```arabic', '```text', '```', 'Translation:', 'translation:'], '', $raw);
            
            
            $clean = ltrim($clean, " \t\n\r\0\x0B*:-");
            
            $result = trim($clean, " \t\n\r\0\x0B\"'«»“”‘’");

            Log::info('AutoTranslationService Success', ['result' => $result]);

            return $result;

        } catch (\Throwable $e) {
            Log::error('AutoTranslationService Error', ['msg' => $e->getMessage()]);
            return $text;
        }
    }
}