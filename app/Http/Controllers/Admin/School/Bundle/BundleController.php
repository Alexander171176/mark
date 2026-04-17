<?php

namespace App\Http\Controllers\Admin\School\Bundle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\Bundle\BundleRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\School\Bundle\BundleResource;
use App\Http\Resources\Admin\School\Course\CourseResource;
use App\Models\Admin\School\Bundle\Bundle;
use App\Models\Admin\School\Bundle\BundleImage;
use App\Models\Admin\School\Course\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class BundleController extends Controller
{
    /**
     * Разрешённые локали
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     * Список бандлов (по локали).
     */
    public function index(Request $request): Response
    {
        $adminCountBundles = (int) config('site_settings.AdminCountBundles', 10);
        $adminSortBundles  = config('site_settings.AdminSortBundles', 'idDesc');

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $bundles = collect();
        $bundlesCount = 0;

        try {
            $bundles = Bundle::query()
                ->byLocale($currentLocale)
                ->with([
                    'courses:id,title,slug,locale',
                    'images' => fn ($q) => $q->orderBy('bundle_has_images.order'),
                    'prices',
                ])
                ->orderBy('sort')
                ->get();

            $bundlesCount = Bundle::query()
                ->byLocale($currentLocale)
                ->count();

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки бандлов (locale: {$currentLocale}): " . $e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/School/Bundles/Index', [
            'bundles'          => BundleResource::collection($bundles)->resolve(),
            'bundlesCount'     => $bundlesCount,
            'adminCountBundles'=> $adminCountBundles,
            'adminSortBundles' => $adminSortBundles,
            'currentLocale'    => $currentLocale,
            'availableLocales' => $this->availableLocales,
        ]);
    }

    /**
     * Форма создания бандла.
     * Передаём список курсов (по локали, чтобы было удобнее).
     */
    public function create(Request $request): Response
    {
        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        // Курсы — только по локали (как в CourseController на edit для relatedCourses)
        $courses = Course::query()
            ->byLocale($currentLocale)
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'locale']);

        return Inertia::render('Admin/School/Bundles/Create', [
            'courses'          => CourseResource::collection($courses)->resolve(),
            'currentLocale'    => $currentLocale,
            'availableLocales' => $this->availableLocales,
        ]);
    }

    /**
     * Сохранение нового бандла.
     * - BundleRequest: валидация + нормализация
     * - sync курсов
     * - обработка изображений (BundleImage + Spatie) как в курсах
     */
    public function store(BundleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $imagesData = $data['images'] ?? [];                 // если добавишь в BundleRequest
        $courseIds  = $request->input('course_ids', []);     // есть у тебя в BundleRequest

        unset(
            $data['images'],
            $data['deletedImages'],
            $data['course_ids']
        );

        try {
            DB::beginTransaction();

            /** @var Bundle $bundle */
            $bundle = Bundle::create($data);

            // Курсы
            if (!empty($courseIds)) {
                $bundle->courses()->sync($courseIds);
            }

            // Изображения (аналогично CourseController::store)
            $imageSyncData = [];
            $imageIndex    = 0;

            foreach ($imagesData as $imageData) {
                $fileKey = "images.{$imageIndex}.file";

                if ($request->hasFile($fileKey)) {
                    $image = BundleImage::create([
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
                            Log::warning("Недопустимый файл изображения (bundle ID {$bundle->id}), индекс {$imageIndex}", [
                                'fileKey' => $fileKey,
                                'error'   => $file?->getErrorMessage(),
                            ]);
                            $image->delete();
                        }
                    } catch (Throwable $e) {
                        Log::error("Ошибка Spatie media-library для bundle {$bundle->id}, индекс {$imageIndex}: " . $e->getMessage(), [
                            'trace' => $e->getTraceAsString(),
                        ]);
                        $image->delete();
                    }
                }

                $imageIndex++;
            }

            if (!empty($imageSyncData)) {
                $bundle->images()->sync($imageSyncData);
            }

            DB::commit();

            Log::info('Бандл успешно создан', [
                'id'    => $bundle->id,
                'title' => $bundle->title,
            ]);

            return redirect()
                ->route('admin.bundles.index')
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при создании бандла: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * show в админке — редирект на edit.
     */
    public function show(Bundle $bundle): RedirectResponse
    {
        return redirect()->route('admin.bundles.edit', $bundle);
    }

    /**
     * Форма редактирования бандла.
     */
    public function edit(Bundle $bundle): Response
    {
        $currentLocale = $bundle->locale ?? config('app.fallback_locale', 'ru');

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $bundle->load([
            'courses:id,title,slug,locale',
            'images' => fn ($q) => $q->orderBy('bundle_has_images.order'),
            'prices',
        ]);

        // Курсы на выбор — только текущая локаль
        $courses = Course::query()
            ->byLocale($currentLocale)
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'locale']);

        return Inertia::render('Admin/School/Bundles/Edit', [
            'bundle'           => new BundleResource($bundle),
            'courses'          => CourseResource::collection($courses)->resolve(),
            'currentLocale'    => $currentLocale,
            'availableLocales' => $this->availableLocales,
        ]);
    }

    /**
     * Обновление бандла.
     * - обновление полей
     * - sync курсов
     * - обработка изображений (обновление/добавление/удаление) как у курсов
     */
    public function update(BundleRequest $request, Bundle $bundle): RedirectResponse
    {
        $data = $request->validated();

        $imagesData      = $data['images']        ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];
        $courseIds       = $request->input('course_ids', []);

        unset(
            $data['images'],
            $data['deletedImages'],
            $data['course_ids'],
            $data['_method']
        );

        try {
            DB::beginTransaction();

            // 1) Удаляем выбранные изображения
            if (!empty($deletedImageIds)) {
                $bundle->images()->detach($deletedImageIds);
                $this->deleteImages($deletedImageIds);
            }

            // 2) Обновляем поля
            $bundle->update($data);

            // 3) Синхронизируем курсы
            if (!empty($courseIds)) {
                $bundle->courses()->sync($courseIds);
            } else {
                $bundle->courses()->detach();
            }

            // 4) Обработка изображений (аналогично CourseController::update)
            $syncData = [];

            foreach ($imagesData as $index => $imageData) {
                $fileKey = "images.{$index}.file";

                // Обновление существующего изображения
                if (!empty($imageData['id'])) {
                    $img = BundleImage::find($imageData['id']);

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
                    $new = BundleImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    $new->addMedia($request->file($fileKey))
                        ->toMediaCollection('images');

                    $syncData[$new->id] = ['order' => $new->order];
                }
            }

            // pivot sync (важно: сохраняем порядок по order)
            $bundle->images()->sync($syncData);

            DB::commit();

            Log::info('Бандл обновлён', [
                'id'    => $bundle->id,
                'title' => $bundle->title,
            ]);

            return redirect()
                ->route('admin.bundles.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при обновлении бандла ID {$bundle->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['server' => __('admin/controllers.updated_error')]);
        }
    }

    /**
     * Удаление бандла вместе с изображениями.
     */
    public function destroy(Bundle $bundle): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $imageIds = $bundle->images()->pluck('bundle_images.id')->toArray();

            $bundle->images()->detach();
            $this->deleteImages($imageIds);

            $bundle->courses()->detach();
            $bundle->delete();

            DB::commit();

            Log::info('Бандл удалён', ['id' => $bundle->id]);

            return redirect()
                ->route('admin.bundles.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при удалении бандла ID {$bundle->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Обновление активности одного бандла.
     */
    public function updateActivity(UpdateActivityRequest $request, Bundle $bundle): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $bundle->activity = $validated['activity'];
            $bundle->save();

            DB::commit();

            Log::info("Обновлено activity бандла ID {$bundle->id} на {$bundle->activity}");

            return back()->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка обновления активности бандла (ID: {$bundle->id}): " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности бандлов.
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => 'required|array',
            'ids.*'    => 'integer|exists:bundles,id',
            'activity' => 'required|boolean',
        ]);

        $ids      = $validated['ids'];
        $activity = $validated['activity'];

        if (empty($ids)) {
            $message = __('admin/controllers.bulk_updated_activity_no_selection');

            return $request->expectsJson()
                ? response()->json(['message' => $message], 400)
                : back()->with('warning', $message);
        }

        try {
            $updatedCount = Bundle::whereIn('id', $ids)->update(['activity' => $activity]);
            $message = __('admin/controllers.bulk_activity_updated_success');

            Log::info($message, ['ids' => $ids, 'activity' => $activity]);

            return $request->expectsJson()
                ? response()->json(['message' => $message, 'updatedCount' => $updatedCount])
                : back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error("Ошибка при массовом обновлении активности бандлов: " . $e->getMessage(), [
                'exception' => $e,
                'ids'       => $ids,
            ]);

            $errorMessage = __('admin/controllers.bulk_activity_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $errorMessage], 500)
                : back()->with('error', $errorMessage);
        }
    }

    /**
     * Обновление сортировки одного бандла.
     */
    public function updateSort(UpdateSortEntityRequest $request, Bundle $bundle): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $originalSort = $bundle->sort;

            $bundle->sort = (int) $validated['sort'];
            $bundle->save();

            Log::info("Сортировка бандла '{$bundle->title}' (ID: {$bundle->id}) изменена с {$originalSort} на {$bundle->sort}");

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении сортировки бандла (ID: {$bundle->id}): " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки бандлов.
     * Ожидает: bundles: [{id: 1, sort: 10}, ...]
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'bundles'        => ['required', 'array'],
            'bundles.*.id'   => ['required', 'integer', 'exists:bundles,id'],
            'bundles.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['bundles'] as $row) {
                    Bundle::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка массового обновления сортировки бандлов: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Удаление изображений BundleImage (и медиа Spatie).
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $imagesToDelete = BundleImage::whereIn('id', $imageIds)->get();

        foreach ($imagesToDelete as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }

        Log::info('Удалены записи BundleImage и их медиа', [
            'image_ids' => $imageIds,
        ]);
    }
}
