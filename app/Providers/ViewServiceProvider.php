<?php

namespace App\Providers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
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
                'makeEncryption' => Crypt::encrypt('MD NAYEEM SARKER', config('variables.customKey')),
                'makeEncryptionKey' => 'MD NAYEEM SARKER',
                'a_user' => User::where('user_type', '1')->count(),
                'g_user' => User::where('as_user', 'general')->count(),
                't_user' => User::where('as_user', 'teacher')->count(),
                's_user' => User::where('as_user', 'student')->count(),
                'notifications' => Notification::latest()->get(),
            ]);
        });
    }
}
