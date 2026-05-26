<?php

namespace App\Http\Controllers\Admin\School\Quiz;

use App\Http\Controllers\Admin\School\Base\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\Quiz\SchoolQuizRequest;
use App\Http\Resources\Admin\School\Course\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\Lesson\SchoolLessonResource;
use App\Http\Resources\Admin\School\Module\SchoolModuleResource;
use App\Http\Resources\Admin\School\Quiz\SchoolQuizResource;
use App\Models\Admin\School\Course\SchoolCourse;
use App\Models\Admin\School\Lesson\SchoolLesson;
use App\Models\Admin\School\Module\SchoolModule;
use App\Models\Admin\School\Quiz\SchoolQuiz;
use App\Models\Admin\School\Quiz\SchoolQuizImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Викторинами (SchoolQuizzes) в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное и массовое)
 * - клонирование
 * - мультиязычность, изображения
 * - связи с курсами, модулями, уроками.
 *
 * @version 1.1 (мультиязычная архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolQuiz
 * @see SchoolQuizRequest
 */
class SchoolQuizController extends BaseSchoolAdminController
{
    /** Основная модель */
    protected string $modelClass = SchoolQuiz::class;

    /** Модель изображений */
    protected string $imageModelClass = SchoolQuizImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности для уведомлений */
    protected string $entityLabel = 'квизов';

    /** Поля переводов */
    protected array $translationFields = [
        'title',
        'short',
        'description',
    ];

    /** Список квизов. */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $adminSchoolQuizzesPerPage = (int) config('site_settings.adminSchoolQuizzesPerPage', 10);
        $adminSchoolQuizzesDefaultSort = (string) config('site_settings.adminSchoolQuizzesDefaultSort', 'idDesc');

        try {
            $quizzes = $this->baseQuery()
                ->with([
                    'translation',
                    'translations',
                    'images',
                    'course.translation',
                    'module.translation',
                    'lesson.translation',
                ])
                ->withCount([
                    'questions',
                    'attempts',
                    'images',
                ])
                ->get();

            return Inertia::render('Admin/School/Quizzes/Index', [
                'quizzes' => SchoolQuizResource::collection($quizzes),
                'quizzesCount' => $this->baseQuery()->count(),

                'adminSchoolQuizzesPerPage' => $adminSchoolQuizzesPerPage,
                'adminSchoolQuizzesDefaultSort' => $adminSchoolQuizzesDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки school quizzes: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/Quizzes/Index', [
                'quizzes' => [],
                'quizzesCount' => 0,

                'adminSchoolQuizzesPerPage' => $adminSchoolQuizzesPerPage,
                'adminSchoolQuizzesDefaultSort' => $adminSchoolQuizzesDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'error' => 'Ошибка загрузки квизов.',
            ]);
        }
    }

    /** Страница создания квиза. */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/School/Quizzes/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'courses' => $this->coursesForSelect(),
            'modules' => $this->modulesForSelect(),
            'lessons' => $this->lessonsForSelect(),
        ]);
    }

    /** Сохранение нового квиза. */
    public function store(SchoolQuizRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages']
        );

        try {
            DB::transaction(function () use ($request, $data, $translations, $imagesData) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = SchoolQuiz::query()->max('sort');
                    $data['sort'] = is_null($maxSort) ? 1 : $maxSort + 1;
                }

                $quiz = SchoolQuiz::create($data);

                $this->syncTranslations($quiz, $translations);
                $this->syncImages($quiz, $request, $imagesData);
            });

            return redirect()
                ->route('admin.schoolQuizzes.index')
                ->with('success', 'Квиз успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school quiz: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании квиза.');
        }
    }

    /** Редирект на страницу редактирования. */
    public function show(int $schoolQuiz): RedirectResponse
    {
        return redirect()->route('admin.schoolQuizzes.edit', $schoolQuiz);
    }

    /** Страница редактирования квиза. */
    public function edit(int $schoolQuiz, Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $quiz = $this->baseQuery()
            ->with([
                'translation',
                'translations',
                'images',
                'course.translation',
                'module.translation',
                'lesson.translation',
                'questions.translation',
                'attempts',
            ])
            ->withCount([
                'questions',
                'attempts',
                'images',
            ])
            ->findOrFail($schoolQuiz);

        return Inertia::render('Admin/School/Quizzes/Edit', [
            'quiz' => new SchoolQuizResource($quiz),

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'courses' => $this->coursesForSelect(),
            'modules' => $this->modulesForSelect(),
            'lessons' => $this->lessonsForSelect(),
        ]);
    }

    /** Обновление квиза. */
    public function update(SchoolQuizRequest $request, int $schoolQuiz): RedirectResponse
    {
        $quiz = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolQuiz);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['_method']
        );

        try {
            DB::transaction(function () use (
                $request,
                $quiz,
                $data,
                $translations,
                $imagesData,
                $deletedImageIds
            ) {
                $quiz->update($data);

                $this->syncTranslations($quiz, $translations);
                $this->syncImages($quiz, $request, $imagesData, $deletedImageIds);
            });

            return redirect()
                ->route('admin.schoolQuizzes.index')
                ->with('success', 'Квиз успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school quiz ID ' . $quiz->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении квиза.');
        }
    }

    /** Удаление квиза. */
    public function destroy(int $schoolQuiz): RedirectResponse
    {
        $quiz = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolQuiz);

        try {
            DB::transaction(function () use ($quiz) {
                $imageIds = $quiz->images()
                    ->pluck('school_quiz_images.id')
                    ->toArray();

                if (!empty($imageIds)) {
                    $quiz->images()->detach();
                    $this->deleteImages($imageIds);
                }

                $quiz->translations()->delete();

                $quiz->delete();
            });

            return redirect()
                ->route('admin.schoolQuizzes.index')
                ->with('success', 'Квиз успешно удалён.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school quiz ID ' . $quiz->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении квиза.');
        }
    }

    /** Массовое удаление квизов. */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_quizzes,id'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                $quizzes = SchoolQuiz::query()
                    ->with('images')
                    ->whereIn('id', $data['ids'])
                    ->get();

                foreach ($quizzes as $quiz) {
                    $imageIds = $quiz->images()
                        ->pluck('school_quiz_images.id')
                        ->toArray();

                    if (!empty($imageIds)) {
                        $quiz->images()->detach();
                        $this->deleteImages($imageIds);
                    }

                    $quiz->translations()->delete();
                    $quiz->delete();
                }
            });

            return back()->with('success', 'Выбранные квизы успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка массового удаления school quizzes: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении квизов.');
        }
    }

    /** Клонирование квиза. */
    public function clone(Request $request, int $schoolQuiz): RedirectResponse
    {
        $quiz = $this->baseQuery()
            ->with([
                'translations',
                'images',
            ])
            ->findOrFail($schoolQuiz);

        try {
            DB::transaction(function () use ($quiz) {
                $clone = $quiz->replicate([
                    'created_at',
                    'updated_at',
                ]);

                $baseSlug = $quiz->slug;
                $newSlug = $baseSlug . '-copy';
                $counter = 2;

                while (SchoolQuiz::query()->where('slug', $newSlug)->exists()) {
                    $newSlug = $baseSlug . '-copy-' . $counter;
                    $counter++;
                }

                $clone->slug = $newSlug;
                $clone->activity = false;
                $clone->left = false;
                $clone->main = false;
                $clone->right = false;
                $clone->published_at = null;
                $clone->save();

                foreach ($quiz->translations as $translation) {
                    $clonedTranslation = $translation->replicate([
                        'created_at',
                        'updated_at',
                    ]);

                    $clonedTranslation->school_quiz_id = $clone->id;
                    $clonedTranslation->title = $clonedTranslation->title . ' (копия)';
                    $clonedTranslation->save();
                }

                $imageSyncData = [];

                foreach ($quiz->images as $image) {
                    $clonedImage = $image->replicate([
                        'created_at',
                        'updated_at',
                    ]);

                    $clonedImage->save();

                    $media = $image->getFirstMedia($this->imageMediaCollection);

                    if ($media) {
                        $media->copy($clonedImage, $this->imageMediaCollection);
                    }

                    $order = $image->pivot->order ?? $image->order ?? 0;
                    $imageSyncData[$clonedImage->id] = ['order' => $order];
                }

                if (!empty($imageSyncData)) {
                    $clone->images()->sync($imageSyncData);
                }
            });

            return redirect()
                ->route('admin.schoolQuizzes.index')
                ->with('success', 'Квиз успешно клонирован.');
        } catch (Throwable $e) {
            Log::error('Ошибка клонирования school quiz ID ' . $quiz->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при клонировании квиза.');
        }
    }

    /** Список курсов для select. */
    private function coursesForSelect(): AnonymousResourceCollection
    {
        $courses = SchoolCourse::query()
            ->with(['translation', 'translations'])
            ->get();

        return SchoolCourseSharedResource::collection($courses);
    }

    /** Список модулей для select. */
    private function modulesForSelect(): AnonymousResourceCollection
    {
        $modules = SchoolModule::query()
            ->with([
                'translation',
                'translations',
                'course.translation',
            ])
            ->get();

        return SchoolModuleResource::collection($modules);
    }

    /** Список уроков для select. */
    private function lessonsForSelect(): AnonymousResourceCollection
    {
        $lessons = SchoolLesson::query()
            ->with([
                'translation',
                'translations',
                'module.translation',
                'module.course.translation',
            ])
            ->get();

        return SchoolLessonResource::collection($lessons);
    }
}
