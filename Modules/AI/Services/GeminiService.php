<?php

namespace Modules\AI\Services;

use Illuminate\Support\Facades\Http;
use Modules\AI\Entities\AiConversation;
use Modules\AI\Entities\Message;

class GeminiService
{
    public function __construct(private AiToolRegistry $toolRegistry) {}
    /**
     * Summary of ask
     * @param string $userMessage
     * @param int $userId
     * @param mixed $conversation
     * @return array{conversation_id: mixed, reply: mixed|array{conversation_id: mixed, reply: string}}
     */
    public function ask(string $userMessage, int $userId, ?AiConversation $conversation = null)
    {
        $apiKey = config('services.gemini.key', env('GEMINI_API_KEY'));
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key={$apiKey}";

        if (!$conversation) {
            $conversation = AiConversation::create([
                'user_id' => $userId,
                //for make small title by take first 30 letters from user message
                'title'   => mb_substr($userMessage, 0, 30, 'UTF-8'),
            ]);
        }
//save user message
        Message::create([
            'conversation_id' => $conversation->id,
            'role'            => 'user',
            'content'         => [['text' => $userMessage]],
        ]);

        //get old messages to send them for ai
        $contents = $conversation->messages()->get()->map(function ($msg) {
            return [
                'role'  => $msg->role,
                'parts' => $msg->content,
            ];
        })->toArray();

        $payload = [
            'systemInstruction' => [
                'parts' => [
                           ['text' => "You are an intelligent HR assistant integrated into the HRFlow application.\n" .
                              "Important response rules:\n" .
                              "1. When executing tools and receiving JSON data, NEVER display raw JSON objects, code snippets, or raw technical fields to the user under any circumstances.\n" .
                              "2. Always analyze the data and craft a natural, human-friendly response in the user's language (defaulting to Arabic or English based on the input).\n" .
                              "3. Format performance reviews, list items, and structured data cleanly using Markdown tables or concise bullet points for optimal readability."
                             ]
                      ]
                ],
            'contents' => $contents,
            'tools'    => $this->toolRegistry->getDeclarationsForGemini(),
        ];

        $response = Http::timeout(60)->post($url, $payload)->json();

        $candidate = $response['candidates'][0]['content']['parts'][0] ?? null;

        if (!$candidate) {
            return [
                'conversation_id' => $conversation->id,
                 'reply' => 'An error occurred while communicating with the provider. Please try again later.',
                  ];
        }




//////////////////////////// Path 1: Tool Execution Path (Function Calling)

if (isset($candidate['functionCall'])) {
    $functionCall = $candidate['functionCall'];
    $functionName = $functionCall['name'];
    $arguments    = $functionCall['args'] ?? [];

//save request from ai model for excute tool
    Message::create([
        'conversation_id' => $conversation->id,
        'role'            => 'model',
        'content'         => [['functionCall' => $functionCall]],
    ]);

    $toolResult = $this->toolRegistry->execute($functionName, $arguments, $userId);

//save result of tool
    Message::create([
        'conversation_id' => $conversation->id,
        'role'            => 'function',
        'content'         => [
            [
                'functionResponse' => [
                    'name'     => $functionName,
                    'response' => is_array($toolResult) ? $toolResult : ['result' => $toolResult]
                ]
            ]
        ],
    ]);

    $updatedContents = $conversation->messages()->get()->map(function ($msg) {
            // Convert 'function' roles to 'user' because Gemini API expects tool
       // execution results (functionResponse) to be sent under the 'user' role in context history.

    if ($msg->role === 'function') {
        return [
            'role'  => 'user',
            'parts' => $msg->content,
        ];
    }

    return [
        'role'  => $msg->role,
        'parts' => $msg->content,
    ];
})->toArray();

    $finalPayload = [
        'systemInstruction' => $payload['systemInstruction'],
        'contents'          => $updatedContents,
        'tools'             => $payload['tools'],
    ];




    $finalResponse = Http::timeout(90)->post($url, $finalPayload)->json();
// Safely extract the generated response text from the first candidate part, or fall back to null if missing.
    $replyText = $finalResponse['candidates'][0]['content']['parts'][0]['text'] ?? null;



    // Fallback logic: If Gemini fails to generate a text summary, format and present the raw tool data to the user.

if (!$replyText) {
    if (is_array($toolResult)) {
        $dataToFormat = $toolResult['data'] ?? $toolResult;

        if (is_array($dataToFormat)) {
            unset($dataToFormat['status'], $dataToFormat['count'], $dataToFormat['total']);
        }

        $replyText = $this->formatDynamicToolResult($dataToFormat, $toolResult['message'] ?? null);
    } else {
        $replyText = (string) $toolResult;
    }
}

        //save final message from ai
    Message::create([
        'conversation_id' => $conversation->id,
        'role'            => 'model',
        'content'         => [['text' => $replyText]],
    ]);

    return [
        'conversation_id' => $conversation->id,
        'reply'           => $replyText,
    ];
}

///////////////////// Path 2: Direct Text Response Path (Greetings, Casual Chat & Q&A)

$replyText = $candidate['text'] ?? 'Hello! How can I assist you with HRFlow today?';
Message::create([
    'conversation_id' => $conversation->id,
    'role'            => 'model',
    'content'         => [['text' => $replyText]],
]);

return [
    'conversation_id' => $conversation->id,
    'reply'           => $replyText,
];

}



/**
 * Summary of formatDynamicToolResult
 * @param array $data
 * @param mixed $defaultMessage
 * @return string
 */
private function formatDynamicToolResult(array $data, ?string $defaultMessage = null): string
{
    if (empty($data)) {
        return $defaultMessage ?? 'لا توجد بيانات متاحة حالياً.';
    }

    $output = "";
    if ($defaultMessage) {
        $output .= "{$defaultMessage}\n\n";
    }

    if (array_is_list($data)) {
        foreach ($data as $index => $item) {
            $num = $index + 1;
            $output .= " #{$num} ";

            if (is_array($item)) {
                foreach ($item as $key => $value) {
                    $formattedKey = ucwords(str_replace(['_', '-'], ' ', $key));
                    $formattedValue = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : ($value ?? 'N/A');
                    $output .= "• {$formattedKey}: {$formattedValue}  ";
                }
            } else {
                $output .= "• {$item}\n";
            }
            $output .= "\n";
        }

        return trim($output);
    }

    foreach ($data as $key => $value) {
        $formattedKey = ucwords(str_replace(['_', '-'], ' ', $key));

        if (is_array($value)) {
            $output .= " {$formattedKey}:**  ";
            foreach ($value as $subKey => $subValue) {
                $subFormattedKey = ucwords(str_replace(['_', '-'], ' ', $subKey));
                $output .= "  • {$subFormattedKey}:  " . (is_array($subValue) ? json_encode($subValue, JSON_UNESCAPED_UNICODE) : ($subValue ?? 'N/A')) ;
            }
        } else {
            $output .= "• {$formattedKey}: " . ($value ?? 'N/A') ;
        }
    }

    return trim($output);
}


}
