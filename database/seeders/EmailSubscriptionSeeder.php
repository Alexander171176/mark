<?php

namespace Database\Seeders;

use App\Models\Admin\Crm\EmailSubscription\EmailSubscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class EmailSubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('email_subscriptions')) {
            $this->command?->warn('Нет таблицы email_subscriptions — пропускаю EmailSubscriptionSeeder.');
            return;
        }

        $faker = fake('ru_RU');
        $now   = now();

        // При наличии users — подлинкуем часть подписок к реальным пользователям
        $userRows = [];
        if (Schema::hasTable('users')) {
            $userRows = DB::table('users')->select('id', 'email', 'name')->get()->all();
        }

        // Списки рассылок
        $lists = ['newsletter', 'promos', 'product_updates', 'events', 'blog_digest'];

        // Распределение статусов с весами
        $statusPool = [
            // чаще всего pending/subscribed
            'pending','pending','pending','pending',
            'subscribed','subscribed','subscribed','subscribed',
            'unsubscribed','bounced','complained'
        ];

        // Пример провайдеров
        $providers = [null, 'mailchimp', 'sendgrid', 'ses', 'sendinblue'];

        // Сколько подписок создать/обновить
        $total = 180;

        $created = 0;
        $updated = 0;

        for ($i = 0; $i < $total; $i++) {
            // часть возьмём из users (если есть), часть — синтетические
            $useRealUser = !empty($userRows) && $faker->boolean(65);
            if ($useRealUser) {
                $u = $faker->randomElement($userRows);
                $email   = mb_strtolower($u->email ?? $faker->unique()->safeEmail());
                $userId  = $u->id ?? null;
                $nameTag = $u->name ?? null;
            } else {
                $email   = mb_strtolower($faker->unique()->safeEmail());
                $userId  = null;
                $nameTag = null;
            }

            $list      = $faker->randomElement($lists);
            $status    = $faker->randomElement($statusPool);
            $provider  = $faker->randomElement($providers);
            $createdAt = $now->copy()->subDays(rand(0, 180))->subMinutes(rand(0, 1440));

            // Базовые поля
            $ip        = $faker->ipv4();
            $userAgent = $faker->userAgent();
            $locale    = $faker->randomElement(['ru', 'ru-RU', 'kk', 'en', null]);
            $source    = $faker->randomElement(['widget', 'footer_form', 'checkout_optin', 'import', 'webinar', 'blog_box']);

            // Поля подтверждения/отписки зависят от статуса
            $confirmToken   = null;
            $confirmedAt    = null;
            $unsubscribedAt = null;
            $unsubReason    = null;
            $lastEvent      = null;

            switch ($status) {
                case 'pending':
                    $confirmToken = Str::random(40);
                    $lastEvent    = 'pending';
                    break;

                case 'subscribed':
                    $confirmedAt = $createdAt->copy()->addHours(rand(1, 72));
                    $lastEvent   = $faker->randomElement(['delivered', 'open', 'click']);
                    break;

                case 'unsubscribed':
                    $confirmedAt    = $createdAt->copy()->addHours(rand(1, 72));
                    $unsubscribedAt = $confirmedAt->copy()->addDays(rand(1, 90));
                    $unsubReason    = $faker->randomElement(['too_many_emails', 'not_relevant', 'other']);
                    $lastEvent      = 'unsubscribe';
                    break;

                case 'bounced':
                    $confirmedAt = $faker->boolean(40) ? $createdAt->copy()->addHours(rand(1, 72)) : null;
                    $lastEvent   = 'bounce';
                    break;

                case 'complained':
                    $confirmedAt = $faker->boolean(50) ? $createdAt->copy()->addHours(rand(1, 72)) : null;
                    $lastEvent   = 'spam';
                    break;
            }

            // Теги подписчика
            $tags = $faker->randomElements(
                ['ru', 'en', 'kk', 'customer', 'lead', 'student', 'webinar', 'promo', 'trial'],
                rand(1, 3)
            );

            // Апсерт по уникальному ключу (email, list)
            $keys = [
                'email' => $email,
                'list'  => $list,
            ];

            $payload = [
                'user_id'                => $userId,
                'status'                 => $status,
                'confirm_token'          => $confirmToken,
                'confirmed_at'           => $confirmedAt,
                'unsubscribed_at'        => $unsubscribedAt,
                'unsub_reason'           => $unsubReason,
                'source'                 => $source,
                'locale'                 => $locale,
                'ip'                     => $ip,
                'user_agent'             => $userAgent,
                'provider'               => $provider,
                'provider_subscriber_id' => $provider ? strtoupper(Str::random(10)) : null,
                'last_event'             => $lastEvent,
                'tags'                   => $tags, // cast -> array
                'meta'                   => [
                    'seeded'      => true,
                    'seed_run'    => $now->toDateTimeString(),
                    'fingerprint' => sha1($email.'|'.$list),
                    'name_hint'   => $nameTag,
                ],
                'created_at'             => $createdAt,
                'updated_at'             => $now,
            ];

            $existing = EmailSubscription::query()
                ->where('email', $email)
                ->where('list', $list)
                ->first();

            if ($existing) {
                $existing->fill($payload)->save();
                $updated++;
            } else {
                EmailSubscription::query()->create($keys + $payload);
                $created++;
            }
        }

        $this->command?->info("Email subscriptions upserted: created {$created}, updated {$updated}.");
    }
}
