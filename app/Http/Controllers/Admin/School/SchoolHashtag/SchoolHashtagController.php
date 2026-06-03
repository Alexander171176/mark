<?php

namespace App\Http\Controllers\Admin\School\SchoolHashtag;

use App\Http\Controllers\Admin\School\Base\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolHashtag\SchoolHashtagRequest;
use App\Http\Resources\Admin\School\SchoolHashtag\SchoolHashtagResource;
use App\Models\Admin\School\SchoolHashtag\SchoolHashtag;
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

    /** Расширение сортировки для хештегов. */
    protected function extendedSortMap(): array
    {
        return [
            'viewsAsc' => 'views_asc',
            'viewsDesc' => 'views_desc',

            'activity' => 'activity',
            'inactive' => 'inactive',
        ];
    }

    /** Список хештегов */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $adminSchoolHashtagsPerPage = (int) config('site_settings.adminSchoolHashtagsPerPage', 20);
        $adminSchoolHashtagsDefaultSort = (string) config('site_settings.adminSchoolHashtagsDefaultSort', 'idDesc');
        $sort = $this->normalizeSortParam($adminSchoolHashtagsDefaultSort);

        $hashtags = $this->baseQuery()
            ->with(['translation', 'translations'])
            ->withCount(['courses', 'modules', 'lessons'])
            ->when($sort === 'activity', fn ($query) => $query->where('activity', true))
            ->when($sort === 'inactive', fn ($query) => $query->where('activity', false))
            ->when($sort === 'views_asc', fn ($query) => $query->orderBy('views')->orderByDesc('id'))
            ->when($sort === 'views_desc', fn ($query) => $query->orderByDesc('views')->orderByDesc('id'))
            ->when($sort === 'sort_asc', fn ($query) => $query->orderBy('sort')->orderByDesc('id'))
            ->when($sort === 'sort_desc', fn ($query) => $query->orderByDesc('sort')->orderByDesc('id'))
            ->when($sort === 'date_asc', fn ($query) => $query->orderBy('id')->orderByDesc('id'))
            ->when($sort === 'date_desc', fn ($query) => $query->orderByDesc('id'))
            ->get();

        return Inertia::render('Admin/School/Hashtags/Index', [
            'hashtags' => SchoolHashtagResource::collection($hashtags),
            'hashtagsCount' => $this->baseQuery()->count(),

            'adminSchoolHashtagsPerPage' => $adminSchoolHashtagsPerPage,
            'adminSchoolHashtagsDefaultSort' => $adminSchoolHashtagsDefaultSort,

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Страница создания хештега */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/School/Hashtags/Create', [
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
    public function edit(int $schoolHashtag, Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $hashtag = $this->baseQuery()
            ->with(['translation', 'translations'])
            ->withCount(['courses', 'modules', 'lessons'])
            ->findOrFail($schoolHashtag);

        return Inertia::render('Admin/School/Hashtags/Edit', [
            'hashtag' => new SchoolHashtagResource($hashtag),
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
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
}
