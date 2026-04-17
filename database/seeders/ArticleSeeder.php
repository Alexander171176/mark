<?php

namespace Database\Seeders;

use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Rubric\Rubric;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1;
        $locales = ['ru', 'kk', 'en'];

        $now = now();

        // Берём все рубрики (любой level)
        $rubrics = Rubric::query()
            ->select(['id', 'title', 'locale', 'url', 'level', 'parent_id'])
            ->get();

        foreach ($rubrics as $rubric) {
            foreach ($locales as $locale) {

                // Для каждой рубрики в каждой локали создаём 6 статей
                for ($i = 1; $i <= 6; $i++) {

                    $title = $this->makeTitle($locale, $rubric->title, $rubric->id, $i);
                    $url = $this->makeUrl($locale, $rubric->id, $i);

                    $article = Article::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'locale'  => $locale,
                            'title'   => $title,
                        ],
                        [
                            'sort' => $i,
                            'activity' => true,

                            // чтобы на главной что-то было видно — раскидаем по флагам
                            'left'  => $i === 1,
                            'main'  => in_array($i, [2, 3], true),
                            'right' => $i === 4,

                            'moderation_status' => 1,
                            'moderated_by' => $userId,
                            'moderated_at' => $now,
                            'moderation_note' => null,

                            'img' => null,

                            'url' => $url,
                            'subtitle' => $this->makeSubtitle($locale, $rubric->title, $i),
                            'short' => $this->makeShort($locale, $rubric->title, $i),
                            'description' => $this->makeBody($locale, $rubric->title, $rubric->id, $i),
                            'pseudonym' => null,

                            'published_at' => $now->toDateString(),
                            'show_from_at' => $now,
                            'show_to_at' => null,

                            'views' => 0,

                            'meta_title' => $this->makeMetaTitle($locale, $rubric->title, $i),
                            'meta_keywords' => $this->makeMetaKeywords($locale, $rubric->title),
                            'meta_desc' => $this->makeMetaDesc($locale, $rubric->title, $i),

                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    );

                    // Привязка к рубрике через pivot (article_has_rubric)
                    // syncWithoutDetaching, чтобы не затирать другие связи (если добавишь потом)
                    $article->rubrics()->syncWithoutDetaching([$rubric->id]);
                }
            }
        }
    }

    private function makeTitle(string $locale, string $rubricTitle, int $rubricId, int $i): string
    {
        return match ($locale) {
            'ru' => "{$rubricTitle}: статья #{$i} (рубрика {$rubricId})",
            'kk' => "{$rubricTitle}: мақала #{$i} (айдар {$rubricId})",
            default => "{$rubricTitle}: article #{$i} (rubric {$rubricId})",
        };
    }

    private function makeUrl(string $locale, int $rubricId, int $i): string
    {
        // делаем максимально стабильный URL (латиница)
        return "rubric-{$rubricId}/article-{$i}-{$locale}";
    }

    private function makeSubtitle(string $locale, string $rubricTitle, int $i): ?string
    {
        return match ($locale) {
            'ru' => "Коротко о теме «{$rubricTitle}», часть {$i}",
            'kk' => "«{$rubricTitle}» тақырыбы қысқаша, бөлім {$i}",
            default => "Quick note on “{$rubricTitle}”, part {$i}",
        };
    }

    private function makeShort(string $locale, string $rubricTitle, int $i): ?string
    {
        return match ($locale) {
            'ru' => "Практическая заметка по «{$rubricTitle}» — материал {$i}.",
            'kk' => "«{$rubricTitle}» бойынша практикалық жазба — материал {$i}.",
            default => "A practical note on “{$rubricTitle}” — item {$i}.",
        };
    }

    private function makeBody(string $locale, string $rubricTitle, int $rubricId, int $i): ?string
    {
        return match ($locale) {
            'ru' => "Демо-контент для тестирования. Рубрика: {$rubricTitle} (ID {$rubricId}). Статья #{$i}. Здесь позже будет реальный текст.",
            'kk' => "Тестілеуге арналған демо-контент. Айдар: {$rubricTitle} (ID {$rubricId}). Мақала #{$i}. Кейін нақты мәтін болады.",
            default => "Demo content for testing. Rubric: {$rubricTitle} (ID {$rubricId}). Article #{$i}. Real text will be added later.",
        };
    }

    private function makeMetaTitle(string $locale, string $rubricTitle, int $i): ?string
    {
        return match ($locale) {
            'ru' => "{$rubricTitle} — статья {$i}",
            'kk' => "{$rubricTitle} — мақала {$i}",
            default => "{$rubricTitle} — article {$i}",
        };
    }

    private function makeMetaKeywords(string $locale, string $rubricTitle): ?string
    {
        return match ($locale) {
            'ru' => mb_strtolower("{$rubricTitle}, IT, программирование, разработка, статьи"),
            'kk' => mb_strtolower("{$rubricTitle}, IT, бағдарламалау, әзірлеу, мақалалар"),
            default => mb_strtolower("{$rubricTitle}, IT, programming, development, articles"),
        };
    }

    private function makeMetaDesc(string $locale, string $rubricTitle, int $i): ?string
    {
        return match ($locale) {
            'ru' => "Тестовая статья #{$i} по теме «{$rubricTitle}».",
            'kk' => "«{$rubricTitle}» тақырыбы бойынша тест мақала #{$i}.",
            default => "Test article #{$i} on “{$rubricTitle}”.",
        };
    }
}
