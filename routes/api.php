<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\DestinationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// NusantaraAI — Chat API
Route::post('/gemini', [GeminiController::class, 'chat']);

// NusantaraAI — Itinerary API
Route::post('/itinerary/generate', [ItineraryController::class, 'generate']);

// NusantaraAI — Destination Images & Metadata API
Route::get('/destinations/image', [DestinationController::class, 'getImage']);
Route::post('/destinations/images', [DestinationController::class, 'getBatchImages']);
