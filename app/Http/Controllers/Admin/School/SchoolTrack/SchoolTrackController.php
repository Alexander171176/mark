<?php

namespace App\Http\Controllers\Admin\School\SchoolTrack;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolTrack\SchoolTrackRequest;
use App\Http\Resources\Admin\School\SchoolTrack\SchoolTrackResource;
use App\Http\Resources\Admin\School\SchoolTrack\SchoolTrackSharedResource;
use App\Models\Admin\School\SchoolTrack\SchoolTrack;
use App\Models\Admin\School\SchoolTrack\SchoolTrackImage;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;
use Throwable;

/**
 * Контроллер для управления Категориями Обучения в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное)
 * - мультиязычность, изображения
 *
 * @version 1.1 (мультиязычная архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolTrack Модель Категории
 * @see SchoolTrackRequest Запрос для создания/обновления
 */
class SchoolTrackController extends BaseSchoolAdminController
{
    /** Основная модель */
    protected string $modelClass = SchoolTrack::class;

    /** Модель изображений */
    protected string $imageModelClass = SchoolTrackImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности */
    protected string $entityLabel = 'треков';

    /** Поля переводов */
    protected array $translationFields = [
        'name',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Определение уровня вложенности */
    private function resolveLevel(?int $parentId): int
    {
        if (!$parentId) {
            return 1;
        }

        $parent = $this->baseQuery()
            ->select('id', 'parent_id')
            ->find($parentId);

        if (!$parent) {
            return 1;
        }

        return $parent->parent_id ? 3 : 2;
    }

    /** Проверка максимальной глубины */
    private function ensureAllowedLevel(?int $parentId): void
    {
        if ($this->resolveLevel($parentId) > 3) {
            throw new InvalidArgumentException('Нельзя создавать трек глубже 3 уровня вложенности.');
        }
    }

    /** Подготовка childrenRecursive для Vue */
    private function prepareTreeChildren($nodes): void
    {
        $nodes->each(function ($node) {
            if ($node->relationLoaded('childrenRecursive')) {
                $node->setRelation('children', $node->childrenRecursive);
                $this->prepareTreeChildren($node->childrenRecursive);
            } elseif ($node->relationLoaded('children')) {
                $this->prepareTreeChildren($node->children);
            }
        });
    }

    /** Список треков */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);
        $adminSchoolTracksPerPage = $settings->int('site_settings.adminSchoolTracksPerPage', 6);
        $adminSchoolTracksDefaultSort = $settings->string('site_settings.adminSchoolTracksDefaultSort', 'idDesc');
        $sort = (string) $request->query('sort', $adminSchoolTracksDefaultSort);

        try {
            $tracksTree = $this->baseQuery()
                ->root()
                ->with([
                    'translation',
                    'translations',
                    'images',
                    'childrenRecursive',
                ])
                ->withCount([
                    'children',
                    'courses',
                    'images',
                    'likes',
                ])
                ->ordered()
                ->get();

            $this->prepareTreeChildren($tracksTree);

            $tracksFlat = $this->baseQuery()
                ->with([
                    'translation',
                    'translations',
                    'parent.translation',
                    'images',
                    'courses.translation',
                ])
                ->withCount([
                    'children',
                    'courses',
                    'images',
                    'likes',
                ])
                ->sortByParam($sort, $currentLocale)
                ->get();

            return Inertia::render('Admin/School/SchoolTracks/Index', [
                'tracksTree' => SchoolTrackResource::collection($tracksTree),
                'tracks' => SchoolTrackResource::collection($tracksFlat),
                'tracksCount' => $this->baseQuery()->count(),

                'adminSchoolTracksPerPage' => $adminSchoolTracksPerPage,
                'adminSchoolTracksDefaultSort' => $sort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка school tracks: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/SchoolTracks/Index', [
                'tracksTree' => [],
                'tracks' => [],
                'tracksCount' => 0,

                'adminSchoolTracksPerPage' => $adminSchoolTracksPerPage,
                'adminSchoolTracksDefaultSort' => $sort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'error' => 'Ошибка загрузки треков.',
            ]);
        }
    }

    /** Страница создания трека */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $parents = $this->baseQuery()
            ->with(['translation', 'translations'])
            ->withCount(['children', 'courses'])
            ->ordered()
            ->get();

        return Inertia::render('Admin/School/SchoolTracks/Create', [
            'currentLocale' => $currentLocale,
            'parents' => SchoolTrackSharedResource::collection($parents),
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Создание трека */
    public function store(SchoolTrackRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];

        unset($data['translations'], $data['images'], $data['deletedImages']);

        try {
            DB::transaction(function () use ($request, $data, $translations, $imagesData) {
                $this->ensureAllowedLevel($data['parent_id'] ?? null);

                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = SchoolTrack::query()
                        ->where('parent_id', $data['parent_id'] ?? null)
                        ->max('sort');

                    $data['sort'] = is_null($maxSort) ? 1 : $maxSort + 1;
                }

                $track = SchoolTrack::create($data);

                $this->syncTranslations($track, $translations);
                $this->syncImages($track, $request, $imagesData);
            });

            return redirect()
                ->route('admin.schoolTracks.index')
                ->with('success', 'Трек успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school track: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', $e instanceof InvalidArgumentException
                    ? $e->getMessage()
                    : 'Ошибка при создании трека.');
        }
    }

    /** Редирект на редактирование */
    public function show(int $schoolTrack): RedirectResponse
    {
        return redirect()->route('admin.schoolTracks.edit', $schoolTrack);
    }

    /** Страница редактирования трека */
    public function edit(int $schoolTrack, Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $track = $this->baseQuery()
            ->with([
                'translation',
                'translations',
                'parent.translation',
                'images',
                'courses.translation',
            ])
            ->withCount([
                'children',
                'courses',
                'images',
                'likes',
            ])
            ->findOrFail($schoolTrack);

        $parents = $this->baseQuery()
            ->where('id', '!=', $track->id)
            ->with(['translation', 'translations'])
            ->withCount(['children', 'courses'])
            ->ordered()
            ->get();

        return Inertia::render('Admin/School/SchoolTracks/Edit', [
            'track' => new SchoolTrackResource($track),
            'parents' => SchoolTrackSharedResource::collection($parents),
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Обновление трека */
    public function update(SchoolTrackRequest $request, int $schoolTrack): RedirectResponse
    {
        $track = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolTrack);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset($data['translations'], $data['images'], $data['deletedImages'], $data['_method']);

        try {
            DB::transaction(function () use ($request, $track, $data, $translations, $imagesData, $deletedImageIds) {
                if (!empty($data['parent_id']) && (int) $data['parent_id'] === (int) $track->id) {
                    throw new InvalidArgumentException('Трек не может быть родителем самого себя.');
                }

                $this->ensureAllowedLevel($data['parent_id'] ?? null);

                $track->update($data);

                $this->syncTranslations($track, $translations);
                $this->syncImages($track, $request, $imagesData, $deletedImageIds);
            });

            return redirect()
                ->route('admin.schoolTracks.index')
                ->with('success', 'Трек успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school track ID ' . $track->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', $e instanceof InvalidArgumentException
                    ? $e->getMessage()
                    : 'Ошибка при обновлении трека.');
        }
    }

    /** Удаление трека */
    public function destroy(int $schoolTrack): RedirectResponse
    {
        $track = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolTrack);

        try {
            DB::transaction(function () use ($track) {
                if ($track->children()->exists()) {
                    throw new InvalidArgumentException('Нельзя удалить трек: сначала удалите или переместите дочерние треки.');
                }

                if ($track->courses()->exists()) {
                    throw new InvalidArgumentException('Нельзя удалить трек: к нему привязаны курсы.');
                }

                $imageIds = $track->images()
                    ->pluck('school_track_images.id')
                    ->toArray();

                if (!empty($imageIds)) {
                    $track->images()->detach();
                    $this->deleteImages($imageIds);
                }

                $track->translations()->delete();
                $track->likes()->delete();
                $track->delete();
            });

            return redirect()
                ->route('admin.schoolTracks.index')
                ->with('success', 'Трек успешно удалён.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school track ID ' . $track->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', $e instanceof InvalidArgumentException
                ? $e->getMessage()
                : 'Ошибка при удалении трека.');
        }
    }

    /** Массовое обновление сортировки дерева */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required_without:tracks', 'array'],
            'items.*.id' => ['required_with:items', 'integer', 'exists:school_tracks,id'],
            'items.*.sort' => ['required_with:items', 'integer', 'min:0'],
            'items.*.parent_id' => ['nullable', 'integer', 'exists:school_tracks,id'],

            'tracks' => ['required_without:items', 'array'],
            'tracks.*.id' => ['required_with:tracks', 'integer', 'exists:school_tracks,id'],
            'tracks.*.sort' => ['required_with:tracks', 'integer', 'min:0'],
            'tracks.*.parent_id' => ['nullable', 'integer', 'exists:school_tracks,id'],
        ]);

        $items = $validated['items'] ?? $validated['tracks'];
        $ids = array_column($items, 'id');

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            $message = 'Часть треков недоступна для изменения сортировки.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 400)
                : back()->with('error', $message);
        }

        try {
            DB::transaction(function () use ($items) {
                foreach ($items as $row) {
                    if (!empty($row['parent_id']) && (int) $row['parent_id'] === (int) $row['id']) {
                        throw new InvalidArgumentException('Трек не может быть родителем самого себя.');
                    }

                    $this->ensureAllowedLevel($row['parent_id'] ?? null);

                    SchoolTrack::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                        'parent_id' => $row['parent_id'] ?? null,
                    ]);
                }
            });

            $message = 'Сортировка дерева треков обновлена.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка updateSortBulk school tracks: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = $e instanceof InvalidArgumentException
                ? $e->getMessage()
                : 'Ошибка при массовом обновлении сортировки треков.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }
}
