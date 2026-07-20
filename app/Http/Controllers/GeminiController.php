<?php

namespace App\Http\Controllers;

use App\Services\GeminiApiService;
use App\Services\RagService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class GeminiController extends Controller
{
    public function __construct(
        protected GeminiApiService $lmService,
        protected RagService $ragService
    ) {}

    /**
     * Handle chat message from frontend dashboard.
     * POST /api/gemini
     */
    public function chat(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'history'               => 'required|array|min:1',
            'history.*.role'        => 'required|in:user,model',
            'history.*.parts'       => 'required|array',
            'history.*.parts.*.text'=> 'required|string|max:4000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Format percakapan tidak valid.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $history = $request->input('history');

        // Retrieve latest user message to use as RAG query
        $latestUserMessage = '';
        for ($i = count($history) - 1; $i >= 0; $i--) {
            if ($history[$i]['role'] === 'user') {
                $latestUserMessage = $history[$i]['parts'][0]['text'] ?? '';
                break;
            }
        }

        $ragContext = '';
        if (!empty($latestUserMessage)) {
            // Find Top 5 relevant context
            $ragContext = $this->ragService->searchContext($latestUserMessage, 5);
        }

        $result = $this->lmService->chat($history, $ragContext);

        return response()->json($result);
    }
}
