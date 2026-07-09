<?php

use App\Http\Controllers\Api\v2\Utility\ApiContestController;
use Illuminate\Support\Facades\Route;

Route::prefix('app')->middleware(['app'])->group(function () {
    Route::prefix('contest')->group(function () {
        Route::get('/', [ApiContestController::class, 'contest']);
        Route::get('show/{slug}', [ApiContestController::class, 'contest_show']);
        Route::get('get-result/{slug}', [ApiContestController::class, 'contest_get_result']);
        Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
            Route::get('my', [ApiContestController::class, 'contest_my']);
            Route::get('enroll/{slug}', [ApiContestController::class, 'contest_enroll']);
            Route::get('take-exam/{slug}', [ApiContestController::class, 'contest_take_exam']);
            Route::post('submit-exam-for-result', [ApiContestController::class, 'contest_submit_exam_for_result']);
            Route::get('get-single-result/{slug}', [ApiContestController::class, 'contest_get_single_result']);
        });
    });
});
