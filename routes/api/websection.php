<?php

use App\Http\Controllers\Api\v2\Utility\ApiWebSectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Website ("Book 2" / masterenglishbook.com) Section API
|--------------------------------------------------------------------------
|
| Public, read-only. Mirrors routes/api/book.php's structure/middleware so
| the frontend authenticates the same way (x-api-key: app or public_key).
| Reads exclusively from Section/WebChapter/WebLesson - see
| ApiWebSectionController's docblock for why this stays separate from
| routes/api/book.php.
|
*/

Route::prefix('app')->middleware(['app'])->group(function () {
    Route::middleware('cache.headers:public;max_age=300;etag')->group(function () {
        Route::prefix('websection')->group(function () {
            Route::get('index', [ApiWebSectionController::class, 'section_index']);
            Route::get('chapter/index', [ApiWebSectionController::class, 'chapter_index']);
            Route::get('lesson/index', [ApiWebSectionController::class, 'lesson_index']);
        });
    });

    // "show" endpoints are excluded from caching, matching book.php's own
    // reasoning - if a pageview counter gets added to these later, a cached
    // response must not silently skip it.
    Route::prefix('websection')->group(function () {
        Route::get('chapter/show/{slug}', [ApiWebSectionController::class, 'chapter_show']);
        Route::get('lesson/show/{slug}', [ApiWebSectionController::class, 'lesson_show']);
    });
});
