<?php

namespace App\Providers;

use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogBanner\BlogBanner;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\Blog\Comment\Comment;
use App\Models\Admin\School\SchoolBundle\SchoolBundle;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Models\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlan;
use App\Models\Admin\School\SchoolTrack\SchoolTrack;
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
            'availableLocales' => fn () => config('app.available_locales', ['ru']),

            'admin' => fn () => $this->adminModerationCounts(),

            'canLogin' => fn () => Route::has('login'),
            'canRegister' => fn () => Route::has('register'),

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

    private function adminModerationCounts(): array
    {
        if (!$this->blogTablesExist()) {
            return $this->emptyAdminModerationCounts();
        }

        return Cache::remember('admin_moderation_counts', 60, fn () => [
            'rubrics_under_moderation_count' => BlogRubric::where('moderation_status', 0)->count(),
            'articles_under_moderation_count' => BlogArticle::where('moderation_status', 0)->count(),
            'tags_under_moderation_count' => BlogTag::where('moderation_status', 0)->count(),
            'banners_under_moderation_count' => BlogBanner::where('moderation_status', 0)->count(),
            'videos_under_moderation_count' => BlogVideo::where('moderation_status', 0)->count(),
            'comments_under_moderation_count' => Comment::where('moderation_status', 0)->count(),
        ]);
    }

    private function blogTablesExist(): bool
    {
        return Schema::hasTable('blog_rubrics')
            && Schema::hasTable('blog_articles')
            && Schema::hasTable('blog_tags')
            && Schema::hasTable('blog_banners')
            && Schema::hasTable('blog_videos')
            && Schema::hasTable('comments');
    }

    private function emptyAdminModerationCounts(): array
    {
        return [
            'rubrics_under_moderation_count' => 0,
            'articles_under_moderation_count' => 0,
            'tags_under_moderation_count' => 0,
            'banners_under_moderation_count' => 0,
            'videos_under_moderation_count' => 0,
            'comments_under_moderation_count' => 0,
        ];
    }

    private function bootMorphMap(): void
    {
        Relation::morphMap([
            'rubric' => BlogRubric::class,
            'article' => BlogArticle::class,
            'tag' => BlogTag::class,
            'banner' => BlogBanner::class,
            'video' => BlogVideo::class,

            'track' => SchoolTrack::class,
            'course' => SchoolCourse::class,
            'module' => SchoolModule::class,
            'lesson' => SchoolLesson::class,
            'bundle' => SchoolBundle::class,
            'subscription_plan' => SchoolSubscriptionPlan::class,
        ]);
    }
}
