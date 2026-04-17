<?php

namespace App\Http\Controllers\Admin\Blog\Video;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\Video\VideoRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateLeftRequest;
use App\Http\Requests\Admin\System\UpdateMainRequest;
use App\Http\Requests\Admin\System\UpdateRightRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\Blog\Video\VideoResource;
use App\Http\Resources\Admin\Blog\Video\VideoSharedResource;
use App\Models\Admin\Blog\Video\Video;
use App\Models\Admin\Blog\Video\VideoImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Видео (Blog) в админке.
 *
 * Паттерн (как BannerController):
 * - локали (табы)
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - activity/left/main/right (single + bulk)
 * - sort + drag&drop (bulk)
 * - moderation (approve/reject) только для admin
 * - images (Spatie) — НЕ МЕНЯЕМ логику
 * - video file (Spatie) — НЕ МЕНЯЕМ логику
 *
 * @version 1.1
 * @author Александр
 */
class VideoController extends Controller
{
    /**
     * Разрешённые локали.
     *
     * @var array<string>
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     * Нормализует локаль.
     */
    private function normalizeLocale(?string $locale): string
    {
        $locale = $locale ?: config('app.fallback_locale', 'ru');

        return in_array($locale, $this->availableLocales, true)
            ? $locale
            : config('app.fallback_locale', 'ru');
    }

    /**
     * Базовый query с ограничением “владелец/админ”.
     * Автор видит только свои. Admin — все.
     */
    private function baseQuery(): Builder
    {
        $q = Video::query();

        $user = auth()->user();
        if ($user && !$user->hasRole('admin')) {
            $q->where('user_id', $user->id);
        }

        return $q;
    }

    /**
     * Общие данные для страниц (локали).
     */
    private function sharedSelects(?string $locale = null): array
    {
        $locale = $this->normalizeLocale($locale);

        return [
            'currentLocale'    => $locale,
            'availableLocales' => $this->availableLocales,
        ];
    }

    /**
     * Список видео + локали.
     * GET /admin/videos?locale=ru
     */
    public function index(Request $request): Response
    {
        $adminCountVideos = (int) config('site_settings.AdminCountVideos', 15);
        $adminSortVideos  = (string) config('site_settings.AdminSortVideos', 'idDesc');

        $currentLocale = $this->normalizeLocale($request->query('locale'));

        // предупредим, если прилетела неизвестная локаль
        if (!in_array($request->query('locale', $currentLocale), $this->availableLocales, true)) {
            session()->flash('warning', __('admin/controllers.index_locale_error'));
        }

        try {
            $videos = $this->baseQuery()
                ->where('locale', $currentLocale)
                ->withCount(['images', 'comments', 'likes'])
                ->with([
                    'images' => fn ($q) => $q->orderByPivot('order', 'asc'),
                    'relatedVideos',
                    'owner',
                    'moderator:id,name',
                ])
                ->orderBy('sort')
                ->orderBy('id')
                ->get();

            return Inertia::render('Admin/Blog/Videos/Index', [
                'videos'      => VideoResource::collection($videos),
                'videosCount' => $videos->count(),

                'adminCountVideos' => $adminCountVideos,
                'adminSortVideos'  => $adminSortVideos,

                'currentLocale'    => $currentLocale,
                'availableLocales' => $this->availableLocales,
            ]);
        } catch (Throwable $e) {
            Log::error("Ошибка загрузки видео для Index (locale: {$currentLocale}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Blog/Videos/Index', [
                'videos'      => [],
                'videosCount' => 0,

                'adminCountVideos' => $adminCountVideos,
                'adminSortVideos'  => $adminSortVideos,

                'currentLocale'    => $currentLocale,
                'availableLocales' => $this->availableLocales,

                'error' => __('admin/controllers.index_error'),
            ]);
        }
    }

    /**
     * Создание видео
     * GET /admin/videos/create?locale=ru
     */
    public function create(Request $request): Response
    {
        $currentLocale = $this->normalizeLocale($request->query('locale'));

        // список для related
        $allVideos = $this->baseQuery()
            ->where('locale', $currentLocale)
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        return Inertia::render('Admin/Blog/Videos/Create', array_merge(
            [
                'related_videos' => VideoSharedResource::collection($allVideos),
            ],
            $this->sharedSelects($currentLocale)
        ));
    }

    /**
     * Сохранение нового видео
     * POST /admin/videos
     */
    public function store(VideoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $imagesData = $data['images'] ?? [];
        $relatedIds = collect($data['related_videos'] ?? [])->pluck('id')->toArray();

        unset($data['images'], $data['related_videos']);

        $user = auth()->user();

        // Владелец: принудительно. Admin может передать user_id, но по умолчанию тоже себе.
        if ($user && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            // автор не управляет модерацией
            unset($data['moderation_status'], $data['moderation_note'], $data['moderated_by'], $data['moderated_at']);
        } else {
            $data['user_id'] = $data['user_id'] ?? ($user?->id);
        }

        // Локаль: нормализуем (если поле есть в форме)
        if (array_key_exists('locale', $data)) {
            $data['locale'] = $this->normalizeLocale($data['locale']);
        }

        try {
            DB::beginTransaction();

            $video = Video::create($data);

            // local video file (Spatie) — НЕ МЕНЯЕМ
            if ($video->source_type === 'local' && $request->hasFile('video_file')) {
                $video->addMediaFromRequest('video_file')->toMediaCollection('videos');
            }

            // related videos
            $video->relatedVideos()->sync($relatedIds);

            // images (НЕ ТРОГАЮ ЛОГИКУ)
            $imageSyncData = [];
            foreach ($imagesData as $index => $imageData) {
                $fileKey = "images.{$index}.file";

                if ($request->hasFile($fileKey)) {
                    $image = VideoImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    try {
                        $file = $request->file($fileKey);

                        if ($file->isValid()) {
                            $image->addMedia($file)->toMediaCollection('images');
                            $imageSyncData[$image->id] = ['order' => $image->order];
                        } else {
                            Log::warning("Недопустимый файл изображения {$fileKey} для видео {$video->id}", [
                                'error' => $file->getErrorMessage(),
                            ]);
                            $image->delete();
                            continue;
                        }
                    } catch (Throwable $e) {
                        Log::error("Ошибка Spatie media-library (video {$video->id}, img index {$index}): {$e->getMessage()}", [
                            'trace' => $e->getTraceAsString(),
                        ]);
                        $image->delete();
                        continue;
                    }
                }
            }

            $video->images()->sync($imageSyncData);

            DB::commit();

            return redirect()->route('admin.videos.index', ['locale' => $video->locale])
                ->with('success', __('admin/controllers.created_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании видео: {$e->getMessage()}", ['exception' => $e]);

            return back()->withInput()->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * Редактирование видео
     * GET /admin/videos/{video}/edit?locale=ru
     */
    public function edit(Request $request, int $video): Response
    {
        $videoModel = $this->baseQuery()
            ->with([
                'images' => fn ($q) => $q->orderByPivot('order', 'asc'),
                'relatedVideos',
                'owner:id,name,email',
                'moderator:id,name',
            ])
            ->findOrFail($video);

        $targetLocale = $this->normalizeLocale($request->query('locale', $videoModel->locale));

        $videoUrl = $videoModel->getFirstMediaUrl('videos') ?: null;

        $allVideos = $this->baseQuery()
            ->where('id', '<>', $videoModel->id)
            ->where('locale', $targetLocale)
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        return Inertia::render('Admin/Blog/Videos/Edit', array_merge(
            [
                'video'         => new VideoResource($videoModel),
                'video_url'     => $videoUrl,
                'related_videos'=> VideoSharedResource::collection($allVideos),
                'targetLocale'  => $targetLocale,
            ],
            $this->sharedSelects($videoModel->locale)
        ));
    }

    /**
     * Обновление видео
     * PUT/PATCH /admin/videos/{video}
     */
    public function update(VideoRequest $request, int $video): RedirectResponse
    {
        $video = $this->baseQuery()->findOrFail($video);

        $data = $request->validated();

        $imagesData      = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];
        $relatedIds      = collect($data['related_videos'] ?? [])->pluck('id')->toArray();

        unset($data['images'], $data['deletedImages'], $data['related_videos'], $data['_method']);

        $user = auth()->user();
        if ($user && !$user->hasRole('admin')) {
            // автор не меняет владельца и модерацию
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderation_note'], $data['moderated_by'], $data['moderated_at']);
        }

        // Локаль: нормализуем (если поле меняется)
        if (array_key_exists('locale', $data)) {
            $data['locale'] = $this->normalizeLocale($data['locale']);
        }

        try {
            DB::beginTransaction();

            // 1) Удаляем превью-изображения
            if (!empty($deletedImageIds)) {
                $video->images()->detach($deletedImageIds);
                $this->deleteImages($deletedImageIds);
            }

            // 2) Обновляем поля видео
            $video->update($data);

            // 2.1) Обновление видеофайла (local) — НЕ МЕНЯЕМ
            if ($video->source_type === 'local' && $request->hasFile('video_file')) {
                $video->clearMediaCollection('videos');
                $video->addMediaFromRequest('video_file')->toMediaCollection('videos');
            }

            // (опционально) Если сменили source_type с local на не-local — чистим файл
            if ($video->source_type !== 'local') {
                $video->clearMediaCollection('videos');
            }

            // 3) related videos
            $video->relatedVideos()->sync($relatedIds);

            // 4) images (НЕ ТРОГАЮ ЛОГИКУ)
            $syncData = [];
            foreach ($imagesData as $index => $imageData) {
                $fileKey = "images.{$index}.file";

                if (!empty($imageData['id'])) {
                    $img = VideoImage::find($imageData['id']);

                    if ($img && !in_array($img->id, $deletedImageIds, true)) {
                        $img->update([
                            'order'   => $imageData['order']   ?? $img->order,
                            'alt'     => $imageData['alt']     ?? $img->alt,
                            'caption' => $imageData['caption'] ?? $img->caption,
                        ]);

                        if ($request->hasFile($fileKey)) {
                            $img->clearMediaCollection('images');
                            $img->addMedia($request->file($fileKey))->toMediaCollection('images');
                        }

                        $syncData[$img->id] = ['order' => $img->order];
                    }
                } elseif ($request->hasFile($fileKey)) {
                    $new = VideoImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    $new->addMedia($request->file($fileKey))->toMediaCollection('images');
                    $syncData[$new->id] = ['order' => $new->order];
                }
            }

            $video->images()->sync($syncData);

            DB::commit();

            return redirect()->route('admin.videos.index', ['locale' => $video->locale])
                ->with('success', __('admin/controllers.updated_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при обновлении видео ID {$video->id}: {$e->getMessage()}", ['exception' => $e]);

            return back()->withInput()->with('error', __('admin/controllers.updated_error'));
        }
    }

    /**
     * Удаление видео
     * DELETE /admin/videos/{video}
     */
    public function destroy(int $video): RedirectResponse
    {
        $video = $this->baseQuery()->findOrFail($video);

        try {
            DB::beginTransaction();

            // удаляем превью и их медиа
            $this->deleteImages($video->images()->pluck('id')->toArray());

            // удаляем видеофайл (Spatie)
            $video->clearMediaCollection('videos');

            $video->delete();

            DB::commit();

            return redirect()->route('admin.videos.index', ['locale' => $video->locale])
                ->with('success', __('admin/controllers.deleted_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении видео ID {$video->id}: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Массовое удаление
     * DELETE /admin/actions/videos/bulk-destroy
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:videos,id',
        ]);

        $ids   = $validated['ids'];
        $count = count($ids);

        // ограничение “владелец/админ”
        $allowedIds = $this->baseQuery()->whereIn('id', $ids)->pluck('id')->toArray();
        if (count($allowedIds) !== $count) {
            return back()->with('error', __('admin/controllers.bulk_deleted_error'));
        }

        try {
            DB::beginTransaction();

            // соберём все image_id через pivot
            $allImageIds = VideoImage::whereHas('videos', fn ($q) => $q->whereIn('videos.id', $allowedIds))
                ->pluck('id')->toArray();

            if (!empty($allImageIds)) {
                DB::table('video_has_images')->whereIn('video_id', $allowedIds)->delete();
                $this->deleteImages($allImageIds);
            }

            // удаляем видеофайлы Spatie (singleFile, но делаем корректно поштучно)
            $videos = Video::whereIn('id', $allowedIds)->get();
            foreach ($videos as $v) {
                $v->clearMediaCollection('videos');
            }

            Video::whereIn('id', $allowedIds)->delete();

            DB::commit();

            return redirect()->route('admin.videos.index')
                ->with('success', __('admin/controllers.bulk_deleted_success', ['count' => $count]));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при массовом удалении видео: ".$e->getMessage(), [
                'ids'       => $allowedIds,
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.bulk_deleted_error'));
        }
    }

    /**
     * Обновление в левой колонке
     * PUT /admin/actions/videos/{video}/left
     */
    public function updateLeft(UpdateLeftRequest $request, int $video): RedirectResponse
    {
        $video = $this->baseQuery()->findOrFail($video);
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $video->left = $validated['left'];
            $video->save();
            DB::commit();

            return back()->with('success', __('admin/controllers.left_updated_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка updateLeft видео ID {$video->id}: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.left_updated_error'));
        }
    }

    /**
     * Массовое обновление в левой колонке
     * PUT /admin/actions/videos/bulk-left
     */
    public function bulkUpdateLeft(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:videos,id',
            'left'  => 'required|boolean',
        ]);

        $allowedIds = $this->baseQuery()->whereIn('id', $data['ids'])->pluck('id')->toArray();
        if (count($allowedIds) !== count($data['ids'])) {
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_left_updated_error')], 403);
        }

        try {
            Video::whereIn('id', $allowedIds)->update(['left' => $data['left']]);
            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            Log::error('Ошибка bulkUpdateLeft: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_left_updated_error')], 500);
        }
    }

    /**
     * Обновление в главном
     * PUT /admin/actions/videos/{video}/main
     */
    public function updateMain(UpdateMainRequest $request, int $video): RedirectResponse
    {
        $video = $this->baseQuery()->findOrFail($video);
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $video->main = $validated['main'];
            $video->save();
            DB::commit();

            return back()->with('success', __('admin/controllers.main_updated_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка updateMain видео ID {$video->id}: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.main_updated_error'));
        }
    }

    /**
     * Массовое обновление в главном
     * PUT /admin/actions/videos/bulk-main
     */
    public function bulkUpdateMain(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:videos,id',
            'main'  => 'required|boolean',
        ]);

        $allowedIds = $this->baseQuery()->whereIn('id', $data['ids'])->pluck('id')->toArray();
        if (count($allowedIds) !== count($data['ids'])) {
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_main_updated_error')], 403);
        }

        try {
            Video::whereIn('id', $allowedIds)->update(['main' => $data['main']]);
            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            Log::error('Ошибка bulkUpdateMain: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_main_updated_error')], 500);
        }
    }

    /**
     * Обновление в правой колонке
     * PUT /admin/actions/videos/{video}/right
     */
    public function updateRight(UpdateRightRequest $request, int $video): RedirectResponse
    {
        $video = $this->baseQuery()->findOrFail($video);
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $video->right = $validated['right'];
            $video->save();
            DB::commit();

            return back()->with('success', __('admin/controllers.right_updated_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка updateRight видео ID {$video->id}: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.right_updated_error'));
        }
    }

    /**
     * Массовое обновление в правой колонке
     * PUT /admin/actions/videos/bulk-right
     */
    public function bulkUpdateRight(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:videos,id',
            'right' => 'required|boolean',
        ]);

        $allowedIds = $this->baseQuery()->whereIn('id', $data['ids'])->pluck('id')->toArray();
        if (count($allowedIds) !== count($data['ids'])) {
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_right_updated_error')], 403);
        }

        try {
            Video::whereIn('id', $allowedIds)->update(['right' => $data['right']]);
            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            Log::error('Ошибка bulkUpdateRight: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_right_updated_error')], 500);
        }
    }

    /**
     * Обновление активности
     * PUT /admin/actions/videos/{video}/activity
     */
    public function updateActivity(UpdateActivityRequest $request, int $video): RedirectResponse
    {
        $video = $this->baseQuery()->findOrFail($video);
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $video->activity = $validated['activity'];
            $video->save();
            DB::commit();

            return back()->with('success', __('admin/controllers.activity_updated_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка updateActivity видео ID {$video->id}: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности
     * PUT /admin/actions/videos/bulk-activity
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'      => 'required|array',
            'ids.*'    => 'required|integer|exists:videos,id',
            'activity' => 'required|boolean',
        ]);

        $allowedIds = $this->baseQuery()->whereIn('id', $validated['ids'])->pluck('id')->toArray();
        if (count($allowedIds) !== count($validated['ids'])) {
            return back()->with('error', __('admin/controllers.bulk_activity_updated_error'));
        }

        try {
            DB::beginTransaction();
            Video::whereIn('id', $allowedIds)->update(['activity' => $validated['activity']]);
            DB::commit();

            return back()->with('success', __('admin/controllers.bulk_activity_updated_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка bulkUpdateActivity видео: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.bulk_activity_updated_error'));
        }
    }

    /**
     * Обновление сортировки
     * PUT /admin/actions/videos/{video}/sort
     */
    public function updateSort(UpdateSortEntityRequest $request, int $video): RedirectResponse
    {
        $video = $this->baseQuery()->findOrFail($video);
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $video->sort = $validated['sort'];
            $video->save();
            DB::commit();

            return back()->with('success', __('admin/controllers.sort_updated_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка updateSort видео ID {$video->id}: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки (как у BannerController)
     *
     * PUT /admin/actions/videos/update-sort-bulk
     *
     * Поддержка payload:
     * - items: [{id, sort}]
     * - либо старый videos: [{id, sort}]
     */
    public function updateSortBulk(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['nullable', 'string', Rule::in($this->availableLocales)],

            'items'        => ['required_without:videos', 'array'],
            'items.*.id'   => ['required_with:items', 'integer', 'exists:videos,id'],
            'items.*.sort' => ['required_with:items', 'integer', 'min:0'],

            'videos'        => ['required_without:items', 'array'],
            'videos.*.id'   => ['required_with:videos', 'integer', 'exists:videos,id'],
            'videos.*.sort' => ['required_with:videos', 'integer', 'min:0'],
        ]);

        $data = $validated['items'] ?? $validated['videos'];

        try {
            DB::transaction(function () use ($data, $validated) {
                $ids = array_column($data, 'id');

                $q = $this->baseQuery()->whereIn('id', $ids);
                if (!empty($validated['locale'])) {
                    $q->where('locale', $validated['locale']);
                }

                $allowedIds = $q->pluck('id')->toArray();
                if (count($allowedIds) !== count($ids)) {
                    abort(403);
                }

                foreach ($data as $row) {
                    Video::whereKey($row['id'])->update(['sort' => (int) $row['sort']]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);
        } catch (Throwable $e) {
            Log::error("Bulk sort videos error: ".$e->getMessage(), ['exception' => $e]);

            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Одобрение admin (approve/reject)
     * PUT/POST /admin/actions/videos/{video}/approve
     */
    public function approve(Request $request, int $video): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('admin')) {
            abort(403);
        }

        $videoModel = Video::query()->findOrFail($video);

        $validated = $request->validate([
            'moderation_status' => ['required', 'integer', Rule::in([0, 1, 2])],
            'moderation_note'   => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $videoModel->update([
                'moderation_status' => (int) $validated['moderation_status'],
                'moderation_note'   => $validated['moderation_note'] ?? null,
                'moderated_by'      => $user->id,
                'moderated_at'      => now(),
            ]);

            $msg = __('admin/controllers.updated_success');

            return $request->expectsJson()
                ? response()->json([
                    'message' => $msg,
                    'video'   => new VideoResource($videoModel->load([
                        'images' => fn ($q) => $q->orderByPivot('order', 'asc'),
                        'relatedVideos',
                        'owner:id,name,email',
                        'moderator:id,name',
                    ])),
                ])
                : back()->with('success', $msg);
        } catch (Throwable $e) {
            Log::error("Ошибка approve видео {$videoModel->id}: ".$e->getMessage(), ['exception' => $e]);

            $msg = __('admin/controllers.updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Приватный метод удаления изображений (Spatie).
     * ЛОГИКА НЕ МЕНЯЛАСЬ.
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) return;

        $imagesToDelete = VideoImage::whereIn('id', $imageIds)->get();

        foreach ($imagesToDelete as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }

        Log::info('Удалены записи VideoImage и их медиа', ['image_ids' => $imageIds]);
    }
}
