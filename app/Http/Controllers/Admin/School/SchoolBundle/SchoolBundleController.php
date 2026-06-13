<?php

namespace App\Http\Controllers\Admin\School\SchoolBundle;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolBundle\SchoolBundleRequest;
use App\Http\Resources\Admin\School\SchoolBundle\SchoolBundleResource;
use App\Http\Resources\Admin\School\SchoolBundle\SchoolBundleSharedResource;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Models\Admin\School\SchoolBundle\SchoolBundle;
use App\Models\Admin\School\SchoolBundle\SchoolBundleImage;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
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
 * Контроллер для управления Наборами курсов (SchoolBundle) в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное)
 * - мультиязычность, изображения
 * - связи с треками, хештегами и связанными курсами.
 *
 * @version 1.1 (мультиязычная архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolBundle
 * @see SchoolBundleRequest
 *
 */
class SchoolBundleController extends BaseSchoolAdminController
{
    /** Основная модель */
    protected string $modelClass = SchoolBundle::class;

    /** Модель изображений */
    protected string $imageModelClass = SchoolBundleImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности для уведомлений */
    protected string $entityLabel = 'наборов курсов';

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

    /** Список наборов курсов. */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminSchoolBundlesPerPage', 6);
        $defaultSort = $settings->string('adminSchoolBundlesDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminSchoolBundlesProcessingMode',
            'frontend'
        );

        $bundlesCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $bundlesCount,
                300
            );

        try {
            $bundles = $this->getIndexBundles(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/School/SchoolBundles/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminSchoolBundlesPerPage' => $perPage,
                'adminSchoolBundlesDefaultSort' => $defaultSort,
                'adminSchoolBundlesProcessingMode' => $processingMode,

                'bundles' => SchoolBundleResource::collection($bundles),
                'bundlesCount' => $bundlesCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка school bundles: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/SchoolBundles/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminSchoolBundlesPerPage' => $perPage,
                'adminSchoolBundlesDefaultSort' => $defaultSort,
                'adminSchoolBundlesProcessingMode' => $processingMode,

                'bundles' => [],
                'bundlesCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки наборов курсов.',
            ]);
        }
    }

    /** Страница создания набора курсов. */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/School/SchoolBundles/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'courses' => $this->coursesForSelect(),
        ]);
    }

    /** Сохранение нового набора курсов. */
    public function store(SchoolBundleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $courseIds = $data['course_ids'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['course_ids']
        );

        try {
            DB::transaction(function () use (
                $request,
                $data,
                $translations,
                $imagesData,
                $courseIds
            ) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = SchoolBundle::query()->max('sort');
                    $data['sort'] = is_null($maxSort) ? 1 : $maxSort + 1;
                }

                $bundle = SchoolBundle::create($data);

                $this->syncTranslations($bundle, $translations);
                $this->syncImages($bundle, $request, $imagesData);

                $bundle->courses()->sync($courseIds);
            });

            return redirect()
                ->route('admin.schoolBundles.index')
                ->with('success', 'Набор курсов успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school bundle: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании набора курсов.');
        }
    }

    /** Редирект на страницу редактирования. */
    public function show(int $schoolBundle): RedirectResponse
    {
        return redirect()->route('admin.schoolBundles.edit', $schoolBundle);
    }

    /** Страница редактирования набора курсов. */
    public function edit(int $schoolBundle, Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $bundle = $this->baseQuery()
            ->with([
                'translation',
                'translations',
                'images',
                'courses.translation',
                'courses.translations',
                'prices',
            ])
            ->withCount([
                'courses',
                'images',
                'prices',
                'orderItems',
            ])
            ->findOrFail($schoolBundle);

        return Inertia::render('Admin/School/SchoolBundles/Edit', [
            'bundle' => new SchoolBundleResource($bundle),

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'courses' => $this->coursesForSelect(),
        ]);
    }

    /** Обновление набора курсов. */
    public function update(SchoolBundleRequest $request, int $schoolBundle): RedirectResponse
    {
        $bundle = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolBundle);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];
        $courseIds = $data['course_ids'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['course_ids'],
            $data['_method']
        );

        try {
            DB::transaction(function () use (
                $request,
                $bundle,
                $data,
                $translations,
                $imagesData,
                $deletedImageIds,
                $courseIds
            ) {
                $bundle->update($data);

                $this->syncTranslations($bundle, $translations);
                $this->syncImages($bundle, $request, $imagesData, $deletedImageIds);

                $bundle->courses()->sync($courseIds);
            });

            return redirect()
                ->route('admin.schoolBundles.index')
                ->with('success', 'Набор курсов успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school bundle ID ' . $bundle->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении набора курсов.');
        }
    }

    /** Удаление набора курсов. */
    public function destroy(int $schoolBundle): RedirectResponse
    {
        $bundle = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolBundle);

        try {
            DB::transaction(function () use ($bundle) {
                $imageIds = $bundle->images()
                    ->pluck('school_bundle_images.id')
                    ->toArray();

                if (!empty($imageIds)) {
                    $bundle->images()->detach();
                    $this->deleteImages($imageIds);
                }

                $bundle->courses()->detach();
                $bundle->translations()->delete();

                $bundle->delete();
            });

            return redirect()
                ->route('admin.schoolBundles.index')
                ->with('success', 'Набор курсов успешно удалён.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school bundle ID ' . $bundle->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении набора курсов.');
        }
    }

    /** Список наборов курсов для select. */
    private function bundlesForSelect(?int $excludeId = null): AnonymousResourceCollection
    {
        $bundles = SchoolBundle::query()
            ->when($excludeId, fn ($query) => $query->whereKeyNot($excludeId))
            ->with([
                'translation',
                'translations',
                'images',
            ])
            ->get();

        return SchoolBundleSharedResource::collection($bundles);
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

    /** Базовый запрос для списка наборов курсов. */
    private function indexQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                'translation',
                'translations',
                'images',
                'courses.translation',
                'courses.translations',
                'prices',
            ])
            ->withCount([
                'courses',
                'images',
                'prices',
                'orderItems',
            ]);
    }

    /** Получение списка наборов курсов по активному режиму обработки. */
    private function getIndexBundles(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery();

        if ($useServerProcessing) {
            return $query
                ->search($search, $locale)
                ->sortByParam($sort, $locale)
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->sortByParam($sort, $locale)
            ->get();
    }
}
