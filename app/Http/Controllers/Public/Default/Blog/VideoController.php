<?php

namespace App\Http\Controllers\Public\Default\Blog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Blog\Video\VideoResource;
use App\Models\Admin\Blog\Video\Video;
use App\Traits\Public\BuildsRubricTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Контроллер для показа видео (Blog) в публичной части.
 *
 * Паттерн:
 * - список всех видео
 * - показ конкретного видео
 * - поиск
 * - сортировка
 * - пагинация
 * - лайки
 * - связанные видео
 * - left/right/main sidebar blocks
 * - rubricTree для левого меню
 *
 * @version 1.1
 */
class VideoController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use HasSidebarDataTrait;
    use BuildsRubricTreeTrait;

    /**
     * Страница всех видео.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $perPage = $this->resolvePerPage($request, 12);
        $search = $this->resolveSearch($request);
        $sort = $this->resolveSort($request, 'date_desc');

        $videos = Video::query()
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

        $videos = $this->appendUserLikes($videos, VideoResource::class);

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/Videos/Index', [
            'videos' => $videos,
            'videosCount' => $videos->total(),
            'videosFound' => $videos->total(),
            'filters' => $this->buildIndexFilters($search, $perPage, $sort),
            'rubricTree' => $rubricTree,
            'locale' => $locale,
            ...$sidebarData,
        ]);
    }

    /**
     * Страница показа конкретного видео.
     *
     * @param string $url
     * @return Response
     */
    public function show(string $url): Response
    {
        $locale = app()->getLocale();

        $video = Video::query()
            ->forPublic($locale)
            ->where('url', $url)
            ->withCount('likes')
            ->with([
                'owner',
                'images' => fn ($q) => $q->orderBy('order'),
                'relatedVideos' => fn ($q) => $q
                    ->forPublic($locale)
                    ->withCount('likes')
                    ->with([
                        'owner',
                        'images' => fn ($qq) => $qq->orderBy('order'),
                    ])
                    ->orderBy('video_related.sort', 'asc')
                    ->orderBy('videos.id', 'desc'),
            ])
            ->firstOrFail();

        $video->increment('views');

        $alreadyLiked = auth()->check()
            ? $video->likes()->where('user_id', auth()->id())->exists()
            : false;

        $recommendedVideos = $video->relatedVideos->map(function ($relatedVideo) {
            $resolved = (new VideoResource($relatedVideo))->resolve();

            $resolved['already_liked'] = auth()->check()
                ? $relatedVideo->likes()->where('user_id', auth()->id())->exists()
                : false;

            return $resolved;
        });

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/Videos/Show', [
            'video' => array_merge(
                (new VideoResource($video))->resolve(),
                ['already_liked' => $alreadyLiked]
            ),
            'recommendedVideos' => $recommendedVideos,
            'rubricTree' => $rubricTree,
            'locale' => $locale,
            ...$sidebarData,
        ]);
    }

    /**
     * Лайк видео.
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

        $video = Video::query()->findOrFail($id);

        if (!$video->activity || (int) $video->moderation_status !== 1 || $video->is_private) {
            return response()->json([
                'success' => false,
                'message' => 'Видео недоступно для лайка.',
            ], 403);
        }

        $userId = auth()->id();

        if ($video->likes()->where('user_id', $userId)->exists()) {
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
