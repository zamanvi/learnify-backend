<?php

use App\Http\Controllers\Api\v2\Game\GameController;
use Illuminate\Support\Facades\Route;

// Public game routes - use grammer middleware (x-api-key: app)
Route::prefix('game')->middleware(['grammer', 'throttle:60,1'])->group(function () {
    Route::get('/daily-word', [GameController::class, 'daily_word']);
    Route::get('/quiz/{lesson_id}', [GameController::class, 'quiz']);
    Route::get('/leaderboard', [GameController::class, 'leaderboard']);
});

// Auth-required game routes - use app middleware (encrypted key) + sanctum
Route::prefix('app')->middleware(['app'])->group(function () {
    Route::prefix('game')->group(function () {
        Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
            Route::post('/xp', [GameController::class, 'add_xp']);
            Route::get('/streak', [GameController::class, 'streak']);
            Route::post('/streak/update', [GameController::class, 'update_streak']);
        });
    });
});
