<?php

namespace Database\Seeders;

use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Tag\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1;
        $now = now();

        // База тегов: ключ => [ru, kk, en]
        // slug оставляем латиницей и одинаковым для всех локалей (удобно для URL)
        $base = [
            'html'            => ['ru' => 'HTML',            'kk' => 'HTML',             'en' => 'HTML'],
            'css'             => ['ru' => 'CSS',             'kk' => 'CSS',              'en' => 'CSS'],
            'javascript'      => ['ru' => 'JavaScript',      'kk' => 'JavaScript',       'en' => 'JavaScript'],
            'vuejs'           => ['ru' => 'Vue.js',          'kk' => 'Vue.js',           'en' => 'Vue.js'],
            'laravel'         => ['ru' => 'Laravel',         'kk' => 'Laravel',          'en' => 'Laravel'],
            'tailwind-css'    => ['ru' => 'Tailwind CSS',    'kk' => 'Tailwind CSS',     'en' => 'Tailwind CSS'],
            'web-development' => ['ru' => 'Web-разработка',  'kk' => 'Web-әзірлеу',      'en' => 'Web Development'],
            'seo'             => ['ru' => 'SEO',             'kk' => 'SEO',              'en' => 'SEO'],
            'ui-ux'           => ['ru' => 'UI/UX',           'kk' => 'UI/UX',            'en' => 'UI/UX'],
            'programming'     => ['ru' => 'Программирование','kk' => 'Бағдарламалау',    'en' => 'Programming'],
        ];

        $locales = ['ru', 'kk', 'en'];

        // 1) Создаём/обновляем теги
        foreach ($locales as $locale) {
            $sort = 1;

            foreach ($base as $slug => $names) {
                $name = $names[$locale];

                Tag::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'locale'  => $locale,
                        'slug'    => $slug,
                    ],
                    [
                        'sort' => $sort++,
                        'activity' => true,

                        'moderation_status' => 1,
                        'moderated_by' => $userId,
                        'moderated_at' => $now,
                        'moderation_note' => null,

                        'icon' => null,

                        'name' => $name,
                        'subtitle' => null,
                        'short' => $this->makeShort($locale, $name),
                        'description' => $this->makeDescription($locale, $name),
                        'views' => 0,

                        'meta_title' => $this->makeMetaTitle($locale, $name),
                        'meta_keywords' => $this->makeMetaKeywords($locale, $name),
                        'meta_desc' => $this->makeMetaDesc($locale, $name),

                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }

        // 2) Привязка тегов к статьям
        //    - только в рамках одной локали
        //    - стабильно: 3 тега на статью, выбор зависит от article_id
        foreach ($locales as $locale) {
            $tagIds = Tag::query()
                ->where('user_id', $userId)
                ->where('locale', $locale)
                ->orderBy('sort')
                ->pluck('id')
                ->values();

            if ($tagIds->isEmpty()) {
                continue;
            }

            Article::query()
                ->where('user_id', $userId)
                ->where('locale', $locale)
                ->select(['id'])
                ->chunkById(200, function ($articles) use ($tagIds) {
                    $count = $tagIds->count();

                    foreach ($articles as $article) {
                        // псевдо-стабильный выбор 3 тегов по article_id
                        $start = $article->id % $count;

                        $selected = [
                            $tagIds[$start],
                            $tagIds[($start + 1) % $count],
                            $tagIds[($start + 2) % $count],
                        ];

                        // перезаписываем набор тегов у статьи, чтобы при повторном запуске было предсказуемо
                        DB::table('article_has_tag')
                            ->where('article_id', $article->id)
                            ->delete();

                        DB::table('article_has_tag')->insert(array_map(fn ($tagId) => [
                            'article_id' => $article->id,
                            'tag_id' => $tagId,
                        ], $selected));
                    }
                });
        }
    }

    private function makeShort(string $locale, string $name): string
    {
        return match ($locale) {
            'ru' => "Коротко про {$name}: практические заметки и примеры.",
            'kk' => "{$name} туралы қысқаша: практикалық жазбалар мен мысалдар.",
            default => "Quick notes about {$name}: practical tips and examples.",
        };
    }

    private function makeDescription(string $locale, string $name): string
    {
        return match ($locale) {
            'ru' => "Подборка материалов по тегу {$name}. Используется для тестирования и наполнения контента.",
            'kk' => "{$name} тегі бойынша материалдар жинағы. Тестілеу және контент толтыру үшін.",
            default => "A collection of posts tagged {$name}. Used for testing and content seeding.",
        };
    }

    private function makeMetaTitle(string $locale, string $name): string
    {
        return match ($locale) {
            'ru' => "{$name} — статьи и материалы",
            'kk' => "{$name} — мақалалар мен материалдар",
            default => "{$name} — articles and resources",
        };
    }

    private function makeMetaKeywords(string $locale, string $name): string
    {
        return match ($locale) {
            'ru' => mb_strtolower("{$name}, IT, разработка, программирование, статьи"),
            'kk' => mb_strtolower("{$name}, IT, әзірлеу, бағдарламалау, мақалалар"),
            default => mb_strtolower("{$name}, IT, development, programming, articles"),
        };
    }

    private function makeMetaDesc(string $locale, string $name): string
    {
        return match ($locale) {
            'ru' => "Материалы по теме {$name}: заметки, примеры и практические статьи.",
            'kk' => "{$name} тақырыбы бойынша материалдар: жазбалар, мысалдар және практикалық мақалалар.",
            default => "Resources about {$name}: notes, examples, and practical articles.",
        };
    }
}
