<?php

namespace App\Http\Controllers\Admin\Finance\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Finance\Order\OrderRequest;
use App\Http\Resources\Admin\Finance\Order\OrderResource;
use App\Models\Admin\Finance\Order\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class OrderController extends Controller
{
    /**
     * Список заказов в админке.
     * Фильтры и поиск — через query-параметры:
     *
     *  - search (номер заказа / имя / email / телефон)
     *
     * Пагинация и сортировка — на фронтенде.
     */
    public function index(Request $request): Response
    {
        $adminCountOrders = (int) config('site_settings.AdminCountOrders', 20);
        $adminSortOrders  = config('site_settings.AdminSortOrders', 'createdDesc');

        $status         = $request->query('status');
        $paymentStatus  = $request->query('payment_status');
        $isPaidParam    = $request->query('is_paid'); // '1', '0', null
        $search         = trim((string) $request->query('search', ''));

        $query = Order::query()
            ->with(['user:id,name,email']);

        // фильтр по статусу заказа
        if (!empty($status)) {
            $query->where('status', $status);
        }

        // фильтр по статусу оплаты
        if (!empty($paymentStatus)) {
            $query->where('payment_status', $paymentStatus);
        }

        // фильтр по флагу is_paid
        if ($isPaidParam !== null && $isPaidParam !== '') {
            $isPaid = filter_var($isPaidParam, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
            if ($isPaid !== null) {
                $query->where('is_paid', $isPaid);
            }
        }

        // поиск по номеру / покупателю / email / телефону
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('buyer_name', 'like', "%{$search}%")
                    ->orWhere('buyer_email', 'like', "%{$search}%")
                    ->orWhere('buyer_phone', 'like', "%{$search}%");
            });
        }

        // по умолчанию последние сверху
        $orders = $query
            ->orderByDesc('created_at')
            ->get();

        $ordersCount = Order::query()->count();

        return Inertia::render('Admin/Finance/Orders/Index', [
            'orders'                  => OrderResource::collection($orders)->resolve(),
            'ordersCount'             => $ordersCount,
            'filters'                 => [
                'status'         => $status,
                'payment_status' => $paymentStatus,
                'is_paid'        => $isPaidParam,
                'search'         => $search,
            ],
            'adminCountOrders'        => $adminCountOrders,
            'adminSortOrders'         => $adminSortOrders,
        ]);
    }

    /**
     * show в админке — редирект на форму редактирования.
     */
    public function show(Order $order): RedirectResponse
    {
        return redirect()->route('admin.orders.edit', $order);
    }

    /**
     * Форма редактирования заказа:
     *  - данные заказа
     *  - пользователь
     *  - статусы
     *  - связанные сущности (items, payments, refunds, enrollments)
     */
    public function edit(Order $order): Response
    {
        // TODO: $this->authorize('update-orders', $order);

        $order->load([
            'user:id,name,email',
            'items',        // позиции заказа
            'payments',     // платежи
            'refunds',      // возвраты
            'enrollments',  // зачисления (для онлайн-школы)
        ]);

        $users = User::query()
            ->orderBy('id')
            ->get(['id', 'name', 'email']);

        return Inertia::render('Admin/Finance/Orders/Edit', [
            'order' => new OrderResource($order),
            'users' => $users,
        ]);
    }

    /**
     * Обновление заказа.
     *
     * Здесь мы не трогаем order_items — только поля самого заказа.
     * Управление позициями можно сделать отдельными эндпоинтами.
     */
    public function update(OrderRequest $request, Order $order): RedirectResponse|JsonResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $order->update($data);

            DB::commit();

            Log::info('Заказ обновлён', [
                'id'     => $order->id,
                'number' => $order->number,
            ]);

            $message = __('admin/controllers.updated_success');

            // JSON-ответ (если запрос AJAX)
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'order'   => new OrderResource($order->fresh(['user'])),
                ]);
            }

            // ⬇⬇⬇ Вот тут меняем редирект
            return redirect()
                ->route('admin.orders.index')
                ->with('success', $message);

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при обновлении заказа ID {$order->id}", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'payload' => $data,
            ]);

            $errorMessage = __('admin/controllers.updated_error');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $errorMessage,
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors([
                    'server' => $errorMessage,
                ]);
        }
    }

    /**
     * Удаление заказа (soft delete, так как модель использует SoftDeletes).
     */
    public function destroy(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        // TODO: $this->authorize('delete-orders', $order);

        try {
            DB::beginTransaction();

            $order->delete();

            DB::commit();

            Log::info('Заказ удалён (soft delete)', [
                'id'     => $order->id,
                'number' => $order->number,
            ]);

            $message = __('admin/controllers.deleted_success');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ]);
            }

            return redirect()
                ->route('admin.orders.index')
                ->with('success', $message);

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при удалении заказа ID {$order->id}", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            $errorMessage = __('admin/controllers.deleted_error');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $errorMessage,
                ], 500);
            }

            return back()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Клонирование заказа.
     *
     * Копирует основные поля заказа, но:
     *  - генерирует новый number
     *  - сбрасывает оплату/статусы
     *
     * @param Request $request
     * @param int $order  ID заказа (через RMB)
     * @return RedirectResponse|JsonResponse
     */
    public function clone(Request $request, int $order): RedirectResponse|JsonResponse
    {
        // TODO: $this->authorize('create-orders', Order::class);

        /** @var Order $order */
        $order = Order::withTrashed()
            ->with(['user'])
            ->findOrFail($order);

        try {
            DB::beginTransaction();

            $cloned = $order->replicate();

            // Новый номер заказа
            $cloned->number = $this->generateOrderNumber();

            // Сброс полей, связанных с оплатой/экспортом
            $cloned->is_paid             = false;
            $cloned->paid_at             = null;
            $cloned->payment_status      = 'pending';
            $cloned->confirmation_code   = null;
            $cloned->confirmation_status = null;
            $cloned->failure_reason      = null;
            $cloned->external_id         = null;
            $cloned->exported_at         = null;

            // Статус заказа по умолчанию
            $cloned->status              = 'new';

            // Технические поля
            $cloned->public_hash         = null;

            // Даты / soft delete
            $cloned->created_at          = now();
            $cloned->updated_at          = now();
            $cloned->deleted_at          = null;

            $cloned->save();

            DB::commit();

            Log::info("Заказ ID {$order->id} успешно клонирован в ID {$cloned->id}", [
                'source_id' => $order->id,
                'clone_id'  => $cloned->id,
            ]);

            $message = __('admin/controllers.cloned_success');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'order'   => new OrderResource($cloned->fresh(['user'])),
                ]);
            }

            return redirect()
                ->route('admin.orders.index', $cloned)
                ->with('success', $message);

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при клонировании заказа ID {$order->id}: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = __('admin/controllers.cloned_error');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $errorMessage,
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Генерация уникального номера заказа.
     */
    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-'.now()->format('Ym').'-ADM-'.strtoupper(str()->random(6));
        } while (Order::where('number', $number)->exists());

        return $number;
    }

}
