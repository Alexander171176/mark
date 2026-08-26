<?php

namespace App\Http\Controllers\Admin\School\SchoolOrder;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleSharedResource;
use App\Http\Resources\Admin\School\SchoolOrder\SchoolOrderResource;
use App\Http\Resources\Admin\School\SchoolOrder\SchoolOrderSharedResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseSchedule;
use App\Models\Admin\School\SchoolOrder\SchoolOrder;
use App\Models\User;
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
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления заказами
 * школы в административной панели.
 *
 * CRUD +:
 * - frontend/server/auto processing;
 * - фильтры;
 * - поиск;
 * - сортировка;
 * - клонирование;
 * - связи User / Course / Schedule;
 * - locale-aware связанные сущности.
 *
 * @version 1.2
 */
class SchoolOrderController extends BaseSchoolAdminController
{
    /**
     * =========================================================
     * INDEX
     * =========================================================
     */

    /** Список заказов. */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $status = $request->query(
            'status'
        );

        $paymentStatus = $request->query(
            'payment_status'
        );

        $isPaidParam = $request->query(
            'is_paid'
        );

        $settings = app(
            AdminSettingsService::class
        );

        $perPage = $settings->int(
            'adminSchoolOrdersPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminSchoolOrdersDefaultSort',
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
            'adminSchoolOrdersProcessingMode',
            'frontend'
        );

        /**
         * Count выполняем отдельным
         * лёгким запросом.
         */
        $ordersCount = $this->countQuery(
            status: $status,
            paymentStatus: $paymentStatus,
            isPaidParam: $isPaidParam,
        )->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $ordersCount,
            300
        );

        try {
            $orders = $this->getIndexOrders(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
                status: $status,
                paymentStatus: $paymentStatus,
                isPaidParam: $isPaidParam,
            );

            return Inertia::render(
                'Admin/School/SchoolOrders/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolOrdersPerPage' =>
                        $perPage,

                    'adminSchoolOrdersDefaultSort' =>
                        $defaultSort,

                    'adminSchoolOrdersProcessingMode' =>
                        $processingMode,

                    /**
                     * Index использует
                     * компактный SharedResource.
                     */
                    'orders' =>
                        SchoolOrderSharedResource::collection(
                            $orders
                        ),

                    'ordersCount' =>
                        $ordersCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' => [
                        'status' =>
                            $status,

                        'payment_status' =>
                            $paymentStatus,

                        'is_paid' =>
                            $isPaidParam,

                        'search' =>
                            $search,
                    ],
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки school orders: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/School/SchoolOrders/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolOrdersPerPage' =>
                        $perPage,

                    'adminSchoolOrdersDefaultSort' =>
                        $defaultSort,

                    'adminSchoolOrdersProcessingMode' =>
                        $processingMode,

                    'orders' =>
                        [],

                    'ordersCount' =>
                        0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' => [
                        'status' =>
                            $status,

                        'payment_status' =>
                            $paymentStatus,

                        'is_paid' =>
                            $isPaidParam,

                        'search' =>
                            $search,
                    ],

                    'error' =>
                        'Ошибка загрузки заказов.',
                ]
            );
        }
    }

    /**
     * =========================================================
     * CREATE / STORE
     * =========================================================
     */

    /** Страница создания заказа. */
    public function create(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        return Inertia::render(
            'Admin/School/SchoolOrders/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                /**
                 * User не переводимый.
                 */
                'users' =>
                    $this->usersForSelect(),

                /**
                 * Переводимые сущности:
                 * только currentLocale.
                 */
                'courses' =>
                    $this->coursesForSelect(
                        $currentLocale
                    ),

                'schedules' =>
                    $this->schedulesForSelect(
                        $currentLocale
                    ),
            ]
        );
    }

    /** Создание заказа. */
    public function store(
        Request $request
    ): RedirectResponse {
        $data = $this->validateOrder(
            $request
        );

        if (empty($data['number'])) {
            $data['number'] =
                $this->generateOrderNumber();
        }

        if (
            !empty($data['is_paid'])
            && empty($data['paid_at'])
        ) {
            $data['paid_at'] = now();
        }

        try {
            SchoolOrder::create(
                $data
            );

            return redirect()
                ->route(
                    'admin.schoolOrders.index'
                )
                ->with(
                    'success',
                    'Заказ успешно создан.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка создания school order: '
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
                    'Ошибка при создании заказа.'
                );
        }
    }

    /**
     * =========================================================
     * SHOW / EDIT / UPDATE
     * =========================================================
     */

    /** Редирект на редактирование. */
    public function show(
        int $schoolOrder
    ): RedirectResponse {
        return redirect()->route(
            'admin.schoolOrders.edit',
            $schoolOrder
        );
    }

    /** Страница редактирования заказа. */
    public function edit(
        int $schoolOrder,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $order = SchoolOrder::query()
            ->with([
                /**
                 * User не переводимый.
                 */
                'user:id,name,email',

                /**
                 * Course:
                 * только currentLocale.
                 */
                'course' => fn ($query) =>
                $query->with([
                    'translations' =>
                        fn ($translationQuery) =>
                        $translationQuery->where(
                            'locale',
                            $currentLocale
                        ),
                ]),

                /**
                 * Schedule:
                 * только currentLocale.
                 *
                 * Внутренний Course тоже
                 * только currentLocale.
                 */
                'schedule' => fn ($query) =>
                $query->with([
                    'translations' =>
                        fn ($translationQuery) =>
                        $translationQuery->where(
                            'locale',
                            $currentLocale
                        ),

                    'course' => fn ($courseQuery) =>
                    $courseQuery->with([
                        'translations' =>
                            fn ($translationQuery) =>
                            $translationQuery->where(
                                'locale',
                                $currentLocale
                            ),
                    ]),
                ]),

                /**
                 * Полные связанные данные
                 * нужны Edit.
                 */
                'orderItems',
                'payments',
                'refunds',
                'invoices',
                'enrollments',
                'subscriptions',
            ])
            ->withCount([
                'orderItems',
                'payments',
                'refunds',
                'invoices',
                'enrollments',
                'subscriptions',
            ])
            ->findOrFail(
                $schoolOrder
            );

        return Inertia::render(
            'Admin/School/SchoolOrders/Edit',
            [
                'order' =>
                    new SchoolOrderResource(
                        $order
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'users' =>
                    $this->usersForSelect(),

                'courses' =>
                    $this->coursesForSelect(
                        $currentLocale
                    ),

                'schedules' =>
                    $this->schedulesForSelect(
                        $currentLocale
                    ),
            ]
        );
    }

    /** Обновление заказа. */
    public function update(
        Request $request,
        int $schoolOrder
    ): RedirectResponse|JsonResponse {
        $order = SchoolOrder::query()
            ->findOrFail(
                $schoolOrder
            );

        $data = $this->validateOrder(
            $request,
            $order->id
        );

        unset(
            $data['_method']
        );

        if (
            !empty($data['is_paid'])
            && empty($data['paid_at'])
        ) {
            $data['paid_at'] = now();
        }

        try {
            DB::transaction(
                function () use (
                    $order,
                    $data
                ) {
                    $order->update(
                        $data
                    );
                }
            );

            $message =
                'Заказ успешно обновлён.';

            if (
                $request->expectsJson()
            ) {
                $currentLocale =
                    $this->resolveLocale(
                        $request
                    );

                $freshOrder = $order
                    ->fresh()
                    ->load([
                        'user:id,name,email',

                        'course' => fn ($query) =>
                        $query->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery->where(
                                    'locale',
                                    $currentLocale
                                ),
                        ]),

                        'schedule' => fn ($query) =>
                        $query->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery->where(
                                    'locale',
                                    $currentLocale
                                ),

                            'course' => fn ($courseQuery) =>
                            $courseQuery->with([
                                'translations' =>
                                    fn ($translationQuery) =>
                                    $translationQuery->where(
                                        'locale',
                                        $currentLocale
                                    ),
                            ]),
                        ]),
                    ]);

                return response()->json([
                    'message' =>
                        $message,

                    'order' =>
                        new SchoolOrderResource(
                            $freshOrder
                        ),
                ]);
            }

            return redirect()
                ->route(
                    'admin.schoolOrders.index'
                )
                ->with(
                    'success',
                    $message
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления school order ID '
                . $order->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,

                    'payload' =>
                        $data,
                ]
            );

            $message =
                'Ошибка при обновлении заказа.';

            return $request->expectsJson()
                ? response()->json(
                    [
                        'message' =>
                            $message,
                    ],
                    500
                )
                : back()
                    ->withInput()
                    ->with(
                        'error',
                        $message
                    );
        }
    }

    /**
     * =========================================================
     * DELETE / CLONE
     * =========================================================
     */

    /** Удаление заказа. */
    public function destroy(
        Request $request,
        int $schoolOrder
    ): RedirectResponse|JsonResponse {
        $order = SchoolOrder::query()
            ->findOrFail(
                $schoolOrder
            );

        try {
            $order->delete();

            $message =
                'Заказ успешно удалён.';

            return $request->expectsJson()
                ? response()->json([
                    'message' =>
                        $message,
                ])
                : redirect()
                    ->route(
                        'admin.schoolOrders.index'
                    )
                    ->with(
                        'success',
                        $message
                    );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка удаления school order ID '
                . $order->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            $message =
                'Ошибка при удалении заказа.';

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

    /** Клонирование заказа. */
    public function clone(
        Request $request,
        int $schoolOrder
    ): RedirectResponse|JsonResponse {
        $order = SchoolOrder::query()
            ->with([
                'orderItems',
            ])
            ->findOrFail(
                $schoolOrder
            );

        try {
            $clone = null;

            DB::transaction(
                function () use (
                    $order,
                    &$clone
                ) {
                    $clone =
                        $order->replicate([
                            'created_at',
                            'updated_at',
                        ]);

                    $clone->number =
                        $this->generateOrderNumber();

                    $clone->is_paid =
                        false;

                    $clone->paid_at =
                        null;

                    $clone->payment_status =
                        'pending';

                    $clone->confirmation_code =
                        null;

                    $clone->confirmation_status =
                        null;

                    $clone->failure_reason =
                        null;

                    $clone->payment_reference =
                        null;

                    $clone->external_id =
                        null;

                    $clone->exported_at =
                        null;

                    $clone->public_hash =
                        null;

                    $clone->status =
                        'new';

                    $clone->save();

                    foreach (
                        $order->orderItems
                        as $item
                    ) {
                        $clonedItem =
                            $item->replicate([
                                'created_at',
                                'updated_at',
                            ]);

                        $clonedItem->school_order_id =
                            $clone->id;

                        $clonedItem->save();
                    }
                }
            );

            $message =
                'Заказ успешно клонирован.';

            if (
                $request->expectsJson()
            ) {
                return response()->json([
                    'message' =>
                        $message,

                    'order' =>
                        new SchoolOrderResource(
                            $clone->fresh([
                                'user',
                            ])
                        ),
                ]);
            }

            return redirect()
                ->route(
                    'admin.schoolOrders.index'
                )
                ->with(
                    'success',
                    $message
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка клонирования school order ID '
                . $order->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            $message =
                'Ошибка при клонировании заказа.';

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
     * VALIDATION
     * =========================================================
     */

    /** Валидация заказа. */
    private function validateOrder(
        Request $request,
        ?int $orderId = null
    ): array {
        return $request->validate([
            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'school_course_id' => [
                'nullable',
                'integer',
                'exists:school_courses,id',
            ],

            'school_course_schedule_id' => [
                'nullable',
                'integer',
                'exists:school_course_schedules,id',
            ],

            'number' => [
                'nullable',
                'string',
                'max:64',
                'unique:school_orders,number,'
                . $orderId,
            ],

            'buyer_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'buyer_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'buyer_phone' => [
                'nullable',
                'string',
                'max:64',
            ],

            'billing_company' => [
                'nullable',
                'string',
                'max:255',
            ],

            'billing_tax_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'billing_address' => [
                'nullable',
                'string',
            ],

            'is_paid' => [
                'nullable',
                'boolean',
            ],

            'paid_at' => [
                'nullable',
                'date',
            ],

            'payment_method_id' => [
                'nullable',
                'integer',
            ],

            'payment_method' => [
                'nullable',
                'string',
                'max:64',
            ],

            'payment_provider' => [
                'nullable',
                'string',
                'max:64',
            ],

            'payment_reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'confirmation_code' => [
                'nullable',
                'string',
                'max:255',
            ],

            'confirmation_status' => [
                'nullable',
                'string',
                'max:64',
            ],

            'failure_reason' => [
                'nullable',
                'string',
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'subtotal' => [
                'required',
                'numeric',
                'min:0',
            ],

            'discount_total' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'tax_total' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'total' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                'string',
                'max:32',
            ],

            'payment_status' => [
                'required',
                'string',
                'max:32',
            ],

            'items' => [
                'nullable',
                'array',
            ],

            'meta' => [
                'nullable',
                'array',
            ],

            'user_comment' => [
                'nullable',
                'string',
            ],

            'manager_comment' => [
                'nullable',
                'string',
            ],

            'external_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'exported_at' => [
                'nullable',
                'date',
            ],

            'client_ip' => [
                'nullable',
                'string',
                'max:64',
            ],

            'user_agent' => [
                'nullable',
                'string',
            ],

            'public_hash' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);
    }

    /**
     * =========================================================
     * ORDER NUMBER
     * =========================================================
     */

    /** Генерация номера заказа. */
    private function generateOrderNumber(): string
    {
        do {
            $number =
                'ORD-'
                . now()->format('Ym')
                . '-ADM-'
                . Str::upper(
                    Str::random(6)
                );
        } while (
            SchoolOrder::query()
                ->where(
                    'number',
                    $number
                )
                ->exists()
        );

        return $number;
    }

    /**
     * =========================================================
     * SELECT HELPERS
     * =========================================================
     */

    /**
     * Пользователи для select.
     *
     * User не переводимый,
     * поэтому locale не требуется.
     */
    private function usersForSelect(): Collection
    {
        return User::query()
            ->select(
                'id',
                'name',
                'email'
            )
            ->orderBy(
                'name'
            )
            ->get();
    }

    /**
     * Курсы для select.
     *
     * Только currentLocale.
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
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        return SchoolCourseSharedResource::collection(
            $courses
        );
    }

    /**
     * Расписания для select.
     *
     * Schedule и его Course:
     * только currentLocale.
     */
    private function schedulesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $schedules =
            SchoolCourseSchedule::query()
                ->with([
                    'translations' =>
                        fn ($query) =>
                        $query->where(
                            'locale',
                            $locale
                        ),

                    'course' => fn ($query) =>
                    $query->with([
                        'translations' =>
                            fn ($translationQuery) =>
                            $translationQuery->where(
                                'locale',
                                $locale
                            ),
                    ]),
                ])
                ->orderByDesc('id')
                ->get();

        return SchoolCourseScheduleSharedResource::collection(
            $schedules
        );
    }

    /**
     * =========================================================
     * INDEX QUERIES
     * =========================================================
     */

    /**
     * Лёгкий query только для count().
     */
    private function countQuery(
        null|string|int $status = null,
        null|string|int $paymentStatus = null,
        null|string|bool $isPaidParam = null,
    ): Builder {
        return SchoolOrder::query()
            ->when(
                $status,
                fn (Builder $query) =>
                $query->where(
                    'status',
                    $status
                )
            )
            ->when(
                $paymentStatus,
                fn (Builder $query) =>
                $query->where(
                    'payment_status',
                    $paymentStatus
                )
            )
            ->when(
                $isPaidParam !== null
                && $isPaidParam !== '',
                function (Builder $query) use (
                    $isPaidParam
                ) {
                    $isPaid = filter_var(
                        $isPaidParam,
                        FILTER_VALIDATE_BOOL,
                        FILTER_NULL_ON_FAILURE
                    );

                    if ($isPaid !== null) {
                        $query->where(
                            'is_paid',
                            $isPaid
                        );
                    }
                }
            );
    }

    /**
     * Базовый query Admin Index.
     */
    private function indexQuery(
        string $locale,
        null|string|int $status = null,
        null|string|int $paymentStatus = null,
        null|string|bool $isPaidParam = null,
    ): Builder {
        return $this->countQuery(
            status: $status,
            paymentStatus: $paymentStatus,
            isPaidParam: $isPaidParam,
        )
            ->with([
                /**
                 * User не переводимый.
                 */
                'user:id,name,email',

                /**
                 * Course:
                 * только currentLocale.
                 */
                'course' => fn ($query) =>
                $query->with([
                    'translations' =>
                        fn ($translationQuery) =>
                        $translationQuery->where(
                            'locale',
                            $locale
                        ),
                ]),

                /**
                 * Schedule:
                 * только currentLocale.
                 */
                'schedule' => fn ($query) =>
                $query->with([
                    'translations' =>
                        fn ($translationQuery) =>
                        $translationQuery->where(
                            'locale',
                            $locale
                        ),

                    'course' => fn ($courseQuery) =>
                    $courseQuery->with([
                        'translations' =>
                            fn ($translationQuery) =>
                            $translationQuery->where(
                                'locale',
                                $locale
                            ),
                    ]),
                ]),
            ])
            ->withCount([
                'orderItems',
                'payments',
                'refunds',
                'invoices',
                'enrollments',
                'subscriptions',
            ]);
    }

    /**
     * Получение списка заказов
     * по текущему processing mode.
     */
    private function getIndexOrders(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
        null|string|int $status = null,
        null|string|int $paymentStatus = null,
        null|string|bool $isPaidParam = null,
    ) {
        $query = $this->indexQuery(
            locale: $locale,
            status: $status,
            paymentStatus: $paymentStatus,
            isPaidParam: $isPaidParam,
        );

        if ($useServerProcessing) {
            return $query
                ->search(
                    $search,
                    $locale
                )
                ->sortByParam(
                    $sort
                )
                ->paginate(
                    $perPage
                )
                ->withQueryString();
        }

        return $query
            ->sortByParam(
                $sort
            )
            ->get();
    }
}
