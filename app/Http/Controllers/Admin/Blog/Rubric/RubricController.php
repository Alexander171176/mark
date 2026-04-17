<?php

namespace App\Http\Controllers\Admin\Blog\Rubric;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\Rubric\RubricRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\Blog\Rubric\RubricResource;
use App\Models\Admin\Blog\Rubric\Rubric;
use App\Models\Admin\Blog\Rubric\RubricImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;
use Throwable;

/**
 * Контроллер для управления Рубриками (Blog) в админке.
 *
 * Паттерн:
 * - локали (табы)
 * - дерево (root + children)
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - activity (single + bulk)
 * - sort + parent_id после drag&drop (bulk)
 * - moderation (approve/reject) только для admin
 * - images (Spatie)
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see Rubric
 * @see RubricRequest
 */
class RubricController extends Controller
{
    /**
     * Разрешённые локали.
     *
     * @var array|string[]
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     * Базовый query с ограничением “владелец/админ”.
     * Автор видит только свои статьи. Admin — все.
     *
     * @return Builder
     */
    private function baseQuery(): Builder
    {
        $q = Rubric::query();

        $user = auth()->user();
        if ($user && !$user->hasRole('admin')) {
            $q->where('user_id', $user->id);
        }

        return $q;
    }

    /**
     * Пересчитать level по parent_id.
     * level = 1 если parent_id = null, иначе parent.level + 1
     *
     * @param int|null $parentId
     * @return int
     */
    private function resolveLevel(?int $parentId): int
    {
        if (!$parentId) return 1;

        $parent = $this->baseQuery()->select('id', 'level')->find($parentId);
        if (!$parent) return 1;

        return ((int)$parent->level) + 1;
    }

    /**
     * Список рубрик + локали.
     * GET /admin/rubrics?locale=ru
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $adminCountRubrics = (int) config('site_settings.AdminCountRubrics', 15);
        $adminSortRubrics  = (string) config('site_settings.AdminSortRubrics', 'idDesc');

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_locale_error'));
        }

        try {
            // 1) ДЕРЕВО (полная рекурсия)
            $rubricsTree = $this->baseQuery()
                ->where('locale', $currentLocale)
                ->whereNull('parent_id')
                ->with(['owner', 'images', 'childrenRecursive'])
                ->orderBy('sort')
                ->get();

            // ✅ СЧЁТЧИК СТАТЕЙ ДЛЯ КАЖДОГО УЗЛА ДЕРЕВА (включая внуков)
            $loadArticlesCount = function ($nodes) use (&$loadArticlesCount) {
                if (!$nodes || $nodes->isEmpty()) return;

                // добавит articles_count
                $nodes->loadCount('articles');

                foreach ($nodes as $node) {
                    if ($node->relationLoaded('childrenRecursive') && $node->childrenRecursive?->isNotEmpty()) {
                        $loadArticlesCount($node->childrenRecursive);
                    }
                }
            };
            $loadArticlesCount($rubricsTree);

            // 2) alias childrenRecursive -> children (как у тебя)
            $rubricsTree->each(function ($rubric) {
                $rubric->setRelation('children', $rubric->childrenRecursive ?? collect());

                $map = function ($node) use (&$map) {
                    $node->setRelation('children', $node->childrenRecursive ?? collect());
                    if ($node->relationLoaded('childrenRecursive')) {
                        $node->childrenRecursive->each($map);
                    }
                };

                if ($rubric->relationLoaded('childrenRecursive')) {
                    $rubric->childrenRecursive->each($map);
                }
            });

            // 3) СЧЁТЧИК рубрик
            $rubricsCount = $this->baseQuery()
                ->where('locale', $currentLocale)
                ->count();

            // 4) ПЛОСКИЙ СПИСОК (✅ тут тоже считаем articles_count)
            $rubricsFlat = $this->baseQuery()
                ->where('locale', $currentLocale)
                ->with(['owner', 'images'])
                ->withCount('articles') // ✅ ВОТ ЭТОГО НЕ ХВАТАЛО
                ->orderBy('sort')
                ->get();

            return Inertia::render('Admin/Blog/Rubrics/Index', [
                'rubricsTree'  => RubricResource::collection($rubricsTree),
                'rubrics'      => RubricResource::collection($rubricsFlat),
                'rubricsCount' => $rubricsCount,

                'adminCountRubrics' => $adminCountRubrics,
                'adminSortRubrics'  => $adminSortRubrics,

                'currentLocale'    => $currentLocale,
                'availableLocales' => $this->availableLocales,
            ]);

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки рубрик для Index (locale: {$currentLocale}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Blog/Rubrics/Index', [
                'rubricsTree' => [],
                'rubrics'     => [],
                'rubricsCount'=> 0,

                'adminCountRubrics' => $adminCountRubrics,
                'adminSortRubrics'  => $adminSortRubrics,

                'currentLocale'     => $currentLocale,
                'availableLocales'  => $this->availableLocales,

                'error' => __('admin/controllers.index_error'),
            ]);
        }
    }

    /**
     * Создание Рубрики
     * GET /admin/rubrics/create
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        // TODO: Проверка прав $this->authorize('create-rubrics', Rubric::class);

        $targetLocale = $request->query('locale', config('app.fallback_locale', 'ru'));
        if (!in_array($targetLocale, $this->availableLocales, true)) {
            $targetLocale = config('app.fallback_locale', 'ru');
        }

        $parents = collect();

        try {
            $parents = $this->baseQuery()
                ->where('locale', $targetLocale)
                ->orderBy('title')
                ->get(['id', 'title', 'parent_id', 'level', 'locale']);

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки родителей рубрик для Create (locale: {$targetLocale}): ".$e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/Blog/Rubrics/Create', [
            'targetLocale' => $targetLocale,
            'parents'      => RubricResource::collection($parents),
            'availableLocales' => $this->availableLocales,
        ]);
    }

    /**
     * Сохранение новой Рубрики
     * POST /admin/rubrics
     *
     * @param RubricRequest $request
     * @return RedirectResponse
     */
    public function store(RubricRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $imagesData = $data['images'] ?? [];

        unset($data['images']);

        // owner: принудительно (если не admin)
        $user = auth()->user();
        if ($user && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderation_note']);
        } else {
            $data['user_id'] = $data['user_id'] ?? ($user?->id);
        }

        // level считаем на сервере
        $data['level'] = $this->resolveLevel($data['parent_id'] ?? null);

        try {
            DB::beginTransaction();

            // sort по умолчанию (как у learningCategories)
            if (!isset($data['sort']) || is_null($data['sort'])) {
                $maxSort = Rubric::query()
                    ->where('locale', $data['locale'])
                    ->where('parent_id', $data['parent_id'] ?? null)
                    ->max('sort');

                $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
            }

            $rubric = Rubric::create($data);

            // images (как у статей/learningCategories)
            $imageSyncData = [];
            $imageIndex = 0;

            foreach ($imagesData as $imageData) {
                $fileKey = "images.{$imageIndex}.file";

                if ($request->hasFile($fileKey)) {
                    $image = RubricImage::create([
                        'order'   => $imageData['order'] ?? 0,
                        'alt'     => $imageData['alt'] ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    try {
                        $file = $request->file($fileKey);

                        if ($file->isValid()) {
                            $image->addMedia($file)->toMediaCollection('images');
                            $imageSyncData[$image->id] = ['order' => $image->order];
                        } else {
                            Log::warning("Недопустимый файл изображения {$fileKey}
                                                    для рубрики {$rubric->id}", [
                                'error' => $file->getErrorMessage(),
                            ]);
                            $image->delete();
                            $imageIndex++;
                            continue;
                        }

                    } catch (Throwable $e) {
                        Log::error("Ошибка Spatie media-library в рубрике {$rubric->id}, индекс {$imageIndex}: ".$e->getMessage(), [
                            'exception' => $e,
                        ]);
                        $image->delete();
                        $imageIndex++;
                        continue;
                    }
                }

                $imageIndex++;
            }

            $rubric->images()->sync($imageSyncData);

            DB::commit();

            return redirect()
                ->route('admin.rubrics.index', ['locale' => $rubric->locale])
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании рубрики: ".$e->getMessage(), ['exception' => $e]);
            return back()->withInput()->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * Редактирование Рубрики
     * GET /admin/rubrics/{rubric}/edit
     *
     * @param Request $request
     * @param int $rubric
     * @return Response|RedirectResponse
     */
    public function edit(Request $request, int $rubric): Response|RedirectResponse
    {
        // TODO: Проверка прав $this->authorize('edit-rubrics', Rubric::class);

        $rubricModel = $this->baseQuery()
            ->with(['images' => fn($q) => $q->orderBy('order')])
            ->findOrFail($rubric);

        $targetLocale = $request->query('locale', $rubricModel->locale);
        if (!in_array($targetLocale, $this->availableLocales, true)) {
            $targetLocale = $rubricModel->locale;
        }

        try {
            $parents = $this->baseQuery()
                ->where('locale', $rubricModel->locale)
                ->where('id', '!=', $rubricModel->id)
                ->orderBy('title')
                ->get(['id', 'title', 'parent_id', 'level', 'locale']);

            return Inertia::render('Admin/Blog/Rubrics/Edit', [
                'targetLocale' => $targetLocale,
                'rubric'       => new RubricResource($rubricModel),
                'parents'      => RubricResource::collection($parents),
                'availableLocales' => $this->availableLocales,
                'currentLocale' => $rubricModel->locale,
            ]);

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки Edit рубрики ID {$rubricModel->id}: ".$e->getMessage(), ['exception' => $e]);
            return redirect()->route('admin.rubrics.index', ['locale' => $rubricModel->locale])
                ->with('error', __('admin/controllers.index_error'));
        }
    }

    /**
     * Обновление Рубрик
     * PUT/PATCH /admin/rubrics/{rubric}
     *
     * @param RubricRequest $request
     * @param int $rubric
     * @return RedirectResponse
     */
    public function update(RubricRequest $request, int $rubric): RedirectResponse
    {
        $rubricModel = $this->baseQuery()->with('images')->findOrFail($rubric);

        $data = $request->validated();
        $imagesData      = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset($data['images'], $data['deletedImages'], $data['_method']);

        // owner: принудительно и без модерации для автора
        $user = auth()->user();
        if ($user && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderation_note']);
        }

        // level пересчитываем при смене parent_id
        $data['level'] = $this->resolveLevel($data['parent_id'] ?? null);

        try {
            DB::transaction(function () use ($rubricModel, $request, $data, $imagesData, $deletedImageIds) {

                // 1) удалить выбранные изображения
                if (!empty($deletedImageIds)) {
                    $rubricModel->images()->detach($deletedImageIds);
                    $this->deleteImages($deletedImageIds);
                }

                // 2) обновить поля
                $rubricModel->update($data);

                // 3) images sync (как у learningCategories)
                $syncData = [];

                foreach ($imagesData as $index => $imageData) {
                    $fileKey = "images.{$index}.file";

                    // существующее
                    if (!empty($imageData['id'])) {
                        $img = RubricImage::find($imageData['id']);

                        if ($img && !in_array($img->id, $deletedImageIds, true)) {
                            $img->update([
                                'order'   => $imageData['order'] ?? $img->order,
                                'alt'     => $imageData['alt'] ?? $img->alt,
                                'caption' => $imageData['caption'] ?? $img->caption,
                            ]);

                            if ($request->hasFile($fileKey)) {
                                $img->clearMediaCollection('images');
                                $img->addMedia($request->file($fileKey))
                                    ->toMediaCollection('images');
                            }

                            $syncData[$img->id] = ['order' => $img->order];
                        }

                        // новое
                    } elseif ($request->hasFile($fileKey)) {
                        $new = RubricImage::create([
                            'order'   => $imageData['order'] ?? 0,
                            'alt'     => $imageData['alt'] ?? '',
                            'caption' => $imageData['caption'] ?? '',
                        ]);

                        $new->addMedia($request->file($fileKey))
                            ->toMediaCollection('images');

                        $syncData[$new->id] = ['order' => $new->order];
                    }
                }

                $rubricModel->images()->sync($syncData);
            });

            return redirect()
                ->route('admin.rubrics.index', ['locale' => $rubricModel->locale])
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении рубрики ID {$rubricModel->id}: ".$e->getMessage(), ['exception' => $e]);
            return back()->withInput()->with('error', __('admin/controllers.updated_error'));
        }
    }

    /**
     * Удаление Рубрики
     * DELETE /admin/rubrics/{rubric}
     *
     * @param int $rubric
     * @return RedirectResponse
     */
    public function destroy(int $rubric): RedirectResponse
    {
        $rubricModel = $this->baseQuery()->with('images')->findOrFail($rubric);

        try {
            DB::beginTransaction();

            // (опционально) запретить удаление, если есть дети
            if ($rubricModel->children()->exists()) {
                DB::rollBack();
                return back()->with('error', 'Нельзя удалить рубрику: сначала удалите/переместите дочерние рубрики.');
            }

            $imageIds = $rubricModel->images()->pluck('id')->toArray();

            if (!empty($imageIds)) {
                // 1) очистить pivot (важно при FK без каскада)
                DB::table('rubric_has_images')
                    ->where('rubric_id', $rubricModel->id)
                    ->delete();

                // 2) удалить сами изображения + медиа (Spatie)
                $this->deleteImages($imageIds);
            }

            // 3) удалить рубрику
            $rubricModel->delete();

            DB::commit();

            return redirect()
                ->route('admin.rubrics.index', ['locale' => $rubricModel->locale])
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении рубрики ID {$rubricModel->id}: ".$e->getMessage(), ['exception' => $e]);
            return back()->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Массовое удаление
     * DELETE /admin/actions/rubrics/bulk-destroy
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => ['required','array'],
            'ids.*' => ['required','integer','exists:rubrics,id'],
        ]);

        $ids = $validated['ids'];
        $count = count($ids);

        $allowedIds = $this->baseQuery()->whereIn('id', $ids)->pluck('id')->toArray();
        if (count($allowedIds) !== $count) {
            return back()->with('error', __('admin/controllers.bulk_deleted_error'));
        }

        try {
            DB::beginTransaction();

            $allImageIds = RubricImage::whereHas('rubrics', fn($q) => $q->whereIn('rubrics.id', $allowedIds))
                ->pluck('id')
                ->toArray();

            if (!empty($allImageIds)) {
                DB::table('rubric_has_images')->whereIn('rubric_id', $allowedIds)->delete();
                $this->deleteImages($allImageIds);
            }

            Rubric::whereIn('id', $allowedIds)->delete();

            DB::commit();

            return redirect()
                ->route('admin.rubrics.index')
                ->with('success', __('admin/controllers.bulk_deleted_success', ['count' => $count]));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка массового удаления рубрик: ".$e->getMessage(), ['exception' => $e]);
            return back()->with('error', __('admin/controllers.bulk_deleted_error'));
        }
    }

    /**
     * Обновление активности
     * PUT /admin/actions/rubrics/{rubric}/activity
     *
     * @param UpdateActivityRequest $request
     * @param int $rubric
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, int $rubric): RedirectResponse
    {
        $rubricModel = $this->baseQuery()->findOrFail($rubric);
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $rubricModel->activity = $validated['activity'];
            $rubricModel->save();
            DB::commit();

            return back()->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка updateActivity рубрики {$rubricModel->id}: ".$e->getMessage(), ['exception' => $e]);
            return back()->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности
     * PUT /admin/actions/rubrics/bulk-activity
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => ['required','array'],
            'ids.*'    => ['required','integer','exists:rubrics,id'],
            'activity' => ['required','boolean'],
        ]);

        $allowedIds = $this->baseQuery()->whereIn('id', $validated['ids'])->pluck('id')->toArray();
        if (count($allowedIds) !== count($validated['ids'])) {
            return back()->with('error', __('admin/controllers.bulk_activity_updated_error'));
        }

        try {
            $updatedCount = Rubric::whereIn('id', $allowedIds)->update(['activity' => $validated['activity']]);

            $msg = __('admin/controllers.bulk_activity_updated_success');
            return $request->expectsJson()
                ? response()->json(['message' => $msg, 'updatedCount' => $updatedCount])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка bulkUpdateActivity рубрик: ".$e->getMessage(), ['exception' => $e]);

            $msg = __('admin/controllers.bulk_activity_updated_error');
            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Обновление сортировки
     * PUT /admin/actions/rubrics/{rubric}/sort
     *
     * @param UpdateSortEntityRequest $request
     * @param int $rubric
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, int $rubric): RedirectResponse
    {
        $rubricModel = $this->baseQuery()->findOrFail($rubric);
        $validated = $request->validated();

        try {
            $rubricModel->sort = $validated['sort'];
            $rubricModel->save();

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка updateSort рубрики {$rubricModel->id}: ".$e->getMessage(), ['exception' => $e]);
            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки
     * PUT /admin/actions/rubrics/update-sort-bulk
     *
     * Принимает:
     * - locale
     * - items: [{id, sort, parent_id}]
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function updateSortBulk(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'locale'                    => ['required','string', Rule::in($this->availableLocales)],

            'items'                     => ['required_without:rubrics','array'],
            'items.*.id'                => ['required_with:items','integer','exists:rubrics,id'],
            'items.*.sort'              => ['required_with:items','integer','min:0'],
            'items.*.parent_id'         => ['nullable','integer'],

            // На всякий случай оставим поддержку старого payload "rubrics"
            'rubrics'                   => ['required_without:items','array'],
            'rubrics.*.id'              => ['required_with:rubrics','integer','exists:rubrics,id'],
            'rubrics.*.sort'            => ['required_with:rubrics','integer','min:0'],
            'rubrics.*.parent_id'       => ['nullable','integer'],
        ]);

        $locale = $validated['locale'];
        $data   = $validated['items'] ?? $validated['rubrics'];

        try {
            DB::transaction(function () use ($data, $locale) {

                $ids = array_column($data, 'id');

                // RMB + locale check
                $allowedIds = $this->baseQuery()
                    ->whereIn('id', $ids)
                    ->where('locale', $locale)
                    ->pluck('id')
                    ->toArray();

                if (count($allowedIds) !== count($ids)) {
                    throw new InvalidArgumentException(__('admin/controllers.invalid_rubric_ids_error'));
                }

                // validate parent ids (в этой же локали)
                $parentIds = array_values(array_unique(array_filter(array_column($data, 'parent_id'))));
                if ($parentIds) {
                    $parentsOk = Rubric::whereIn('id', $parentIds)->where('locale', $locale)->count();
                    if ($parentsOk !== count($parentIds)) {
                        throw new InvalidArgumentException(__('admin/controllers.invalid_parent_ids_error'));
                    }
                }

                foreach ($data as $row) {
                    if (isset($row['parent_id']) && (int)$row['parent_id'] === (int)$row['id']) {
                        throw new InvalidArgumentException(__('admin/controllers.parent_loop_error') . " (ID: {$row['id']})");
                    }

                    $parentId = $row['parent_id'] ?? null;

                    Rubric::whereKey($row['id'])->update([
                        'sort'      => (int)$row['sort'],
                        'parent_id' => $parentId,
                        'level'     => $this->resolveLevel($parentId),
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');
            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (InvalidArgumentException $e) {
            $msg = $e->getMessage();
            return $request->expectsJson()
                ? response()->json(['message' => $msg], 400)
                : back()->with('error', $msg);

        } catch (Throwable $e) {
            Log::error("Bulk sort rubrics error: ".$e->getMessage(), ['exception' => $e]);

            $msg = __('admin/controllers.bulk_sort_updated_error');
            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Клонирование
     * POST /admin/actions/rubrics/{rubric}/clone
     *
     * @param Request $request
     * @param int $rubric
     * @return RedirectResponse
     */
    public function clone(Request $request, int $rubric): RedirectResponse
    {
        $rubricModel = $this->baseQuery()->with(['images'])->findOrFail($rubric);

        DB::beginTransaction();

        try {
            $cloned = $rubricModel->replicate();

            $user = auth()->user();
            if ($user && !$user->hasRole('admin')) {
                $cloned->user_id = $user->id;
                unset($cloned->moderation_status, $cloned->moderation_note);
            }

            // примитивная уникализация
            $cloned->title = $rubricModel->title . '-2';
            $cloned->url   = $rubricModel->url . '-2';

            $cloned->activity = false;
            $cloned->views = 0;
            $cloned->created_at = now();
            $cloned->updated_at = now();

            // level оставляем по parent_id клона
            $cloned->level = $this->resolveLevel($cloned->parent_id);

            // sort: добавим в конец в текущем parent/locale
            $maxSort = Rubric::query()
                ->where('locale', $cloned->locale)
                ->where('parent_id', $cloned->parent_id)
                ->max('sort');
            $cloned->sort = is_null($maxSort) ? 0 : $maxSort + 1;

            $cloned->save();

            // Клонируем изображения
            $imageSyncData = [];
            foreach ($rubricModel->images as $image) {
                $clonedImage = $image->replicate();
                $clonedImage->save();

                $originalMedia = $image->getFirstMedia('images');
                if ($originalMedia) {
                    try {
                        $originalMedia->copy($clonedImage, 'images');
                        $imageSyncData[$clonedImage->id] = ['order' => $image->pivot->order ?? $image->order ?? 0];
                    } catch (Throwable $e) {
                        Log::error("Ошибка копирования медиа при клонировании рубрики: ".$e->getMessage(), [
                            'exception' => $e,
                        ]);
                    }
                }
            }
            $cloned->images()->sync($imageSyncData);

            DB::commit();

            return redirect()
                ->route('admin.rubrics.index', ['locale' => $cloned->locale])
                ->with('success', __('admin/controllers.cloned_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при клонировании рубрики ID {$rubricModel->id}: ".$e->getMessage(), ['exception' => $e]);
            return back()->withInput()->with('error', __('admin/controllers.cloned_error'));
        }
    }

    /**
     * Одобрение рубрики admin
     * PUT/POST /admin/actions/rubrics/{rubric}/approve
     *
     * @param Request $request
     * @param int $rubric
     * @return RedirectResponse|JsonResponse
     */
    public function approve(Request $request, int $rubric): RedirectResponse|JsonResponse
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('admin')) {
            abort(403);
        }

        $rubricModel = Rubric::query()->findOrFail($rubric);

        $validated = $request->validate([
            'moderation_status' => ['required', 'integer', Rule::in([0, 1, 2])], // 0/1/2
            'moderation_note'   => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $rubricModel->update([
                'moderation_status' => (int) $validated['moderation_status'],
                'moderation_note'   => $validated['moderation_note'] ?? null,
                'moderated_by'      => $user->id,
                'moderated_at'      => now(),
            ]);

            $msg = __('admin/controllers.updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg, 'rubric' => new RubricResource($rubricModel)])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка approve рубрики {$rubricModel->id}: ".$e->getMessage(), ['exception' => $e]);

            $msg = __('admin/controllers.updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Приватный метод удаления изображений (Spatie).
     *
     * @param array $imageIds
     * @return void
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) return;

        $imagesToDelete = RubricImage::whereIn('id', $imageIds)->get();

        foreach ($imagesToDelete as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }

        Log::info('Удалены записи RubricImage и их медиа', ['image_ids' => $imageIds]);
    }
}
