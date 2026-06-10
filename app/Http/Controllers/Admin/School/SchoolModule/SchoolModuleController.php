<?php

namespace App\Http\Controllers\Admin\School\SchoolModule;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolModule\SchoolModuleRequest;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Models\Admin\School\SchoolModule\SchoolModuleImage;
use App\Services\SiteSettings\AdminSettingsService;
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

    /** Список модулей. */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);
        $adminSchoolModulesPerPage = $settings->int('site_settings.adminSchoolModulesPerPage', 6);
        $adminSchoolModulesDefaultSort = $settings->string('site_settings.adminSchoolModulesDefaultSort', 'idDesc');
        $sort = (string) $request->query('sort', $adminSchoolModulesDefaultSort);

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
                ->sortByParam($sort, $currentLocale)
                ->get();

            return Inertia::render('Admin/School/SchoolModules/Index', [
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

            return Inertia::render('Admin/School/SchoolModules/Index', [
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

        return Inertia::render('Admin/School/SchoolModules/Create', [
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

        return Inertia::render('Admin/School/SchoolModules/Edit', [
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
