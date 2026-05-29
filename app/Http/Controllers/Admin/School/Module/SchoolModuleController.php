<?php

namespace App\Http\Controllers\Admin\School\Module;

use App\Http\Controllers\Admin\School\Base\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\Module\SchoolModuleRequest;
use App\Http\Resources\Admin\School\Course\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\Module\SchoolModuleResource;
use App\Models\Admin\School\Course\SchoolCourse;
use App\Models\Admin\School\Module\SchoolModule;
use App\Models\Admin\School\Module\SchoolModuleImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
 * - мультиязычность, изображения
 * - связи с связанными курсами.
 *
 * @version 1.1 (мультиязычная архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolModule
 * @see SchoolModuleRequest
 */
class SchoolModuleController extends BaseSchoolAdminController
{
    /** Основная модель */
    protected string $modelClass = SchoolModule::class;

    /** Модель изображений */
    protected string $imageModelClass = SchoolModuleImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности для уведомлений */
    protected string $entityLabel = 'модулей';

    /** Поля переводов */
    protected array $translationFields = [
        'title',
        'subtitle',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Расширение сортировки для модулей. */
    protected function extendedSortMap(): array
    {
        return [
            'published_at' => 'published_at_desc',
            'lessons_count' => 'lessons_count_desc',

            'status' => 'status_asc',
            'availability' => 'availability_asc',

            'difficulty' => 'difficulty_desc',
            'duration' => 'duration_desc',

            'popularity' => 'popularity_desc',
            'rating_count' => 'rating_count_desc',
            'rating_avg' => 'rating_avg_desc',

            'views' => 'views_desc',
            'likes' => 'likes_desc',
            'likes_count' => 'likes_count_desc',

            'activity' => 'activity',
            'inactive' => 'inactive',
        ];
    }

    /** Список модулей. */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $adminSchoolModulesPerPage = (int) config('site_settings.adminSchoolModulesPerPage', 10);
        $adminSchoolModulesDefaultSort = (string) config('site_settings.adminSchoolModulesDefaultSort', 'idDesc');
        $sort = $this->normalizeSortParam($adminSchoolModulesDefaultSort);

        try {
            $modules = $this->baseQuery()
                ->with([
                    'translation',
                    'translations',
                    'images',
                    'course.translation',
                    'lessons.translation',
                ])
                ->withCount([
                    'lessons',
                    'images',
                    'likes',
                ])
                ->when($sort === 'activity', fn ($query) => $query->where('activity', true))
                ->when($sort === 'inactive', fn ($query) => $query->where('activity', false))
                ->when($sort === 'published_at_desc', fn ($query) => $query->orderByDesc('published_at')->orderByDesc('id'))
                ->when($sort === 'lessons_count_desc', fn ($query) => $query->orderByDesc('lessons_count')->orderByDesc('id'))
                ->when($sort === 'views_desc', fn ($query) => $query->orderByDesc('views')->orderByDesc('id'))
                ->when($sort === 'likes_desc', fn ($query) => $query->orderByDesc('likes')->orderByDesc('id'))
                ->when($sort === 'likes_count_desc', fn ($query) => $query->orderByDesc('likes_count')->orderByDesc('id'))
                ->when($sort === 'popularity_desc', fn ($query) => $query->orderByDesc('popularity')->orderByDesc('id'))
                ->when($sort === 'rating_count_desc', fn ($query) => $query->orderByDesc('rating_count')->orderByDesc('id'))
                ->when($sort === 'rating_avg_desc', fn ($query) => $query->orderByDesc('rating_avg')->orderByDesc('id'))
                ->when($sort === 'difficulty_desc', fn ($query) => $query->orderByDesc('difficulty')->orderByDesc('id'))
                ->when($sort === 'duration_desc', fn ($query) => $query->orderByDesc('duration')->orderByDesc('id'))
                ->when($sort === 'status_asc', fn ($query) => $query->orderBy('status')->orderByDesc('id'))
                ->when($sort === 'availability_asc', fn ($query) => $query->orderBy('availability')->orderByDesc('id'))
                ->when($sort === 'sort_asc', fn ($query) => $query->orderBy('sort')->orderByDesc('id'))
                ->when($sort === 'sort_desc', fn ($query) => $query->orderByDesc('sort')->orderByDesc('id'))
                ->when($sort === 'date_asc', fn ($query) => $query->orderBy('id')->orderByDesc('id'))
                ->when($sort === 'date_desc', fn ($query) => $query->orderByDesc('id'))
                ->get();

            return Inertia::render('Admin/School/Modules/Index', [
                'modules' => SchoolModuleResource::collection($modules),
                'modulesCount' => $this->baseQuery()->count(),

                'adminSchoolModulesPerPage' => $adminSchoolModulesPerPage,
                'adminSchoolModulesDefaultSort' => $adminSchoolModulesDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка school modules: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/Modules/Index', [
                'modules' => [],
                'modulesCount' => 0,

                'adminSchoolModulesPerPage' => $adminSchoolModulesPerPage,
                'adminSchoolModulesDefaultSort' => $adminSchoolModulesDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'error' => 'Ошибка загрузки модулей.',
            ]);
        }
    }

    /** Страница создания модуля. */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/School/Modules/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'courses' => $this->coursesForSelect(),
        ]);
    }

    /** Сохранение нового модуля. */
    public function store(SchoolModuleRequest $request): RedirectResponse
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
            DB::transaction(function () use (
                $request,
                $data,
                $translations,
                $imagesData
            ) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = SchoolModule::query()
                        ->where('school_course_id', $data['school_course_id'])
                        ->max('sort');

                    $data['sort'] = is_null($maxSort) ? 1 : $maxSort + 1;
                }

                $module = SchoolModule::create($data);

                $this->syncTranslations($module, $translations);
                $this->syncImages($module, $request, $imagesData);
            });

            return redirect()
                ->route('admin.schoolModules.index')
                ->with('success', 'Модуль успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school module: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании модуля.');
        }
    }

    /** Редирект на страницу редактирования. */
    public function show(int $schoolModule): RedirectResponse
    {
        return redirect()->route('admin.schoolModules.edit', $schoolModule);
    }

    /** Страница редактирования модуля. */
    public function edit(int $schoolModule, Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $module = $this->baseQuery()
            ->with([
                'translation',
                'translations',
                'images',
                'course.translation',
                'lessons.translation',
            ])
            ->withCount([
                'lessons',
                'images',
                'likes',
            ])
            ->findOrFail($schoolModule);

        return Inertia::render('Admin/School/Modules/Edit', [
            'module' => new SchoolModuleResource($module),

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'courses' => $this->coursesForSelect(),
        ]);
    }

    /** Обновление модуля. */
    public function update(SchoolModuleRequest $request, int $schoolModule): RedirectResponse
    {
        $module = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolModule);

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
                $module,
                $data,
                $translations,
                $imagesData,
                $deletedImageIds
            ) {
                $module->update($data);

                $this->syncTranslations($module, $translations);
                $this->syncImages($module, $request, $imagesData, $deletedImageIds);
            });

            return redirect()
                ->route('admin.schoolModules.index')
                ->with('success', 'Модуль успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school module ID ' . $module->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении модуля.');
        }
    }

    /** Удаление модуля. */
    public function destroy(int $schoolModule): RedirectResponse
    {
        $module = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolModule);

        try {
            DB::transaction(function () use ($module) {
                $imageIds = $module->images()
                    ->pluck('school_module_images.id')
                    ->toArray();

                if (!empty($imageIds)) {
                    $module->images()->detach();
                    $this->deleteImages($imageIds);
                }

                $module->translations()->delete();
                $module->likes()->delete();

                $module->delete();
            });

            return redirect()
                ->route('admin.schoolModules.index')
                ->with('success', 'Модуль успешно удалён.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school module ID ' . $module->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении модуля.');
        }
    }

    /** Список курсов для select. */
    private function coursesForSelect(): AnonymousResourceCollection
    {
        $courses = SchoolCourse::query()
            ->with([
                'translation',
                'translations',
                'images',
            ])
            ->get();

        return SchoolCourseSharedResource::collection($courses);
    }
}
