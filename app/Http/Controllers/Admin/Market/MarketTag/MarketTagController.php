<?php

namespace App\Http\Controllers\Admin\Market\MarketTag;

use App\Http\Controllers\Admin\Market\BaseMarketAdminController;
use App\Http\Requests\Admin\Market\MarketTag\MarketTagRequest;
use App\Http\Resources\Admin\Market\MarketTag\MarketTagResource;
use App\Models\Admin\Market\MarketTag\MarketTag;
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
 * Контроллер для управления Тегами товаров (MarketTag) в админке.
 *
 * Паттерн:
 * - Поиск, Пагинация, сортировка (режимы: frontend | auto | server )
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - activity (single + bulk)
 * - delete (single + bulk)
 * - sort + drag&drop (bulk)
 * - moderation (approve/reject) только для admin
 *
 * @version 1.1 (мультиязычеая архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */
class MarketTagController extends BaseMarketAdminController
{
    /** Основная модель контроллера */
    protected string $modelClass = MarketTag::class;

    /** Название сущности для сообщений */
    protected string $entityLabel = 'тегов';

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

    /** Список тегов */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminMarketTagsPerPage', 10);
        $defaultSort = $settings->string('adminMarketTagsDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminMarketTagsProcessingMode',
            'frontend'
        );

        $tagsCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer($processingMode, $tagsCount, 300);

        try {
            $tags = $this->getIndexTags(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/Market/MarketTags/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminMarketTagsPerPage' => $perPage,
                'adminMarketTagsDefaultSort' => $defaultSort,
                'adminMarketTagsProcessingMode' => $processingMode,

                'tags' => MarketTagResource::collection($tags),
                'tagsCount' => $tagsCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка market tags: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Market/MarketTags/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminMarketTagsPerPage' => $perPage,
                'adminMarketTagsDefaultSort' => $defaultSort,
                'adminMarketTagsProcessingMode' => $processingMode,

                'tags' => [],
                'tagsCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки тегов.',
            ]);
        }
    }

    /** Страница создания тега */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/Market/MarketTags/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Создание тега */
    public function store(MarketTagRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset($data['translations']);

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;

            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
        } else {
            $data['user_id'] = $data['user_id'] ?? $user?->id;
        }

        try {
            DB::transaction(function () use (&$tag, $data, $translations) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = MarketTag::query()->max('sort');
                    $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
                }

                $tag = MarketTag::create($data);

                $this->syncTranslations($tag, $translations);
            });

            return redirect()
                ->route('admin.marketTags.index')
                ->with('success', 'Тег успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка при создании market tag: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании тега.');
        }
    }

    /** Перенаправление просмотра на редактирование */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.marketTags.edit', $id);
    }

    /** Страница редактирования тега */
    public function edit(int $marketTag, Request $request): Response
    {
        $tag = $this->baseQuery()
            ->with([
                'owner',
                'moderator',
                'translations',
            ])
            ->findOrFail($marketTag);

        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/Market/MarketTags/Edit', [
            'tag' => new MarketTagResource($tag),

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Обновление тега */
    public function update(
        MarketTagRequest $request,
        int $marketTag
    ): RedirectResponse {
        $tag = $this->baseQuery()->findOrFail($marketTag);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset(
            $data['translations'],
            $data['_method']
        );

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;

            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
        }

        try {
            DB::transaction(function () use ($tag, $data, $translations) {
                $tag->update($data);

                $this->syncTranslations($tag, $translations);
            });

            return redirect()
                ->route('admin.marketTags.index')
                ->with('success', 'Тег успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка при обновлении market tag ID ' . $tag->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении тега.');
        }
    }

    /** Удаление тега */
    public function destroy(int $marketTag): RedirectResponse
    {
        $tag = $this->baseQuery()
            ->with('translations')
            ->findOrFail($marketTag);

        try {
            DB::transaction(function () use ($tag) {
                $tag->translations()->delete();
                $tag->delete();
            });

            return redirect()
                ->route('admin.marketTags.index')
                ->with('success', 'Тег успешно удалён.');
        } catch (Throwable $e) {
            Log::error('Ошибка при удалении market tag ID ' . $tag->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении тега.');
        }
    }

    /** Массовое удаление тегов */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:market_tags,id'],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with('error', 'Часть тегов недоступна для удаления.');
        }

        try {
            DB::transaction(function () use ($allowedIds) {
                DB::table('market_tag_translations')
                    ->whereIn('market_tag_id', $allowedIds)
                    ->delete();

                MarketTag::query()
                    ->whereIn('id', $allowedIds)
                    ->delete();
            });

            return back()->with('success', 'Выбранные теги успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка bulkDestroy market tags: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении тегов.');
        }
    }

    /** Базовый запрос списка тегов */
    private function indexQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                'owner',
                'moderator',
                'translations',
            ]);
    }

    /** Получение списка тегов для индекса */
    private function getIndexTags(
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
            ->ordered()
            ->get();
    }
}
