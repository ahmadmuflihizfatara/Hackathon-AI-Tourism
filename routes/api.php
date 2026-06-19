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

use App\Http\Controllers\DestinationController;

// Endpoint gambar destinasi (tanpa auth, bebas diakses frontend)
Route::get('/destinations/image',   [DestinationController::class, 'getImage']);
Route::post('/destinations/images', [DestinationController::class, 'getBatchImages']);
