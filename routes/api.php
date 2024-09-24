<?php

use App\Http\Controllers\HangmanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('hangman/{indice}/{jugar?}', [HangmanController::class, 'palabra'])
->where('palabra', '[0-9]+');