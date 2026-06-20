<?php

use App\Http\Controllers\Api\v2\Utility\ApiScholarshipController;
use Illuminate\Support\Facades\Route;

Route::prefix('app')->middleware(['app'])->group(function () {
    Route::prefix('scholarship')->group(function () {
        Route::get('/', [ApiScholarshipController::class, 'scholarship']);
        // Route::get('inactive', [ApiScholarshipController::class, 'scholarship_inactive']);
        Route::get('show/{slug}', [ApiScholarshipController::class, 'scholarship_show']);
        Route::get('get-result/{slug}', [ApiScholarshipController::class, 'scholarship_get_result']);
        Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
            Route::get('enroll/{slug}', [ApiScholarshipController::class, 'scholarship_enroll']);
        });
    });
});
