<?php

use App\Http\Controllers\Api\v2\Game\BattleController;
use App\Http\Controllers\Api\v2\Game\GameController;
use App\Http\Controllers\Api\v2\Game\GroupController;
use App\Http\Controllers\Api\v2\Game\LiptoController;
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
        Route::middleware(['auth:sanctum', config('jetstream.auth_session')])->group(function () {
            Route::post('/xp', [GameController::class, 'add_xp']);
            Route::get('/streak', [GameController::class, 'streak']);
            Route::post('/streak/update', [GameController::class, 'update_streak']);

            // Lipto virtual currency
            Route::prefix('lipto')->group(function () {
                Route::get('/balance',   [LiptoController::class, 'balance']);
                Route::post('/earn',     [LiptoController::class, 'earn']);
                Route::post('/spend',    [LiptoController::class, 'spend']);
                Route::post('/transfer', [LiptoController::class, 'transfer']);
            });

            // Group / Room
            Route::prefix('group')->group(function () {
                Route::post('/create',              [GroupController::class, 'create']);
                Route::post('/join',                [GroupController::class, 'join']);
                Route::get('/my',                   [GroupController::class, 'myGroups']);
                Route::get('/{code}/leaderboard',   [GroupController::class, 'leaderboard']);
                Route::delete('/{code}/leave',      [GroupController::class, 'leave']);
            });

            // Battle (async 1v1)
            Route::prefix('battle')->group(function () {
                Route::post('/challenge',  [BattleController::class, 'challenge']);
                Route::get('/pending',     [BattleController::class, 'pending']);
                Route::get('/history',     [BattleController::class, 'history']);
                Route::get('/{id}',        [BattleController::class, 'show']);
                Route::post('/{id}/submit',[BattleController::class, 'submit']);
            });
        });
    });
});
