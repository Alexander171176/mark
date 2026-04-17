<?php

namespace App\Http\Controllers\Admin\School\CourseSchedule;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\CourseSchedule\CourseScheduleRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\School\Course\CourseResource;
use App\Http\Resources\Admin\School\CourseSchedule\CourseScheduleImageResource;
use App\Http\Resources\Admin\School\CourseSchedule\CourseScheduleResource;
use App\Http\Resources\Admin\School\InstructorProfile\InstructorProfileResource;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\CourseSchedule\CourseSchedule;
use App\Models\Admin\School\CourseSchedule\CourseScheduleImage;
use App\Models\Admin\School\InstructorProfile\InstructorProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления потоками / расписаниями курсов в админке.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (с изображениями)
 * - клонирование потока
 *
 * @see CourseSchedule
 * @see CourseScheduleRequest
 */
class CourseScheduleController extends Controller
{
    /**
     * Разрешённые локали.
     *
     * @var array<string>
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     * Список потоков.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $adminCountCourseSchedules = (int) config('site_settings.AdminCountCourseSchedules', 10);
        $adminSortCourseSchedules  = config('site_settings.AdminSortCourseSchedules', 'idDesc');

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $schedules      = collect();
        $schedulesCount = 0;

        try {
            $schedules = CourseSchedule::query()
                ->byLocale($currentLocale)
                ->with([
                    'course:id,title,slug',
                    'instructor' => function ($q) {
                        $q->select('id', 'title', 'slug', 'user_id')   // важно добавить user_id
                        ->with(['user:id,name,email']);              // подгружаем только нужные поля
                    },
                    'images' => fn ($q) => $q->orderBy('course_schedule_has_images.order'),
                ])
                ->withCount('cohortEnrollments')
                ->orderBy('sort')
                ->get();

            $schedulesCount = CourseSchedule::query()
                ->byLocale($currentLocale)
                ->count();

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки потоков (locale: {$currentLocale}): ".$e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/School/CourseSchedules/Index', [
            'schedules'                        => CourseScheduleResource::collection($schedules)->resolve(),
            'schedulesCount'                   => $schedulesCount,
            'adminCountCourseSchedules'        => $adminCountCourseSchedules,
            'adminSortCourseSchedules'         => $adminSortCourseSchedules,
            'currentLocale'                    => $currentLocale,
            'availableLocales'                 => $this->availableLocales,
        ]);
    }

    /**
     * Форма создания потока.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        // TODO: $this->authorize('create-course-schedules', CourseSchedule::class);

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        // Курсы всех локалей
        $courses = Course::query()
            ->with('instructorProfile:id,title,slug')
            ->orderBy('locale', 'desc')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'locale', 'instructor_profile_id']);

        // Преподаватели (если нужна выборка отдельно)
        $instructors = InstructorProfile::query()
            ->with('user:id,name,email')
            ->orderBy('sort')
            ->get();

        return Inertia::render('Admin/School/CourseSchedules/Create', [
            'courses'       => CourseResource::collection($courses),
            'instructors'   => InstructorProfileResource::collection($instructors),
            'currentLocale' => $currentLocale,
        ]);
    }

    /**
     * Создание нового потока.
     *
     * @param CourseScheduleRequest $request
     * @return RedirectResponse
     */
    public function store(CourseScheduleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $imagesData      = $data['images']        ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset(
            $data['images'],
            $data['deletedImages'],
        );

        try {
            DB::beginTransaction();

            /** @var CourseSchedule $schedule */
            $schedule = CourseSchedule::create($data);

            // 🔹 Обработка изображений (как в Lesson, но с CourseScheduleImage)
            $imageSyncData = [];
            $imageIndex    = 0;

            foreach ($imagesData as $imageData) {
                $fileKey = "images.{$imageIndex}.file";

                // На создании ID запрещён (см. rules), значит только новые
                if ($request->hasFile($fileKey)) {
                    $image = CourseScheduleImage::create([
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
                            Log::warning("Недопустимый файл изображения (schedule ID {$schedule->id}), индекс {$imageIndex}", [
                                'fileKey' => $fileKey,
                                'error'   => $file?->getErrorMessage(),
                            ]);
                            $image->delete();
                        }
                    } catch (Throwable $e) {
                        Log::error("Ошибка Spatie media-library для потока {$schedule->id}, индекс изображения {$imageIndex}: ".$e->getMessage(), [
                            'trace' => $e->getTraceAsString(),
                        ]);
                        $image->delete();
                    }
                }

                $imageIndex++;
            }

            if (!empty($imageSyncData)) {
                $schedule->images()->sync($imageSyncData);
            }

            DB::commit();

            Log::info('Поток успешно создан', [
                'id'    => $schedule->id,
                'title' => $schedule->title,
            ]);

            return redirect()
                ->route('admin.courseSchedules.index')
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании потока: ".$e->getMessage(), [
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
    public function show(CourseSchedule $courseSchedule): RedirectResponse
    {
        return redirect()->route('admin.course-schedules.edit', $courseSchedule);
    }

    /**
     * Форма редактирования потока.
     *
     * @param CourseSchedule $courseSchedule
     * @return Response
     */
    public function edit(CourseSchedule $courseSchedule): Response
    {
        // TODO: $this->authorize('update-course-schedules', $courseSchedule);

        $currentLocale = $courseSchedule->locale ?? config('app.fallback_locale', 'ru');

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $courseSchedule->load([
            'course:id,title,slug',
            'instructor:id,title,slug',
            'images' => fn ($q) => $q->orderBy('course_schedule_has_images.order'),
        ]);

        // Курсы всех локалей
        $courses = Course::query()
            ->with('instructorProfile:id,title,slug')
            ->orderBy('locale', 'desc')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'locale', 'instructor_profile_id']);

        // Преподаватели (если нужна выборка отдельно)
        $instructors = InstructorProfile::query()
            ->with('user:id,name,email')
            ->orderBy('sort')
            ->get();

        return Inertia::render('Admin/School/CourseSchedules/Edit', [
            'schedule'      => new CourseScheduleResource($courseSchedule),
            'courses'       => CourseResource::collection($courses),
            'instructors'   => InstructorProfileResource::collection($instructors),
            'images'        => CourseScheduleImageResource::collection($courseSchedule->images),
            'currentLocale' => $currentLocale,
        ]);
    }

    /**
     * Обновление потока.
     *
     * @param CourseScheduleRequest $request
     * @param CourseSchedule $courseSchedule
     * @return RedirectResponse
     */
    public function update(CourseScheduleRequest $request, CourseSchedule $courseSchedule): RedirectResponse
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

            // 1) Удаляем выбранные изображения
            if (!empty($deletedImageIds)) {
                $courseSchedule->images()->detach($deletedImageIds);
                $this->deleteImages($deletedImageIds);
            }

            // 2) Обновляем поля потока
            $courseSchedule->update($data);

            // 3) Обработка изображений
            $syncData = [];

            foreach ($imagesData as $index => $imageData) {
                $fileKey = "images.{$index}.file";

                // Обновление существующего изображения
                if (!empty($imageData['id'])) {
                    $img = CourseScheduleImage::find($imageData['id']);

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
                    $new = CourseScheduleImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    $new->addMedia($request->file($fileKey))
                        ->toMediaCollection('images');

                    $syncData[$new->id] = ['order' => $new->order];
                }
            }

            // 4) Синхронизируем pivot-таблицу
            if (!empty($syncData)) {
                $courseSchedule->images()->sync($syncData);
            } else {
                // Если ничего не пришло и всё удалили — отвяжем
                if (!empty($deletedImageIds) && empty($imagesData)) {
                    $courseSchedule->images()->detach();
                }
            }

            DB::commit();

            Log::info('Поток обновлён', [
                'id'    => $courseSchedule->id,
                'title' => $courseSchedule->title,
            ]);

            return redirect()
                ->route('admin.courseSchedules.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при обновлении потока ID {$courseSchedule->id}: ".$e->getMessage(), [
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
     * Удаление потока вместе с изображениями.
     *
     * @param CourseSchedule $courseSchedule
     * @return RedirectResponse
     */
    public function destroy(CourseSchedule $courseSchedule): RedirectResponse
    {
        // TODO: $this->authorize('delete-course-schedules', $courseSchedule);

        try {
            DB::beginTransaction();

            $imageIds = $courseSchedule->images()->pluck('course_schedule_images.id')->toArray();
            $courseSchedule->images()->detach();
            $this->deleteImages($imageIds);

            $courseSchedule->delete();

            DB::commit();

            Log::info('Поток удалён', ['id' => $courseSchedule->id]);

            return redirect()
                ->route('admin.courseSchedules.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении потока ID {$courseSchedule->id}: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Обновление активности одного потока.
     *
     * @param UpdateActivityRequest $request
     * @param CourseSchedule $courseSchedule
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, CourseSchedule $courseSchedule): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $courseSchedule->activity = $validated['activity'];
            $courseSchedule->save();

            DB::commit();

            Log::info("Обновлено activity потока ID {$courseSchedule->id} на {$courseSchedule->activity}");

            return back()
                ->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка обновления активности потока (ID: {$courseSchedule->id}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности потоков.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => 'required|array',
            'ids.*'    => 'integer|exists:course_schedules,id',
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
            $updatedCount = CourseSchedule::whereIn('id', $ids)->update(['activity' => $activity]);
            $message      = __('admin/controllers.bulk_activity_updated_success');

            Log::info($message, ['ids' => $ids, 'activity' => $activity]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message'      => $message,
                    'updatedCount' => $updatedCount,
                ]);
            }

            return back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error("Ошибка при массовом обновлении активности потоков: ".$e->getMessage(), [
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
     * Обновление сортировки одного потока.
     *
     * @param UpdateSortEntityRequest $request
     * @param CourseSchedule $courseSchedule
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, CourseSchedule $courseSchedule): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $originalSort         = $courseSchedule->sort;
            $courseSchedule->sort = (int) $validated['sort'];
            $courseSchedule->save();

            Log::info("Сортировка потока '{$courseSchedule->title}' (ID: {$courseSchedule->id}) изменена с {$originalSort} на {$courseSchedule->sort}");

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении сортировки потока (ID: {$courseSchedule->id}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки потоков.
     * Ожидает массив:
     * schedules: [{id: 1, sort: 10}, ...]
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'schedules'        => ['required', 'array'],
            'schedules.*.id'   => ['required', 'integer', 'exists:course_schedules,id'],
            'schedules.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['schedules'] as $row) {
                    CourseSchedule::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка массового обновления сортировки потоков: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Клонирование потока.
     *
     * @param Request $request
     * @param int $courseSchedule ID потока для клонирования
     * @return RedirectResponse
     */
    public function clone(Request $request, int $courseSchedule): RedirectResponse
    {
        // TODO: $this->authorize('create-course-schedules', CourseSchedule::class);

        /** @var CourseSchedule $schedule */
        $schedule = CourseSchedule::withTrashed()
            ->with('images')
            ->findOrFail($courseSchedule);

        DB::beginTransaction();

        try {
            $locale = $schedule->locale ?? config('app.fallback_locale', 'ru');

            $baseTitle = $schedule->title;
            $baseSlug  = $schedule->slug;

            $newTitle = $baseTitle.'-2';

            $suffix  = '-2';
            $newSlug = $baseSlug.$suffix;
            $counter = 2;

            while (
            CourseSchedule::where('locale', $locale)
                ->where('slug', $newSlug)
                ->exists()
            ) {
                $newSlug = $baseSlug.$suffix.'-'.$counter;
                $counter++;
            }

            $cloned = $schedule->replicate();

            $cloned->title    = $newTitle;
            $cloned->slug     = $newSlug;
            $cloned->activity = false;
            $cloned->views    = 0;
            $cloned->status   = 'draft';
            $cloned->created_at = now();
            $cloned->updated_at = now();

            $cloned->save();

            // 🔹 Клонируем изображения
            $imageSyncData = [];

            foreach ($schedule->images as $image) {
                $clonedImage = $image->replicate();
                $clonedImage->save();

                $originalMedia = $image->getFirstMedia('images');

                if ($originalMedia) {
                    try {
                        $originalMedia->copy($clonedImage, 'images');

                        $order = $image->pivot->order ?? $image->order ?? 0;
                        $imageSyncData[$clonedImage->id] = ['order' => $order];
                    } catch (Throwable $e) {
                        Log::error(
                            "Ошибка копирования медиа при клонировании потока ID {$schedule->id}: ".$e->getMessage(),
                            ['trace' => $e->getTraceAsString()]
                        );
                    }
                }
            }

            if (!empty($imageSyncData)) {
                $cloned->images()->sync($imageSyncData);
            }

            DB::commit();

            Log::info(
                "Поток ID {$schedule->id} успешно клонирован в ID {$cloned->id}",
                ['source_id' => $schedule->id, 'clone_id' => $cloned->id]
            );

            return redirect()
                ->route('admin.courseSchedules.index')
                ->with('success', __('admin/controllers.cloned_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error(
                "Ошибка при клонировании потока ID {$schedule->id}: ".$e->getMessage(),
                ['trace' => $e->getTraceAsString()]
            );

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.cloned_error'));
        }
    }

    /**
     * Удаление записей CourseScheduleImage и их медиа.
     *
     * @param array<int> $imageIds
     * @return void
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $imagesToDelete = CourseScheduleImage::whereIn('id', $imageIds)->get();

        foreach ($imagesToDelete as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }

        Log::info('Удалены записи CourseScheduleImage и их медиа', [
            'image_ids' => $imageIds,
        ]);
    }
}
