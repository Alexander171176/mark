<?php

namespace Database\Seeders;

use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogArticle\BlogArticleTranslation;
use Illuminate\Database\Seeder;

class BlogArticleTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            'how-frontend-projects-are-structured' => [
                'ru' => [
                    'title' => 'Как структурировать frontend-проекты',
                    'subtitle' => 'Архитектура интерфейсов',
                    'short' => 'Разбираем структуру папок, компонентов и модулей во frontend-проекте.',
                    'description' => 'В статье рассматриваются подходы к организации frontend-проекта: структура папок, разделение UI и бизнес-логики, переиспользование компонентов и подготовка проекта к росту.',
                    'pseudonym' => 'Александр Косолапов',
                    'meta_title' => 'Как структурировать frontend-проекты',
                    'meta_keywords' => 'frontend, structure, components, architecture',
                    'meta_desc' => 'Материал о правильной структуре frontend-проектов и организации компонентов.',
                ],
                'en' => [
                    'title' => 'How to Structure Frontend Projects',
                    'subtitle' => 'Interface architecture',
                    'short' => 'We look at folders, components and modules inside a frontend project.',
                    'description' => 'This article explains approaches to organizing a frontend project: folder structure, separation of UI and business logic, reusable components and project scalability.',
                    'pseudonym' => 'Alexander Kosolapov',
                    'meta_title' => 'How to Structure Frontend Projects',
                    'meta_keywords' => 'frontend, structure, components, architecture',
                    'meta_desc' => 'A guide to frontend project structure and reusable interface architecture.',
                ],
                'kk' => [
                    'title' => 'Frontend жобаларын қалай құрылымдау керек',
                    'subtitle' => 'Интерфейс архитектурасы',
                    'short' => 'Frontend жоба ішіндегі бумалар, компоненттер және модульдер құрылымын қарастырамыз.',
                    'description' => 'Бұл мақалада frontend жобаны ұйымдастыру тәсілдері қарастырылады: бумалар құрылымы, UI және бизнес-логиканы бөлу, қайта қолданылатын компоненттер және жобаны кеңейту.',
                    'pseudonym' => 'Александр Косолапов',
                    'meta_title' => 'Frontend жобаларын қалай құрылымдау керек',
                    'meta_keywords' => 'frontend, structure, components, architecture',
                    'meta_desc' => 'Frontend жоба құрылымы және интерфейс архитектурасы туралы материал.',
                ],
            ],
        ];

        $articles = BlogArticle::query()->get()->keyBy('url');

        foreach ($articles as $article) {
            $url = $article->url;

            $prepared = $translations[$url] ?? $this->makeAutoTranslations($url);

            foreach ($prepared as $locale => $data) {
                BlogArticleTranslation::updateOrCreate(
                    [
                        'article_id' => $article->id,
                        'locale' => $locale,
                    ],
                    $data
                );
            }
        }
    }

    protected function makeAutoTranslations(string $slug): array
    {
        $titleBase = str($slug)
            ->replace('-', ' ')
            ->title()
            ->toString();

        return [
            'ru' => [
                'title' => $titleBase . ' — руководство',
                'subtitle' => 'Практический материал по теме',
                'short' => 'Краткий обзор по теме: ' . $titleBase . '.',
                'description' => 'Подробный материал по теме "' . $titleBase . '". В статье рассматриваются практические подходы, архитектурные решения, типичные ошибки и рекомендации для реальных IT-проектов.',
                'pseudonym' => 'Александр Косолапов',
                'meta_title' => $titleBase . ' — руководство',
                'meta_keywords' => strtolower(str_replace(' ', ', ', $titleBase)) . ', programming, it',
                'meta_desc' => 'Практический материал по теме "' . $titleBase . '" для разработчиков.',
            ],
            'en' => [
                'title' => $titleBase . ' Guide',
                'subtitle' => 'Practical material on the topic',
                'short' => 'A short overview of the topic: ' . $titleBase . '.',
                'description' => 'A detailed article about "' . $titleBase . '". It covers practical approaches, architectural decisions, common mistakes and recommendations for real IT projects.',
                'pseudonym' => 'Alexander Kosolapov',
                'meta_title' => $titleBase . ' Guide',
                'meta_keywords' => strtolower(str_replace(' ', ', ', $titleBase)) . ', programming, it',
                'meta_desc' => 'Practical material on "' . $titleBase . '" for developers.',
            ],
            'kk' => [
                'title' => $titleBase . ' нұсқаулығы',
                'subtitle' => 'Тақырып бойынша практикалық материал',
                'short' => $titleBase . ' тақырыбы бойынша қысқаша шолу.',
                'description' => '"' . $titleBase . '" тақырыбы бойынша толық материал. Мақалада практикалық тәсілдер, архитектуралық шешімдер, жиі қателер және нақты IT жобаларға арналған ұсыныстар қарастырылады.',
                'pseudonym' => 'Александр Косолапов',
                'meta_title' => $titleBase . ' нұсқаулығы',
                'meta_keywords' => strtolower(str_replace(' ', ', ', $titleBase)) . ', programming, it',
                'meta_desc' => '"' . $titleBase . '" тақырыбы бойынша әзірлеушілерге арналған практикалық материал.',
            ],
        ];
    }
}
