<?php

namespace App\Http\Controllers\Public\Default\Blog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Blog\Article\ArticleResource;
use App\Http\Resources\Admin\Blog\Rubric\RubricResource;
use App\Http\Resources\Admin\Blog\Video\VideoResource;
use App\Models\Admin\Blog\Article\Article;
use App\Traits\Public\BuildsRubricTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Контроллер для показа статей (Blog) в публичной части.
 *
 * Паттерн:
 * - список всех статей
 * - показ конкретной статьи
 * - поиск
 * - сортировка
 * - пагинация
 * - хлебная крошка по первой рубрике
 * - лайки
 * - связанные статьи
 * - видео статьи
 * - left/right/main sidebar blocks
 * - rubricTree для левого меню
 *
 * @version 1.1
 */
class ArticleController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use HasSidebarDataTrait;
    use BuildsRubricTreeTrait;

    /**
     * Страница всех статей.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $perPage = $this->resolvePerPage($request);
        $search = $this->resolveSearch($request);
        $sort = $this->resolveSort($request, 'date_desc');

        $articles = Article::query()
            ->forPublic($locale)
            ->search($search)
            ->with([
                'owner',
                'images' => fn ($q) => $q->orderBy('order'),
            ])
            ->withCount('likes')
            ->sortByParam($sort)
            ->paginate($perPage)
            ->withQueryString();

        $articles = $this->appendUserLikes($articles, ArticleResource::class);

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/Articles/Index', [
            'articles' => $articles,
            'articlesCount' => $articles->total(),
            'articlesFound' => $articles->total(),
            'filters' => $this->buildIndexFilters($search, $perPage, $sort),
            'rubricTree' => $rubricTree,
            'locale' => $locale,
            ...$sidebarData,
        ]);
    }

    /**
     * Страница показа конкретной статьи.
     *
     * @param string $url
     * @return Response
     */
    public function show(string $url): Response
    {
        $locale = app()->getLocale();

        $article = Article::query()
            ->forPublic($locale)
            ->where('url', $url)
            ->withCount('likes')
            ->with([
                'owner',
                'images' => fn ($q) => $q->orderBy('order'),
                'tags',
                'rubrics' => fn ($q) => $q
                    ->forPublic($locale)
                    ->ordered(),
                'relatedArticles' => fn ($q) => $q
                    ->forPublic($locale)
                    ->with([
                        'owner',
                        'images' => fn ($qq) => $qq->orderBy('order'),
                    ])
                    ->withCount('likes')
                    ->ordered(),
            ])
            ->firstOrFail();

        $article->increment('views');

        $breadcrumbRubric = $article->rubrics
            ->unique('id')
            ->sortBy('sort')
            ->first();

        $alreadyLiked = auth()->check()
            ? $article->likes()->where('user_id', auth()->id())->exists()
            : false;

        $recommendedArticles = $article->relatedArticles->map(function ($relatedArticle) {
            $resolved = (new ArticleResource($relatedArticle))->resolve();

            $resolved['already_liked'] = auth()->check()
                ? $relatedArticle->likes()->where('user_id', auth()->id())->exists()
                : false;

            return $resolved;
        });

        $articleVideos = $article->videos()
            ->forPublic($locale)
            ->with([
                'images' => fn ($q) => $q->orderBy('order'),
            ])
            ->get();

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/Articles/Show', [
            'article' => array_merge(
                (new ArticleResource($article))->resolve(),
                ['already_liked' => $alreadyLiked]
            ),

            'breadcrumbRubric' => $breadcrumbRubric
                ? (new RubricResource($breadcrumbRubric))->resolve()
                : null,

            'recommendedArticles' => $recommendedArticles,
            'articleVideos' => VideoResource::collection($articleVideos),

            'rubricTree' => $rubricTree,
            'locale' => $locale,
            ...$sidebarData,
        ]);
    }

    /**
     * Лайк статьи.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function like(string $id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $article = Article::findOrFail($id);
        $user = auth()->user();

        if ($article->likes()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $article->likes()->count(),
            ]);
        }

        $article->likes()->create([
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $article->likes()->count(),
        ]);
    }
}
