<?php
namespace Modules\AI\Services\Providers;

use Modules\AI\Contracts\AiProviderInterface;
use Illuminate\Support\Facades\Http;

class GeminiProvider implements AiProviderInterface
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->baseUrl = config('services.gemini.base_url');
        $this->model =config('services.gemini.model');
    }

public function generateContent(array $contents, array $tools = []): array
    {
        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

        $payload = ['contents' => $contents];

        if (!empty($tools)) {
            $payload['tools'] = [['functionDeclarations' => $tools]];
        }

        $response = Http::acceptJson()->post($url, $payload);

        if ($response->failed()) {
            throw new \Exception("Gemini API Error: " . $response->body());
        }

        return $response->json();
    }
}
