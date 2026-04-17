<?php

namespace Database\Seeders;

use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Hashtag\Hashtag;
use App\Models\Admin\School\InstructorProfile\InstructorProfile;
use App\Models\Admin\School\LearningCategory\LearningCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('courses') || !Schema::hasTable('instructor_profiles')) {
            $this->command?->warn('courses / instructor_profiles table is missing — skipping CourseSeeder.');
            return;
        }

        $instructorProfiles = InstructorProfile::query()
            ->inRandomOrder()
            ->get(['id', 'title']);

        if ($instructorProfiles->isEmpty()) {
            $this->command?->warn('No instructor profiles found — run InstructorProfileSeeder first.');
            return;
        }

        // Базовый каталог «основ» курсов (RU-тексты, но ок для демо)
        $catalog = [
            'Полный курс PHP для начинающих',
            'Laravel с нуля до PRO',
            'Современный JavaScript (ES6+) от А до Я',
            'React и Redux: создание продакшен-приложений',
            'TypeScript на практике',
            'Node.js: REST API и микросервисы',
            'Next.js: SSR/SSG и оптимизация',
            'HTML & CSS: профессиональная вёрстка',
            'Tailwind CSS — быстрый дизайн интерфейсов',
            'Python для анализа данных',
            'SQL для аналитиков: PostgreSQL/MySQL',
            'Docker и контейнеризация',
            'UI/UX дизайн в Figma',
            'SEO: техническая оптимизация и контент',
            'Английский для IT-специалистов',
        ];

        // Возможные статусы/доступность
        $statusPool       = ['draft', 'published', 'archived'];
        $availabilityPool = ['public', 'unlisted', 'private'];
        $locales          = ['ru', 'en', 'kk'];

        // Категории по локалям (если таблица есть)
        $learningCategoryIdsByLocale = [];

        if (Schema::hasTable('learning_categories')) {
            $learningCategoryIdsByLocale = LearningCategory::query()
                ->get(['id', 'locale'])
                ->groupBy('locale')
                ->map(fn ($items) => $items->pluck('id')->all())
                ->toArray();
        }

        // Хештеги (если есть таблица) — ключ: slug, значение: id
        $hashtagBySlug = Schema::hasTable('hashtags')
            ? Hashtag::query()->pluck('id', 'slug')->all()
            : [];

        /**
         * Ключевые слова заголовка → массив slug'ов хештегов.
         *
         * Здесь используем только реально существующие слуги из HashtagSeeder:
         * html, css, javascript, laravel, vuejs, tailwindcss
         */
        $keywordMap = [
            // PHP-курсы помечаем Laravel + HTML
            'php'        => ['laravel', 'html'],

            'laravel'    => ['laravel', 'html', 'css'],

            // JS-стек (общий)
            'javascript' => ['javascript', 'vuejs'],
            'react'      => ['javascript'],          // отдельного react-тега нет
            'vue'        => ['vuejs', 'javascript'],
            'node'       => ['javascript'],          // nodejs не заводим как тег
            'typescript' => ['javascript'],

            // Вёрстка
            'html'       => ['html', 'css'],
            'css'        => ['css', 'tailwindcss'],
            'tailwind'   => ['tailwindcss', 'css'],

            // Остальные ключевые слова (python/sql/docker/figma/seo/aws/english)
            // пока без маппинга на хештеги — тегов под них нет.
        ];

        DB::transaction(function () use (
            $catalog,
            $instructorProfiles,
            $statusPool,
            $availabilityPool,
            $locales,
            $learningCategoryIdsByLocale,
            $hashtagBySlug,
            $keywordMap
        ) {
            $titles = collect($catalog);
            $now    = now();

            // Если уже есть курсы — продолжаем сортировку с максимального sort
            $sortCounter = (int) (Course::max('sort') ?? 0) + 1;

            foreach ($titles as $baseTitle) {
                foreach ($locales as $locale) {
                    $instructorProfileId = (int) $instructorProfiles->random()->id;

                    // Базовый slug из заголовка
                    $baseSlug = Str::slug($baseTitle);

                    // Для RU — чистый; для EN/KK — с суффиксом локали
                    $slug = $locale === 'ru'
                        ? $baseSlug
                        : "{$baseSlug}-{$locale}";

                    /** @var Course $course */
                    $course = Course::withTrashed()->firstOrNew([
                        'slug' => $slug,
                    ]);

                    // Случайные атрибуты витрины
                    $status       = collect($statusPool)->random();
                    $availability = collect($availabilityPool)->random();

                    // Флаги витрины
                    $isNew  = mt_rand(1, 10) <= 3; // ~30%
                    $isHit  = mt_rand(1, 10) <= 2; // ~20%
                    $isSale = mt_rand(1, 10) <= 2; // ~20%

                    $left  = mt_rand(1, 10) <= 2;  // ~20%
                    $main  = mt_rand(1, 10) <= 2;  // ~20%
                    $right = mt_rand(1, 10) <= 2;  // ~20%

                    $ratingAvg   = round(mt_rand(380, 500) / 100, 2); // 3.80..5.00
                    $ratingCount = mt_rand(0, 250);
                    $students    = mt_rand(20, 5000);
                    $popularity  = $students + (int)($ratingAvg * 100) + $ratingCount;
                    $views       = mt_rand(50, 10000);
                    $likes       = mt_rand(5, 2000);

                    $publishedAt = $status === 'published'
                        ? $now->copy()->subDays(mt_rand(0, 120))
                        : null;

                    // Краткое описание и полное описание
                    $subtitle    = $this->makeSubtitle($baseTitle);
                    $description = $this->makeDescription($baseTitle);
                    $short       = Str::limit(strip_tags($description), 240);

                    // SEO
                    $metaTitle = Str::limit($baseTitle, 160);
                    $metaDesc  = Str::limit(
                        $short !== '' ? $short : strip_tags($description),
                        255
                    );
                    $metaKeywords = Str::limit(
                        collect(preg_split('/\s+/u', mb_strtolower($baseTitle)))
                            ->filter(fn ($word) => $word && mb_strlen($word) >= 3)
                            ->unique()
                            ->implode(', '),
                        255
                    );

                    // Заполнение полей
                    $course->fill([
                        'instructor_profile_id' => $instructorProfileId,
                        'locale'                => $locale,

                        // Для демо: один и тот же текст на всех локалях
                        'title'       => $baseTitle,
                        'slug'        => $slug,
                        'subtitle'    => $subtitle,
                        'short'       => $short,
                        'description' => $description,

                        'level'      => collect(['beginner', 'intermediate', 'advanced'])->random(),
                        'difficulty' => mt_rand(1, 5),
                        'duration'   => mt_rand(90, 2400), // от 1.5ч до ~40ч

                        'availability' => $availability,
                        'status'       => $status,
                        'published_at' => $publishedAt,

                        'is_new'  => $isNew,
                        'is_hit'  => $isHit,
                        'is_sale' => $isSale,
                        'left'    => $left,
                        'main'    => $main,
                        'right'   => $right,

                        'meta_title'    => $metaTitle,
                        'meta_keywords' => $metaKeywords,
                        'meta_desc'     => $metaDesc,

                        'activity' => true,
                        // если запись новая — даём новый sort, если существующая — не трогаем sort
                        'sort'     => $course->exists ? $course->sort : $sortCounter++,

                        'rating_avg'     => $ratingAvg,
                        'rating_count'   => $ratingCount,
                        'students_count' => $students,
                        'popularity'     => $popularity,
                        'views'          => $views,
                        'likes'          => $likes,
                    ]);

                    if ($course->exists && $course->trashed()) {
                        $course->restore();
                    }

                    $course->save();

                    // ---------------- Категории (по локали) ----------------
                    $learningCategoryIds = $learningCategoryIdsByLocale[$locale] ?? [];

                    if (!empty($learningCategoryIds)) {
                        $toAttachCategories = collect($learningCategoryIds)
                            ->shuffle()
                            ->take(mt_rand(1, min(3, count($learningCategoryIds))))
                            ->all();

                        // Для демо просто пересобираем категории на каждый сид
                        $course->learningCategories()->sync($toAttachCategories);
                    }

                    // ---------------- Хэштеги (2–4 по ключевым словам / случайные) ----------------
                    if (!empty($hashtagBySlug) && method_exists($course, 'hashtags')) {
                        $picked = [];

                        $titleLower = mb_strtolower((string) $baseTitle);

                        // 1) Подбираем по ключевым словам -> IDS из $hashtagBySlug
                        foreach ($keywordMap as $needle => $slugs) {
                            if (Str::contains($titleLower, $needle)) {
                                foreach ($slugs as $slugKey) {
                                    if (isset($hashtagBySlug[$slugKey])) {
                                        $id = (int) $hashtagBySlug[$slugKey];
                                        if ($id > 0) {
                                            $picked[$id] = true;
                                        }
                                    }
                                }
                            }
                        }

                        // 2) Если ничего не подобрали — добавим случайные 2–4 ID
                        if (empty($picked)) {
                            $allIds = array_values($hashtagBySlug); // просто массив ID
                            $count  = min(mt_rand(2, 4), count($allIds));

                            if ($count > 0) {
                                $randomIds = collect($allIds)->random($count);

                                foreach ((array) $randomIds as $id) {
                                    $id = (int) $id;
                                    if ($id > 0) {
                                        $picked[$id] = true;
                                    }
                                }
                            }
                        }

                        // 3) Навешиваем хэштеги — только валидные ID > 0
                        if (!empty($picked)) {
                            $idsToAttach = array_values(array_unique(
                                array_filter(array_keys($picked), fn ($id) => (int) $id > 0)
                            ));

                            if (!empty($idsToAttach)) {
                                // Для идемпотентности — пересобираем список хэштегов
                                $course->hashtags()->sync($idsToAttach);
                            }
                        } else {
                            // Если ничего не подобрано — просто очищаем
                            $course->hashtags()->detach();
                        }
                    }
                }
            }
        });

        /**
         * Опционально: посеять рекомендованные курсы (course_related)
         * Чтобы не разрасталась связка — используем sync.
         */
        if (Schema::hasTable('course_related')) {
            $allCourses = Course::all(['id']);

            foreach ($allCourses as $course) {
                // 0–3 рекомендованных курса, не включая сам себя
                $relatedIds = $allCourses
                    ->where('id', '!=', $course->id)
                    ->pluck('id')
                    ->shuffle()
                    ->take(mt_rand(0, 3))
                    ->all();

                if (!empty($relatedIds)) {
                    $course->relatedCourses()->sync($relatedIds);
                } else {
                    $course->relatedCourses()->detach();
                }
            }
        }
    }

    private function makeSubtitle(string $title): string
    {
        $lower = mb_strtolower($title);

        return match (true) {
            Str::contains($lower, 'laravel')    => 'Практика от CRUD до продакшена',
            Str::contains($lower, 'javascript') => 'Современный JS без магии',
            Str::contains($lower, 'react')      => 'Hooks, Redux Toolkit и best practices',
            Str::contains($lower, 'vue')        => 'Composition API, Pinia и экосистема',
            Str::contains($lower, 'python')     => 'NumPy/Pandas/Matplotlib на практике',
            Str::contains($lower, 'docker')     => 'От образов до продакшен-оркестрации',
            Str::contains($lower, 'kubernetes') => 'Deploy и observability для dev',
            default                             => 'Полный практический курс',
        };
    }

    private function makeDescription(string $title): string
    {
        return <<<MD
**{$title}** — интенсивный практический курс с заданиями, квизами и проектами.
Вы разберётесь в ключевых концепциях, соберёте полноценный pet-/prod-проект и получите
навыки, востребованные на рынке. Подходит для самостоятельного обучения и корпоративных программ.
MD;
    }
}
