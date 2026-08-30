<?php

namespace App\Http\Controllers\Admin\Market\MarketAttribute;

use App\Http\Controllers\Admin\Market\BaseMarketAdminController;
use App\Http\Requests\Admin\Market\MarketAttribute\MarketAttributeRequest;
use App\Http\Resources\Admin\Market\MarketAttribute\MarketAttributeResource;
use App\Http\Resources\Admin\Market\MarketAttribute\MarketAttributeSharedResource;
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

    /**
     * Список характеристик.
     *
     * Поддерживает:
     * - frontend;
     * - server;
     * - auto.
     *
     * Для Index загружается только перевод
     * текущей локали характеристики и её группы.
     */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminMarketAttributesPerPage',
            10
        );

        $defaultSort = $settings->string(
            'adminMarketAttributesDefaultSort',
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
            'adminMarketAttributesProcessingMode',
            'frontend'
        );

        /**
         * Первый COUNT используется
         * ProcessingModeService для выбора
         * frontend/server режима.
         */
        $attributesCount = $this->baseQuery()
            ->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $attributesCount,
            300
        );

        try {
            $attributes = $this->getIndexAttributes(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search
            );

            return Inertia::render(
                'Admin/Market/MarketAttributes/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminMarketAttributesPerPage' =>
                        $perPage,

                    'adminMarketAttributesDefaultSort' =>
                        $defaultSort,

                    'adminMarketAttributesProcessingMode' =>
                        $processingMode,

                    /**
                     * Index использует компактный
                     * SharedResource.
                     */
                    'attributes' =>
                        MarketAttributeSharedResource::collection(
                            $attributes
                        ),

                    'attributesCount' =>
                        $attributesCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка market attributes: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/Market/MarketAttributes/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminMarketAttributesPerPage' =>
                        $perPage,

                    'adminMarketAttributesDefaultSort' =>
                        $defaultSort,

                    'adminMarketAttributesProcessingMode' =>
                        $processingMode,

                    'attributes' => [],

                    'attributesCount' => 0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'error' =>
                        'Ошибка загрузки характеристик.',
                ]
            );
        }
    }

    /** Страница создания характеристики */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render(
            'Admin/Market/MarketAttributes/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'groups' =>
                    MarketAttributeGroupSharedResource::collection(
                        $this->groupsForSelect(
                            $currentLocale
                        )
                    ),
            ]
        );
    }

    /** Создание характеристики */
    public function store(
        MarketAttributeRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $translations =
            $data['translations'] ?? [];

        unset(
            $data['translations']
        );

        $user = auth()->user();

        /**
         * Обычный пользователь может создавать
         * характеристику только от своего имени
         * и не управляет модерацией.
         */
        if (
            $user
            && method_exists(
                $user,
                'hasRole'
            )
            && ! $user->hasRole('admin')
        ) {
            $data['user_id'] =
                $user->id;

            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
        } else {
            $data['user_id'] =
                $data['user_id']
                ?? $user?->id;
        }

        try {
            DB::transaction(
                function () use (
                    &$attribute,
                    $data,
                    $translations
                ): void {
                    if (
                        ! isset($data['sort'])
                        || $data['sort'] === null
                    ) {
                        $maxSort =
                            MarketAttribute::query()
                                ->where(
                                    'market_attribute_group_id',
                                    $data['market_attribute_group_id']
                                )
                                ->max('sort');

                        $data['sort'] =
                            $maxSort === null
                                ? 0
                                : $maxSort + 1;
                    }

                    $attribute =
                        MarketAttribute::create(
                            $data
                        );

                    $this->syncTranslations(
                        $attribute,
                        $translations
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.marketAttributes.index'
                )
                ->with(
                    'success',
                    'Характеристика успешно создана.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при создании market attribute: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при создании характеристики.'
                );
        }
    }

    /** Перенаправление просмотра на редактирование */
    public function show(
        string $id
    ): RedirectResponse {
        return redirect()->route(
            'admin.marketAttributes.edit',
            $id
        );
    }

    /**
     * Страница редактирования характеристики.
     *
     * Для самой характеристики намеренно
     * загружаются ВСЕ translations,
     * поскольку TranslationTabs редактирует
     * все локали.
     *
     * Группы для select загружаются отдельно
     * с переводом только текущей локали.
     */
    public function edit(
        int $marketAttribute,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $attribute = $this->baseQuery()
            ->with('translations')
            ->findOrFail(
                $marketAttribute
            );

        return Inertia::render(
            'Admin/Market/MarketAttributes/Edit',
            [
                'attribute' =>
                    new MarketAttributeResource(
                        $attribute
                    ),

                'groups' =>
                    MarketAttributeGroupSharedResource::collection(
                        $this->groupsForSelect(
                            $currentLocale
                        )
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),
            ]
        );
    }

    /** Обновление характеристики */
    public function update(
        MarketAttributeRequest $request,
        int $marketAttribute
    ): RedirectResponse {
        $attribute = $this->baseQuery()
            ->findOrFail(
                $marketAttribute
            );

        $data = $request->validated();

        $translations =
            $data['translations'] ?? [];

        unset(
            $data['translations'],
            $data['_method']
        );

        $user = auth()->user();

        /**
         * Обычный пользователь остаётся
         * владельцем своей характеристики
         * и не может изменять модерацию.
         */
        if (
            $user
            && method_exists(
                $user,
                'hasRole'
            )
            && ! $user->hasRole('admin')
        ) {
            $data['user_id'] =
                $user->id;

            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
        }

        try {
            DB::transaction(
                function () use (
                    $attribute,
                    $data,
                    $translations
                ): void {
                    $attribute->update(
                        $data
                    );

                    $this->syncTranslations(
                        $attribute,
                        $translations
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.marketAttributes.index'
                )
                ->with(
                    'success',
                    'Характеристика успешно обновлена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при обновлении market attribute ID '
                . $attribute->id
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
                    'Ошибка при обновлении характеристики.'
                );
        }
    }

    /** Удаление характеристики */
    public function destroy(
        int $marketAttribute
    ): RedirectResponse {
        $attribute = $this->baseQuery()
            ->findOrFail(
                $marketAttribute
            );

        /**
         * Нельзя удалить характеристику,
         * если к ней привязаны значения.
         */
        if ($attribute->values()->exists()) {
            return back()->with(
                'error',
                'Нельзя удалить характеристику, к которой привязаны значения.'
            );
        }

        try {
            DB::transaction(
                function () use (
                    $attribute
                ): void {
                    $attribute
                        ->translations()
                        ->delete();

                    $attribute->delete();
                }
            );

            return redirect()
                ->route(
                    'admin.marketAttributes.index'
                )
                ->with(
                    'success',
                    'Характеристика успешно удалена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при удалении market attribute ID '
                . $attribute->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении характеристики.'
            );
        }
    }

    /** Массовое удаление характеристик */
    public function bulkDestroy(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
            ],

            'ids.*' => [
                'required',
                'integer',
                'exists:market_attributes,id',
            ],
        ]);

        $ids = $validated['ids'];

        /**
         * Проверяем доступность всех характеристик
         * через baseQuery().
         */
        $allowedIds = $this->baseQuery()
            ->whereIn(
                'market_attributes.id',
                $ids
            )
            ->pluck(
                'market_attributes.id'
            )
            ->toArray();

        if (
            count($allowedIds)
            !== count($ids)
        ) {
            return back()->with(
                'error',
                'Часть характеристик недоступна для удаления.'
            );
        }

        /**
         * Нельзя удалять характеристики,
         * к которым привязаны значения.
         */
        $hasValues =
            MarketAttribute::query()
                ->whereIn(
                    'market_attributes.id',
                    $allowedIds
                )
                ->whereHas(
                    'values'
                )
                ->exists();

        if ($hasValues) {
            return back()->with(
                'error',
                'Нельзя удалить характеристики, к которым привязаны значения.'
            );
        }

        try {
            DB::transaction(
                function () use (
                    $allowedIds
                ): void {
                    DB::table(
                        'market_attribute_translations'
                    )
                        ->whereIn(
                            'market_attribute_id',
                            $allowedIds
                        )
                        ->delete();

                    MarketAttribute::query()
                        ->whereIn(
                            'market_attributes.id',
                            $allowedIds
                        )
                        ->delete();
                }
            );

            return back()->with(
                'success',
                'Выбранные характеристики успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка bulkDestroy market attributes: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом удалении характеристик.'
            );
        }
    }

    /**
     * Базовый запрос списка характеристик.
     *
     * Для Index:
     * - translations характеристики ограничены currentLocale;
     * - group.translations ограничены currentLocale;
     * - owner загружается одним batch-запросом;
     * - moderator не загружается;
     * - values_count считается через subquery.
     */
    private function indexQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                'group',

                'group.translations' =>
                    fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),

                'owner:id,name,email,profile_photo_path',

                'translations' =>
                    fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),
            ])
            ->withCount(
                'values'
            );
    }

    /**
     * Получение списка характеристик
     * для Admin Index.
     */
    private function getIndexAttributes(
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
            ->ordered()
            ->get();
    }

    /**
     * Группы характеристик для select.
     *
     * Загружается только перевод
     * текущей локали.
     *
     * attributes_count здесь не нужен.
     */
    private function groupsForSelect(
        string $locale
    ) {
        return MarketAttributeGroup::query()
            ->with([
                'translations' =>
                    fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),
            ])
            ->active()
            ->approved()
            ->ordered()
            ->get();
    }
}
