<?php

use App\Http\Controllers\Api\v2\Utility\ApiProfileController;
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

// gender, subject, location, instuite, search_text

Route::prefix('app')->middleware(['app'])->group(function () {
    Route::prefix('teacher')->group(function () {
        Route::get('all', [ApiProfileController::class, 'all_teacher']);
        Route::get('search', [ApiProfileController::class, 'search_teacher']);
    });
    Route::get('teacher/{slug}', [ApiProfileController::class, 'teacher_profile']);
    Route::get('public/{id}', [ApiProfileController::class, 'public_profile']);
    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('all', [ApiProfileController::class, 'all_user']);
            Route::get('all-student', [ApiProfileController::class, 'all_student']);
            Route::get('single/{id}', [ApiProfileController::class, 'single_user']);
            Route::get('search', [ApiProfileController::class, 'search_user']);
        });
        Route::prefix('profile')->group(function () {
            Route::get('my', [ApiProfileController::class, 'my_profile']);
            Route::post('update', [ApiProfileController::class, 'update_info']);
            Route::post('change-url', [ApiProfileController::class, 'change_url']);
            Route::post('delete', [ApiProfileController::class, 'delete_user']);
        });
        Route::prefix('switch')->group(function () {
            Route::post('as-teacher', [ApiProfileController::class, 'switch_as_teacher']);
            Route::post('as-student', [ApiProfileController::class, 'switch_as_student']);
            // Route::post('as-both', [ApiProfileController::class, 'switch_as_both']);
        });
    });
});
