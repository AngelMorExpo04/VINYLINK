<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SongController;

Route::get('/', [SongController::class, 'index'])->name('dashboard');
Route::post('/songs', [SongController::class, 'store'])->name('songs.store');
Route::delete('/songs/{song}', [SongController::class, 'destroy'])->name('songs.destroy');
