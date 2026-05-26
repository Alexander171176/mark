<?php

namespace Database\Seeders;

use App\Models\Admin\School\CohortEnrollment\SchoolCohortEnrollment;
use Illuminate\Database\Seeder;

class SchoolCohortEnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $id = 1;

        $statuses = [
            1 => 'approved',
            2 => 'pending',
            3 => 'approved',
            4 => 'rejected',
            5 => 'cancelled',
            6 => 'approved',
        ];

        for ($scheduleId = 1; $scheduleId <= 8; $scheduleId++) {
            foreach (range(2, 7) as $index => $userId) {
                SchoolCohortEnrollment::updateOrCreate(
                    ['id' => $id],
                    [
                        'school_course_schedule_id' => $scheduleId,
                        'user_id' => $userId,
                        'status' => $statuses[$index + 1],
                        'enrolled_at' => now()->subDays($scheduleId + $index),
                        'notes' => match ($statuses[$index + 1]) {
                            'approved' => 'Заявка одобрена для тестового потока.',
                            'pending' => 'Заявка ожидает проверки администратором.',
                            'rejected' => 'Заявка отклонена в демонстрационных данных.',
                            'cancelled' => 'Запись отменена пользователем.',
                            default => null,
                        },
                    ]
                );

                $id++;
            }
        }
    }
}
