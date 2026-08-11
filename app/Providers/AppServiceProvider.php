<?php

namespace App\Providers;

use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\School\SchoolBundle\SchoolBundle;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        JsonResource::withoutWrapping();

        $this->bootLocale();
        $this->bootInertiaSharedData();
        $this->bootMorphMap();
        $this->bootDatabaseQueryLogging();
    }

    /**
     * Логирование SQL-запросов для анализа производительности.
     *
     * Работает только в local-окружении,
     * чтобы не создавать дополнительную нагрузку
     * на production.
     */
    private function bootDatabaseQueryLogging(): void
    {
        if (!App::environment('local')) {
            return;
        }

        DB::listen(function (QueryExecuted $query) {
            Log::debug('SQL QUERY', [
                'time_ms' => $query->time,
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'connection' => $query->connectionName,
            ]);
        });
    }

    private function bootLocale(): void
    {
        $availableLocales = config('app.available_locales', ['ru']);
        $locale = LaravelLocalization::getCurrentLocale() ?: config('app.locale');

        if (!in_array($locale, $availableLocales, true)) {
            $locale = config('app.fallback_locale', 'ru');
        }

        App::setLocale($locale);
    }

    private function bootInertiaSharedData(): void
    {
        Inertia::share([
            'locale' => fn () => App::getLocale(),

            'availableLocales' => fn () =>
            config('app.available_locales', ['ru']),

            'canLogin' => fn () =>
            Route::has('login'),

            'canRegister' => fn () =>
            Route::has('register'),

            'laravelLang' => fn () => [
                'admin' => [
                    'welcome' => trans('admin/welcome'),
                ],

                'public' => [
                    'privacy' => trans('public/privacy'),
                ],
            ],
        ]);
    }

    private function bootMorphMap(): void
    {
        Relation::morphMap([

            // Блог
            'blog_article' => BlogArticle::class,
            'blog_video'   => BlogVideo::class,

            // Онлайн школа
            'school_course' => SchoolCourse::class,
            'school_lesson' => SchoolLesson::class,
            'school_bundle' => SchoolBundle::class,

            // Маркетплейс
            'market_product' => MarketProduct::class,

        ]);
    }
}
