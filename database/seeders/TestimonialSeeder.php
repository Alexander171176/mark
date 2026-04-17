<?php

namespace Database\Seeders;

use App\Models\Admin\School\Testimonial\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('testimonials')) {
            $this->command?->warn('Нет таблицы testimonials — пропускаю TestimonialSeeder.');
            return;
        }

        $now     = now();
        $seedKey = 'seed:testimonials:v1';

        // Идемпотентность: удаляем только записи, созданные этим сидом ранее
        Testimonial::query()->where('meta->seed_key', $seedKey)->delete();

        // Демо-набор (RU/EN)
        $rows = [
            // ===== RU =====
            [
                'quote'        => 'Прошла программу по веб-разработке — понравилась структура: теория, практика и обратная связь. Уже устроилась на стажировку.',
                'author_name'  => 'Анна Петрова',
                'author_title' => 'Junior Frontend Developer',
                'company'      => 'TechStart',
                'avatar_url'   => null,
                'source_url'   => null,
                'rating'       => 5,
                'locale'       => 'ru',
                'activity'    => true,
            ],
            [
                'quote'        => 'Очень помогли короткие практические задания и проверка наставника. Понял, где мои слабые места.',
                'author_name'  => 'Игорь Смирнов',
                'author_title' => 'QA Engineer',
                'company'      => 'FinApps',
                'avatar_url'   => null,
                'source_url'   => null,
                'rating'       => 4,
                'locale'       => 'ru',
                'activity'    => true,
            ],
            [
                'quote'        => 'Сильные преподаватели и понятные проекты. Сертификат пригодился при трудоустройстве.',
                'author_name'  => 'Мария Ким',
                'author_title' => 'Data Analyst',
                'company'      => 'InsightLab',
                'avatar_url'   => null,
                'source_url'   => null,
                'rating'       => 5,
                'locale'       => 'ru',
                'activity'    => true,
            ],

            // ===== EN =====
            [
                'quote'        => 'Clear curriculum and hands-on projects. Mentor feedback was invaluable for landing my first role.',
                'author_name'  => 'John Walker',
                'author_title' => 'Junior Backend Developer',
                'company'      => 'CloudForge',
                'avatar_url'   => null,
                'source_url'   => null,
                'rating'       => 5,
                'locale'       => 'en',
                'activity'    => true,
            ],
            [
                'quote'        => 'Loved the pacing and bite-sized lessons. The certificate helped me pass HR screening.',
                'author_name'  => 'Emily Chen',
                'author_title' => 'Product Analyst',
                'company'      => 'NovaSoft',
                'avatar_url'   => null,
                'source_url'   => null,
                'rating'       => 4,
                'locale'       => 'en',
                'activity'    => true,
            ],
        ];

        $created = 0;

        // Позиции считаем отдельно по локалям
        $posByLocale = [];

        foreach ($rows as $i => $row) {
            $locale = $row['locale'] ?? null;
            $key    = $locale ?? 'default';

            if (!isset($posByLocale[$key])) {
                $posByLocale[$key] = 0;
            }
            $posByLocale[$key]++;

            $createdAt = $now->copy()->subDays(7 - $i)->subHours($i);

            Testimonial::query()->create([
                'quote'         => $row['quote'],
                'author_name'   => $row['author_name'],
                'author_title'  => $row['author_title'] ?? null,
                'company'       => $row['company'] ?? null,
                'avatar_url'    => $row['avatar_url'] ?? null,
                'source_url'    => $row['source_url'] ?? null,
                'rating'        => $row['rating'] ?? null,
                'activity'      => (bool)($row['activity'] ?? true),
                'sort'          => $posByLocale[$key],
                'locale'        => $locale,
                'meta'          => [
                    'seed_key' => $seedKey,
                    'tags'     => ['seed', 'cms', 'testimonials'],
                ],
                'created_at'    => $createdAt,
                'updated_at'    => $createdAt,
            ]);

            $created++;
        }

        $this->command?->info("Testimonials seeded: created {$created} (идемпотентно по meta.seed_key).");
    }
}
