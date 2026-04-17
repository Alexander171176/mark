<?php

namespace Database\Seeders;

use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use App\Models\Admin\School\Quiz\Quiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // sanity checks
        foreach (['quizzes', 'courses'] as $tbl) {
            if (!Schema::hasTable($tbl)) {
                $this->command?->warn("Таблица {$tbl} отсутствует — пропускаю QuizSeeder.");
                return;
            }
        }

        $courses = Course::query()
            ->select('id', 'slug', 'title', 'locale') // locale добавили сюда
            ->get();

        if ($courses->isEmpty()) {
            $this->command?->warn('Курсы не найдены — сначала посейте курсы/модули/уроки.');
            return;
        }

        foreach ($courses as $course) {
            // Нормализуем локаль курса к ru/en/kk
            $locale = $this->normalizeLocale($course->locale);

            // ===== Квизы уровня курса =====
            $this->upsertCourseFinalQuiz($course, $locale);

            if (rand(0, 100) < 55) {
                $this->upsertCoursePracticeQuiz($course, $locale);
            }

            // ===== Квизы уровня модуля =====
            if (Schema::hasTable('modules')) {
                $modules = Module::query()
                    ->where('course_id', $course->id)
                    ->select('id', 'slug', 'title')
                    ->get();

                foreach ($modules as $module) {
                    $this->upsertModuleQuiz($course, $module, $locale);
                }
            }

            // ===== Practice-квизы уровня уроков =====
            if (Schema::hasTable('lessons') && Schema::hasTable('modules')) {
                $lessons = Lesson::query()
                    ->whereIn('module_id', function ($q) use ($course) {
                        $q->select('id')
                            ->from('modules')
                            ->where('course_id', $course->id);
                    })
                    ->select('id', 'module_id', 'title', 'slug', 'status', 'sort')
                    ->get();

                foreach ($lessons as $lesson) {
                    /**
                     * Вероятность выдачи practice-квиза:
                     *  - published: базово 35%
                     *  - draft:     базово 15%
                     *  - для первых уроков в модуле (sort <= 3) +15%
                     */
                    $baseChance = $lesson->status === 'published' ? 35 : 15;

                    if ((int) $lesson->sort <= 3) {
                        $baseChance += 15;
                    }

                    if (rand(0, 100) < $baseChance) {
                        $this->upsertLessonPracticeQuiz($course, $lesson, $locale);
                    }
                }
            }
        }
    }

    /**
     * Нормализация локали курса к тройке en|ru|kk.
     */
    private function normalizeLocale(?string $locale): string
    {
        $locale = strtolower((string) $locale);

        return in_array($locale, ['ru', 'en', 'kk'], true)
            ? $locale
            : 'ru';
    }

    /* ================= helpers: upsert creators ================= */

    private function upsertCourseFinalQuiz(Course $course, string $locale): void
    {
        $slug = "quiz-course-{$course->slug}-final";

        Quiz::updateOrCreate(
            ['slug' => $slug],
            [
                'course_id'          => $course->id,
                'module_id'          => null,
                'lesson_id'          => null,

                'locale'             => $locale,
                'title'              => "Итоговый тест по курсу «{$course->title}»",
                'short'              => 'Финальный тест, охватывающий ключевые темы курса.',
                'description'        => 'Финальный graded-квиз по материалам курса.',

                'type'               => 'graded',
                'attempts_limit'     => [1, 2, 3, 0][rand(0, 3)], // 0 = без ограничений
                'time_limit_minutes' => [20, 30, 45, null][rand(0, 3)],
                'pass_score'         => [60, 65, 70, 75, 80][rand(0, 4)],

                // Отображение
                'sort'               => 1000,
                'activity'           => true,
                'left'               => false,
                'main'               => true,   // финальный тест можно показать как основной
                'right'              => false,
                'published_at'       => now()->subDays(rand(5, 60)),
            ]
        );
    }

    private function upsertCoursePracticeQuiz(Course $course, string $locale): void
    {
        $slug = "quiz-course-{$course->slug}-practice";

        Quiz::updateOrCreate(
            ['slug' => $slug],
            [
                'course_id'          => $course->id,
                'module_id'          => null,
                'lesson_id'          => null,

                'locale'             => $locale,
                'title'              => "Самопроверка по курсу «{$course->title}»",
                'short'              => 'Небольшой тренировочный квиз для самопроверки по курсу.',
                'description'        => 'Небольшой тренировочный квиз для самопроверки.',

                'type'               => 'practice',
                'attempts_limit'     => 0,
                'time_limit_minutes' => null,
                'pass_score'         => 50,

                'sort'               => 900,
                'activity'           => true,
                'left'               => false,
                'main'               => false,
                'right'              => true,  // условно отправим в правую колонку
                'published_at'       => now()->subDays(rand(3, 30)),
            ]
        );
    }

    private function upsertModuleQuiz(Course $course, Module $module, string $locale): void
    {
        $slug      = "quiz-module-{$course->slug}-{$module->slug}";
        $published = rand(0, 100) < 80;

        Quiz::updateOrCreate(
            ['slug' => $slug],
            [
                'course_id'          => $course->id,
                'module_id'          => $module->id,
                'lesson_id'          => null,

                'locale'             => $locale,
                'title'              => "Тест по модулю: {$module->title}",
                'short'              => 'Промежуточный тест для проверки понимания модуля.',
                'description'        => 'Промежуточный graded-квиз по материалам модуля.',

                'type'               => 'graded',
                'attempts_limit'     => [1, 2, 3, 0][rand(0, 3)],
                'time_limit_minutes' => [10, 15, 20, null][rand(0, 3)],
                'pass_score'         => [60, 65, 70, 75][rand(0, 3)],

                'sort'               => 500,
                'activity'           => $published,
                'left'               => true,   // можно использовать для тестов в левой колонке
                'main'               => false,
                'right'              => false,
                'published_at'       => $published ? now()->subDays(rand(2, 45)) : null,
            ]
        );
    }

    private function upsertLessonPracticeQuiz(Course $course, Lesson $lesson, string $locale): void
    {
        $moduleSlug = Module::query()
            ->where('id', $lesson->module_id)
            ->value('slug') ?? "m{$lesson->module_id}";

        $slug = "quiz-lesson-{$course->slug}-{$moduleSlug}-{$lesson->slug}";

        Quiz::updateOrCreate(
            ['slug' => $slug],
            [
                'course_id'          => $course->id,
                'module_id'          => $lesson->module_id,
                'lesson_id'          => $lesson->id,

                'locale'             => $locale,
                'title'              => "Проверка по уроку: {$lesson->title}",
                'short'              => 'Короткий тест по материалу конкретного урока.',
                'description'        => 'Короткий practice-квиз для закрепления материала урока.',

                'type'               => 'practice',
                'attempts_limit'     => 0,
                'time_limit_minutes' => [5, 7, 10, null][rand(0, 3)],
                'pass_score'         => 50,

                'sort'               => 100,
                'activity'           => true,
                'left'               => false,
                'main'               => false,
                'right'              => false,
                'published_at'       => now()->subDays(rand(1, 25)),
            ]
        );
    }
}
