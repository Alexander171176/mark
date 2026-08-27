<?php

namespace App\Http\Controllers\Admin\School\SchoolBundlePrice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\SchoolBundlePrice\SchoolBundlePriceRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\School\SchoolBundle\SchoolBundleSharedResource;
use App\Http\Resources\Admin\School\SchoolBundlePrice\SchoolBundlePriceResource;
use App\Http\Resources\Admin\School\SchoolBundlePrice\SchoolBundlePriceSharedResource;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\School\SchoolBundle\SchoolBundle;
use App\Models\Admin\School\SchoolBundlePrice\SchoolBundlePrice;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления
 * ценами наборов курсов школы.
 *
 * @version 1.2
 * @author Александр Косолапов
 */
class SchoolBundlePriceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    /**
     * Список цен наборов курсов.
     */
    public function index(Request $request): Response
    {
        $currentLocale = app()->getLocale();

        $bundleId = $request->query('school_bundle_id');
        $currencyId = $request->query('currency_id');
        $activity = $request->query('activity');

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminSchoolBundlePricesPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminSchoolBundlePricesDefaultSort',
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
            'adminSchoolBundlePricesProcessingMode',
            'frontend'
        );

        /**
         * Лёгкий count без eager loading.
         */
        $pricesCount = $this->indexBaseQuery(
            bundleId: $bundleId,
            currencyId: $currencyId,
            activity: $activity,
        )->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $pricesCount,
            300
        );

        try {
            $prices = $this->getIndexBundlePrices(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
                bundleId: $bundleId,
                currencyId: $currencyId,
                activity: $activity,
            );

            return Inertia::render(
                'Admin/School/SchoolBundlePrices/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolBundlePricesPerPage' =>
                        $perPage,

                    'adminSchoolBundlePricesDefaultSort' =>
                        $defaultSort,

                    'adminSchoolBundlePricesProcessingMode' =>
                        $processingMode,

                    /**
                     * Index использует
                     * компактный SharedResource.
                     */
                    'prices' =>
                        SchoolBundlePriceSharedResource::collection(
                            $prices
                        ),

                    'pricesCount' =>
                        $pricesCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' => [
                        'school_bundle_id' =>
                            $bundleId
                                ? (int) $bundleId
                                : null,

                        'currency_id' =>
                            $currencyId
                                ? (int) $currencyId
                                : null,

                        'activity' =>
                            $activity,

                        'search' =>
                            $search,
                    ],

                    /**
                     * Bundle переводимая сущность.
                     */
                    'bundles' =>
                        $this->bundlesForSelect(
                            $currentLocale
                        ),

                    /**
                     * Currency не переводимая.
                     */
                    'currencies' =>
                        $this->currenciesForSelect(),
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки school bundle prices: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return Inertia::render(
                'Admin/School/SchoolBundlePrices/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolBundlePricesPerPage' =>
                        $perPage,

                    'adminSchoolBundlePricesDefaultSort' =>
                        $defaultSort,

                    'adminSchoolBundlePricesProcessingMode' =>
                        $processingMode,

                    'prices' =>
                        [],

                    'pricesCount' =>
                        0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' => [
                        'school_bundle_id' =>
                            $bundleId
                                ? (int) $bundleId
                                : null,

                        'currency_id' =>
                            $currencyId
                                ? (int) $currencyId
                                : null,

                        'activity' =>
                            $activity,

                        'search' =>
                            $search,
                    ],

                    'bundles' =>
                        [],

                    'currencies' =>
                        [],

                    'error' =>
                        'Ошибка загрузки цен наборов курсов.',
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE / STORE
    |--------------------------------------------------------------------------
    */

    /**
     * Страница создания цены набора.
     */
    public function create(Request $request): Response
    {
        $currentLocale = app()->getLocale();

        return Inertia::render(
            'Admin/School/SchoolBundlePrices/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                /**
                 * Bundle переводимый:
                 * только currentLocale.
                 */
                'bundles' =>
                    $this->bundlesForSelect(
                        $currentLocale
                    ),

                /**
                 * Currency не переводимая.
                 */
                'currencies' =>
                    $this->currenciesForSelect(),

                'defaultBundleId' =>
                    $request->query('school_bundle_id')
                        ? (int) $request->query(
                        'school_bundle_id'
                    )
                        : null,

                'defaultCurrencyId' =>
                    $request->query('currency_id')
                        ? (int) $request->query(
                        'currency_id'
                    )
                        : null,
            ]
        );
    }

    /**
     * Сохранение новой цены набора.
     */
    public function store(
        SchoolBundlePriceRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        try {
            DB::transaction(
                function () use ($data) {
                    /**
                     * Автоматический sort
                     * внутри конкретного Bundle.
                     */
                    if (
                        !isset($data['sort'])
                        || $data['sort'] === null
                    ) {
                        $maxSort = SchoolBundlePrice::query()
                            ->where(
                                'school_bundle_id',
                                $data['school_bundle_id']
                            )
                            ->max('sort');

                        $data['sort'] = $maxSort === null
                            ? 0
                            : (int) $maxSort + 1;
                    }

                    SchoolBundlePrice::create(
                        $data
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.schoolBundlePrices.index'
                )
                ->with(
                    'success',
                    'Цена набора курсов успешно создана.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка создания school bundle price: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,

                    'payload' =>
                        $data,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при создании цены набора курсов.'
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW / EDIT / UPDATE
    |--------------------------------------------------------------------------
    */

    /**
     * Редирект на страницу редактирования.
     */
    public function show(
        int $schoolBundlePrice
    ): RedirectResponse {
        return redirect()->route(
            'admin.schoolBundlePrices.edit',
            $schoolBundlePrice
        );
    }

    /**
     * Страница редактирования цены набора.
     */
    public function edit(
        int $schoolBundlePrice
    ): Response {
        $currentLocale = app()->getLocale();

        $price = SchoolBundlePrice::query()
            ->with([
                /**
                 * Bundle нужен только
                 * в текущей locale.
                 *
                 * Никаких:
                 *
                 * bundle.translation
                 * bundle.translations(all)
                 */
                'bundle' =>
                    fn ($query) =>
                    $query->with([
                        'translations' =>
                            fn ($translationQuery) =>
                            $translationQuery->where(
                                'locale',
                                $currentLocale
                            ),
                    ]),

                /**
                 * Currency не переводимая.
                 */
                'currency:id,code,name,symbol',
            ])
            ->findOrFail(
                $schoolBundlePrice
            );

        return Inertia::render(
            'Admin/School/SchoolBundlePrices/Edit',
            [
                'price' =>
                    new SchoolBundlePriceResource(
                        $price
                    ),

                'currentLocale' =>
                    $currentLocale,

                'bundles' =>
                    $this->bundlesForSelect(
                        $currentLocale
                    ),

                'currencies' =>
                    $this->currenciesForSelect(),
            ]
        );
    }

    /**
     * Обновление цены набора.
     */
    public function update(
        SchoolBundlePriceRequest $request,
        int $schoolBundlePrice
    ): RedirectResponse {
        $price = SchoolBundlePrice::query()
            ->findOrFail(
                $schoolBundlePrice
            );

        $data = $request->validated();

        try {
            DB::transaction(
                function () use (
                    $price,
                    $data
                ) {
                    $price->update(
                        $data
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.schoolBundlePrices.index'
                )
                ->with(
                    'success',
                    'Цена набора курсов успешно обновлена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления school bundle price ID '
                . $price->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,

                    'payload' =>
                        $data,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при обновлении цены набора курсов.'
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    /**
     * Удаление цены набора.
     */
    public function destroy(
        int $schoolBundlePrice
    ): RedirectResponse {
        $price = SchoolBundlePrice::query()
            ->findOrFail(
                $schoolBundlePrice
            );

        $bundleId =
            $price->school_bundle_id;

        try {
            $price->delete();

            return redirect()
                ->route(
                    'admin.schoolBundlePrices.index',
                    [
                        'school_bundle_id' =>
                            $bundleId,
                    ]
                )
                ->with(
                    'success',
                    'Цена набора курсов успешно удалена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка удаления school bundle price ID '
                . $price->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении цены набора курсов.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVITY
    |--------------------------------------------------------------------------
    */

    /**
     * Обновление активности одной цены.
     */
    public function updateActivity(
        UpdateActivityRequest $request,
        int $schoolBundlePrice
    ): RedirectResponse {
        $price = SchoolBundlePrice::query()
            ->findOrFail(
                $schoolBundlePrice
            );

        try {
            $price->update([
                'activity' =>
                    $request->validated(
                        'activity'
                    ),
            ]);

            return back()->with(
                'success',
                'Активность цены набора курсов обновлена.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления активности school bundle price ID '
                . $price->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при обновлении активности цены набора курсов.'
            );
        }
    }

    /**
     * Массовое обновление активности.
     */
    public function bulkUpdateActivity(
        Request $request
    ): RedirectResponse|JsonResponse {
        $data = $request->validate([
            'ids' => [
                'required',
                'array',
            ],

            'ids.*' => [
                'required',
                'integer',
                'exists:school_bundle_prices,id',
            ],

            'activity' => [
                'required',
                'boolean',
            ],
        ]);

        try {
            SchoolBundlePrice::query()
                ->whereIn(
                    'id',
                    $data['ids']
                )
                ->update([
                    'activity' =>
                        $data['activity'],
                ]);

            $message =
                'Активность выбранных цен наборов курсов успешно обновлена.';

            return $request->expectsJson()
                ? response()->json([
                    'message' =>
                        $message,
                ])
                : back()->with(
                    'success',
                    $message
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массового обновления активности school bundle prices: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            $message =
                'Ошибка при массовом обновлении активности цен наборов курсов.';

            return $request->expectsJson()
                ? response()->json(
                    [
                        'message' =>
                            $message,
                    ],
                    500
                )
                : back()->with(
                    'error',
                    $message
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SORT
    |--------------------------------------------------------------------------
    */

    /**
     * Обновление сортировки одной цены.
     */
    public function updateSort(
        UpdateSortEntityRequest $request,
        int $schoolBundlePrice
    ): RedirectResponse {
        $price = SchoolBundlePrice::query()
            ->findOrFail(
                $schoolBundlePrice
            );

        try {
            $price->update([
                'sort' =>
                    $request->validated(
                        'sort'
                    ),
            ]);

            return back()->with(
                'success',
                'Сортировка цены набора курсов обновлена.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления сортировки school bundle price ID '
                . $price->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при обновлении сортировки цены набора курсов.'
            );
        }
    }

    /**
     * Массовое обновление сортировки.
     */
    public function updateSortBulk(
        Request $request
    ): RedirectResponse|JsonResponse {
        $data = $request->validate([
            'items' => [
                'required',
                'array',
            ],

            'items.*.id' => [
                'required',
                'integer',
                'exists:school_bundle_prices,id',
            ],

            'items.*.sort' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        try {
            DB::transaction(
                function () use ($data) {
                    foreach (
                        $data['items']
                        as $item
                    ) {
                        SchoolBundlePrice::query()
                            ->whereKey(
                                $item['id']
                            )
                            ->update([
                                'sort' =>
                                    (int) $item['sort'],
                            ]);
                    }
                }
            );

            $message =
                'Сортировка цен наборов курсов успешно обновлена.';

            return $request->expectsJson()
                ? response()->json([
                    'message' =>
                        $message,
                ])
                : back()->with(
                    'success',
                    $message
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массовой сортировки school bundle prices: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            $message =
                'Ошибка при массовом обновлении сортировки цен наборов курсов.';

            return $request->expectsJson()
                ? response()->json(
                    [
                        'message' =>
                            $message,
                    ],
                    500
                )
                : back()->with(
                    'error',
                    $message
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | BULK DELETE
    |--------------------------------------------------------------------------
    */

    /**
     * Массовое удаление цен.
     */
    public function bulkDestroy(
        Request $request
    ): RedirectResponse|JsonResponse {
        $data = $request->validate([
            'ids' => [
                'required',
                'array',
            ],

            'ids.*' => [
                'required',
                'integer',
                'exists:school_bundle_prices,id',
            ],
        ]);

        try {
            DB::transaction(
                function () use ($data) {
                    SchoolBundlePrice::query()
                        ->whereIn(
                            'id',
                            $data['ids']
                        )
                        ->delete();
                }
            );

            $message =
                'Выбранные цены наборов курсов успешно удалены.';

            return $request->expectsJson()
                ? response()->json([
                    'message' =>
                        $message,
                ])
                : back()->with(
                    'success',
                    $message
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массового удаления school bundle prices: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            $message =
                'Ошибка при массовом удалении цен наборов курсов.';

            return $request->expectsJson()
                ? response()->json(
                    [
                        'message' =>
                            $message,
                    ],
                    500
                )
                : back()->with(
                    'error',
                    $message
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Наборы курсов для select.
     *
     * Загружаем только
     * translations(currentLocale).
     *
     * Изображения здесь не нужны.
     */
    private function bundlesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $bundles = SchoolBundle::query()
            ->with([
                'translations' =>
                    fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),
            ])
            ->orderByDesc('id')
            ->get();

        return SchoolBundleSharedResource::collection(
            $bundles
        );
    }

    /**
     * Валюты для select.
     *
     * Currency не переводимая.
     */
    private function currenciesForSelect(): Collection
    {
        return Currency::query()
            ->select(
                'id',
                'code',
                'name',
                'symbol'
            )
            ->orderBy('code')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX QUERIES
    |--------------------------------------------------------------------------
    */

    /**
     * Лёгкий базовый Index Query.
     *
     * Без eager loading.
     * Используется также для count().
     */
    private function indexBaseQuery(
        null|string|int $bundleId = null,
        null|string|int $currencyId = null,
        null|string|bool $activity = null,
    ): Builder {
        return SchoolBundlePrice::query()
            ->when(
                $bundleId,
                fn (Builder $query) =>
                $query->where(
                    'school_bundle_id',
                    (int) $bundleId
                )
            )
            ->when(
                $currencyId,
                fn (Builder $query) =>
                $query->where(
                    'currency_id',
                    (int) $currencyId
                )
            )
            ->when(
                $activity !== null
                && $activity !== '',
                function (
                    Builder $query
                ) use ($activity) {
                    $query->where(
                        'activity',
                        filter_var(
                            $activity,
                            FILTER_VALIDATE_BOOL
                        )
                    );
                }
            );
    }

    /**
     * Полный Index Query.
     *
     * Bundle получает только
     * translations(currentLocale).
     */
    private function indexQuery(
        string $locale,
        null|string|int $bundleId = null,
        null|string|int $currencyId = null,
        null|string|bool $activity = null,
    ): Builder {
        return $this->indexBaseQuery(
            bundleId: $bundleId,
            currencyId: $currencyId,
            activity: $activity,
        )
            ->with([
                /**
                 * Переводимый Bundle.
                 */
                'bundle' =>
                    fn ($query) =>
                    $query->with([
                        'translations' =>
                            fn ($translationQuery) =>
                            $translationQuery->where(
                                'locale',
                                $locale
                            ),
                    ]),

                /**
                 * Непереводимая Currency.
                 */
                'currency:id,code,name,symbol',
            ]);
    }

    /**
     * Получение списка цен
     * по активному processing mode.
     */
    private function getIndexBundlePrices(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
        null|string|int $bundleId = null,
        null|string|int $currencyId = null,
        null|string|bool $activity = null,
    ) {
        $query = $this->indexQuery(
            locale: $locale,
            bundleId: $bundleId,
            currencyId: $currencyId,
            activity: $activity,
        );

        /**
         * SERVER:
         * поиск + сортировка
         * + SQL pagination.
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
         * FRONTEND / AUTO(frontend):
         * полный dataset.
         */
        return $query
            ->sortByParam(
                $sort,
                $locale
            )
            ->get();
    }
}
