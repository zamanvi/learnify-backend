<?php

use App\Http\Controllers\Api\v2\Utility\ApiWizardController;
use Illuminate\Support\Facades\Route;

Route::prefix('app')->middleware(['app', 'cache.headers:public;max_age=300;etag'])->group(function () {
    Route::prefix('wizard')->group(function () {
        Route::get('chapters', [ApiWizardController::class, 'chapters']);
        Route::get('stories', [ApiWizardController::class, 'stories']);
        Route::get('story/{id}', [ApiWizardController::class, 'story_show']);
    });
});
