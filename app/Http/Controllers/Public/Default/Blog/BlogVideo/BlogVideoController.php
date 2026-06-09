<?php

namespace App\Http\Controllers\Public\Default\Blog\BlogVideo;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoResource;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Traits\Public\Blog\BuildsRubricTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
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

        $perPage = $this->resolvePerPage(
            $request,
            (int) config('site_settings.publicBlogVideosPerPage', 20)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            (string) config('site_settings.publicBlogVideosDefaultSort', 'sortAsc')
        );

        $view = $this->resolveView(
            $request,
            (string) config('site_settings.publicBlogVideosDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            (string) config('site_settings.publicBlogVideosProcessingMode', 'server')
        );

        $videos = BlogVideo::query()
            ->forPublic()
            ->search($search, $locale)
            ->with([
                'translations',
                'owner',
                'images',
            ])
            ->withCount([
                'likes',
                'comments',
                'images',
            ])
            ->sortByParam($sort, $locale)
            ->paginate($perPage)
            ->withQueryString();

        $videos = $this->appendUserLikes($videos, BlogVideoResource::class);

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/BlogVideos/Index', [
            'videos' => $videos,

            'videosCount' => BlogVideo::query()
                ->forPublic()
                ->count(),

            'videosFound' => $videos->total(),

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
}
