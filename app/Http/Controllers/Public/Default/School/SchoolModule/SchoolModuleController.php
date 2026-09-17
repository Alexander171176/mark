<?php

namespace App\Http\Controllers\Public\Default\School\SchoolModule;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\School\SchoolLesson\SchoolLessonSharedResource;
use App\Http\Resources\Public\School\SchoolModule\SchoolModuleResource;
use App\Http\Resources\Public\School\SchoolModule\SchoolModuleSharedResource;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
use App\Traits\Public\School\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolModuleController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /** Страница списка модулей. */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $cmsSeoPage = app(CmsPageResolverService::class)
            ->resolveSeo($request->path());

        $cmsSeoTranslation = $cmsSeoPage?->translationOrFallback();

        $seo = $cmsSeoTranslation
            ? [
                'title' => $cmsSeoTranslation->meta_title ?: $cmsSeoTranslation->title,
                'keywords' => $cmsSeoTranslation->meta_keywords,
                'description' => $cmsSeoTranslation->meta_desc ?: $cmsSeoTranslation->short,
            ]
            : [
                'title' => __('Модули'),
                'keywords' => '',
                'description' => '',
            ];

        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int('publicSchoolModulesPerPage', 12)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string('publicSchoolModulesDefaultSort', 'idDesc')
        );

        $view = $this->resolveView(
            $request,
            $settings->string('publicSchoolModulesDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicSchoolModulesProcessingMode', 'server')
        );

        /**
         * Decision COUNT нужен только режиму auto.
         *
         * server:
         * режим уже известен, а total получим из paginator.
         *
         * frontend:
         * режим уже известен, а количество получим
         * из загруженной Collection.
         *
         * auto:
         * предварительный COUNT необходим только
         * для выбора между server и frontend.
         */
        $decisionCount = null;

        if ($processingMode === 'auto') {
            $decisionCount = SchoolModule::query()
                ->forPublic($locale)
                ->count();

            $useServerProcessing = app(ProcessingModeService::class)
                ->shouldUseServer(
                    $processingMode,
                    $decisionCount,
                    300
                );
        } else {
            $useServerProcessing = $processingMode === 'server';
        }

        $modules = $this->getIndexModules(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        /**
         * Фактическое количество найденных модулей.
         *
         * server:
         * total() уже вычислен Laravel paginator.
         *
         * frontend:
         * вся Public Collection уже загружена.
         */
        if ($useServerProcessing) {
            $modulesFound = $modules->total();

            /**
             * Без поиска paginator total одновременно
             * является общим количеством Public-модулей.
             */
            if ($decisionCount === null && $search === '') {
                $modulesCount = $modulesFound;
            } else {
                /**
                 * auto уже выполнил decision COUNT.
                 *
                 * Для server + search нужен отдельный
                 * unfiltered Public COUNT.
                 */
                $modulesCount = $decisionCount
                    ?? SchoolModule::query()
                        ->forPublic($locale)
                        ->count();
            }
        } else {
            /**
             * Frontend получил весь Public-набор.
             *
             * В auto используем decision COUNT,
             * в frontend — размер Collection.
             */
            $modulesFound = $modules->count();
            $modulesCount = $decisionCount ?? $modulesFound;
        }

        $modules = SchoolModuleSharedResource::collection($modules);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render(
            'Public/Default/School/SchoolModules/Index',
            [
                'seo' => $seo,

                'publicSchoolModulesProcessingMode' => $processingMode,
                'useServerProcessing' => $useServerProcessing,

                'modules' => $modules,

                'modulesCount' => $modulesCount,
                'modulesFound' => $modulesFound,

                'filters' => $this->buildIndexFilters(
                    $search,
                    $perPage,
                    $sort,
                    $view,
                    $processingMode
                ),

                'trackTree' => $trackTree,
                'locale' => $locale,

                ...$sidebarData,
            ]
        );
    }

    /** Страница конкретного модуля. */
    public function show(string $courseSlug, string $slug): Response
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        $moduleQuery = SchoolModule::query()
            ->forPublic($locale)
            ->where('slug', $slug)

            /**
             * Slug модуля уникален только в рамках курса.
             *
             * Поэтому Public Show идентифицирует модуль
             * по связке:
             *
             * course.slug + module.slug
             *
             * Дополнительно родительский курс должен
             * соответствовать Public-условиям.
             */
            ->whereHas('course', function ($courseQuery) use ($courseSlug, $locale) {
                $courseQuery
                    ->forPublic($locale)
                    ->where('slug', $courseSlug);
            })

            ->with([
                /**
                 * Сам SchoolModule::forPublic()
                 * уже загружает translations
                 * current + fallback.
                 *
                 * Повторно translation/translations
                 * здесь не загружаем.
                 */
                'images.media',

                /**
                 * Родительский курс.
                 *
                 * SchoolCourseSharedResource ожидает:
                 * - translations;
                 * - images;
                 * - instructorProfile.
                 */
                'course' => function ($query) use ($locales) {
                    $query
                        ->with([
                            'translations' => fn($translationQuery) => $translationQuery->whereIn(
                                'locale',
                                $locales
                            ),

                            'images.media',

                            'instructorProfile' => function ($instructorQuery) use ($locales) {
                                $instructorQuery->with([
                                    'translations' => fn($translationQuery) => $translationQuery->whereIn(
                                        'locale',
                                        $locales
                                    ),

                                    'images.media',
                                ]);
                            },
                        ]);
                },

                /**
                 * Только публичные уроки модуля.
                 *
                 * SchoolLesson::forPublic()
                 * уже загружает translations
                 * current + fallback.
                 */
                'lessons' => function ($query) use ($locale) {
                    $query
                        ->forPublic($locale)
                        ->with([
                            'images.media',
                        ])
                        ->withCount([
                            'likes',
                            'images',

                            /**
                             * Только публичные хештеги.
                             */
                            'hashtags as hashtags_count' =>
                                fn (Builder $hashtagQuery) =>
                                $hashtagQuery->forPublic($locale),
                        ])
                        ->ordered();

                    $this->withUserLike($query);
                },
            ])
            ->withCount([
                /**
                 * Только публичные уроки модуля.
                 */
                'lessons as lessons_count' =>
                    fn (Builder $query) =>
                    $query->forPublic($locale),

                'likes',
            ]);

        /**
         * already_liked самого модуля
         * добавляется в основной SQL.
         */
        $this->withUserLike($moduleQuery);

        $module = $moduleQuery->firstOrFail();

        /** Просмотр модуля. */
        $module->increment('views');

        /**
         * Полный новый Public Resource.
         */
        $moduleData = new SchoolModuleResource(
            $module
        );

        /**
         * Дочерние публичные уроки модуля.
         *
         * Для списка используется
         * краткий Public SharedResource.
         */
        $lessons = SchoolLessonSharedResource::collection(
            $module->lessons
        );

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render(
            'Public/Default/School/SchoolModules/Show',
            [
                'module' => $moduleData,
                'lessons' => $lessons,

                'trackTree' => $trackTree,
                'locale' => $locale,

                ...$sidebarData,
            ]
        );
    }

    /** Лайк модуля. */
    public function like(string $id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $locale = app()->getLocale();

        $module = SchoolModule::query()
            ->forPublic($locale)
            ->findOrFail($id);

        $userId = auth()->id();

        /**
         * Повторный лайк запрещаем.
         */
        if (
            $module->likes()
                ->where('user_id', $userId)
                ->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $module->likes()->count(),
            ]);
        }

        $module->likes()->create([
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $module->likes()->count(),
        ]);
    }

    /** Базовый запрос Public Index модулей. */
    private function indexQuery(string $locale): Builder
    {
        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        $query = SchoolModule::query()
            ->forPublic($locale)
            ->with([
                /**
                 * Сам forPublic() уже загружает
                 * translations current + fallback.
                 *
                 * Здесь повторно translations
                 * указывать не нужно.
                 */
                'images.media',

                /**
                 * Родительский курс нужен
                 * SchoolModuleSharedResource.
                 */
                'course' => function ($courseQuery) use ($locales) {
                    $courseQuery
                        ->with([
                            'translations' => fn($translationQuery) => $translationQuery->whereIn(
                                'locale',
                                $locales
                            ),

                            'images.media',

                            'instructorProfile' => function ($instructorQuery) use ($locales) {
                                $instructorQuery->with([
                                    'translations' => fn($translationQuery) => $translationQuery->whereIn(
                                        'locale',
                                        $locales
                                    ),

                                    'images.media',
                                ]);
                            },
                        ]);
                },
            ])
            ->withCount([
                /**
                 * Только публичные уроки модуля.
                 */
                'lessons as lessons_count' =>
                    fn (Builder $query) =>
                    $query->forPublic($locale),
                'likes',
            ]);

        /**
         * already_liked добавляем одним EXISTS
         * в основной SQL.
         *
         * Для гостя WithUserLikesTrait
         * ничего дополнительного не добавляет.
         */
        return $this->withUserLike($query);
    }

    /** Получение списка Public модулей по активному режиму обработки. */
    private function getIndexModules(
        string $locale,
        bool   $useServerProcessing,
        int    $perPage,
        string $sort,
        string $search = ''
    )
    {
        $query = $this->indexQuery($locale);

        if ($useServerProcessing) {
            return $query
                ->publicSearch(
                    $search,
                    $locale
                )
                ->publicSortByParam(
                    $sort,
                    $locale
                )
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->publicSortByParam(
                $sort,
                $locale
            )
            ->get();
    }
}
