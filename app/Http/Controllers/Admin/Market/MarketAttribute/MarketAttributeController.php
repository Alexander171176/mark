<?php

namespace App\Http\Controllers\Admin\Market\MarketAttribute;

use App\Http\Controllers\Admin\Market\BaseMarketAdminController;
use App\Http\Requests\Admin\Market\MarketAttribute\MarketAttributeRequest;
use App\Http\Resources\Admin\Market\MarketAttribute\MarketAttributeResource;
use App\Http\Resources\Admin\Market\MarketAttributeGroup\MarketAttributeGroupSharedResource;
use App\Models\Admin\Market\MarketAttribute\MarketAttribute;
use App\Models\Admin\Market\MarketAttributeGroup\MarketAttributeGroup;
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
 * Контроллер для управления Характеристиками (MarketAttribute) в админке.
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
class MarketAttributeController extends BaseMarketAdminController
{
    /** Основная модель контроллера */
    protected string $modelClass = MarketAttribute::class;

    /** Название сущности для сообщений */
    protected string $entityLabel = 'характеристик';

    /** Поля переводов */
    protected array $translationFields = [
        'title',
        'subtitle',
        'short',
        'description',
    ];

    /** Список характеристик */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminMarketAttributesPerPage', 10);
        $defaultSort = $settings->string('adminMarketAttributesDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminMarketAttributesProcessingMode',
            'frontend'
        );

        $attributesCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer($processingMode, $attributesCount, 300);

        try {
            $attributes = $this->getIndexAttributes(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/Market/MarketAttributes/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminMarketAttributesPerPage' => $perPage,
                'adminMarketAttributesDefaultSort' => $defaultSort,
                'adminMarketAttributesProcessingMode' => $processingMode,

                'attributes' => MarketAttributeResource::collection($attributes),
                'attributesCount' => $attributesCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка market attributes: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Market/MarketAttributes/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminMarketAttributesPerPage' => $perPage,
                'adminMarketAttributesDefaultSort' => $defaultSort,
                'adminMarketAttributesProcessingMode' => $processingMode,

                'attributes' => [],
                'attributesCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки характеристик.',
            ]);
        }
    }

    /** Страница создания характеристики */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/Market/MarketAttributes/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'groups' => MarketAttributeGroupSharedResource::collection(
                $this->groupsForSelect()
            ),
        ]);
    }

    /** Создание характеристики */
    public function store(MarketAttributeRequest $request): RedirectResponse
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
            DB::transaction(function () use (&$attribute, $data, $translations) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = MarketAttribute::query()
                        ->where('market_attribute_group_id', $data['market_attribute_group_id'])
                        ->max('sort');

                    $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
                }

                $attribute = MarketAttribute::create($data);

                $this->syncTranslations($attribute, $translations);
            });

            return redirect()
                ->route('admin.marketAttributes.index')
                ->with('success', 'Характеристика успешно создана.');
        } catch (Throwable $e) {
            Log::error('Ошибка при создании market attribute: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании характеристики.');
        }
    }

    /** Перенаправление просмотра на редактирование */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.marketAttributes.edit', $id);
    }

    /** Страница редактирования характеристики */
    public function edit(int $marketAttribute, Request $request): Response
    {
        $attribute = $this->baseQuery()
            ->with([
                'group.translations',
                'owner',
                'moderator',
                'translations',
            ])
            ->withCount('values')
            ->findOrFail($marketAttribute);

        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/Market/MarketAttributes/Edit', [
            'attribute' => new MarketAttributeResource($attribute),

            'groups' => MarketAttributeGroupSharedResource::collection(
                $this->groupsForSelect()
            ),

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Обновление характеристики */
    public function update(
        MarketAttributeRequest $request,
        int $marketAttribute
    ): RedirectResponse {
        $attribute = $this->baseQuery()->findOrFail($marketAttribute);

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
            DB::transaction(function () use ($attribute, $data, $translations) {
                $attribute->update($data);

                $this->syncTranslations($attribute, $translations);
            });

            return redirect()
                ->route('admin.marketAttributes.index')
                ->with('success', 'Характеристика успешно обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка при обновлении market attribute ID ' . $attribute->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении характеристики.');
        }
    }

    /** Удаление характеристики */
    public function destroy(int $marketAttribute): RedirectResponse
    {
        $attribute = $this->baseQuery()
            ->with('translations')
            ->findOrFail($marketAttribute);

        if ($attribute->values()->exists()) {
            return back()->with(
                'error',
                'Нельзя удалить характеристику, к которой привязаны значения.'
            );
        }

        try {
            DB::transaction(function () use ($attribute) {
                $attribute->translations()->delete();
                $attribute->delete();
            });

            return redirect()
                ->route('admin.marketAttributes.index')
                ->with('success', 'Характеристика успешно удалена.');
        } catch (Throwable $e) {
            Log::error('Ошибка при удалении market attribute ID ' . $attribute->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении характеристики.');
        }
    }

    /** Массовое удаление характеристик */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:market_attributes,id'],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with('error', 'Часть характеристик недоступна для удаления.');
        }

        $hasValues = MarketAttribute::query()
            ->whereIn('id', $allowedIds)
            ->whereHas('values')
            ->exists();

        if ($hasValues) {
            return back()->with(
                'error',
                'Нельзя удалить характеристики, к которым привязаны значения.'
            );
        }

        try {
            DB::transaction(function () use ($allowedIds) {
                DB::table('market_attribute_translations')
                    ->whereIn('market_attribute_id', $allowedIds)
                    ->delete();

                MarketAttribute::query()
                    ->whereIn('id', $allowedIds)
                    ->delete();
            });

            return back()->with('success', 'Выбранные характеристики успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка bulkDestroy market attributes: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении характеристик.');
        }
    }

    /** Базовый запрос списка характеристик */
    private function indexQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                'group.translations',
                'owner',
                'moderator',
                'translations',
            ])
            ->withCount('values');
    }

    /** Получение списка характеристик для индекса */
    private function getIndexAttributes(
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

    /** Группы характеристик для select */
    private function groupsForSelect()
    {
        return MarketAttributeGroup::query()
            ->with('translations')
            ->withCount('attributes')
            ->active()
            ->approved()
            ->ordered()
            ->get();
    }
}
