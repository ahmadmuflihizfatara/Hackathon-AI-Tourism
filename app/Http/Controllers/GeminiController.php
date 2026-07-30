<?php

namespace App\Http\Controllers;

use App\Services\GeminiApiService;
use App\Services\RagService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
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
        Log::info('Gemini chat request payload', $request->all());

        $validator = Validator::make($request->all(), [
            'history'               => 'required|array|min:1',
            'history.*.role'        => 'required|in:user,model',
            'history.*.parts'       => 'required|array',
            'history.*.parts.*.text'=> 'required|string|max:4000',
        ]);

        if ($validator->fails()) {
            Log::warning('Gemini chat validation failed', [
                'payload' => $request->all(),
                'errors' => $validator->errors()->toArray(),
            ]);
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
            try {
                // Find Top 5 relevant context
                $ragContext = $this->ragService->searchContext($latestUserMessage, 5);
            } catch (\Throwable $e) {
                // Jangan sampai kegagalan RAG (mis. koneksi DB vector, index kosong,
                // dsb.) membuat seluruh chat gagal total dengan 500 HTML page.
                Log::error('RagService searchContext gagal, lanjut tanpa konteks RAG', [
                    'error' => $e->getMessage(),
                ]);
                $ragContext = '';
            }
        }

        try {
            $result = $this->lmService->chat($history, $ragContext);
        } catch (\Throwable $e) {
            Log::error('GeminiApiService chat() melempar exception', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Maaf, terjadi gangguan pada server AI. Silakan coba lagi.',
            ], 500);
        }

        return response()->json($result);
    }
}