<?php

namespace App\Http\Controllers\Admin\School\Assignment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\Assignment\AssignmentRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateLeftRequest;
use App\Http\Requests\Admin\System\UpdateMainRequest;
use App\Http\Requests\Admin\System\UpdateRightRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\School\Assignment\AssignmentResource;
use App\Http\Resources\Admin\School\Course\CourseResource;
use App\Http\Resources\Admin\School\InstructorProfile\InstructorProfileResource;
use App\Http\Resources\Admin\School\Lesson\LessonResource;
use App\Http\Resources\Admin\School\Module\ModuleResource;
use App\Models\Admin\School\Assignment\Assignment;
use App\Models\Admin\School\Assignment\AssignmentImage;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\InstructorProfile\InstructorProfile;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Заданиями (Assignments) в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное и массовое)
 * - клонирование заданий
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see Assignment
 * @see AssignmentRequest
 */
class AssignmentController extends Controller
{
    /**
     * Разрешённые локали.
     *
     * @var array|string[]
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     *  Отображение списка.
     *  Загружает пагинированный список с сортировкой по настройкам.
     *  Передает данные для отображения и настройки пагинации/сортировки.
     *  Пагинация и сортировка выполняются на фронтенде.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $adminCountAssignments = (int) config('site_settings.AdminCountAssignments', 10);
        $adminSortAssignments  = config('site_settings.AdminSortAssignments', 'idDesc');

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $assignments      = collect();
        $assignmentsCount = 0;

        try {
            $assignments = Assignment::query()
                ->byLocale($currentLocale)
                ->with([
                    'course',
                    'module',
                    'lesson',
                    'instructor.user', // 👈 добавили user у инструктора
                    'images' => fn($q) => $q->orderBy('assignment_has_images.order'),
                ])
                ->orderBy('sort')
                ->get();

            $assignmentsCount = Assignment::query()
                ->byLocale($currentLocale)
                ->count();

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки заданий (locale: {$currentLocale}): " . $e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/School/Assignments/Index', [
            'assignments'          => AssignmentResource::collection($assignments)->resolve(),
            'assignmentsCount'     => $assignmentsCount,
            'adminCountAssignments'=> $adminCountAssignments,
            'adminSortAssignments' => $adminSortAssignments,
            'currentLocale'        => $currentLocale,
            'availableLocales'     => $this->availableLocales,
        ]);
    }

    /**
     * Форма создания задания.
     * Передаём списки: курсы, модули, уроки, преподаватели.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        // TODO: $this->authorize('create-assignments', Assignment::class);

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

        // Модули всех локалей с курсом
        $modules = Module::query()
            ->with(['course:id,title,locale'])
            ->orderBy('locale', 'desc')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'locale', 'course_id']);

        // Уроки всех локалей + модуль + курс
        $lessons = Lesson::query()
            ->with([
                'module' => function ($q) {
                    $q->select('id', 'title', 'slug', 'course_id', 'locale')
                        ->with(['course:id,title,locale']);
                },
            ])
            ->orderBy('locale', 'desc')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'module_id', 'locale']);

        // Преподаватели (без локали, глобально)
        $instructors = InstructorProfile::query()
            ->with(['user:id,name,email'])
            ->orderByDesc('sort')   // ⬅️ sort DESC
            ->orderBy('id')
            ->get(['id', 'user_id', 'title']);

        return Inertia::render('Admin/School/Assignments/Create', [
            'courses'        => CourseResource::collection($courses),
            'modules'        => ModuleResource::collection($modules),
            'lessons'        => LessonResource::collection($lessons),
            'instructors'    => InstructorProfileResource::collection($instructors),
            'currentLocale'  => $currentLocale,
        ]);
    }

    /**
     * Сохранение нового задания.
     *
     * - AssignmentRequest: валидация + нормализация полей
     * - обработка изображений (Spatie, AssignmentImage)
     *
     * @param AssignmentRequest $request
     * @return RedirectResponse
     */
    public function store(AssignmentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // изображения для задания
        $imagesData = $data['images'] ?? [];

        unset(
            $data['images'],
            $data['deletedImages'],
        );

        try {
            DB::beginTransaction();

            /** @var Assignment $assignment */
            $assignment = Assignment::create($data);

            // 🔹 Обработка изображений (паттерн как у уроков)
            $imageSyncData = [];
            $imageIndex    = 0;

            foreach ($imagesData as $imageData) {
                $fileKey = "images.{$imageIndex}.file";

                if ($request->hasFile($fileKey)) {
                    $image = AssignmentImage::create([
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
                            Log::warning("Недопустимый файл изображения (assignment ID {$assignment->id}), индекс {$imageIndex}", [
                                'fileKey' => $fileKey,
                                'error'   => $file?->getErrorMessage(),
                            ]);
                            $image->delete();
                        }
                    } catch (Throwable $e) {
                        Log::error("Ошибка Spatie media-library для задания {$assignment->id}, индекс изображения {$imageIndex}: " . $e->getMessage(), [
                            'trace' => $e->getTraceAsString(),
                        ]);
                        $image->delete();
                    }
                }

                $imageIndex++;
            }

            if (!empty($imageSyncData)) {
                $assignment->images()->sync($imageSyncData);
            }

            DB::commit();

            Log::info('Задание успешно создано', [
                'id'    => $assignment->id,
                'title' => $assignment->title,
            ]);

            return redirect()
                ->route('admin.assignments.index')
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании задания: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * show в админке — редирект на edit.
     *
     * @param Assignment $assignment
     * @return RedirectResponse
     */
    public function show(Assignment $assignment): RedirectResponse
    {
        return redirect()->route('admin.assignments.edit', $assignment);
    }

    /**
     * Форма редактирования задания.
     * Передаём связки: курсы, модули, уроки, преподаватели.
     *
     * @param Assignment $assignment
     * @return Response
     */
    public function edit(Assignment $assignment): Response
    {
        // TODO: $this->authorize('update-assignments', $assignment);

        // Локаль задания
        $currentLocale = $assignment->locale ?? config('app.fallback_locale', 'ru');

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        // Подгружаем нужные связи задания
        $assignment->load([
            'course',
            'module:id,title,slug',
            'lesson:id,title,slug',
            'instructor',
            'images' => fn($q) => $q->orderBy('assignment_has_images.order'),
        ]);

        // Курсы всех локалей
        $courses = Course::query()
            ->orderBy('locale', 'desc')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'locale']);

        // Модули всех локалей с курсом
        $modules = Module::query()
            ->with(['course:id,title,locale'])
            ->orderBy('locale', 'desc')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'locale', 'course_id']);

        // Уроки всех локалей + модуль + курс
        $lessons = Lesson::query()
            ->with([
                'module' => function ($q) {
                    $q->select('id', 'title', 'slug', 'course_id', 'locale')
                        ->with(['course:id,title,locale']);
                },
            ])
            ->orderBy('locale', 'desc')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'module_id', 'locale']);

        // Преподаватели
        $instructors = InstructorProfile::query()
            ->with(['user:id,name,email'])
            ->orderByDesc('sort')
            ->orderBy('id')
            ->get(['id', 'user_id', 'title']);

        return Inertia::render('Admin/School/Assignments/Edit', [
            'assignment'    => new AssignmentResource($assignment),
            'courses'       => CourseResource::collection($courses),
            'modules'       => ModuleResource::collection($modules),
            'lessons'       => LessonResource::collection($lessons),
            'instructors'   => InstructorProfileResource::collection($instructors),
            'currentLocale' => $currentLocale,
        ]);
    }

    /**
     * Обновление задания:
     * - обновление полей
     * - обработка изображений
     *
     * @param AssignmentRequest $request
     * @param Assignment $assignment
     * @return RedirectResponse
     */
    public function update(AssignmentRequest $request, Assignment $assignment): RedirectResponse
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
                $assignment->images()->detach($deletedImageIds);
                $this->deleteImages($deletedImageIds);
            }

            // 2) Обновляем поля задания
            $assignment->update($data);

            // 3) Обработка изображений
            $syncData = [];

            foreach ($imagesData as $index => $imageData) {
                $fileKey = "images.{$index}.file";

                // Обновление существующего изображения
                if (!empty($imageData['id'])) {
                    $img = AssignmentImage::find($imageData['id']);

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
                    $new = AssignmentImage::create([
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
            $assignment->images()->sync($syncData);

            DB::commit();

            Log::info('Задание обновлено', [
                'id'    => $assignment->id,
                'title' => $assignment->title,
            ]);

            return redirect()
                ->route('admin.assignments.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при обновлении задания ID {$assignment->id}: " . $e->getMessage(), [
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
     * Удаление задания вместе с изображениями.
     *
     * @param Assignment $assignment
     * @return RedirectResponse
     */
    public function destroy(Assignment $assignment): RedirectResponse
    {
        // TODO: $this->authorize('delete-assignments', $assignment);

        try {
            DB::beginTransaction();

            $imageIds = $assignment->images()->pluck('assignment_images.id')->toArray();
            $assignment->images()->detach();
            $this->deleteImages($imageIds);

            $assignment->delete();

            DB::commit();

            Log::info('Задание удалено', ['id' => $assignment->id]);

            return redirect()
                ->route('admin.assignments.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении задания ID {$assignment->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Массовое удаление указанных.
     * Принимает массив ID в теле запроса.
     *
     * @param Request $request Запрос, содержащий массив 'ids'.
     * @return RedirectResponse Редирект назад с сообщением.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        // TODO: Проверка прав $this->authorize('delete-assignments');

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:assignments,id',
        ]);

        $assignmentIds = $validated['ids'];
        $count = count($assignmentIds); // Получаем количество для сообщения

        try {
            DB::beginTransaction(); // Оставляем транзакцию для массовой операции

            $allImageIds = AssignmentImage::whereHas('assignments', fn($q) => $q
                ->whereIn('assignments.id', $assignmentIds))
                ->pluck('id')->toArray();

            if (!empty($allImageIds)) {
                DB::table('assignment_has_images')
                    ->whereIn('assignment_id', $assignmentIds)->delete();
                $this->deleteImages($allImageIds);
            }

            Assignment::whereIn('id', $assignmentIds)->delete();
            DB::commit();

            Log::info('Задания удалены: ', $assignmentIds);
            return redirect()->route('admin.assignments.index')
                ->with('success', __('admin/controllers.bulk_deleted_success',
                    ['count' => $count]));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при массовом удалении заданий: " . $e->getMessage(),
                ['ids' => $assignmentIds]);
            return back()
                ->with('error', __('admin/controllers.bulk_deleted_error'));
        }
    }

    /**
     * Включение Задания в левом сайдбаре
     * Использует Route Model Binding и UpdateLeftRequest.
     *
     * @param UpdateLeftRequest $request
     * @param Assignment $assignment
     * @return RedirectResponse
     */
    public function updateLeft(UpdateLeftRequest $request, Assignment $assignment): RedirectResponse
    {
        // authorize() в UpdateLeftRequest
        $validated = $request->validated();

        try {
            $assignment->left = $validated['left'];
            $assignment->save();

            Log::info("Обновлено значение активации в левой колонке для задания ID {$assignment->id}");
            return redirect()->route('admin.assignments.index')
                ->with('success', __('admin/controllers.left_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка обновления значение в левой колонке задания ID
                        {$assignment->id}: " . $e->getMessage());
            return back()
                ->with('error', __('admin/controllers.left_updated_error'));
        }
    }

    /**
     * Включение Главными
     * Использует Route Model Binding и UpdateMainRequest.
     *
     * @param UpdateMainRequest $request
     * @param Assignment $assignment
     * @return RedirectResponse
     */
    public function updateMain(UpdateMainRequest $request, Assignment $assignment): RedirectResponse
    {
        // authorize() в UpdateMainRequest
        $validated = $request->validated();

        try {
            $assignment->main = $validated['main'];
            $assignment->save();

            Log::info("Обновлено значение активации в главном для задания ID {$assignment->id}");
            return redirect()->route('admin.assignments.index')
                ->with('success', __('admin/controllers.main_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка обновления значение в главном задания ID {$assignment->id}: "
                . $e->getMessage());
            return back()
                ->with('error', __('admin/controllers.main_updated_error'));
        }
    }

    /**
     * Включение Задания в правом сайдбаре
     * Использует Route Model Binding и UpdateRightRequest.
     *
     * @param UpdateRightRequest $request
     * @param Assignment $assignment
     * @return RedirectResponse
     */
    public function updateRight(UpdateRightRequest $request, Assignment $assignment): RedirectResponse
    {
        // authorize() в UpdateRightRequest
        $validated = $request->validated();

        try {
            $assignment->right = $validated['right'];
            $assignment->save();

            Log::info("Обновлено значение активации в правой колонке для задания ID {$assignment->id}");
            return redirect()->route('admin.assignments.index')
                ->with('success', __('admin/controllers.right_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка обновления значение в правой колонке задания ID {$assignment->id}: "
                . $e->getMessage());
            return back()
                ->with('error', __('admin/controllers.right_updated_error'));
        }
    }

    /**
     * Обновление статуса активности одного.
     *
     * @param UpdateActivityRequest $request
     * @param Assignment $assignment
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, Assignment $assignment): RedirectResponse
    {
        // authorize() в UpdateActivityRequest
        $validated = $request->validated();

        try {
            $assignment->activity = $validated['activity'];
            $assignment->save();

            Log::info("Обновлено activity задания ID {$assignment->id} на {$assignment->activity}");
            return back()
                ->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка обновления активности задания ID {$assignment->id}: "
                . $e->getMessage());
            return back()
                ->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => 'required|array',
            'ids.*'    => 'integer|exists:assignments,id',
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
            $updatedCount = Assignment::whereIn('id', $ids)->update(['activity' => $activity]);
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
            Log::error("Ошибка при массовом обновлении активности заданий: " . $e->getMessage(), [
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
     * Обновление сортировки одного.
     *
     * @param UpdateSortEntityRequest $request
     * @param Assignment $assignment
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, Assignment $assignment): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $originalSort = $assignment->sort;
            $assignment->sort = (int) $validated['sort'];
            $assignment->save();

            Log::info("Сортировка задания '{$assignment->title}' (ID: {$assignment->id}) изменена с {$originalSort} на {$assignment->sort}");

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении сортировки задания (ID: {$assignment->id}): " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки.
     * Ожидает массив: [{id: 1, sort: 10}, ...]
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'assignments'           => ['required', 'array'],
            'assignments.*.id'      => ['required', 'integer', 'exists:assignments,id'],
            'assignments.*.sort'    => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['assignments'] as $row) {
                    Assignment::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка массового обновления сортировки заданий: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Клонирование задания.
     * Копирует основные поля и связи.
     * Генерирует новые уникальные title и slug.
     *
     * @param Request $request (Не используется, но нужен для сигнатуры маршрута)
     * @param int $assignment Модель задания для клонирования (через RMB).
     * @return RedirectResponse
     */
    public function clone(Request $request, int $assignment): RedirectResponse
    {
        // Грузим задание вместе с soft-deleted, плюс связи
        $assignment = Assignment::withTrashed()
            ->with(['images', 'media'])
            ->findOrFail($assignment);

        DB::beginTransaction();

        try {
            // Базовые значения для генерации title/slug
            $baseTitle = $assignment->title;
            $baseSlug  = $assignment->slug;
            $locale    = $assignment->locale ?? config('app.fallback_locale', 'ru');

            $newTitle = $baseTitle . '-2';

            // 🔹 Если хочешь игнорировать soft-deleted при проверке уникальности slug:
            // ->whereNull('deleted_at')
            $suffix   = '-2';
            $newSlug  = $baseSlug . $suffix;
            $counter  = 2;

            while (
            Assignment::where('locale', $locale)
                ->where('slug', $newSlug)
                // ->whereNull('deleted_at') // добавь, если нужно не учитывать удалённые
                ->exists()
            ) {
                $newSlug = $baseSlug . $suffix . '-' . $counter;
                $counter++;
            }

            $cloned = $assignment->replicate();

            $cloned->title        = $newTitle;
            $cloned->slug         = $newSlug;
            $cloned->activity     = false;
            $cloned->published_at = null;
            $cloned->status       = 'draft';
            $cloned->due_at       = null;
            $cloned->created_at   = now();
            $cloned->updated_at   = now();

            $cloned->save();

            // Клонируем изображения
            $imageSyncData = [];

            foreach ($assignment->images as $image) {
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
                            "Ошибка копирования медиа при клонировании задания ID {$assignment->id}: " .
                            $e->getMessage(),
                            ['trace' => $e->getTraceAsString()]
                        );
                    }
                }
            }

            if (!empty($imageSyncData)) {
                $cloned->images()->sync($imageSyncData);
            }

            // Клонируем вложения (attachments), если используются
            foreach ($assignment->getMedia('attachments') as $media) {
                try {
                    $media->copy($cloned, 'attachments');
                } catch (Throwable $e) {
                    Log::error(
                        "Ошибка копирования вложений при клонировании задания ID {$assignment->id}: " .
                        $e->getMessage(),
                        ['trace' => $e->getTraceAsString()]
                    );
                }
            }

            DB::commit();

            Log::info(
                "Задание ID {$assignment->id} успешно клонировано в ID {$cloned->id}",
                ['source_id' => $assignment->id, 'clone_id' => $cloned->id]
            );

            return redirect()
                ->route('admin.assignments.index')
                ->with('success', __('admin/controllers.cloned_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error(
                "Ошибка при клонировании задания ID {$assignment->id}: " . $e->getMessage(),
                ['trace' => $e->getTraceAsString()]
            );

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.cloned_error'));
        }
    }

    /**
     * Приватный метод удаления изображений задания (Spatie MediaLibrary).
     *
     * @param array $imageIds
     * @return void
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $imagesToDelete = AssignmentImage::whereIn('id', $imageIds)->get();

        foreach ($imagesToDelete as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }

        Log::info('Удалены записи AssignmentImage и их медиа', [
            'image_ids' => $imageIds,
        ]);
    }
}
