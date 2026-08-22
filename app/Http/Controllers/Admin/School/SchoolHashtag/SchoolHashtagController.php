<?php

namespace App\Http\Controllers\Admin\School\SchoolHashtag;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolHashtag\SchoolHashtagRequest;
use App\Http\Resources\Admin\School\SchoolHashtag\SchoolHashtagResource;
use App\Http\Resources\Admin\School\SchoolHashtag\SchoolHashtagSharedResource;
use App\Models\Admin\School\SchoolHashtag\SchoolHashtag;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления полиморфными хештегами (Hashtags) в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное и массовое)
 * - drag&drop
 *
 * @version 1.1 (мультиязычеая архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolHashtag
 * @see SchoolHashtagRequest
 */
class SchoolHashtagController extends BaseSchoolAdminController
{
    /** Основная модель */
    protected string $modelClass = SchoolHashtag::class;

    /** Название сущности */
    protected string $entityLabel = 'хештега';

    /** Поля переводов */
    protected array $translationFields = [
        'name',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Список хештегов */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminSchoolHashtagsPerPage', 6);
        $defaultSort = $settings->string('adminSchoolHashtagsDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminSchoolHashtagsProcessingMode',
            'frontend'
        );

        $hashtagsCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $hashtagsCount,
                300
            );

        try {
            $hashtags = $this->getIndexHashtags(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/School/SchoolHashtags/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminSchoolHashtagsPerPage' => $perPage,
                'adminSchoolHashtagsDefaultSort' => $defaultSort,
                'adminSchoolHashtagsProcessingMode' => $processingMode,

                'hashtags' => SchoolHashtagSharedResource::collection($hashtags),
                'hashtagsCount' => $hashtagsCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка school hashtags: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/SchoolHashtags/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminSchoolHashtagsPerPage' => $perPage,
                'adminSchoolHashtagsDefaultSort' => $defaultSort,
                'adminSchoolHashtagsProcessingMode' => $processingMode,

                'hashtags' => [],
                'hashtagsCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки хештегов.',
            ]);
        }
    }

    /** Страница создания хештега */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/School/SchoolHashtags/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Создание хештега */
    public function store(SchoolHashtagRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        DB::transaction(function () use ($data, $translations) {
            if (!isset($data['sort']) || is_null($data['sort'])) {
                $maxSort = SchoolHashtag::query()->max('sort');
                $data['sort'] = is_null($maxSort) ? 1 : $maxSort + 1;
            }

            $hashtag = SchoolHashtag::create($data);

            $this->syncTranslations($hashtag, $translations);
        });

        return redirect()
            ->route('admin.schoolHashtags.index')
            ->with('success', 'Хештег успешно создан.');
    }

    /** Редирект на страницу редактирования */
    public function show(int $schoolHashtag): RedirectResponse
    {
        return redirect()->route('admin.schoolHashtags.edit', $schoolHashtag);
    }

    /** Страница редактирования хештега */
    public function edit(
        int $schoolHashtag,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $hashtag = $this->baseQuery()
            ->with([
                /**
                 * Edit должен получить
                 * все переводы одной выборкой.
                 */
                'translations',
            ])
            ->findOrFail(
                $schoolHashtag
            );

        return Inertia::render(
            'Admin/School/SchoolHashtags/Edit',
            [
                'hashtag' =>
                    new SchoolHashtagResource(
                        $hashtag
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),
            ]
        );
    }

    /** Обновление хештега */
    public function update(SchoolHashtagRequest $request, int $schoolHashtag): RedirectResponse
    {
        $hashtag = $this->baseQuery()->findOrFail($schoolHashtag);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        DB::transaction(function () use ($hashtag, $data, $translations) {
            $hashtag->update($data);

            $this->syncTranslations($hashtag, $translations);
        });

        return redirect()
            ->route('admin.schoolHashtags.index')
            ->with('success', 'Хештег успешно обновлён.');
    }

    /** Удаление хештега */
    public function destroy(int $schoolHashtag): RedirectResponse
    {
        $hashtag = $this->baseQuery()->findOrFail($schoolHashtag);

        DB::transaction(function () use ($hashtag) {
            DB::table('school_hashtaggables')
                ->where('school_hashtag_id', $hashtag->id)
                ->delete();

            $hashtag->translations()->delete();
            $hashtag->delete();
        });

        return redirect()
            ->route('admin.schoolHashtags.index')
            ->with('success', 'Хештег успешно удалён.');
    }

    /** Массовое удаление хештегов */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_hashtags,id'],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with('error', 'Часть хештегов недоступна для удаления.');
        }

        try {
            DB::transaction(function () use ($allowedIds) {
                DB::table('school_hashtaggables')
                    ->whereIn('school_hashtag_id', $allowedIds)
                    ->delete();

                $hashtags = SchoolHashtag::query()
                    ->whereIn('id', $allowedIds)
                    ->get();

                foreach ($hashtags as $hashtag) {
                    $hashtag->translations()->delete();
                    $hashtag->delete();
                }
            });

            return back()->with('success', 'Выбранные хештеги успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка bulkDestroy school hashtags: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении хештегов.');
        }
    }

    /** Базовый запрос для списка хештегов. */
    private function indexQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                /**
                 * Для Admin Index нужен
                 * только выбранный перевод.
                 */
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),
            ])
            ->withCount([
                'courses',
                'modules',
                'lessons',
            ]);
    }

    /** Получение списка хештегов по активному режиму обработки. */
    private function getIndexHashtags(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery(
            $locale
        );

        /**
         * Server mode:
         * поиск, сортировка и пагинация — SQL.
         */
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

        /**
         * Frontend mode:
         * Vue выполняет поиск,
         * сортировку и пагинацию.
         */
        return $query
            ->orderByDesc(
                'school_hashtags.id'
            )
            ->get();
    }
}
