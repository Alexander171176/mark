<?php

namespace App\Http\Controllers\Public\Default\Blog\BlogTag;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleResource;
use App\Http\Resources\Admin\Blog\BlogTag\BlogTagResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogTag\BlogTag;
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

    /** Страница всех тегов блога. */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $perPage = $this->resolvePerPage(
            $request,
            (int) config('site_settings.publicBlogTagsPerPage', 20)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            (string) config('site_settings.publicBlogTagsDefaultSort', 'nameAsc')
        );

        $view = $this->resolveView(
            $request,
            (string) config('site_settings.publicBlogTagsDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            (string) config('site_settings.publicBlogTagsProcessingMode', 'server')
        );

        $tags = BlogTag::query()
            ->forPublic()
            ->search($search, $locale)
            ->with([
                'translations',
                'owner',
            ])
            ->withCount('articles')
            ->sortByParam($sort, $locale)
            ->paginate($perPage)
            ->withQueryString();

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/BlogTags/Index', [
            'tags' => BlogTagResource::collection($tags),

            'tagsCount' => BlogTag::query()
                ->forPublic()
                ->count(),

            'tagsFound' => $tags->total(),

            'filters' => $this->buildIndexFilters(
                $search,
                $perPage,
                $sort,
                $view,
                $processingMode
            ),

            'rubricTree' => $rubricTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }

    /** Страница конкретного тега блога. */
    public function show(Request $request, string $slug): Response
    {
        $locale = app()->getLocale();

        $tag = BlogTag::query()
            ->forPublic()
            ->whereSlug($slug)
            ->with([
                'translations',
                'owner',
            ])
            ->withCount('articles')
            ->firstOrFail();

        $tag->increment('views');

        $articlesSearch = $this->resolveSearch($request, 'q_articles');

        $perPageArticles = $this->resolvePerPage(
            $request,
            (int) config('site_settings.publicBlogArticlesPerPage', 3),
            3,
            60
        );

        $articlesSort = (string) $request->query(
            'sort_articles',
            config('site_settings.publicBlogArticlesDefaultSort', 'sortAsc')
        );

        $articles = BlogArticle::query()
            ->forPublic()
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('blog_tags.id', $tag->id);
            })
            ->search($articlesSearch, $locale)
            ->with([
                'translations',
                'owner',
                'images',
            ])
            ->withCount('likes')
            ->sortByParam($articlesSort, $locale)
            ->paginate($perPageArticles, ['*'], 'page_articles')
            ->withQueryString();

        $articles = $this->appendUserLikes($articles, BlogArticleResource::class);

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/BlogTags/Show', [
            'tag' => new BlogTagResource($tag),

            'articles' => $articles,
            'articlesFound' => $articles->total(),

            'filters' => [
                'q_articles' => $articlesSearch,
                'per_page_articles' => $perPageArticles,
                'sort_articles' => $articlesSort,
            ],

            'rubricTree' => $rubricTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }
}
