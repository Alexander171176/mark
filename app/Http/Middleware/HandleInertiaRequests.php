<?php

namespace App\Http\Middleware;

use App\Http\Resources\Admin\System\User\UserSharedResource;
use App\Services\Public\Cms\CmsNavigationService;
use App\Services\Public\Market\MarketCatalogNavigationService;
use App\Services\SiteSettings\AdminSettingsService;
use App\Services\SiteSettings\PublicSettingsService;
use Illuminate\Http\Request;
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
            $shared['adminSettings'] = fn () => app(AdminSettingsService::class)->all();

            return $shared;
        }

        // Публичка
        $shared['publicSettings'] = fn () => app(PublicSettingsService::class)->all(); // настройки
        $shared['marketCatalog'] = fn () => app(MarketCatalogNavigationService::class)->catalog(); // категории
        $shared['cmsMenu'] = fn () => app(CmsNavigationService::class)->menu(); // меню в Header
        $shared['cmsFooter'] = fn () => app(CmsNavigationService::class)->footer(); // меню в Footer

        return $shared;
    }
}
