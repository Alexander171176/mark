<?php

namespace App\Http\Controllers\Admin\School\LearningCategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\LearningCategory\LearningCategoryRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Resources\Admin\School\LearningCategory\LearningCategoryResource;
use App\Http\Resources\Admin\School\LearningCategory\LearningCategorySharedResource;
use App\Models\Admin\School\LearningCategory\LearningCategory;
use App\Models\Admin\School\LearningCategory\LearningCategoryImage;
use Illuminate\Auth\Access\AuthorizationException;
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
 * Контроллер для управления Категориями Обучения в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное)
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see LearningCategory Модель Категории
 * @see LearningCategoryRequest Запрос для создания/обновления
 */
class LearningCategoryController extends Controller
{
    /**
     * Разрешённые локали.
     *
     * @var array|string[]
     */
    protected array $availableLocales = ['ru', 'en', 'kk']; // Обновите список

    /**
     * Отобразить список ресурсов.
     * GET /admin/learning-categories
     * Route: admin.learningCategories
     *
     * @param Request $request
     * @return Response
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        $adminCountLearningCategories = (int) config('site_settings.AdminCountLearningCategories', 15);
        $adminSortLearningCategories  = (string) config('site_settings.AdminSortLearningCategories', 'idDesc');

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_locale_error'));
        }

        $learningCategoriesTree = collect();
        $learningCategoriesFlat = collect();
        $learningCategoriesCount = 0;

        try {
            // ДЕРЕВО
            $learningCategoriesTree = LearningCategory::query()
                ->byLocale($currentLocale)
                ->root()
                ->with([
                    'parent:id,name,slug',
                    'images',
                    'children' => fn ($q) => $q
                        ->with([
                            'parent:id,name,slug',
                            'images',
                            'children' => fn ($q2) => $q2
                                ->with([
                                    'parent:id,name,slug',
                                    'images',
                                    'children.images',
                                ])
                                ->withCount([
                                    'children',
                                    'courses',
                                ]),
                        ])
                        ->withCount([
                            'children',
                            'courses',
                        ]),
                ])
                ->withCount([
                    'children',
                    'courses',
                ])
                ->orderBy('sort')
                ->get();

            // ПЛОСКИЙ СПИСОК ДЛЯ КАРТОЧЕК
            $learningCategoriesFlat = LearningCategory::query()
                ->byLocale($currentLocale)
                ->with([
                    'parent:id,name,slug',
                    'images',
                ])
                ->withCount([
                    'children',
                    'courses',
                ])
                ->orderBy('sort')
                ->get();

            $learningCategoriesCount = LearningCategory::query()
                ->byLocale($currentLocale)
                ->count();

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки категорий для Index (locale: {$currentLocale}): "
                . $e->getMessage(), ['exception' => $e]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/School/LearningCategories/Index', [
            'learningCategoriesTree' => LearningCategoryResource::collection($learningCategoriesTree),
            'learningCategories' => LearningCategoryResource::collection($learningCategoriesFlat),
            'adminCountLearningCategories' => $adminCountLearningCategories,
            'adminSortLearningCategories'  => $adminSortLearningCategories,
            'learningCategoriesCount' => $learningCategoriesCount,
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales,
        ]);
    }

    /**
     * Форма для создания нового ресурса.
     * GET /admin/learning-categories/create
     * Route: admin.learningCategories.create
     *
     * @param Request $request
     * @return Response
     * @throws AuthorizationException
     */
    public function create(Request $request): Response
    {
        // TODO: Проверка прав $this->authorize('create-learning-categories', LearningCategory::class);

        $targetLocale = $request->query('locale', config('app.fallback_locale', 'ru'));
        if (!in_array($targetLocale, $this->availableLocales)) {
            $targetLocale = config('app.fallback_locale', 'ru');
        }

        $potentialParents = collect();
        try {
            $potentialParents = LearningCategory::query()
                ->byLocale($targetLocale)
                ->orderBy('name')
                ->get(['id', 'name', 'parent_id', 'locale']);

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки родительских категорий для Create (locale: {$targetLocale}): " . $e->getMessage(), ['exception' => $e]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/School/LearningCategories/Create', [
            'targetLocale' => $targetLocale,
            'potentialParents' => LearningCategorySharedResource::collection($potentialParents),
            'availableLocales' => $this->availableLocales,
        ]);
    }

    /**
     * Сохранение вновь созданного ресурса в хранилище.
     * POST /admin/learning-categories
     * Route: admin.learningCategories.store
     *
     * @param LearningCategoryRequest $request
     * @return RedirectResponse
     */
    public function store(LearningCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $imagesData = $data['images'] ?? [];

        unset($data['images']);

        try {
            DB::beginTransaction();

            // Присвоение sort, если не задан
            if (!isset($data['sort']) || is_null($data['sort'])) {
                $maxSort = LearningCategory::query()
                    ->where('locale', $data['locale'])
                    ->where('parent_id', $data['parent_id'] ?? null)
                    ->max('sort');
                $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
            }

            $learningCategory = LearningCategory::create($data);

            // Обработка изображений
            $imageSyncData = [];
            $imageIndex = 0;

            foreach ($imagesData as $imageData) {
                $fileKey = "images.{$imageIndex}.file";

                if ($request->hasFile($fileKey)) {
                    $image = LearningCategoryImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    try {
                        $file = $request->file($fileKey);

                        if ($file->isValid()) {
                            $image
                                ->addMedia($file)
                                ->toMediaCollection('images');

                            $imageSyncData[$image->id] = ['order' => $image->order];
                        } else {
                            Log::warning("Недопустимый файл изображения с индексом {$imageIndex}
                                                    для категории {$learningCategory->id}", [
                                'fileKey' => $fileKey,
                                'error'   => $file->getErrorMessage(),
                            ]);
                            $image->delete();
                            continue;
                        }
                    } catch (Throwable $e) {
                        Log::error("Ошибка Spatie media-library в категории {$learningCategory->id},
                                            индекс изображения - {$imageIndex}: {$e->getMessage()}", [
                            'trace' => $e->getTraceAsString(),
                        ]);
                        $image->delete();
                        continue;
                    }
                }

                $imageIndex++;
            }

            $learningCategory->images()->sync($imageSyncData);

            DB::commit();

            Log::info("Категория обучения успешно создана",
                ['id' => $learningCategory->id, 'name' => $learningCategory->name]);

            return redirect()->route('admin.learningCategories.index', ['locale' => $learningCategory->locale])
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании категории обучения: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * Отобразить форму для редактирования указанного ресурса.
     * GET /admin/learning-categories/{learningCategory}/edit
     * Route: admin.learningCategories.edit
     *
     * @param Request $request
     * @param LearningCategory $learningCategory
     * @return Response|RedirectResponse
     */
    public function edit(Request $request, LearningCategory $learningCategory): Response|RedirectResponse
    {
        // TODO: Проверка прав $this->authorize('edit-learning-categories', LearningCategory::class);

        $targetLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($targetLocale, $this->availableLocales)) {
            Log::warning("Недопустимая локаль '{$targetLocale}' в edit. Используется fallback.");
            $targetLocale = config('app.fallback_locale', 'ru');
        }

        try {
            $potentialParents = LearningCategory::query()
                ->byLocale($learningCategory->locale) // ← ключевой момент: не targetLocale, а locale самой категории
                ->where('id', '!=', $learningCategory->id) // исключить саму категорию
                ->orderBy('name')
                ->get(['id', 'name', 'parent_id', 'locale']);

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки родительских категорий для Edit (ID:
            {$learningCategory->id}, locale: {$targetLocale}): " . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('admin.learningCategories.index', ['locale' => $targetLocale])
                ->with('error', __('admin/controllers.index_error'));
        }

        // Log::info('Потенциальные родители:', $potentialParents->toArray());

        return Inertia::render('Admin/School/LearningCategories/Edit', [
            'targetLocale'    => $targetLocale,
            'learningCategory'=> new LearningCategoryResource(
                $learningCategory->loadMissing([
                    'parent',
                    'images' => fn($q) => $q->orderBy('order'),
                ])
            ),
            'potentialParents'=> LearningCategorySharedResource::collection($potentialParents),
            'availableLocales'=> $this->availableLocales,
            'currentLocale'   => $learningCategory->locale,
        ]);

    }

    /**
     * Обновление указанного ресурса в хранилище.
     * PUT/PATCH /admin/learning-categories/{learningCategory}
     * Route: admin.learningCategories.update
     *
     * @param LearningCategoryRequest $request
     * @param LearningCategory $learningCategory
     * @return RedirectResponse
     */
    public function update(LearningCategoryRequest $request, LearningCategory $learningCategory): RedirectResponse
    {
        $data = $request->validated();

        $imagesData         = $data['images'] ?? [];
        $deletedImageIds    = $data['deletedImages'] ?? [];
        $originalParentId   = $learningCategory->parent_id;
        $originalLocale     = $learningCategory->locale;
        $learningCategoryId = $learningCategory->id;

        unset(
            $data['images'],
            $data['deletedImages'],
            $data['_method'],
        );

        try {
            DB::transaction(function () use (
                $learningCategory, $data, $originalParentId, $originalLocale,
                $request, $imagesData, $deletedImageIds
            ) {
                // Удаляем изображения
                if (!empty($deletedImageIds)) {
                    $learningCategory->images()->detach($deletedImageIds);
                    $this->deleteImages($deletedImageIds);
                }

                $recalculateSort = false;

                if (
                    isset($data['parent_id']) && $data['parent_id'] != $originalParentId ||
                    isset($data['locale']) && $data['locale'] !== $originalLocale
                ) {
                    $recalculateSort = true;
                    if (isset($data['locale']) && $data['locale'] !== $originalLocale) {
                        Log::warning("Locale changed for category ID {$learningCategory->id}
                                                from {$originalLocale} to {$data['locale']}");
                    }
                }

                if ($recalculateSort && (!isset($data['sort']) || is_null($data['sort']))) {
                    $maxSort = LearningCategory::query()
                        ->where('locale', $data['locale'] ?? $originalLocale)
                        ->where('parent_id', $data['parent_id'] ?? null)
                        ->where('id', '!=', $learningCategory->id)
                        ->max('sort');
                    $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
                }

                $learningCategory->update($data);

                // Обработка изображений
                $syncData = [];

                foreach ($imagesData as $index => $imageData) {
                    $fileKey = "images.{$index}.file";

                    // Обновление существующего изображения
                    if (!empty($imageData['id'])) {
                        $img = LearningCategoryImage::find($imageData['id']);
                        if ($img && !in_array($img->id, $deletedImageIds, true)) {
                            $img->update([
                                'order'   => $imageData['order']   ?? $img->order,
                                'alt'     => $imageData['alt']     ?? $img->alt,
                                'caption' => $imageData['caption'] ?? $img->caption,
                            ]);

                            if ($request->hasFile($fileKey)) {
                                $img->clearMediaCollection('images');
                                $img->addMedia($request->file($fileKey))
                                    ->toMediaCollection('images');
                            }

                            $syncData[$img->id] = ['order' => $img->order];
                        }

                        // Добавление нового изображения
                    } elseif ($request->hasFile($fileKey)) {
                        $new = LearningCategoryImage::create([
                            'order'   => $imageData['order']   ?? 0,
                            'alt'     => $imageData['alt']     ?? '',
                            'caption' => $imageData['caption'] ?? '',
                        ]);

                        $new->addMedia($request->file($fileKey))
                            ->toMediaCollection('images');

                        $syncData[$new->id] = ['order' => $new->order];
                    }
                }

                $learningCategory->images()->sync($syncData);
            });

            Log::info("Категория обновлена (ID: {$learningCategoryId})", $learningCategory->refresh()->toArray());

            return redirect()
                ->route('admin.learningCategories.index', ['locale' => $learningCategory->locale])
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении категории (ID: {$learningCategoryId}): {$e->getMessage()}", [
                'exception' => $e,
                'data' => $data
            ]);
            return back()->withInput()
                ->with('error', __('admin/controllers.updated_error'));
        }
    }

    /**
     * Удалить указанный ресурс из хранилища.
     * DELETE /admin/learning-categories/{learningCategory}
     * Route: admin.categories.destroy
     *
     * @param LearningCategory $learningCategory
     * @return RedirectResponse
     */
    public function destroy(LearningCategory $learningCategory): RedirectResponse
    {
        $locale = $learningCategory->locale;
        $name   = $learningCategory->name; // исправили title -> name
        $id     = $learningCategory->id;

        try {
            DB::beginTransaction();
            $this->deleteImages($learningCategory->images()->pluck('id')->toArray());
            $learningCategory->delete(); // softDeletes – ок
            DB::commit();

            Log::info("Категория '{$name}' (ID: {$id}) удалена.");
            return redirect()->route('admin.learningCategories.index', ['locale' => $locale])
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении категории '{$name}' (ID: {$id}): ".$e->getMessage(), ['exception' => $e]);
            return redirect()->route('admin.learningCategories.index', ['locale' => $locale])
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Обновите статус активности ресурса.
     * PUT /admin/actions/learning-categories/{learningCategory}/activity
     * Route: admin.actions.learningCategories.updateActivity
     *
     * @param UpdateActivityRequest $request
     * @param LearningCategory $learningCategory
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, LearningCategory $learningCategory): RedirectResponse
    {
        // authorize() в UpdateActivityRequest
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $learningCategory->activity = $validated['activity'];
            $learningCategory->save();
            DB::commit();

            Log::info("Обновлено activity категории ID {$learningCategory->id} на {$learningCategory->activity}");
            return back()
                ->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка обновления активности категории (ID: {$learningCategory->id}): "
                . $e->getMessage(), ['exception' => $e]);
            return back()
                ->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Обновить статус активности для нескольких категорий.
     * PUT /admin/actions/learning-categories/bulk-activity
     * Route: admin.actions.learningCategories.bulkUpdateActivity
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     * @throws AuthorizationException
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        // TODO: Проверка прав $this->authorize('edit-learning-categories', LearningCategory::class);

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:learning_categories,id', // Проверяем, что все ID существуют
            'activity' => 'required|boolean',
        ]);

        $learningCategoryIds = $validated['ids'];
        $activity = $validated['activity'];

        if (empty($learningCategoryIds)) {
            $message = __('admin/controllers.bulk_updated_activity_no_selection');
            if ($request->expectsJson()) return response()->json(['message' => $message], 400);
            return back()->with('warning', $message);
        }

        try {
            // Обновляем одним запросом
            $updatedCount = LearningCategory::whereIn('id', $learningCategoryIds)->update(['activity' => $activity]);
            $message = __('admin/controllers.bulk_activity_updated_success');
            Log::info($message, ['ids' => $learningCategoryIds, 'activity' => $activity]);

            if ($request->expectsJson()) {
                return response()->json(['message' => $message, 'updatedCount' => $updatedCount]);
            }
            return back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error("Ошибка при массовом обновлении активности категорий: " . $e->getMessage(), ['exception' => $e, 'ids' => $learningCategoryIds]);
            $errorMessage = __('admin/controllers.bulk_activity_updated_error');

            if ($request->expectsJson()) {
                return response()->json(['message' => $errorMessage], 500);
            }
            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Обновить значение сортировки для одной категории.
     * PUT /admin/actions/learning-categories/{learningCategory}/sort
     * Route: admin.actions.learningCategories.updateSort
     *
     * @param Request $request
     * @param LearningCategory $learningCategory
     * @return RedirectResponse|JsonResponse
     */
    public function updateSort(Request $request, LearningCategory $learningCategory): RedirectResponse|JsonResponse
    {
        // TODO: Проверка прав $this->authorize('edit-learning-categories', LearningCategory::class);

        $validated = $request->validate([
            'sort' => 'required|integer|min:0',
        ]);

        try {
            $originalSort = $learningCategory->sort;
            $newSort = $validated['sort'];

            // TODO: Возможно, нужна логика для "расталкивания" других элементов,
            // если просто присвоить sort, могут появиться дубликаты на одном уровне.
            // Это усложнение, часто для одиночного обновления sort не реализуется.
            $learningCategory->sort = $newSort;
            $learningCategory->save();

            Log::info("Сортировка категории '{$learningCategory->name}' (ID: {$learningCategory->id}) изменена с {$originalSort} на {$newSort}");
            $message = __('admin/controllers.sort_updated_success');

            if ($request->expectsJson()) {
                return response()->json(['message' => $message, 'sort' => $learningCategory->sort]);
            }
            return back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении сортировки категории (ID: {$learningCategory->id}): " . $e->getMessage(), ['exception' => $e]);
            $errorMessage = __('admin/controllers.sort_updated_error');

            if ($request->expectsJson()) {
                return response()->json(['message' => $errorMessage], 500);
            }
            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Обновление порядка и родительских связей категорий после drag-and-drop.
     * PUT /admin/actions/learning-categories/update-sort-bulk
     * Route: admin.actions.learningCategories.updateSortBulk
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function updateSortBulk(Request $request): JsonResponse|RedirectResponse
    {
        // Валидация
        $validated = $request->validate([
            'locale'                   => ['required','string', Rule::in($this->availableLocales)],
            'items'                    => ['required_without:learning_categories','array'],
            'items.*.id'               => ['integer','required_with:items'],
            'items.*.sort'             => ['integer','required_with:items'],
            'items.*.parent_id'        => ['nullable','integer'],
            'learning_categories'      => ['required_without:items','array'],
            'learning_categories.*.id' => ['integer','required_with:learning_categories'],
            'learning_categories.*.sort'=> ['integer','required_with:learning_categories'],
            'learning_categories.*.parent_id'=> ['nullable','integer'],
        ]);

        $locale = $validated['locale'];
        $data   = $validated['items'] ?? $validated['learning_categories'];

        // дальнейшая логика — используем $data
        try {
            DB::transaction(function () use ($data, $locale) {
                $ids = array_column($data, 'id');

                // check IDs/locale
                $exists = LearningCategory::whereIn('id', $ids)->where('locale', $locale)->count();
                if ($exists !== count($ids)) {
                    throw new \InvalidArgumentException(__('admin/controllers.invalid_category_ids_error'));
                }

                // validate parent ids
                $parentIds = array_values(array_unique(array_filter(array_column($data, 'parent_id'))));
                if ($parentIds) {
                    $parentsOk = LearningCategory::whereIn('id', $parentIds)->where('locale', $locale)->count();
                    if ($parentsOk !== count($parentIds)) {
                        throw new \InvalidArgumentException(__('admin/controllers.invalid_parent_ids_error'));
                    }
                }

                foreach ($data as $row) {
                    if (isset($row['parent_id']) && (int)$row['parent_id'] === (int)$row['id']) {
                        throw new \InvalidArgumentException(__('admin/controllers.parent_loop_error')." (ID: {$row['id']})");
                    }
                    LearningCategory::whereKey($row['id'])->update([
                        'sort'      => (int)$row['sort'],
                        'parent_id' => $row['parent_id'] ?? null,
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');
            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (\InvalidArgumentException $e) {
            $msg = $e->getMessage();
            return $request->expectsJson()
                ? response()->json(['message' => $msg], 400)
                : back()->with('error', $msg);

        } catch (\Throwable $e) {
            Log::error("Bulk sort error: ".$e->getMessage(), ['ex'=>$e]);
            $msg = __('admin/controllers.bulk_sort_updated_error');
            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Приватный метод удаления изображений (для Spatie)
     *
     * @param array $imageIds
     * @return void
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) return;
        $imagesToDelete = LearningCategoryImage::whereIn('id', $imageIds)->get();
        foreach ($imagesToDelete as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }
        Log::info('Удалены записи LearningCategoryImage и их медиа: ', ['image_ids' => $imageIds]);
    }
}
