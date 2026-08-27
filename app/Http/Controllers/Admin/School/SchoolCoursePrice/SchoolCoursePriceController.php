<?php

namespace App\Http\Controllers\Admin\School\SchoolCoursePrice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\SchoolCoursePrice\SchoolCoursePriceRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolCoursePrice\SchoolCoursePriceResource;
use App\Http\Resources\Admin\School\SchoolCoursePrice\SchoolCoursePriceSharedResource;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolCoursePrice\SchoolCoursePrice;
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
 * ценами курсов школы.
 *
 * @version 1.2
 * @author Александр Косолапов
 */
class SchoolCoursePriceController extends Controller
{
    /**
     * =========================================================
     * INDEX
     * =========================================================
     */

    /**
     * Список цен курсов.
     */
    public function index(Request $request): Response
    {
        $currentLocale = app()->getLocale();

        $courseId = $request->query('school_course_id');
        $currencyId = $request->query('currency_id');
        $activity = $request->query('activity');

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminSchoolCoursePricesPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminSchoolCoursePricesDefaultSort',
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
            'adminSchoolCoursePricesProcessingMode',
            'frontend'
        );

        /**
         * Лёгкий count без eager loading.
         */
        $pricesCount = $this->indexBaseQuery(
            courseId: $courseId,
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
            $prices = $this->getIndexCoursePrices(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
                courseId: $courseId,
                currencyId: $currencyId,
                activity: $activity,
            );

            return Inertia::render(
                'Admin/School/SchoolCoursePrices/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolCoursePricesPerPage' =>
                        $perPage,

                    'adminSchoolCoursePricesDefaultSort' =>
                        $defaultSort,

                    'adminSchoolCoursePricesProcessingMode' =>
                        $processingMode,

                    /**
                     * Index использует
                     * компактный SharedResource.
                     */
                    'prices' =>
                        SchoolCoursePriceSharedResource::collection(
                            $prices
                        ),

                    'pricesCount' =>
                        $pricesCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' => [
                        'school_course_id' =>
                            $courseId
                                ? (int) $courseId
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
                     * Course переводимый:
                     * только currentLocale.
                     */
                    'courses' =>
                        $this->coursesForSelect(
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
                'Ошибка загрузки school course prices: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return Inertia::render(
                'Admin/School/SchoolCoursePrices/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolCoursePricesPerPage' =>
                        $perPage,

                    'adminSchoolCoursePricesDefaultSort' =>
                        $defaultSort,

                    'adminSchoolCoursePricesProcessingMode' =>
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
                        'school_course_id' =>
                            $courseId
                                ? (int) $courseId
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

                    'courses' =>
                        [],

                    'currencies' =>
                        [],

                    'error' =>
                        'Ошибка загрузки цен курсов.',
                ]
            );
        }
    }

    /**
     * =========================================================
     * CREATE / STORE
     * =========================================================
     */

    /**
     * Страница создания цены курса.
     */
    public function create(Request $request): Response
    {
        $currentLocale = app()->getLocale();

        return Inertia::render(
            'Admin/School/SchoolCoursePrices/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                /**
                 * Course переводимый:
                 * только currentLocale.
                 */
                'courses' =>
                    $this->coursesForSelect(
                        $currentLocale
                    ),

                /**
                 * Currency не переводимая.
                 */
                'currencies' =>
                    $this->currenciesForSelect(),

                'defaultCourseId' =>
                    $request->query('school_course_id')
                        ? (int) $request->query(
                        'school_course_id'
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
     * Сохранение новой цены курса.
     */
    public function store(
        SchoolCoursePriceRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $price = null;

        try {
            DB::transaction(
                function () use (
                    &$price,
                    $data
                ) {
                    /**
                     * Автоматический sort
                     * внутри конкретного курса.
                     */
                    if (
                        !isset($data['sort'])
                        || $data['sort'] === null
                    ) {
                        $maxSort = SchoolCoursePrice::query()
                            ->where(
                                'school_course_id',
                                $data['school_course_id']
                            )
                            ->max('sort');

                        $data['sort'] = $maxSort === null
                            ? 0
                            : (int) $maxSort + 1;
                    }

                    $price = SchoolCoursePrice::create(
                        $data
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.schoolCoursePrices.index'
                )
                ->with(
                    'success',
                    'Цена курса успешно создана.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка создания school course price: '
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
                    'Ошибка при создании цены курса.'
                );
        }
    }

    /**
     * =========================================================
     * SHOW / EDIT / UPDATE
     * =========================================================
     */

    /**
     * Редирект на страницу редактирования.
     */
    public function show(
        int $schoolCoursePrice
    ): RedirectResponse {
        return redirect()->route(
            'admin.schoolCoursePrices.edit',
            $schoolCoursePrice
        );
    }

    /**
     * Страница редактирования цены курса.
     */
    public function edit(
        int $schoolCoursePrice
    ): Response {
        $currentLocale = app()->getLocale();

        $price = SchoolCoursePrice::query()
            ->with([
                /**
                 * Course нужен только
                 * в текущей locale.
                 *
                 * Никаких:
                 * course.translation
                 * course.translations(all)
                 */
                'course' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
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
                $schoolCoursePrice
            );

        return Inertia::render(
            'Admin/School/SchoolCoursePrices/Edit',
            [
                'price' =>
                    new SchoolCoursePriceResource(
                        $price
                    ),

                'currentLocale' =>
                    $currentLocale,

                'courses' =>
                    $this->coursesForSelect(
                        $currentLocale
                    ),

                'currencies' =>
                    $this->currenciesForSelect(),
            ]
        );
    }

    /**
     * Обновление цены курса.
     */
    public function update(
        SchoolCoursePriceRequest $request,
        int $schoolCoursePrice
    ): RedirectResponse {
        $price = SchoolCoursePrice::query()
            ->findOrFail(
                $schoolCoursePrice
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
                    'admin.schoolCoursePrices.index'
                )
                ->with(
                    'success',
                    'Цена курса успешно обновлена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления school course price ID '
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
                    'Ошибка при обновлении цены курса.'
                );
        }
    }

    /**
     * =========================================================
     * DELETE
     * =========================================================
     */

    /**
     * Удаление цены курса.
     */
    public function destroy(
        int $schoolCoursePrice
    ): RedirectResponse {
        $price = SchoolCoursePrice::query()
            ->findOrFail(
                $schoolCoursePrice
            );

        $courseId =
            $price->school_course_id;

        try {
            $price->delete();

            return redirect()
                ->route(
                    'admin.schoolCoursePrices.index',
                    [
                        'school_course_id' =>
                            $courseId,
                    ]
                )
                ->with(
                    'success',
                    'Цена курса успешно удалена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка удаления school course price ID '
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
                'Ошибка при удалении цены курса.'
            );
        }
    }

    /**
     * =========================================================
     * ACTIVITY
     * =========================================================
     */

    /**
     * Обновление активности одной цены.
     */
    public function updateActivity(
        UpdateActivityRequest $request,
        int $schoolCoursePrice
    ): RedirectResponse {
        $price = SchoolCoursePrice::query()
            ->findOrFail(
                $schoolCoursePrice
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
                'Активность цены курса обновлена.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления активности school course price ID '
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
                'Ошибка при обновлении активности цены курса.'
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
                'exists:school_course_prices,id',
            ],

            'activity' => [
                'required',
                'boolean',
            ],
        ]);

        try {
            SchoolCoursePrice::query()
                ->whereIn(
                    'id',
                    $data['ids']
                )
                ->update([
                    'activity' =>
                        $data['activity'],
                ]);

            $message =
                'Активность выбранных цен курсов успешно обновлена.';

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
                'Ошибка массового обновления активности school course prices: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            $message =
                'Ошибка при массовом обновлении активности цен курсов.';

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

    /**
     * =========================================================
     * SORT
     * =========================================================
     */

    /**
     * Обновление сортировки одной цены.
     */
    public function updateSort(
        UpdateSortEntityRequest $request,
        int $schoolCoursePrice
    ): RedirectResponse {
        $price = SchoolCoursePrice::query()
            ->findOrFail(
                $schoolCoursePrice
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
                'Сортировка цены курса обновлена.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления сортировки school course price ID '
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
                'Ошибка при обновлении сортировки цены курса.'
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
                'exists:school_course_prices,id',
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
                        SchoolCoursePrice::query()
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
                'Сортировка цен курсов успешно обновлена.';

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
                'Ошибка массовой сортировки school course prices: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            $message =
                'Ошибка при массовом обновлении сортировки цен курсов.';

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

    /**
     * =========================================================
     * BULK DELETE
     * =========================================================
     */

    /**
     * Массовое удаление цен курсов.
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
                'exists:school_course_prices,id',
            ],
        ]);

        try {
            DB::transaction(
                function () use ($data) {
                    SchoolCoursePrice::query()
                        ->whereIn(
                            'id',
                            $data['ids']
                        )
                        ->delete();
                }
            );

            $message =
                'Выбранные цены курсов успешно удалены.';

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
                'Ошибка массового удаления school course prices: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            $message =
                'Ошибка при массовом удалении цен курсов.';

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

    /**
     * =========================================================
     * SELECT HELPERS
     * =========================================================
     */

    /**
     * Курсы для select.
     *
     * Controller загружает только
     * translations(currentLocale).
     */
    private function coursesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $courses = SchoolCourse::query()
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

        return SchoolCourseSharedResource::collection(
            $courses
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

    /**
     * =========================================================
     * INDEX QUERIES
     * =========================================================
     */

    /**
     * Базовый Index Query.
     *
     * Без eager loading.
     * Используется также для count().
     */
    private function indexBaseQuery(
        null|string|int $courseId = null,
        null|string|int $currencyId = null,
        null|string|bool $activity = null,
    ): Builder {
        return SchoolCoursePrice::query()
            ->when(
                $courseId,
                fn (Builder $query) =>
                $query->where(
                    'school_course_id',
                    (int) $courseId
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
     * Course получает только
     * translations(currentLocale).
     */
    private function indexQuery(
        string $locale,
        null|string|int $courseId = null,
        null|string|int $currencyId = null,
        null|string|bool $activity = null,
    ): Builder {
        return $this->indexBaseQuery(
            courseId: $courseId,
            currencyId: $currencyId,
            activity: $activity,
        )
            ->with([
                /**
                 * Переводимый Course.
                 */
                'course' =>
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
    private function getIndexCoursePrices(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
        null|string|int $courseId = null,
        null|string|int $currencyId = null,
        null|string|bool $activity = null,
    ) {
        $query = $this->indexQuery(
            locale: $locale,
            courseId: $courseId,
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
         * FRONTEND:
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
