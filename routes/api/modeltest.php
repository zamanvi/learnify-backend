<?php

use App\Http\Controllers\Api\v1\ApiController\ApiController;
use App\Http\Controllers\Api\v2\Utility\ApiEventController;
use App\Http\Controllers\Api\v2\Utility\ApiModeltestController;
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
    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
        Route::prefix('modeltest')->group(function () {
            Route::get('all', [ApiModeltestController::class, 'all_modeltest']);
            Route::get('my', [ApiModeltestController::class, 'my_modeltest']);
            Route::get('syllabus/{id}', [ApiModeltestController::class, 'modeltest_syllabus']);
            Route::get('take-exam/{id}', [ApiModeltestController::class, 'modeltest_take_exam']);
            Route::post('submit-exam-for-result', [ApiModeltestController::class, 'modeltest_submit_exam_for_result']);
            Route::get('get-result/{id}', [ApiModeltestController::class, 'modeltest_get_result']);
        });
    });
});
