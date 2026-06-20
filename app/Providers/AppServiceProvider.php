<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Repositories\ChapterRepository;
use App\Repositories\ChapterRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\LessonRepository;
use App\Repositories\LessonRepositoryInterface;
use App\Repositories\SettingRepository;
use App\Repositories\WordRepository;
use App\Repositories\WordRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the ChapterRepositoryInterface to the ChapterRepository implementation
        $this->app->bind(ChapterRepositoryInterface::class, ChapterRepository::class);

        // Bind the LessonRepositoryInterface to the LessonRepository implementation
        $this->app->bind(LessonRepositoryInterface::class, LessonRepository::class);

        // Bind the WordRepositoryInterface to the WordRepository implementation
        $this->app->bind(WordRepositoryInterface::class, WordRepository::class);

        // Bind SettingRepositoryInterface to SettingRepository
        $this->app->bind(SettingRepositoryInterface::class, SettingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
    }
}
