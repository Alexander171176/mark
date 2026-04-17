<?php

namespace Database\Seeders;

use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('modules') || !Schema::hasTable('lessons')) {
            $this->command?->warn('modules/lessons table is missing — skipping LessonSeeder.');
            return;
        }

        $modules = Module::query()->get(['id', 'course_id', 'title', 'status', 'published_at']);

        if ($modules->isEmpty()) {
            $this->command?->warn('No modules found — run ModuleSeeder first.');
            return;
        }

        DB::transaction(function () use ($modules) {
            foreach ($modules as $module) {
                $this->seedLessonsForModule($module);
                $this->recalcModuleAggregates($module);
            }
        });
    }

    /* ================= Helpers ================= */

    private function seedLessonsForModule(Module $module): void
    {
        // Сколько уроков будет в модуле
        $lessonCount = rand(3, 6);

        // Первый(е) 1–2 урока — полностью бесплатный превью
        $freePreviewCount = rand(1, 2);

        // 🔹 Локали, которые хотим чередовать
        $locales = ['ru', 'en', 'kk'];

        // Заголовки
        $titles = $this->makeLessonTitles($module->title, $lessonCount);

        foreach ($titles as $idx => $title) {
            $sort = $idx + 1;

            // slug уникален в границах module_id
            $baseSlug = Str::slug($title) ?: 'lesson';
            $slug     = $this->uniqueSlugWithinModule($module->id, $baseSlug);

            $type     = $this->pickLessonType($sort);
            $duration = $this->decideDurationByType($type);

            // Статус и дата публикации согласованы со статусом модуля
            [$status, $publishedAt] = $this->decideStatusDates($module, $sort);

            // free preview для первых X уроков, если опубликованы
            $isFree = $sort <= $freePreviewCount && $status === 'published';

            $lesson = Lesson::withTrashed()
                ->where('module_id', $module->id)
                ->where('slug', $slug)
                ->first();

            if (!$lesson) {
                $lesson = new Lesson([
                    'module_id' => $module->id,
                    'slug'      => $slug,
                ]);
            }

            $description = $this->makeBody($title, $type);

            $metaTitle = Str::limit($title, 160);
            $metaDesc = Str::limit(
                Str::of(strip_tags((string) $description))->trim(),
                160
            );
            $metaKeywords = Str::limit(
                collect(preg_split('/\s+/u', mb_strtolower($title)))
                    ->filter(fn ($word) => $word && mb_strlen($word) >= 3)
                    ->unique()
                    ->implode(', '),
                160
            );

            // 🔹 Берём локаль по кругу: ru, en, kk, ru, en, kk...
            $locale = $locales[$idx % count($locales)];

            $lesson->fill([
                // Основное
                'title'       => $title,
                'subtitle'    => null,
                'short'       => $description ? Str::limit(strip_tags($description), 240) : null,
                'description' => $description,

                // публикация / доступность
                'status'       => $status,
                'availability' => 'public',
                'published_at' => $publishedAt,

                // 🔹 ЯВНО пишем локаль
                'locale'       => $locale,

                // превью
                'preview_mode'  => $isFree ? 'full' : null,
                'preview_value' => null,

                // длительность
                'duration' => $duration,

                // управление списком
                'sort'     => $sort,
                'activity' => true,

                // витринные метрики
                'views'        => $status === 'published' ? rand(50, 3000) : 0,
                'likes'        => $status === 'published' ? rand(0, 250) : 0,
                'rating_count' => $status === 'published' ? rand(0, 100) : 0,
                'rating_avg'   => $status === 'published'
                    ? round(rand(350, 500) / 100, 2)
                    : 0,
                'popularity'   => $status === 'published'
                    ? rand(10, 5000)
                    : 0,

                // SEO
                'meta_title'    => $metaTitle,
                'meta_keywords' => $metaKeywords,
                'meta_desc'     => $metaDesc,
            ]);

            if ($lesson->exists && $lesson->trashed()) {
                $lesson->restore();
            }

            $lesson->save();
        }
    }

    private function makeLessonTitles(string $moduleTitle, int $count): array
    {
        $base = [
            'Введение',
            'Цели и план урока',
            'Ключевые понятия',
            'Разбор примеров',
            'Практика',
            'Работа над ошибками',
            'Оптимизация и лайфхаки',
            'Итоги и домашнее задание',
            'Дополнительные материалы',
            'Q&A и частые ошибки',
            'Мини-проект',
            'Разбор кейсов',
        ];

        $prefix = Str::of($moduleTitle)->limit(40);
        $titles = [];

        for ($i = 0; $i < $count; $i++) {
            $raw = $base[$i] ?? ("Урок #" . ($i + 1));
            $titles[] = rand(0, 1) ? "{$raw}" : "{$raw}: {$prefix}";
        }

        return $titles;
    }

    private function uniqueSlugWithinModule(int $moduleId, string $base): string
    {
        $slug = $base !== '' ? $base : 'lesson';
        $i    = 2;

        while (
        Lesson::withTrashed()
            ->where('module_id', $moduleId)
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    private function pickLessonType(int $sort): string
    {
        // Тип нужен только логике длины/текста (НЕ колонка БД)
        $pool = [
            'video','video','video',
            'article','article',
            'quiz',
            'assignment',
            $sort >= 4 ? 'live' : 'video',
        ];

        return $pool[array_rand($pool)];
    }

    private function decideDurationByType(string $type): ?int
    {
        return match ($type) {
            'video'      => rand(6, 18) * 60,   // 6–18 мин
            'live'       => rand(30, 90) * 60,  // 30–90 мин
            'article'    => null,
            'quiz'       => null,
            'assignment' => null,
            default      => rand(5, 12) * 60,
        };
    }

    private function decideStatusDates(Module $module, int $sort): array
    {
        if ($module->status === 'published') {
            // у опубликованного модуля большинство уроков published
            $status = (rand(1, 10) <= 9) ? 'published' : 'draft';
            $publishedAt = $status === 'published'
                ? ($module->published_at?->copy()->addDays(max(0, intdiv($sort, 2))) ?? now()->subDays(rand(0, 60)))
                : null;

            return [$status, $publishedAt];
        }

        // у черновых модулей часть уроков может быть опубликована
        $status = (rand(1, 10) <= 3) ? 'published' : 'draft';
        $publishedAt = $status === 'published' ? now()->subDays(rand(0, 30)) : null;

        return [$status, $publishedAt];
    }

    private function makeBody(string $title, string $type): ?string
    {
        $p1 = "В этом уроке «{$title}» мы разберём ключевые темы, выполним практические шаги и закрепим материал.";
        $p2 = "Вы узнаете, как применять полученные знания в реальных задачах и избежите типичных ошибок.";

        $note = match ($type) {
            'video' => "\n\n> Видеоурок снабжён конспектом и тайм-кодами.",
            'live'  => "\n\n> Это запись живой сессии с ответами на вопросы.",
            default => "",
        };

        // Для quiz/assignment тело можно оставить пустым
        return in_array($type, ['quiz', 'assignment'], true)
            ? null
            : "{$p1}\n\n{$p2}{$note}";
    }

    private function recalcModuleAggregates(Module $module): void
    {
        $lessons = Lesson::query()
            ->where('module_id', $module->id)
            ->get(['duration']);

        // duration трактуем как секунды и пересчитываем в минуты
        $seconds = (int) $lessons->sum(fn ($l) => (int) ($l->duration ?? 0));
        $minutes = (int) round($seconds / 60);

        // Базовый payload: только количество уроков
        $payload = [
            'lessons_count' => $lessons->count(),
        ];

        // Если в modules есть duration_minutes — пишем туда
        if (Schema::hasColumn('modules', 'duration_minutes')) {
            $payload['duration_minutes'] = $minutes;
        }
        // Если в modules есть duration — пишем туда
        elseif (Schema::hasColumn('modules', 'duration')) {
            $payload['duration'] = $minutes;
        }

        $module->forceFill($payload)->save();
    }

}
