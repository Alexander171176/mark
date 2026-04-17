<?php

namespace Database\Seeders;

use App\Models\Admin\Crm\Lead\Lead;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('leads')) {
            $this->command?->warn('Нет таблицы leads — пропускаю LeadSeeder.');
            return;
        }

        $faker = fake('ru_RU'); // русскоязычные имена/тексты
        $now   = now();

        // Возьмём набор возможных менеджеров (если есть таблица users)
        $managerIds = [];
        if (Schema::hasTable('users')) {
            $managerIds = DB::table('users')->pluck('id')->all();
        }

        // Наборы значений
        $sources = ['contact_form', 'landing', 'course_page', 'popup', 'widget', 'webinar', 'promo'];
        $pages   = [
            'https://edu.local/',
            'https://edu.local/courses/laravel',
            'https://edu.local/courses/vue',
            'https://edu.local/webinar/seo',
            'https://edu.local/blog/education-trends',
            'https://edu.local/pricing',
            'https://edu.local/contacts',
        ];
        $referrers = [
            null,
            'https://google.com',
            'https://yandex.ru',
            'https://vk.com',
            'https://t.me',
            'https://facebook.com',
        ];

        $utmCampaigns = [
            'black_friday',
            'spring_sale',
            'webinar_laravel',
            'content_marketing',
            'remarketing_q3',
            'brand_search',
        ];
        $utmSources  = ['google', 'yandex', 'vk', 'facebook', 'telegram', 'email', 'direct'];
        $utmMediums  = ['cpc', 'cpm', 'social', 'referral', 'email', 'organic'];
        $utmTerms    = ['laravel course', 'vue course', 'frontend', 'backend', 'seo', 'education'];
        $utmContents = ['banner1', 'banner2', 'video1', 'textlink', 'carousel'];

        // Распределение статусов (весами)
        $statusPool = [
            'new','new','new','new',
            'contacted','contacted','qualified',
            'won','lost',
            'spam',
        ];

        // Сколько лидов сгенерировать
        $count = 120;
        $created = 0; $updated = 0;

        for ($i = 0; $i < $count; $i++) {
            // Временной разброс за последние ~90 дней
            $createdAt = $now->copy()->subDays(rand(0, 90))->subMinutes(rand(0, 1440));

            // Контакты
            $name  = $faker->name();
            $email = $faker->unique()->safeEmail();
            // телефон — укоротим до 32 символов и оставим только разрешённые символы
            $phoneRaw = $faker->phoneNumber();
            $phone    = mb_substr(preg_replace('/[^0-9\+\-\(\)\s]/u', '', $phoneRaw), 0, 32);

            // Сообщение
            $message = $faker->optional(0.8)->realText(rand(80, 220));

            // Источник/страница/реферер
            $source   = $faker->randomElement($sources);
            $pageUrl  = $faker->randomElement($pages);
            $referrer = $faker->randomElement($referrers);

            // UTM
            $utm_source   = $faker->randomElement($utmSources);
            $utm_medium   = $faker->randomElement($utmMediums);
            $utm_campaign = $faker->randomElement($utmCampaigns);
            $utm_term     = $faker->optional(0.5)->randomElement($utmTerms);
            $utm_content  = $faker->optional(0.5)->randomElement($utmContents);

            // Тех.инфо
            $ip        = $faker->ipv4();
            $userAgent = $faker->userAgent();
            $consent   = $faker->boolean(75); // 75% согласны

            // Статус/обработка
            $status = $faker->randomElement($statusPool);
            $processedAt = in_array($status, ['new','spam'], true)
                ? null
                : $createdAt->copy()->addHours(rand(1, 72));

            // Ответственный менеджер — не всегда
            $managerId = (!empty($managerIds) && $faker->boolean(60))
                ? $faker->randomElement($managerIds)
                : null;

            // Детерминированный «отпечаток» для идемпотентности
            // Привязываем к email + (дню) + источнику + url + кампании
            $fingerprint = sha1(implode('|', [
                mb_strtolower($email),
                $createdAt->format('Y-m-d'),
                (string) $source,
                (string) $pageUrl,
                (string) $utm_campaign,
            ]));

            // Найдём существующую запись по fingerprint в JSON
            /** @var Lead|null $existing */
            $existing = Lead::query()
                ->where('meta->fingerprint', $fingerprint)
                ->first();

            $payload = [
                // Контакты
                'name'        => $name,
                'email'       => $email,
                'phone'       => $phone,
                // Сообщение и источник
                'message'     => $message,
                'source'      => $source,
                'page_url'    => $pageUrl,
                'referrer'    => $referrer,
                // UTM
                'utm_source'   => $utm_source,
                'utm_medium'   => $utm_medium,
                'utm_campaign' => $utm_campaign,
                'utm_term'     => $utm_term,
                'utm_content'  => $utm_content,
                // Тех.инфо
                'ip'          => $ip,
                'user_agent'  => $userAgent,
                'consent'     => $consent,
                // Статус/обработка
                'status'       => $status,
                'processed_at' => $processedAt,
                'manager_id'   => $managerId,
                // Примечания/мета
                'notes'        => $faker->optional(0.2)->sentence(8),
                'meta'         => [
                    'seeded'      => true,
                    'seed_run'    => $now->toDateTimeString(),
                    'source'      => 'LeadSeeder',
                    'fingerprint' => $fingerprint,
                    'tags'        => $faker->randomElements(
                        ['lead','form','landing','webinar','promo','remarketing'],
                        rand(1, 3)
                    ),
                ],
                // Времена
                'created_at'   => $createdAt,
                'updated_at'   => $now,
            ];

            if ($existing) {
                $existing->fill($payload)->save();
                $updated++;
            } else {
                Lead::query()->create($payload);
                $created++;
            }
        }

        $this->command?->info("Leads upserted: created {$created}, updated {$updated}.");
    }
}
