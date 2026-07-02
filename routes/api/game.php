<?php

use App\Http\Controllers\Api\v2\Game\GameController;
use Illuminate\Support\Facades\Route;

Route::prefix('app')->middleware(['app'])->group(function () {
    Route::prefix('game')->group(function () {

        // Public routes
        Route::get('/daily-word', [GameController::class, 'daily_word']);
        Route::get('/quiz/{lesson_id}', [GameController::class, 'quiz']);
        Route::get('/leaderboard', [GameController::class, 'leaderboard']);

        // Auth required
        Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
            Route::post('/xp', [GameController::class, 'add_xp']);
            Route::get('/streak', [GameController::class, 'streak']);
            Route::post('/streak/update', [GameController::class, 'update_streak']);
        });
    });
});
