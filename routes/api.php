<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SongController;

// Endpoint para el hardware
Route::get('/fetch-song', [SongController::class, 'apiFetch']);
