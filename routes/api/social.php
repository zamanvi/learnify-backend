<?php

use App\Http\Controllers\Api\v2\Utility\ApiMessageController;
use App\Http\Controllers\Api\v2\Utility\ApiSocailController;
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
        Route::prefix('friend')->group(function () {
            Route::get('all', [ApiSocailController::class, 'allFriend']);
            Route::get('add/{id}', [ApiSocailController::class, 'addFriend']);
            Route::get('remove/{id}', [ApiSocailController::class, 'removeFriend']);
            Route::get('unfriend/{id}', [ApiSocailController::class, 'unfriendFriend']);
            Route::get('confirm/{id}', [ApiSocailController::class, 'confirmFriend']);
            Route::get('send', [ApiSocailController::class, 'sendFriend']);
            Route::get('received', [ApiSocailController::class, 'receivedFriend']);
        });
        Route::prefix('message')->group(function () {
            Route::get('list', [ApiMessageController::class, 'messageList']);
            Route::post('make/{id}', [ApiMessageController::class, 'makeMessage']);
            Route::get('show/{id}', [ApiMessageController::class, 'showMessage']);
            Route::post('send/{id}', [ApiMessageController::class, 'sendMessage']);
            Route::post('edit/{chat_id}/{message_id}', [ApiMessageController::class, 'editMessage']);
            Route::get('unsend/{chat_id}/{message_id}', [ApiMessageController::class, 'unsendMessage']);
            Route::get('remove/{chat_id}/{message_id}', [ApiMessageController::class, 'removeMessage']);
            Route::delete('delete/{chat_id}/{message_id}', [ApiMessageController::class, 'deleteMessage']);
        });
    });
});
