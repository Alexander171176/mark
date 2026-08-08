<?php

namespace App\Http\Controllers\Public\Default\School\SchoolInstructor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseResource;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolInstructorController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /** Страница списка инструкторов. */
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
                'title' => __('Инструкторы'),
                'keywords' => '',
                'description' => '',
            ];

        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int('publicSchoolInstructorsPerPage', 12)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string('publicSchoolInstructorsDefaultSort', 'idDesc')
        );

        $view = $this->resolveView(
            $request,
            $settings->string('publicSchoolInstructorsDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicSchoolInstructorsProcessingMode', 'server')
        );

        $instructorProfilesCount = SchoolInstructorProfile::query()
            ->forPublic($locale)
            ->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $instructorProfilesCount,
                300
            );

        $instructorProfiles = $this->getIndexInstructorProfiles(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        $instructorProfilesFound = $useServerProcessing
            ? $instructorProfiles->total()
            : $instructorProfiles->count();

        $instructorProfiles = SchoolInstructorProfileResource::collection($instructorProfiles);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/SchoolInstructors/Index', [

            'seo' => $seo,

            'publicSchoolInstructorsProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'instructorProfiles' => $instructorProfiles,

            'instructorProfilesCount' => $instructorProfilesCount,
            'instructorProfilesFound' => $instructorProfilesFound,

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

    /** Страница конкретного инструктора. */
    public function show(Request $request, string $slug): Response
    {
        $locale = app()->getLocale();

        $instructorProfile = SchoolInstructorProfile::query()
            ->forPublic($locale)
            ->where('slug', $slug)
            ->with([
                'translation',
                'translations',
                'user:id,name,email',
                'images',
            ])
            ->withCount([
                'courses',
                'payouts',
            ])
            ->firstOrFail();

        $instructorProfile->increment('views');

        $coursesSearch = $this->resolveSearch($request, 'q_courses');

        $perPageCourses = $this->resolvePerPage(
            $request,
            (int) config('site_settings.publicSchoolCoursesPerPage', 6),
            3,
            60
        );

        $coursesSort = (string) $request->query(
            'sort_courses',
            config('site_settings.publicSchoolCoursesDefaultSort', 'sortAsc')
        );

        $courses = SchoolCourse::query()
            ->forPublic($locale)
            ->where('school_instructor_profile_id', $instructorProfile->id)
            ->search($coursesSearch, $locale)
            ->with([
                'translation',
                'translations',
                'images',
                'instructorProfile',
                'instructorProfile.translation',
                'instructorProfile.translations',
                'instructorProfile.images',
                'tracks.translation',
                'tracks.translations',
                'hashtags.translation',
                'hashtags.translations',
                'prices',
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
            ])
            ->sortByParam($coursesSort, $locale)
            ->paginate($perPageCourses, ['*'], 'page_courses')
            ->withQueryString();

        $courses = $this->appendUserLikes(
            $courses,
            SchoolCourseResource::class
        );

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/SchoolInstructors/Show', [
            'instructorProfile' => new SchoolInstructorProfileResource($instructorProfile),

            'courses' => $courses,
            'coursesFound' => $courses->total(),

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

    /** Базовый запрос для списка публичных инструкторов. */
    private function indexQuery(string $locale): Builder
    {
        return SchoolInstructorProfile::query()
            ->forPublic($locale)
            ->with([
                'translation',
                'translations',
                'user:id,name,email',
                'images',
            ])
            ->withCount([
                'courses',
                'payouts',
            ]);
    }

    /** Получение списка публичных инструкторов по активному режиму обработки. */
    private function getIndexInstructorProfiles(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery($locale);

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
