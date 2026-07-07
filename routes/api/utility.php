<?php

use App\Http\Controllers\Api\v2\Utility\ApiShahidController;
use App\Http\Controllers\Api\v2\Utility\ApiUtilityController;
use App\Models\Word;
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

// TEMP export — delete after use
Route::get('/tmp-words-export-x9k2', function () {
    return response()->json(Word::select('id', 'word', 'meaning', 'synonyms', 'antonyms')->orderBy('id')->get());
});

Route::prefix('app')->middleware(['app'])->group(function () {

	Route::get('/slider', [ApiUtilityController::class, 'slider']);
	Route::get('/blogs', [ApiUtilityController::class, 'all_blog']);
	Route::get('/blog/{slug}', [ApiUtilityController::class, 'blog_item_slug']);
	Route::get('/shahids', [ApiShahidController::class, 'shahid_index']);
	Route::get('/shahid/{slug}', [ApiShahidController::class, 'shahid_slug']);

	Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function (){
        Route::get('/all-class', [ApiUtilityController::class, 'all_class']);
        Route::get('/class', [ApiUtilityController::class, 'single_class']);
        Route::get('/all-country', [ApiUtilityController::class, 'all_country']);
        Route::get('/country', [ApiUtilityController::class, 'single_country']);
        Route::get('/division/{id}', [ApiUtilityController::class, 'division']);
        Route::get('/division', [ApiUtilityController::class, 'single_division']);
        Route::get('/city/{id}', [ApiUtilityController::class, 'city']);
        Route::get('/city', [ApiUtilityController::class, 'single_city']);
        Route::get('/upazila/{id}', [ApiUtilityController::class, 'upazila']);
        Route::get('/upazila', [ApiUtilityController::class, 'single_upazila']);
    });
});
