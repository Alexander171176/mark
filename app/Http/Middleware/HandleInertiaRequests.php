<?php

namespace App\Http\Middleware;

use App\Http\Resources\Admin\System\User\UserSharedResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogBanner\BlogBanner;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\Blog\Comment\Comment;
use App\Services\Public\Cms\CmsNavigationService;
use App\Services\Public\Market\MarketCatalogNavigationService;
use App\Services\SiteSettings\AdminSettingsService;
use App\Services\SiteSettings\PublicSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * @var string
     */
    protected $rootView = 'app';

    /**
     * @param Request $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function share(Request $request): array
    {
        $user = auth()->user();

        $isAdminArea = $request->segment(1) === 'admin' || $request->segment(2) === 'admin';

        $isAdminUser = $user?->hasRole('admin') ?? false;

        if ($isAdminArea && $user) {
            $user->loadMissing(['roles', 'permissions']);
        }

        $shared = [
            ...parent::share($request),

            'user' => fn () => $user ? (new UserSharedResource($user))->toArray($request) : null,

            'isAdmin' => fn () => $isAdminUser,

            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location'  => $request->url(),
                'routeName' => optional($request->route())->getName(),
            ],

            'locale' => LaravelLocalization::getCurrentLocale(),
            'appUrl' => config('app.url'),

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info'    => fn () => $request->session()->get('info'),
            ],
        ];

        // Админка
        if ($isAdminArea) {
            /**
             * Административная статистика модерации.
             */
            $shared['admin'] = fn () =>
            $this->adminModerationCounts();

            /**
             * Настройки административной части.
             */
            $shared['adminSettings'] = fn () =>
            app(AdminSettingsService::class)->all();

            return $shared;
        }

        // Публичка
        $shared['publicSettings'] = fn () => app(PublicSettingsService::class)->all(); // настройки
        $shared['marketCatalog'] = fn () => app(MarketCatalogNavigationService::class)->catalog(); // категории
        $shared['cmsMenu'] = fn () => app(CmsNavigationService::class)->menu(); // меню в Header
        $shared['cmsFooter'] = fn () => app(CmsNavigationService::class)->footer(); // меню в Footer

        return $shared;
    }

    /**
     * Количество сущностей блога,
     * ожидающих модерации.
     *
     * Данные используются только
     * в административной части.
     */
    private function adminModerationCounts(): array
    {
        return Cache::remember(
            'admin_moderation_counts',
            60,
            function () {
                if (!$this->blogTablesExist()) {
                    return $this->emptyAdminModerationCounts();
                }

                return [
                    'rubrics_under_moderation_count' =>
                        BlogRubric::where(
                            'moderation_status',
                            0
                        )->count(),

                    'articles_under_moderation_count' =>
                        BlogArticle::where(
                            'moderation_status',
                            0
                        )->count(),

                    'tags_under_moderation_count' =>
                        BlogTag::where(
                            'moderation_status',
                            0
                        )->count(),

                    'banners_under_moderation_count' =>
                        BlogBanner::where(
                            'moderation_status',
                            0
                        )->count(),

                    'videos_under_moderation_count' =>
                        BlogVideo::where(
                            'moderation_status',
                            0
                        )->count(),

                    'comments_under_moderation_count' =>
                        Comment::where(
                            'moderation_status',
                            0
                        )->count(),
                ];
            }
        );
    }

    /**
     * Проверить наличие таблиц,
     * необходимых для статистики модерации блога.
     */
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
}
