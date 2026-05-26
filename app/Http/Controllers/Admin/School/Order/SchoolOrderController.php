<?php

namespace App\Http\Controllers\Admin\School\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\Course\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\CourseSchedule\SchoolCourseScheduleSharedResource;
use App\Http\Resources\Admin\School\Order\SchoolOrderResource;
use App\Models\Admin\School\Course\SchoolCourse;
use App\Models\Admin\School\CourseSchedule\SchoolCourseSchedule;
use App\Models\Admin\School\Order\SchoolOrder;
use App\Models\User;
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
 * Контроллер для управления Заказами (SchoolOrder) в административной панели.
 * Список и редактирование заказов
 *
 * - фильтры и поиск
 * - пагинация
 * - сортировка
 * - удаление
 * - клонирование
 * - связи с пользователями, курсами, расписанием.
 *
 * @version 1.1 (заказы)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 */
class SchoolOrderController extends Controller
{
    /** Список заказов */
    public function index(Request $request): Response
    {
        $status = $request->query('status');
        $paymentStatus = $request->query('payment_status');
        $isPaidParam = $request->query('is_paid');
        $search = trim((string) $request->query('search', ''));

        $adminSchoolOrdersPerPage = (int) config('site_settings.adminSchoolOrdersPerPage', 10);
        $adminSchoolOrdersDefaultSort = (string) config('site_settings.adminSchoolOrdersDefaultSort', 'date_desc');

        try {
            $orders = SchoolOrder::query()
                ->with([
                    'user:id,name,email',
                    'course.translation',
                    'course.translations',
                    'schedule.translation',
                    'schedule.translations',
                    'schedule.course.translation',
                ])
                ->withCount([
                    'orderItems',
                    'payments',
                    'refunds',
                    'invoices',
                    'enrollments',
                    'subscriptions',
                ])
                ->when($status, fn ($query) => $query->where('status', $status))
                ->when($paymentStatus, fn ($query) => $query->where('payment_status', $paymentStatus))
                ->when($isPaidParam !== null && $isPaidParam !== '', function ($query) use ($isPaidParam) {
                    $isPaid = filter_var($isPaidParam, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

                    if ($isPaid !== null) {
                        $query->where('is_paid', $isPaid);
                    }
                })
                ->search($search)
                ->sortByParam($adminSchoolOrdersDefaultSort)
                ->get();

            return Inertia::render('Admin/School/Orders/Index', [
                'orders' => SchoolOrderResource::collection($orders),
                'ordersCount' => SchoolOrder::query()->count(),

                'filters' => [
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'is_paid' => $isPaidParam,
                    'search' => $search,
                ],

                'adminSchoolOrdersPerPage' => $adminSchoolOrdersPerPage,
                'adminSchoolOrdersDefaultSort' => $adminSchoolOrdersDefaultSort,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки school orders: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/Orders/Index', [
                'orders' => [],
                'ordersCount' => 0,

                'filters' => [
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'is_paid' => $isPaidParam,
                    'search' => $search,
                ],

                'adminSchoolOrdersPerPage' => $adminSchoolOrdersPerPage,
                'adminSchoolOrdersDefaultSort' => $adminSchoolOrdersDefaultSort,

                'error' => 'Ошибка загрузки заказов.',
            ]);
        }
    }

    /** Страница создания заказа */
    public function create(): Response
    {
        return Inertia::render('Admin/School/Orders/Create', [
            'users' => $this->usersForSelect(),
            'courses' => $this->coursesForSelect(),
            'schedules' => $this->schedulesForSelect(),
        ]);
    }

    /** Создание заказа */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateOrder($request);

        if (empty($data['number'])) {
            $data['number'] = $this->generateOrderNumber();
        }

        if (!empty($data['is_paid']) && empty($data['paid_at'])) {
            $data['paid_at'] = now();
        }

        try {
            SchoolOrder::create($data);

            return redirect()
                ->route('admin.schoolOrders.index')
                ->with('success', 'Заказ успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school order: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $data,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании заказа.');
        }
    }

    /** Редирект на редактирование */
    public function show(int $schoolOrder): RedirectResponse
    {
        return redirect()->route('admin.schoolOrders.edit', $schoolOrder);
    }

    /** Страница редактирования заказа */
    public function edit(int $schoolOrder): Response
    {
        $order = SchoolOrder::query()
            ->with([
                'user:id,name,email',
                'course.translation',
                'course.translations',
                'schedule.translation',
                'schedule.translations',
                'schedule.course.translation',
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
            ->findOrFail($schoolOrder);

        return Inertia::render('Admin/School/Orders/Edit', [
            'order' => new SchoolOrderResource($order),

            'users' => $this->usersForSelect(),
            'courses' => $this->coursesForSelect(),
            'schedules' => $this->schedulesForSelect(),
        ]);
    }

    /** Обновление заказа */
    public function update(Request $request, int $schoolOrder): RedirectResponse|JsonResponse
    {
        $order = SchoolOrder::query()->findOrFail($schoolOrder);

        $data = $this->validateOrder($request, $order->id);

        unset($data['_method']);

        if (!empty($data['is_paid']) && empty($data['paid_at'])) {
            $data['paid_at'] = now();
        }

        try {
            DB::transaction(function () use ($order, $data) {
                $order->update($data);
            });

            $message = 'Заказ успешно обновлён.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'order' => new SchoolOrderResource(
                        $order->fresh(['user', 'course.translation', 'schedule.translation'])
                    ),
                ]);
            }

            return redirect()
                ->route('admin.schoolOrders.index')
                ->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school order ID ' . $order->id . ': ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $data,
            ]);

            $message = 'Ошибка при обновлении заказа.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->withInput()->with('error', $message);
        }
    }

    /** Удаление заказа */
    public function destroy(Request $request, int $schoolOrder): RedirectResponse|JsonResponse
    {
        $order = SchoolOrder::query()->findOrFail($schoolOrder);

        try {
            $order->delete();

            $message = 'Заказ успешно удалён.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : redirect()
                    ->route('admin.schoolOrders.index')
                    ->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school order ID ' . $order->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = 'Ошибка при удалении заказа.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /** Клонирование заказа */
    public function clone(Request $request, int $schoolOrder): RedirectResponse|JsonResponse
    {
        $order = SchoolOrder::query()
            ->with(['orderItems'])
            ->findOrFail($schoolOrder);

        try {
            DB::transaction(function () use ($order, &$clone) {
                $clone = $order->replicate([
                    'created_at',
                    'updated_at',
                ]);

                $clone->number = $this->generateOrderNumber();

                $clone->is_paid = false;
                $clone->paid_at = null;
                $clone->payment_status = 'pending';

                $clone->confirmation_code = null;
                $clone->confirmation_status = null;
                $clone->failure_reason = null;
                $clone->payment_reference = null;
                $clone->external_id = null;
                $clone->exported_at = null;
                $clone->public_hash = null;

                $clone->status = 'new';

                $clone->save();

                foreach ($order->orderItems as $item) {
                    $clonedItem = $item->replicate([
                        'created_at',
                        'updated_at',
                    ]);

                    $clonedItem->school_order_id = $clone->id;
                    $clonedItem->save();
                }
            });

            $message = 'Заказ успешно клонирован.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'order' => new SchoolOrderResource($clone->fresh(['user'])),
                ]);
            }

            return redirect()
                ->route('admin.schoolOrders.index')
                ->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка клонирования school order ID ' . $order->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = 'Ошибка при клонировании заказа.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /** Валидация заказа */
    private function validateOrder(Request $request, ?int $orderId = null): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'school_course_id' => ['nullable', 'integer', 'exists:school_courses,id'],
            'school_course_schedule_id' => ['nullable', 'integer', 'exists:school_course_schedules,id'],

            'number' => [
                'nullable',
                'string',
                'max:64',
                'unique:school_orders,number,' . $orderId,
            ],

            'buyer_name' => ['nullable', 'string', 'max:255'],
            'buyer_email' => ['nullable', 'email', 'max:255'],
            'buyer_phone' => ['nullable', 'string', 'max:64'],

            'billing_company' => ['nullable', 'string', 'max:255'],
            'billing_tax_id' => ['nullable', 'string', 'max:255'],
            'billing_address' => ['nullable', 'string'],

            'is_paid' => ['nullable', 'boolean'],
            'paid_at' => ['nullable', 'date'],

            'payment_method_id' => ['nullable', 'integer'],
            'payment_method' => ['nullable', 'string', 'max:64'],
            'payment_provider' => ['nullable', 'string', 'max:64'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'confirmation_code' => ['nullable', 'string', 'max:255'],
            'confirmation_status' => ['nullable', 'string', 'max:64'],
            'failure_reason' => ['nullable', 'string'],

            'currency' => ['required', 'string', 'size:3'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'discount_total' => ['nullable', 'numeric', 'min:0'],
            'tax_total' => ['nullable', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],

            'status' => ['required', 'string', 'max:32'],
            'payment_status' => ['required', 'string', 'max:32'],

            'items' => ['nullable', 'array'],
            'meta' => ['nullable', 'array'],

            'user_comment' => ['nullable', 'string'],
            'manager_comment' => ['nullable', 'string'],

            'external_id' => ['nullable', 'string', 'max:255'],
            'exported_at' => ['nullable', 'date'],

            'client_ip' => ['nullable', 'string', 'max:64'],
            'user_agent' => ['nullable', 'string'],
            'public_hash' => ['nullable', 'string', 'max:255'],
        ]);
    }

    /** Генерация номера заказа */
    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . now()->format('Ym') . '-ADM-' . Str::upper(Str::random(6));
        } while (SchoolOrder::query()->where('number', $number)->exists());

        return $number;
    }

    /** Пользователи для select */
    private function usersForSelect(): Collection|array
    {
        return User::query()
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }

    /** Курсы для select */
    private function coursesForSelect(): AnonymousResourceCollection
    {
        $courses = SchoolCourse::query()
            ->with(['translation', 'translations'])
            ->get();

        return SchoolCourseSharedResource::collection($courses);
    }

    /** Расписания для select */
    private function schedulesForSelect(): AnonymousResourceCollection
    {
        $schedules = SchoolCourseSchedule::query()
            ->with([
                'translation',
                'translations',
                'course.translation',
                'course.translations',
            ])
            ->get();

        return SchoolCourseScheduleSharedResource::collection($schedules);
    }
}
