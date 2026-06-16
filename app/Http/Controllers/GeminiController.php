<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class GeminiController extends Controller
{
    public function __construct(protected GeminiService $gemini) {}

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

        $result = $this->gemini->chat($request->input('history'));

        return response()->json($result);
    }
}
