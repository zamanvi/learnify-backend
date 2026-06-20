<?php

use App\Http\Controllers\ContestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModelTestController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShahidController;
use App\Http\Controllers\SuperAdmin\BookController;
use App\Http\Controllers\SuperAdmin\ChapterController;
use App\Http\Controllers\SuperAdmin\LessonController;
use App\Http\Controllers\SuperAdmin\NoticeController;
use App\Http\Controllers\SuperAdmin\SAdminController;
use App\Http\Controllers\SuperAdmin\WordController;

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
Route::get('/aboutus', [HomeController::class, 'front_about'])->name('front.about');
Route::get('/contactus', [HomeController::class, 'front_contact'])->name('front.contact');
Route::get('/our-course', [HomeController::class, 'front_course'])->name('front.course');
Route::get('/our-blog', [HomeController::class, 'front_blog'])->name('front.blog');

Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified'])->group(function () {

    Route::get('/dashboard', function () {
        if (Auth::user()->user_type == 1) {
            return redirect('superadmin');
        }else {
            return redirect('admin');
        }
    })->name('dashboard');

    // require __DIR__.'/contest.php';

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
    Route::get('/slider', [SAdminController::class, 'slider']);
    Route::post('/slider', [SAdminController::class, 'slider_store'])->name('slider.store');

    Route::get('/app-version', [SAdminController::class, 'app_version'])->name('app-version');
    Route::post('/app-version', [SAdminController::class, 'app_version_store'])->name('app-version.store');

    // Bolg Panel Super Admin
    Route::prefix('blog')->group(function () {
        Route::get('create', [SAdminController::class, 'blog_create'])->name('blog.create');
        Route::post('store', [SAdminController::class, 'blog_store'])->name('blog.store');
        Route::get('show/{slug}', [SAdminController::class, 'blog_show'])->name('blog.show');
        Route::get('edit/{slug}', [SAdminController::class, 'blog_edit'])->name('blog.edit');
        Route::put('update/{id}', [SAdminController::class, 'blog_update'])->name('blog.update');
        Route::get('delete/{id}', [SAdminController::class, 'blog_delete'])->name('blog.delete');
    });

    // Shahid Panel Super Admin
    Route::prefix('shahid')->group(function () {
        Route::get('create', [ShahidController::class, 'shahid_create'])->name('shahid.create');
        Route::post('store', [ShahidController::class, 'shahid_store'])->name('shahid.store');
        Route::get('show/{slug}', [ShahidController::class, 'shahid_show'])->name('shahid.show');
        Route::get('edit/{slug}', [ShahidController::class, 'shahid_edit'])->name('shahid.edit');
        Route::put('update/{id}', [ShahidController::class, 'shahid_update'])->name('shahid.update');
        Route::get('delete/{id}', [ShahidController::class, 'shahid_delete'])->name('shahid.delete');
    });

    // vocabulary Panel Super Admin
    Route::prefix('vocabulary')->group(function () {
        Route::resource('chapters', ChapterController::class);
        Route::resource('chapters/lessons', LessonController::class);
        Route::get('chapters/lessons/create/{id}', [LessonController::class, 'chapters_lessons_create'])->name('chapters.lessons.create');
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
    // Scholleship Panel Super Admin
    Route::prefix('scholarship')->group(function () {
        Route::get('create', [SAdminController::class, 'scholarship_create'])->name('scholarship.create');
        Route::post('store', [SAdminController::class, 'scholarship_store'])->name('scholarship.store');
        Route::get('show/{slug}', [SAdminController::class, 'scholarship_show'])->name('scholarship.show');
        Route::get('edit/{slug}', [SAdminController::class, 'scholarship_edit'])->name('scholarship.edit');
        Route::put('update/{id}', [SAdminController::class, 'scholarship_update'])->name('scholarship.update');
        Route::get('delete/{slug}', [SAdminController::class, 'scholarship_delete'])->name('scholarship.delete');
        Route::get('publish/{slug}', [SAdminController::class, 'scholarship_publish'])->name('scholarship.publish');
        Route::get('participant/{slug}', [SAdminController::class, 'scholarship_participant'])->name('scholarship.participant');
        Route::get('result/{slug}', [SAdminController::class, 'scholarship_result'])->name('scholarship.result');
    });

    // Bolg Panel Super Admin
    Route::prefix('book')->group(function () {
        Route::get('index', [BookController::class, 'book_index'])->name('book.index');
        Route::post('store', [BookController::class, 'book_store'])->name('book.store');
        Route::get('edit/{slug}', [BookController::class, 'book_edit'])->name('book.edit');
        Route::put('update/{id}', [BookController::class, 'book_update'])->name('book.update');
        // Route::delete('delete/{id}', [BookController::class, 'book_delete'])->name('book.delete');

        Route::prefix('chapter')->group(function () {
            Route::get('create/{slug}', [BookController::class, 'chapter_index'])->name('chapter.index');
            Route::post('store', [BookController::class, 'chapter_store'])->name('chapter.store');
            Route::get('edit/{slug}', [BookController::class, 'chapter_edit'])->name('chapter.edit');
            Route::put('update/{id}', [BookController::class, 'chapter_update'])->name('chapter.update');
            Route::get('delete/{id}', [BookController::class, 'chapter_delete'])->name('chapter.delete');
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

    // contest
    Route::prefix('contest')->group(function () {
        Route::get('all', [ContestController::class, 'contest_index'])->name('contest.index');
        Route::get('create', [ContestController::class, 'contest_create'])->name('contest.create');
        Route::post('store', [ContestController::class, 'contest_store'])->name('contest.store');
        Route::get('show/{slug}', [ContestController::class, 'contest_show'])->name('contest.show');
        Route::get('edit/{slug}', [ContestController::class, 'contest_edit'])->name('contest.edit');
        Route::put('update/{id}', [ContestController::class, 'contest_update'])->name('contest.update');
        Route::get('delete/{id}', [ContestController::class, 'contest_delete'])->name('contest.delete');

        Route::get('result/{slug}', [ContestController::class, 'contest_result'])->name('contest.result');
        Route::get('participant/{slug}', [ContestController::class, 'contest_participant'])->name('contest.participant');

        Route::prefix('question')->group(function () {
            Route::get('all/{slug}', [ContestController::class, 'contest_question_index'])->name('contest.question.index');
            Route::get('create/{slug}', [ContestController::class, 'contest_question_create'])->name('contest.question.create');
            Route::post('store', [ContestController::class, 'contest_question_store'])->name('contest.question.store');
            Route::get('show/{id}', [ContestController::class, 'contest_question_show'])->name('contest.question.show');
            Route::get('edit/{id}', [ContestController::class, 'contest_question_edit'])->name('contest.question.edit');
            Route::put('update/{id}', [ContestController::class, 'contest_question_update'])->name('contest.question.update');
            Route::get('delete/{id}', [ContestController::class, 'contest_question_delete'])->name('contest.question.delete');
        });
    });

    Route::prefix('modeltest')->group(function () {
        Route::get('all', [ModelTestController::class, 'modeltest_index'])->name('modeltest.index');
        Route::get('create', [ModelTestController::class, 'modeltest_create'])->name('modeltest.create');
        Route::get('show/{id}', [ModelTestController::class, 'modeltest_show'])->name('modeltest.show');
        Route::get('edit/{id}', [ModelTestController::class, 'modeltest_edit'])->name('modeltest.edit');
        Route::delete('delete/{id}', [ModelTestController::class, 'modeltest_delete'])->name('modeltest.delete');
        Route::post('store', [ModelTestController::class, 'modeltest_store'])->name('modeltest.store');
        Route::put('update/{id}', [ModelTestController::class, 'modeltest_update'])->name('modeltest.update');
        Route::get('result', [SAdminController::class, 'modeltest_result'])->name('modeltest.result');

        Route::prefix('syllabus')->group(function () {
            Route::get('create/{id}', [ModelTestController::class, 'm_syllabus_create'])->name('msyllabus.create');
            Route::get('show/{id}', [ModelTestController::class, 'msyllabus_show'])->name('msyllabus.show');
            Route::get('edit/{id}', [ModelTestController::class, 'msyllabus_edit'])->name('msyllabus.edit');
            Route::post('store', [ModelTestController::class, 'msyllabus_store'])->name('msyllabus.store');
            Route::put('update/{id}', [ModelTestController::class, 'msyllabus_update'])->name('msyllabus.update');
            Route::delete('delete/{id}', [ModelTestController::class, 'msyllabus_delete'])->name('msyllabus.delete');
        });
        Route::prefix('question')->group(function () {
            Route::get('all', [ModelTestController::class, 'mquestion_index'])->name('mquestion.index');
            Route::get('create/{id}', [ModelTestController::class, 'mquestion_create'])->name('mquestion.create');
            Route::post('store', [ModelTestController::class, 'mquestion_store'])->name('mquestion.store');
            Route::delete('delete/{id}', [ModelTestController::class, 'mquestion_delete'])->name('mquestion.delete');
        });
    });
    // user

    // admin
    // country
    Route::get('/countries', [PageController::class, 'country_index'])->name('country.index');
    Route::post('/country', [PageController::class, 'country_store'])->name('country.store');
    Route::put('/country/active/{id}', [PageController::class, 'country_active'])->name('country.active');
    Route::put('/country/inactive/{id}', [PageController::class, 'country_inactive'])->name('country.inactive');
    Route::get('/country/edit/{id}', [PageController::class, 'country_edit'])->name('country.edit');
    Route::put('/country/update/{id}', [PageController::class, 'country_update'])->name('country.update');
    Route::delete('/country/delete/{id}', [PageController::class, 'country_delete'])->name('country.delete');

    Route::get('/divisions/{id}', [PageController::class, 'division_index'])->name('division.index');
    Route::get('/division/create/{id}', [PageController::class, 'division_create'])->name('division.create');
    Route::post('/division', [PageController::class, 'division_store'])->name('division.store');
    Route::get('/division/edit/{id}', [PageController::class, 'division_edit'])->name('division.edit');
    Route::put('/division/update/{id}', [PageController::class, 'division_update'])->name('division.update');
    Route::delete('/division/delete/{id}', [PageController::class, 'division_delete'])->name('division.delete');

    Route::get('/cities/{id}', [PageController::class, 'city_index'])->name('city.index');
    Route::get('/city/create/{id}', [PageController::class, 'city_create'])->name('city.create');
    Route::post('/city/store', [PageController::class, 'city_store'])->name('city.store');
    Route::get('/city/edit/{id}', [PageController::class, 'city_edit'])->name('city.edit');
    Route::put('/city/update/{id}', [PageController::class, 'city_update'])->name('city.update');
    Route::delete('/city/delete/{id}', [PageController::class, 'city_delete'])->name('city.delete');

    Route::get('/upazilas/{id}', [PageController::class, 'upazila_index'])->name('upazila.index');
    Route::get('/upazila/create/{id}', [PageController::class, 'upazila_create'])->name('upazila.create');
    Route::post('/upazila/store', [PageController::class, 'upazila_store'])->name('upazila.store');
    Route::get('/upazila/edit/{id}', [PageController::class, 'upazila_edit'])->name('upazila.edit');
    Route::put('/upazila/update/{id}', [PageController::class, 'upazila_update'])->name('upazila.update');
    Route::delete('/upazila/delete/{id}', [PageController::class, 'upazila_delete'])->name('upazila.delete');

    Route::get('/allclass', [PageController::class, 'allclass']);
    Route::post('/class', [PageController::class, 'class']);
    Route::post('/class', [PageController::class, 'class']);

    Route::get('/notification', [HomeController::class, 'all_notification']);
    Route::put('/notification/{id}', [HomeController::class, 'read_notification']);
    Route::get('/supportlist', [HomeController::class, 'supportlist']);
    Route::get('/support', [HomeController::class, 'support']);
    Route::get('/supportcreate', [HomeController::class, 'support_create']);
    Route::post('/support', [HomeController::class, 'support_store']);
    Route::get('/supportreplay/{id}', [HomeController::class, 'support_replay_create']);
    Route::post('/supportreplay/{id}', [HomeController::class, 'support_replay_store']);
    // Superadmin & admin end

    Route::get('/find-friend', [ProfileController::class, 'find_friend']);
    Route::get('/my-friend', [ProfileController::class, 'friends']);
    Route::get('/add-friend/{id}', [ProfileController::class, 'add_friend']);
    Route::get('/unfriend/{id}', [ProfileController::class, 'unfriend']);
    Route::get('/cancelrequest/{id}', [ProfileController::class, 'cancelrequest']);
    Route::get('/confirmrequest/{id}', [ProfileController::class, 'confirmrequest']);
    Route::get('/allrequest', [ProfileController::class, 'allrequest']);
    Route::get('/allsend', [ProfileController::class, 'allsend']);

    Route::post('/makemessage/{id}', [ProfileController::class, 'makemessage']);
    Route::get('/chat', [ProfileController::class, 'chat']);
    Route::get('/chatmessage/{id}', [ProfileController::class, 'chatmessage']);
    Route::POST('/chatmessage/{id}', [ProfileController::class, 'chatmessage_store']);
    Route::POST('/sendpoints/{id}', [ProfileController::class, 'sendpoints']);
    Route::POST('/sendcard/{id}', [ProfileController::class, 'sendcard']);



});
