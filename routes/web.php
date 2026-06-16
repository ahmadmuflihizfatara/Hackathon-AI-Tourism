<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — NusantaraAI Tourism Planner
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', [PageController::class, 'landing'])->name('landing');

// Dashboard (two-column itinerary view)
Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
