<?php

use App\Http\Controllers\Api\v2\Utility\ApiBookController;
use App\Http\Controllers\Api\v2\Utility\ApiPageController;
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
    // Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // });

    // List/lookup endpoints have no write side effects, safe to let the
    // client's HTTP cache short-circuit repeat requests.
    Route::middleware('cache.headers:public;max_age=300;etag')->group(function () {
        Route::get('/page', [ApiPageController::class, 'page_find']);
        Route::get('/app-version', [ApiPageController::class, 'appVersion']);
        Route::get('/notices/{app}', [ApiPageController::class, 'appNotices']);
        Route::prefix('book')->group(function () {
            Route::get('index', [ApiBookController::class, 'book_index']);
            Route::get('chapter/index', [ApiBookController::class, 'book_chapter_index']);
            Route::get('chapter/index2', [ApiBookController::class, 'book_chapter_index2']);
            Route::get('item/index', [ApiBookController::class, 'book_item_index']);
        });
    });

    // "show" endpoints increment a pageview counter on every hit - excluded
    // from caching so a client-side cache hit can't silently skip that count.
    Route::prefix('book')->group(function () {
        Route::get('show/{slug}', [ApiBookController::class, 'book_show']);
        Route::get('chapter/show/{slug}', [ApiBookController::class, 'book_chapter_show']);
        Route::get('item/show/{slug}', [ApiBookController::class, 'book_item_show']);
    });
});

