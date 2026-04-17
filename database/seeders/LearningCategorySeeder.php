<?php

namespace Database\Seeders;

use App\Models\Admin\School\LearningCategory\LearningCategory;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LearningCategorySeeder extends Seeder
{
    /**
     * Три дерева (ru, en, kk) + Faker для SEO.
     */
    public function run(): void
    {
        DB::transaction(function () {

            // ---------------- RU ----------------
            $ru = [
                [
                    'name' => 'Разработка',
                    'slug' => 'development',
                    'description' => 'Программирование, веб и DevOps.',
                    'children' => [
                        [
                            'name' => 'Веб-разработка',
                            'slug' => 'web-development',
                            'children' => [
                                ['name' => 'PHP',        'slug' => 'php'],
                                ['name' => 'JavaScript', 'slug' => 'javascript'],
                                ['name' => 'Laravel',    'slug' => 'laravel'],
                                ['name' => 'Vue.js',     'slug' => 'vuejs'],
                            ],
                        ],
                        [
                            'name' => 'DevOps и Облака',
                            'slug' => 'devops-cloud',
                            'children' => [
                                ['name' => 'Docker & Kubernetes', 'slug' => 'docker-kubernetes'],
                                ['name' => 'Linux',               'slug' => 'linux'],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Данные и Аналитика',
                    'slug' => 'data',
                    'description' => 'Data Science и машинное обучение.',
                    'children' => [
                        ['name' => 'Data Science',     'slug' => 'data-science'],
                        ['name' => 'Machine Learning', 'slug' => 'machine-learning'],
                    ],
                ],
                [
                    'name' => 'Дизайн',
                    'slug' => 'design',
                    'children' => [
                        ['name' => 'UI/UX Дизайн', 'slug' => 'ui-ux-design'],
                    ],
                ],
            ];

            // ---------------- EN ----------------
            $en = [
                [
                    'name' => 'Development',
                    'slug' => 'development',
                    'description' => 'Programming, web development and DevOps.',
                    'children' => [
                        [
                            'name' => 'Web Development',
                            'slug' => 'web-development',
                            'children' => [
                                ['name' => 'PHP',        'slug' => 'php'],
                                ['name' => 'JavaScript', 'slug' => 'javascript'],
                                ['name' => 'Laravel',    'slug' => 'laravel'],
                                ['name' => 'Vue.js',     'slug' => 'vuejs'],
                            ],
                        ],
                        [
                            'name' => 'DevOps & Cloud',
                            'slug' => 'devops-cloud',
                            'children' => [
                                ['name' => 'Docker & Kubernetes', 'slug' => 'docker-kubernetes'],
                                ['name' => 'Linux',               'slug' => 'linux'],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Data & Analytics',
                    'slug' => 'data',
                    'description' => 'Data Science and Machine Learning.',
                    'children' => [
                        ['name' => 'Data Science',     'slug' => 'data-science'],
                        ['name' => 'Machine Learning', 'slug' => 'machine-learning'],
                    ],
                ],
                [
                    'name' => 'Design',
                    'slug' => 'design',
                    'children' => [
                        ['name' => 'UI/UX Design', 'slug' => 'ui-ux-design'],
                    ],
                ],
            ];

            // ---------------- KK ----------------
            $kk = [
                [
                    'name' => 'Даму',
                    'slug' => 'development',
                    'description' => 'Бағдарламалау, веб және DevOps.',
                    'children' => [
                        [
                            'name' => 'Веб-даму',
                            'slug' => 'web-development',
                            'children' => [
                                ['name' => 'PHP',        'slug' => 'php'],
                                ['name' => 'JavaScript', 'slug' => 'javascript'],
                                ['name' => 'Laravel',    'slug' => 'laravel'],
                                ['name' => 'Vue.js',     'slug' => 'vuejs'],
                            ],
                        ],
                        [
                            'name' => 'DevOps және бұлт',
                            'slug' => 'devops-cloud',
                            'children' => [
                                ['name' => 'Docker & Kubernetes', 'slug' => 'docker-kubernetes'],
                                ['name' => 'Linux',               'slug' => 'linux'],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Деректер және аналитика',
                    'slug' => 'data',
                    'description' => 'Data Science және Machine Learning.',
                    'children' => [
                        ['name' => 'Data Science',     'slug' => 'data-science'],
                        ['name' => 'Machine Learning', 'slug' => 'machine-learning'],
                    ],
                ],
                [
                    'name' => 'Дизайн',
                    'slug' => 'design',
                    'children' => [
                        ['name' => 'UI/UX дизайн', 'slug' => 'ui-ux-design'],
                    ],
                ],
            ];

            // Засеваем по локалям
            $this->seedTree($ru, 'ru');
            $this->seedTree($en, 'en');
            $this->seedTree($kk, 'kk');
        });
    }

    /**
     * Рекурсивная посадка дерева для заданной локали.
     */
    protected function seedTree(array $nodes, string $locale, ?int $parentId = null): void
    {
        $faker = $this->fakerForLocale($locale);

        foreach (array_values($nodes) as $index => $node) {
            $name        = $node['name'];
            $slug        = $this->makeSlug($locale, $node['slug'] ?? null, $name);
            $activity    = $node['activity'] ?? true;
            $sort        = $node['sort'] ?? $index;

            // Тексты
            $short       = $node['short'] ?? $faker->sentence(8);
            $description = $node['description'] ?? $faker->paragraph(3);

            // SEO через Faker
            $meta = $this->seoFor($faker);

            /** @var LearningCategory $category */
            $category = LearningCategory::withTrashed()->firstOrNew([
                'locale' => $locale,
                'slug'   => $slug,
            ]);

            $category->fill([
                'parent_id'     => $parentId,
                'locale'        => $locale,
                'name'          => $name,
                'slug'          => $slug,
                'short'         => $short,
                'description'   => $description,
                'activity'      => $activity,
                'sort'          => $sort,
                'meta_title'    => $node['meta_title']    ?? $meta['title'],
                'meta_keywords' => $node['meta_keywords'] ?? $meta['keywords'],
                'meta_desc'     => $node['meta_desc']     ?? $meta['desc'],
            ]);

            if ($category->exists && $category->trashed()) {
                $category->restore();
            }

            $category->save();

            if (!empty($node['children']) && is_array($node['children'])) {
                $this->seedTree($node['children'], $locale, $category->id);
            }
        }
    }

    /**
     * Генерация slug внутри локали с проверкой конфликтов.
     */
    protected function makeSlug(string $locale, ?string $slug, string $name): string
    {
        $base = Str::slug($slug ?? $name);

        if (!LearningCategory::where('locale', $locale)->where('slug', $base)->exists()) {
            return $base;
        }

        $i = 2;
        while (LearningCategory::where('locale', $locale)->where('slug', "{$base}-{$i}")->exists()) {
            $i++;
        }
        return "{$base}-{$i}";
    }

    /**
     * Faker для нужной локали.
     */
    protected function fakerForLocale(string $locale)
    {
        return match ($locale) {
            'ru' => Faker::create('ru_RU'),
            'en' => Faker::create('en_US'),
            'kk' => Faker::create('kk_KZ'), // при отсутствии полного словаря вернётся англ. fallback
            default => Faker::create(),
        };
    }

    /**
     * Набор SEO полей (title/keywords/desc) на базе Faker.
     */
    protected function seoFor($faker): array
    {
        return [
            'title'    => $faker->sentence(6),                    // ~60–70 символов
            'keywords' => implode(', ', $faker->words(6)),        // ключевые слова через запятую
            'desc'     => $faker->text(160),                      // meta description ~160 симв.
        ];
        // при желании можно подточить длины/капитализацию
    }
}
