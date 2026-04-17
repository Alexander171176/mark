<?php

namespace Database\Seeders;

use App\Models\Admin\School\CohortEnrollment\CohortEnrollment;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\CourseSchedule\CourseSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CohortEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Проверим таблицы
        foreach (['users', 'course_schedules', 'cohort_enrollments', 'courses'] as $tbl) {
            if (!Schema::hasTable($tbl)) {
                $this->command?->warn("Таблица {$tbl} отсутствует — пропускаю CohortEnrollmentSeeder.");
                return;
            }
        }

        // Пользователи
        $users = User::query()->select('id', 'email')->get();
        if ($users->isEmpty()) {
            $this->command?->warn('Пользователи не найдены — запустить сидер пользователей перед CohortEnrollmentSeeder.');
            return;
        }

        // Расписания (можно ограничить только опубликованными)
        $schedules = CourseSchedule::query()
            ->select([
                'id',
                'course_id',
                'capacity',
                'status',
                'starts_at',
                'ends_at',
                'enroll_starts_at',
                'enroll_ends_at',
            ])
            ->get();

        if ($schedules->isEmpty()) {
            $this->command?->warn('Расписания курсов не найдены — запустите CourseScheduleSeeder.');
            return;
        }

        // Накапливаем счётчик утверждённых по курсам, чтобы потом обновить denorm
        $approvedByCourse = [];

        foreach ($schedules as $schedule) {
            $target = $this->targetEnrollments($schedule->capacity);

            // Выберем случайный набор пользователей (без повторов)
            $pickedUsers = $users->shuffle()->take($target);

            foreach ($pickedUsers as $u) {
                // Распределение статусов
                $status = $this->randomStatus();

                // enrolled_at: в окно записи, если оно задано, иначе около starts_at
                $enrolledAt = $this->randomEnrollDate($schedule);

                // Идемпотентно создаём (уникальный ключ на уровне БД: course_schedule_id + user_id)
                $enrollment = CohortEnrollment::firstOrCreate(
                    [
                        'course_schedule_id' => $schedule->id,
                        'user_id'            => $u->id,
                    ],
                    [
                        'status'      => $status,
                        'enrolled_at' => $enrolledAt,
                        'notes'       => null,
                    ]
                );

                // Если запись уже существовала — слегка «освежим» статус/дату, но не трогаем вручную изменённые
                if (!$enrollment->wasRecentlyCreated) {
                    // только если была "pending" — иногда апгрейдим до "approved"
                    if ($enrollment->status === 'pending' && rand(0,100) < 40) {
                        $enrollment->status = 'approved';
                        $enrollment->enrolled_at = $enrollment->enrolled_at ?? $enrolledAt;
                        $enrollment->save();
                    }
                }

                if (($enrollment->status ?? $status) === 'approved') {
                    $approvedByCourse[$schedule->course_id] = ($approvedByCourse[$schedule->course_id] ?? 0) + 1;
                }
            }
        }

        // Пересчёт denorm: students_count в courses (по всем потокам)
        $this->recalcCourseStudentsCount($approvedByCourse);
    }

    /**
     * Сколько записей сделать для расписания.
     */
    private function targetEnrollments(int $capacity): int
    {
        if ($capacity <= 0) {
            // без лимита — имитируем умеренный набор
            return rand(8, 35);
        }

        // с лимитом: от 60% до 100% заполнения
        $min = (int) max(1, floor($capacity * 0.6));
        $max = (int) max($min, $capacity);
        return rand($min, $max);
    }

    /**
     * Случайный статус записи.
     */
    private function randomStatus(): string
    {
        $r = rand(1, 100);
        // Примерное распределение:
        // 65% approved, 20% pending, 10% cancelled, 5% rejected
        return match (true) {
            $r <= 65 => 'approved',
            $r <= 85 => 'pending',
            $r <= 95 => 'cancelled',
            default  => 'rejected',
        };
    }

    /**
     * Правдоподобная дата записи в окно enroll_* (если задано),
     * иначе — около starts_at, чуть заранее.
     */
    private function randomEnrollDate($schedule): ?\Illuminate\Support\Carbon
    {
        // Если окно записи задано — возьмём случайный момент внутри
        if ($schedule->enroll_starts_at || $schedule->enroll_ends_at) {
            $start = $schedule->enroll_starts_at ?? now()->subDays(30);
            $end   = $schedule->enroll_ends_at   ?? now()->addDays(5);
            if ($end->lt($start)) {
                [$start, $end] = [$end, $start]; // на всякий случай
            }
            return now()->setTimestamp(rand($start->getTimestamp(), $end->getTimestamp()));
        }

        // Иначе — в диапазоне от starts_at - 30д до starts_at - 1д
        if ($schedule->starts_at) {
            $end   = $schedule->starts_at->copy()->subDay();
            $start = $schedule->starts_at->copy()->subDays(30);
            if ($end->lt($start)) {
                return $schedule->starts_at->copy()->subDays(rand(1, 14));
            }
            return now()->setTimestamp(rand($start->getTimestamp(), $end->getTimestamp()));
        }

        // Совсем без дат — вернём что-то недавнее
        return now()->subDays(rand(1, 21));
    }

    /**
     * Пересчитать denorm students_count по курсам.
     * Если курс не попал в $approvedByCourse (нет новых approved), пересчитаем его агрегатом из БД.
     */
    private function recalcCourseStudentsCount(array $approvedByCourse): void
    {
        // Соберём актуальные значения из БД (на случай, если часть курсов не попала в $approvedByCourse)
        $dbAgg = DB::table('cohort_enrollments as ce')
            ->join('course_schedules as cs', 'cs.id', '=', 'ce.course_schedule_id')
            ->select('cs.course_id', DB::raw("SUM(CASE WHEN ce.status = 'approved' THEN 1 ELSE 0 END) as approved_cnt"))
            ->groupBy('cs.course_id')
            ->pluck('approved_cnt', 'course_id')
            ->toArray();

        // Слить два источника: инкременты + фактическая агрегация
        $final = $dbAgg; // начальное — из БД
        foreach ($approvedByCourse as $courseId => $delta) {
            $final[$courseId] = ($final[$courseId] ?? 0) + $delta;
        }

        // Обновить courses.students_count
        foreach ($final as $courseId => $count) {
            Course::whereKey($courseId)->update(['students_count' => (int) $count]);
        }
    }
}
