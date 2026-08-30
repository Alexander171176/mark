<?php

namespace App\Http\Controllers\Admin\Market\MarketAttributeGroup;

use App\Http\Controllers\Admin\Market\BaseMarketAdminController;
use App\Http\Requests\Admin\Market\MarketAttributeGroup\MarketAttributeGroupRequest;
use App\Http\Resources\Admin\Market\MarketAttributeGroup\MarketAttributeGroupResource;
use App\Http\Resources\Admin\Market\MarketAttributeGroup\MarketAttributeGroupSharedResource;
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
 * Контроллер для управления Группами характеристик (MarketAttributeGroup) в админке.
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
class MarketAttributeGroupController extends BaseMarketAdminController
{
    /** Основная модель контроллера */
    protected string $modelClass = MarketAttributeGroup::class;

    /** Название сущности для сообщений */
    protected string $entityLabel = 'групп характеристик';

    /** Поля переводов */
    protected array $translationFields = [
        'title',
        'subtitle',
        'short',
    ];

    /**
     * Список групп характеристик.
     *
     * Поддерживает:
     * - frontend;
     * - server;
     * - auto.
     *
     * Для Index загружается только перевод
     * текущей локали.
     */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $settings = app(
            AdminSettingsService::class
        );

        $perPage = $settings->int(
            'adminMarketAttributeGroupsPerPage',
            10
        );

        $defaultSort = $settings->string(
            'adminMarketAttributeGroupsDefaultSort',
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
            'adminMarketAttributeGroupsProcessingMode',
            'frontend'
        );

        /**
         * Первый COUNT используется
         * ProcessingModeService для выбора
         * frontend/server режима.
         */
        $groupsCount = $this->baseQuery()
            ->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $groupsCount,
            300
        );

        try {
            $groups = $this->getIndexGroups(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search
            );

            return Inertia::render(
                'Admin/Market/MarketAttributeGroups/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminMarketAttributeGroupsPerPage' =>
                        $perPage,

                    'adminMarketAttributeGroupsDefaultSort' =>
                        $defaultSort,

                    'adminMarketAttributeGroupsProcessingMode' =>
                        $processingMode,

                    /**
                     * Index использует компактный
                     * SharedResource.
                     */
                    'groups' =>
                        MarketAttributeGroupSharedResource::collection(
                            $groups
                        ),

                    'groupsCount' =>
                        $groupsCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка market attribute groups: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/Market/MarketAttributeGroups/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminMarketAttributeGroupsPerPage' =>
                        $perPage,

                    'adminMarketAttributeGroupsDefaultSort' =>
                        $defaultSort,

                    'adminMarketAttributeGroupsProcessingMode' =>
                        $processingMode,

                    'groups' => [],

                    'groupsCount' => 0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'error' =>
                        'Ошибка загрузки групп характеристик.',
                ]
            );
        }
    }

    /** Страница создания группы характеристик */
    public function create(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        return Inertia::render(
            'Admin/Market/MarketAttributeGroups/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),
            ]
        );
    }

    /** Создание группы характеристик */
    public function store(
        MarketAttributeGroupRequest $request
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
         * группу только от своего имени
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
                    &$group,
                    $data,
                    $translations
                ): void {
                    if (
                        ! isset($data['sort'])
                        || $data['sort'] === null
                    ) {
                        $maxSort =
                            MarketAttributeGroup::query()
                                ->max('sort');

                        $data['sort'] =
                            $maxSort === null
                                ? 0
                                : $maxSort + 1;
                    }

                    $group =
                        MarketAttributeGroup::create(
                            $data
                        );

                    $this->syncTranslations(
                        $group,
                        $translations
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.marketAttributeGroups.index'
                )
                ->with(
                    'success',
                    'Группа характеристик успешно создана.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при создании market attribute group: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при создании группы характеристик.'
                );
        }
    }

    /** Перенаправление просмотра на редактирование */
    public function show(
        string $id
    ): RedirectResponse {
        return redirect()->route(
            'admin.marketAttributeGroups.edit',
            $id
        );
    }

    /**
     * Страница редактирования группы характеристик.
     *
     * Для Edit намеренно загружаются
     * ВСЕ переводы группы, поскольку
     * TranslationTabs редактирует все локали.
     */
    public function edit(
        int $marketAttributeGroup,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $group = $this->baseQuery()
            ->with('translations')
            ->findOrFail(
                $marketAttributeGroup
            );

        return Inertia::render(
            'Admin/Market/MarketAttributeGroups/Edit',
            [
                'group' =>
                    new MarketAttributeGroupResource(
                        $group
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),
            ]
        );
    }

    /** Обновление группы характеристик */
    public function update(
        MarketAttributeGroupRequest $request,
        int $marketAttributeGroup
    ): RedirectResponse {
        $group = $this->baseQuery()
            ->findOrFail(
                $marketAttributeGroup
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
         * владельцем своей группы
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
                    $group,
                    $data,
                    $translations
                ): void {
                    $group->update(
                        $data
                    );

                    $this->syncTranslations(
                        $group,
                        $translations
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.marketAttributeGroups.index'
                )
                ->with(
                    'success',
                    'Группа характеристик успешно обновлена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при обновлении market attribute group ID '
                . $group->id
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
                    'Ошибка при обновлении группы характеристик.'
                );
        }
    }

    /**
     * Удаление группы характеристик.
     *
     * Предварительно загружать translations
     * не требуется: используется relation DELETE.
     */
    public function destroy(
        int $marketAttributeGroup
    ): RedirectResponse {
        $group = $this->baseQuery()
            ->findOrFail(
                $marketAttributeGroup
            );

        /**
         * Нельзя удалить группу,
         * если к ней привязаны характеристики.
         */
        if (
            $group
                ->attributes()
                ->exists()
        ) {
            return back()->with(
                'error',
                'Нельзя удалить группу, к которой привязаны характеристики.'
            );
        }

        try {
            DB::transaction(
                function () use (
                    $group
                ): void {
                    $group
                        ->translations()
                        ->delete();

                    $group->delete();
                }
            );

            return redirect()
                ->route(
                    'admin.marketAttributeGroups.index'
                )
                ->with(
                    'success',
                    'Группа характеристик успешно удалена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при удалении market attribute group ID '
                . $group->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении группы характеристик.'
            );
        }
    }

    /** Массовое удаление групп характеристик */
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
                'exists:market_attribute_groups,id',
            ],
        ]);

        $ids = $validated['ids'];

        /**
         * Проверяем доступность всех групп
         * через baseQuery().
         */
        $allowedIds = $this->baseQuery()
            ->whereIn(
                'market_attribute_groups.id',
                $ids
            )
            ->pluck(
                'market_attribute_groups.id'
            )
            ->toArray();

        if (
            count($allowedIds)
            !== count($ids)
        ) {
            return back()->with(
                'error',
                'Часть групп характеристик недоступна для удаления.'
            );
        }

        /**
         * Нельзя удалить группы,
         * к которым привязаны характеристики.
         */
        $hasAttributes =
            MarketAttributeGroup::query()
                ->whereIn(
                    'market_attribute_groups.id',
                    $allowedIds
                )
                ->whereHas(
                    'attributes'
                )
                ->exists();

        if ($hasAttributes) {
            return back()->with(
                'error',
                'Нельзя удалить группы, к которым привязаны характеристики.'
            );
        }

        try {
            DB::transaction(
                function () use (
                    $allowedIds
                ): void {
                    DB::table(
                        'market_attribute_group_translations'
                    )
                        ->whereIn(
                            'market_attribute_group_id',
                            $allowedIds
                        )
                        ->delete();

                    MarketAttributeGroup::query()
                        ->whereIn(
                            'market_attribute_groups.id',
                            $allowedIds
                        )
                        ->delete();
                }
            );

            return back()->with(
                'success',
                'Выбранные группы характеристик успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка bulkDestroy market attribute groups: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом удалении групп характеристик.'
            );
        }
    }

    /**
     * Базовый запрос списка групп характеристик.
     *
     * Для Index:
     * - owner загружается одним batch-запросом;
     * - translations ограничены currentLocale;
     * - moderator не загружается;
     * - attributes_count считается через subquery.
     */
    private function indexQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                'owner:id,name,email,profile_photo_path',

                'translations' =>
                    fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),
            ])
            ->withCount(
                'attributes'
            );
    }

    /**
     * Получение списка групп характеристик
     * для Admin Index.
     */
    private function getIndexGroups(
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
         * Server:
         * поиск, сортировка и пагинация
         * выполняются на сервере.
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
         * Frontend / Auto→Frontend:
         * сервер возвращает полный набор,
         * локальный поиск/сортировка/пагинация
         * выполняются в Vue.
         */
        return $query
            ->ordered()
            ->get();
    }
}
