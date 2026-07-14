<?php

use App\Http\Controllers\Api\v2\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('app')->middleware(['app'])->group(function () {
    // Throttled separately (and more tightly) than ordinary read routes -
    // these are exactly the endpoints a brute-force/spam script would hit.
    Route::middleware(['throttle:10,1'])->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forget-password', [AuthController::class, 'forget_password']);
        Route::post('/verify-otp', [AuthController::class, 'verify_otp']);
        Route::post('/confirm-password', [AuthController::class, 'confirm_password_verify']);
    });
    Route::middleware(['auth:sanctum', config('jetstream.auth_session')])->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('refresh-token', [AuthController::class, 'refreshToken']);
    });
});

// Kept at the top-level path (not under /app) so existing callers don't
// break, but now requires the same x-api-key: app header as every other
// mobile-facing endpoint - this was previously reachable by anyone with
// no auth at all, including attacker-controlled `points`.
Route::post('/old_user_create', [AuthController::class, 'old_user_create'])->middleware(['app']);
