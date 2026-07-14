<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SuperAdmin\BookController;
use App\Http\Controllers\SuperAdmin\ChapterController;
use App\Http\Controllers\SuperAdmin\LessonController;
use App\Http\Controllers\SuperAdmin\NoticeController;
use App\Http\Controllers\SuperAdmin\SAdminController;
use App\Http\Controllers\SuperAdmin\WordController;
use App\Http\Controllers\SuperAdmin\NotificationLogController;
use App\Http\Controllers\SuperAdmin\WizardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/course-enroll', [HomeController::class, 'front_course']);
Route::get('/our-course', [HomeController::class, 'front_course'])->name('front.course');

Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified'])->group(function () {

    Route::get('/dashboard', function () {
        if (Auth::user()->user_type == 1) {
            return redirect('superadmin');
        }else {
            return redirect('admin');
        }
    })->name('dashboard');

    // Superadmin & admin start
    Route::get('superadmin/slug', [SAdminController::class, 'superadmin_slug']);
    Route::get('/clear-cash', [HomeController::class, 'clear_cash']);
    // Route::get('/old-teacher', [HomeController::class, 'old_teacher']);
    Route::get('/superadmin', [HomeController::class, 'superadmin'])->name('superadmin');

    Route::get('/adminlist', [SAdminController::class, 'adminlist'])->name('adminlist');
    Route::get('/alluser', [SAdminController::class, 'alluser'])->name('alluser');
    Route::get('students', [SAdminController::class, 'admin_approval_students'])->name('admin.approval.students');
    Route::get('approved/teacher', [SAdminController::class, 'admin_approval_teacher_index'])->name('admin.approval.teacher.index');
    Route::get('pending/teacher', [SAdminController::class, 'admin_approval_teacher_pending'])->name('admin.approval.teacher.pending');
    Route::get('unapproved/teacher', [SAdminController::class, 'admin_approval_teacher_unapproved'])->name('admin.approval.teacher.unapproved');
    Route::get('teacher/edit/{id}/{type}', [SAdminController::class, 'admin_approval_teacher_edit'])->name('admin.approval.teacher.edit');
    Route::put('teacher/approve/{id}', [SAdminController::class, 'admin_approval_teacher_approve'])->name('admin.approval.teacher.approve');
    Route::get('/adminlist/{id}', [SAdminController::class, 'adminview']);
    Route::delete('/adminlist/{id}', [SAdminController::class, 'admindelete']);
    Route::post('/createadmin', [SAdminController::class, 'createadmin'])->name('createadmin');

    Route::get('/app-version', [SAdminController::class, 'app_version'])->name('app-version');
    Route::post('/app-version', [SAdminController::class, 'app_version_store'])->name('app-version.store');

    // vocabulary Panel Super Admin
    Route::prefix('vocabulary')->group(function () {
        Route::resource('chapters', ChapterController::class);
        Route::resource('chapters/lessons', LessonController::class);
        Route::get('chapters/lessons/create/{id}', [LessonController::class, 'chapters_lessons_create'])->name('chapters.lessons.create');
        Route::post('chapters/lessons/{id}/toggle-premium', [LessonController::class, 'togglePremium'])->name('lessons.toggle-premium');
        Route::resource('chapters/lessons/words', WordController::class);
        Route::get('chapters/lessons/words/create/{id}', [WordController::class, 'chapters_lessons_words_create'])->name('chapters.lessons.words.create');
    });

    // Page Super Admin
    Route::resource('notices', NoticeController::class);
    Route::prefix('page')->group(function () {
        Route::get('create', [SAdminController::class, 'page_create'])->name('page.create');
        Route::post('store', [SAdminController::class, 'page_store'])->name('page.store');
        Route::get('show/{id}', [SAdminController::class, 'page_show'])->name('page.show');
        Route::get('edit/{id}', [SAdminController::class, 'page_edit'])->name('page.edit');
        Route::put('update/{id}', [SAdminController::class, 'page_update'])->name('page.update');
        Route::delete('delete/{id}', [SAdminController::class, 'page_delete'])->name('page.delete');
    });
    // Bolg Panel Super Admin
    Route::prefix('book')->group(function () {
        Route::get('index', [BookController::class, 'book_index'])->name('book.index');
        Route::post('store', [BookController::class, 'book_store'])->name('book.store');
        Route::get('edit/{slug}', [BookController::class, 'book_edit'])->name('book.edit');
        Route::put('update/{id}', [BookController::class, 'book_update'])->name('book.update');
        // Route::delete('delete/{id}', [BookController::class, 'book_delete'])->name('book.delete');

        Route::get('sections/{slug}', [BookController::class, 'book_sections'])->name('book.sections');

        Route::prefix('chapter')->group(function () {
            Route::get('create/{slug}', [BookController::class, 'chapter_index'])->name('chapter.index');
            Route::post('store', [BookController::class, 'chapter_store'])->name('chapter.store');
            Route::get('edit/{slug}', [BookController::class, 'chapter_edit'])->name('chapter.edit');
            Route::put('update/{id}', [BookController::class, 'chapter_update'])->name('chapter.update');
            Route::get('delete/{id}', [BookController::class, 'chapter_delete'])->name('chapter.delete');
            Route::post('update-type/{id}', [BookController::class, 'chapter_update_type'])->name('chapter.update.type');
        });

        Route::prefix('item')->group(function () {
            Route::get('create/{slug}', [BookController::class, 'item_index'])->name('item.index');
            Route::get('send/notification/{slug}', [BookController::class, 'item_notification'])->name('item.notification');
            Route::post('send/notification', [BookController::class, 'item_notification_store'])->name('item.notification.store');
            Route::post('store', [BookController::class, 'item_store'])->name('item.store');
            Route::get('edit/{slug}', [BookController::class, 'item_edit'])->name('item.edit');
            Route::get('show/{slug}', [BookController::class, 'item_show'])->name('item.show');
            Route::put('update/{id}', [BookController::class, 'item_update'])->name('item.update');
            Route::get('delete/{id}', [BookController::class, 'item_delete'])->name('item.delete');
        });
    });

    // Wizard (odd-true-stories) Panel Super Admin
    Route::prefix('wizard')->group(function () {
        Route::get('chapters', [WizardController::class, 'chapter_index'])->name('wizard.chapter.index');
        Route::post('chapter/store', [WizardController::class, 'chapter_store'])->name('wizard.chapter.store');
        Route::get('chapter/edit/{id}', [WizardController::class, 'chapter_edit'])->name('wizard.chapter.edit');
        Route::put('chapter/update/{id}', [WizardController::class, 'chapter_update'])->name('wizard.chapter.update');
        Route::get('chapter/delete/{id}', [WizardController::class, 'chapter_delete'])->name('wizard.chapter.delete');

        Route::get('stories/{chapter}', [WizardController::class, 'story_index'])->name('wizard.story.index');
        Route::post('story/store', [WizardController::class, 'story_store'])->name('wizard.story.store');
        Route::get('story/edit/{id}', [WizardController::class, 'story_edit'])->name('wizard.story.edit');
        Route::put('story/update/{id}', [WizardController::class, 'story_update'])->name('wizard.story.update');
        Route::get('story/delete/{id}', [WizardController::class, 'story_delete'])->name('wizard.story.delete');
    });

    Route::get('notification-logs', [NotificationLogController::class, 'index'])->name('notification.logs');
    Route::get('/notification', [HomeController::class, 'all_notification']);
    Route::put('/notification/{id}', [HomeController::class, 'read_notification']);
    Route::get('/supportlist', [HomeController::class, 'supportlist']);
    Route::get('/support', [HomeController::class, 'support']);
    Route::get('/supportcreate', [HomeController::class, 'support_create']);
    Route::post('/support', [HomeController::class, 'support_store']);
    Route::get('/supportreplay/{id}', [HomeController::class, 'support_replay_create']);
    Route::post('/supportreplay/{id}', [HomeController::class, 'support_replay_store']);
    // Superadmin & admin end

});
