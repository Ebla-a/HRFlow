<?php

namespace Modules\AI\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\AI\Entities\AiConversation;
use Modules\AI\Http\Requests\AiFormRequest;
use Modules\AI\Services\GeminiService;

class AIController extends Controller
{
   public function __construct(private GeminiService $geminiService) {}

/**
 * Summary of ask
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function ask(AiFormRequest $request)
    {

      $validated = $request->validated();

        $userId = auth()->id() ;

// Fetch existing conversation model if conversation_id is provided
        $conversation = $request->filled('conversation_id')
            ? AiConversation::find($validated['conversation_id'])
            : null;

        $result = $this->geminiService->ask(
            $validated['prompt'],
            $userId,
            $conversation
        );

        return response()->json([
            'status'          => 'success',
            'conversation_id' => $result['conversation_id'],
            'reply'           => $result['reply'],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }


    /**
     * Summary of index
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $conversations = AiConversation::where('user_id', auth()->id() )
            ->latest()
            ->get();

        return response()->json($conversations);
    }
    /**
     * Summary of show
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $conversation = AiConversation::where('user_id', auth()->id() )
            ->with('messages')
            ->findOrFail($id);

        return response()->json($conversation);
    }
}
