<?php

namespace Database\Seeders;

use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Module\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModuleSeeder extends Seeder
{
    /**
     * Доступные локали.
     */
    protected array $locales = ['ru', 'en', 'kk'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('courses') || !Schema::hasTable('modules')) {
            $this->command?->warn('courses/modules table is missing — skipping ModuleSeeder.');
            return;
        }

        $courses = Course::query()->get();

        if ($courses->isEmpty()) {
            $this->command?->warn('No courses found — run CourseSeeder first.');
            return;
        }

        DB::transaction(function () use ($courses) {
            foreach ($courses as $course) {

                // Для каждого курса создаём набор модулей на RU/EN/KK
                foreach ($this->locales as $locale) {

                    // Сколько модулей на этом языке
                    $moduleCount = rand(3, 6);

                    // Получаем набор локализованных названий
                    $titlePool = $this->moduleTitlePool($locale, $course->title);

                    // Берём нужное количество
                    $titles = $this->takeTitles($titlePool, $moduleCount);

                    foreach ($titles as $idx => $title) {
                        $sort = $idx + 1;

                        $baseSlug = Str::slug($title, '-');

                        // как в CourseSeeder: для ru – чистый slug, для en/kk – slug-<locale>
                        $slugBaseWithLocale = $locale === 'ru'
                            ? $baseSlug
                            : "{$baseSlug}-{$locale}";

                        $slug = $this->uniqueSlug($course->id, $slugBaseWithLocale);

                        [$status, $publishedAt] = $this->decideStatusDates($course, $sort);

                        $description = $this->makeDescription($locale, $title, $course->title);

                        // Создаём / обновляем по course_id + locale + slug
                        $module = Module::withTrashed()
                            ->where('course_id', $course->id)
                            ->where('locale', $locale)
                            ->where('slug', $slug)
                            ->first();

                        if (!$module) {
                            $module = new Module([
                                'course_id' => $course->id,
                                'locale'    => $locale,
                                'slug'      => $slug,
                            ]);
                        }

                        $module->fill([
                            'title'            => $title,
                            'subtitle'         => null,
                            'short'            => null,
                            'description'      => $description,

                            'status'           => $status,
                            'availability'     => 'public',
                            'published_at'     => $publishedAt,

                            'sort'             => $sort,
                            'duration' => rand(20, 180),

                            'lessons_count'    => 0,
                            'popularity'       => rand(0, 150),
                            'rating_count'     => rand(0, 150),
                            'rating_avg'       => rand(0, 500) / 100, // 0.00–5.00
                            'views'            => rand(20, 5000),
                            'likes'            => rand(0, 300),

                            'activity'         => true,
                        ]);

                        if ($module->exists && $module->trashed()) {
                            $module->restore();
                        }

                        $module->save();
                    }
                }
            }
        });
    }

    /* =======================================================================
     *                         TITLE POOLS (RU / EN / KK)
     * ======================================================================= */

    private function moduleTitlePool(string $locale, string $courseTitle): array
    {
        $course = mb_strtolower($courseTitle);

        // RU
        if ($locale === 'ru') {
            if (Str::contains($course, 'laravel')) {
                return [
                    'Введение в Laravel',
                    'Роутинг и контроллеры',
                    'Модели и Eloquent ORM',
                    'Миграции и фабрики',
                    'Валидация и FormRequest',
                    'API и ресурсы',
                    'Тестирование',
                    'Деплой',
                ];
            }

            if (Str::contains($course, 'javascript') || Str::contains($course, 'vue') || Str::contains($course, 'react')) {
                return [
                    'Основы синтаксиса JS',
                    'Работа с DOM',
                    'Модули и сборка',
                    'Асинхронность и API',
                    'Состояние приложения',
                    'Маршрутизация',
                    'Тестирование',
                    'Оптимизация',
                ];
            }

            return [
                'Вводный модуль',
                'Базовые концепции',
                'Работа на практике',
                'Инструменты и экосистема',
                'Работа с данными',
                'Проектная работа',
                'Оптимизация',
            ];
        }

        // EN
        if ($locale === 'en') {
            if (Str::contains($course, 'laravel')) {
                return [
                    'Introduction to Laravel',
                    'Routing and Controllers',
                    'Models and Eloquent',
                    'Migrations and Factories',
                    'Validation and Form Requests',
                    'REST API & Resources',
                    'Testing',
                    'Deployment',
                ];
            }

            if (Str::contains($course, 'javascript') || Str::contains($course, 'vue') || Str::contains($course, 'react')) {
                return [
                    'JavaScript Basics',
                    'DOM Manipulation',
                    'Modules & Build Tools',
                    'Async Programming & API',
                    'State Management',
                    'Routing',
                    'Testing',
                    'Performance Optimization',
                ];
            }

            return [
                'Intro Module',
                'Core Concepts',
                'Hands-on Practice',
                'Tools & Ecosystem',
                'Data Handling',
                'Project Work',
                'Optimization',
            ];
        }

        // KK (каз)
        if ($locale === 'kk') {
            if (Str::contains($course, 'laravel')) {
                return [
                    'Laravel негіздері',
                    'Маршруттар және контроллерлер',
                    'Модельдер және Eloquent',
                    'Миграциялар және фабрикалар',
                    'Валидация және Form Request',
                    'REST API және ресурстар',
                    'Тестілеу',
                    'Жариялау және деплой',
                ];
            }

            if (Str::contains($course, 'javascript') || Str::contains($course, 'vue') || Str::contains($course, 'react')) {
                return [
                    'JavaScript негіздері',
                    'DOM жұмыс істеу',
                    'Модульдер және құрастыру',
                    'Асинхрондылық және API',
                    'Мемлекеттік басқару',
                    'Маршрутизация',
                    'Тестілеу',
                    'Өнімділікті оңтайландыру',
                ];
            }

            return [
                'Кіріспе модуль',
                'Негізгі тұжырымдамалар',
                'Практикалық жұмыс',
                'Құралдар және экожүйе',
                'Деректермен жұмыс',
                'Жоба жұмысы',
                'Оңтайландыру',
            ];
        }

        return [];
    }

    private function takeTitles(array $pool, int $count): array
    {
        if (count($pool) >= $count) {
            return array_slice($pool, 0, $count);
        }

        $titles = $pool;
        $i = count($pool);

        while (count($titles) < $count) {
            $i++;
            $titles[] = "Module #{$i}";
        }

        return $titles;
    }

    /* =======================================================================
     *                               SLUG LOGIC
     * ======================================================================= */

    private function uniqueSlug(int $courseId, string $base): string
    {
        $slug = $base ?: 'module';
        $i    = 2;

        while (
        Module::withTrashed()
            ->where('course_id', $courseId)
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /* =======================================================================
     *                         PUBLISH / STATUS LOGIC
     * ======================================================================= */

    private function decideStatusDates(Course $course, int $sort): array
    {
        if ($course->status === 'published') {
            $status = (rand(1, 10) <= 8) ? 'published' : 'draft';
            $publishedAt = $status === 'published'
                ? $course->published_at?->copy()->addDays($sort - 1)
                : null;
            return [$status, $publishedAt];
        }

        $status = (rand(1, 10) <= 2) ? 'published' : 'draft';
        $publishedAt = $status === 'published' ? now()->subDays(rand(1, 30)) : null;
        return [$status, $publishedAt];
    }

    /* =======================================================================
     *                             DESCRIPTION
     * ======================================================================= */

    private function makeDescription(string $locale, string $moduleTitle, string $courseTitle): string
    {
        return match ($locale) {
            'ru' => "{$moduleTitle} — часть курса «{$courseTitle}». В модуле изучим ключевые темы, выполним практику и подготовимся к следующему этапу.",
            'en' => "{$moduleTitle} is part of the course “{$courseTitle}”. In this module we explore key concepts, practice tasks, and prepare for the next section.",
            'kk' => "{$moduleTitle} — «{$courseTitle}» курсының бөлігі. Бұл модульде негізгі тақырыптарды қарастырып, тәжірибе жасап, келесі бөлімге дайындаламыз.",
            default => $moduleTitle,
        };
    }
}
