<?php

namespace App\Http\Controllers\Public\Default\School\SchoolHashtag;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Public\School\SchoolHashtag\SchoolHashtagResource;
use App\Models\Admin\School\SchoolHashtag\SchoolHashtag;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolHashtagController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /**
     * Страница конкретного хештега школы.
     *
     * Список курсов поддерживает:
     * server / frontend / auto.
     */
    public function show(Request $request, string $slug): Response
    {
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        $locales = array_values(array_unique([
            $locale,
            $fallbackLocale,
        ]));

        $settings = app(PublicSettingsService::class);

        /* ======================== Hashtag ======================== */

        $hashtag = SchoolHashtag::query()
            ->forPublic($locale)
            ->where('slug', $slug)
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', $locales),
            ])
            ->withCount([
                'courses',
                'modules',
                'lessons',
            ])
            ->firstOrFail();

        $hashtag->increment('views');

        /* ======================== Course settings ======================== */

        /**
         * Единственный источник истины для количества
         * курсов на публичной странице.
         */
        $perPageCourses = $settings->int(
            'publicSchoolCoursesPerPage',
            12
        );

        $coursesSearch = $this->resolveSearch(
            $request,
            'q_courses'
        );

        $coursesSort = (string) $request->query(
            'sort_courses',
            $settings->string('publicSchoolCoursesDefaultSort', 'idDesc')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicSchoolCoursesProcessingMode', 'server')
        );

        /* ======================== Processing mode ======================== */

        $processingModeService = app(ProcessingModeService::class);

        $coursesCount = null;

        /**
         * Предварительный COUNT нужен только auto,
         * потому что режим должен принять решение
         * исходя из общего количества курсов.
         */
        if ($processingMode === 'auto') {
            $coursesCount = $hashtag
                ->courses()
                ->forPublic($locale)
                ->count();

            $useServerProcessing = $processingModeService->shouldUseServer(
                $processingMode,
                $coursesCount,
                300
            );
        } else {
            $useServerProcessing = $processingMode === 'server';
        }

        /* ======================== Courses ======================== */

        $courses = $this->getHashtagCourses(
            hashtag: $hashtag,
            locale: $locale,
            locales: $locales,
            useServerProcessing: $useServerProcessing,
            perPage: $perPageCourses,
            sort: $coursesSort,
            search: $coursesSearch,
        );

        if ($useServerProcessing) {
            $coursesFound = $courses->total();

            if ($coursesCount === null && $coursesSearch === '') {
                $coursesCount = $coursesFound;
            }

            if ($coursesCount === null) {
                $coursesCount = $hashtag
                    ->courses()
                    ->forPublic($locale)
                    ->count();
            }
        } else {
            $coursesFound = $courses->count();
            $coursesCount ??= $coursesFound;
        }

        $courses = SchoolCourseSharedResource::collection($courses);

        /* ======================== Sidebars ======================== */

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        /* ======================== Response ======================== */

        return Inertia::render('Public/Default/School/SchoolHashtags/Show', [
            'hashtag' => new SchoolHashtagResource($hashtag),

            'publicSchoolCoursesProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'courses' => $courses,
            'coursesCount' => $coursesCount,
            'coursesFound' => $coursesFound,

            'filters' => [
                'q_courses' => $coursesSearch,
                'per_page_courses' => $perPageCourses,
                'sort_courses' => $coursesSort,
            ],

            'trackTree' => $trackTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }

    /**
     * Базовый запрос публичных курсов
     * текущего хештега.
     */
    private function coursesQuery(
        SchoolHashtag $hashtag,
        string $locale,
        array $locales
    ): Builder|Relation {
        $query = $hashtag
            ->courses()
            ->forPublic($locale)
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', $locales),

                'images.media',

                'instructorProfile.translations' =>
                    fn ($query) => $query->whereIn('locale', $locales),

                'instructorProfile.images.media',
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
         * already_liked добавляется одним EXISTS
         * для авторизованного пользователя.
         */
        return $this->withUserLike($query);
    }

    /**
     * Получение курсов по активному
     * режиму обработки.
     */
    private function getHashtagCourses(
        SchoolHashtag $hashtag,
        string $locale,
        array $locales,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->coursesQuery(
            hashtag: $hashtag,
            locale: $locale,
            locales: $locales,
        );

        /**
         * Server:
         * поиск, сортировка и пагинация на backend.
         */
        if ($useServerProcessing) {
            return $query
                ->search($search, $locale)
                ->sortByParam($sort, $locale)
                ->paginate(
                    $perPage,
                    ['*'],
                    'page_courses'
                )
                ->withQueryString();
        }

        /**
         * Frontend:
         * backend отдаёт полный набор,
         * поиск и пагинация выполняются во Vue.
         */
        return $query
            ->sortByParam($sort, $locale)
            ->get();
    }
}
