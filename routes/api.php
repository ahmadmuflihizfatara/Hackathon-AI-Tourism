<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\ItineraryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// NusantaraAI — Chat API
Route::post('/gemini', [GeminiController::class, 'chat']);

// NusantaraAI — Itinerary API
Route::post('/itinerary/generate', [ItineraryController::class, 'generate']);
