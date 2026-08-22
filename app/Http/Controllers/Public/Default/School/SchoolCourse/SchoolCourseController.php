<?php

namespace App\Http\Controllers\Public\Default\School\SchoolCourse;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\School\SchoolCourse\SchoolCourseResource;
use App\Http\Resources\Public\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolCourseController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /** Страница списка курсов. */
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
                'title' => __('Курсы'),
                'keywords' => '',
                'description' => '',
            ];

        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int('publicSchoolCoursesPerPage', 12)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string('publicSchoolCoursesDefaultSort', 'idDesc')
        );

        $view = $this->resolveView(
            $request,
            $settings->string('publicSchoolCoursesDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicSchoolCoursesProcessingMode', 'server')
        );

        $coursesCount = SchoolCourse::query()
            ->forPublic($locale)
            ->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $coursesCount,
                300
            );

        $courses = $this->getIndexCourses(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        $coursesFound = $useServerProcessing
            ? $courses->total()
            : $courses->count();

        /**
         * Public Index всегда использует
         * краткий Public Resource.
         *
         * already_liked уже находится
         * внутри моделей благодаря
         * withUserLike().
         */
        $courses =
            SchoolCourseSharedResource::collection(
                $courses
            );

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/SchoolCourses/Index', [

            'seo' => $seo,

            'publicSchoolCoursesProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'courses' => $courses,

            'coursesCount' => $coursesCount,
            'coursesFound' => $coursesFound,

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
        ]);
    }

    /** Страница конкретного курса. */
    public function show(
        string $slug
    ): Response {
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

        /**
         * Основной запрос курса.
         */
        $courseQuery = SchoolCourse::query()
            ->forPublic(
                $locale
            )
            ->where(
                'slug',
                $slug
            )
            ->with([
                /**
                 * translations курса уже
                 * загружает forPublic().
                 */

                /**
                 * Изображения курса.
                 */
                'images.media',

                /**
                 * Инструктор.
                 */
                'instructorProfile.translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                'instructorProfile.images.media',

                /**
                 * Треки.
                 */
                'tracks.translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                'tracks.images.media',

                /**
                 * Хештеги.
                 */
                'hashtags.translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                /**
                 * Цены.
                 */
                'prices',

                /**
                 * Отзывы.
                 */
                'reviews' =>
                    fn ($query) =>
                    $query
                        ->with(
                            'user:id,name'
                        )
                        ->latest(),

                /**
                 * Рекомендованные курсы.
                 */
                'relatedCourses' =>
                    function ($query) use (
                        $locale,
                        $locales
                    ) {
                        $query
                            ->forPublic(
                                $locale
                            )
                            ->with([
                                'images.media',

                                'instructorProfile.translations' =>
                                    fn ($translationQuery) =>
                                    $translationQuery->whereIn(
                                        'locale',
                                        $locales
                                    ),

                                'instructorProfile.images.media',
                            ])
                            ->withCount([
                                'likes',
                            ])
                            ->ordered();

                        /**
                         * already_liked для всех
                         * related courses одним EXISTS.
                         */
                        $this->withUserLike(
                            $query
                        );
                    },

                /**
                 * Модули.
                 *
                 * Пока оставляем их текущий
                 * ресурс до отдельного
                 * рефакторинга SchoolModule.
                 */
                'modules' =>
                    function ($query) use (
                        $locale
                    ) {
                        $query
                            ->forPublic(
                                $locale
                            )
                            ->with([
                                'translation',
                                'translations',
                                'images',

                                'lessons' =>
                                    fn ($lessonQuery) =>
                                    $lessonQuery
                                        ->forPublic(
                                            $locale
                                        )
                                        ->with([
                                            'translation',
                                            'translations',
                                            'images',
                                        ])
                                        ->ordered(),
                            ])
                            ->withCount([
                                'lessons',
                                'likes',
                            ])
                            ->ordered();

                        /**
                         * already_liked сразу
                         * для всех модулей.
                         */
                        $this->withUserLike(
                            $query
                        );
                    },
            ])
            ->withCount([
                'modules',
                'lessons',
                'tracks',
                'hashtags',
                'images',
                'prices',
                'reviews',
                'likes',
            ]);

        /**
         * already_liked курса
         * добавляется в основной SQL.
         */
        $courseQuery = $this->withUserLike(
            $courseQuery
        );

        $course =
            $courseQuery->firstOrFail();

        /**
         * Просмотр.
         */
        $course->increment(
            'views'
        );

        /**
         * Полный Public Resource.
         */
        $courseData =
            new SchoolCourseResource(
                $course
            );

        /**
         * SchoolModule пока ещё
         * не прошёл новый Public Resource
         * refactoring.
         *
         * already_liked уже присутствует
         * на модели благодаря withUserLike().
         */
        $modules =
            SchoolModuleResource::collection(
                $course->modules
            );

        $trackTree =
            $this->buildTrackTree(
                $locale
            );

        $sidebarData =
            $this->getSidebarData(
                $locale
            );

        return Inertia::render(
            'Public/Default/School/SchoolCourses/Show',
            [
                'course' =>
                    $courseData,

                'modules' =>
                    $modules,

                'trackTree' =>
                    $trackTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
    }

    /** Лайк курса. */
    public function like(string $id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $course = SchoolCourse::query()
            ->forPublic()
            ->findOrFail($id);

        $userId = auth()->id();

        if ($course->likes()->where('user_id', $userId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $course->likes()->count(),
            ]);
        }

        $course->likes()->create([
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $course->likes()->count(),
        ]);
    }

    /** Базовый запрос Public Index курсов. */
    private function indexQuery(
        string $locale
    ): Builder {
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

        $query = SchoolCourse::query()
            ->forPublic(
                $locale
            )
            ->with([
                /**
                 * Сам forPublic() уже загружает
                 * translations current + fallback.
                 *
                 * Здесь повторно translations
                 * указывать не нужно.
                 */

                /**
                 * Изображения курса + Spatie Media.
                 */
                'images.media',

                /**
                 * Инструктор:
                 * current + fallback.
                 */
                'instructorProfile.translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                /**
                 * Изображения инструктора.
                 */
                'instructorProfile.images.media',
            ])
            ->withCount([
                'modules',
                'lessons',
                'tracks',
                'hashtags',
                'reviews',
                'likes',
            ]);

        /**
         * already_liked одним EXISTS.
         *
         * Для гостя SQL не добавляется.
         */
        return $this->withUserLike(
            $query
        );
    }

    /** Получение списка публичных курсов по активному режиму обработки. */
    private function getIndexCourses(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query =
            $this->indexQuery(
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
