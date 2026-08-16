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
                ['text' => 'You are a smart AI assistant for the HRFlow HR system.
                The system automatically identifies the employee from the session,
                 so never ask them for their employee ID. Instead,
                 invoke the appropriate tool immediatelyوAfter invoking any tool, always convert the raw returned data into a clear, friendly human sentence for the user. Never return an empty text response.'
                 ]        ]
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



    $finalResponse = Http::timeout(60)->post($url, $finalPayload)->json();
// Safely extract the generated response text from the first candidate part, or fall back to null if missing.
    $replyText = $finalResponse['candidates'][0]['content']['parts'][0]['text'] ?? null;



    // Fallback logic: If Gemini fails to generate a text summary, format and present the raw tool data to the user.
     if (!$replyText) {
         if (is_array($toolResult) && isset($toolResult['total_remaining_days'])) {
        $replyText = "مجموع إجازاتك المتبقية لسنة " . ($toolResult['year'] ?? 2026) . " هو " . $toolResult['total_remaining_days'] . " يوم.";
    } else {
              $replyText = "Here are the requested details:\n" . json_encode($toolResult, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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







}
