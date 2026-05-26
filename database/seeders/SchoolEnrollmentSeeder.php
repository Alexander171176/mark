<?php

namespace Database\Seeders;

use App\Models\Admin\School\Enrollment\SchoolEnrollment;
use App\Models\Admin\School\Order\SchoolOrder;
use Illuminate\Database\Seeder;

class SchoolEnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            1 => 'active',
            2 => 'completed',
            3 => 'active',
            4 => 'paused',
            5 => 'cancelled',
            6 => 'expired',
        ];

        for ($id = 1; $id <= 48; $id++) {
            $order = SchoolOrder::find($id);

            if (!$order) {
                continue;
            }

            $status = $statuses[(($id - 1) % 6) + 1];

            SchoolEnrollment::updateOrCreate(
                ['id' => $id],
                [
                    'user_id' => $order->user_id,
                    'school_course_id' => $order->school_course_id,
                    'school_course_schedule_id' => $order->school_course_schedule_id,
                    'school_order_id' => $order->id,

                    'status' => $status,

                    'started_at' => now()->subDays(30 + $id),
                    'expires_at' => match ($status) {
                        'expired' => now()->subDays(5),
                        'cancelled' => now()->addDays(10),
                        default => now()->addMonths(6),
                    },
                    'completed_at' => $status === 'completed'
                        ? now()->subDays($id)
                        : null,

                    'progress_percent' => match ($status) {
                        'completed' => 100,
                        'active' => 20 + (($id * 7) % 70),
                        'paused' => 45,
                        'expired' => 30,
                        'cancelled' => 10,
                        default => 0,
                    },

                    'notes' => match ($status) {
                        'active' => 'Активное зачисление на курс.',
                        'completed' => 'Курс завершён пользователем.',
                        'paused' => 'Обучение временно приостановлено.',
                        'cancelled' => 'Зачисление отменено.',
                        'expired' => 'Срок доступа истёк.',
                        default => null,
                    },

                    'meta' => [
                        'source' => 'seeder',
                        'demo' => true,
                    ],
                ]
            );
        }
    }
}
