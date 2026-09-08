<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class SitemapController extends Controller
{
    /** Страница sitemap. */
    public function index(): Response
    {
        $files = $this->sitemapFiles();

        $selectedFile = collect($files)
            ->firstWhere('exists', true);

        return Inertia::render('Admin/System/SitemapPage', [
            'files' => $files,

            'selectedFile' => $selectedFile['name']
                ?? null,

            'content' => $selectedFile
                ? $this->getFileContent(
                    $selectedFile['name']
                )
                : '',
        ]);
    }

    /** Генерация sitemap. */
    public function generate(): RedirectResponse
    {
        try {
            $this->build();

            return redirect()
                ->route('admin.sitemap.index')
                ->with(
                    'success',
                    'Мультиязычные sitemap успешно обновлены.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка генерации sitemap: ' . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->withErrors([
                'sitemap' => 'Не удалось сгенерировать sitemap.',
            ]);
        }
    }

    /** Скачать выбранный sitemap-файл. */
    public function download(
        Request $request
    ): BinaryFileResponse {
        $file = $this->validateSitemapFile(
            (string) $request->query(
                'file',
                $this->file()
            )
        );

        $path = public_path(
            $file
        );

        abort_unless(
            file_exists($path),
            404
        );

        return response()->download(
            $path,
            $file,
            [
                'Content-Type'
                => 'application/xml; charset=UTF-8',
            ]
        );
    }

    /** Основная сборка sitemap. */
    private function build(): void
    {
        $originalLocale = app()->getLocale();

        try {
            foreach ($this->locales() as $locale) {
                $this->buildLocaleSitemap(
                    $locale
                );
            }

            $this->buildSitemapIndex();
        } finally {
            $this->setLocale(
                $originalLocale
            );
        }
    }

    /** Сборка sitemap конкретной локали. */
    private function buildLocaleSitemap(
        string $locale
    ): void {
        $this->setLocale(
            $locale
        );

        $sitemap = Sitemap::create();

        $this->addHomeUrl(
            $sitemap,
            $locale
        );

        foreach ($this->resources() as $group) {
            foreach ($group as $resource) {
                $this->addIndexUrl(
                    $sitemap,
                    $resource,
                    $locale
                );

                $this->addShowUrls(
                    $sitemap,
                    $resource,
                    $locale
                );
            }
        }

        $sitemap->writeToFile(
            public_path(
                $this->localeFile(
                    $locale
                )
            )
        );
    }

    /** Сборка главного sitemap index. */
    private function buildSitemapIndex(): void
    {
        $index = SitemapIndex::create();

        foreach ($this->locales() as $locale) {
            $file = $this->localeFile(
                $locale
            );

            $path = public_path(
                $file
            );

            if (!file_exists($path)) {
                continue;
            }

            $index->add(
                url($file)
            );
        }

        $index->writeToFile(
            public_path(
                $this->file()
            )
        );
    }

    /** Главная страница. */
    private function addHomeUrl(
        Sitemap $sitemap,
        string $locale
    ): void {
        if (!Route::has('home')) {
            return;
        }

        $url = Url::create(
            $this->localizedRoute(
                'home',
                $locale
            )
        )
            ->setPriority(
                1.0
            )
            ->setChangeFrequency(
                Url::CHANGE_FREQUENCY_DAILY
            );

        foreach (
            $this->locales()
            as $alternateLocale
        ) {
            $url->addAlternate(
                $this->localizedRoute(
                    'home',
                    $alternateLocale
                ),
                $alternateLocale
            );
        }

        $sitemap->add(
            $url
        );
    }

    /** Index-страница сущности. */
    private function addIndexUrl(
        Sitemap $sitemap,
        array $resource,
        string $locale
    ): void {
        $routeName =
            $resource['indexRoute']
            ?? null;

        if (
            !$routeName
            || !Route::has($routeName)
            || !$this->hasTranslatedPublicItems(
                $resource,
                $locale
            )
        ) {
            return;
        }

        $url = Url::create(
            $this->localizedRoute(
                $routeName,
                $locale
            )
        )
            ->setPriority(
                $resource['indexPriority']
                ?? 0.6
            )
            ->setChangeFrequency(
                $resource['indexChangeFrequency']
                ?? Url::CHANGE_FREQUENCY_WEEKLY
            );

        foreach (
            $this->locales()
            as $alternateLocale
        ) {
            if (!$this->hasTranslatedPublicItems(
                $resource,
                $alternateLocale
            )) {
                continue;
            }

            $url->addAlternate(
                $this->localizedRoute(
                    $routeName,
                    $alternateLocale
                ),
                $alternateLocale
            );
        }

        $sitemap->add(
            $url
        );
    }

    /** Show-страницы сущности. */
    private function addShowUrls(
        Sitemap $sitemap,
        array $resource,
        string $locale
    ): void {
        $showRoute =
            $resource['showRoute']
            ?? null;

        if (
            !$showRoute
            || !Route::has($showRoute)
        ) {
            return;
        }

        $urlField =
            $resource['urlField']
            ?? 'url';

        $routeParameter =
            $resource['routeParameter']
            ?? $urlField;

        $translationRelation =
            $resource['translationRelation']
            ?? 'translations';

        $translationLocaleField =
            $resource['translationLocaleField']
            ?? 'locale';

        $trimRouteParameterSlashes =
            (bool) (
                $resource['trimRouteParameterSlashes']
                ?? false
            );

        $this->queryForSitemap(
            $resource['model'],
            $resource,
            $locale
        )
            ->select([
                'id',
                $urlField,
                'updated_at',
            ])
            ->with(
                $translationRelation
            )
            ->whereNotNull(
                $urlField
            )
            ->where(
                $urlField,
                '!=',
                ''
            )
            ->orderBy(
                'id'
            )
            ->chunkById(
                500,
                function ($items) use (
                    $sitemap,
                    $resource,
                    $locale,
                    $showRoute,
                    $urlField,
                    $routeParameter,
                    $translationRelation,
                    $translationLocaleField,
                    $trimRouteParameterSlashes
                ) {
                    foreach ($items as $item) {
                        $translatedLocales = $item
                            ->{$translationRelation}
                            ->pluck(
                                $translationLocaleField
                            )
                            ->filter()
                            ->unique()
                            ->values()
                            ->all();

                        $routeParameterValue =
                            $item->{$urlField};

                        if ($trimRouteParameterSlashes) {
                            $routeParameterValue = trim(
                                (string) $routeParameterValue,
                                '/'
                            );
                        }

                        $url = Url::create(
                            $this->localizedRoute(
                                $showRoute,
                                $locale,
                                [
                                    $routeParameter
                                    => $routeParameterValue,
                                ]
                            )
                        )
                            ->setPriority(
                                $resource['priority']
                                ?? 0.8
                            )
                            ->setChangeFrequency(
                                $resource['changeFrequency']
                                ?? Url::CHANGE_FREQUENCY_WEEKLY
                            );

                        if ($item->updated_at) {
                            $url->setLastModificationDate(
                                $item->updated_at
                            );
                        }

                        foreach (
                            $translatedLocales
                            as $alternateLocale
                        ) {
                            if (!in_array(
                                $alternateLocale,
                                $this->locales(),
                                true
                            )) {
                                continue;
                            }

                            $url->addAlternate(
                                $this->localizedRoute(
                                    $showRoute,
                                    $alternateLocale,
                                    [
                                        $routeParameter
                                        => $routeParameterValue,
                                    ]
                                ),
                                $alternateLocale
                            );
                        }

                        $sitemap->add(
                            $url
                        );
                    }
                }
            );
    }

    /** Есть ли публичные сущности с переводом указанной локали. */
    private function hasTranslatedPublicItems(
        array $resource,
        string $locale
    ): bool {
        return $this->queryForSitemap(
            $resource['model'],
            $resource,
            $locale
        )->exists();
    }

    /** Базовый публичный запрос сущности для конкретной локали. */
    private function queryForSitemap(
        string $model,
        array $resource,
        string $locale
    ): Builder {
        $query = $model::query();

        if (method_exists(
            $model,
            'scopeForPublic'
        )) {
            $query->forPublic();
        }

        $scope =
            $resource['scope']
            ?? null;

        if (
            $scope
            && method_exists(
                $model,
                'scope' . ucfirst($scope)
            )
        ) {
            $query->{$scope}();
        }

        $urlField =
            $resource['urlField']
            ?? 'url';

        $excludeUrlValues =
            $resource['excludeUrlValues']
            ?? [];

        if ($excludeUrlValues) {
            $query->whereNotIn(
                $urlField,
                $excludeUrlValues
            );
        }

        $translationRelation =
            $resource['translationRelation']
            ?? 'translations';

        $translationLocaleField =
            $resource['translationLocaleField']
            ?? 'locale';

        return $query->whereHas(
            $translationRelation,
            function (Builder $query) use (
                $translationLocaleField,
                $locale
            ): void {
                $query->where(
                    $translationLocaleField,
                    $locale
                );
            }
        );
    }

    /** Построение маршрута для конкретной локали. */
    private function localizedRoute(
        string $routeName,
        string $locale,
        array $parameters = []
    ): string {
        $url = route(
            $routeName,
            $parameters
        );

        $locales = implode(
            '|',
            array_map(
                fn (string $item) => preg_quote(
                    $item,
                    '#'
                ),
                $this->locales()
            )
        );

        return preg_replace(
            '#^('
            . preg_quote(
                url('/'),
                '#'
            )
            . ')/('
            . $locales
            . ')(?=/|$)#',
            '$1/' . $locale,
            $url
        ) ?? $url;
    }

    /** Установка локали приложения. */
    private function setLocale(
        string $locale
    ): void {
        app()->setLocale(
            $locale
        );
    }

    /** Имя главного sitemap-файла. */
    private function file(): string
    {
        return config(
            'sitemap.file',
            'sitemap.xml'
        );
    }

    /** Имя sitemap-файла конкретной локали. */
    private function localeFile(
        string $locale
    ): string {
        return str_replace(
            '{locale}',
            $locale,
            config(
                'sitemap.localeFile',
                'sitemap-{locale}.xml'
            )
        );
    }

    /** Доступные локали sitemap. */
    private function locales(): array
    {
        return config(
            'sitemap.locales',
            [
                config(
                    'app.locale',
                    'ru'
                ),
            ]
        );
    }

    /** Публичные ресурсы sitemap. */
    private function resources(): array
    {
        return config(
            'sitemap.resources',
            []
        );
    }

    /** Все sitemap-файлы. */
    private function sitemapFiles(): array
    {
        $files = [
            [
                'key' => 'index',
                'locale' => null,
                'name' => $this->file(),
            ],
        ];

        foreach ($this->locales() as $locale) {
            $files[] = [
                'key' => $locale,
                'locale' => $locale,
                'name' => $this->localeFile(
                    $locale
                ),
            ];
        }

        return array_map(
            function (array $file): array {
                $file['exists'] = file_exists(
                    public_path(
                        $file['name']
                    )
                );

                return $file;
            },
            $files
        );
    }

    /** Проверка разрешённого sitemap-файла. */
    private function validateSitemapFile(
        string $file
    ): string {
        $allowedFiles = array_column(
            $this->sitemapFiles(),
            'name'
        );

        abort_unless(
            in_array(
                $file,
                $allowedFiles,
                true
            ),
            404
        );

        return $file;
    }

    /** Содержимое sitemap-файла. */
    private function getFileContent(
        string $file
    ): string {
        $path = public_path(
            $file
        );

        if (!file_exists($path)) {
            return '';
        }

        return file_get_contents(
            $path
        ) ?: '';
    }

    /** Содержимое выбранного sitemap-файла. */
    public function content(
        Request $request
    ): JsonResponse {
        $file = $this->validateSitemapFile(
            (string) $request->query(
                'file',
                ''
            )
        );

        return response()->json([
            'file' => $file,
            'content' => $this->getFileContent(
                $file
            ),
        ]);
    }
}
