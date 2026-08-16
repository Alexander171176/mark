<?php

namespace App\Http\Controllers\Public\Default\Blog\BlogTag;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\Blog\BlogArticle\BlogArticleSharedResource;
use App\Http\Resources\Public\Blog\BlogTag\BlogTagResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\Blog\BuildsRubricTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogTagController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use HasSidebarDataTrait;
    use BuildsRubricTreeTrait;

    /** Страница конкретного тега блога. */
    public function show(
        Request $request,
        string $slug
    ): Response {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        /**
         * Публичные настройки сайта.
         *
         * Это единственный источник настроек
         * количества и сортировки статей.
         */
        $settings = app(
            PublicSettingsService::class
        );

        /**
         * Основной тег.
         */
        $tag = BlogTag::query()
            ->forPublic()
            ->whereSlug(
                $slug
            )
            ->with([
                /**
                 * Только текущая локаль
                 * + fallback ru.
                 */
                'translations' => fn ($query) =>
                $query->whereIn(
                    'locale',
                    $locales
                ),
            ])
            ->withCount([
                'articles',
            ])
            ->firstOrFail();

        /**
         * Увеличиваем просмотры тега.
         */
        $tag->increment('views');

        /**
         * Поиск по статьям тега.
         */
        $articlesSearch = $this->resolveSearch(
            $request,
            'q_articles'
        );

        /**
         * Количество статей на странице.
         *
         * Значение берётся из PublicSettingsService.
         * Например, если в настройках указано 12,
         * paginator получает именно 12.
         */
        $perPageArticles = $this->resolvePerPage(
            $request,
            $settings->int(
                'publicBlogArticlesPerPage',
                12
            ),
            1,
            60
        );

        /**
         * Сортировка статей по умолчанию
         * также берётся из публичных настроек.
         */
        $articlesSort = (string) $request->query(
            'sort_articles',
            $settings->string(
                'publicBlogArticlesDefaultSort',
                'sortAsc'
            )
        );

        /**
         * Базовый запрос статей текущего тега.
         */
        $articlesQuery = BlogArticle::query()
            ->forPublic()
            ->whereHas(
                'tags',
                function ($query) use ($tag) {
                    $query->where(
                        'blog_tags.id',
                        $tag->id
                    );
                }
            )
            ->search(
                $articlesSearch,
                $locale
            )
            ->with([
                /**
                 * Текущая локаль
                 * + fallback ru.
                 */
                'translations' => fn ($query) =>
                $query->whereIn(
                    'locale',
                    $locales
                ),

                /**
                 * Автор нужен карточкам статей.
                 */
                'owner',

                /**
                 * Изображения + Spatie Media
                 * загружаются пакетно.
                 */
                'images.media',
            ])
            ->withCount([
                'likes',
            ]);

        /**
         * already_liked добавляется одним EXISTS.
         *
         * Для гостя дополнительный EXISTS
         * вообще не создаётся.
         */
        $articlesQuery = $this->withUserLike(
            $articlesQuery
        );

        /**
         * Серверная сортировка
         * и пагинация статей тега.
         */
        $articles = $articlesQuery
            ->sortByParam(
                $articlesSort,
                $locale
            )
            ->paginate(
                $perPageArticles,
                ['*'],
                'page_articles'
            )
            ->withQueryString();

        /**
         * Сохраняем total до преобразования
         * paginator в ResourceCollection.
         */
        $articlesFound = $articles->total();

        /**
         * Для карточек используется
         * краткий Public Resource статьи.
         */
        $articles = BlogArticleSharedResource::collection(
            $articles
        );

        /**
         * Дерево рубрик.
         */
        $rubricTree = $this->getRubricTree(
            $locale
        );

        /**
         * Данные сайдбаров.
         */
        $sidebarData = $this->getSidebarData(
            $locale
        );

        return Inertia::render(
            'Public/Default/Blog/BlogTags/Show',
            [
                /**
                 * Полный публичный ресурс тега.
                 */
                'tag' => new BlogTagResource(
                    $tag
                ),

                /**
                 * Статьи тега.
                 */
                'articles' => $articles,
                'articlesFound' => $articlesFound,

                /**
                 * Только реальные фильтры страницы.
                 *
                 * per_page_articles возвращаем
                 * для информации frontend,
                 * но Show.vue больше не управляет им.
                 */
                'filters' => [
                    'q_articles' =>
                        $articlesSearch,

                    'per_page_articles' =>
                        $perPageArticles,

                    'sort_articles' =>
                        $articlesSort,
                ],

                'rubricTree' => $rubricTree,
                'locale' => $locale,

                ...$sidebarData,
            ]
        );
    }
}
