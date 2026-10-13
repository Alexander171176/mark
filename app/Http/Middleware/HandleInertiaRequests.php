<?php

namespace App\Http\Middleware;

use App\Context\Location\LocationContext;
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
     * Корневой шаблон Inertia.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Версия ресурсов Inertia.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Общие данные для страниц Inertia.
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        /**
         * Определяем административную часть приложения.
         *
         * Учитываем маршруты с префиксом локали:
         * /admin/...
         * /ru/admin/...
         */
        $isAdminArea = $request->segment(1) === 'admin'
            || $request->segment(2) === 'admin';

        /**
         * Системный суперпользователь.
         *
         * Используем ту же проверку, что и в Gate::before.
         */
        $isSuperAdmin = $user !== null
            && (int) $user->getKey() === 1;

        /**
         * Администратор системы.
         *
         * Суперпользователь имеет доступ независимо
         * от назначения роли admin.
         */
        $isAdminUser = $user !== null && (
                $isSuperAdmin ||
                $user->hasRole('admin')
            );

        /**
         * Загружаем роли и прямые разрешения
         * только для административной части.
         */
        if ($isAdminArea && $user) {
            $user->loadMissing([
                'roles',
                'permissions',
            ]);
        }

        /**
         * Общие данные для административной
         * и публичной частей приложения.
         */
        $shared = [
            ...parent::share($request),

            /**
             * Авторизованный пользователь.
             */
            'user' => fn () => $user
                ? (new UserSharedResource($user))->toArray($request)
                : null,

            /**
             * Признаки административного доступа.
             */
            'isAdmin' => fn () => $isAdminUser,

            'isSuperAdmin' => fn () => $isSuperAdmin,

            /**
             * Роли текущего пользователя.
             *
             * Передаём массив названий:
             * ['admin', 'manager']
             */
            'roles' => fn () => $user
                ? $user->getRoleNames()->values()->all()
                : [],

            /**
             * Все эффективные разрешения пользователя:
             * прямые + полученные через роли.
             *
             * Суперпользователю не требуется
             * физически назначать все разрешения.
             */
            'permissions' => fn () => $user
                ? $user->getAllPermissions()
                    ->pluck('name')
                    ->unique()
                    ->values()
                    ->all()
                : [],

            /**
             * Маршруты Ziggy.
             */
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
                'routeName' => optional($request->route())->getName(),
            ],

            /**
             * Локализация и адрес приложения.
             */
            'locale' => LaravelLocalization::getCurrentLocale(),

            'appUrl' => config('app.url'),

            /**
             * Flash-сообщения.
             */
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
        ];

        /**
         * ============================================
         * АДМИНИСТРАТИВНАЯ ЧАСТЬ
         * ============================================
         */
        if ($isAdminArea) {

            /**
             * Настройки административной части.
             */
            $shared['adminSettings'] = fn () =>
            app(AdminSettingsService::class)->all();

            return $shared;
        }

        /**
         * ============================================
         * ПУБЛИЧНАЯ ЧАСТЬ
         * ============================================
         */

        /**
         * Публичные настройки сайта.
         */
        $shared['publicSettings'] = fn () =>
        app(PublicSettingsService::class)->all();

        /**
         * Каталог маркетплейса.
         */
        $shared['marketCatalog'] = fn () =>
        app(MarketCatalogNavigationService::class)->catalog();

        /**
         * Навигационное меню CMS.
         */
        $shared['cmsMenu'] = fn () =>
        app(CmsNavigationService::class)->menu();

        /**
         * Нижнее меню CMS.
         */
        $shared['cmsFooter'] = fn () =>
        app(CmsNavigationService::class)->footer();

        /**
         * Текущая локация публичной части.
         *
         * Location уже определена middleware ResolveLocation
         * и хранится в LocationContext.
         */
        $shared['currentLocation'] = function () {

            $location = app(LocationContext::class)->current();

            if (!$location) {
                return null;
            }

            return [
                'id' => $location->id,
                'parent_id' => $location->parent_id,
                'type' => $location->type,
                'slug' => $location->slug,
                'code' => $location->code,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'timezone' => $location->timezone,
                'is_default' => (bool) $location->is_default,
            ];
        };

        return $shared;
    }
}
