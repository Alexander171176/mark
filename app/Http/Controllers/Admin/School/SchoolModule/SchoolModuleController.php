<?php

namespace App\Http\Controllers\Admin\School\SchoolModule;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolModule\SchoolModuleRequest;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Models\Admin\School\SchoolModule\SchoolModuleImage;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
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

        $perPage = $settings->int('adminSchoolModulesPerPage', 6);
        $defaultSort = $settings->string('adminSchoolModulesDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminSchoolModulesProcessingMode',
            'frontend'
        );

        $modulesCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $modulesCount,
                300
            );

        try {
            $modules = $this->getIndexModules(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/School/SchoolModules/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminSchoolModulesPerPage' => $perPage,
                'adminSchoolModulesDefaultSort' => $defaultSort,
                'adminSchoolModulesProcessingMode' => $processingMode,

                /**
                 * Admin Index всегда использует
                 * краткий Shared Resource.
                 */
                'modules' => SchoolModuleSharedResource::collection(
                    $modules
                ),
                'modulesCount' => $modulesCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка school modules: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/SchoolModules/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminSchoolModulesPerPage' => $perPage,
                'adminSchoolModulesDefaultSort' => $defaultSort,
                'adminSchoolModulesProcessingMode' => $processingMode,

                'modules' => [],
                'modulesCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки модулей.',
            ]);
        }
    }

    /** Страница создания модуля. */
    public function create(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        return Inertia::render(
            'Admin/School/SchoolModules/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'courses' =>
                    $this->coursesForSelect(
                        $currentLocale
                    ),
            ]
        );
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
    public function edit(
        int $schoolModule,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $module = $this->baseQuery()
            ->with([
                /**
                 * Все переводы нужны Edit
                 * для TranslationTabs.
                 *
                 * Отдельную relation translation
                 * не загружаем.
                 */
                'translations',

                /**
                 * Изображения +
                 * Spatie Media.
                 */
                'images.media',

                /**
                 * Родительский курс.
                 *
                 * Для отображения достаточно
                 * выбранной административной локали.
                 */
                'course' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $currentLocale
                    ),
                ]),
            ])
            ->withCount([
                'lessons',
                'images',
                'likes',
            ])
            ->findOrFail(
                $schoolModule
            );

        return Inertia::render(
            'Admin/School/SchoolModules/Edit',
            [
                'module' =>
                    new SchoolModuleResource(
                        $module
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'courses' =>
                    $this->coursesForSelect(
                        $currentLocale
                    ),
            ]
        );
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

    /** Список курсов для select Create/Edit. */
    private function coursesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $courses = SchoolCourse::query()
            ->with([
                /**
                 * Для select нужен только
                 * перевод выбранной локали.
                 */
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),
            ])
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        return SchoolCourseSharedResource::collection(
            $courses
        );
    }

    /** Базовый запрос для Admin Index модулей. */
    private function indexQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                /**
                 * Admin Index:
                 * только выбранная локаль.
                 *
                 * Все переводы здесь не нужны.
                 */
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),

                /**
                 * Изображения модуля +
                 * Spatie Media пакетным запросом.
                 */
                'images.media',

                /**
                 * Родительский курс.
                 *
                 * Для Index нужен только
                 * перевод выбранной локали.
                 *
                 * Курс также нужен
                 * frontend-поиску.
                 */
                'course' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $locale
                    ),
                ]),
            ])
            ->withCount([
                'lessons',
                'images',
                'likes',
            ]);
    }

    /** Получение списка модулей по активному режиму обработки. */
    private function getIndexModules(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery(
            $locale
        );

        if ($useServerProcessing) {
            return $query
                ->search(
                    $search,
                    $locale
                )
                ->sortByParam(
                    $sort,
                    $locale
                )
                ->paginate(
                    $perPage
                )
                ->withQueryString();
        }

        return $query
            ->sortByParam(
                $sort,
                $locale
            )
            ->get();
    }
}
