<?php

namespace App\Providers;

use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogBanner\BlogBanner;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\Blog\Comment\Comment;
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
                Schema::hasTable('blog_rubrics') &&
                Schema::hasTable('blog_articles') &&
                Schema::hasTable('blog_tags') &&
                Schema::hasTable('blog_banners') &&
                Schema::hasTable('blog_videos') &&
                Schema::hasTable('comments')
            )
                ? Cache::remember('admin_moderation_counts', 60, function () {
                    return [
                        'rubrics_under_moderation_count' => BlogRubric::where('moderation_status', 0)->count(),
                        'articles_under_moderation_count' => BlogArticle::where('moderation_status', 0)->count(),
                        'tags_under_moderation_count' => BlogTag::where('moderation_status', 0)->count(),
                        'banners_under_moderation_count' => BlogBanner::where('moderation_status', 0)->count(),
                        'videos_under_moderation_count' => BlogVideo::where('moderation_status', 0)->count(),
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
            'rubric' => BlogRubric::class,
            'article' => BlogArticle::class,
            'tag' => BlogTag::class,
            'banner' => BlogBanner::class,
            'video' => BlogVideo::class,

            'track' => LearningCategory::class,
            'course' => Course::class,
            'module' => Module::class,
            'lesson' => Lesson::class,
            'bundle' => Bundle::class,
            'subscription_plan' => SubscriptionPlan::class,
        ]);
    }
}
