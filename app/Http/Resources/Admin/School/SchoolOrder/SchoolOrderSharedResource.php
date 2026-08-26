<?php

namespace App\Http\Resources\Admin\School\SchoolOrder;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolOrderSharedResource extends JsonResource
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

            'paid_at' =>
                $this->paid_at?->toISOString(),

            /**
             * Платёжная информация.
             *
             * Нужна frontend-поиску.
             */
            'payment_method' =>
                $this->payment_method,

            'payment_provider' =>
                $this->payment_provider,

            'payment_reference' =>
                $this->payment_reference,

            /**
             * Служебные поля.
             *
             * Также участвуют
             * во frontend-поиске.
             */
            'external_id' =>
                $this->external_id,

            'client_ip' =>
                $this->client_ip,

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
             * Controller загружает только:
             * translations(currentLocale)
             */
            'course' =>
                new SchoolCourseSharedResource(
                    $this->whenLoaded(
                        'course'
                    )
                ),

            /**
             * Поток / расписание.
             *
             * Controller загружает только:
             * translations(currentLocale)
             *
             * и при необходимости:
             * course.translations(currentLocale)
             */
            'schedule' =>
                new SchoolCourseScheduleSharedResource(
                    $this->whenLoaded(
                        'schedule'
                    )
                ),

            /**
             * Counts для отображения
             * и дальнейшей frontend-логики.
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
             * Даты нужны отображению
             * и frontend-сортировке.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
