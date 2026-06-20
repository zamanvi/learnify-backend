<?php

namespace App\Providers;

use App\Models\Blog;
use App\Models\Contest;
use App\Models\ContestQuestion;
use App\Models\ModelTestAll;
use App\Models\Notification;
use App\Models\ScholarShip;
use App\Models\Setting;
use App\Models\Shahid;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $view->with([
                // 'settings' => array_to_object(Setting::pluck('value', 'key')->all()),
                'makeEncryption' => Crypt::encrypt('MD NAYEEM SARKER', config('variables.customKey')),
                'makeEncryptionKey' => 'MD NAYEEM SARKER',
                'a_user' => User::where('user_type', '1')->count(),
                'g_user' => User::where('as_user', 'general')->count(),
                't_user' => User::where('as_user', 'teacher')->count(),
                's_user' => User::where('as_user', 'student')->count(),
                'notifications' => Notification::latest()->get(),
                'contest_count' => Contest::count(),
                'modeltest_count' => ModelTestAll::count(),
                'blog_count' => Blog::count(),
                'shahid_count' => Shahid::count(),
                'scholarShip_count' => ScholarShip::count(),
            ]);
            if (Route::is(['contest.index', 'contest.create', 'contest.show', 'contest.edit', 'contest.result'])) {
                $view->with('contests', Contest::paginate(20));
            }
            if (Route::is(['contest.question.index', 'contest.question.create', 'contest.question.show', 'contest.question.edit'])) {
                $view->with('contestQuestions', ContestQuestion::paginate(20));
            }
            if (Route::is(['scholarship.create', 'scholarship.show', 'scholarship.edit'])) {
                $view->with('scholarShips', ScholarShip::paginate(20));
            }
        });
    }
}
