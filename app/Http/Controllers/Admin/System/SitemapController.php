<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SitemapController extends Controller
{
    private string $file = 'sitemap.xml';

    /** Единый список сущностей для sitemap */
    private array $sitemapResources = [
        [
            'model' => BlogRubric::class,
            'indexRoute' => 'public.blogRubrics.index',
            'showRoute' => 'public.blogRubrics.show',
            'urlField' => 'url',
            'priority' => 0.8,
            'changeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
        ],
        [
            'model' => BlogArticle::class,
            'indexRoute' => 'public.blogArticles.index',
            'showRoute' => 'public.blogArticles.show',
            'urlField' => 'url',
            'priority' => 0.9,
            'changeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
        ],
        [
            'model' => BlogTag::class,
            'indexRoute' => null,
            'showRoute' => 'public.blogTags.show',
            'urlField' => 'slug',
            'priority' => 0.7,
            'changeFrequency' => Url::CHANGE_FREQUENCY_MONTHLY,
        ],
        [
            'model' => BlogVideo::class,
            'indexRoute' => 'public.blogVideos.index',
            'showRoute' => 'public.blogVideos.show',
            'urlField' => 'url',
            'priority' => 0.8,
            'changeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
        ],

        // Пример сущности без страницы index:
        // [
        //     'model' => SomeModel::class,
        //     'indexRoute' => null,
        //     'showRoute' => 'public.some.show',
        //     'urlField' => 'slug',
        // ],
    ];

    /** Страница просмотра / генерации */
    public function index(): Response
    {
        $content = file_exists(public_path($this->file))
            ? file_get_contents(public_path($this->file))
            : '';

        return Inertia::render('Admin/System/SitemapPage', [
            'content' => $content,
        ]);
    }

    /** Сгенерировать sitemap */
    public function generate(): Response
    {
        $this->build();

        return Inertia::render('Admin/System/SitemapPage', [
            'content' => file_get_contents(public_path($this->file)),
            'flash' => ['success' => 'sitemap.xml обновлён'],
        ]);
    }

    /** Скачать sitemap */
    public function download(): BinaryFileResponse
    {
        abort_unless(file_exists(public_path($this->file)), 404);

        return response()->download(public_path($this->file));
    }

    /** Основная логика сборки sitemap */
    private function build(): void
    {
        $sitemap = Sitemap::create()
            ->add(
                Url::create(route('home'))
                    ->setLastModificationDate(Carbon::yesterday())
                    ->setPriority(1.0)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            );

        foreach ($this->sitemapResources as $resource) {
            $this->addIndexUrl($sitemap, $resource);
            $this->addShowUrls($sitemap, $resource);
        }

        $sitemap->writeToFile(public_path($this->file));
    }

    /** Добавить index-страницу сущности */
    private function addIndexUrl(Sitemap $sitemap, array $resource): void
    {
        $indexRoute = $resource['indexRoute'] ?? null;

        if (!$indexRoute || !Route::has($indexRoute)) {
            return;
        }

        $sitemap->add(
            Url::create(route($indexRoute))
                ->setLastModificationDate(Carbon::yesterday())
                ->setPriority($resource['indexPriority'] ?? 0.6)
                ->setChangeFrequency($resource['indexChangeFrequency'] ?? Url::CHANGE_FREQUENCY_WEEKLY)
        );
    }

    /** Добавить show-страницы сущности */
    private function addShowUrls(Sitemap $sitemap, array $resource): void
    {
        $model = $resource['model'];
        $showRoute = $resource['showRoute'];
        $urlField = $resource['urlField'] ?? 'url';

        if (!Route::has($showRoute)) {
            return;
        }

        $this->queryForSitemap($model)
            ->select(['id', $urlField, 'updated_at'])
            ->whereNotNull($urlField)
            ->orderBy('id')
            ->chunkById(500, function ($items) use ($sitemap, $resource, $showRoute, $urlField) {
                foreach ($items as $item) {
                    $sitemap->add(
                        Url::create(route($showRoute, $item->{$urlField}))
                            ->setLastModificationDate($item->updated_at ?? Carbon::yesterday())
                            ->setPriority($resource['priority'] ?? 0.8)
                            ->setChangeFrequency($resource['changeFrequency'] ?? Url::CHANGE_FREQUENCY_WEEKLY)
                    );
                }
            });
    }

    /** Базовый запрос для sitemap */
    private function queryForSitemap(string $model): Builder
    {
        $query = $model::query();

        if (method_exists($model, 'scopeForPublic')) {
            return $query->forPublic();
        }

        if (Schema::hasColumn((new $model)->getTable(), 'activity')) {
            return $query->where('activity', true);
        }

        return $query;
    }
}
