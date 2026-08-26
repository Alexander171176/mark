<?php

namespace App\Http\Resources\Admin\School\SchoolOrder;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleSharedResource;
use App\Http\Resources\Admin\School\SchoolOrderItem\SchoolOrderItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' =>
                $this->id,

            'user_id' =>
                $this->user_id,

            'school_course_id' =>
                $this->school_course_id,

            'school_course_schedule_id' =>
                $this->school_course_schedule_id,

            /**
             * Заказ.
             */
            'number' =>
                $this->number,

            /**
             * Покупатель.
             */
            'buyer_name' =>
                $this->buyer_name,

            'buyer_email' =>
                $this->buyer_email,

            'buyer_phone' =>
                $this->buyer_phone,

            /**
             * Платёжные реквизиты.
             */
            'billing_company' =>
                $this->billing_company,

            'billing_tax_id' =>
                $this->billing_tax_id,

            'billing_address' =>
                $this->billing_address,

            /**
             * Оплата.
             */
            'is_paid' =>
                (bool) $this->is_paid
                || in_array(
                    $this->payment_status,
                    [
                        'paid',
                        'succeeded',
                    ],
                    true
                ),

            'paid_at' =>
                $this->paid_at?->toISOString(),

            'payment_method_id' =>
                $this->payment_method_id,

            'payment_method' =>
                $this->payment_method,

            'payment_provider' =>
                $this->payment_provider,

            'payment_reference' =>
                $this->payment_reference,

            'confirmation_code' =>
                $this->confirmation_code,

            'confirmation_status' =>
                $this->confirmation_status,

            'failure_reason' =>
                $this->failure_reason,

            /**
             * Суммы.
             */
            'currency' =>
                $this->currency,

            'subtotal' =>
                (string) $this->subtotal,

            'discount_total' =>
                (string) $this->discount_total,

            'tax_total' =>
                (string) $this->tax_total,

            'total' =>
                (string) $this->total,

            /**
             * Статусы.
             */
            'status' =>
                $this->status,

            'payment_status' =>
                $this->payment_status,

            'is_cancelled' =>
                $this->status === 'cancelled',

            'is_refunded' =>
                in_array(
                    $this->payment_status,
                    [
                        'refunded',
                        'partial',
                        'partially_refunded',
                    ],
                    true
                ),

            /**
             * Snapshot и служебные данные.
             */
            'items_snapshot' =>
                $this->items,

            'meta' =>
                $this->meta,

            'user_comment' =>
                $this->user_comment,

            'manager_comment' =>
                $this->manager_comment,

            'external_id' =>
                $this->external_id,

            'exported_at' =>
                $this->exported_at?->toISOString(),

            'client_ip' =>
                $this->client_ip,

            'user_agent' =>
                $this->user_agent,

            'public_hash' =>
                $this->public_hash,

            /**
             * Пользователь.
             *
             * User не переводимый.
             */
            'user' => $this->whenLoaded(
                'user',
                fn () => $this->user
                    ? [
                        'id' =>
                            $this->user->id,

                        'name' =>
                            $this->user->name,

                        'email' =>
                            $this->user->email,
                    ]
                    : null
            ),

            /**
             * Курс.
             *
             * Controller обязан загрузить:
             * course.translations(currentLocale)
             */
            'course' =>
                new SchoolCourseSharedResource(
                    $this->whenLoaded(
                        'course'
                    )
                ),

            /**
             * Поток / расписание курса.
             *
             * Controller обязан загрузить:
             * schedule.translations(currentLocale)
             * schedule.course.translations(currentLocale)
             */
            'schedule' =>
                new SchoolCourseScheduleSharedResource(
                    $this->whenLoaded(
                        'schedule'
                    )
                ),

            /**
             * Позиции заказа.
             *
             * Нужны Edit.
             */
            'items' =>
                SchoolOrderItemResource::collection(
                    $this->whenLoaded(
                        'orderItems'
                    )
                ),

            /**
             * Платежи.
             */
            'payments' => $this->whenLoaded(
                'payments',
                fn () => $this->payments->map(
                    fn ($payment) => [
                        'id' =>
                            $payment->id,

                        'school_payment_method_id' =>
                            $payment->school_payment_method_id,

                        'school_user_payment_method_id' =>
                            $payment->school_user_payment_method_id,

                        'provider' =>
                            $payment->provider,

                        'provider_payment_id' =>
                            $payment->provider_payment_id,

                        'status' =>
                            $payment->status,

                        'currency' =>
                            $payment->currency,

                        'amount' =>
                            (string) $payment->amount,

                        'captured_at' =>
                            $payment->captured_at?->toISOString(),

                        'refunded_at' =>
                            $payment->refunded_at?->toISOString(),

                        'refunded_amount' =>
                            $payment->refunded_amount !== null
                                ? (string) $payment->refunded_amount
                                : null,

                        'created_at' =>
                            $payment->created_at?->toISOString(),
                    ]
                )
            ),

            /**
             * Возвраты.
             */
            'refunds' => $this->whenLoaded(
                'refunds',
                fn () => $this->refunds->map(
                    fn ($refund) => [
                        'id' =>
                            $refund->id,

                        'school_payment_id' =>
                            $refund->school_payment_id,

                        'provider' =>
                            $refund->provider,

                        'provider_refund_id' =>
                            $refund->provider_refund_id,

                        'status' =>
                            $refund->status,

                        'currency' =>
                            $refund->currency,

                        'amount' =>
                            (string) $refund->amount,

                        'reason' =>
                            $refund->reason,

                        'requested_at' =>
                            $refund->requested_at?->toISOString(),

                        'processed_at' =>
                            $refund->processed_at?->toISOString(),

                        'created_at' =>
                            $refund->created_at?->toISOString(),
                    ]
                )
            ),

            /**
             * Инвойсы.
             */
            'invoices' => $this->whenLoaded(
                'invoices',
                fn () => $this->invoices->map(
                    fn ($invoice) => [
                        'id' =>
                            $invoice->id,

                        'number' =>
                            $invoice->number,

                        'status' =>
                            $invoice->status,

                        'currency' =>
                            $invoice->currency,

                        'total' =>
                            (string) $invoice->total,

                        'issued_at' =>
                            $invoice->issued_at?->toISOString(),

                        'due_at' =>
                            $invoice->due_at?->toISOString(),

                        'paid_at' =>
                            $invoice->paid_at?->toISOString(),
                    ]
                )
            ),

            /**
             * Зачисления.
             */
            'enrollments' => $this->whenLoaded(
                'enrollments',
                fn () => $this->enrollments->map(
                    fn ($enrollment) => [
                        'id' =>
                            $enrollment->id,

                        'user_id' =>
                            $enrollment->user_id,

                        'school_course_id' =>
                            $enrollment->school_course_id,

                        'school_course_schedule_id' =>
                            $enrollment->school_course_schedule_id,

                        'status' =>
                            $enrollment->status,

                        'progress_percent' =>
                            (int) $enrollment->progress_percent,
                    ]
                )
            ),

            /**
             * Подписки.
             */
            'subscriptions' => $this->whenLoaded(
                'subscriptions',
                fn () => $this->subscriptions->map(
                    fn ($subscription) => [
                        'id' =>
                            $subscription->id,

                        'user_id' =>
                            $subscription->user_id,

                        'school_subscription_plan_id' =>
                            $subscription->school_subscription_plan_id,

                        'status' =>
                            $subscription->status,

                        'currency' =>
                            $subscription->currency,

                        'price' =>
                            (string) $subscription->price,

                        'started_at' =>
                            $subscription->started_at?->toISOString(),

                        'ends_at' =>
                            $subscription->ends_at?->toISOString(),
                    ]
                )
            ),

            /**
             * Counts.
             */
            'order_items_count' => $this->when(
                isset($this->order_items_count),
                fn () => (int) $this->order_items_count
            ),

            'payments_count' => $this->when(
                isset($this->payments_count),
                fn () => (int) $this->payments_count
            ),

            'refunds_count' => $this->when(
                isset($this->refunds_count),
                fn () => (int) $this->refunds_count
            ),

            'invoices_count' => $this->when(
                isset($this->invoices_count),
                fn () => (int) $this->invoices_count
            ),

            'enrollments_count' => $this->when(
                isset($this->enrollments_count),
                fn () => (int) $this->enrollments_count
            ),

            'subscriptions_count' => $this->when(
                isset($this->subscriptions_count),
                fn () => (int) $this->subscriptions_count
            ),

            /**
             * Даты.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
