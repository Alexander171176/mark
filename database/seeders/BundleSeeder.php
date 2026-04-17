<?php

namespace Database\Seeders;

use App\Models\Admin\School\Bundle\Bundle;
use App\Models\Admin\School\Course\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BundleSeeder extends Seeder
{
    private const LOCALES = ['ru', 'en', 'kk'];

    public function run(): void
    {
        foreach (['bundles', 'courses', 'bundle_has_course'] as $table) {
            if (!Schema::hasTable($table)) {
                $this->command?->warn("Таблица {$table} отсутствует — пропускаю BundleSeeder.");
                return;
            }
        }

        $courses = Course::query()
            ->select(['id', 'locale', 'title', 'slug', 'status', 'published_at'])
            ->get();

        if ($courses->isEmpty()) {
            $this->command?->warn('Курсы не найдены — сначала посейте CourseSeeder.');
            return;
        }

        // Пулы названий под локали (можно расширять)
        $titlePool = [
            'ru' => collect([
                'Полный путь Frontend-разработчика',
                'Backend Essentials',
                'Старт в Data Science',
                'Fullstack Pro Bundle',
                'Набор для мобильной разработки',
                'DevOps Crash Pack',
                'Инструменты продуктовой аналитики',
                'QA Automation Suite',
            ])->shuffle(),

            'en' => collect([
                'Frontend Developer Roadmap',
                'Backend Essentials Bundle',
                'Data Science Starter Pack',
                'Fullstack Pro Bundle',
                'Mobile Development Kit',
                'DevOps Crash Pack',
                'Product Analytics Toolbox',
                'QA Automation Suite',
            ])->shuffle(),

            'kk' => collect([
                'Frontend әзірлеуші жол картасы',
                'Backend Essentials топтамасы',
                'Data Science бастапқы жинағы',
                'Fullstack Pro топтамасы',
                'Мобильді әзірлеу жинағы',
                'DevOps жедел жинағы',
                'Өнім аналитикасы құралдары',
                'QA автоматтандыру жинағы',
            ])->shuffle(),
        ];

        // Сколько "тем" (базовых бандлов) делаем (каждая тема будет в 3 локалях)
        $baseCount = rand(4, 7);

        DB::transaction(function () use ($courses, $titlePool, $baseCount) {

            for ($i = 0; $i < $baseCount; $i++) {

                // Берём "базовый" заголовок из RU пула (как источник slug)
                $baseTitleRu = $titlePool['ru']->get($i) ?? "Набор {$i}";
                $baseSlug = Str::slug($baseTitleRu);

                foreach (self::LOCALES as $locale) {

                    $title = $titlePool[$locale]->get($i) ?? $baseTitleRu;

                    // slug глобально уникальный -> добавляем суффикс локали (кроме ru)
                    $localizedBase = $locale === 'ru'
                        ? $baseSlug
                        : ($baseSlug . '-' . $locale);

                    $slug = $this->uniqueSlug($localizedBase);

                    $activity = (bool) rand(0, 1);

                    $publishedAt = $activity && rand(0, 100) < 80
                        ? now()->subDays(rand(3, 120))
                        : null;

                    $views = rand(0, 2000);
                    $likes = rand(0, min(500, $views));

                    /** @var Bundle $bundle */
                    $bundle = Bundle::query()->updateOrCreate(
                        ['slug' => $slug],
                        [
                            'locale'       => $locale,
                            'title'        => $title,
                            'subtitle'     => $this->fakeSubtitle($locale),
                            'short'        => $this->fakeShort($title, $locale),
                            'description'  => $this->fakeDescription($title, $locale),

                            'sort'         => $i * 10,
                            'activity'     => $activity,
                            'published_at' => $publishedAt,

                            'views'        => $views,
                            'likes'        => $likes,

                            'meta_title'    => $this->fakeMetaTitle($title),
                            'meta_keywords' => $this->fakeMetaKeywords($locale),
                            'meta_desc'     => $this->fakeMetaDesc($title, $locale),

                            'meta'         => [
                                'badge'  => rand(0, 100) < 35 ? 'HIT' : null,
                                'ribbon' => rand(0, 100) < 25 ? '-25%' : null,
                                'theme'  => collect(['blue', 'green', 'violet', 'orange', 'teal'])->random(),
                            ],
                        ]
                    );

                    // ---------- Курсы для привязки ----------

                    // 1) пытаемся взять курсы нужной локали
                    $coursesByLocale = $courses->where('locale', $locale);

                    // 2) предпочитаем published
                    $publishedByLocale = $coursesByLocale->where('status', 'published');
                    $publishedAll      = $courses->where('status', 'published');

                    $source =
                        $publishedByLocale->isNotEmpty() ? $publishedByLocale :
                            ($coursesByLocale->isNotEmpty() ? $coursesByLocale :
                                ($publishedAll->isNotEmpty() ? $publishedAll : $courses));

                    $attachIds = $source
                        ->shuffle()
                        ->take(rand(2, min(6, $source->count())))
                        ->pluck('id')
                        ->all();

                    $bundle->courses()->syncWithoutDetaching($attachIds);
                }
            }
        });

        $this->command?->info('BundleSeeder: бандлы для ru/en/kk успешно созданы (без изображений).');
    }

    /**
     * Уникальный slug с учётом soft-deletes.
     */
    private function uniqueSlug(string $base): string
    {
        $slug = $base ?: Str::random(8);
        $try  = 0;

        do {
            $candidate = $try === 0 ? $slug : "{$slug}-{$try}";
            $exists    = Bundle::withTrashed()->where('slug', $candidate)->exists();

            if (!$exists) {
                return $candidate;
            }

            $try++;
        } while ($try < 50);

        return $slug . '-' . Str::lower(Str::random(6));
    }

    private function fakeSubtitle(string $locale): ?string
    {
        return match ($locale) {
            'ru' => 'Выгодно и по делу — всё в одном наборе',
            'en' => 'Best value — everything in one bundle',
            'kk' => 'Тиімді ұсыныс — бәрі бір жинақта',
            default => null,
        };
    }

    private function fakeShort(string $title, string $locale): ?string
    {
        return match ($locale) {
            'ru' => "Коротко о наборе «{$title}»: собрали ключевые курсы в одном предложении.",
            'en' => "About “{$title}”: a curated selection of key courses in one bundle.",
            'kk' => "«{$title}» туралы қысқаша: негізгі курстар бір жинақта.",
            default => null,
        };
    }

    private function fakeDescription(string $title, string $locale): string
    {
        return match ($locale) {
            'ru' => "Набор «{$title}» — выгодный комплект из популярных курсов. Подходит для ускоренного старта и системного обучения.",
            'en' => "“{$title}” is a value bundle of popular courses. Perfect for a fast start and structured learning.",
            'kk' => "«{$title}» — танымал курстардан құралған тиімді жинақ. Жылдам бастауға және жүйелі оқуға арналған.",
            default => "Bundle {$title}",
        };
    }

    private function fakeMetaTitle(string $title): string
    {
        // 160 max — держим коротко
        return Str::limit($title . ' — Bundle', 160, '');
    }

    private function fakeMetaKeywords(string $locale): string
    {
        return match ($locale) {
            'ru' => 'курсы, обучение, онлайн-курс, набор курсов, комплект, скидка',
            'en' => 'courses, online learning, bundle, learning pack, discount',
            'kk' => 'курстар, онлайн оқу, жинақ, оқу пакеті, жеңілдік',
            default => 'bundle',
        };
    }

    private function fakeMetaDesc(string $title, string $locale): string
    {
        $text = match ($locale) {
            'ru' => "Набор «{$title}»: несколько курсов по выгодной цене. Подходит для быстрого старта и уверенного прогресса.",
            'en' => "“{$title}”: multiple courses at a great price. Designed for a fast start and steady progress.",
            'kk' => "«{$title}»: бірнеше курс тиімді бағамен. Жылдам бастауға және тұрақты прогреске арналған.",
            default => $title,
        };

        return Str::limit($text, 255, '');
    }
}
