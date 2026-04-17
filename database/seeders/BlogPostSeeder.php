<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('blog_posts')) {
            $this->command?->warn('Нет таблицы blog_posts — пропускаю BlogPostSeeder.');
            return;
        }

        // Автор (если есть пользователи — возьмём первого)
        $authorId = null;
        if (Schema::hasTable('users')) {
            $authorId = DB::table('users')->orderBy('id')->value('id');
        }

        $now = now();

        // --- Контент для посева ---
        // Формат элемента:
        // ['locale' => 'ru|en', 'title' => '...', 'excerpt' => '...', 'content' => '<p>...</p>', 'status' => 'published|draft', 'days_ago' => 10]
        $posts = [
            // RU
            [
                'locale' => 'ru',
                'title'  => 'Как мы проектируем онлайн-курсы: методология и инструменты',
                'excerpt'=> 'Подход к дизайну курсов, который помогает студентам доходить до результата.',
                'content'=> "<h2>Методология</h2><p>Мы опираемся на принцип «минимального работающего навыка»...</p>",
                'status' => 'published',
                'days_ago' => 20,
                'cover' => 'https://picsum.photos/seed/course-design/1200/630',
            ],
            [
                'locale' => 'ru',
                'title'  => 'Лонгрид: что такое результат обучения и как его измерять',
                'excerpt'=> 'Разбираем формулировки результатов обучения и метрики эффективности.',
                'content'=> "<p>Результаты обучения — это наблюдаемое поведение и артефакты...</p>",
                'status' => 'published',
                'days_ago' => 14,
                'cover' => 'https://picsum.photos/seed/learning-outcomes/1200/630',
            ],
            [
                'locale' => 'ru',
                'title'  => 'Новости платформы: редактор уроков, чек-листы и новые шаблоны',
                'excerpt'=> 'Большое обновление конструктора и библиотека шаблонов.',
                'content'=> "<ul><li>Редактор уроков 2.0</li><li>Шаблоны заданий</li><li>Чек-листы</li></ul>",
                'status' => 'published',
                'days_ago' => 7,
                'cover' => 'https://picsum.photos/seed/release-notes/1200/630',
            ],
            [
                'locale' => 'ru',
                'title'  => 'Что почитать разработчику авторских курсов: топ-10 материалов',
                'excerpt'=> 'Собрали ссылки на книги, блоги и исследования.',
                'content'=> "<p>Подборка источников, которые помогают системно подойти к разработке...</p>",
                'status' => 'draft',
                'days_ago' => 2,
                'cover' => null,
            ],

            // EN
            [
                'locale' => 'en',
                'title'  => 'How We Design Online Courses: Principles & Tooling',
                'excerpt'=> 'A practical framework that helps learners reach outcomes.',
                'content'=> "<h2>Framework</h2><p>We rely on the Minimum Viable Skill approach...</p>",
                'status' => 'published',
                'days_ago' => 21,
                'cover' => 'https://picsum.photos/seed/en-course-design/1200/630',
            ],
            [
                'locale' => 'en',
                'title'  => 'Longread: Learning Outcomes and How to Measure Them',
                'excerpt'=> 'From outcome statements to effectiveness metrics.',
                'content'=> "<p>Learning outcomes are observable behaviors and artifacts...</p>",
                'status' => 'published',
                'days_ago' => 12,
                'cover' => 'https://picsum.photos/seed/en-outcomes/1200/630',
            ],
            [
                'locale' => 'en',
                'title'  => 'Platform Updates: Lesson Editor, Checklists & New Templates',
                'excerpt'=> 'A major release with fresh authoring capabilities.',
                'content'=> "<ul><li>Lesson Editor 2.0</li><li>Assignment templates</li><li>Checklists</li></ul>",
                'status' => 'published',
                'days_ago' => 5,
                'cover' => 'https://picsum.photos/seed/en-release-notes/1200/630',
            ],
            [
                'locale' => 'en',
                'title'  => 'Reading List for Course Authors: Top-10 Resources',
                'excerpt'=> 'Books, blogs and research we recommend.',
                'content'=> "<p>A curated collection to help you build better learning experiences...</p>",
                'status' => 'draft',
                'days_ago' => 1,
                'cover' => null,
            ],
        ];

        // Подтянем уже существующие слаги для быстрого поиска
        $existingSlugs = DB::table('blog_posts')->pluck('slug')->all();
        $existing = array_fill_keys($existingSlugs, true);

        $rows = [];
        $created = 0;
        $updated = 0;

        foreach ($posts as $idx => $p) {
            $baseSlug = $this->buildBaseSlug($p['locale'], $p['title']);
            $slug     = $this->uniqueSlug($baseSlug, $existing);

            $published = ($p['status'] === 'published');
            $publishedAt = $published ? now()->copy()->subDays($p['days_ago'] ?? 0) : null;

            $readingTime = $this->estimateReadingTime($p['content']);

            $nowTs = now();

            // Проверим — есть ли запись с таким slug
            $exists = DB::table('blog_posts')->where('slug', $slug)->exists();

            $payload = [
                'author_id'      => $authorId,
                'title'          => $p['title'],
                'slug'           => $slug,
                'excerpt'        => $p['excerpt'],
                'content'        => $p['content'],
                'status'         => $p['status'],
                'activity'       => true,
                'published_at'   => $publishedAt,
                'cover_image_url'=> $p['cover'],
                'reading_time'   => $readingTime,
                'meta'           => json_encode([
                    'seeded'   => true,
                    'seed_run' => $nowTs->toDateTimeString(),
                    'tags'     => $published ? ['release', 'methodology'] : ['draft'],
                ], JSON_UNESCAPED_UNICODE),
                'locale'         => $p['locale'],
                'updated_at'     => $nowTs,
            ];

            if ($exists) {
                // Обновим
                DB::table('blog_posts')->where('slug', $slug)->update($payload);
                $updated++;
            } else {
                // Создадим
                $payload['created_at'] = $nowTs;
                DB::table('blog_posts')->insert($payload);
                $created++;
            }
        }

        $this->command?->info("Blog posts upserted: created {$created}, updated {$updated}.");
    }

    private function buildBaseSlug(?string $locale, string $title): string
    {
        // Так как slug глобально уникален, включим префикс локали
        $prefix = $locale ? Str::slug($locale) : null;
        $base   = Str::slug($title);
        return $prefix ? "{$prefix}/{$base}" : $base;
    }

    private function uniqueSlug(string $base, array &$existing): string
    {
        $slug = $base;
        $i = 2;
        while (isset($existing[$slug]) || DB::table('blog_posts')->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        $existing[$slug] = true;
        return $slug;
    }

    private function estimateReadingTime(?string $html): int
    {
        if (!$html) return 0;
        // грубо: удалим теги и посчитаем слова
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
        if ($text === '') return 0;
        $words = str_word_count($text, 0, 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюя');
        // Средняя скорость чтения ~200 слов/мин
        return max(1, (int)ceil($words / 200));
    }
}
