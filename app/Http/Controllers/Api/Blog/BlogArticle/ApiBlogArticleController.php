<?php

namespace App\Http\Controllers\Api\Blog\BlogArticle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\BlogArticle\BlogArticleRequest;
use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Blog Articles",
 *     description="API управления статьями блога"
 * )
 *
 * @OA\Schema(
 *     schema="BlogArticle",
 *     type="object",
 *     title="BlogArticle",
 *     description="Модель статьи блога",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="sort", type="integer", example=0),
 *     @OA\Property(property="activity", type="boolean", example=true),
 *     @OA\Property(property="left", type="boolean", example=false),
 *     @OA\Property(property="main", type="boolean", example=false),
 *     @OA\Property(property="right", type="boolean", example=false),
 *     @OA\Property(property="url", type="string", example="example-article"),
 *     @OA\Property(property="published_at", type="string", format="date", nullable=true),
 *     @OA\Property(property="views", type="integer", example=100),
 *     @OA\Property(property="likes_count", type="integer", example=5),
 *     @OA\Property(property="translation", type="object"),
 *     @OA\Property(property="translations", type="array", @OA\Items(type="object")),
 *     @OA\Property(property="rubrics", type="array", @OA\Items(type="object")),
 *     @OA\Property(property="tags", type="array", @OA\Items(type="object")),
 *     @OA\Property(property="images", type="array", @OA\Items(type="object")),
 *     @OA\Property(property="videos", type="array", @OA\Items(type="object")),
 *     @OA\Property(property="related_articles", type="array", @OA\Items(type="object"))
 * )
 */
class ApiBlogArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/blog-articles",
     *     operationId="getBlogArticles",
     *     tags={"Blog Articles"},
     *     summary="Получить список статей блога",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="articles",
     *                 type="array",
     *
     *                 @OA\Items(ref="#/components/schemas/BlogArticle")
     *             ),
     *
     *             @OA\Property(
     *                 property="articlesCount",
     *                 type="integer",
     *                 example=25
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $articles = BlogArticle::query()
            ->with([
                'translations',
                'owner',
                'images',
                'rubrics.translations',
                'tags.translations',
                'videos.translations',
                'videos.images',
                'relatedArticles.translations',
                'relatedArticles.images',
            ])
            ->withCount([
                'comments',
                'rubrics',
                'tags',
                'images',
                'videos',
                'likes',
                'relatedArticles',
            ])
            ->ordered()
            ->get();

        return response()->json([
            'articles' => BlogArticleResource::collection($articles),
            'articlesCount' => BlogArticle::query()->count(),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/blog-articles",
     *     operationId="storeBlogArticle",
     *     tags={"Blog Articles"},
     *     summary="Создать статью блога",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/BlogArticle")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Статья успешно создана",
     *
     *         @OA\JsonContent(ref="#/components/schemas/BlogArticle")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации"
     *     )
     * )
     */
    public function store(BlogArticleRequest $request): JsonResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $rubricIds = $data['rubrics'] ?? [];
        $tagIds = $data['tags'] ?? [];
        $videos = $data['videos'] ?? [];
        $relatedArticles = $data['related_articles'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['rubrics'],
            $data['tags'],
            $data['videos'],
            $data['related_articles']
        );

        $article = DB::transaction(function () use (
            $data,
            $translations,
            $rubricIds,
            $tagIds,
            $videos,
            $relatedArticles
        ) {
            if (!isset($data['sort']) || is_null($data['sort'])) {
                $maxSort = BlogArticle::query()->max('sort');
                $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
            }

            $article = BlogArticle::create($data);

            foreach ($translations as $locale => $translation) {
                $article->translations()->create([
                    ...$translation,
                    'locale' => $locale,
                ]);
            }

            $article->rubrics()->sync($rubricIds);
            $article->tags()->sync($tagIds);

            $this->syncVideos($article, $videos);
            $this->syncRelatedArticles($article, $relatedArticles);

            return $article;
        });

        $article = $this->loadArticleRelations($article);

        Log::info('API - Статья блога создана', [
            'id' => $article->id,
        ]);

        return response()->json(
            new BlogArticleResource($article),
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/blog-articles/{id}",
     *     operationId="showBlogArticle",
     *     tags={"Blog Articles"},
     *     summary="Получить статью блога",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *
     *         @OA\JsonContent(ref="#/components/schemas/BlogArticle")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Статья не найдена"
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $article = BlogArticle::query()
            ->whereKey($id)
            ->firstOrFail();

        $article = $this->loadArticleRelations($article);

        return response()->json(
            new BlogArticleResource($article)
        );
    }

    /**
     * @OA\Put(
     *     path="/api/blog-articles/{id}",
     *     operationId="updateBlogArticle",
     *     tags={"Blog Articles"},
     *     summary="Обновить статью блога",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/BlogArticle")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Статья успешно обновлена",
     *
     *         @OA\JsonContent(ref="#/components/schemas/BlogArticle")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Статья не найдена"
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации"
     *     )
     * )
     */
    public function update(
        BlogArticleRequest $request,
        string $id
    ): JsonResponse {
        $article = BlogArticle::query()
            ->whereKey($id)
            ->firstOrFail();

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $rubricIds = $data['rubrics'] ?? [];
        $tagIds = $data['tags'] ?? [];
        $videos = $data['videos'] ?? [];
        $relatedArticles = $data['related_articles'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['rubrics'],
            $data['tags'],
            $data['videos'],
            $data['related_articles'],
            $data['_method']
        );

        DB::transaction(function () use (
            $article,
            $data,
            $translations,
            $rubricIds,
            $tagIds,
            $videos,
            $relatedArticles
        ) {
            $article->update($data);

            foreach ($translations as $locale => $translation) {
                $article->translations()->updateOrCreate(
                    ['locale' => $locale],
                    $translation
                );
            }

            $article->rubrics()->sync($rubricIds);
            $article->tags()->sync($tagIds);

            $this->syncVideos($article, $videos);
            $this->syncRelatedArticles($article, $relatedArticles);
        });

        $article = $this->loadArticleRelations($article);

        Log::info('API - Статья блога обновлена', [
            'id' => $article->id,
        ]);

        return response()->json(
            new BlogArticleResource($article)
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/blog-articles/{id}",
     *     operationId="deleteBlogArticle",
     *     tags={"Blog Articles"},
     *     summary="Удалить статью блога",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Статья успешно удалена"
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Статья не найдена"
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $article = BlogArticle::query()
            ->with('images')
            ->whereKey($id)
            ->firstOrFail();

        DB::transaction(function () use ($article) {
            $article->images()->detach();
            $article->rubrics()->detach();
            $article->tags()->detach();
            $article->videos()->detach();
            $article->relatedArticles()->detach();
            $article->usedInRelatedArticles()->detach();

            $article->comments()->delete();
            $article->likes()->delete();
            $article->translations()->delete();

            $article->delete();
        });

        Log::info('API - Статья блога удалена', [
            'id' => $id,
        ]);

        return response()->json(
            null,
            204
        );
    }

    /** Загрузка связей статьи. */
    private function loadArticleRelations(BlogArticle $article): BlogArticle
    {
        return $article
            ->load([
                'translations',
                'owner',
                'images',
                'rubrics.translations',
                'tags.translations',
                'videos.translations',
                'videos.images',
                'relatedArticles.translations',
                'relatedArticles.images',
            ])
            ->loadCount([
                'comments',
                'rubrics',
                'tags',
                'images',
                'videos',
                'likes',
                'relatedArticles',
            ]);
    }

    /** Синхронизация видео статьи. */
    private function syncVideos(BlogArticle $article, array $videos): void
    {
        $syncData = [];

        foreach ($videos as $index => $item) {
            $id = is_array($item) ? ($item['id'] ?? null) : $item;
            $sort = is_array($item) ? ($item['sort'] ?? $index) : $index;

            if (!$id) {
                continue;
            }

            $syncData[(int) $id] = [
                'sort' => (int) $sort,
            ];
        }

        $article->videos()->sync($syncData);
    }

    /** Синхронизация связанных статей. */
    private function syncRelatedArticles(
        BlogArticle $article,
        array $relatedArticles
    ): void {
        $syncData = [];

        foreach ($relatedArticles as $index => $item) {
            $id = is_array($item) ? ($item['id'] ?? null) : $item;

            if (!$id || (int) $id === (int) $article->id) {
                continue;
            }

            $syncData[(int) $id] = [
                'sort' => is_array($item)
                    ? (int) ($item['sort'] ?? $index)
                    : $index,
            ];
        }

        $article->relatedArticles()->sync($syncData);
    }
}
