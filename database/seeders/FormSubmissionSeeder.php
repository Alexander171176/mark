<?php

namespace Database\Seeders;

use App\Models\Admin\Crm\FormSubmission\FormSubmission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FormSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('form_submissions')) {
            $this->command?->warn('Нет таблицы form_submissions — пропускаю FormSubmissionSeeder.');
            return;
        }

        $now      = now();
        $seedKey  = 'seed:form_submissions:v1';
        $baseUa   = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 '
            . '(KHTML, like Gecko) Chrome/119.0 Safari/537.36';
        $ips      = ['192.168.1.10', '10.0.0.24', '203.0.113.55', '2001:db8::1'];

        // Идемпотентность: очищаем предыдущие записи именно этого сидера
        FormSubmission::query()
            ->where('meta->seed_key', $seedKey)
            ->delete();

        // Вспомогательный поиск пользователя по email
        $findUserId = function (?string $email): ?int {
            if (!$email || !Schema::hasTable('users')) return null;
            $user = User::query()->where('email', $email)->first(['id']);
            return $user?->id;
        };

        // Набор тестовых заявок
        $rows = [
            // Contact — RU
            [
                'form_key'     => 'contact',
                'name'         => 'Александр',
                'email'        => 'alex@example.com',
                'phone'        => '+7 (700) 111-22-33',
                'message'      => 'Здравствуйте! Интересует корпоративное обучение по Laravel.',
                'data'         => [
                    'subject' => 'Корпоративный запрос',
                    'company' => 'ООО «Пример»',
                ],
                'ip'           => $ips[0],
                'user_agent'   => $baseUa,
                'referrer'     => 'https://google.com/search?q=laravel+courses',
                'page_url'     => 'https://example.local/contacts',
                'locale'       => 'ru',
                'is_spam'      => false,
                'is_read'      => false,
                'processed_at' => null,
                'notes'        => null,
            ],

            // Demo request — EN
            [
                'form_key'     => 'demo_request',
                'name'         => 'John Doe',
                'email'        => 'john.doe@example.com',
                'phone'        => '+44 20 7946 0958',
                'message'      => 'Requesting a live demo for the team (5 people).',
                'data'         => [
                    'team_size' => 5,
                    'timezone'  => 'Europe/London',
                    'product'   => 'Academy Pro',
                ],
                'ip'           => $ips[1],
                'user_agent'   => $baseUa,
                'referrer'     => 'https://linkedin.com/',
                'page_url'     => 'https://example.local/en/request-demo',
                'locale'       => 'en',
                'is_spam'      => false,
                'is_read'      => true,
                'processed_at' => $now->copy()->subDays(2),
                'notes'        => 'Назначена встреча на среду.',
            ],

            // Newsletter — RU
            [
                'form_key'     => 'newsletter',
                'name'         => 'Ольга',
                'email'        => 'olga@example.com',
                'phone'        => null,
                'message'      => null,
                'data'         => [
                    'agree_terms' => true,
                    'topics'      => ['laravel', 'vue', 'devops'],
                ],
                'ip'           => $ips[2],
                'user_agent'   => $baseUa,
                'referrer'     => 'https://example.local/blog',
                'page_url'     => 'https://example.local',
                'locale'       => 'ru',
                'is_spam'      => false,
                'is_read'      => true,
                'processed_at' => $now->copy()->subDay(),
                'notes'        => 'Подтверждена подписка в ESP.',
            ],

            // Contact — RU (обработан)
            [
                'form_key'     => 'contact',
                'name'         => 'Иван Петров',
                'email'        => 'ivan.petrov@example.com',
                'phone'        => '+7 (727) 555-66-77',
                'message'      => 'Нужна консультация по программе обучения для начинающих.',
                'data'         => [
                    'preferred_time' => '19:00-21:00',
                ],
                'ip'           => $ips[3],
                'user_agent'   => $baseUa,
                'referrer'     => 'https://yandex.kz/',
                'page_url'     => 'https://example.local/ru/contacts',
                'locale'       => 'ru',
                'is_spam'      => false,
                'is_read'      => true,
                'processed_at' => $now->copy()->subHours(5),
                'notes'        => 'Перезвонили, выслали коммерческое предложение.',
            ],

            // Callback — помечено как спам
            [
                'form_key'     => 'callback',
                'name'         => 'Free $$$',
                'email'        => 'spam@spammy.biz',
                'phone'        => '+1 (555) 000-0000',
                'message'      => 'WIN BIG PRIZE CLICK HERE',
                'data'         => [
                    'honeypot' => 'yes',
                ],
                'ip'           => '45.12.34.56',
                'user_agent'   => $baseUa,
                'referrer'     => 'http://example-spam.tld/',
                'page_url'     => 'https://example.local/callback',
                'locale'       => 'en',
                'is_spam'      => true,
                'is_read'      => true,
                'processed_at' => null,
                'notes'        => 'Автомодерация: спам.',
            ],
        ];

        $created = 0;

        foreach ($rows as $i => $row) {
            $email     = $row['email'] ?? null;
            $userId    = $findUserId($email);
            $createdAt = $now->copy()->subDays(7 - $i)->subHours($i); // немного «раскидаем» по времени

            FormSubmission::query()->create([
                'user_id'       => $userId,
                'form_key'      => $row['form_key'],
                'name'          => $row['name'],
                'email'         => $row['email'],
                'phone'         => $row['phone'],
                'message'       => $row['message'],
                'data'          => $row['data'],             // массив → JSON (через casts)
                'ip'            => $row['ip'],
                'user_agent'    => $row['user_agent'],
                'referrer'      => $row['referrer'],
                'page_url'      => $row['page_url'],
                'locale'        => $row['locale'],
                'is_spam'       => (bool)$row['is_spam'],
                'is_read'       => (bool)$row['is_read'],
                'processed_at'  => $row['processed_at'],
                'notes'         => $row['notes'],
                'meta'          => [
                    'seed_key'  => $seedKey,
                    'uuid'      => (string) Str::uuid(),
                    'tags'      => ['demo', 'seed'],
                ],
                'created_at'    => $createdAt,
                'updated_at'    => $createdAt,
            ]);

            $created++;
        }

        $this->command?->info("Form submissions seeded: created {$created} (идемпотентно по meta.seed_key).");
    }
}
