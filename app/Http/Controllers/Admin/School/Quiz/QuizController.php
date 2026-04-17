<?php

namespace App\Http\Controllers\Admin\School\Quiz;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\Quiz\QuizRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateLeftRequest;
use App\Http\Requests\Admin\System\UpdateMainRequest;
use App\Http\Requests\Admin\System\UpdateRightRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\School\Course\CourseResource;
use App\Http\Resources\Admin\School\Lesson\LessonResource;
use App\Http\Resources\Admin\School\Module\ModuleResource;
use App\Http\Resources\Admin\School\Quiz\QuizResource;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use App\Models\Admin\School\Quiz\Quiz;
use App\Models\Admin\School\Quiz\QuizImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Викторинами (Quizzes) в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное и массовое)
 * - клонирование
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see Quiz
 * @see QuizRequest
 */
class QuizController extends Controller
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
        $adminCountQuizzes = (int) config('site_settings.AdminCountQuizzes', 10);
        $adminSortQuizzes  = config('site_settings.AdminSortQuizzes', 'idDesc');

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $quizzes      = collect();
        $quizzesCount = 0;

        try {
            $quizzes = Quiz::query()
                ->byLocale($currentLocale)
                ->with([
                    'course',
                    'module',
                    'lesson',
                    'images' => fn($q) => $q->orderBy('quiz_has_images.order'),
                ])
                ->orderBy('sort')
                ->get();

            $quizzesCount = Quiz::query()
                ->byLocale($currentLocale)
                ->count();

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки квизов (locale: {$currentLocale}): " . $e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/School/Quizzes/Index', [
            'quizzes'          => QuizResource::collection($quizzes)->resolve(),
            'quizzesCount'     => $quizzesCount,
            'adminCountQuizzes'=> $adminCountQuizzes,
            'adminSortQuizzes' => $adminSortQuizzes,
            'currentLocale'    => $currentLocale,
            'availableLocales' => $this->availableLocales,
        ]);
    }

    /**
     * Форма создания викторины.
     * Передаём списки: курсы, модули, уроки.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
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

        return Inertia::render('Admin/School/Quizzes/Create', [
            'courses'       => CourseResource::collection($courses),
            'modules'       => ModuleResource::collection($modules),
            'lessons'       => LessonResource::collection($lessons),
            'currentLocale' => $currentLocale,
        ]);
    }

    /**
     * Сохранение нового квиза.
     *
     * - QuizRequest: валидация + нормализация
     * - обработка изображений (QuizImage + Spatie ML)
     *
     * @param QuizRequest $request
     * @return RedirectResponse
     */
    public function store(QuizRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $imagesData = $data['images'] ?? [];

        unset(
            $data['images'],
            $data['deletedImages'],
        );

        try {
            DB::beginTransaction();

            /** @var Quiz $quiz */
            $quiz = Quiz::create($data);

            // Обработка изображений
            $imageSyncData = [];
            $imageIndex    = 0;

            foreach ($imagesData as $imageData) {
                $fileKey = "images.{$imageIndex}.file";

                if ($request->hasFile($fileKey)) {
                    $image = QuizImage::create([
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
                            Log::warning("Недопустимый файл изображения (quiz ID {$quiz->id}), индекс {$imageIndex}", [
                                'fileKey' => $fileKey,
                                'error'   => $file?->getErrorMessage(),
                            ]);
                            $image->delete();
                        }
                    } catch (Throwable $e) {
                        Log::error("Ошибка Spatie media-library для квиза {$quiz->id}, индекс изображения {$imageIndex}: " . $e->getMessage(), [
                            'trace' => $e->getTraceAsString(),
                        ]);
                        $image->delete();
                    }
                }

                $imageIndex++;
            }

            if (!empty($imageSyncData)) {
                $quiz->images()->sync($imageSyncData);
            }

            DB::commit();

            Log::info('Квиз успешно создан', [
                'id'    => $quiz->id,
                'title' => $quiz->title,
            ]);

            return redirect()
                ->route('admin.quizzes.index')
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании квиза: " . $e->getMessage(), [
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
     * @param Quiz $quiz
     * @return RedirectResponse
     */
    public function show(Quiz $quiz): RedirectResponse
    {
        return redirect()->route('admin.quizzes.edit', $quiz);
    }

    /**
     * Форма редактирования квиза.
     * Передаём связки: курсы, модули, уроки
     *
     * @param Quiz $quiz
     * @return Response
     */
    public function edit(Quiz $quiz): Response
    {
        $currentLocale = $quiz->locale ?? config('app.fallback_locale', 'ru');

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        // Подгружаем связи
        $quiz->load([
            'course',
            'module:id,title,slug',
            'lesson:id,title,slug',
            'images' => fn($q) => $q->orderBy('quiz_has_images.order'),
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

        return Inertia::render('Admin/School/Quizzes/Edit', [
            'quiz'          => new QuizResource($quiz),
            'courses'       => CourseResource::collection($courses),
            'modules'       => ModuleResource::collection($modules),
            'lessons'       => LessonResource::collection($lessons),
            'currentLocale' => $currentLocale,
        ]);
    }

    /**
     * Обновление квиза:
     *  - обновление полей
     *  - обработка изображений
     *
     * @param QuizRequest $request
     * @param Quiz $quiz
     * @return RedirectResponse
     */
    public function update(QuizRequest $request, Quiz $quiz): RedirectResponse
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

            // 1) Удаляем указанные изображения
            if (!empty($deletedImageIds)) {
                $quiz->images()->detach($deletedImageIds);
                $this->deleteImages($deletedImageIds);
            }

            // 2) Обновляем поля квиза
            $quiz->update($data);

            // 3) Обработка изображений
            $syncData = [];

            foreach ($imagesData as $index => $imageData) {
                $fileKey = "images.{$index}.file";

                // Обновление существующего изображения
                if (!empty($imageData['id'])) {
                    $img = QuizImage::find($imageData['id']);

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
                    $new = QuizImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    $new->addMedia($request->file($fileKey))
                        ->toMediaCollection('images');

                    $syncData[$new->id] = ['order' => $new->order];
                }
            }

            // 4) Синхронизация pivot
            $quiz->images()->sync($syncData);

            DB::commit();

            Log::info('Квиз обновлён', [
                'id'    => $quiz->id,
                'title' => $quiz->title,
            ]);

            return redirect()
                ->route('admin.quizzes.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при обновлении квиза ID {$quiz->id}: " . $e->getMessage(), [
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
     * Удаление квиза с изображениями.
     *
     * @param Quiz $quiz
     * @return RedirectResponse
     */
    public function destroy(Quiz $quiz): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $imageIds = $quiz->images()->pluck('quiz_images.id')->toArray();
            $quiz->images()->detach();
            $this->deleteImages($imageIds);

            $quiz->delete();

            DB::commit();

            Log::info('Квиз удалён', ['id' => $quiz->id]);

            return redirect()
                ->route('admin.quizzes.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении квиза ID {$quiz->id}: " . $e->getMessage(), [
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
        $validated = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:quizzes,id',
        ]);

        $quizIds = $validated['ids'];
        $count   = count($quizIds);

        try {
            DB::beginTransaction();

            // ID всех изображений, привязанных к этим квизам
            $allImageIds = QuizImage::whereHas('quizzes', fn($q) => $q
                ->whereIn('quizzes.id', $quizIds))
                ->pluck('id')
                ->toArray();

            if (!empty($allImageIds)) {
                DB::table('quiz_has_images')
                    ->whereIn('quiz_id', $quizIds)
                    ->delete();

                $this->deleteImages($allImageIds);
            }

            Quiz::whereIn('id', $quizIds)->delete();

            DB::commit();

            Log::info('Квизы удалены: ', $quizIds);

            return redirect()->route('admin.quizzes.index')
                ->with('success', __('admin/controllers.bulk_deleted_success', [
                    'count' => $count,
                ]));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при массовом удалении квизов: " . $e->getMessage(), [
                'ids'       => $quizIds,
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.bulk_deleted_error'));
        }
    }

    /**
     * Включение квиза в левой колонке.
     *
     * @param UpdateLeftRequest $request
     * @param Quiz $quiz
     * @return RedirectResponse
     */
    public function updateLeft(UpdateLeftRequest $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $quiz->left = $validated['left'];
            $quiz->save();

            Log::info("Обновлено значение left для квиза ID {$quiz->id}");

            return redirect()->route('admin.quizzes.index')
                ->with('success', __('admin/controllers.left_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка обновления left квиза ID {$quiz->id}: " . $e->getMessage());

            return back()
                ->with('error', __('admin/controllers.left_updated_error'));
        }
    }

    /**
     * Включение квиза как «главный».
     *
     * @param UpdateMainRequest $request
     * @param Quiz $quiz
     * @return RedirectResponse
     */
    public function updateMain(UpdateMainRequest $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $quiz->main = $validated['main'];
            $quiz->save();

            Log::info("Обновлено значение main для квиза ID {$quiz->id}");

            return redirect()->route('admin.quizzes.index')
                ->with('success', __('admin/controllers.main_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка обновления main квиза ID {$quiz->id}: " . $e->getMessage());

            return back()
                ->with('error', __('admin/controllers.main_updated_error'));
        }
    }

    /**
     * Включение квиза в правой колонке.
     *
     * @param UpdateRightRequest $request
     * @param Quiz $quiz
     * @return RedirectResponse
     */
    public function updateRight(UpdateRightRequest $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $quiz->right = $validated['right'];
            $quiz->save();

            Log::info("Обновлено значение right для квиза ID {$quiz->id}");

            return redirect()->route('admin.quizzes.index')
                ->with('success', __('admin/controllers.right_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка обновления right квиза ID {$quiz->id}: " . $e->getMessage());

            return back()
                ->with('error', __('admin/controllers.right_updated_error'));
        }
    }

    /**
     * Обновление активности одного квиза.
     *
     * @param UpdateActivityRequest $request
     * @param Quiz $quiz
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $quiz->activity = $validated['activity'];
            $quiz->save();

            Log::info("Обновлено activity квиза ID {$quiz->id} на {$quiz->activity}");

            return back()
                ->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка обновления активности квиза ID {$quiz->id}: " . $e->getMessage());

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
            'ids.*'    => 'integer|exists:quizzes,id',
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
            $updatedCount = Quiz::whereIn('id', $ids)->update(['activity' => $activity]);
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
            Log::error("Ошибка при массовом обновлении активности квизов: " . $e->getMessage(), [
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
     * Обновление сортировки одного квиза.
     *
     * @param UpdateSortEntityRequest $request
     * @param Quiz $quiz
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $originalSort = $quiz->sort;
            $quiz->sort   = (int) $validated['sort'];
            $quiz->save();

            Log::info("Сортировка квиза '{$quiz->title}' (ID: {$quiz->id}) изменена с {$originalSort} на {$quiz->sort}");

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении сортировки квиза (ID: {$quiz->id}): " . $e->getMessage(), [
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
            'quizzes'           => ['required', 'array'],
            'quizzes.*.id'      => ['required', 'integer', 'exists:quizzes,id'],
            'quizzes.*.sort'    => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['quizzes'] as $row) {
                    Quiz::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка массового обновления сортировки квизов: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Клонирование квиза.
     *
     * Без soft-deletes, просто копия с новым title/slug,
     * activity=false, published_at=null.
     *
     * @param Request $request
     * @param int $quiz
     * @return RedirectResponse
     */
    public function clone(Request $request, int $quiz): RedirectResponse
    {
        $quiz = Quiz::with(['images', 'media'])->findOrFail($quiz);

        DB::beginTransaction();

        try {
            $baseTitle = $quiz->title;
            $baseSlug  = $quiz->slug;
            $locale    = $quiz->locale ?? config('app.fallback_locale', 'ru');

            $newTitle = $baseTitle . ' (копия)';

            $suffix  = '-2';
            $newSlug = $baseSlug . $suffix;
            $counter = 2;

            while (
            Quiz::where('locale', $locale)
                ->where('slug', $newSlug)
                ->exists()
            ) {
                $newSlug = $baseSlug . $suffix . '-' . $counter;
                $counter++;
            }

            $cloned = $quiz->replicate();

            $cloned->title        = $newTitle;
            $cloned->slug         = $newSlug;
            $cloned->activity     = false;
            $cloned->left         = false;
            $cloned->main         = false;
            $cloned->right        = false;
            $cloned->published_at = null;
            $cloned->created_at   = now();
            $cloned->updated_at   = now();

            $cloned->save();

            // Клонируем изображения
            $imageSyncData = [];

            foreach ($quiz->images as $image) {
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
                            "Ошибка копирования медиа при клонировании квиза ID {$quiz->id}: " .
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
            foreach ($quiz->getMedia('attachments') as $media) {
                try {
                    $media->copy($cloned, 'attachments');
                } catch (Throwable $e) {
                    Log::error(
                        "Ошибка копирования вложений при клонировании квиза ID {$quiz->id}: " .
                        $e->getMessage(),
                        ['trace' => $e->getTraceAsString()]
                    );
                }
            }

            DB::commit();

            Log::info(
                "Квиз ID {$quiz->id} успешно клонирован в ID {$cloned->id}",
                ['source_id' => $quiz->id, 'clone_id' => $cloned->id]
            );

            return redirect()
                ->route('admin.quizzes.index')
                ->with('success', __('admin/controllers.cloned_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error(
                "Ошибка при клонировании квиза ID {$quiz->id}: " . $e->getMessage(),
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

        $imagesToDelete = QuizImage::whereIn('id', $imageIds)->get();

        foreach ($imagesToDelete as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }

        Log::info('Удалены записи QuizImage и их медиа', [
            'image_ids' => $imageIds,
        ]);
    }
}
