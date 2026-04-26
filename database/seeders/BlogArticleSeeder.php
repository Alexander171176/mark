<?php

namespace Database\Seeders;

use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BlogArticleSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $userId = 1;

            $rubrics = BlogRubric::query()->get()->keyBy('url');
            $tags = BlogTag::query()->get()->keyBy('slug');

            $blueprints = $this->articleBlueprints();
            $sort = 0;
            $dayShift = 0;

            foreach ($rubrics as $rubricUrl => $rubric) {
                $items = $blueprints[$rubricUrl] ?? $this->fallbackBlueprints($rubricUrl);

                foreach ($items as $index => $item) {
                    $publishedAt = Carbon::now()->subDays($dayShift)->toDateString();
                    $showFromAt = Carbon::now()->subDays($dayShift)->startOfDay();
                    $showToAt = Carbon::now()->addMonths(12)->endOfDay();

                    $article = BlogArticle::updateOrCreate(
                        [
                            'url' => $item['slug'],
                        ],
                        [
                            'user_id' => $userId,

                            'sort' => $sort,
                            'activity' => true,

                            'left' => $sort % 7 === 0,
                            'main' => $sort % 9 === 0,
                            'right' => $sort % 5 === 0,

                            'moderation_status' => 1,
                            'moderated_by' => $userId,
                            'moderated_at' => now(),
                            'moderation_note' => null,

                            'img' => null,
                            'published_at' => $publishedAt,
                            'show_from_at' => $showFromAt,
                            'show_to_at' => $showToAt,

                            'views' => rand(35, 350),
                        ]
                    );

                    // Привязка к рубрике
                    $article->rubrics()->syncWithoutDetaching([$rubric->id]);

                    // Привязка тегов
                    $tagIds = collect($item['tags'] ?? [])
                        ->map(fn ($slug) => $tags->get($slug)?->id)
                        ->filter()
                        ->values()
                        ->all();

                    if (!empty($tagIds)) {
                        $article->tags()->syncWithoutDetaching($tagIds);
                    }

                    $sort++;
                    $dayShift++;
                }
            }

            // Простейшие связанные статьи внутри каждой рубрики
            foreach ($rubrics as $rubric) {
                $articles = $rubric->articles()->orderBy('id')->get();

                foreach ($articles as $i => $article) {
                    $related = $articles
                        ->where('id', '!=', $article->id)
                        ->take(3)
                        ->values();

                    $syncData = [];
                    foreach ($related as $rIndex => $relatedArticle) {
                        $syncData[$relatedArticle->id] = ['sort' => $rIndex];
                    }

                    if (!empty($syncData)) {
                        $article->relatedArticles()->syncWithoutDetaching($syncData);
                    }
                }
            }

            app(BlogArticleTranslationSeeder::class)->run();
        });
    }

    protected function articleBlueprints(): array
    {
        return [
            'frontend-development' => [
                [
                    'slug' => 'how-frontend-projects-are-structured',
                    'tags' => ['javascript', 'vue-js', 'tailwind-css'],
                ],
                [
                    'slug' => 'responsive-layout-principles',
                    'tags' => ['javascript', 'tailwind-css'],
                ],
                [
                    'slug' => 'component-driven-ui-development',
                    'tags' => ['vue-js', 'tailwind-css'],
                ],
                [
                    'slug' => 'how-to-connect-frontend-with-api',
                    'tags' => ['javascript', 'api', 'vue-js'],
                ],
            ],
            'backend-development' => [
                [
                    'slug' => 'backend-architecture-for-modern-web-apps',
                    'tags' => ['php', 'laravel', 'api'],
                ],
                [
                    'slug' => 'how-to-design-rest-api-endpoints',
                    'tags' => ['api', 'php', 'laravel'],
                ],
                [
                    'slug' => 'working-with-business-logic-in-services',
                    'tags' => ['php', 'laravel'],
                ],
                [
                    'slug' => 'caching-and-performance-on-backend',
                    'tags' => ['php', 'laravel', 'devops'],
                ],
            ],
            'devops-and-infrastructure' => [
                [
                    'slug' => 'why-devops-is-important-for-small-teams',
                    'tags' => ['devops', 'docker'],
                ],
                [
                    'slug' => 'setting-up-staging-and-production-environments',
                    'tags' => ['devops', 'docker'],
                ],
                [
                    'slug' => 'deployment-pipeline-basics',
                    'tags' => ['devops', 'api'],
                ],
                [
                    'slug' => 'infrastructure-monitoring-basics',
                    'tags' => ['devops'],
                ],
            ],
            'html-and-css' => [
                [
                    'slug' => 'semantic-html-for-modern-websites',
                    'tags' => ['tailwind-css'],
                ],
                [
                    'slug' => 'css-grid-vs-flexbox-practical-difference',
                    'tags' => ['tailwind-css'],
                ],
                [
                    'slug' => 'how-to-build-responsive-layouts',
                    'tags' => ['tailwind-css'],
                ],
                [
                    'slug' => 'clean-css-architecture-basics',
                    'tags' => ['tailwind-css'],
                ],
            ],
            'javascript' => [
                [
                    'slug' => 'modern-javascript-features-you-should-know',
                    'tags' => ['javascript'],
                ],
                [
                    'slug' => 'async-await-in-real-projects',
                    'tags' => ['javascript', 'api'],
                ],
                [
                    'slug' => 'dom-events-and-ui-interactions',
                    'tags' => ['javascript'],
                ],
                [
                    'slug' => 'working-with-fetch-and-json',
                    'tags' => ['javascript', 'api'],
                ],
            ],
            'php' => [
                [
                    'slug' => 'php-oop-fundamentals-for-real-projects',
                    'tags' => ['php'],
                ],
                [
                    'slug' => 'dependency-injection-in-php-applications',
                    'tags' => ['php', 'laravel'],
                ],
                [
                    'slug' => 'structuring-large-php-projects',
                    'tags' => ['php', 'laravel'],
                ],
                [
                    'slug' => 'error-handling-and-logging-in-php',
                    'tags' => ['php'],
                ],
            ],
            'databases' => [
                [
                    'slug' => 'database-design-basics-for-web-projects',
                    'tags' => ['mysql', 'postgresql'],
                ],
                [
                    'slug' => 'indexes-and-query-optimization',
                    'tags' => ['mysql', 'postgresql'],
                ],
                [
                    'slug' => 'choosing-between-mysql-and-postgresql',
                    'tags' => ['mysql', 'postgresql'],
                ],
                [
                    'slug' => 'normalization-and-practical-schema-design',
                    'tags' => ['mysql', 'postgresql'],
                ],
            ],
            'docker' => [
                [
                    'slug' => 'docker-for-local-development',
                    'tags' => ['docker', 'devops'],
                ],
                [
                    'slug' => 'docker-images-containers-and-volumes',
                    'tags' => ['docker'],
                ],
                [
                    'slug' => 'how-to-build-php-stack-with-docker',
                    'tags' => ['docker', 'php', 'laravel'],
                ],
                [
                    'slug' => 'common-docker-debugging-cases',
                    'tags' => ['docker', 'devops'],
                ],
            ],
            'ci-cd' => [
                [
                    'slug' => 'continuous-integration-basics',
                    'tags' => ['devops'],
                ],
                [
                    'slug' => 'continuous-delivery-for-web-projects',
                    'tags' => ['devops'],
                ],
                [
                    'slug' => 'testing-before-deployment-pipeline',
                    'tags' => ['devops', 'api'],
                ],
                [
                    'slug' => 'how-to-automate-release-process',
                    'tags' => ['devops'],
                ],
            ],
            'vue-js' => [
                [
                    'slug' => 'vue-components-best-practices',
                    'tags' => ['vue-js', 'javascript'],
                ],
                [
                    'slug' => 'working-with-props-and-emits-in-vue',
                    'tags' => ['vue-js', 'javascript'],
                ],
                [
                    'slug' => 'vue-composables-for-clean-code',
                    'tags' => ['vue-js'],
                ],
                [
                    'slug' => 'state-management-patterns-in-vue',
                    'tags' => ['vue-js', 'javascript'],
                ],
            ],
            'tailwind-css' => [
                [
                    'slug' => 'tailwind-css-utility-first-approach',
                    'tags' => ['tailwind-css'],
                ],
                [
                    'slug' => 'building-admin-panels-with-tailwind',
                    'tags' => ['tailwind-css', 'vue-js'],
                ],
                [
                    'slug' => 'dark-mode-patterns-with-tailwind',
                    'tags' => ['tailwind-css'],
                ],
                [
                    'slug' => 'responsive-ui-with-tailwind-css',
                    'tags' => ['tailwind-css'],
                ],
            ],
            'laravel' => [
                [
                    'slug' => 'laravel-routing-and-controllers-basics',
                    'tags' => ['laravel', 'php'],
                ],
                [
                    'slug' => 'eloquent-relations-in-real-projects',
                    'tags' => ['laravel', 'php', 'mysql'],
                ],
                [
                    'slug' => 'requests-resources-and-validation-in-laravel',
                    'tags' => ['laravel', 'php', 'api'],
                ],
                [
                    'slug' => 'laravel-service-layer-and-clean-architecture',
                    'tags' => ['laravel', 'php'],
                ],
            ],
            'mysql' => [
                [
                    'slug' => 'mysql-indexes-explained-simply',
                    'tags' => ['mysql'],
                ],
                [
                    'slug' => 'mysql-query-optimization-for-beginners',
                    'tags' => ['mysql'],
                ],
                [
                    'slug' => 'mysql-relations-and-foreign-keys',
                    'tags' => ['mysql', 'php'],
                ],
                [
                    'slug' => 'mysql-migrations-and-schema-updates',
                    'tags' => ['mysql', 'laravel'],
                ],
            ],
            'postgresql' => [
                [
                    'slug' => 'postgresql-basics-for-backend-developers',
                    'tags' => ['postgresql'],
                ],
                [
                    'slug' => 'jsonb-in-postgresql-practical-usage',
                    'tags' => ['postgresql', 'api'],
                ],
                [
                    'slug' => 'postgresql-indexing-strategies',
                    'tags' => ['postgresql'],
                ],
                [
                    'slug' => 'postgresql-vs-mysql-in-real-projects',
                    'tags' => ['postgresql', 'mysql'],
                ],
            ],
            'docker-compose' => [
                [
                    'slug' => 'docker-compose-for-full-stack-projects',
                    'tags' => ['docker', 'devops'],
                ],
                [
                    'slug' => 'managing-services-with-docker-compose',
                    'tags' => ['docker'],
                ],
                [
                    'slug' => 'php-nginx-mysql-stack-in-docker-compose',
                    'tags' => ['docker', 'php', 'mysql'],
                ],
                [
                    'slug' => 'debugging-multi-container-environments',
                    'tags' => ['docker', 'devops'],
                ],
            ],
            'github-actions' => [
                [
                    'slug' => 'github-actions-for-laravel-projects',
                    'tags' => ['devops', 'laravel'],
                ],
                [
                    'slug' => 'running-tests-in-github-actions',
                    'tags' => ['devops'],
                ],
                [
                    'slug' => 'automatic-deployment-with-github-actions',
                    'tags' => ['devops', 'docker'],
                ],
                [
                    'slug' => 'workflow-secrets-and-environment-variables',
                    'tags' => ['devops'],
                ],
            ],
        ];
    }

    protected function fallbackBlueprints(string $rubricUrl): array
    {
        return [
            [
                'slug' => $rubricUrl . '-guide-1',
                'tags' => ['api'],
            ],
            [
                'slug' => $rubricUrl . '-guide-2',
                'tags' => ['php'],
            ],
            [
                'slug' => $rubricUrl . '-guide-3',
                'tags' => ['javascript'],
            ],
            [
                'slug' => $rubricUrl . '-guide-4',
                'tags' => ['devops'],
            ],
        ];
    }
}
