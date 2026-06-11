<?php

namespace App\Http\Controllers\Public\Default\Blog\BlogVideo;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoResource;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\Blog\BuildsRubricTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogVideoController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use HasSidebarDataTrait;
    use BuildsRubricTreeTrait;

    /** Страница всех видео блога. */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int('publicBlogVideosPerPage', 20)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string('publicBlogVideosDefaultSort', 'sortAsc')
        );

        $view = $this->resolveView(
            $request,
            $settings->string('publicBlogVideosDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicBlogVideosProcessingMode', 'server')
        );

        $videosCount = BlogVideo::query()
            ->forPublic()
            ->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $videosCount,
                300
            );

        $videos = $this->getIndexVideos(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        $videosFound = $useServerProcessing
            ? $videos->total()
            : $videos->count();

        $videos = $useServerProcessing
            ? $this->appendUserLikes($videos, BlogVideoResource::class)
            : BlogVideoResource::collection($videos);

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/BlogVideos/Index', [
            'publicBlogVideosProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'videos' => $videos,

            'videosCount' => $videosCount,
            'videosFound' => $videosFound,

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

    /** Страница конкретного видео блога. */
    public function show(string $url): Response
    {
        $locale = app()->getLocale();

        $video = BlogVideo::query()
            ->forPublic()
            ->where('url', $url)
            ->withCount([
                'likes',
                'comments',
                'images',
            ])
            ->with([
                'translations',
                'owner',
                'images',

                'relatedVideos' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations',
                        'owner',
                        'images',
                    ])
                    ->withCount('likes')
                    ->sortByParam('sortAsc', $locale),
            ])
            ->firstOrFail();

        $video->increment('views');

        $alreadyLiked = auth()->check()
            ? $video->likes()->where('user_id', auth()->id())->exists()
            : false;

        $recommendedVideos = $video->relatedVideos->map(function ($relatedVideo) {
            $resolved = (new BlogVideoResource($relatedVideo))->resolve();

            $resolved['already_liked'] = auth()->check()
                ? $relatedVideo->likes()->where('user_id', auth()->id())->exists()
                : false;

            return $resolved;
        });

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/BlogVideos/Show', [
            'video' => array_merge(
                (new BlogVideoResource($video))->resolve(),
                ['already_liked' => $alreadyLiked]
            ),

            'recommendedVideos' => $recommendedVideos,

            'rubricTree' => $rubricTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }

    /** Лайк видео. */
    public function like(string $id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $video = BlogVideo::query()
            ->forPublic()
            ->findOrFail($id);

        $userId = auth()->id();

        $alreadyLiked = $video->likes()
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyLiked) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $video->likes()->count(),
            ]);
        }

        $video->likes()->create([
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $video->likes()->count(),
        ]);
    }

    /** Базовый запрос для списка публичных видео. */
    private function indexQuery(): Builder
    {
        return BlogVideo::query()
            ->forPublic()
            ->with([
                'translations',
                'owner',
                'images',
            ])
            ->withCount([
                'likes',
                'comments',
                'images',
            ]);
    }

    /** Получение списка публичных видео по активному режиму обработки. */
    private function getIndexVideos(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery();

        if ($useServerProcessing) {
            return $query
                ->search($search, $locale)
                ->sortByParam($sort, $locale)
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->sortByParam($sort, $locale)
            ->get();
    }
}
