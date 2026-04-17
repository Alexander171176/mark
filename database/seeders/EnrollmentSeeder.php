<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\Order\Order;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\CourseSchedule\CourseSchedule;
use App\Models\Admin\School\Enrollment\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // sanity checks
        $required = ['users', 'courses', 'enrollments'];
        foreach ($required as $tbl) {
            if (!Schema::hasTable($tbl)) {
                $this->command?->warn("Таблица {$tbl} отсутствует — пропускаю EnrollmentSeeder.");
                return;
            }
        }

        $users   = User::query()->select('id')->get();
        $courses = Course::query()->select('id')->get();

        if ($users->isEmpty() || $courses->isEmpty()) {
            $this->command?->warn('Пользователи или курсы не найдены — пропускаю EnrollmentSeeder.');
            return;
        }

        // Подтягиваем расписания разом (если таблица есть)
        $schedulesByCourse = collect();
        if (Schema::hasTable('course_schedules')) {
            $schedulesByCourse = CourseSchedule::query()
                ->select('id', 'course_id', 'starts_at', 'ends_at', 'enroll_starts_at', 'enroll_ends_at', 'status')
                ->get()
                ->groupBy('course_id');
        }

        // Заказы (для привязки order_id, если есть)
        $paidOrdersByUser = collect();
        if (Schema::hasTable('orders')) {
            $paidOrdersByUser = Order::query()
                ->whereIn('status', ['paid', 'refunded', 'partial_refund'])
                ->select('id', 'user_id', 'created_at')
                ->latest('created_at')
                ->get()
                ->groupBy('user_id');
        }

        // Для каждого курса выберем пул пользователей и создадим зачисления
        foreach ($courses as $course) {
            // Сколько пользователей зачислить на этот курс
            $count = rand(8, 40);
            $pickedUsers = $users->random(min($count, $users->count()));

            foreach ($pickedUsers as $user) {
                // 60% — учимся с потоком (если есть расписание), 40% — самообучение
                $scheduleId = null;
                $courseSchedules = $schedulesByCourse->get($course->id, collect());
                if ($courseSchedules->isNotEmpty() && rand(0, 100) < 60) {
                    // предпочтём опубликованные, затем любые
                    $published = $courseSchedules->where('status', 'published');
                    $schedule  = ($published->isNotEmpty() ? $published : $courseSchedules)->random();
                    $scheduleId = $schedule->id;
                }

                // Статус зачисления и временные метки
                $status = $this->randomStatus();
                $startedAt = $this->pickStartedAt($scheduleId ? $courseSchedules->firstWhere('id', $scheduleId) : null);
                [$expiresAt, $completedAt] = $this->datesByStatus($status, $startedAt);

                // Прогресс
                $progress = $this->progressByStatus($status);

                // Заказ (иногда)
                $orderId = null;
                if ($paidOrdersByUser->has($user->id) && rand(0, 100) < 70) {
                    $orderId = $paidOrdersByUser[$user->id]->random()->id;
                }

                // идемпотентно по (user_id, course_id, course_schedule_id)
                Enrollment::updateOrCreate(
                    [
                        'user_id'            => $user->id,
                        'course_id'          => $course->id,
                        'course_schedule_id' => $scheduleId,
                    ],
                    [
                        'order_id'         => $orderId,
                        'status'           => $status,
                        'started_at'       => $startedAt,
                        'expires_at'       => $expiresAt,
                        'completed_at'     => $completedAt,
                        'progress_percent' => $progress,
                        'notes'            => $this->maybeNotes(),
                        'meta'             => $this->fakeMeta(),
                    ]
                );
            }
        }
    }

    /* ================= helpers ================= */

    private function randomStatus(): string
    {
        // Примерное распределение:
        // active 58%, completed 20%, cancelled 8%, expired 10%, paused 4%
        $r = rand(1, 100);
        return match (true) {
            $r <= 58 => 'active',
            $r <= 78 => 'completed',
            $r <= 86 => 'cancelled',
            $r <= 96 => 'expired',
            default  => 'paused',
        };
    }

    private function pickStartedAt(?CourseSchedule $schedule): ?Carbon
    {
        if ($schedule) {
            // если есть расписание: старт где-то около начала потока
            $base = $schedule->starts_at ?: now()->subWeeks(rand(1, 8));
            return (clone $base)->addDays(rand(0, 10))->setTime(rand(9, 21), rand(0, 59));
        }

        // самообучение: случай за последние 120 дней
        return now()->subDays(rand(1, 120))->setTime(rand(9, 21), rand(0, 59));
    }

    private function datesByStatus(string $status, ?Carbon $startedAt): array
    {
        $startedAt = $startedAt ?: now()->subDays(rand(1, 90));
        $expiresAt = null;
        $completedAt = null;

        switch ($status) {
            case 'active':
                // иногда есть дедлайн в будущем
                if (rand(0, 100) < 40) {
                    $expiresAt = (clone $startedAt)->addDays(rand(30, 120));
                }
                break;

            case 'completed':
                $completedAt = (clone $startedAt)->addDays(rand(14, 120));
                // иногда срок доступа истёк уже после завершения
                if (rand(0, 100) < 25) {
                    $expiresAt = (clone $completedAt)->addDays(rand(30, 180));
                }
                break;

            case 'expired':
                // истёк вчера/ранее
                $expiresAt = now()->subDays(rand(1, 30))->setTime(rand(9, 21), rand(0, 59));
                break;

            case 'cancelled':
                // отменили вскоре после старта
                $expiresAt = null;
                break;

            case 'paused':
                // пауза без даты истечения, скорее всего
                if (rand(0, 100) < 20) {
                    $expiresAt = (clone $startedAt)->addDays(rand(20, 90));
                }
                break;
        }

        return [$expiresAt, $completedAt];
    }

    private function progressByStatus(string $status): int
    {
        return match ($status) {
            'completed' => 100,
            'active'    => rand(5, 95),
            'paused'    => rand(20, 70),
            'cancelled' => rand(0, 40),
            'expired'   => rand(30, 85),
            default     => rand(0, 90),
        };
    }

    private function maybeNotes(): ?string
    {
        if (rand(0, 100) < 12) {
            return collect([
                'Перевод из другого потока.',
                'Доступ продлён на 30 дней.',
                'Выдан промокод на повторное прохождение.',
                'Необходимо подтвердить email.',
                'Просил паузу на отпуск.',
            ])->random();
        }
        return null;
    }

    private function fakeMeta(): array
    {
        return [
            'channel'  => collect(['self-paced', 'cohort', 'gift', 'corporate'])->random(),
            'ref'      => collect(['landing', 'blog', 'recommendation', 'ad', ''])->random(),
        ];
    }
}
