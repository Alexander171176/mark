<?php

namespace App\Http\Controllers\Admin\System\Location;

use App\Http\Requests\Admin\System\Location\LocationRequest;
use App\Http\Resources\Admin\System\Location\LocationResource;
use App\Http\Resources\Admin\System\Location\LocationSharedResource;
use App\Models\Admin\System\Location\Location;
use App\Models\Admin\System\Location\LocationTranslation;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;
use Throwable;

/**
 * Контроллер для управления Локациями (System) в админке.
 *
 * Паттерн:
 * - мультиязычность;
 * - иерархия Location;
 * - CRUD;
 * - server/frontend/auto processing;
 * - table/cards view;
 * - activity;
 * - default location;
 * - sort;
 *
 * Location является независимой системной сущностью
 * и не управляет другими модулями приложения.
 */
class LocationController extends BaseLocationAdminController
{
    /**
     * Основная модель.
     */
    protected string $modelClass = Location::class;

    /**
     * Название сущности для уведомлений.
     */
    protected string $entityLabel = 'локаций';

    /**
     * Поля переводов.
     */
    protected array $translationFields = [
        'title',
        'title_in',
        'title_from',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /**
     * Список локаций.
     */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $settings = app(
            AdminSettingsService::class
        );

        $perPage = $settings->int('adminSystemLocationsPerPage', 6);

        $defaultSort = $settings->string('adminSystemLocationsDefaultSort', 'idDesc');

        $defaultView = $settings->string(
            'adminSystemLocationsDefaultView',
            'table'
        );

        $processingMode = $settings->string(
            'adminSystemLocationsProcessingMode',
            'frontend'
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

        $type = trim(
            (string) $request->query(
                'type',
                ''
            )
        );

        $activity = $request->query(
            'activity'
        );

        $locationsCount =
            $this->baseQuery()->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $locationsCount,
            300
        );

        try {
            $locations =
                $this->getIndexLocations(
                    locale: $currentLocale,
                    useServerProcessing:
                    $useServerProcessing,
                    perPage: $perPage,
                    sort: $sortParam,
                    search: $search,
                    type: $type,
                    activity: $activity,
                );

            return Inertia::render(
                'Admin/System/Locations/Index',
                [
                    'locations' =>
                        LocationSharedResource::collection(
                            $locations
                        ),

                    'locationsCount' =>
                        $locationsCount,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSystemLocationsPerPage' =>
                        $perPage,

                    'adminSystemLocationsDefaultSort' =>
                        $defaultSort,

                    'adminSystemLocationsDefaultView' =>
                        $defaultView,

                    'adminSystemLocationsProcessingMode' =>
                        $processingMode,

                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'locationTypes' =>
                        $this->locationTypes(),

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'typeFilter' =>
                        $type,

                    'activityFilter' =>
                        $activity,
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка locations: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/System/Locations/Index',
                [
                    'locations' => [],
                    'locationsCount' => 0,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSystemLocationsPerPage' =>
                        $perPage,

                    'adminSystemLocationsDefaultSort' =>
                        $defaultSort,

                    'adminSystemLocationsDefaultView' =>
                        $defaultView,

                    'adminSystemLocationsProcessingMode' =>
                        $processingMode,

                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'locationTypes' =>
                        $this->locationTypes(),

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'typeFilter' =>
                        $type,

                    'activityFilter' =>
                        $activity,

                    'error' =>
                        'Ошибка загрузки локаций.',
                ]
            );
        }
    }

    /**
     * Страница создания локации.
     */
    public function create(
        Request $request
    ): Response {
        $currentLocale =
            $this->resolveLocale(
                $request
            );

        /**
         * Для Create нужны все переводы родителей,
         * потому что TranslationTabs может менять
         * текущую локаль формы.
         */
        $parents = $this->baseQuery()
            ->with('translations')
            ->ordered()
            ->get();

        return Inertia::render(
            'Admin/System/Locations/Create',
            [
                'parents' =>
                    LocationResource::collection(
                        $parents
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'locationTypes' =>
                    $this->locationTypes(),
            ]
        );
    }

    /**
     * Создание локации.
     */
    public function store(
        LocationRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $translations =
            $data['translations'] ?? [];

        unset(
            $data['translations']
        );

        try {
            DB::transaction(
                function () use (
                    $data,
                    $translations
                ) {
                    /**
                     * Проверка иерархии.
                     */
                    $this->ensureValidParent(
                        $data['parent_id']
                        ?? null
                    );

                    /**
                     * Если Location становится default:
                     * - она автоматически активна;
                     * - предыдущая default Location
                     *   теряет этот статус.
                     */
                    $this->prepareDefaultLocation(
                        $data
                    );

                    $location =
                        $this->modelClass::create(
                            $data
                        );

                    $this->syncTranslations(
                        $location,
                        $translations
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.locations.index'
                )
                ->with(
                    'success',
                    'Локация успешно создана.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при создании location: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e instanceof InvalidArgumentException
                        ? $e->getMessage()
                        : 'Ошибка при создании локации.'
                );
        }
    }

    /**
     * Редирект на страницу редактирования.
     */
    public function show(
        int $location
    ): RedirectResponse {
        return redirect()->route(
            'admin.locations.edit',
            $location
        );
    }

    /**
     * Страница редактирования локации.
     */
    public function edit(
        int $location,
        Request $request
    ): Response {
        $currentLocale =
            $this->resolveLocale(
                $request
            );

        /**
         * Основная редактируемая Location.
         *
         * Для TranslationTabs нужны
         * все переводы.
         */
        $currentLocation =
            $this->baseQuery()
                ->with('translations')
                ->findOrFail(
                    $location
                );

        /**
         * Текущую Location исключаем
         * из возможных родителей.
         *
         * Полная защита от циклов
         * выполняется при update.
         */
        $parents =
            $this->baseQuery()
                ->where(
                    'id',
                    '!=',
                    $currentLocation->id
                )
                ->with('translations')
                ->ordered()
                ->get();

        return Inertia::render(
            'Admin/System/Locations/Edit',
            [
                'location' =>
                    new LocationResource(
                        $currentLocation
                    ),

                'parents' =>
                    LocationResource::collection(
                        $parents
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'locationTypes' =>
                    $this->locationTypes(),
            ]
        );
    }

    /**
     * Обновление локации.
     */
    public function update(
        LocationRequest $request,
        int $location
    ): RedirectResponse {
        $currentLocation =
            $this->baseQuery()
                ->findOrFail(
                    $location
                );

        $data = $request->validated();

        $translations =
            $data['translations'] ?? [];

        unset(
            $data['translations']
        );

        try {
            DB::transaction(
                function () use (
                    $currentLocation,
                    $data,
                    $translations
                ) {
                    /**
                     * Проверка родителя
                     * и защита от циклической иерархии.
                     */
                    $this->ensureValidParent(
                        $data['parent_id']
                        ?? null,
                        $currentLocation->id
                    );

                    /**
                     * Нельзя просто снять default
                     * с текущей Location.
                     */
                    $this->ensureDefaultCanBeRemoved(
                        $currentLocation,
                        $data
                    );

                    /**
                     * Если Location назначается default:
                     * - она автоматически активна;
                     * - default снимается с остальных.
                     */
                    $this->prepareDefaultLocation(
                        $data,
                        $currentLocation->id
                    );

                    $currentLocation->update(
                        $data
                    );

                    $this->syncTranslations(
                        $currentLocation,
                        $translations
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.locations.index'
                )
                ->with(
                    'success',
                    'Локация успешно обновлена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при обновлении location ID '
                . $currentLocation->id
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
                    $e instanceof InvalidArgumentException
                        ? $e->getMessage()
                        : 'Ошибка при обновлении локации.'
                );
        }
    }

    /**
     * Удаление локации.
     */
    public function destroy(
        int $location
    ): RedirectResponse {
        $currentLocation =
            $this->baseQuery()
                ->findOrFail(
                    $location
                );

        try {
            DB::transaction(
                function () use (
                    $currentLocation
                ) {
                    /**
                     * Default Location удалять нельзя.
                     */
                    $this->ensureDefaultCanBeDeleted(
                        $currentLocation
                    );

                    /**
                     * Родительскую Location
                     * с дочерними элементами
                     * удалять нельзя.
                     */
                    $this->ensureLocationHasNoChildren(
                        $currentLocation
                    );

                    /**
                     * translations имеют cascadeOnDelete,
                     * поэтому отдельно их удалять
                     * не требуется.
                     */
                    $currentLocation->delete();
                }
            );

            return redirect()
                ->route(
                    'admin.locations.index'
                )
                ->with(
                    'success',
                    'Локация успешно удалена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при удалении location ID '
                . $currentLocation->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                $e instanceof InvalidArgumentException
                    ? $e->getMessage()
                    : 'Ошибка при удалении локации.'
            );
        }
    }

    /**
     * Массовое удаление локаций.
     */
    public function bulkDestroy(
        Request $request
    ): RedirectResponse {
        $validated =
            $request->validate([
                'ids' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'ids.*' => [
                    'required',
                    'integer',
                    'distinct',
                    'exists:locations,id',
                ],
            ]);

        $ids = $validated['ids'];

        /**
         * Получаем доступные записи
         * через базовый запрос.
         */
        $allowedIds =
            $this->baseQuery()
                ->whereIn(
                    'id',
                    $ids
                )
                ->pluck('id')
                ->toArray();

        if (
            count($allowedIds)
            !== count($ids)
        ) {
            return back()->with(
                'error',
                'Часть локаций недоступна для удаления.'
            );
        }

        try {
            DB::transaction(
                function () use (
                    $allowedIds
                ) {
                    /**
                     * Default Location
                     * массово удалять нельзя.
                     */
                    if (
                        $this->baseQuery()
                            ->whereIn(
                                'id',
                                $allowedIds
                            )
                            ->where(
                                'is_default',
                                true
                            )
                            ->exists()
                    ) {
                        throw new InvalidArgumentException(
                            'Нельзя удалить локацию по умолчанию. Сначала назначьте другую локацию по умолчанию.'
                        );
                    }

                    /**
                     * Выбранную Location можно удалить
                     * вместе с её дочерней Location,
                     * если дочерний элемент тоже входит
                     * в текущий удаляемый набор.
                     *
                     * Если существует дочерняя Location
                     * вне выбранного списка — удаление
                     * запрещаем.
                     */
                    $hasExternalChildren =
                        $this->baseQuery()
                            ->whereIn(
                                'parent_id',
                                $allowedIds
                            )
                            ->whereNotIn(
                                'id',
                                $allowedIds
                            )
                            ->exists();

                    if ($hasExternalChildren) {
                        throw new InvalidArgumentException(
                            'Нельзя удалить выбранные локации, пока у них есть дочерние элементы вне выбранного списка.'
                        );
                    }

                    $this->modelClass::query()
                        ->whereIn(
                            'id',
                            $allowedIds
                        )
                        ->delete();
                }
            );

            return back()->with(
                'success',
                'Выбранные локации успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка bulkDestroy locations: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                $e instanceof InvalidArgumentException
                    ? $e->getMessage()
                    : 'Ошибка при массовом удалении локаций.'
            );
        }
    }

    /**
     * Базовый запрос списка Location.
     *
     * Загружаем текущую и fallback locale,
     * потому что LocationSharedResource
     * использует translationOrFallback().
     */
    private function indexLocationsQuery(
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

        return $this->baseQuery()
            ->with([
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                'parent.translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),
            ]);
    }

    /**
     * Список Location по режиму обработки.
     */
    private function getIndexLocations(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
        string $type = '',
        mixed $activity = null,
    ) {
        $query =
            $this->indexLocationsQuery(
                $locale
            );

        /**
         * Server:
         * поиск, фильтрация, сортировка
         * и пагинация выполняются в SQL.
         */
        if ($useServerProcessing) {
            $this->applySearch(
                $query,
                $search,
                $locale
            );

            $this->applyFilters(
                $query,
                $type,
                $activity
            );

            $this->applySort(
                $query,
                $sort,
                $locale
            );

            return $query
                ->paginate(
                    $perPage
                )
                ->withQueryString();
        }

        /**
         * Frontend:
         * отдаём всю коллекцию.
         *
         * Поиск, фильтрацию, сортировку
         * и пагинацию выполняет Vue.
         */
        return $query
            ->ordered()
            ->get();
    }

    /**
     * Server-поиск.
     */
    private function applySearch(
        Builder $query,
        string $search,
        string $locale
    ): void {
        if ($search === '') {
            return;
        }

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

        $query->where(
            function (
                Builder $builder
            ) use (
                $search,
                $locales
            ) {
                $builder
                    ->where(
                        'slug',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'code',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'type',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'timezone',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhereHas(
                        'translations',
                        function (
                            Builder $translationQuery
                        ) use (
                            $search,
                            $locales
                        ) {
                            $translationQuery
                                ->whereIn(
                                    'locale',
                                    $locales
                                )
                                ->where(
                                    function (
                                        Builder $textQuery
                                    ) use (
                                        $search
                                    ) {
                                        $textQuery
                                            ->where(
                                                'title',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhere(
                                                'title_in',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhere(
                                                'title_from',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhere(
                                                'short',
                                                'like',
                                                '%' . $search . '%'
                                            );
                                    }
                                );
                        }
                    )
                    ->orWhereHas(
                        'parent.translations',
                        function (
                            Builder $translationQuery
                        ) use (
                            $search,
                            $locales
                        ) {
                            $translationQuery
                                ->whereIn(
                                    'locale',
                                    $locales
                                )
                                ->where(
                                    'title',
                                    'like',
                                    '%' . $search . '%'
                                );
                        }
                    );
            }
        );
    }

    /**
     * Server-фильтрация.
     */
    private function applyFilters(
        Builder $query,
        string $type,
        mixed $activity
    ): void {
        if ($type !== '') {
            $query->where(
                'type',
                $type
            );
        }

        if (
            $activity !== null
            && $activity !== ''
        ) {
            $query->where(
                'activity',
                filter_var(
                    $activity,
                    FILTER_VALIDATE_BOOLEAN
                )
            );
        }
    }

    /**
     * Server-сортировка.
     */
    private function applySort(
        Builder $query,
        string $sort,
        string $locale
    ): void {
        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        switch ($sort) {
            case 'idAsc':
                $query->orderBy(
                    'locations.id'
                );
                break;

            case 'idDesc':
                $query->orderByDesc(
                    'locations.id'
                );
                break;

            case 'sortDesc':
                $query
                    ->orderByDesc(
                        'locations.sort'
                    )
                    ->orderByDesc(
                        'locations.id'
                    );
                break;

            case 'titleAsc':
                $this->orderByTitle(
                    $query,
                    $locale,
                    $fallbackLocale,
                    'asc'
                );
                break;

            case 'titleDesc':
                $this->orderByTitle(
                    $query,
                    $locale,
                    $fallbackLocale,
                    'desc'
                );
                break;

            case 'typeAsc':
                $query
                    ->orderBy(
                        'locations.type'
                    )
                    ->orderBy(
                        'locations.sort'
                    );
                break;

            case 'typeDesc':
                $query
                    ->orderByDesc(
                        'locations.type'
                    )
                    ->orderBy(
                        'locations.sort'
                    );
                break;

            case 'slugAsc':
                $query->orderBy(
                    'locations.slug'
                );
                break;

            case 'slugDesc':
                $query->orderByDesc(
                    'locations.slug'
                );
                break;

            case 'sortAsc':
            default:
                $query
                    ->orderBy(
                        'locations.sort'
                    )
                    ->orderBy(
                        'locations.id'
                    );
                break;
        }
    }

    /**
     * Сортировка по переведённому title
     * с учётом fallback locale.
     */
    private function orderByTitle(
        Builder $query,
        string $locale,
        string $fallbackLocale,
        string $direction
    ): void {
        $direction =
            strtolower($direction) === 'desc'
                ? 'desc'
                : 'asc';

        $currentTitle =
            LocationTranslation::query()
                ->select('title')
                ->whereColumn(
                    'location_translations.location_id',
                    'locations.id'
                )
                ->where(
                    'locale',
                    $locale
                )
                ->limit(1);

        $fallbackTitle =
            LocationTranslation::query()
                ->select('title')
                ->whereColumn(
                    'location_translations.location_id',
                    'locations.id'
                )
                ->where(
                    'locale',
                    $fallbackLocale
                )
                ->limit(1);

        $query
            ->orderByRaw(
                'COALESCE(('
                . $currentTitle->toSql()
                . '), ('
                . $fallbackTitle->toSql()
                . "), '') {$direction}",
                array_merge(
                    $currentTitle->getBindings(),
                    $fallbackTitle->getBindings()
                )
            )
            ->orderBy(
                'locations.id'
            );
    }

    /**
     * Типы Location.
     *
     * Это только варианты для UI,
     * а не ограничение БД.
     *
     * Новые существующие type автоматически
     * добавляются в список.
     */
    private function locationTypes(): array
    {
        return collect([
            'country',
            'region',
            'city',
            'district',
        ])
            ->merge(
                $this->baseQuery()
                    ->distinct()
                    ->orderBy('type')
                    ->pluck('type')
            )
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
