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
 * Контроллер для управления наборами курсов
 * (SchoolBundle) в административной панели.
 *
 * CRUD +:
 * - мультиязычность;
 * - изображения через базовую Spatie-логику;
 * - связь с курсами;
 * - цены;
 * - activity;
 * - sort;
 * - processing modes:
 *   frontend | server | auto.
 *
 * @version 1.2
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

    /**
     * =========================================================
     * INDEX
     * =========================================================
     */

    /** Список наборов курсов. */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $settings = app(
            AdminSettingsService::class
        );

        $perPage = $settings->int(
            'adminSchoolBundlesPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminSchoolBundlesDefaultSort',
            'idDesc'
        );

        $sortParam = (string) $request->query(
            'sort',
            $defaultSort
        );

        $search = trim(
            (string) $request->query(
                'search',
                ''
            )
        );

        $processingMode = $settings->string(
            'adminSchoolBundlesProcessingMode',
            'frontend'
        );

        /**
         * Отдельный лёгкий count
         * без eager loading.
         */
        $bundlesCount = $this->countQuery()
            ->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
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

            return Inertia::render(
                'Admin/School/SchoolBundles/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolBundlesPerPage' =>
                        $perPage,

                    'adminSchoolBundlesDefaultSort' =>
                        $defaultSort,

                    'adminSchoolBundlesProcessingMode' =>
                        $processingMode,

                    /**
                     * Index использует
                     * компактный SharedResource.
                     */
                    'bundles' =>
                        SchoolBundleSharedResource::collection(
                            $bundles
                        ),

                    'bundlesCount' =>
                        $bundlesCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка school bundles: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/School/SchoolBundles/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolBundlesPerPage' =>
                        $perPage,

                    'adminSchoolBundlesDefaultSort' =>
                        $defaultSort,

                    'adminSchoolBundlesProcessingMode' =>
                        $processingMode,

                    'bundles' =>
                        [],

                    'bundlesCount' =>
                        0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'error' =>
                        'Ошибка загрузки наборов курсов.',
                ]
            );
        }
    }

    /**
     * =========================================================
     * CREATE / STORE
     * =========================================================
     */

    /** Страница создания набора курсов. */
    public function create(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        return Inertia::render(
            'Admin/School/SchoolBundles/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                /**
                 * Courses для select:
                 * только currentLocale.
                 */
                'courses' =>
                    $this->coursesForSelect(
                        $currentLocale
                    ),
            ]
        );
    }

    /** Сохранение нового набора курсов. */
    public function store(
        SchoolBundleRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $translations =
            $data['translations']
            ?? [];

        $imagesData =
            $data['images']
            ?? [];

        $courseIds =
            $data['course_ids']
            ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['course_ids']
        );

        try {
            DB::transaction(
                function () use (
                    $request,
                    $data,
                    $translations,
                    $imagesData,
                    $courseIds
                ) {
                    /**
                     * Автоматический sort.
                     */
                    if (
                        !isset($data['sort'])
                        || is_null($data['sort'])
                    ) {
                        $maxSort =
                            SchoolBundle::query()
                                ->max('sort');

                        $data['sort'] =
                            is_null($maxSort)
                                ? 1
                                : $maxSort + 1;
                    }

                    $bundle =
                        SchoolBundle::create(
                            $data
                        );

                    /**
                     * Базовая мультиязычная логика.
                     */
                    $this->syncTranslations(
                        $bundle,
                        $translations
                    );

                    /**
                     * Базовая Spatie image-логика.
                     */
                    $this->syncImages(
                        $bundle,
                        $request,
                        $imagesData
                    );

                    /**
                     * Курсы набора.
                     */
                    $bundle
                        ->courses()
                        ->sync(
                            $courseIds
                        );
                }
            );

            return redirect()
                ->route(
                    'admin.schoolBundles.index'
                )
                ->with(
                    'success',
                    'Набор курсов успешно создан.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка создания school bundle: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при создании набора курсов.'
                );
        }
    }

    /**
     * =========================================================
     * SHOW / EDIT / UPDATE
     * =========================================================
     */

    /** Редирект на страницу редактирования. */
    public function show(
        int $schoolBundle
    ): RedirectResponse {
        return redirect()->route(
            'admin.schoolBundles.edit',
            $schoolBundle
        );
    }

    /** Страница редактирования набора курсов. */
    public function edit(
        int $schoolBundle,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $bundle = $this->baseQuery()
            ->with([
                /**
                 * Для Edit нужны ВСЕ
                 * переводы самого Bundle.
                 */
                'translations',

                /**
                 * Изображения через
                 * базовую Spatie-модель.
                 */
                'images.media',

                /**
                 * Связанные Courses:
                 * только currentLocale.
                 */
                'courses' => fn ($query) =>
                $query->with([
                    'translations' =>
                        fn ($translationQuery) =>
                        $translationQuery->where(
                            'locale',
                            $currentLocale
                        ),
                ]),

                /**
                 * Цены нужны Edit.
                 */
                'prices',
            ])
            ->withCount([
                'courses',
                'images',
                'prices',
                'orderItems',
            ])
            ->findOrFail(
                $schoolBundle
            );

        return Inertia::render(
            'Admin/School/SchoolBundles/Edit',
            [
                'bundle' =>
                    new SchoolBundleResource(
                        $bundle
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                /**
                 * Select Courses:
                 * только currentLocale.
                 */
                'courses' =>
                    $this->coursesForSelect(
                        $currentLocale
                    ),
            ]
        );
    }

    /** Обновление набора курсов. */
    public function update(
        SchoolBundleRequest $request,
        int $schoolBundle
    ): RedirectResponse {
        $bundle = $this->baseQuery()
            ->with([
                'images.media',
            ])
            ->findOrFail(
                $schoolBundle
            );

        $data = $request->validated();

        $translations =
            $data['translations']
            ?? [];

        $imagesData =
            $data['images']
            ?? [];

        $deletedImageIds =
            $data['deletedImages']
            ?? [];

        $courseIds =
            $data['course_ids']
            ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['course_ids'],
            $data['_method']
        );

        try {
            DB::transaction(
                function () use (
                    $request,
                    $bundle,
                    $data,
                    $translations,
                    $imagesData,
                    $deletedImageIds,
                    $courseIds
                ) {
                    $bundle->update(
                        $data
                    );

                    $this->syncTranslations(
                        $bundle,
                        $translations
                    );

                    $this->syncImages(
                        $bundle,
                        $request,
                        $imagesData,
                        $deletedImageIds
                    );

                    $bundle
                        ->courses()
                        ->sync(
                            $courseIds
                        );
                }
            );

            return redirect()
                ->route(
                    'admin.schoolBundles.index'
                )
                ->with(
                    'success',
                    'Набор курсов успешно обновлён.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления school bundle ID '
                . $bundle->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при обновлении набора курсов.'
                );
        }
    }

    /**
     * =========================================================
     * DELETE
     * =========================================================
     */

    /** Удаление набора курсов. */
    public function destroy(
        int $schoolBundle
    ): RedirectResponse {
        $bundle = $this->baseQuery()
            ->with([
                'images.media',
            ])
            ->findOrFail(
                $schoolBundle
            );

        try {
            DB::transaction(
                function () use ($bundle) {
                    $imageIds = $bundle
                        ->images()
                        ->pluck(
                            'school_bundle_images.id'
                        )
                        ->toArray();

                    if (!empty($imageIds)) {
                        $bundle
                            ->images()
                            ->detach();

                        $this->deleteImages(
                            $imageIds
                        );
                    }

                    $bundle
                        ->courses()
                        ->detach();

                    $bundle
                        ->translations()
                        ->delete();

                    $bundle->delete();
                }
            );

            return redirect()
                ->route(
                    'admin.schoolBundles.index'
                )
                ->with(
                    'success',
                    'Набор курсов успешно удалён.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка удаления school bundle ID '
                . $bundle->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении набора курсов.'
            );
        }
    }

    /**
     * =========================================================
     * SELECT HELPERS
     * =========================================================
     */

    /**
     * Курсы для select.
     *
     * Controller загружает:
     *
     * translations(currentLocale)
     * images.media
     */
    private function coursesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $courses = SchoolCourse::query()
            ->with([
                'translations' =>
                    fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),

                'images.media',
            ])
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        return SchoolCourseSharedResource::collection(
            $courses
        );
    }

    /**
     * =========================================================
     * INDEX QUERIES
     * =========================================================
     */

    /**
     * Лёгкий query для count().
     */
    private function countQuery(): Builder
    {
        return $this->baseQuery();
    }

    /**
     * Базовый запрос Admin Index.
     */
    private function indexQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                /**
                 * Bundle translation:
                 * только currentLocale.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),

                /**
                 * Изображения.
                 */
                'images.media',

                /**
                 * Courses:
                 * только currentLocale.
                 */
                'courses' =>
                    fn ($query) =>
                    $query->with([
                        'translations' =>
                            fn (
                                $translationQuery
                            ) =>
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                ),
                    ]),

                /**
                 * Prices нужны Index.
                 */
                'prices',
            ])
            ->withCount([
                'courses',
                'images',
                'prices',
                'orderItems',
            ]);
    }

    /**
     * Получение списка наборов
     * по активному processing mode.
     */
    private function getIndexBundles(
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
