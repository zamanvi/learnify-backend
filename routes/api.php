<?php

use App\Http\Controllers\SuperAdmin\ChapterController;
use App\Http\Controllers\SuperAdmin\LessonController;
use App\Http\Controllers\SuperAdmin\WordController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('grammer')->middleware(['grammer'])->group(function () {
    Route::get('/initial', [ChapterController::class, 'initial']);
    Route::get('/chapters', [ChapterController::class, 'index']);
    Route::get('/chapter/show/{id}', [ChapterController::class, 'show']);
    Route::get('/lessons/{id}', [LessonController::class, 'chapters_lessons_create']);
    Route::get('/lesson/show/{id}', [LessonController::class, 'show']);
    Route::get('/words/{id}', [WordController::class, 'chapters_lessons_words_create']);
    Route::get('/word/show/{id}', [WordController::class, 'show']);
});

Route::prefix('app')->middleware(['app'])->group(function () {
    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
        // Route::post('/logout', [ApiController::class, 'logout']);

        // Route::get('/find-friend', [ApiController::class, 'find_friend']);
        // Route::get('/my-friend', [ApiController::class, 'friends']);
        // Route::post('/add-friend', [ApiController::class, 'add_friend']);
        // Route::post('/cancel-request', [ApiController::class, 'cancel_request']);
        // Route::post('/confirm-request', [ApiController::class, 'confirmrequest']);
        // Route::post('/unfriend', [ApiController::class, 'unfriend']);
        // Route::get('/allrequest', [ApiController::class, 'allrequest']);
        // Route::get('/allsend', [ApiController::class, 'allsend']);

        // Route::post('/makemessage', [ApiController::class, 'makemessage']);
        // Route::get('/chats', [ApiController::class, 'chats']);
        // Route::get('/chatmessage/{id}', [ApiController::class, 'chatmessage']);
        // Route::post('/chatmessage', [ApiController::class, 'chatmessage_store']);
        // Route::post('/sendpoints', [ApiController::class, 'sendpoints']);
        // Route::post('/sendcard', [ApiController::class, 'sendcard']);
        // Route::get('/pointhistory', [ApiController::class, 'pointhistory']);

        // require __DIR__.'/api/contest.php';

    });
});
