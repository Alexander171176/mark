<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\Faq\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('faqs')) {
            $this->command?->warn('Нет таблицы faqs — пропускаю FaqSeeder.');
            return;
        }

        $now     = now();
        $seedKey = 'seed:faqs:v1';

        // Идемпотентность: удаляем только наши предыдущие сид-записи
        Faq::query()->where('meta->seed_key', $seedKey)->delete();

        // Набор FAQ (RU/EN). slug сгенерируем автоматически; для EN добавим суффикс локали.
        $rows = [
            // ===== RU: Общие =====
            [
                'question'  => 'Как проходит обучение на платформе?',
                'answer'    => 'Обучение состоит из видеоуроков, практических заданий и обратной связи от наставников. Доступ открыт 24/7 с любого устройства.',
                'category'  => 'general',
                'locale'    => 'ru',
                'activity' => true,
            ],
            [
                'question'  => 'Сколько времени занимает один курс?',
                'answer'    => 'Средняя длительность — 4–8 недель, но вы двигаетесь в своём темпе. Ускоренное прохождение возможно.',
                'category'  => 'general',
                'locale'    => 'ru',
                'activity' => true,
            ],

            // ===== RU: Курсы =====
            [
                'question'  => 'Есть ли у курсов вступительное тестирование?',
                'answer'    => 'Да, базовое тестирование помогает подобрать оптимальный уровень. Оно занимает 5–10 минут.',
                'category'  => 'courses',
                'locale'    => 'ru',
                'activity' => true,
            ],
            [
                'question'  => 'Выдаётся ли сертификат после завершения?',
                'answer'    => 'Да, именной сертификат выдается при успешном завершении всех обязательных заданий и итоговой проверки.',
                'category'  => 'courses',
                'locale'    => 'ru',
                'activity' => true,
            ],

            // ===== RU: Оплата =====
            [
                'question'  => 'Какие способы оплаты поддерживаются?',
                'answer'    => 'Банковские карты, Apple Pay/Google Pay, а также безналичный расчёт для компаний.',
                'category'  => 'payments',
                'locale'    => 'ru',
                'activity' => true,
            ],
            [
                'question'  => 'Можно ли оформить рассрочку?',
                'answer'    => 'Да, рассрочка доступна для некоторых программ. Условия зависят от партнёра по оплате.',
                'category'  => 'payments',
                'locale'    => 'ru',
                'activity' => true,
            ],

            // ===== RU: Аккаунт/Техника =====
            [
                'question'  => 'Забыл пароль — как восстановить доступ?',
                'answer'    => 'Нажмите «Забыли пароль?» на странице входа и следуйте инструкциям в письме для сброса пароля.',
                'category'  => 'account',
                'locale'    => 'ru',
                'activity' => true,
            ],
            [
                'question'  => 'Видео не воспроизводится. Что делать?',
                'answer'    => 'Обновите браузер, очистите кэш, попробуйте режим инкогнито или другой браузер. Если не помогло — свяжитесь с поддержкой.',
                'category'  => 'technical',
                'locale'    => 'ru',
                'activity' => true,
            ],

            // ===== EN: General =====
            [
                'question'  => 'How does the learning process work?',
                'answer'    => 'You get video lessons, hands-on assignments, and mentor feedback. 24/7 access from any device.',
                'category'  => 'general',
                'locale'    => 'en',
                'activity' => true,
            ],
            [
                'question'  => 'Do I receive a certificate upon completion?',
                'answer'    => 'Yes, a named certificate is issued after completing all mandatory assignments and the final review.',
                'category'  => 'courses',
                'locale'    => 'en',
                'activity' => true,
            ],
            [
                'question'  => 'What payment methods are supported?',
                'answer'    => 'Credit/debit cards, Apple Pay/Google Pay, and invoicing for companies.',
                'category'  => 'payments',
                'locale'    => 'en',
                'activity' => true,
            ],
            [
                'question'  => 'I forgot my password. How can I reset it?',
                'answer'    => 'Use the “Forgot password?” link on the login page and follow the instructions sent to your email.',
                'category'  => 'account',
                'locale'    => 'en',
                'activity' => true,
            ],
        ];

        $created = 0;

        // Позиции по категориям (чтобы внутри категории шло упорядочивание)
        $sortCounters = [];

        foreach ($rows as $i => $row) {
            $category = $row['category'] ?? 'general';
            $locale   = $row['locale']   ?? null;

            if (!isset($sortCounters[$category])) {
                $sortCounters[$category] = 0;
            }
            $sortCounters[$category]++;

            // Сгенерируем slug на основе вопроса + локали (чтобы уменьшить коллизии)
            $base = Str::slug(mb_substr($row['question'], 0, 120), '-');
            if ($locale) {
                $base = trim($base . '-' . strtolower($locale), '-');
            }
            $slug = $this->uniqueSlug($base);

            $createdAt = $now->copy()->subDays(10 - $i)->subHours($i);

            Faq::query()->create([
                'question'   => $row['question'],
                'answer'     => $row['answer'],
                'slug'       => $slug,
                'category'   => $category,
                'locale'     => $locale,
                'activity'  => (bool)($row['activity'] ?? true),
                'sort'   => $sortCounters[$category],
                'meta'       => [
                    'seed_key' => $seedKey,
                    'tags'     => ['seed', $category],
                ],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $created++;
        }

        $this->command?->info("FAQs seeded: created {$created} (идемпотентно по meta.seed_key).");
    }

    /**
     * Генератор уникального slug с суффиксом -2, -3, ...
     */
    private function uniqueSlug(string $base): string
    {
        $slug   = $base ?: Str::random(8);
        $suffix = 2;

        while (Faq::query()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $suffix;
            $suffix++;
            // страховка на случай очень редких коллизий
            if ($suffix > 200) {
                $slug = $base . '-' . Str::random(6);
                if (!Faq::query()->where('slug', $slug)->exists()) {
                    break;
                }
            }
        }

        return $slug;
    }
}
