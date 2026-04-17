<?php

namespace App\Http\Middleware;

use App\Http\Resources\Admin\System\User\UserSharedResource;
use App\Services\SiteSettings\SiteSettings;
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

        // ✅ Админка у тебя на /admin..., но есть ещё локаль /ru/admin...
        $isAdmin = $request->segment(1) === 'admin' || $request->segment(2) === 'admin';

        // ✅ Подгружаем роли/разрешения ТОЛЬКО для админки (и только если пользователь есть)
        if ($isAdmin && $user) {
            $user->loadMissing(['roles', 'permissions']);
        }

        $shared = [
            ...parent::share($request),

            'user' => fn () => $user ? (new UserSharedResource($user))->toArray($request) : null,

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

        // ✅ 1) Админка: тоже только snapshot/admin.php
        if ($isAdmin) {
            $shared['adminSettings'] = fn () => SiteSettings::all('admin');
            return $shared;
        }

        // ✅ 2) Публичка: только snapshot/public.php
        $shared['publicSettings'] = fn () => SiteSettings::all('public');

        return $shared;
    }
}
