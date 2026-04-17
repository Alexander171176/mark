<?php

namespace Database\Seeders;

use App\Models\Admin\Blog\Rubric\Rubric;
use Illuminate\Database\Seeder;

class RubricSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1; // Александр Косолапов

        $defaultIcon = '<svg class="w-4 h-4 fill-current text-slate-400 shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24"><path d="M23,9h-0.414C21.549,5.32,18.681,2.451,15,1.414V1c0-0.552-0.447-1-1-1h-4C9.447,0,9,0.448,9,1v0.414 C5.319,2.451,2.451,5.32,1.414,9H1c-0.553,0-1,0.448-1,1v4c0,0.552,0.447,1,1,1h0.414C2.451,18.68,5.319,21.549,9,22.586V23 c0,0.552,0.447,1,1,1h4c0.553,0,1-0.448,1-1v-0.414c3.681-1.037,6.549-3.906,7.586-7.586H23c0.553,0,1-0.448,1-1v-4 C24,9.448,23.553,9,23,9z M12,16c-2.206,0-4-1.794-4-4s1.794-4,4-4s4,1.794,4,4S14.206,16,12,16z M20.482,9h-3.294 C16.662,8.093,15.907,7.338,15,6.812V3.518C17.567,4.42,19.58,6.433,20.482,9z M9,3.518v3.294C8.093,7.338,7.338,8.093,6.812,9 H3.518C4.42,6.433,6.433,4.42,9,3.518z M3.518,15h3.294C7.338,15.907,8.093,16.662,9,17.188v3.294C6.433,19.58,4.42,17.567,3.518,15 z M15,20.482v-3.294c0.907-0.526,1.662-1.282,2.188-2.188h3.294C19.58,17.567,17.567,19.58,15,20.482z"></path></svg>';

        $now = now();

        /**
         * Структура:
         * locale => parents[]
         * parent: title, url, children[]
         * child: title, url, grandchildren[]
         * grandchild: title, url
         */
        $tree = [
            'ru' => [
                [
                    'title' => 'Разработка',
                    'url' => 'razrabotka',
                    'children' => [
                        [
                            'title' => 'Backend',
                            'url' => 'backend',
                            'grandchildren' => [
                                ['title' => 'Laravel', 'url' => 'laravel'],
                                ['title' => 'PHP', 'url' => 'php'],
                                ['title' => 'Архитектура', 'url' => 'arhitektura'],
                            ],
                        ],
                        [
                            'title' => 'Frontend',
                            'url' => 'frontend',
                            'grandchildren' => [
                                ['title' => 'JavaScript', 'url' => 'javascript'],
                                ['title' => 'Vue.js', 'url' => 'vuejs'],
                                ['title' => 'Tailwind CSS', 'url' => 'tailwindcss'],
                            ],
                        ],
                        [
                            'title' => 'Мобильная разработка',
                            'url' => 'mobile-dev',
                            'grandchildren' => [
                                ['title' => 'Android', 'url' => 'android'],
                                ['title' => 'iOS', 'url' => 'ios'],
                                ['title' => 'Flutter', 'url' => 'flutter'],
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'DevOps и инфраструктура',
                    'url' => 'devops-infra',
                    'children' => [
                        [
                            'title' => 'Контейнеры',
                            'url' => 'containers',
                            'grandchildren' => [
                                ['title' => 'Docker', 'url' => 'docker'],
                                ['title' => 'Docker Compose', 'url' => 'docker-compose'],
                                ['title' => 'Kubernetes', 'url' => 'kubernetes'],
                            ],
                        ],
                        [
                            'title' => 'CI/CD',
                            'url' => 'ci-cd',
                            'grandchildren' => [
                                ['title' => 'GitHub Actions', 'url' => 'github-actions'],
                                ['title' => 'GitLab CI', 'url' => 'gitlab-ci'],
                                ['title' => 'Deploy', 'url' => 'deploy'],
                            ],
                        ],
                        [
                            'title' => 'Серверы',
                            'url' => 'servers',
                            'grandchildren' => [
                                ['title' => 'Linux', 'url' => 'linux'],
                                ['title' => 'Nginx', 'url' => 'nginx'],
                                ['title' => 'Мониторинг', 'url' => 'monitoring'],
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Данные и аналитика',
                    'url' => 'data-analytics',
                    'children' => [
                        [
                            'title' => 'Базы данных',
                            'url' => 'databases',
                            'grandchildren' => [
                                ['title' => 'MySQL', 'url' => 'mysql'],
                                ['title' => 'PostgreSQL', 'url' => 'postgresql'],
                                ['title' => 'Оптимизация', 'url' => 'optimization'],
                            ],
                        ],
                        [
                            'title' => 'Data Engineering',
                            'url' => 'data-engineering',
                            'grandchildren' => [
                                ['title' => 'ETL/ELT', 'url' => 'etl-elt'],
                                ['title' => 'DWH', 'url' => 'dwh'],
                                ['title' => 'Airflow', 'url' => 'airflow'],
                            ],
                        ],
                        [
                            'title' => 'AI/ML',
                            'url' => 'ai-ml',
                            'grandchildren' => [
                                ['title' => 'NLP', 'url' => 'nlp'],
                                ['title' => 'Рекомендации', 'url' => 'recsys'],
                                ['title' => 'MLOps', 'url' => 'mlops'],
                            ],
                        ],
                    ],
                ],
            ],

            'en' => [
                [
                    'title' => 'Development',
                    'url' => 'development',
                    'children' => [
                        [
                            'title' => 'Backend',
                            'url' => 'backend',
                            'grandchildren' => [
                                ['title' => 'Laravel', 'url' => 'laravel'],
                                ['title' => 'PHP', 'url' => 'php'],
                                ['title' => 'Architecture', 'url' => 'architecture'],
                            ],
                        ],
                        [
                            'title' => 'Frontend',
                            'url' => 'frontend',
                            'grandchildren' => [
                                ['title' => 'JavaScript', 'url' => 'javascript'],
                                ['title' => 'Vue.js', 'url' => 'vuejs'],
                                ['title' => 'Tailwind CSS', 'url' => 'tailwindcss'],
                            ],
                        ],
                        [
                            'title' => 'Mobile Development',
                            'url' => 'mobile-development',
                            'grandchildren' => [
                                ['title' => 'Android', 'url' => 'android'],
                                ['title' => 'iOS', 'url' => 'ios'],
                                ['title' => 'Flutter', 'url' => 'flutter'],
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'DevOps & Infrastructure',
                    'url' => 'devops-infrastructure',
                    'children' => [
                        [
                            'title' => 'Containers',
                            'url' => 'containers',
                            'grandchildren' => [
                                ['title' => 'Docker', 'url' => 'docker'],
                                ['title' => 'Docker Compose', 'url' => 'docker-compose'],
                                ['title' => 'Kubernetes', 'url' => 'kubernetes'],
                            ],
                        ],
                        [
                            'title' => 'CI/CD',
                            'url' => 'ci-cd',
                            'grandchildren' => [
                                ['title' => 'GitHub Actions', 'url' => 'github-actions'],
                                ['title' => 'GitLab CI', 'url' => 'gitlab-ci'],
                                ['title' => 'Deploy', 'url' => 'deploy'],
                            ],
                        ],
                        [
                            'title' => 'Servers',
                            'url' => 'servers',
                            'grandchildren' => [
                                ['title' => 'Linux', 'url' => 'linux'],
                                ['title' => 'Nginx', 'url' => 'nginx'],
                                ['title' => 'Monitoring', 'url' => 'monitoring'],
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Data & Analytics',
                    'url' => 'data-analytics',
                    'children' => [
                        [
                            'title' => 'Databases',
                            'url' => 'databases',
                            'grandchildren' => [
                                ['title' => 'MySQL', 'url' => 'mysql'],
                                ['title' => 'PostgreSQL', 'url' => 'postgresql'],
                                ['title' => 'Optimization', 'url' => 'optimization'],
                            ],
                        ],
                        [
                            'title' => 'Data Engineering',
                            'url' => 'data-engineering',
                            'grandchildren' => [
                                ['title' => 'ETL/ELT', 'url' => 'etl-elt'],
                                ['title' => 'DWH', 'url' => 'dwh'],
                                ['title' => 'Airflow', 'url' => 'airflow'],
                            ],
                        ],
                        [
                            'title' => 'AI/ML',
                            'url' => 'ai-ml',
                            'grandchildren' => [
                                ['title' => 'NLP', 'url' => 'nlp'],
                                ['title' => 'Recommendations', 'url' => 'recsys'],
                                ['title' => 'MLOps', 'url' => 'mlops'],
                            ],
                        ],
                    ],
                ],
            ],

            'kk' => [
                [
                    'title' => 'Әзірлеу',
                    'url' => 'azirleu',
                    'children' => [
                        [
                            'title' => 'Backend әзірлеу',
                            'url' => 'backend-azirleu',
                            'grandchildren' => [
                                ['title' => 'Laravel', 'url' => 'laravel'],
                                ['title' => 'PHP', 'url' => 'php'],
                                ['title' => 'Архитектура', 'url' => 'arhitektura'],
                            ],
                        ],
                        [
                            'title' => 'Frontend әзірлеу',
                            'url' => 'frontend-azirleu',
                            'grandchildren' => [
                                ['title' => 'JavaScript', 'url' => 'javascript'],
                                ['title' => 'Vue.js', 'url' => 'vuejs'],
                                ['title' => 'Tailwind CSS', 'url' => 'tailwindcss'],
                            ],
                        ],
                        [
                            'title' => 'Мобильді әзірлеу',
                            'url' => 'mobile-azirleu',
                            'grandchildren' => [
                                ['title' => 'Android', 'url' => 'android'],
                                ['title' => 'iOS', 'url' => 'ios'],
                                ['title' => 'Flutter', 'url' => 'flutter'],
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'DevOps және инфрақұрылым',
                    'url' => 'devops-infra',
                    'children' => [
                        [
                            'title' => 'Контейнерлеу',
                            'url' => 'containerleu',
                            'grandchildren' => [
                                ['title' => 'Docker', 'url' => 'docker'],
                                ['title' => 'Docker Compose', 'url' => 'docker-compose'],
                                ['title' => 'Kubernetes', 'url' => 'kubernetes'],
                            ],
                        ],
                        [
                            'title' => 'CI/CD',
                            'url' => 'ci-cd',
                            'grandchildren' => [
                                ['title' => 'GitHub Actions', 'url' => 'github-actions'],
                                ['title' => 'GitLab CI', 'url' => 'gitlab-ci'],
                                ['title' => 'Deploy', 'url' => 'deploy'],
                            ],
                        ],
                        [
                            'title' => 'Серверлер',
                            'url' => 'serverler',
                            'grandchildren' => [
                                ['title' => 'Linux', 'url' => 'linux'],
                                ['title' => 'Nginx', 'url' => 'nginx'],
                                ['title' => 'Мониторинг', 'url' => 'monitoring'],
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Деректер және талдау',
                    'url' => 'derecter-talday',
                    'children' => [
                        [
                            'title' => 'Дерекқорлар',
                            'url' => 'derekkorlar',
                            'grandchildren' => [
                                ['title' => 'MySQL', 'url' => 'mysql'],
                                ['title' => 'PostgreSQL', 'url' => 'postgresql'],
                                ['title' => 'Оңтайландыру',
                                    'url' => 'optimization'],
                            ],
                        ],
                        [
                            'title' => 'Data Engineering',
                            'url' => 'data-engineering',
                            'grandchildren' => [
                                ['title' => 'ETL/ELT', 'url' => 'etl-elt'],
                                ['title' => 'DWH', 'url' => 'dwh'],
                                ['title' => 'Airflow', 'url' => 'airflow'],
                            ],
                        ],
                        [
                            'title' => 'AI/ML',
                            'url' => 'ai-ml',
                            'grandchildren' => [
                                ['title' => 'NLP', 'url' => 'nlp'],
                                ['title' => 'Ұсыным жүйелері', 'url' => 'recsys'],
                                ['title' => 'MLOps', 'url' => 'mlops'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($tree as $locale => $parents) {
            $parentSort = 10;

            foreach ($parents as $p) {
                $parent = $this->upsertRubric([
                    'user_id' => $userId,
                    'locale' => $locale,
                    'title' => $p['title'],
                    'url' => $p['url'],
                    'parent_id' => null,
                    'level' => 1,
                    'sort' => $parentSort,
                    'icon' => $defaultIcon,
                    'now' => $now,
                ]);

                $childSort = 10;

                foreach ($p['children'] as $c) {
                    $child = $this->upsertRubric([
                        'user_id' => $userId,
                        'locale' => $locale,
                        'title' => $c['title'],
                        'url' => $c['url'],
                        'parent_id' => $parent->id,
                        'level' => 2,
                        'sort' => $childSort,
                        'icon' => $defaultIcon,
                        'now' => $now,
                    ]);

                    $grandSort = 10;

                    foreach ($c['grandchildren'] as $g) {
                        $this->upsertRubric([
                            'user_id' => $userId,
                            'locale' => $locale,
                            'title' => $g['title'],
                            'url' => $g['url'],
                            'parent_id' => $child->id,
                            'level' => 3,
                            'sort' => $grandSort,
                            'icon' => $defaultIcon,
                            'now' => $now,
                        ]);

                        $grandSort += 10;
                    }

                    $childSort += 10;
                }

                $parentSort += 10;
            }
        }
    }

    private function upsertRubric(array $data): Rubric
    {
        $title = $data['title'];
        $locale = $data['locale'];

        $short = match ($locale) {
            'ru' => "Рубрика про {$title}: практики, примеры и разборы.",
            'kk' => "{$title} туралы: тәжірибе, мысалдар және талдау.",
            default => "About {$title}: practices, examples, and breakdowns.",
        };

        $description = match ($locale) {
            'ru' => "Материалы по теме «{$title}»: статьи, рекомендации, подходы, частые ошибки и рабочие решения.",
            'kk' => "«{$title}» тақырыбы бойынша материалдар: мақалалар, ұсыныстар, тәсілдер, қателер және жұмыс істейтін шешімдер.",
            default => "Materials on “{$title}”: articles, recommendations, approaches, common pitfalls, and practical solutions.",
        };

        $metaTitle = match ($locale) {
            'ru' => "{$title} — статьи и гайды",
            'kk' => "{$title} — мақалалар мен гидтер",
            default => "{$title} — articles & guides",
        };

        $metaKeywords = match ($locale) {
            'ru' => mb_strtolower("{$title}, программирование, IT, разработка, гайды"),
            'kk' => mb_strtolower("{$title}, бағдарламалау, IT, әзірлеу, гидтер"),
            default => mb_strtolower("{$title}, programming, IT, development, guides"),
        };

        $metaDesc = match ($locale) {
            'ru' => "Подборка материалов по теме «{$title}»: практические статьи, примеры и решения.",
            'kk' => "«{$title}» тақырыбы бойынша материалдар: тәжірибелік мақалалар, мысалдар және шешімдер.",
            default => "A curated set of materials on “{$title}”: practical articles, examples, and solutions.",
        };

        $payload = [
            'user_id' => $data['user_id'],
            'parent_id' => $data['parent_id'],
            'level' => $data['level'],
            'in_menu' => true,
            'sort' => $data['sort'],
            'activity' => true,

            'moderation_status' => 1,
            'moderated_by' => $data['user_id'],
            'moderated_at' => $data['now'],
            'moderation_note' => null,

            'icon' => $data['icon'],
            'locale' => $locale,
            'title' => $title,
            'url' => $data['url'],

            'subtitle' => null,
            'short' => $short,
            'description' => $description,
            'views' => 0,

            'meta_title' => $metaTitle,
            'meta_keywords' => $metaKeywords,
            'meta_desc' => $metaDesc,

            'created_at' => $data['now'],
            'updated_at' => $data['now'],
        ];

        return Rubric::updateOrCreate(
            [
                'user_id' => $data['user_id'],
                'locale' => $locale,
                'title' => $title,
            ],
            $payload
        );
    }
}
