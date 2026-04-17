<?php

namespace App\Providers;

use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Banner\Banner;
use App\Models\Admin\Blog\Comment\Comment;
use App\Models\Admin\Blog\Rubric\Rubric;
use App\Models\Admin\Blog\Tag\Tag;
use App\Models\Admin\Blog\Video\Video;
use App\Models\Admin\Finance\SubscriptionPlan\SubscriptionPlan;
use App\Models\Admin\School\Bundle\Bundle;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\LearningCategory\LearningCategory;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use App\Models\Admin\System\Setting\Setting;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        /**
         * 1) Определение и валидация локали.
         */
        $availableLocales = config('app.available_locales', ['ru']);
        $locale = LaravelLocalization::getCurrentLocale() ?: config('app.locale');

        if (!in_array($locale, $availableLocales, true)) {
            $locale = config('app.fallback_locale', 'ru');
        }

        if ($locale !== App::getLocale()) {
            App::setLocale($locale);
        }

        /**
         * 2) Настройки из БД — только если таблица существует.
         */
        $settings = [];

        if (Schema::hasTable('settings')) {
            $settings = Cache::remember('site_settings_all', 3600, function () {
                return Setting::pluck('value', 'option')->toArray();
            });
        }

        config(['site_settings' => $settings]);

        /**
         * 3) Общие данные для Inertia.
         */
        Inertia::share([
            'siteSettings' => fn () => config('site_settings', []),

            'locale' => fn () => App::getLocale(),

            'availableLocales' => fn () => config('app.available_locales', ['ru']),

            'admin' => fn () => (
                Schema::hasTable('rubrics') &&
                Schema::hasTable('articles') &&
                Schema::hasTable('tags') &&
                Schema::hasTable('banners') &&
                Schema::hasTable('videos') &&
                Schema::hasTable('comments')
            )
                ? Cache::remember('admin_moderation_counts', 60, function () {
                    return [
                        'rubrics_under_moderation_count' => Rubric::where('moderation_status', 0)->count(),
                        'articles_under_moderation_count' => Article::where('moderation_status', 0)->count(),
                        'tags_under_moderation_count' => Tag::where('moderation_status', 0)->count(),
                        'banners_under_moderation_count' => Banner::where('moderation_status', 0)->count(),
                        'videos_under_moderation_count' => Video::where('moderation_status', 0)->count(),
                        'comments_under_moderation_count' => Comment::where('moderation_status', 0)->count(),
                    ];
                })
                : [
                    'rubrics_under_moderation_count' => 0,
                    'articles_under_moderation_count' => 0,
                    'tags_under_moderation_count' => 0,
                    'banners_under_moderation_count' => 0,
                    'videos_under_moderation_count' => 0,
                    'comments_under_moderation_count' => 0,
                ],

            'canLogin' => fn () => Route::has('login'),
            'canRegister' => fn () => Route::has('register'),

            'laravelLang' => fn () => [
                'admin' => [
                    'welcome' => trans('admin/welcome'),
                ],
            ],
        ]);

        /**
         * 4) Morph map для polymorphic relations.
         */
        Relation::morphMap([
            'rubric' => Rubric::class,
            'article' => Article::class,
            'tag' => Tag::class,
            'banner' => Banner::class,
            'video' => Video::class,
            'track' => LearningCategory::class,
            'course' => Course::class,
            'module' => Module::class,
            'lesson' => Lesson::class,
            'bundle' => Bundle::class,
            'subscription_plan' => SubscriptionPlan::class,
        ]);
    }
}
