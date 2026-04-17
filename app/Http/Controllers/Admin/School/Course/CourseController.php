<?php

namespace App\Http\Controllers\Admin\School\Course;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\Course\CourseRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\School\Course\CourseResource;
use App\Http\Resources\Admin\School\Hashtag\HashtagResource;
use App\Http\Resources\Admin\School\InstructorProfile\InstructorProfileResource;
use App\Http\Resources\Admin\School\LearningCategory\LearningCategoryResource;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Course\CourseImage;
use App\Models\Admin\School\Hashtag\Hashtag;
use App\Models\Admin\School\InstructorProfile\InstructorProfile;
use App\Models\Admin\School\LearningCategory\LearningCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Курсами в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное)
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see Course
 * @see CourseRequest
 */
class CourseController extends Controller
{
    /**
     * Разрешённые локали
     *
     * @var array|string[]
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     * Список курсов.
     * Пагинация и сортировка выполняются на фронте, здесь отдаем полный список по локали.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $adminCountLearningCourses = (int) config('site_settings.AdminCountLearningCourses', 10);
        $adminSortLearningCourses  = config('site_settings.AdminSortLearningCourses', 'idDesc');

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $courses = collect();
        $coursesCount = 0;

        try {
            $courses = Course::query()
                ->byLocale($currentLocale)
                ->with([
                    'instructorProfile' => fn ($q) => $q
                        ->select('id', 'user_id', 'title', 'slug', 'locale')
                        ->with([
                            'images' => fn ($imgQ) => $imgQ->orderBy('instructor_profile_has_images.order', 'asc'),
                            'user:id,name,email',
                        ]),

                    'learningCategories:id,name,slug',
                    'hashtags:id,name,slug,color',
                    'images' => fn($q) => $q->orderBy('course_has_images.order'),
                    'prices',
                ])
                ->withCount([
                    'modules',
                    'lessons',
                    // 'reviews', // временно убрали, пока нет поля reviews.course_id
                    'learningCategories',
                    'hashtags',
                ])
                ->orderBy('sort')
                ->get();

            $coursesCount = Course::query()
                ->byLocale($currentLocale)
                ->count();

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки курсов (locale: {$currentLocale}): ".$e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/School/Courses/Index', [
            'courses'          => CourseResource::collection($courses)->resolve(),
            'coursesCount'     => $coursesCount,
            'adminCountLearningCourses'=> $adminCountLearningCourses,
            'adminSortLearningCourses' => $adminSortLearningCourses,
            'currentLocale'    => $currentLocale,
            'availableLocales' => $this->availableLocales,
        ]);
    }

    /**
     * Форма создания курса.
     * Передаём списки: инструкторы, категории, хештеги.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        // TODO: $this->authorize('create-courses', Course::class);

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        // Преподаватели
        $instructorProfiles = InstructorProfile::query()
            ->with(['user:id,name,email'])
            ->orderBy('locale', 'desc')
            ->orderBy('id', 'desc')
            ->get(['id', 'user_id', 'title', 'locale']);

        // Категории обучения — все локали, сначала ru, потом en, kk и т.д.
        $learningCategories = LearningCategory::query()
            ->orderBy('locale', 'desc')   // 🔹 сначала ru, потом en/kk (зависит от значений)
            ->orderBy('sort')             // внутри локали по sort
            ->get(['id', 'name', 'slug', 'locale']);

        // Рекомендованные курсы
        $courses = Course::query()
            ->orderBy('locale', 'desc')
            ->orderBy('id', 'desc')
            ->get(['id', 'title', 'slug', 'locale']);

        // Хештеги — все локали, также по locale desc
        $hashtags = Hashtag::query()
            ->orderBy('locale', 'desc')
            ->orderBy('id')
            ->get(['id', 'name', 'slug', 'locale', 'color']);

        return Inertia::render('Admin/School/Courses/Create', [
            'instructorProfiles' => InstructorProfileResource::collection($instructorProfiles),
            'learningCategories' => LearningCategoryResource::collection($learningCategories),
            'hashtags'       => HashtagResource::collection($hashtags),
            'courses' => CourseResource::collection($courses),
        ]);
    }

    /**
     * Сохранение нового курса.
     * - CourseRequest: валидация + нормализация полей
     * - обработка изображений (Spatie, CourseImage)
     * - синхронизация категорий/тегов/рекомендованных курсов
     *
     * @param CourseRequest $request
     * @return RedirectResponse
     */
    public function store(CourseRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // изображения приходят в формате, аналогичном InstructorProfile
        $imagesData = $data['images'] ?? [];

        // категории/теги забираем напрямую из request (validated их сейчас не описывает)
        $learningCategoryIds = $data['learning_category_ids'] ?? [];
        $hashtagIds          = $data['hashtag_ids'] ?? [];
        $relatedIds          = $data['related_course_ids'] ?? [];

        unset(
            $data['images'],
            $data['deletedImages'],
            $data['learning_category_ids'],
            $data['hashtag_ids'],
            $data['related_course_ids']
        );

        try {
            DB::beginTransaction();

            /** @var Course $course */
            $course = Course::create($data);

            // 🔹 Синхронизация категорий
            if (!empty($learningCategoryIds)) {
                $course->learningCategories()->sync($learningCategoryIds);
            }

            // 🔹 Синхронизация хештегов
            if (!empty($hashtagIds)) {
                $course->hashtags()->sync($hashtagIds);
            }

            // 🔹 Синхронизация рекомендуемых курсов
            if (!empty($relatedIds)) {
                $course->relatedCourses()->sync($relatedIds);
            }

            // 🔹 Обработка изображений (аналогично InstructorProfileController)
            $imageSyncData = [];
            $imageIndex    = 0;

            foreach ($imagesData as $imageData) {
                $fileKey = "images.{$imageIndex}.file";

                if ($request->hasFile($fileKey)) {
                    $image = CourseImage::create([
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
                            Log::warning("Недопустимый файл изображения (курс ID {$course->id}), индекс {$imageIndex}", [
                                'fileKey' => $fileKey,
                                'error'   => $file?->getErrorMessage(),
                            ]);
                            $image->delete();
                            // переходим к следующему
                        }
                    } catch (Throwable $e) {
                        Log::error("Ошибка Spatie media-library для курса {$course->id}, индекс изображения {$imageIndex}: ".$e->getMessage(), [
                            'trace' => $e->getTraceAsString(),
                        ]);
                        $image->delete();
                    }
                }

                $imageIndex++;
            }

            if (!empty($imageSyncData)) {
                $course->images()->sync($imageSyncData);
            }

            DB::commit();

            Log::info('Курс успешно создан', [
                'id'    => $course->id,
                'title' => $course->title,
            ]);

            return redirect()
                ->route('admin.courses.index')
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании курса: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * show в админке можно просто редиректить на edit (как часто делается в админках).
     */
    public function show(Course $course): RedirectResponse
    {
        return redirect()->route('admin.courses.edit', $course);
    }

    /**
     * Форма редактирования курса.
     *  Передаём списки: инструкторы, категории, хештеги.
     *
     * @param Course $course
     * @return Response
     */
    public function edit(Course $course): Response
    {
        $currentLocale = $course->locale ?? config('app.fallback_locale', 'ru');

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $course->load([
            'instructorProfile:id,user_id,title,slug,locale',
            'learningCategories:id,name,slug,locale',
            'hashtags:id,name,slug,locale,color',
            'images' => fn ($q) => $q->orderBy('course_has_images.order', 'asc'),
            'prices',
            'relatedCourses:id,title,slug,locale',
        ]);

        $instructorProfiles = InstructorProfile::query()
            ->with(['user:id,name,email'])
            ->orderBy('locale', 'desc')
            ->orderBy('id', 'desc')
            ->get(['id', 'user_id', 'title', 'locale']);

        $learningCategories = LearningCategory::query()
            ->orderBy('locale', 'desc')
            ->orderBy('sort')
            ->get(['id', 'name', 'slug', 'locale']);

        $hashtags = Hashtag::query()
            ->orderBy('locale', 'desc')
            ->orderBy('id')
            ->get(['id', 'name', 'slug', 'locale', 'color']);

        $courses = Course::query()
            ->byLocale($currentLocale)
            ->orderBy('locale', 'desc')
            ->orderBy('id', 'desc')
            ->get(['id', 'title', 'slug', 'locale']);

        return Inertia::render('Admin/School/Courses/Edit', [
            'course' => new CourseResource($course),
            'instructorProfiles' => InstructorProfileResource::collection($instructorProfiles),
            'learningCategories' => LearningCategoryResource::collection($learningCategories),
            'hashtags' => HashtagResource::collection($hashtags),
            'courses' => CourseResource::collection($courses),
        ]);
    }

    /**
     * Обновление курса.
     * - обновление полей курса
     * - синхронизация категорий, хештегов, рекомендованных курсов
     * - обработка изображений
     *
     * @param CourseRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function update(CourseRequest $request, Course $course): RedirectResponse
    {
        $data = $request->validated();

        $imagesData      = $data['images']        ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        // категории/теги из инпута
        $learningCategoryIds = $request->input('learning_category_ids', []);
        $hashtagIds          = $request->input('hashtag_ids', []);

        // 🔹 рекомендуемые курсы
        $relatedIds  = $request->input('related_course_ids', []);

        unset(
            $data['images'],
            $data['deletedImages'],
            $data['learning_category_ids'],
            $data['hashtag_ids'],
            $data['related_course_ids'],
            $data['_method']
        );

        try {
            DB::beginTransaction();

            // 1) Удаляем выбранные изображения (и их медиа) — как у инструктора
            if (!empty($deletedImageIds)) {
                $course->images()->detach($deletedImageIds);
                $this->deleteImages($deletedImageIds);
            }

            // 2) Обновляем поля курса
            $course->update($data);

            // 3) Синхронизируем категории
            if (!empty($learningCategoryIds)) {
                $course->learningCategories()->sync($learningCategoryIds);
            } else {
                $course->learningCategories()->detach();
            }

            // 3.1) Синхронизируем хештеги
            if (!empty($hashtagIds)) {
                $course->hashtags()->sync($hashtagIds);
            } else {
                $course->hashtags()->detach();
            }

            // 3.2) 🔹 Синхронизируем рекомендуемые курсы
            if (!empty($relatedIds)) {
                $course->relatedCourses()->sync($relatedIds);
            } else {
                $course->relatedCourses()->detach();
            }

            // 4) Обработка изображений — 1:1 как в InstructorProfileController
            $syncData = [];

            foreach ($imagesData as $index => $imageData) {
                $fileKey = "images.{$index}.file";

                // Обновление существующего изображения
                if (!empty($imageData['id'])) {
                    $img = CourseImage::find($imageData['id']);

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
                    $new = CourseImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    $new->addMedia($request->file($fileKey))
                        ->toMediaCollection('images');

                    $syncData[$new->id] = ['order' => $new->order];
                }
            }

            // Синхронизация pivot-таблицы изображений
            $course->images()->sync($syncData);

            DB::commit();

            Log::info('Курс обновлен', [
                'id'    => $course->id,
                'title' => $course->title,
            ]);

            return redirect()
                ->route('admin.courses.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при обновлении курса ID {$course->id}: ".$e->getMessage(), [
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
     * Удаление курса вместе с изображениями.
     *
     * @param Course $course
     * @return RedirectResponse
     */
    public function destroy(Course $course): RedirectResponse
    {
        // TODO: $this->authorize('delete-courses', $course);

        try {
            DB::beginTransaction();

            $imageIds = $course->images()->pluck('course_images.id')->toArray();
            $course->images()->detach();
            $this->deleteImages($imageIds);

            $course->delete();

            DB::commit();

            Log::info('Курс удалён', ['id' => $course->id]);

            return redirect()
                ->route('admin.courses.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении курса ID {$course->id}: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Обновление статуса активности одного курса.
     *
     * @param UpdateActivityRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, Course $course): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $course->activity = $validated['activity'];
            $course->save();

            DB::commit();

            Log::info("Обновлено activity курса ID {$course->id} на {$course->activity}");

            return back()
                ->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка обновления активности курса (ID: {$course->id}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности курсов.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => 'required|array',
            'ids.*'    => 'integer|exists:courses,id',
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
            $updatedCount = Course::whereIn('id', $ids)->update(['activity' => $activity]);
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
            Log::error("Ошибка при массовом обновлении активности курсов: ".$e->getMessage(), [
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
     * Обновление сортировки одного курса.
     *
     * @param UpdateSortEntityRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, Course $course): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $originalSort = $course->sort;
            $course->sort = (int) $validated['sort'];
            $course->save();

            Log::info("Сортировка курса '{$course->title}' (ID: {$course->id}) изменена с {$originalSort} на {$course->sort}");

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении сортировки курса (ID: {$course->id}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки курсов.
     * Ожидает массив:
     * courses: [{id: 1, sort: 10}, ...]
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'courses'        => ['required', 'array'],
            'courses.*.id'   => ['required', 'integer', 'exists:courses,id'],
            'courses.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['courses'] as $row) {
                    Course::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка массового обновления сортировки курсов: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Приватный метод удаления изображений курса (Spatie MediaLibrary).
     *
     * @param array $imageIds
     * @return void
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $imagesToDelete = CourseImage::whereIn('id', $imageIds)->get();

        foreach ($imagesToDelete as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }

        Log::info('Удалены записи CourseImage и их медиа', [
            'image_ids' => $imageIds,
        ]);
    }
}
