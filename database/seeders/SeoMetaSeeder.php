<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\Cms\SeoMeta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SeoMetaSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('seo_metas')) {
            $this->command?->warn('Нет таблицы seo_metas — пропускаю SeoMetaSeeder.');
            return;
        }

        $now = now();
        $locales = ['ru', 'en'];
        $created = 0; $updated = 0;

        // 1) Глобальные записи (без привязки к сущности)
        foreach ($locales as $loc) {
            $where = [
                'seoable_type' => null,
                'seoable_id'   => null,
                'locale'       => $loc,
            ];

            $payload = [
                'title'            => $this->cut($loc === 'ru' ? 'Онлайн-платформа обучения' : 'Online Learning Platform', 60),
                'description'      => $this->cut($loc === 'ru'
                    ? 'Курсы, уроки и программы повышения квалификации с практикой и обратной связью.'
                    : 'Courses, lessons and upskilling programs with practice and feedback.', 160),
                'keywords'         => $loc === 'ru'
                    ? 'онлайн-курсы, обучение, уроки'
                    : 'online courses, learning, lessons',
                'robots_noindex'   => false,
                'robots_nofollow'  => false,
                'canonical_url'    => url('/'),
                'og_title'         => $loc === 'ru' ? 'Онлайн-платформа обучения' : 'Online Learning Platform',
                'og_description'   => $loc === 'ru'
                    ? 'Практико-ориентированные курсы и программы.'
                    : 'Practice-oriented courses and programs.',
                'og_image_url'     => url('/img/og-default.jpg'),
                'og_type'          => 'website',
                'twitter_card'     => 'summary_large_image',
                'activity'        => true,
                'json_ld'          => [
                    '@context' => 'https://schema.org',
                    '@type'    => 'WebSite',
                    'name'     => $loc === 'ru' ? 'Онлайн-платформа обучения' : 'Online Learning Platform',
                    'url'      => url('/'),
                ],
                'meta'             => [
                    'seeded'   => true,
                    'seed_run' => $now->toDateTimeString(),
                    'source'   => 'SeoMetaSeeder',
                    'scope'    => 'global',
                ],
                'updated_at'       => $now,
            ];

            $model = SeoMeta::query()->where($where)->first();
            if ($model) {
                $model->fill($payload)->save();
                $updated++;
            } else {
                SeoMeta::query()->create(array_merge($where, $payload, ['created_at' => $now]));
                $created++;
            }
        }

        // 2) SEO для страниц
        if (Schema::hasTable('pages')) {
            $pages = DB::table('pages')
                ->select('id','title','excerpt','content','locale','slug','published_at','updated_at')
                ->orderByDesc('published_at')->limit(20)->get();

            foreach ($pages as $page) {
                $locale = $page->locale ?: null;

                $where = [
                    'seoable_type' => 'App\\Models\\Admin\\Constructor\\Page\\Page',
                    'seoable_id'   => $page->id,
                    'locale'       => $locale,
                ];

                $title = $this->buildTitle($page->title);
                $desc  = $this->bestDescription($page->excerpt, $page->content);

                $payload = [
                    'title'            => $this->cut($title, 60),
                    'description'      => $this->cut($desc, 160),
                    'keywords'         => $this->makeKeywords($desc),
                    'robots_noindex'   => false,
                    'robots_nofollow'  => false,
                    'canonical_url'    => url('/' . ltrim($page->slug, '/')),
                    'og_title'         => $this->cut($title, 90),
                    'og_description'   => $this->cut($desc, 200),
                    'og_image_url'     => url('/img/og-page.jpg'),
                    'og_type'          => 'article',
                    'twitter_card'     => 'summary_large_image',
                    'activity'        => true,
                    'json_ld'          => [
                        '@context' => 'https://schema.org',
                        '@type'    => 'Article',
                        'headline' => $title,
                        'dateModified' => optional($page->updated_at)->toDateTimeString(),
                        'mainEntityOfPage' => url('/' . ltrim($page->slug, '/')),
                    ],
                    'meta'             => [
                        'seeded'   => true,
                        'seed_run' => $now->toDateTimeString(),
                        'scope'    => 'page',
                        'slug'     => $page->slug,
                    ],
                    'updated_at'       => $now,
                ];

                $model = SeoMeta::query()->where($where)->first();
                if ($model) {
                    $model->fill($payload)->save();
                    $updated++;
                } else {
                    SeoMeta::query()->create(array_merge($where, $payload, ['created_at' => $now]));
                    $created++;
                }
            }
        }

        // 3) SEO для постов
        if (Schema::hasTable('blog_posts')) {
            $posts = DB::table('blog_posts')
                ->select('id','title','excerpt','content','locale','slug','published_at','cover_image_url','updated_at','status','activity')
                ->orderByDesc('published_at')->limit(30)->get();

            foreach ($posts as $post) {
                $locale = $post->locale ?: null;

                $where = [
                    'seoable_type' => 'App\\Models\\Admin\\Constructor\\Cms\\BlogPost',
                    'seoable_id'   => $post->id,
                    'locale'       => $locale,
                ];

                $title = $this->buildTitle($post->title);
                $desc  = $this->bestDescription($post->excerpt, $post->content);
                $noindex = !($post->status === 'published' && $post->activity);

                $payload = [
                    'title'            => $this->cut($title, 60),
                    'description'      => $this->cut($desc, 160),
                    'keywords'         => $this->makeKeywords($desc),
                    'robots_noindex'   => $noindex,
                    'robots_nofollow'  => false,
                    'canonical_url'    => url('/' . ltrim($post->slug, '/')),
                    'og_title'         => $this->cut($title, 90),
                    'og_description'   => $this->cut($desc, 200),
                    'og_image_url'     => $post->cover_image_url ?: url('/img/og-post.jpg'),
                    'og_type'          => 'article',
                    'twitter_card'     => 'summary_large_image',
                    'activity'        => true,
                    'json_ld'          => [
                        '@context' => 'https://schema.org',
                        '@type'    => 'BlogPosting',
                        'headline' => $title,
                        'image'    => $post->cover_image_url ?: url('/img/og-post.jpg'),
                        'datePublished' => optional($post->published_at)->toDateTimeString(),
                        'dateModified'  => optional($post->updated_at)->toDateTimeString(),
                        'mainEntityOfPage' => url('/' . ltrim($post->slug, '/')),
                    ],
                    'meta'             => [
                        'seeded'   => true,
                        'seed_run' => $now->toDateTimeString(),
                        'scope'    => 'blog_post',
                        'slug'     => $post->slug,
                    ],
                    'updated_at'       => $now,
                ];

                $model = SeoMeta::query()->where($where)->first();
                if ($model) {
                    $model->fill($payload)->save();
                    $updated++;
                } else {
                    SeoMeta::query()->create(array_merge($where, $payload, ['created_at' => $now]));
                    $created++;
                }
            }
        }

        $this->command?->info("SEO metas upserted: created {$created}, updated {$updated}.");
    }

    /* ==================== Хелперы ==================== */

    private function cut(?string $text, int $limit): ?string
    {
        if ($text === null) return null;
        $clean = trim(preg_replace('/\s+/', ' ', strip_tags($text)));
        return Str::limit($clean, $limit, '…');
    }

    private function bestDescription(?string $excerpt, ?string $content): ?string
    {
        $base = $excerpt ?: $this->firstSentence($content);
        return $this->cut($base, 180);
    }

    private function firstSentence(?string $html): ?string
    {
        if (!$html) return null;
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
        if ($text === '') return null;
        if (preg_match('/^(.+?[\.!?])\s/u', $text, $m)) {
            return $m[1];
        }
        return Str::limit($text, 200, '');
    }

    private function buildTitle(string $title): string
    {
        return $title . ' • EduPlatform';
    }

    private function makeKeywords(?string $desc): ?string
    {
        if (!$desc) return null;
        $words = collect(preg_split('/[^\p{L}\p{N}\-]+/u', mb_strtolower($desc)) ?: [])
            ->filter(fn($w) => mb_strlen($w) >= 5)
            ->map(fn($w) => trim($w, '-'))
            ->filter()
            ->unique()
            ->take(6)
            ->implode(', ');
        return $words ?: null;
    }
}
