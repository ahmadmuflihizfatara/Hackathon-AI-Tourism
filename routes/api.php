<?php

use App\Http\Controllers\GeminiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — NusantaraAI Tourism Planner
|--------------------------------------------------------------------------
*/

// Gemini AI proxy — called by the frontend dashboard
Route::post('/gemini', [GeminiController::class, 'chat'])->name('api.gemini');

use App\Http\Controllers\MapController;

Route::post('/route/optimize', [MapController::class, 'optimize']);
Route::post('/route/directions', [MapController::class, 'directions']);