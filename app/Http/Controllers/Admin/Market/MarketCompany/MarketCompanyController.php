<?php

namespace App\Http\Controllers\Admin\Market\MarketCompany;

use App\Http\Controllers\Admin\Market\BaseMarketAdminController;
use App\Http\Requests\Admin\Market\MarketCompany\MarketCompanyRequest;
use App\Http\Resources\Admin\Market\MarketCompany\MarketCompanyResource;
use App\Http\Resources\Admin\Market\MarketCompany\MarketCompanySharedResource;
use App\Models\Admin\Market\MarketCompany\MarketCompany;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Фирмами (MarketCompany) в админке.
 *
 * Паттерн:
 * - Поиск, Пагинация, сортировка (режимы: frontend | auto | server )
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - activity/left/main/right (single + bulk)
 * - activity (single + bulk)
 * - sort + drag&drop (bulk)
 * - moderation (approve/reject) только для admin
 * - images single
 *
 * @version 1.1 (мультиязычеая архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */
class MarketCompanyController extends BaseMarketAdminController
{
    /** Основная модель контроллера */
    protected string $modelClass = MarketCompany::class;

    /** Название сущности для сообщений */
    protected string $entityLabel = 'компаний';

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

    /**
     * Список компаний.
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
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminMarketCompaniesPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminMarketCompaniesDefaultSort',
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
            'adminMarketCompaniesProcessingMode',
            'frontend'
        );

        /**
         * Первый COUNT нужен ProcessingModeService
         * для выбора frontend/server режима.
         */
        $companiesCount = $this->baseQuery()
            ->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $companiesCount,
            300
        );

        try {
            $companies = $this->getIndexCompanies(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search
            );

            return Inertia::render(
                'Admin/Market/MarketCompanies/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminMarketCompaniesPerPage' =>
                        $perPage,

                    'adminMarketCompaniesDefaultSort' =>
                        $defaultSort,

                    'adminMarketCompaniesProcessingMode' =>
                        $processingMode,

                    /**
                     * Index использует компактный
                     * SharedResource.
                     */
                    'companies' =>
                        MarketCompanySharedResource::collection(
                            $companies
                        ),

                    'companiesCount' =>
                        $companiesCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка market companies: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/Market/MarketCompanies/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminMarketCompaniesPerPage' =>
                        $perPage,

                    'adminMarketCompaniesDefaultSort' =>
                        $defaultSort,

                    'adminMarketCompaniesProcessingMode' =>
                        $processingMode,

                    'companies' => [],

                    'companiesCount' => 0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'error' =>
                        'Ошибка загрузки компаний.',
                ]
            );
        }
    }

    /** Страница создания компании */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale(
            $request
        );

        return Inertia::render(
            'Admin/Market/MarketCompanies/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),
            ]
        );
    }

    /** Создание компании */
    public function store(
        MarketCompanyRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset(
            $data['translations'],
            $data['logo'],
            $data['signature'],
            $data['stamp']
        );

        $user = auth()->user();

        /**
         * Обычный пользователь может создавать
         * компанию только от своего имени
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
            $data['user_id'] = $user->id;

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
                    &$company,
                    $data,
                    $translations
                ): void {
                    if (
                        ! isset($data['sort'])
                        || $data['sort'] === null
                    ) {
                        $maxSort = MarketCompany::query()
                            ->max('sort');

                        $data['sort'] =
                            $maxSort === null
                                ? 0
                                : $maxSort + 1;
                    }

                    $company = MarketCompany::create(
                        $data
                    );

                    $this->syncTranslations(
                        $company,
                        $translations
                    );
                }
            );

            /**
             * Файлы сохраняются после создания,
             * чтобы компания уже имела ID.
             */
            if ($request->hasFile('logo')) {
                $company->update([
                    'logo' =>
                        $request
                            ->file('logo')
                            ->store(
                                'market/companies/logos',
                                'public'
                            ),
                ]);
            }

            if ($request->hasFile('signature')) {
                $company->update([
                    'signature' =>
                        $request
                            ->file('signature')
                            ->store(
                                'market/companies/signatures',
                                'public'
                            ),
                ]);
            }

            if ($request->hasFile('stamp')) {
                $company->update([
                    'stamp' =>
                        $request
                            ->file('stamp')
                            ->store(
                                'market/companies/stamps',
                                'public'
                            ),
                ]);
            }

            return redirect()
                ->route(
                    'admin.marketCompanies.index'
                )
                ->with(
                    'success',
                    'Компания успешно создана.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при создании market company: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при создании компании.'
                );
        }
    }

    /** Перенаправление просмотра на редактирование */
    public function show(
        string $id
    ): RedirectResponse {
        return redirect()->route(
            'admin.marketCompanies.edit',
            $id
        );
    }

    /**
     * Страница редактирования компании.
     *
     * Для Edit намеренно загружаются
     * ВСЕ переводы компании, поскольку
     * TranslationTabs редактирует все локали.
     */
    public function edit(
        int $marketCompany,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $company = $this->baseQuery()
            ->with([
                'owner:id,name,email,profile_photo_path',
                'moderator:id,name',

                /**
                 * Здесь ограничения locale нет
                 * намеренно.
                 */
                'translations',
            ])
            ->findOrFail(
                $marketCompany
            );

        return Inertia::render(
            'Admin/Market/MarketCompanies/Edit',
            [
                'company' =>
                    new MarketCompanyResource(
                        $company
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),
            ]
        );
    }

    /** Обновление компании */
    public function update(
        MarketCompanyRequest $request,
        int $marketCompany
    ): RedirectResponse {
        $company = $this->baseQuery()
            ->findOrFail(
                $marketCompany
            );

        $data = $request->validated();

        $translations =
            $data['translations'] ?? [];

        unset(
            $data['translations'],
            $data['_method'],
            $data['logo'],
            $data['signature'],
            $data['stamp']
        );

        $user = auth()->user();

        /**
         * Обычный пользователь сохраняется
         * владельцем своей компании
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
                    $company,
                    $data,
                    $translations
                ): void {
                    $company->update(
                        $data
                    );

                    $this->syncTranslations(
                        $company,
                        $translations
                    );
                }
            );

            if ($request->hasFile('logo')) {
                $company->update([
                    'logo' =>
                        $request
                            ->file('logo')
                            ->store(
                                'market/companies/logos',
                                'public'
                            ),
                ]);
            }

            if ($request->hasFile('signature')) {
                $company->update([
                    'signature' =>
                        $request
                            ->file('signature')
                            ->store(
                                'market/companies/signatures',
                                'public'
                            ),
                ]);
            }

            if ($request->hasFile('stamp')) {
                $company->update([
                    'stamp' =>
                        $request
                            ->file('stamp')
                            ->store(
                                'market/companies/stamps',
                                'public'
                            ),
                ]);
            }

            return redirect()
                ->route(
                    'admin.marketCompanies.index'
                )
                ->with(
                    'success',
                    'Компания успешно обновлена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при обновлении market company ID '
                . $company->id
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
                    'Ошибка при обновлении компании.'
                );
        }
    }

    /**
     * Удаление компании.
     *
     * Предварительно загружать translations
     * не требуется: используется relation DELETE.
     */
    public function destroy(
        int $marketCompany
    ): RedirectResponse {
        $company = $this->baseQuery()
            ->findOrFail(
                $marketCompany
            );

        try {
            DB::transaction(
                function () use (
                    $company
                ): void {
                    $company
                        ->translations()
                        ->delete();

                    $company->delete();
                }
            );

            return redirect()
                ->route(
                    'admin.marketCompanies.index'
                )
                ->with(
                    'success',
                    'Компания успешно удалена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при удалении market company ID '
                . $company->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении компании.'
            );
        }
    }

    /**
     * Массовое удаление компаний.
     *
     * Доступность компаний проверяется
     * через baseQuery().
     */
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
                'exists:market_companies,id',
            ],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
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
                'Часть компаний недоступна для удаления.'
            );
        }

        try {
            DB::transaction(
                function () use (
                    $allowedIds
                ): void {
                    /**
                     * Загружать translations не требуется.
                     * Для каждой компании relation выполняет
                     * прямой DELETE.
                     */
                    $companies = MarketCompany::query()
                        ->whereIn(
                            'id',
                            $allowedIds
                        )
                        ->get();

                    foreach (
                        $companies as $company
                    ) {
                        $company
                            ->translations()
                            ->delete();

                        $company->delete();
                    }
                }
            );

            return back()->with(
                'success',
                'Выбранные компании успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка bulkDestroy market companies: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом удалении компаний.'
            );
        }
    }

    /**
     * Базовый запрос Index.
     *
     * В Index:
     * - owner загружается пакетно;
     * - moderator загружается пакетно;
     * - translations ограничены currentLocale.
     *
     * Никакие fallback-запросы Resource
     * выполнять не должен.
     */
    private function indexQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                'owner:id,name,email,profile_photo_path',

                'moderator:id,name',

                'translations' =>
                    function (
                        $query
                    ) use (
                        $locale
                    ): void {
                        $query->where(
                            'locale',
                            $locale
                        );
                    },
            ]);
    }

    /**
     * Получение списка компаний
     * для Index.
     *
     * server:
     * - поиск в БД;
     * - сортировка в БД;
     * - paginator.
     *
     * frontend:
     * - полный набор;
     * - локальный поиск/сортировка/пагинация.
     */
    private function getIndexCompanies(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ): LengthAwarePaginator|Collection {
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
}
