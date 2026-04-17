<?php

namespace Database\Seeders;

use App\Models\Admin\School\Assignment\Assignment;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (
            !Schema::hasTable('assignments') ||
            !Schema::hasTable('courses') ||
            !Schema::hasTable('modules') ||
            !Schema::hasTable('lessons')
        ) {
            $this->command?->warn('Отсутствуют таблицы (задания/курсы/модули/уроки) — пропускается AssignmentSeeder.');
            return;
        }

        $courses = Course::query()
            ->with([
                'modules:id,course_id,title,slug,status,published_at,sort',
                // ВАЖНО: без поля type, его нет в миграции lessons
                'modules.lessons:id,module_id,title,slug,status,published_at,sort',
            ])
            ->get([
                'id',
                'instructor_profile_id',
                'title',
                'slug',
                'status',
                'published_at',
                'locale', // NEW: если у курса есть локаль — пробрасываем в задания
            ]);

        if ($courses->isEmpty()) {
            $this->command?->warn('Курсы не найдены — сначала просей курсы/модули/уроки.');
            return;
        }

        DB::transaction(function () use ($courses) {
            foreach ($courses as $course) {
                // 1) 1–2 заданий на весь курс
                $this->seedCourseLevelAssignments($course, rand(1, 2));

                // 2) по 1 заданию на каждый модуль
                foreach ($course->modules as $module) {
                    $this->seedModuleLevelAssignment($course, $module);
                }

                // 3) 0–1 задания на урок
                foreach ($course->modules as $module) {
                    foreach ($module->lessons as $lesson) {
                        // Без поля type: вероятность зависит от статуса и позиции
                        $baseChance = $lesson->status === 'published' ? 45 : 15; // published — чаще
                        if ((int) $lesson->sort <= 3) {
                            $baseChance += 15; // для первых уроков чуть чаще даём ДЗ
                        }

                        $want = rand(0, 100) < $baseChance;

                        if ($want) {
                            $this->seedLessonLevelAssignment($course, $module, $lesson);
                        }
                    }
                }
            }
        });
    }

    /* ========================= Seed helpers ========================= */

    private function seedCourseLevelAssignments(Course $course, int $count): void
    {
        $locale = $course->locale ?? 'ru'; // NEW

        for ($i = 1; $i <= $count; $i++) {
            $title = $this->title("Итоговое задание по курсу: {$course->title}", $i);
            $slug  = $this->uniqueSlug(Str::slug("course-{$course->id}-{$title}"));

            $status     = $this->statusFromParent($course->status);
            $visibility = $this->visibilityFromStatus($status);
            $dueAt      = $this->maybeDueAt(10, 30, $status);
            $publishedAt = $this->publishedAtFromParent($course->published_at, $status); // NEW

            $assignment = Assignment::withTrashed()->firstOrNew(['slug' => $slug]);

            $assignment->fill([
                'course_id'             => $course->id,
                'module_id'             => null,
                'lesson_id'             => null,
                'instructor_profile_id' => $course->instructor_profile_id,

                'locale'      => $locale, // NEW
                'title'       => $title,
                'slug'        => $slug,
                'subtitle'    => 'Итоговое практическое задание по всему курсу', // NEW
                'short'       => Str::limit('Курсовое задание, в котором вы применяете на практике ключевые навыки из всех модулей курса.', 255), // NEW
                'description' => 'Курсовое задание: примените изучённые навыки на практике.',
                'instructions' => $this->instructions(),

                'max_score'      => 100,
                'attempts_limit' => rand(0, 1) ? 0 : 3,
                'grading_type'   => rand(0, 100) < 20 ? 'auto' : 'manual',
                'status'         => $status,
                'visibility'     => $visibility,
                'activity'       => true,

                'published_at' => $publishedAt, // NEW
                'due_at'       => $dueAt,
                'sort'         => $i,
            ]);

            if ($assignment->exists && $assignment->trashed()) {
                $assignment->restore();
            }

            $assignment->save();
        }
    }

    private function seedModuleLevelAssignment(Course $course, Module $module): void
    {
        $locale = $course->locale ?? 'ru'; // NEW

        $title = "Практика по модулю: {$module->title}";
        $slug  = $this->uniqueSlug(Str::slug("module-{$module->id}-practice"));

        $status      = $this->statusFromParent($module->status);
        $visibility  = $this->visibilityFromStatus($status);
        $dueAt       = $this->maybeDueAt(7, 21, $status);
        $publishedAt = $this->publishedAtFromParent($module->published_at, $status); // NEW

        $assignment = Assignment::withTrashed()->firstOrNew(['slug' => $slug]);

        $assignment->fill([
            'course_id'             => $course->id,
            'module_id'             => $module->id,
            'lesson_id'             => null,
            'instructor_profile_id' => $course->instructor_profile_id,

            'locale'      => $locale, // NEW
            'title'       => $title,
            'slug'        => $slug,
            'subtitle'    => 'Практическое задание по материалу модуля', // NEW
            'short'       => Str::limit('Выполните практическое задание, чтобы закрепить ключевые темы текущего модуля.', 255), // NEW
            'description' => 'Закрепляем материал модуля на практическом кейсе.',
            'instructions'=> $this->instructions(),

            'max_score'      => 100,
            'attempts_limit' => rand(0, 1) ? 0 : 2,
            'grading_type'   => rand(0, 100) < 25 ? 'auto' : 'manual',
            'status'         => $status,
            'visibility'     => $visibility,
            'activity'       => true,

            'published_at' => $publishedAt, // NEW
            'due_at'       => $dueAt,
            'sort'         => max(1, (int) $module->sort),
        ]);

        if ($assignment->exists && $assignment->trashed()) {
            $assignment->restore();
        }

        $assignment->save();
    }

    private function seedLessonLevelAssignment(Course $course, Module $module, Lesson $lesson): void
    {
        $locale = $course->locale ?? 'ru'; // NEW

        $title = "Домашнее задание: {$lesson->title}";
        $slug  = $this->uniqueSlug(Str::slug("lesson-{$lesson->id}-hw"));

        $status      = $this->statusFromParent($lesson->status);
        $visibility  = $this->visibilityFromStatus($status);
        $dueAt       = $this->maybeDueAt(3, 14, $status);
        $publishedAt = $this->publishedAtFromParent($lesson->published_at, $status); // NEW

        $assignment = Assignment::withTrashed()->firstOrNew(['slug' => $slug]);

        $assignment->fill([
            'course_id'             => $course->id,
            'module_id'             => $module->id,
            'lesson_id'             => $lesson->id,
            'instructor_profile_id' => $course->instructor_profile_id,

            'locale'      => $locale, // NEW
            'title'       => $title,
            'slug'        => $slug,
            'subtitle'    => 'Домашнее задание по материалу урока', // NEW
            'short'       => Str::limit('Выполните домашнее задание, чтобы закрепить навыки, полученные в этом уроке.', 255), // NEW
            'description' => 'Выполните задания по мотивам урока и приложите решение.',
            'instructions'=> $this->instructions(),

            'max_score'      => 100,
            'attempts_limit' => rand(0, 1) ? 0 : 2,
            'grading_type'   => rand(0, 100) < 30 ? 'auto' : 'manual',
            'status'         => $status,
            'visibility'     => $visibility,
            'activity'       => true,

            'published_at' => $publishedAt, // NEW
            'due_at'       => $dueAt,
            'sort'         => max(1, (int) $lesson->sort),
        ]);

        if ($assignment->exists && $assignment->trashed()) {
            $assignment->restore();
        }

        $assignment->save();
    }

    /* ========================= Utilities ========================= */

    private function title(string $base, int $i): string
    {
        return $i > 1 ? "{$base} (часть {$i})" : $base;
    }

    private function uniqueSlug(string $base): string
    {
        $base = $base !== '' ? $base : 'assignment';
        $slug = $base;
        $i    = 2;

        while (Assignment::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * Определяем статус задания на основе статуса родителя (курс/модуль/урок).
     */
    private function statusFromParent(?string $parentStatus): string
    {
        $parentStatus = $parentStatus ?? 'draft';

        // Если родитель опубликован — чаще делаем published
        if ($parentStatus === 'published') {
            return rand(0, 100) < 70 ? 'published' : 'draft';
        }

        // Остальные — чаще draft
        return rand(0, 100) < 30 ? 'published' : 'draft';
    }

    /**
     * Определяем visibility самого задания независимо от родителя.
     */
    private function visibilityFromStatus(string $status): string
    {
        if ($status === 'published') {
            return rand(0, 100) < 60 ? 'public' : 'enrolled';
        }

        return 'enrolled';
    }

    private function maybeDueAt(int $minDays, int $maxDays, string $status): ?Carbon
    {
        if ($status !== 'published') {
            return null;
        }

        // Иногда без дедлайна
        if (rand(0, 100) < 30) {
            return null;
        }

        return now()->addDays(rand($minDays, $maxDays));
    }

    /**
     * Публикация задания на основе даты публикации родителя.
     */
    private function publishedAtFromParent(?Carbon $parentPublishedAt, string $status): ?Carbon
    {
        if ($status !== 'published') {
            return null;
        }

        if ($parentPublishedAt instanceof Carbon) {
            // Немного «дрейфуем» от родителя, чтобы не все были в одну дату
            return $parentPublishedAt->copy()->addDays(rand(0, 5));
        }

        // Если у родителя нет published_at — считаем, что опубликовали недавно
        return now()->subDays(rand(0, 10));
    }

    private function instructions(): string
    {
        return <<<MD
### Что нужно сделать
1. Внимательно прочитайте формулировку задания.
2. Выполните практическую часть и оформите результат.
3. Загрузите архив/ссылку и приложите краткое описание решения.

**Критерии оценки:** корректность, стиль, оформление, полнота.
MD;
    }
}
