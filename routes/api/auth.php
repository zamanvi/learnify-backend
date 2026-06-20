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
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forget-password', [AuthController::class, 'forget_password']);
    Route::post('/confirm-password', [AuthController::class, 'confirm_password_verify']);
    // Route::post('/forget-password', [AuthController::class, 'forget_password']);
    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('refresh-token', [AuthController::class, 'refreshToken']);
    });
});
Route::post('/old_user_create', [AuthController::class, 'old_user_create']);
