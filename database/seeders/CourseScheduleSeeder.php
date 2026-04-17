<?php

namespace Database\Seeders;

use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\CourseSchedule\CourseSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CourseScheduleSeeder extends Seeder
{
    /**
     * Локальные счётчики sort по каждому course_id.
     *
     * @var array<int,int>
     */
    private array $sortCounters = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('course_schedules') || !Schema::hasTable('courses')) {
            $this->command?->warn('Отсутствуют таблицы (course_schedules/courses) — пропускается CourseScheduleSeeder.');
            return;
        }

        $courses = Course::query()
            ->get([
                'id',
                'title',
                'slug',
                'locale',
                'instructor_profile_id',
                'status',
                'published_at',
            ]);

        if ($courses->isEmpty()) {
            $this->command?->warn('Курсы не найдены — сначала создайте начальные курсы.');
            return;
        }

        foreach ($courses as $course) {
            // 1) Прошлый поток (archived)
            $this->seedArchivedCohort($course);

            // 2) Текущий активный (published, идёт сейчас) — не для всех
            if (rand(0, 100) < 60) {
                $this->seedActiveCohort($course);
            }

            // 3) Будущий поток (published/upcoming)
            $this->seedUpcomingCohort($course);

            // Иногда добавим ещё один будущий «черновик»
            if (rand(0, 100) < 35) {
                $this->seedDraftCohort($course);
            }
        }
    }

    /* ================== Cohort variants ================== */

    private function seedArchivedCohort(Course $course): void
    {
        $tz       = $this->randomTz();
        $baseName = 'Поток весна';
        $start    = now($tz)->subMonths(rand(4, 7))->startOfWeek()->setTime(19, 0);
        $end      = (clone $start)->addWeeks(rand(6, 10));
        $enStart  = (clone $start)->subWeeks(3);
        $enEnd    = (clone $start)->subDays(2);

        $this->upsertSchedule($course, [
            'title'            => "{$baseName} " . now()->subMonths(6)->year,
            'starts_at'        => $start->utc(),
            'ends_at'          => $end->utc(),
            'enroll_starts_at' => $enStart->utc(),
            'enroll_ends_at'   => $enEnd->utc(),
            'capacity'         => [0, 25, 40][rand(0, 2)],
            'is_online'        => (bool) rand(0, 1),
            'location'         => $this->maybeLocation(),
            'meeting_url'      => $this->maybeMeetingUrl(),
            'timezone'         => $tz,
            'status'           => 'archived',
            'activity'         => false,
            'notes'            => 'Завершённый поток (для истории).',
        ]);
    }

    private function seedActiveCohort(Course $course): void
    {
        $tz       = $this->randomTz();
        $baseName = 'Текущий поток';
        $start    = now($tz)->subWeeks(rand(1, 3))->startOfWeek()->setTime(19, 0);
        $end      = (clone $start)->addWeeks(rand(6, 10));
        $enStart  = (clone $start)->subWeeks(2);
        $enEnd    = (clone $start)->addWeeks(1);

        $this->upsertSchedule($course, [
            'title'            => $baseName,
            'starts_at'        => $start->utc(),
            'ends_at'          => $end->utc(),
            'enroll_starts_at' => $enStart->utc(),
            'enroll_ends_at'   => $enEnd->utc(),
            'capacity'         => [0, 30, 50][rand(0, 2)],
            'is_online'        => (bool) rand(0, 1),
            'location'         => $this->maybeLocation(),
            'meeting_url'      => $this->maybeMeetingUrl(),
            'timezone'         => $tz,
            'status'           => 'published',
            'activity'         => true,
            'notes'            => 'Идёт сейчас.',
        ]);
    }

    private function seedUpcomingCohort(Course $course): void
    {
        $tz       = $this->randomTz();
        $baseName = 'Осенний набор';
        $start    = now($tz)->addWeeks(rand(2, 6))->startOfWeek()->setTime(19, 0);
        $end      = (clone $start)->addWeeks(rand(6, 10));
        $enStart  = (clone $start)->subWeeks(4);
        $enEnd    = (clone $start)->subDays(1);

        $this->upsertSchedule($course, [
            'title'            => "{$baseName} " . now()->year,
            'starts_at'        => $start->utc(),
            'ends_at'          => $end->utc(),
            'enroll_starts_at' => $enStart->utc(),
            'enroll_ends_at'   => $enEnd->utc(),
            'capacity'         => [0, 20, 35, 60][rand(0, 3)],
            'is_online'        => (bool) rand(0, 1),
            'location'         => $this->maybeLocation(),
            'meeting_url'      => $this->maybeMeetingUrl(),
            'timezone'         => $tz,
            'status'           => 'published',
            'activity'         => true,
            'notes'            => 'Будущий поток, открыт для записи.',
        ]);
    }

    private function seedDraftCohort(Course $course): void
    {
        $tz       = $this->randomTz();
        $baseName = 'Зимний черновик';
        $start    = now($tz)->addMonths(rand(2, 4))->startOfWeek()->setTime(19, 0);
        $end      = (clone $start)->addWeeks(rand(6, 10));

        $this->upsertSchedule($course, [
            'title'            => "{$baseName} " . now()->addMonths(3)->year,
            'starts_at'        => $start->utc(),
            'ends_at'          => $end->utc(),
            'enroll_starts_at' => null,
            'enroll_ends_at'   => null,
            'capacity'         => 0,
            'is_online'        => (bool) rand(0, 1),
            'location'         => $this->maybeLocation(),
            'meeting_url'      => $this->maybeMeetingUrl(),
            'timezone'         => $tz,
            'status'           => 'draft',
            'activity'         => true,
            'notes'            => 'Черновик расписания: даты/описание уточняются.',
        ]);
    }

    /* ================== Upsert helper ================== */

    /**
     * Идемпотентное сохранение по (course_id, title, starts_at).
     * Восстанавливает soft-deleted запись при повторном запуске.
     */
    private function upsertSchedule(Course $course, array $data): void
    {
        $key = [
            'course_id' => $course->id,
            'title'     => $data['title'],
            'starts_at' => $data['starts_at'],
        ];

        /** @var CourseSchedule $schedule */
        $schedule = CourseSchedule::withTrashed()->firstOrNew($key);

        $locale = $course->locale ?? 'ru';

        $baseSlug       = $course->slug ?? Str::slug($course->title);
        $titleSlugPart  = Str::slug($data['title'] ?? '');
        $startYear      = $data['starts_at'] ? $data['starts_at']->year : now()->year;
        $slugSource     = trim($baseSlug . '-' . $titleSlugPart . '-' . $startYear, '-');
        $generatedSlug  = Str::slug($slugSource);

        $subtitle = $data['status'] === 'draft'
            ? 'Черновой поток курса'
            : 'Учебный поток курса';

        $short = "Группа «{$data['title']}» по курсу «{$course->title}».";

        $formatLabel   = $data['is_online'] ? 'онлайн' : 'офлайн';
        $startsAtLocal = $data['starts_at']
            ? $data['starts_at']->clone()->setTimezone($data['timezone'] ?? 'UTC')->format('d.m.Y')
            : null;

        $description = "Поток курса «{$course->title}» ({$data['title']}).\n\n"
            . "Занятия проходят в формате {$formatLabel}.";

        $metaTitle = "{$course->title} — {$data['title']}";

        $metaDesc = $startsAtLocal
            ? "Учебный поток «{$data['title']}» по курсу «{$course->title}». "
            . "Старт: {$startsAtLocal}, формат: {$formatLabel}."
            : "Учебный поток «{$data['title']}» по курсу «{$course->title}». Формат: {$formatLabel}.";

        $metaKeywords = implode(', ', [
            $course->title,
            'поток курса',
            'онлайн обучение',
            'расписание курса',
        ]);

        $views = $schedule->exists
            ? $schedule->views
            : $this->randomViews($data['status']);

        $schedule->fill(array_merge(
            [
                'sort'          => $schedule->sort ?? $this->nextSort($course->id),
                'locale'        => $schedule->locale ?? $locale,
                'slug'          => $schedule->slug ?? $generatedSlug,
                'subtitle'      => $schedule->subtitle ?? $subtitle,
                'short'         => $schedule->short ?? $short,
                'description'   => $schedule->description ?? $description,
                'meta_title'    => $schedule->meta_title ?? $metaTitle,
                'meta_keywords' => $schedule->meta_keywords ?? $metaKeywords,
                'meta_desc'     => $schedule->meta_desc ?? $metaDesc,
                'views'         => $views,
            ],
            $data,
            [
                // Преподавателя по умолчанию тянем из курса, если явного нет
                'instructor_profile_id' => $schedule->instructor_profile_id ?? $course->instructor_profile_id,
            ]
        ));

        if ($schedule->exists && $schedule->trashed()) {
            $schedule->restore();
        }

        $schedule->save();
    }

    /**
     * Простейший счётчик sort по course_id.
     */
    private function nextSort(int $courseId): int
    {
        if (!isset($this->sortCounters[$courseId])) {
            $this->sortCounters[$courseId] = 1;
        } else {
            $this->sortCounters[$courseId]++;
        }

        return $this->sortCounters[$courseId];
    }

    /**
     * Разные диапазоны просмотров в зависимости от статуса.
     */
    private function randomViews(string $status): int
    {
        return match ($status) {
            'archived'  => rand(100, 600),
            'published' => rand(50, 400),
            'draft'     => rand(0, 80),
            default     => rand(10, 200),
        };
    }

    /* ================== Small helpers ================== */

    private function randomTz(): string
    {
        return collect([
            'Europe/Moscow', 'Europe/Berlin', 'Europe/Warsaw',
            'UTC', 'Asia/Almaty', 'Asia/Tashkent',
            'Asia/Yekaterinburg', 'Asia/Vladivostok', 'America/New_York',
        ])->random();
    }

    private function maybeLocation(): ?string
    {
        if (rand(0, 100) < 50) {
            return null; // чаще онлайн без адреса
        }

        return collect([
            'Москва, наб. Пресненская 10',
            'Санкт-Петербург, Литейный пр., 14',
            'Казань, ул. Баумана, 5',
            'Минск, пр. Независимости, 15',
            'Алматы, пр. Абая, 25',
        ])->random();
    }

    private function maybeMeetingUrl(): ?string
    {
        if (rand(0, 100) < 60) {
            // Zoom/Meet псевдо-ссылка
            return collect([
                'https://zoom.us/j/7' . rand(10000000, 99999999),
                'https://meet.google.com/' . Str::lower(Str::random(3)) . '-' . Str::lower(Str::random(4)) . '-' . Str::lower(Str::random(3)),
                'https://teams.microsoft.com/l/meetup-join/' . Str::uuid(),
            ])->random();
        }

        return null;
    }
}
