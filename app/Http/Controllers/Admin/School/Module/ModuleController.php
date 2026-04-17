<?php

namespace App\Http\Controllers\Admin\School\Module;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\Module\ModuleRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\School\Course\CourseResource;
use App\Http\Resources\Admin\School\Module\ModuleResource;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Module\Module;
use App\Models\Admin\School\Module\ModuleImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Модулями курсов в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное)
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see Module
 * @see ModuleRequest
 */
class ModuleController extends Controller
{
    /**
     * Разрешённые локали
     *
     * @var array|string[]
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     * Список модулей.
     * Пагинация и сортировка — на фронте, здесь отдаём список по локали.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // Конфиги под модули (если нет в site_settings — берём дефолты)
        $adminCountLearningModules = (int) config('site_settings.AdminCountLearningModules', 10);
        $adminSortLearningModules  = config('site_settings.AdminSortLearningModules', 'idDesc');

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $modules = collect();
        $modulesCount = 0;

        try {
            $modules = Module::query()
                ->byLocale($currentLocale)
                ->with([
                    'course:id,title,slug',
                    'images' => fn ($q) => $q->orderBy('module_has_images.order'),
                ])
                ->withCount([
                    'lessons',
                ])
                ->orderBy('sort')
                ->get();

            $modulesCount = Module::query()
                ->byLocale($currentLocale)
                ->count();

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки модулей (locale: {$currentLocale}): ".$e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/School/Modules/Index', [
            'modules'                 => ModuleResource::collection($modules)->resolve(),
            'modulesCount'            => $modulesCount,
            'adminCountLearningModules' => $adminCountLearningModules,
            'adminSortLearningModules'  => $adminSortLearningModules,
            'currentLocale'           => $currentLocale,
            'availableLocales'        => $this->availableLocales,
        ]);
    }

    /**
     * Форма создания модуля.
     * Передаём список курсов для селекта.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        // TODO: $this->authorize('create-modules', Module::class);

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        // Курсы всех локалей
        $courses = Course::query()
            ->orderBy('locale', 'desc')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'locale']);

        return Inertia::render('Admin/School/Modules/Create', [
            'courses'       => CourseResource::collection($courses),
            'currentLocale' => $currentLocale,
        ]);
    }

    /**
     * Сохранение нового модуля.
     * - ModuleRequest: валидация + нормализация полей
     * - обработка изображений (Spatie, ModuleImage)
     *
     * @param ModuleRequest $request
     * @return RedirectResponse
     */
    public function store(ModuleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $imagesData = $data['images'] ?? [];

        unset(
            $data['images'],
            $data['deletedImages'] // на create может и не быть, но не мешает
        );

        try {
            DB::beginTransaction();

            /** @var Module $module */
            $module = Module::create($data);

            // Обработка изображений (по шаблону InstructorProfileController)
            $imageSyncData = [];
            $imageIndex    = 0;

            foreach ($imagesData as $imageData) {
                $fileKey = "images.{$imageIndex}.file";

                if ($request->hasFile($fileKey)) {
                    $image = ModuleImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    try {
                        $file = $request->file($fileKey);

                        if ($file && $file->isValid()) {
                            $image
                                ->addMedia($file)
                                ->toMediaCollection('images');

                            $imageSyncData[$image->id] = ['order' => $image->order];
                        } else {
                            Log::warning("Недопустимый файл изображения (модуль ID {$module->id}), индекс {$imageIndex}", [
                                'fileKey' => $fileKey,
                                'error'   => $file?->getErrorMessage(),
                            ]);
                            $image->delete();
                            // переходим к следующему
                        }
                    } catch (Throwable $e) {
                        Log::error("Ошибка Spatie media-library для модуля {$module->id}, индекс изображения {$imageIndex}: ".$e->getMessage(), [
                            'trace' => $e->getTraceAsString(),
                        ]);
                        $image->delete();
                        // переходим к следующему
                    }
                }

                $imageIndex++;
            }

            if (!empty($imageSyncData)) {
                $module->images()->sync($imageSyncData);
            }

            DB::commit();

            Log::info('Модуль успешно создан', [
                'id'    => $module->id,
                'title' => $module->title,
            ]);

            return redirect()
                ->route('admin.modules.index')
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании модуля: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * show в админке просто редиректим на edit.
     */
    public function show(Module $module): RedirectResponse
    {
        return redirect()->route('admin.modules.edit', $module);
    }

    /**
     * Форма редактирования модуля.
     *
     * @param Module $module
     * @return Response
     */
    public function edit(Module $module): Response
    {
        // TODO: $this->authorize('update-modules', $module);

        // Локаль модуля
        $currentLocale = $module->locale ?? config('app.fallback_locale', 'ru');

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $module->load([
            'course:id,title,slug',
            'images' => fn ($q) => $q->orderBy('module_has_images.order'),
            'lessons:id,module_id,title,slug,sort,status',
        ]);

        // Курсы всех локалей
        $courses = Course::query()
            ->orderBy('locale', 'desc')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'locale']);

        return Inertia::render('Admin/School/Modules/Edit', [
            'module'        => new ModuleResource($module),
            'courses'       => CourseResource::collection($courses),
            'currentLocale' => $currentLocale,
        ]);
    }

    /**
     * Обновление модуля.
     * - обновление полей модуля
     * - обработка изображений
     *
     * @param ModuleRequest $request
     * @param Module $module
     * @return RedirectResponse
     */
    public function update(ModuleRequest $request, Module $module): RedirectResponse
    {
        $data = $request->validated();

        $imagesData      = $data['images']        ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset(
            $data['images'],
            $data['deletedImages'],
            $data['_method']
        );

        try {
            DB::beginTransaction();

            // 1) Удаляем выбранные изображения (и их медиа)
            if (!empty($deletedImageIds)) {
                $module->images()->detach($deletedImageIds);
                $this->deleteImages($deletedImageIds);
            }

            // 2) Обновляем поля модуля
            $module->update($data);

            // 3) Обработка изображений — по шаблону InstructorProfileController
            $syncData = [];

            foreach ($imagesData as $index => $imageData) {
                $fileKey = "images.{$index}.file";

                // Обновление существующего изображения
                if (!empty($imageData['id'])) {
                    $img = ModuleImage::find($imageData['id']);

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

                    // Новое изображение
                } elseif ($request->hasFile($fileKey)) {
                    $new = ModuleImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    $new->addMedia($request->file($fileKey))
                        ->toMediaCollection('images');

                    $syncData[$new->id] = ['order' => $new->order];
                }
            }

            // 4) Синхронизируем pivot-таблицу изображений
            $module->images()->sync($syncData);

            DB::commit();

            Log::info('Модуль обновлён', [
                'id'    => $module->id,
                'title' => $module->title,
            ]);

            return redirect()
                ->route('admin.modules.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при обновлении модуля ID {$module->id}: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'server' => __('admin/controllers.updated_error'),
                ]);
        }
    }

    /**
     * Удаление модуля вместе с изображениями.
     *
     * @param Module $module
     * @return RedirectResponse
     */
    public function destroy(Module $module): RedirectResponse
    {
        // TODO: $this->authorize('delete-modules', $module);

        try {
            DB::beginTransaction();

            $imageIds = $module->images()->pluck('module_images.id')->toArray();
            $module->images()->detach();
            $this->deleteImages($imageIds);

            $module->delete();

            DB::commit();

            Log::info('Модуль удалён', ['id' => $module->id]);

            return redirect()
                ->route('admin.modules.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении модуля ID {$module->id}: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Обновление статуса активности одного модуля.
     *
     * @param UpdateActivityRequest $request
     * @param Module $module
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, Module $module): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $module->activity = $validated['activity'];
            $module->save();

            DB::commit();

            Log::info("Обновлено activity модуля ID {$module->id} на {$module->activity}");

            return back()
                ->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка обновления активности модуля (ID: {$module->id}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности модулей.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => 'required|array',
            'ids.*'    => 'integer|exists:modules,id',
            'activity' => 'required|boolean',
        ]);

        $ids      = $validated['ids'];
        $activity = $validated['activity'];

        if (empty($ids)) {
            $message = __('admin/controllers.bulk_updated_activity_no_selection');

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 400);
            }

            return back()->with('warning', $message);
        }

        try {
            $updatedCount = Module::whereIn('id', $ids)->update(['activity' => $activity]);
            $message = __('admin/controllers.bulk_activity_updated_success');

            Log::info($message, ['ids' => $ids, 'activity' => $activity]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message'      => $message,
                    'updatedCount' => $updatedCount,
                ]);
            }

            return back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error("Ошибка при массовом обновлении активности модулей: ".$e->getMessage(), [
                'exception' => $e,
                'ids'       => $ids,
            ]);

            $errorMessage = __('admin/controllers.bulk_activity_updated_error');

            if ($request->expectsJson()) {
                return response()->json(['message' => $errorMessage], 500);
            }

            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Обновление сортировки одного модуля.
     *
     * @param UpdateSortEntityRequest $request
     * @param Module $module
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, Module $module): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $originalSort = $module->sort;
            $module->sort = (int) $validated['sort'];
            $module->save();

            Log::info("Сортировка модуля '{$module->title}' (ID: {$module->id}) изменена с {$originalSort} на {$module->sort}");

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении сортировки модуля (ID: {$module->id}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки модулей.
     * Ожидает массив:
     * modules: [{id: 1, sort: 10}, ...]
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'modules'        => ['required', 'array'],
            'modules.*.id'   => ['required', 'integer', 'exists:modules,id'],
            'modules.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['modules'] as $row) {
                    Module::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка массового обновления сортировки модулей: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Приватный метод удаления изображений модуля (Spatie MediaLibrary).
     *
     * @param array $imageIds
     * @return void
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $imagesToDelete = ModuleImage::whereIn('id', $imageIds)->get();

        foreach ($imagesToDelete as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }

        Log::info('Удалены записи ModuleImage и их медиа', [
            'image_ids' => $imageIds,
        ]);
    }
}
